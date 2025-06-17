<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use Inertia\Inertia;
use App\Models\Skpd;
use App\Models\SkpdTugas;
use App\Models\KodeNomenklatur;
use App\Models\Monitoring;
use App\Models\MonitoringAnggaran;
use App\Models\Periode;
use App\Models\PeriodeTahap;
use App\Models\PeriodeTahun;
use App\Models\MonitoringRealisasi;
use App\Models\MonitoringPagu;
use Illuminate\Testing\Fluent\Concerns\Has;
use Illuminate\Support\Facades\Log;

class TriwulanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(int $tid, int $tahun = null)
    {
        $user = Auth::user();
        $tahun = $tahun ?? date('Y'); // Default ke tahun saat ini jika tidak disebutkan

        // Validate triwulan ID
        if (!in_array($tid, [1, 2, 3, 4])) {
            return redirect()->back()->with('error', 'Invalid triwulan ID.');
        }

        // Get periode information
        $periode = $this->getPeriodeByTriwulan($tid, $tahun);
        if (!$periode) {
            return redirect()->back()->with('error', 'Periode triwulan tidak ditemukan untuk tahun ' . $tahun . '.');
        }

        if ($user->hasRole('perangkat_daerah')) {
            return redirect()->route('triwulan.show', ['tid' => $tid, 'id' => $user->id, 'tahun' => $tahun]);
        }

        if ($user->hasRole('operator')) {
            $skpdUserIds = Skpd::where('nama_operator', $user->name)->pluck('user_id');
            $users = User::whereIn('id', $skpdUserIds)
                ->role('perangkat_daerah')
                ->with('skpd')
                ->paginate(1000);

            return Inertia::render('Triwulan' . $tid, [
                'users' => $users,
                'tid' => $tid,
                'tahun' => $tahun,
                'periode' => $periode,
                'triwulanName' => $this->getTriwulanName($tid),
            ]);
        }

        $users = User::role('perangkat_daerah')->with('skpd')->paginate(1000);
        return Inertia::render('Triwulan' . $tid, [
            'users' => $users,
            'tid' => $tid,
            'tahun' => $tahun,
            'periode' => $periode,
            'triwulanName' => $this->getTriwulanName($tid),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $tid, string $id, int $tahun = null)
    {
        $tahun = $tahun ?? date('Y'); // Default ke tahun saat ini

        // Validate triwulan ID
        if (!in_array($tid, [1, 2, 3, 4])) {
            return redirect()->back()->with('error', 'Invalid triwulan ID.');
        }

        $skpd = Skpd::with('skpdKepala.user','skpdKepala.user.userDetail','timKerja.user','timKerja.user.userDetail')->findOrFail($id);
        dd($skpd);

        $periode = $this->getPeriodeByTriwulan($tid, $tahun);

        if (!$periode) {
            return redirect()->back()->with('error', 'Periode triwulan tidak ditemukan untuk tahun ' . $tahun . '.');
        }

        $urusanList = KodeNomenklatur::where('jenis_nomenklatur', 0)->get();

        $bidangUrusanList = KodeNomenklatur::where('jenis_nomenklatur', 1)
            ->with(['details' => function($query) {
                $query->select('id', 'id_nomenklatur', 'id_urusan');
            }])
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nomor_kode' => $item->nomor_kode,
                    'nomenklatur' => $item->nomenklatur,
                    'jenis_nomenklatur' => $item->jenis_nomenklatur,
                    'urusan_id' => $item->details->first() ? $item->details->first()->id_urusan : null
                ];
            });

        $programList = KodeNomenklatur::where('jenis_nomenklatur', 2)
            ->with(['details' => function($query) {
                $query->select('id', 'id_nomenklatur', 'id_urusan', 'id_bidang_urusan');
            }])
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nomor_kode' => $item->nomor_kode,
                    'nomenklatur' => $item->nomenklatur,
                    'jenis_nomenklatur' => $item->jenis_nomenklatur,
                    'bidang_urusan_id' => $item->details->first() ? $item->details->first()->id_bidang_urusan : null
                ];
            });

        $kegiatanList = KodeNomenklatur::where('jenis_nomenklatur', 3)
            ->with(['details' => function($query) {
                $query->select('id', 'id_nomenklatur', 'id_program');
            }])
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nomor_kode' => $item->nomor_kode,
                    'nomenklatur' => $item->nomenklatur,
                    'jenis_nomenklatur' => $item->jenis_nomenklatur,
                    'program_id' => $item->details->first() ? $item->details->first()->id_program : null
                ];
            });

        $subkegiatanList = KodeNomenklatur::where('jenis_nomenklatur', 4)
            ->with(['details' => function($query) {
                $query->select('id', 'id_nomenklatur', 'id_kegiatan');
            }])
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nomor_kode' => $item->nomor_kode,
                    'nomenklatur' => $item->nomenklatur,
                    'jenis_nomenklatur' => $item->jenis_nomenklatur,
                    'kegiatan_id' => $item->details->first() ? $item->details->first()->id_kegiatan : null
                ];
            });

        $skpdTugas = SkpdTugas::where('skpd_id', $skpd->id)
            ->where('is_aktif', 1)
            ->with('kodeNomenklatur')
            ->get();

        // Determine the view based on triwulan
        $viewName = 'Triwulan' . $tid . '/Show';

        return Inertia::render($viewName, [
            'skpd' => $skpd,
            'skpdTugas' => $skpdTugas,
            'urusanList' => $urusanList,
            'bidangUrusanList' => $bidangUrusanList,
            'programList' => $programList,
            'kegiatanList' => $kegiatanList,
            'subkegiatanList' => $subkegiatanList,
            'tid' => $tid,
            'tahun' => $tahun,
            'periode' => $periode,
            'triwulanName' => $this->getTriwulanName($tid),
        ]);
    }

    public function showDetail(int $tid, string $id, string $taskId, int $tahun = null)
    {
        $tahun = $tahun ?? date('Y'); // Default ke tahun saat ini

        // Validate triwulan ID
        if (!in_array($tid, [1, 2, 3, 4])) {
            return redirect()->back()->with('error', 'Invalid triwulan ID.');
        }

        $periode = $this->getPeriodeByTriwulan($tid, $tahun);
        if (!$periode) {
            return redirect()->back()->with('error', 'Periode triwulan tidak ditemukan untuk tahun ' . $tahun . '.');
        }

        $tugas = SkpdTugas::with([
            'kodeNomenklatur',
            'skpd.skpdKepala.user.userDetail',
            'skpd.skpdKepala' => function($query) {
                $query->where('is_aktif', 1);
            },
        ])->findOrFail($taskId);

        $skpdTugas = SkpdTugas::where('skpd_id', $tugas->skpd_id)
            ->where('is_aktif', 1)
            ->with([
                'kodeNomenklatur.details',
                'monitoring' => function($query) {
                    $query->with(['monitoringAnggaran.monitoringTarget.periode', 'monitoringAnggaran.monitoringRealisasi.periode']);
                }
            ])
            ->get();

        $urusanId = $tugas->kodeNomenklatur->details->first()->id_urusan;

        // Get bidang urusan data
        $bidangUrusan = KodeNomenklatur::where('jenis_nomenklatur', 1)
            ->whereHas('details', function($query) use ($urusanId) {
                $query->where('id_urusan', $urusanId);
            })
            ->first();

        // Find any monitoring that might have deskripsi for bidang urusan
        $bidangUrusanDeskripsi = '-';
        if ($bidangUrusan) {
            $monitoring = \App\Models\Monitoring::whereHas('skpdTugas', function($query) use ($bidangUrusan) {
                $query->whereHas('kodeNomenklatur', function($query) use ($bidangUrusan) {
                    $query->where('id', $bidangUrusan->id);
                });
            })
            ->select('deskripsi')
            ->first();

            if ($monitoring && !empty($monitoring->deskripsi)) {
                $bidangUrusanDeskripsi = $monitoring->deskripsi;
            }
        }

        $bidangurusanTugas = $skpdTugas->filter(function($item) use ($urusanId) {
            return $item->kodeNomenklatur->jenis_nomenklatur == 1
                && $item->kodeNomenklatur->details->first()
                && $item->kodeNomenklatur->details->first()->id_urusan == $urusanId;
        })->values();

        $programTugas = $skpdTugas->filter(function($item) use ($urusanId) {
            return $item->kodeNomenklatur->jenis_nomenklatur == 2
                && $item->kodeNomenklatur->details->first()
                && $item->kodeNomenklatur->details->first()->id_urusan == $urusanId;
        })->values();

        $kegiatanTugas = $skpdTugas->filter(function($item) use ($urusanId) {
            return $item->kodeNomenklatur->jenis_nomenklatur == 3
                && $item->kodeNomenklatur->details->first()
                && $item->kodeNomenklatur->details->first()->id_urusan == $urusanId;
        })->values();

        $subkegiatanTugas = $skpdTugas->filter(function($item) use ($urusanId) {
            return $item->kodeNomenklatur->jenis_nomenklatur == 4
                && $item->kodeNomenklatur->details->first()
                && $item->kodeNomenklatur->details->first()->id_urusan == $urusanId;
        })->values();

        // Ambil monitoring data untuk tugas ini
        $monitoring = \App\Models\Monitoring::where('skpd_tugas_id', $tugas->id)
            ->with(['monitoringAnggaran.monitoringTarget.periode', 'monitoringAnggaran.monitoringRealisasi.periode'])
            ->get();

        // Determine the view based on triwulan
        $viewName = $this->getDetailViewName($tid);

        return Inertia::render($viewName, [
            'user' => [
                'id' => $tugas->skpd->user_id,
                'nama_skpd' => $tugas->skpd->nama_dinas
            ],
            'tugas' => $tugas,
            'skpdTugas' => $skpdTugas,
            'bidangurusanTugas' => $bidangurusanTugas,
            'programTugas' => $programTugas,
            'kegiatanTugas' => $kegiatanTugas,
            'subkegiatanTugas' => $subkegiatanTugas,
            'bidangUrusanDeskripsi' => $bidangUrusanDeskripsi,
            'tid' => $tid,
            'tahun' => $tahun,
            'periode' => $periode,
            'triwulanName' => $this->getTriwulanName($tid),
            'monitoring' => $monitoring ?? [],
        ]);
    }

    /**
     * Save realization data for a subkegiatan
     */
    public function saveRealisasi(Request $request, int $tid, int $tahun = null)
    {
        $tahun = $tahun ?? date('Y'); // Default ke tahun saat ini

        // Validate triwulan ID
        if (!in_array($tid, [1, 2, 3, 4])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid triwulan ID.'
            ], 400);
        }

        $request->validate([
            'id' => 'required|numeric',
            'realisasi_fisik' => 'required|numeric',
            'realisasi_keuangan' => 'required|numeric',
            'capaian_fisik' => 'required|numeric',
            'capaian_keuangan' => 'required|numeric',
            'keterangan' => 'nullable|string',
            'nama_pptk' => 'nullable|string',
        ]);

        // Check if the specified triwulan period is open
        $periode = $this->getPeriodeByTriwulan($tid, $tahun);
        if (!$periode) {
            return response()->json([
                'success' => false,
                'message' => 'Periode ' . $this->getTriwulanName($tid) . ' tahun ' . $tahun . ' tidak ditemukan.'
            ], 404);
        }
        if ($periode->status != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Periode ' . $this->getTriwulanName($tid) . ' belum dibuka. Data tidak dapat disimpan.'
            ], 403);
        }

        // Get the task record
        $task = SkpdTugas::findOrFail($request->id);

        // First, find the Rencana Awal monitoring record to copy budget data from
        $rencanaAwalMonitoring = Monitoring::where('skpd_tugas_id', $task->id)
            ->where('deskripsi', 'Rencana Awal')
            ->where('tahun', $tahun) // Filter berdasarkan tahun
            ->first();

        // Get or create a monitoring record for REALIZATION specifically
        $deskripsiRealisasi = 'Realisasi ' . $this->getTriwulanName($tid);
        $monitoring = Monitoring::firstOrCreate(
            [
                'skpd_tugas_id' => $task->id,
                'deskripsi' => $deskripsiRealisasi
            ],
            [
                'tahun' => date('Y'),
                'nama_pptk' => $request->nama_pptk ?? '-'
            ]
        );

        // Update monitoring info if provided
        if ($request->has('keterangan') || $request->has('nama_pptk')) {
            $monitoring->nama_pptk = $request->nama_pptk ?? $monitoring->nama_pptk;
            $monitoring->save();
        }

        // Get or create monitoring anggaran
        $anggaran = MonitoringAnggaran::firstOrCreate(
            ['monitoring_id' => $monitoring->id],
            ['sumber_anggaran_id' => 1] // Default sumber anggaran
        );

        // Copy budget data (pagu) from the Rencana Awal record if it exists
        if ($rencanaAwalMonitoring) {
            $rencanaAwalAnggaran = MonitoringAnggaran::where('monitoring_id', $rencanaAwalMonitoring->id)
                ->first();

            if ($rencanaAwalAnggaran) {
                // Copy pagu data for all categories (1=Pokok, 2=Parsial, 3=Perubahan)
                for ($kategori = 1; $kategori <= 3; $kategori++) {
                    $pagu = MonitoringPagu::where('monitoring_anggaran_id', $rencanaAwalAnggaran->id)
                        ->where('kategori', $kategori)
                        ->first();

                    if ($pagu) {
                        MonitoringPagu::updateOrCreate(
                            [
                                'monitoring_anggaran_id' => $anggaran->id,
                                'kategori' => $kategori,
                                'periode_id' => $pagu->periode_id
                            ],
                            [
                                'dana' => $pagu->dana
                            ]
                        );
                    }
                }
            }
        }

        // Try to find existing realisasi for this anggaran and period
        $realisasi = MonitoringRealisasi::firstOrCreate(
            [
                'monitoring_anggaran_id' => $anggaran->id,
                'periode_id' => $periode->id
            ],
            [
                'kinerja_fisik' => $request->realisasi_fisik,
                'keuangan' => $request->realisasi_keuangan
            ]
        );

        // Update values if record already existed
        if ($realisasi->wasRecentlyCreated === false) {
            $realisasi->kinerja_fisik = $request->realisasi_fisik;
            $realisasi->keuangan = $request->realisasi_keuangan;
            $realisasi->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Data realisasi ' . $this->getTriwulanName($tid) . ' berhasil disimpan'
        ]);
    }

    /**
     * Get periode by triwulan ID
     */
    private function getPeriodeByTriwulan(int $tid, int $tahun)
    {
        $triwulanNames = [
            1 => 'Triwulan 1',
            2 => 'Triwulan 2',
            3 => 'Triwulan 3',
            4 => 'Triwulan 4'
        ];

        if (!isset($triwulanNames[$tid])) {
            return null;
        }

        // First get the tahun record
        $periodeTahun = PeriodeTahun::where('tahun', $tahun)->first();
        if (!$periodeTahun) {
            return null;
        }

        // Then get the periode with the correct tahap and tahun
        return Periode::where('tahun_id', $periodeTahun->id)
            ->whereHas('tahap', function($query) use ($triwulanNames, $tid) {
                $query->where('tahap', 'like', '%' . $triwulanNames[$tid] . '%');
            })
            ->first();
    }

    /**
     * Get triwulan name by ID
     */
    private function getTriwulanName(int $tid)
    {
        $names = [
            1 => 'Triwulan 1',
            2 => 'Triwulan 2',
            3 => 'Triwulan 3',
            4 => 'Triwulan 4'
        ];

        return $names[$tid] ?? 'Unknown';
    }

    /**
     * Get view name based on triwulan ID
     */
    private function getViewName(int $tid)
    {
        $views = [
            1 => 'Triwulan1/Show',
            2 => 'Triwulan2/Show',
            3 => 'Triwulan3/Show',
            4 => 'Triwulan4/Show'
        ];

        return $views[$tid] ?? 'Unknown';
    }

    /**
     * Get detail view name based on triwulan ID
     */
    private function getDetailViewName(int $tid)
    {
        $views = [
            1 => 'Triwulan1/Detail',
            2 => 'Triwulan2/Detail',
            3 => 'Triwulan3/Detail',
            4 => 'Triwulan4/Detail'
        ];

        return $views[$tid] ?? 'Triwulan1/Detail';
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

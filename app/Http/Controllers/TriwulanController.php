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
    public function index(int $tid)
    {
        $user = Auth::user();

        // Validate triwulan ID
        if (!in_array($tid, [1, 2, 3, 4])) {
            return redirect()->back()->with('error', 'Invalid triwulan ID.');
        }

        // Get periode information
        $periode = $this->getPeriodeByTriwulan($tid);
        if (!$periode) {
            return redirect()->back()->with('error', 'Periode triwulan tidak ditemukan.');
        }

        if ($user->hasRole('perangkat_daerah')) {
            return redirect()->route('triwulan.show', ['tid' => $tid, 'id' => $user->id]);
        }

        if ($user->hasRole('operator')) {
            $skpdUserIds = Skpd::where('nama_operator', $user->name)->pluck('user_id');
            $users = User::whereIn('id', $skpdUserIds)
                ->role('perangkat_daerah')
                ->with('skpd')
                ->paginate(1000);

            return Inertia::render('Triwulan', [
                'users' => $users,
                'tid' => $tid,
                'periode' => $periode,
                'triwulanName' => $this->getTriwulanName($tid),
            ]);
        }

        $users = User::role('perangkat_daerah')->with('skpd')->paginate(1000);
        return Inertia::render('Triwulan', [
            'users' => $users,
            'tid' => $tid,
            'periode' => $periode,
            'triwulanName' => $this->getTriwulanName($tid),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $tid, string $id)
    {
        // Validate triwulan ID
        if (!in_array($tid, [1, 2, 3, 4])) {
            return redirect()->back()->with('error', 'Invalid triwulan ID.');
        }

        $user = User::with('skpd')->findOrFail($id);
        $periode = $this->getPeriodeByTriwulan($tid);

        if (!$periode) {
            return redirect()->back()->with('error', 'Periode triwulan tidak ditemukan.');
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

        $skpdTugas = SkpdTugas::where('skpd_id', $user->skpd->id)
            ->where('is_aktif', 1)
            ->with('kodeNomenklatur')
            ->get();

        // Determine the view based on triwulan
        $viewName = $this->getViewName($tid);

        return Inertia::render($viewName, [
            'user' => $user,
            'skpdTugas' => $skpdTugas,
            'urusanList' => $urusanList,
            'bidangUrusanList' => $bidangUrusanList,
            'programList' => $programList,
            'kegiatanList' => $kegiatanList,
            'subkegiatanList' => $subkegiatanList,
            'tid' => $tid,
            'periode' => $periode,
            'triwulanName' => $this->getTriwulanName($tid),
        ]);
    }

    public function showDetail(int $tid, $id)
    {
        // Validate triwulan ID
        if (!in_array($tid, [1, 2, 3, 4])) {
            return redirect()->back()->with('error', 'Invalid triwulan ID.');
        }

        $periode = $this->getPeriodeByTriwulan($tid);
        if (!$periode) {
            return redirect()->back()->with('error', 'Periode triwulan tidak ditemukan.');
        }

        $tugas = SkpdTugas::with([
            'kodeNomenklatur',
            'skpd.skpdKepala.user.userDetail',
            'skpd.skpdKepala' => function($query) {
                $query->where('is_aktif', 1);
            },
        ])->findOrFail($id);

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

        $kepalaSkpd = '-';
        $kepala = $tugas->skpd->skpdKepala->first();
        if ($kepala) {
            if ($kepala->user && $kepala->user->userDetail && $kepala->user->userDetail->nama) {
                $kepalaSkpd = $kepala->user->userDetail->nama;
            } elseif ($kepala->user && $kepala->user->name) {
                $kepalaSkpd = $kepala->user->name;
            }
        }

        // Get monitoring target data with descriptions and realisasi
        $monitoringTargets = [];
        $monitoringRealisasi = [];
        $allTasks = collect()->concat($programTugas)->concat($kegiatanTugas)->concat($subkegiatanTugas);

        $taskIds = $allTasks->pluck('id')->toArray();

        // Get all monitoring records for these tasks with filtered monitoringTarget by the specific periode_id
        $monitorings = \App\Models\Monitoring::whereIn('skpd_tugas_id', $taskIds)
            ->with(['monitoringAnggaran' => function($query) use ($periode) {
                $query->with(['monitoringTarget' => function($query) use ($periode) {
                    $query->where('periode_id', $periode->id);
                    $query->with('periode');
                }, 'monitoringRealisasi' => function($query) use ($periode) {
                    $query->where('periode_id', $periode->id);
                    $query->with('periode');
                }]);
            }])
            ->get();

        Log::info('Total monitoring records fetched for Triwulan ' . $tid . ': ' . $monitorings->count());

        // Process monitoring data for the specific periode
        foreach ($monitorings as $monitoring) {
            $taskId = $monitoring->skpd_tugas_id;

            if ($monitoring->monitoringAnggaran->isEmpty()) {
                continue;
            }

            foreach ($monitoring->monitoringAnggaran as $anggaran) {
                // Process monitoring targets
                if (!$anggaran->monitoringTarget->isEmpty()) {
                    foreach ($anggaran->monitoringTarget as $target) {
                        if ($target->periode_id != $periode->id) {
                            continue;
                        }

                        $monitoringTargets[] = [
                            'id' => $target->id,
                            'kinerja_fisik' => $target->kinerja_fisik,
                            'keuangan' => $target->keuangan,
                            'periode_id' => $target->periode_id,
                            'periode' => $target->periode ? $target->periode->nama : null,
                            'monitoring_id' => $monitoring->id,
                            'task_id' => $taskId,
                            'deskripsi' => $monitoring->deskripsi,
                            'nama_pptk' => $monitoring->nama_pptk ?? '-'
                        ];
                    }
                }

                // Process monitoring realisasi
                if (!$anggaran->monitoringRealisasi->isEmpty()) {
                    foreach ($anggaran->monitoringRealisasi as $realisasi) {
                        if ($realisasi->periode_id != $periode->id) {
                            continue;
                        }

                        $monitoringRealisasi[] = [
                            'id' => $realisasi->id,
                            'kinerja_fisik' => $realisasi->kinerja_fisik,
                            'keuangan' => $realisasi->keuangan,
                            'periode_id' => $realisasi->periode_id,
                            'periode' => $realisasi->periode ? $realisasi->periode->nama : null,
                            'monitoring_id' => $monitoring->id,
                            'task_id' => $taskId,
                            'monitoring_anggaran_id' => $anggaran->id,
                            'deskripsi' => $monitoring->deskripsi,
                            'nama_pptk' => $monitoring->nama_pptk ?? '-'
                        ];
                    }
                }
            }
        }

        Log::info('Total monitoring targets (periode_id = ' . $periode->id . '): ' . count($monitoringTargets));
        Log::info('Total monitoring realisasi (periode_id = ' . $periode->id . '): ' . count($monitoringRealisasi));

        // Determine the detail view based on triwulan
        $detailViewName = $this->getDetailViewName($tid);

        return Inertia::render($detailViewName, [
            'tugas' => $tugas,
            'bidangurusanTugas' => $bidangurusanTugas,
            'programTugas' => $programTugas,
            'kegiatanTugas' => $kegiatanTugas,
            'subkegiatanTugas' => $subkegiatanTugas,
            'kepalaSkpd' => $kepalaSkpd,
            'monitoringTargets' => $monitoringTargets,
            'monitoringRealisasi' => $monitoringRealisasi,
            'bidangUrusan' => $bidangUrusan ? [
                'id' => $bidangUrusan->id,
                'nomor_kode' => $bidangUrusan->nomor_kode,
                'nomenklatur' => $bidangUrusan->nomenklatur,
                'deskripsi' => $bidangUrusanDeskripsi
            ] : null,
            'user' => [
                'id' => $tugas->skpd_id,
                'nama_skpd' => $tugas->skpd->nama_skpd
            ],
            'tid' => $tid,
            'periode' => $periode,
            'triwulanName' => $this->getTriwulanName($tid),
        ]);
    }

    /**
     * Save realization data for a subkegiatan
     */
    public function saveRealisasi(Request $request, int $tid)
    {
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
        $periode = $this->getPeriodeByTriwulan($tid);
        if (!$periode) {
            return response()->json([
                'success' => false,
                'message' => 'Periode ' . $this->getTriwulanName($tid) . ' tidak ditemukan.'
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
    private function getPeriodeByTriwulan(int $tid)
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

        return Periode::whereHas('tahap', function($query) use ($triwulanNames, $tid) {
            $query->where('tahap', 'like', '%' . $triwulanNames[$tid] . '%');
        })->first();
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

        return $views[$tid] ?? 'Triwulan1/Show';
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

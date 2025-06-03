<?php

namespace App\Http\Controllers;

use App\Models\Monitoring;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\KodeNomenklatur;
use App\Models\SkpdTugas;
use App\Models\SkpdKepala;
use App\Models\Skpd;
use App\Models\MonitoringTarget;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Models\MonitoringAnggaran;
use App\Models\Periode;
use App\Models\Periode;

class MonitoringController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('perangkat_daerah')) {
            return redirect()->route('monitoring.show', $user->id);
        }

        if ($user->hasRole('operator')) {
            $skpdUserIds = Skpd::where('nama_operator', $user->name)->pluck('user_id');
            $users = User::whereIn('id', $skpdUserIds)
                ->role('perangkat_daerah')
                ->with('skpd')
                ->paginate(1000);

            return Inertia::render('Monitoring', [
                'users' => $users,
            ]);
        }

        $users = User::role('perangkat_daerah')
        ->with(['skpd', 'userDetail'])
        ->paginate(1000);


        return Inertia::render('Monitoring', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        $kodeNomenklatur = KodeNomenklatur::all();

        return Inertia::render('Monitoring/Create', [
            'kodeNomenklatur' => $kodeNomenklatur,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'skpd_id' => 'required|exists:skpd,id',
            'sumber_dana' => 'required|string|max:255',
            'periode_id' => 'nullable|exists:periode,id',
            'tahun' => 'required|digits:4|integer',
            'deskripsi' => 'required|string',
            'pagu_pokok' => 'required|integer',
            'pagu_parsial' => 'nullable|integer',
            'pagu_perubahan' => 'nullable|integer',
        ]);

        Monitoring::create($validated);

        return redirect()->route('monitoring.index')->with('success', 'Data monitoring berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        $user = User::with(['skpd', 'userDetail'])->findOrFail($id);

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
                    'urusan_id' => $item->details->first()?->id_urusan
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
                    'bidang_urusan_id' => $item->details->first()?->id_bidang_urusan
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
                    'program_id' => $item->details->first()?->id_program
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
                    'kegiatan_id' => $item->details->first()?->id_kegiatan
                ];
            });

        $skpdTugas = SkpdTugas::where('skpd_id', $user->skpd->id)
            ->where('is_aktif', 1)
            ->with('kodeNomenklatur')
            ->get();

        return Inertia::render('Monitoring/Show', [
            'user' => $user,
            'skpdTugas' => $skpdTugas,
            'urusanList' => $urusanList,
            'bidangUrusanList' => $bidangUrusanList,
            'programList' => $programList,
            'kegiatanList' => $kegiatanList,
            'subkegiatanList' => $subkegiatanList,
        ]);
    }

    public function showRencanaAwal($id, Request $request)
    {
        $tugas = SkpdTugas::with([
            'kodeNomenklatur',
            'skpd.skpdKepala.user.userDetail',
            'skpd.skpdKepala' => function($query) {
                $query->where('is_aktif', 1);
            }
        ])->findOrFail($id);

        $skpdTugas = SkpdTugas::where('skpd_id', $tugas->skpd_id)
            ->where('is_aktif', 1)
            ->with('kodeNomenklatur.details')
            ->get();

        $urusanId = $tugas->kodeNomenklatur->details->first()?->id_urusan;

        $bidangurusanTugas = $skpdTugas->filter(fn($item) =>
            $item->kodeNomenklatur->jenis_nomenklatur == 1 &&
            $item->kodeNomenklatur->details->first()?->id_urusan == $urusanId
        )->values();

        $bidangurusanTugas = $skpdTugas->filter(fn($item) =>
            $item->kodeNomenklatur->jenis_nomenklatur == 1 &&
            $item->kodeNomenklatur->details->first()?->id_urusan == $urusanId
        )->values();

        $programTugas = $skpdTugas->filter(fn($item) => 
            $item->kodeNomenklatur->jenis_nomenklatur == 2 &&
            $item->kodeNomenklatur->details->first()?->id_urusan == $urusanId
        )->values();

        $kegiatanTugas = $skpdTugas->filter(fn($item) => 
            $item->kodeNomenklatur->jenis_nomenklatur == 3 &&
            $item->kodeNomenklatur->details->first()?->id_urusan == $urusanId
        )->values();

        $subkegiatanTugas = $skpdTugas->filter(fn($item) => 
            $item->kodeNomenklatur->jenis_nomenklatur == 4 &&
            $item->kodeNomenklatur->details->first()?->id_urusan == $urusanId
        )->values();

        // Load monitoring data for each subkegiatan
        foreach ($subkegiatanTugas as $subKegiatan) {
            $monitoring = Monitoring::where('skpd_tugas_id', $subKegiatan->id)
                ->where('tahun', date('Y'))
                ->where('deskripsi', 'Rencana Awal')
                ->with('targets')
                ->first();
            
            if ($monitoring) {
                $subKegiatan->monitoring = $monitoring;
            }
        }

        $kepala = $tugas->skpd->skpdKepala->first();
        $kepalaSkpd = $kepala?->user?->userDetail?->nama ?? $kepala?->user?->name ?? '-';

        // Get the user associated with this SKPD for proper navigation
        $skpdUser = User::where('id', $tugas->skpd->user_id)->first();

        // Get active periods
        $periodeAktif = Periode::with(['tahap', 'tahun'])
            ->where('status', 1)
            ->whereHas('tahap', function($query) {
                $query->where('tahap', 'Rencana');
            })
            ->get();

        // Get all periods for the dropdown
        $semuaPeriodeAktif = Periode::with(['tahap', 'tahun'])
            ->where('status', 1)
        // Get active periods
        $periodeAktif = Periode::with(['tahap', 'tahun'])
            ->where('status', 1)
            ->whereHas('tahap', function($query) {
                $query->where('tahap', 'Rencana');
            })
            ->get();

        // Get all periods for the dropdown
        $semuaPeriodeAktif = Periode::with(['tahap', 'tahun'])
            ->where('status', 1)
            ->get();

        // Get current year
        $tahunAktif = null;
        if ($semuaPeriodeAktif->isNotEmpty()) {
            $tahunAktif = $semuaPeriodeAktif->first()->tahun;
        }

        // Get funding data for each subkegiatan filtered by active period
        $dataAnggaranTerakhir = [];
        $periodeId = null;

        // Get period ID from request if specified
        if ($request->has('periode_id') && $request->periode_id) {
            $periodeId = $request->periode_id;
        }
        // Otherwise use Rencana period ID if active
        elseif ($periodeAktif->isNotEmpty()) {
            $periodeId = $periodeAktif->first()->id;
        }

        // Fetch anggaran data for each subkegiatan using the same approach as ManajemenAnggaranController
        foreach ($subkegiatanTugas as $tugas) {
            // Find monitoring related to this SKPD tugas
            $monitoring = Monitoring::where('skpd_tugas_id', $tugas->id)
                ->latest()
                ->first();

            if ($monitoring) {
                // Get funding data for this monitoring filtered by period
                $sumberAnggaranData = [];

                $monitoringAnggaranQuery = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                    ->with(['sumberAnggaran']);

                if ($periodeId) {
                    $monitoringAnggaranQuery->with(['pagu' => function($query) use ($periodeId) {
                        $query->where('kategori', 1) // Category 1 = pokok
                              ->where('periode_id', $periodeId); // Filter by period
                    }]);
                } else {
                    $monitoringAnggaranQuery->with(['pagu' => function($query) {
                        $query->where('kategori', 1); // Category 1 = pokok
                    }]);
                }

                $monitoringAnggaran = $monitoringAnggaranQuery->get();

                foreach ($monitoringAnggaran as $anggaran) {
                    if ($anggaran->sumberAnggaran && $anggaran->pagu->isNotEmpty()) {
                        $key = $this->reverseMapNamaSumberAnggaran($anggaran->sumberAnggaran->nama);
                        if ($key) {
                            $sumberAnggaranData[$key] = $anggaran->pagu->first()->dana ?? 0;
                        }
                    }
                }

                // Save data per SKPD tugas using same structure as ManajemenAnggaranController
                $dataAnggaranTerakhir[$tugas->id] = [
                    'sumber_anggaran' => [
                        'dak' => isset($sumberAnggaranData['dak']),
                        'dak_peruntukan' => isset($sumberAnggaranData['dak_peruntukan']),
                        'dak_fisik' => isset($sumberAnggaranData['dak_fisik']),
                        'dak_non_fisik' => isset($sumberAnggaranData['dak_non_fisik']),
                        'blud' => isset($sumberAnggaranData['blud']),
                    ],
                    'values' => [
                        'dak' => $sumberAnggaranData['dak'] ?? 0,
                        'dak_peruntukan' => $sumberAnggaranData['dak_peruntukan'] ?? 0,
                        'dak_fisik' => $sumberAnggaranData['dak_fisik'] ?? 0,
                        'dak_non_fisik' => $sumberAnggaranData['dak_non_fisik'] ?? 0,
                        'blud' => $sumberAnggaranData['blud'] ?? 0,
                    ]
                ];
            }
        }

        return Inertia::render('Monitoring/RencanaAwal', [
            'tugas' => $tugas,
            'bidangurusanTugas' => $bidangurusanTugas,
            'bidangurusanTugas' => $bidangurusanTugas,
            'programTugas' => $programTugas,
            'kegiatanTugas' => $kegiatanTugas,
            'subkegiatanTugas' => $subkegiatanTugas,
            'kepalaSkpd' => $kepalaSkpd,
            'dataAnggaranTerakhir' => $dataAnggaranTerakhir,
            'user' => [
                'nip' => $skpdUser?->userDetail?->nip ?? '-',
                'id' => $skpdUser?->id ?? $tugas->skpd_id, // Use user ID instead of skpd_id
                'nama_skpd' => $tugas->skpd->nama_skpd ?? $tugas->skpd->nama_dinas,
                'skpd_id' => $tugas->skpd_id // Keep skpd_id for other purposes
            ],
            'bidangUrusanList' => $bidangurusanTugas,
            'periodeAktif' => $periodeAktif,
            'semuaPeriodeAktif' => $semuaPeriodeAktif,
            'tahunAktif' => $tahunAktif
        ]);
    }

    /**
     * Kebalikan dari fungsi mapNamaSumberAnggaran untuk validasi penghapusan
     */
    private function reverseMapNamaSumberAnggaran(string $nama): ?string
    {
        $reverseMapping = [
            'DAU' => 'dak',
            'DAU Peruntukan' => 'dak_peruntukan',
            'DAK Fisik' => 'dak_fisik',
            'DAK Non Fisik' => 'dak_non_fisik',
            'BLUD' => 'blud',
        ];

        return $reverseMapping[$nama] ?? null;
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'skpd_id' => 'required|exists:skpd,id',
            'sumber_dana' => 'required|string|max:255',
            'periode_id' => 'nullable|exists:periode,id',
            'tahun' => 'required|digits:4|integer',
            'deskripsi' => 'required|string',
            'pagu_pokok' => 'required|integer',
            'pagu_parsial' => 'nullable|integer',
            'pagu_perubahan' => 'nullable|integer'
        ]);

        $monitoring = Monitoring::findOrFail($id);
        $monitoring->update($validated);

        return response()->json([
            'success' => true,
            'monitoring' => $monitoring
        ]);
    }

    public function saveMonitoringData(Request $request)
    {
        $validated = $request->validate([
            'skpd_id' => 'required|exists:skpd,id',
            'sumber_dana' => 'required|string|max:255',
            'periode_id' => 'nullable|exists:periode,id',
            'tahun' => 'required|digits:4|integer',
            'deskripsi' => 'required|string',
            'pagu_pokok' => 'required|integer',
            'pagu_parsial' => 'nullable|integer',
            'pagu_perubahan' => 'nullable|integer',
            'pokok' => 'required|string',
            'parsial' => 'required|string',
            'perubahan' => 'nullable|string',
            'targets' => 'required|array',
            'targets.*.kinerja_fisik' => 'required|numeric',
            'targets.*.keuangan' => 'required|numeric',
        ]);

        $monitoring = Monitoring::create([
            'skpd_id' => $validated['skpd_id'],
            'sumber_dana' => $validated['sumber_dana'],
            'periode_id' => $validated['periode_id'],
            'tahun' => $validated['tahun'],
            'deskripsi' => $validated['deskripsi'],
            'pagu_pokok' => $validated['pagu_pokok'],
            'pagu_parsial' => $validated['pagu_parsial'],
            'pagu_perubahan' => $validated['pagu_perubahan'],
            'pokok' => $validated['pokok'],
            'parsial' => $validated['parsial'],
            'perubahan' => $validated['perubahan'] ?? null,
        ]);

        foreach ($validated['targets'] as $target) {
            $monitoring->targets()->create([
                'kinerja_fisik' => $target['kinerja_fisik'],
                'keuangan' => $target['keuangan'],
            ]);
        }

        return back()->with('success', 'Data monitoring berhasil disimpan.');
    }

    public function showMonitoringDetails($id)
    {
        $monitoring = Monitoring::with([
            'skpd',
            'periode',
            'targets',
            'realisasi'
        ])->findOrFail($id);

        return Inertia::render('Monitoring/Details', [
            'monitoring' => $monitoring,
            'sumber_dana' => $monitoring->sumber_dana,
            'pagu_pokok' => $monitoring->pagu_pokok,
            'pagu_parsial' => $monitoring->pagu_parsial,
            'pagu_perubahan' => $monitoring->pagu_perubahan,
            'targets' => $monitoring->targets,
            'realisasi' => $monitoring->realisasi,
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Monitoring;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\KodeNomenklatur;
use App\Models\SkpdTugas;
use App\Models\SkpdKepala;
use App\Models\Skpd;
use App\Models\TimKerja;
use App\Models\MonitoringTarget;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Models\MonitoringAnggaran;
use App\Models\Periode;
use App\Models\SumberAnggaran;
use App\Models\MonitoringPagu;

class MonitoringController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('perangkat_daerah')) {
            return redirect()->route('monitoring.show', $user->id);
        }

        $query = User::role('perangkat_daerah')
            ->with([
                'skpd' => function($q) {
                    $q->with(['kepalaAktif.user', 'operatorAktif.operator']);
                },
                'userDetail'
            ]);

        if ($user->hasRole('operator')) {
            // Get SKPD IDs where the user is an operator
            $skpdIds = TimKerja::where('operator_id', $user->id)
                ->where('is_aktif', 1)
                ->pluck('skpd_id');

            $query->whereHas('skpd', function($q) use ($skpdIds) {
                $q->whereIn('skpd.id', $skpdIds);
            });
        }

        $users = $query->paginate(1000);

        // Transform the data but keep the original model instance
        foreach ($users as $user) {
            $skpd = $user->skpd->first();
            $user->nama_dinas = $skpd?->nama_skpd;
            $user->operator_name = $skpd?->operatorAktif?->operator?->name;
            $user->kepala_name = $skpd?->kepalaAktif?->user?->name;
            $user->kode_organisasi = $skpd?->kode_organisasi;
        }

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
        $user = User::with([
            'skpd' => function($q) {
                $q->with(['kepalaAktif.user', 'operatorAktif.operator']);
            },
            'userDetail'
        ])->findOrFail($id);

        $skpd = $user->skpd->first();

        // Format data sesuai dengan interface props yang diharapkan
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'user_detail' => $user->userDetail,
            'skpd' => $skpd ? [
                'nama_skpd' => $skpd->nama_skpd,
                'operator_name' => $skpd->operatorAktif?->operator?->name,
                'kepala_name' => $skpd->kepalaAktif?->user?->name,
                'no_dpa' => $skpd->no_dpa,
                'kode_organisasi' => $skpd->kode_organisasi
            ] : null
        ];

        // Get SKPD Tugas with its relations
        $skpdTugas = SkpdTugas::where('skpd_id', $skpd?->id)
            ->with(['kodeNomenklatur'])
            ->get()
            ->map(function($tugas) {
                return [
                    'id' => $tugas->id,
                    'kode_nomenklatur' => [
                        'id' => $tugas->kodeNomenklatur->id,
                        'nomor_kode' => $tugas->kodeNomenklatur->nomor_kode,
                        'nomenklatur' => $tugas->kodeNomenklatur->nomenklatur,
                        'jenis_nomenklatur' => $tugas->kodeNomenklatur->jenis_nomenklatur,
                    ]
                ];
            });

        // Get Urusan List
        $urusanList = KodeNomenklatur::where('jenis_nomenklatur', 0)->get();

        // Get Bidang Urusan List with details
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

        // Get Program List with details
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

        // Get Kegiatan List with details
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

        // Get Subkegiatan List with details
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

        return Inertia::render('Monitoring/Show', [
            'user' => $userData,
            'urusanList' => $urusanList,
            'bidangUrusanList' => $bidangUrusanList,
            'programList' => $programList,
            'kegiatanList' => $kegiatanList,
            'subkegiatanList' => $subkegiatanList,
            'skpdTugas' => $skpdTugas
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
                'id' => $skpdUser?->id ?? $tugas->skpd_id,
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
            'pokok' => 'nullable|string',
            'parsial' => 'nullable|string',
            'perubahan' => 'nullable|string',
            'targets' => 'required|array',
            'targets.*.kinerja_fisik' => 'required|numeric',
            'targets.*.keuangan' => 'required|numeric',
            'tugas_id' => 'nullable|exists:skpd_tugas,id',
            'sumber_anggaran' => 'nullable|array',
            'funding_values' => 'nullable|array',
        ]);

        // Check for existing monitoring if tugas_id is provided
        if (isset($validated['tugas_id'])) {
            $existingMonitoring = Monitoring::where('skpd_tugas_id', $validated['tugas_id'])
                ->where('tahun', $validated['tahun'])
                ->first();
            
            if ($existingMonitoring) {
                // Update existing monitoring
                $existingMonitoring->sumber_dana = $validated['sumber_dana'];
                $existingMonitoring->periode_id = $validated['periode_id'];
                $existingMonitoring->deskripsi = $validated['deskripsi'];
                $existingMonitoring->pagu_pokok = $validated['pagu_pokok'];
                $existingMonitoring->pagu_parsial = $validated['pagu_parsial'] ?? 0;
                $existingMonitoring->pagu_perubahan = $validated['pagu_perubahan'] ?? 0;
                $existingMonitoring->pokok = $validated['pokok'] ?? '';
                $existingMonitoring->parsial = $validated['parsial'] ?? '';
                $existingMonitoring->perubahan = $validated['perubahan'] ?? null;
                $existingMonitoring->save();
                
                // Clear and recreate targets
                $existingMonitoring->targets()->delete();
                foreach ($validated['targets'] as $target) {
                    $existingMonitoring->targets()->create([
                        'kinerja_fisik' => $target['kinerja_fisik'],
                        'keuangan' => $target['keuangan'],
                    ]);
                }
                
                // If funding data is provided, update the connections
                if (isset($validated['sumber_anggaran']) && isset($validated['funding_values'])) {
                    $this->updateFundingConnections($existingMonitoring, $validated);
                }
                
                return back()->with('success', 'Data monitoring berhasil diperbarui.');
            }
            
            // Create new monitoring with skpd_tugas_id
            $monitoring = new Monitoring();
            $monitoring->skpd_id = $validated['skpd_id'];
            $monitoring->skpd_tugas_id = $validated['tugas_id'];
            $monitoring->sumber_dana = $validated['sumber_dana'];
            $monitoring->periode_id = $validated['periode_id'];
            $monitoring->tahun = $validated['tahun'];
            $monitoring->deskripsi = $validated['deskripsi'];
            $monitoring->pagu_pokok = $validated['pagu_pokok'];
            $monitoring->pagu_parsial = $validated['pagu_parsial'] ?? 0;
            $monitoring->pagu_perubahan = $validated['pagu_perubahan'] ?? 0;
            $monitoring->pokok = $validated['pokok'] ?? '';
            $monitoring->parsial = $validated['parsial'] ?? '';
            $monitoring->perubahan = $validated['perubahan'] ?? null;
            $monitoring->save();
            
            // Create targets
            foreach ($validated['targets'] as $target) {
                $monitoring->targets()->create([
                    'kinerja_fisik' => $target['kinerja_fisik'],
                    'keuangan' => $target['keuangan'],
                ]);
            }
            
            // If funding data is provided, update the connections
            if (isset($validated['sumber_anggaran']) && isset($validated['funding_values'])) {
                $this->updateFundingConnections($monitoring, $validated);
            }
            
            return back()->with('success', 'Data monitoring berhasil disimpan.');
        }

        // Create regular monitoring (without tugas_id)
        $monitoring = Monitoring::create([
            'skpd_id' => $validated['skpd_id'],
            'sumber_dana' => $validated['sumber_dana'],
            'periode_id' => $validated['periode_id'],
            'tahun' => $validated['tahun'],
            'deskripsi' => $validated['deskripsi'],
            'pagu_pokok' => $validated['pagu_pokok'],
            'pagu_parsial' => $validated['pagu_parsial'] ?? 0,
            'pagu_perubahan' => $validated['pagu_perubahan'] ?? 0,
            'pokok' => $validated['pokok'] ?? '',
            'parsial' => $validated['parsial'] ?? '',
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

    /**
     * Update funding connections for a monitoring
     */
    private function updateFundingConnections($monitoring, $validated)
    {
        // Get the active period
        $aktivPeriode = Periode::find($validated['periode_id']);
        
        if (!$aktivPeriode) {
            return;
        }
        
        foreach ($validated['sumber_anggaran'] as $key => $value) {
            if ($value) {
                // Find source
                $sumberAnggaran = SumberAnggaran::where('nama', $this->mapNamaSumberAnggaran($key))->first();
                
                if (!$sumberAnggaran) continue;
                
                // Create or find monitoring_anggaran
                $monitoringAnggaran = MonitoringAnggaran::firstOrCreate([
                    'monitoring_id' => $monitoring->id,
                    'sumber_anggaran_id' => $sumberAnggaran->id,
                ]);
                
                // Save or update pagu data
                $monitoringPagu = MonitoringPagu::firstOrCreate([
                    'monitoring_anggaran_id' => $monitoringAnggaran->id,
                    'periode_id' => $aktivPeriode->id,
                    'kategori' => 1
                ], [
                    'dana' => $validated['funding_values'][$key]
                ]);
                
                // Update if it already existed
                if (!$monitoringPagu->wasRecentlyCreated) {
                    $monitoringPagu->dana = $validated['funding_values'][$key];
                    $monitoringPagu->save();
                }
            }
        }
    }
    
    /**
     * Map source funding key to display name
     */
    private function mapNamaSumberAnggaran(string $key): string
    {
        $mapping = [
            'dak' => 'DAU',
            'dak_peruntukan' => 'DAU Peruntukan',
            'dak_fisik' => 'DAK Fisik',
            'dak_non_fisik' => 'DAK Non Fisik',
            'blud' => 'BLUD',
        ];

        return $mapping[$key] ?? $key;
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
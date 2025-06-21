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
use App\Models\MonitoringAnggaran;
use App\Models\MonitoringPagu;
use App\Models\SumberAnggaran;
use App\Models\Periode;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ManajemenAnggaranController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('perangkat_daerah')) {
            return redirect()->route('manajemenanggaran.show', $user->id);
        }

        $query = User::role('perangkat_daerah')
            ->with(['skpd' => function($q) {
                $q->with(['kepalaAktif.user', 'operatorAktif.operator']);
            }]);

        if ($user->hasRole('operator')) {
            $skpdIds = TimKerja::where('operator_id', $user->id)
                ->where('is_aktif', 1)
                ->pluck('skpd_id');

            $query->whereHas('skpd', function($q) use ($skpdIds) {
                $q->whereIn('skpd.id', $skpdIds);
            });
        }

        $users = $query->paginate(1000);

        // Transform the data but keep the original structure
        $transformedUsers = $users->through(function ($user) {
            $skpd = $user->skpd->first();

            // Keep the original user object but add the transformed properties
            $user->nama_dinas = $skpd?->nama_skpd;
            $user->operator_name = $skpd?->operatorAktif?->operator?->name;
            $user->kepala_name = $skpd?->kepalaAktif?->user?->name;
            $user->kode_organisasi = $skpd?->kode_organisasi;

            return $user;
        });

        return Inertia::render('ManajemenAnggaran', [
            'users' => $transformedUsers
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

    public function show(string $id, Request $request)
    {
        $user = User::with([
            'skpd' => function($q) {
                $q->with(['kepalaAktif.user', 'operatorAktif.operator']);
            },
            'userDetail'
        ])->findOrFail($id);

        $skpd = $user->skpd->first();

        // Check if user has SKPD associated
        if (!$skpd) {
            return redirect()->back()->with('error', 'User tidak memiliki SKPD yang terkait.');
        }

        // Keep the original user object but add the transformed properties
        $user->nama_dinas = $skpd?->nama_skpd;
        $user->operator_name = $skpd?->operatorAktif?->operator?->name;
        $user->kepala_name = $skpd?->kepalaAktif?->user?->name;
        $user->kode_organisasi = $skpd?->kode_organisasi;

        \Log::debug('SKPD data:', ['skpd' => $skpd]);

        // Load all SKPD tasks including subkegiatan
        $skpdTugas = SkpdTugas::where('skpd_id', $skpd->id)
            ->where('is_aktif', 1)
            ->with(['kodeNomenklatur.details'])
            ->get();

        \Log::debug('SKPD Tugas data:', ['count' => $skpdTugas->count(), 'data' => $skpdTugas->toArray()]);

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

        // Get active periods
        $periodeAktif = Periode::with(['tahap', 'tahun'])
            ->where('status', 1)
            ->get();

        \Log::debug('Periode aktif:', ['count' => $periodeAktif->count(), 'data' => $periodeAktif->toArray()]);

        // Get all periods for the dropdown
        $semuaPeriodeAktif = Periode::with(['tahap', 'tahun'])
            ->where('status', 1)
            ->get();

        // Get current year
        $tahunAktif = null;
        if ($semuaPeriodeAktif->isNotEmpty()) {
            $tahunAktif = $semuaPeriodeAktif->first()->tahun;
        }

        // Get funding data for each subkegiatan
        $subkegiatanIds = $skpdTugas->where('kode_nomenklatur.jenis_nomenklatur', 4)
            ->pluck('id');

        \Log::debug('Subkegiatan IDs:', ['ids' => $subkegiatanIds->toArray()]);

        // $dataAnggaranTerakhir = [];
        // if ($subkegiatanIds->isNotEmpty()) {
        //     $anggaranData = SumberAnggaran::whereIn('skpd_tugas_id', $subkegiatanIds)
        //         ->latest()
        //         ->get()
        //         ->groupBy('skpd_tugas_id');

        //     \Log::debug('Anggaran data:', ['data' => $anggaranData->toArray()]);

        //     foreach ($anggaranData as $tugasId => $data) {
        //         $latestData = $data->first();
        //         $dataAnggaranTerakhir[$tugasId] = [
        //             'sumber_anggaran' => [
        //                 'dak' => $latestData->dak,
        //                 'dak_peruntukan' => $latestData->dak_peruntukan,
        //                 'dak_fisik' => $latestData->dak_fisik,
        //                 'dak_non_fisik' => $latestData->dak_non_fisik,
        //                 'blud' => $latestData->blud,
        //             ],
        //             'values' => [
        //                 'dak' => $latestData->nilai_dak ?? 0,
        //                 'dak_peruntukan' => $latestData->nilai_dak_peruntukan ?? 0,
        //                 'dak_fisik' => $latestData->nilai_dak_fisik ?? 0,
        //                 'dak_non_fisik' => $latestData->nilai_dak_non_fisik ?? 0,
        //                 'blud' => $latestData->nilai_blud ?? 0,
        //             ]
        //         ];
        //     }
        // }

        $dataAnggaranTerakhir = [];
        $periodeId = null;

        // Check if a specific period was requested
        if ($request->has('periode_id') && $request->periode_id) {
            $periodeId = $request->periode_id;
        }
        // Otherwise use Rencana period ID if active
        elseif ($periodeAktif->isNotEmpty()) {
            $periodeId = $periodeAktif->first()->id;
        }

        foreach ($skpdTugas as $tugas) {
            if ($tugas->kodeNomenklatur->jenis_nomenklatur == 4) { // Hanya ambil sub kegiatan
                // Cari monitoring yang terkait dengan SKPD tugas
                $monitoring = Monitoring::where('skpd_tugas_id', $tugas->id)
                    ->latest()
                    ->first();

                if ($monitoring) {
                    // Ambil data anggaran untuk monitoring ini berdasarkan periode
                    $sumberAnggaranData = [];

                    $monitoringAnggaranQuery = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                        ->with(['sumberAnggaran']);

                    if ($periodeId) {
                        $monitoringAnggaranQuery->with(['pagu' => function($query) use ($periodeId) {
                            $query->where('kategori', 1) // Kategori 1 = pokok
                                  ->where('periode_id', $periodeId); // Filter berdasarkan periode
                        }]);
                    } else {
                        $monitoringAnggaranQuery->with(['pagu' => function($query) {
                            $query->where('kategori', 1); // Kategori 1 = pokok
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

                    // Simpan data per SKPD tugas
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
        }
        \Log::debug('Data anggaran terakhir:', ['data' => $dataAnggaranTerakhir]);

        return Inertia::render('MonitoringAnggaran/Sumberdana', [
            'user' => $user,
            'skpdTugas' => $skpdTugas,
            'urusanList' => $urusanList,
            'bidangUrusanList' => $bidangUrusanList,
            'periodeAktif' => $periodeAktif,
            'tahunAktif' => $tahunAktif,
            'semuaPeriodeAktif' => $semuaPeriodeAktif,
            'dataAnggaranTerakhir' => $dataAnggaranTerakhir
        ]);
    }

    public function showRencanaAwal($id, Request $request)
    {
        $tugas = SkpdTugas::with([
            'kodeNomenklatur',
            'skpd.kepala.user.userDetail',
            'skpd.kepala' => function($query) {
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

        $kepala = $tugas->skpd->kepala->first();
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

        foreach ($subkegiatanTugas as $tugas) {
            if ($tugas->kodeNomenklatur->jenis_nomenklatur == 4) { // Only get sub kegiatan
                // Find monitoring related to this SKPD tugas
                $monitoring = Monitoring::where('skpd_tugas_id', $tugas->id)
                    ->latest()
                    ->first();

                if ($monitoring) {
                    // Get funding data for this monitoring filtered by period
                    $sumberAnggaranData = [];

                    $monitoringAnggaranQuery = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                        ->with(['sumberAnggaran', 'target' => function($query) {
                            $query->orderBy('periode_id');
                        }]);

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
                        if ($anggaran->sumberAnggaran) {
                            $key = $this->reverseMapNamaSumberAnggaran($anggaran->sumberAnggaran->nama);
                            if ($key) {
                                // PERBAIKAN: Selalu masukkan sumber anggaran, meskipun pagu kosong
                                $sumberAnggaranData[$key] = $anggaran->pagu->isNotEmpty() ?
                                    ($anggaran->pagu->first()->dana ?? 0) : 0;
                            }
                        }
                    }

                    // Save data per SKPD tugas
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
        }

        // Load target data for each subkegiatan with separate targets per sumber anggaran
        $subkegiatanWithTargets = $subkegiatanTugas->map(function($subkegiatan) {
            // Find monitoring for this subkegiatan
            $monitoring = Monitoring::where('skpd_tugas_id', $subkegiatan->id)
                ->latest()
                ->first();

            if ($monitoring) {
                // Get monitoring anggaran with targets and sumber anggaran
                $monitoringAnggaran = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                    ->with([
                        'target' => function($query) {
                            $query->orderBy('periode_id');
                        },
                        'sumberAnggaran'
                    ])
                    ->get();

                // Group targets by sumber anggaran
                $targetsBySumberAnggaran = [];
                foreach ($monitoringAnggaran as $anggaran) {
                    if ($anggaran->sumberAnggaran) {
                        $sumberAnggaranId = $anggaran->sumber_anggaran_id;
                        $sumberAnggaranNama = $anggaran->sumberAnggaran->nama;

                        // Map targets dengan urutan yang benar berdasarkan periode_id
                        $targetsArray = [];

                        // Inisialisasi array untuk 4 triwulan
                        for ($i = 0; $i < 4; $i++) {
                            $targetsArray[$i] = [
                                'kinerja_fisik' => 0,
                                'keuangan' => 0,
                                'periode_id' => $i + 2 // periode_id 2,3,4,5 untuk triwulan 1,2,3,4
                            ];
                        }

                        // Isi data target yang ada
                        foreach ($anggaran->target as $target) {
                            // Mapping periode_id ke index array (periode_id 2 = index 0, dst)
                            $triwulanIndex = $target->periode_id - 2;
                            if ($triwulanIndex >= 0 && $triwulanIndex < 4) {
                                $targetsArray[$triwulanIndex] = [
                                    'kinerja_fisik' => $target->kinerja_fisik,
                                    'keuangan' => $target->keuangan,
                                    'periode_id' => $target->periode_id
                                ];
                            }
                        }

                        $targetsBySumberAnggaran[$sumberAnggaranId] = [
                            'sumber_anggaran_id' => $sumberAnggaranId,
                            'sumber_anggaran_nama' => $sumberAnggaranNama,
                            'targets' => $targetsArray
                        ];
                    }
                }

                // Add monitoring data to subkegiatan
                $subkegiatan->monitoring = [
                    'id' => $monitoring->id,
                    'targets_by_sumber_anggaran' => $targetsBySumberAnggaran,
                    // PERBAIKAN: Hapus fallback targets yang menyebabkan cross-contamination
                    // Frontend harus menggunakan targets_by_sumber_anggaran yang spesifik
                    'targets' => [] // Kosongkan untuk mencegah fallback yang salah
                ];
            }

            return $subkegiatan;
        });

        return Inertia::render('Monitoring/RencanaAwal', [
            'tugas' => $tugas,
            'bidangurusanTugas' => $bidangurusanTugas,
            'programTugas' => $programTugas,
            'kegiatanTugas' => $kegiatanTugas,
            'subkegiatanTugas' => $subkegiatanWithTargets,
            'kepalaSkpd' => $kepalaSkpd,
            'user' => [
                'id' => $skpdUser?->id ?? $tugas->skpd_id, // Use user ID instead of skpd_id
                'nama_skpd' => $tugas->skpd->nama_skpd ?? $tugas->skpd->nama_dinas,
                'skpd_id' => $tugas->skpd_id // Keep skpd_id for other purposes
            ],
            'periodeAktif' => $periodeAktif,
            'tahunAktif' => $tahunAktif,
            'semuaPeriodeAktif' => $semuaPeriodeAktif,
            'dataAnggaranTerakhir' => $dataAnggaranTerakhir,
            'bidangUrusanList' => $bidangurusanTugas,
        ]);
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

        // Try to connect with existing monitoring data if we have a tugas_id
        if (isset($validated['tugas_id'])) {
            $existingMonitoring = Monitoring::where('skpd_tugas_id', $validated['tugas_id'])
                ->where('tahun', $validated['tahun'])
                ->first();

            if ($existingMonitoring) {
                // Update existing monitoring
                $existingMonitoring->update([
                    'sumber_dana' => $validated['sumber_dana'],
                    'periode_id' => $validated['periode_id'],
                    'deskripsi' => $validated['deskripsi'],
                    'pagu_pokok' => $validated['pagu_pokok'],
                    'pagu_parsial' => $validated['pagu_parsial'] ?? 0,
                    'pagu_perubahan' => $validated['pagu_perubahan'] ?? 0,
                ]);

                // Clear existing targets and recreate them
                $existingMonitoring->targets()->delete();

                foreach ($validated['targets'] as $target) {
                    $existingMonitoring->targets()->create([
                        'kinerja_fisik' => $target['kinerja_fisik'],
                        'keuangan' => $target['keuangan'],
                    ]);
                }

                return back()->with('success', 'Data monitoring berhasil diperbarui.');
            }

            // No existing monitoring found, so create a new one with the tugas_id
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
            $monitoring->save();

            foreach ($validated['targets'] as $target) {
                $monitoring->targets()->create([
                    'kinerja_fisik' => $target['kinerja_fisik'],
                    'keuangan' => $target['keuangan'],
                ]);
            }

            // If funding data was provided (from Sumberdana.vue), also update that connection
            if (isset($validated['sumber_anggaran']) && isset($validated['funding_values'])) {
                // Use the existing saveSumberDana logic but without creating a response
                $aktivPeriode = Periode::where('id', $validated['periode_id'])->first();

                if ($aktivPeriode) {
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
            }

            return back()->with('success', 'Data monitoring berhasil disimpan.');
        }

        // Create regular monitoring without tugas_id if none provided
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

    public function saveSumberDana(Request $request)
    {
        \Log::info('=== SAVE SUMBER DANA DEBUG ===');
        \Log::info('Request data:', $request->all());

        // Cek apakah ada periode yang aktif (status 1 = buka) dengan tahap "Rencana"
        $aktivPeriode = Periode::where('status', 1)
            ->whereHas('tahap', function($query) {
                $query->where('tahap', 'Rencana');
            })
            ->first();

        \Log::info('Active periode found:', ['periode' => $aktivPeriode ? $aktivPeriode->toArray() : null]);

        if (!$aktivPeriode) {
            \Log::warning('No active Rencana periode found');
            return response()->json([
                'success' => false,
                'message' => 'Periode Rencana belum dibuka. Sumber dana hanya dapat diisi pada periode Rencana.'
            ], 422);
        }

        $validated = $request->validate([
            'skpd_tugas_id' => 'required|exists:skpd_tugas,id',
            'sumber_anggaran' => 'required|array',
            'sumber_anggaran.dak' => 'required|boolean',
            'sumber_anggaran.dak_peruntukan' => 'required|boolean',
            'sumber_anggaran.dak_fisik' => 'required|boolean',
            'sumber_anggaran.dak_non_fisik' => 'required|boolean',
            'sumber_anggaran.blud' => 'required|boolean',
            'values' => 'required|array',
            'values.dak' => 'required|numeric',
            'values.dak_peruntukan' => 'required|numeric',
            'values.dak_fisik' => 'required|numeric',
            'values.dak_non_fisik' => 'required|numeric',
            'values.blud' => 'required|numeric',
        ]);

        \Log::info('Validation passed, validated data:', ['validated' => $validated]);

        DB::beginTransaction();

        try {
            $skpdTugas = SkpdTugas::findOrFail($validated['skpd_tugas_id']);
            \Log::info('SKPD Tugas found:', ['skpd_tugas' => $skpdTugas->toArray()]);

            $monitoring = Monitoring::where('skpd_tugas_id', $validated['skpd_tugas_id'])
                ->where('tahun', date('Y'))
                ->first();

            \Log::info('Existing monitoring:', ['monitoring' => $monitoring ? $monitoring->toArray() : null]);

            if (!$monitoring) {
                \Log::info('Creating new monitoring record');
                $monitoring = new Monitoring();
                $monitoring->skpd_tugas_id = $validated['skpd_tugas_id'];
                $monitoring->periode_id = $aktivPeriode->id; // ← FIX: Set periode_id
                $monitoring->tahun = date('Y');
                // Set field minimal untuk manajemen anggaran - deskripsi dan nama_pptk harus diisi karena NOT NULL
                $monitoring->deskripsi = ''; // Empty string karena field NOT NULL, akan diisi saat input triwulan
                $monitoring->nama_pptk = ''; // Empty string karena field NOT NULL, akan diisi saat input triwulan
                $monitoring->save();
                \Log::info('New monitoring created:', ['monitoring' => $monitoring->toArray()]);
            } else {
                \Log::info('Using existing monitoring record');
            }

            $periode = $aktivPeriode;

            foreach ($validated['sumber_anggaran'] as $key => $value) {
                \Log::info('Processing sumber anggaran', ['key' => $key, 'value' => $value]);

                if ($value) {
                    // Cari sumber anggaran yang sudah ada, *jangan buat baru jika tidak ditemukan*
                    $mappedName = $this->mapNamaSumberAnggaran($key);
                    \Log::info('Mapped sumber anggaran name', ['key' => $key, 'mapped_name' => $mappedName]);

                    $sumberAnggaran = SumberAnggaran::where('nama', $mappedName)->first();
                    \Log::info('Sumber anggaran found', ['sumber_anggaran' => $sumberAnggaran ? $sumberAnggaran->toArray() : null]);

                    if (!$sumberAnggaran) {
                        \Log::warning('Sumber anggaran not found, skipping', ['mapped_name' => $mappedName]);
                        // Jika sumber anggaran tidak ditemukan, skip proses penyimpanan untuk sumber ini
                        continue;
                    }

                    // Cari atau buat monitoring_anggaran
                    $monitoringAnggaran = MonitoringAnggaran::firstOrCreate([
                        'monitoring_id' => $monitoring->id,
                        'sumber_anggaran_id' => $sumberAnggaran->id,
                    ]);
                    \Log::info('Monitoring anggaran created/found', ['monitoring_anggaran' => $monitoringAnggaran->toArray()]);

                    // Simpan atau update pagu anggaran kategori pokok
                    $monitoringPagu = MonitoringPagu::where('monitoring_anggaran_id', $monitoringAnggaran->id)
                        ->where('periode_id', $periode->id)
                        ->where('kategori', 1)
                        ->first();

                    $danaValue = (int)$validated['values'][$key];
                    \Log::info('Processing pagu', ['dana_value' => $danaValue, 'existing_pagu' => $monitoringPagu ? $monitoringPagu->toArray() : null]);

                    if ($monitoringPagu) {
                        \Log::info('Updating existing pagu');
                        $monitoringPagu->dana = $danaValue;
                        $monitoringPagu->save();
                    } else {
                        \Log::info('Creating new pagu');
                        $monitoringPagu = new MonitoringPagu();
                        $monitoringPagu->monitoring_anggaran_id = $monitoringAnggaran->id;
                        $monitoringPagu->periode_id = $periode->id;
                        $monitoringPagu->kategori = 1;
                        $monitoringPagu->dana = $danaValue;
                        $monitoringPagu->save();
                    }
                    \Log::info('Pagu saved', ['pagu' => $monitoringPagu->toArray()]);
                }
            }

            // PERBAIKAN: Jangan hapus pagu sumber anggaran lain
            // Hanya hapus pagu untuk sumber anggaran yang secara eksplisit di-uncheck (false)
            // Jika tidak ada dalam request, biarkan tetap ada (jangan hapus)

            foreach ($validated['sumber_anggaran'] as $key => $value) {
                // Hanya hapus jika secara eksplisit di-set false DAN ada nilai 0
                if ($value === false && $validated['values'][$key] == 0) {
                    $mappedName = $this->mapNamaSumberAnggaran($key);
                    $sumberAnggaran = SumberAnggaran::where('nama', $mappedName)->first();

                    if ($sumberAnggaran) {
                        $monitoringAnggaran = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                            ->where('sumber_anggaran_id', $sumberAnggaran->id)
                            ->first();

                        if ($monitoringAnggaran) {
                            MonitoringPagu::where('monitoring_anggaran_id', $monitoringAnggaran->id)
                                ->where('periode_id', $periode->id)
                                ->where('kategori', 1)
                                ->delete();
                        }
                    }
                }
            }

            DB::commit();
            \Log::info('✅ Transaction committed successfully');

            // Return as a regular response, not JSON
            if ($request->wantsJson()) {
                \Log::info('Returning JSON response');
                return response()->json([
                    'success' => true,
                    'message' => 'Data sumber anggaran berhasil disimpan.'
                ]);
            }

            \Log::info('Returning redirect response');
            return back()->with('success', 'Data sumber anggaran berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving sumber dana', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Fix existing monitoring records with NULL periode_id
     * This method should be called once to fix existing data
     */
    public function fixNullPeriodeId()
    {
        try {
            // Get active periode for "Rencana" phase
            $aktivPeriode = Periode::where('status', 1)
                ->whereHas('tahap', function($query) {
                    $query->where('tahap', 'Rencana');
                })
                ->first();

            if (!$aktivPeriode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada periode Rencana yang aktif'
                ], 422);
            }

            // Update all monitoring records with NULL periode_id
            $updatedCount = Monitoring::whereNull('periode_id')
                ->where('tahun', date('Y'))
                ->update(['periode_id' => $aktivPeriode->id]);

            return response()->json([
                'success' => true,
                'message' => "Berhasil memperbaiki {$updatedCount} record monitoring dengan periode_id NULL"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

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

    private function reverseMapNamaSumberAnggaran(string $nama): string
    {
        $reverseMapping = [
            'DAU' => 'dak',
            'DAU Peruntukan' => 'dak_peruntukan',
            'DAK Fisik' => 'dak_fisik',
            'DAK Non Fisik' => 'dak_non_fisik',
            'BLUD' => 'blud',
        ];

        return $reverseMapping[$nama] ?? $nama;
    }
}

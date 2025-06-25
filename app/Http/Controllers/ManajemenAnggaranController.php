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

        // Check if Triwulan 4 is active for budget change functionality
        $triwulan4Aktif = Periode::with(['tahap', 'tahun'])
            ->where('status', 1)
            ->whereHas('tahap', function($query) {
                $query->where('tahap', 'Triwulan 4');
            })
            ->first();

        return Inertia::render('ManajemenAnggaran', [
            'users' => $transformedUsers,
            'enabledParsialUsers' => session('enabled_parsial_users', []),
            'triwulan4Aktif' => $triwulan4Aktif,
            'isBudgetChangeAvailable' => $triwulan4Aktif !== null
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

        // Check if parsial mode is enabled for this specific user
        $isParsialEnabled = $this->isParsialEnabledForUser($id);

        if ($isParsialEnabled) {
            // Show parsial data if enabled for this user
            return $this->showParsialData($user, $skpdTugas, $urusanList, $bidangUrusanList, $request);
        } else {
            // Show normal rencana awal data if parsial not enabled
            return $this->showRencanaAwalData($user, $skpdTugas, $urusanList, $bidangUrusanList, $request);
        }
    }

    /**
     * Show rencana awal data (normal mode)
     */
    private function showRencanaAwalData($user, $skpdTugas, $urusanList, $bidangUrusanList, $request)
    {
        // PERBAIKAN 1: Get active periods dengan logika yang lebih fleksibel
        $periodeAktif = Periode::with(['tahap', 'tahun'])
            ->where('status', 1)
            ->whereHas('tahap', function($query) {
                $query->where('tahap', 'Rencana');
            })
            ->get();

        \Log::debug('Periode aktif:', ['count' => $periodeAktif->count(), 'data' => $periodeAktif->toArray()]);

        // PERBAIKAN 2: Get all periods for the dropdown (tidak hanya yang aktif)
        $semuaPeriodeAktif = Periode::with(['tahap', 'tahun'])
            ->orderBy('tahun_id', 'desc')
            ->orderBy('tahap_id', 'asc')
            ->get();

        // PERBAIKAN 3: Jika tidak ada periode Rencana aktif, ambil periode Rencana terakhir
        $periodeRencanaFallback = null;
        if ($periodeAktif->isEmpty()) {
            $periodeRencanaFallback = Periode::with(['tahap', 'tahun'])
                ->whereHas('tahap', function($query) {
                    $query->where('tahap', 'Rencana');
                })
                ->latest('created_at')
                ->first();
        }

        // Get current year
        $tahunAktif = null;
        if ($semuaPeriodeAktif->isNotEmpty()) {
            $tahunAktif = $semuaPeriodeAktif->first()->tahun;
        } elseif ($periodeRencanaFallback) {
            $tahunAktif = $periodeRencanaFallback->tahun;
        }

        // Get funding data for each subkegiatan
        $subkegiatanIds = $skpdTugas->where('kode_nomenklatur.jenis_nomenklatur', 4)
            ->pluck('id');

        \Log::debug('Subkegiatan IDs:', ['ids' => $subkegiatanIds->toArray()]);

        $dataAnggaranTerakhir = [];
        $periodeId = null;

        // PERBAIKAN 4: Logika pemilihan periode yang lebih robust
        if ($request->has('periode_id') && $request->periode_id) {
            // Prioritas 1: Periode yang dipilih user
            $periodeId = $request->periode_id;
        } elseif ($periodeAktif->isNotEmpty()) {
            // Prioritas 2: Periode Rencana yang aktif
            $periodeId = $periodeAktif->first()->id;
        } elseif ($periodeRencanaFallback) {
            // Prioritas 3: Periode Rencana terakhir (meskipun sudah ditutup)
            $periodeId = $periodeRencanaFallback->id;
        }
        // Jika $periodeId masih null, akan menampilkan semua data tanpa filter periode

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

                    // PERBAIKAN 5: Hanya filter berdasarkan periode jika ada periode yang dipilih
                    if ($periodeId) {
                        $monitoringAnggaranQuery->with(['pagu' => function($query) use ($periodeId) {
                            $query->where('kategori', 1) // Kategori 1 = pokok
                                  ->where('periode_id', $periodeId); // Filter berdasarkan periode
                        }]);
                    } else {
                        // Jika tidak ada periode spesifik, ambil data terbaru
                        $monitoringAnggaranQuery->with(['pagu' => function($query) {
                            $query->where('kategori', 1) // Kategori 1 = pokok
                                  ->latest('created_at'); // Ambil yang terbaru
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
            'semuaPeriodeAktif' => $semuaPeriodeAktif, // Sudah mencakup semua periode
            'dataAnggaranTerakhir' => $dataAnggaranTerakhir,
            'selectedPeriodeId' => $periodeId, // Tambahkan info periode yang dipilih
            'periodeRencanaFallback' => $periodeRencanaFallback, // Info periode fallback
            'isParsialMode' => false,
            'pageTitle' => 'Manajemen Anggaran - Rencana Awal'
        ]);
    }

    /**
     * Show parsial data (parsial mode)
     */
    private function showParsialData($user, $skpdTugas, $urusanList, $bidangUrusanList, $request)
    {
        // Get periods for parsial (should be triwulan periods, not rencana)
        $periodeAktif = Periode::with(['tahap', 'tahun'])
            ->where('status', 1)
            ->whereHas('tahap', function($query) {
                $query->whereIn('tahap', ['Triwulan 1', 'Triwulan 2', 'Triwulan 3', 'Triwulan 4']);
            })
            ->get();

        $semuaPeriodeAktif = Periode::with(['tahap', 'tahun'])
            ->whereHas('tahap', function($query) {
                $query->whereIn('tahap', ['Rencana', 'Triwulan 1', 'Triwulan 2', 'Triwulan 3', 'Triwulan 4']);
            })
            ->orderBy('tahun_id', 'desc')
            ->orderBy('tahap_id', 'asc')
            ->get();

        // Get current year
        $tahunAktif = $semuaPeriodeAktif->isNotEmpty() ? $semuaPeriodeAktif->first()->tahun : null;

        // Get funding data for each subkegiatan including both rencana awal and parsial
        $subkegiatanIds = $skpdTugas->where('kode_nomenklatur.jenis_nomenklatur', 4)->pluck('id');

        $dataAnggaranTerakhir = [];
        $periodeId = null;

        // Logic for periode selection
        if ($request->has('periode_id') && $request->periode_id) {
            $periodeId = $request->periode_id;
        } elseif ($periodeAktif->isNotEmpty()) {
            $periodeId = $periodeAktif->first()->id;
        }

        foreach ($skpdTugas as $tugas) {
            if ($tugas->kodeNomenklatur->jenis_nomenklatur == 4) { // Only sub kegiatan
                // Find monitoring related to this SKPD tugas
                $monitoring = Monitoring::where('skpd_tugas_id', $tugas->id)
                    ->latest()
                    ->first();

                if ($monitoring) {
                    // Get funding data for both rencana awal (kategori 1) and parsial (kategori 2)
                    $sumberAnggaranData = [
                        'rencana_awal' => [],
                        'parsial' => []
                    ];

                    $monitoringAnggaranQuery = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                        ->with(['sumberAnggaran']);

                    // Get rencana awal data (kategori 1)
                    $monitoringAnggaranQuery->with(['pagu' => function($query) {
                        $query->where('kategori', 1); // Kategori 1 = rencana awal
                    }]);

                    $monitoringAnggaran = $monitoringAnggaranQuery->get();

                    foreach ($monitoringAnggaran as $anggaran) {
                        if ($anggaran->sumberAnggaran && $anggaran->pagu->isNotEmpty()) {
                            $key = $this->reverseMapNamaSumberAnggaran($anggaran->sumberAnggaran->nama);
                            if ($key) {
                                $sumberAnggaranData['rencana_awal'][$key] = $anggaran->pagu->first()->dana ?? 0;
                            }
                        }
                    }

                    // Get parsial data (kategori 2) if periode is selected
                    if ($periodeId) {
                        $monitoringAnggaranParsial = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                            ->with(['sumberAnggaran'])
                            ->with(['pagu' => function($query) use ($periodeId) {
                                $query->where('kategori', 2) // Kategori 2 = parsial
                                      ->where('periode_id', $periodeId);
                            }])
                            ->get();

                        foreach ($monitoringAnggaranParsial as $anggaran) {
                            if ($anggaran->sumberAnggaran && $anggaran->pagu->isNotEmpty()) {
                                $key = $this->reverseMapNamaSumberAnggaran($anggaran->sumberAnggaran->nama);
                                if ($key) {
                                    $sumberAnggaranData['parsial'][$key] = $anggaran->pagu->first()->dana ?? 0;
                                }
                            }
                        }
                    }

                    // Check if parsial is enabled for this subkegiatan
                    $isParsialEnabled = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                        ->whereHas('pagu', function($query) {
                            $query->where('kategori', 2); // Check if any parsial data exists
                        })
                        ->exists();

                    // Prepare data structure for frontend (parsial mode)
                    $allKeys = ['dak', 'dak_peruntukan', 'dak_fisik', 'dak_non_fisik', 'blud'];
                    $sumberAnggaranFlags = [];
                    
                    foreach ($allKeys as $key) {
                        $sumberAnggaranFlags[$key] = isset($sumberAnggaranData['rencana_awal'][$key]) || isset($sumberAnggaranData['parsial'][$key]);
                    }

                    // Save data per SKPD tugas
                    $dataAnggaranTerakhir[$tugas->id] = [
                        'sumber_anggaran' => $sumberAnggaranFlags,
                        'values' => [
                            'rencana_awal' => $sumberAnggaranData['rencana_awal'],
                            'parsial' => $sumberAnggaranData['parsial']
                        ],
                        'is_parsial_enabled' => $isParsialEnabled
                    ];

                } else {
                    // No monitoring data exists, create empty structure
                    $dataAnggaranTerakhir[$tugas->id] = [
                        'sumber_anggaran' => [
                            'dak' => false,
                            'dak_peruntukan' => false,
                            'dak_fisik' => false,
                            'dak_non_fisik' => false,
                            'blud' => false,
                        ],
                        'values' => [
                            'rencana_awal' => [],
                            'parsial' => []
                        ],
                        'is_parsial_enabled' => false
                    ];
                }
            }
        }

        return Inertia::render('MonitoringAnggaran/Sumberdana', [
            'user' => $user,
            'skpdTugas' => $skpdTugas,
            'urusanList' => $urusanList,
            'bidangUrusanList' => $bidangUrusanList,
            'periodeAktif' => $periodeAktif,
            'tahunAktif' => $tahunAktif,
            'semuaPeriodeAktif' => $semuaPeriodeAktif,
            'dataAnggaranTerakhir' => $dataAnggaranTerakhir,
            'selectedPeriodeId' => $periodeId,
            'isParsialMode' => true, // Flag to indicate this is parsial mode
            'pageTitle' => 'Manajemen Anggaran - Mode Parsial'
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

        // PERBAIKAN 1: Get active periods dengan logika yang lebih fleksibel
        $periodeAktif = Periode::with(['tahap', 'tahun'])
            ->where('status', 1)
            ->whereHas('tahap', function($query) {
                $query->where('tahap', 'Rencana');
            })
            ->get();

        // PERBAIKAN 2: Get all periods for the dropdown (tidak hanya yang aktif)
        $semuaPeriodeAktif = Periode::with(['tahap', 'tahun'])
            ->orderBy('tahun_id', 'desc')
            ->orderBy('tahap_id', 'asc')
            ->get();

        // PERBAIKAN 3: Jika tidak ada periode Rencana aktif, ambil periode Rencana terakhir
        $periodeRencanaFallback = null;
        if ($periodeAktif->isEmpty()) {
            $periodeRencanaFallback = Periode::with(['tahap', 'tahun'])
                ->whereHas('tahap', function($query) {
                    $query->where('tahap', 'Rencana');
                })
                ->latest('created_at')
                ->first();
        }

        // Get current year
        $tahunAktif = null;
        if ($semuaPeriodeAktif->isNotEmpty()) {
            $tahunAktif = $semuaPeriodeAktif->first()->tahun;
        } elseif ($periodeRencanaFallback) {
            $tahunAktif = $periodeRencanaFallback->tahun;
        }

        // Get funding data for each subkegiatan filtered by active period
        $dataAnggaranTerakhir = [];
        $periodeId = null;

        // PERBAIKAN 4: Logika pemilihan periode yang lebih robust
        if ($request->has('periode_id') && $request->periode_id) {
            // Prioritas 1: Periode yang dipilih user
            $periodeId = $request->periode_id;
        } elseif ($periodeAktif->isNotEmpty()) {
            // Prioritas 2: Periode Rencana yang aktif
            $periodeId = $periodeAktif->first()->id;
        } elseif ($periodeRencanaFallback) {
            // Prioritas 3: Periode Rencana terakhir (meskipun sudah ditutup)
            $periodeId = $periodeRencanaFallback->id;
        }
        // Jika $periodeId masih null, akan menampilkan semua data tanpa filter periode

        foreach ($subkegiatanTugas as $tugas) {
            if ($tugas->kodeNomenklatur->jenis_nomenklatur == 4) { // Only get sub kegiatan
                // Find monitoring related to this SKPD tugas
                $monitoring = Monitoring::where('skpd_tugas_id', $tugas->id)
                    ->latest()
                    ->first();

                if ($monitoring) {
                    // Get funding data for rencana awal (kategori 1), parsial (kategori 2), and budget change (kategori 3)
                    $sumberAnggaranData = [
                        'rencana_awal' => [],
                        'parsial' => [],
                        'budget_change' => []
                    ];

                    // Get rencana awal data (kategori 1)
                    $monitoringAnggaranRencana = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                        ->with(['sumberAnggaran'])
                        ->with(['pagu' => function($query) {
                            $query->where('kategori', 1); // Kategori 1 = rencana awal
                        }])
                        ->get();

                    foreach ($monitoringAnggaranRencana as $anggaran) {
                        if ($anggaran->sumberAnggaran && $anggaran->pagu->isNotEmpty()) {
                            $key = $this->reverseMapNamaSumberAnggaran($anggaran->sumberAnggaran->nama);
                            if ($key) {
                                $sumberAnggaranData['rencana_awal'][$key] = $anggaran->pagu->first()->dana ?? 0;
                            }
                        }
                    }

                    // Get ALL parsial data (kategori 2) from any triwulan periode (not just active)
                    $monitoringAnggaranParsial = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                        ->with(['sumberAnggaran'])
                        ->with(['pagu' => function($query) {
                            $query->where('kategori', 2); // Kategori 2 = parsial
                        }])
                        ->get();

                    // Aggregate all parsial data regardless of periode
                    foreach ($monitoringAnggaranParsial as $anggaran) {
                        if ($anggaran->sumberAnggaran && $anggaran->pagu->isNotEmpty()) {
                            $key = $this->reverseMapNamaSumberAnggaran($anggaran->sumberAnggaran->nama);
                            if ($key) {
                                // Sum up parsial values from all triwulan periods
                                $currentValue = $sumberAnggaranData['parsial'][$key] ?? 0;
                                foreach ($anggaran->pagu as $pagu) {
                                    $currentValue += $pagu->dana ?? 0;
                                }
                                $sumberAnggaranData['parsial'][$key] = $currentValue;
                            }
                        }
                    }

                    // Get ALL budget change data (kategori 3) from any Triwulan 4 periode
                    $monitoringAnggaranBudgetChange = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                        ->with(['sumberAnggaran'])
                        ->with(['pagu' => function($query) {
                            $query->where('kategori', 3); // Kategori 3 = budget change
                        }])
                        ->get();

                    // Aggregate all budget change data
                    foreach ($monitoringAnggaranBudgetChange as $anggaran) {
                        if ($anggaran->sumberAnggaran && $anggaran->pagu->isNotEmpty()) {
                            $key = $this->reverseMapNamaSumberAnggaran($anggaran->sumberAnggaran->nama);
                            if ($key) {
                                // Sum up budget change values
                                $currentValue = $sumberAnggaranData['budget_change'][$key] ?? 0;
                                foreach ($anggaran->pagu as $pagu) {
                                    $currentValue += $pagu->dana ?? 0;
                                }
                                $sumberAnggaranData['budget_change'][$key] = $currentValue;
                            }
                        }
                    }

                    // Check if parsial and budget change are enabled for this subkegiatan
                    $isParsialEnabled = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                        ->whereHas('pagu', function($query) {
                            $query->where('kategori', 2); // Check if any parsial data exists
                        })
                        ->exists();

                    $isBudgetChangeEnabled = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                        ->whereHas('pagu', function($query) {
                            $query->where('kategori', 3); // Check if any budget change data exists
                        })
                        ->exists();

                    // Prepare data structure for frontend - include rencana awal, parsial, and budget change data
                    $allKeys = ['dak', 'dak_peruntukan', 'dak_fisik', 'dak_non_fisik', 'blud'];
                    $sumberAnggaranFlags = [];
                    $normalizedValues = [];
                    
                    foreach ($allKeys as $key) {
                        $sumberAnggaranFlags[$key] = isset($sumberAnggaranData['rencana_awal'][$key]) || 
                                                   isset($sumberAnggaranData['parsial'][$key]) || 
                                                   isset($sumberAnggaranData['budget_change'][$key]);
                        $normalizedValues[$key] = $sumberAnggaranData['rencana_awal'][$key] ?? 0;
                    }

                    // Save data per SKPD tugas - format seperti parsial mode untuk konsistensi
                    $dataAnggaranTerakhir[$tugas->id] = [
                        'sumber_anggaran' => $sumberAnggaranFlags,
                        'values' => [
                            'rencana_awal' => $sumberAnggaranData['rencana_awal'],
                            'parsial' => $sumberAnggaranData['parsial'],
                            'budget_change' => $sumberAnggaranData['budget_change']
                        ],
                        'is_parsial_enabled' => $isParsialEnabled,
                        'is_budget_change_enabled' => $isBudgetChangeEnabled
                    ];

                    \Log::debug("DEBUG: RencanaAwal data for task {$tugas->id}:", [
                        'rencana_awal' => $sumberAnggaranData['rencana_awal'],
                        'parsial' => $sumberAnggaranData['parsial'],
                        'budget_change' => $sumberAnggaranData['budget_change'],
                        'is_parsial_enabled' => $isParsialEnabled,
                        'is_budget_change_enabled' => $isBudgetChangeEnabled,
                        'final_data_structure' => array_keys($dataAnggaranTerakhir[$tugas->id])
                    ]);
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
            'semuaPeriodeAktif' => $semuaPeriodeAktif, // Sudah mencakup semua periode
            'dataAnggaranTerakhir' => $dataAnggaranTerakhir,
            'bidangUrusanList' => $bidangurusanTugas,
            'selectedPeriodeId' => $periodeId, // Tambahkan info periode yang dipilih
            'periodeRencanaFallback' => $periodeRencanaFallback // Info periode fallback
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

    /**
     * Show parsial page for manajemen anggaran
     */
    public function showParsial(string $id, Request $request)
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

        // Transform user data
        $user->nama_dinas = $skpd?->nama_skpd;
        $user->operator_name = $skpd?->operatorAktif?->operator?->name;
        $user->kepala_name = $skpd?->kepalaAktif?->user?->name;
        $user->kode_organisasi = $skpd?->kode_organisasi;

        // Load all SKPD tasks including subkegiatan
        $skpdTugas = SkpdTugas::where('skpd_id', $skpd->id)
            ->where('is_aktif', 1)
            ->with(['kodeNomenklatur.details'])
            ->get();

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

        // Get periods for parsial (should be triwulan periods, not rencana)
        $periodeAktif = Periode::with(['tahap', 'tahun'])
            ->where('status', 1)
            ->whereHas('tahap', function($query) {
                $query->whereIn('tahap', ['Triwulan 1', 'Triwulan 2', 'Triwulan 3', 'Triwulan 4']);
            })
            ->get();

        $semuaPeriodeAktif = Periode::with(['tahap', 'tahun'])
            ->whereHas('tahap', function($query) {
                $query->whereIn('tahap', ['Rencana', 'Triwulan 1', 'Triwulan 2', 'Triwulan 3', 'Triwulan 4']);
            })
            ->orderBy('tahun_id', 'desc')
            ->orderBy('tahap_id', 'asc')
            ->get();

        // Get current year
        $tahunAktif = $semuaPeriodeAktif->isNotEmpty() ? $semuaPeriodeAktif->first()->tahun : null;

        // Get funding data for each subkegiatan including both rencana awal and parsial
        $subkegiatanIds = $skpdTugas->where('kode_nomenklatur.jenis_nomenklatur', 4)->pluck('id');

        $dataAnggaranTerakhir = [];
        $periodeId = null;

        // Logic for periode selection
        if ($request->has('periode_id') && $request->periode_id) {
            $periodeId = $request->periode_id;
        } elseif ($periodeAktif->isNotEmpty()) {
            $periodeId = $periodeAktif->first()->id;
        }

        foreach ($skpdTugas as $tugas) {
            if ($tugas->kodeNomenklatur->jenis_nomenklatur == 4) { // Only sub kegiatan
                // Find monitoring related to this SKPD tugas
                $monitoring = Monitoring::where('skpd_tugas_id', $tugas->id)
                    ->latest()
                    ->first();

                if ($monitoring) {
                    // Get funding data for both rencana awal (kategori 1) and parsial (kategori 2)
                    $sumberAnggaranData = [
                        'rencana_awal' => [],
                        'parsial' => []
                    ];

                    $monitoringAnggaranQuery = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                        ->with(['sumberAnggaran']);

                    // Get rencana awal data (kategori 1)
                    $monitoringAnggaranQuery->with(['pagu' => function($query) {
                        $query->where('kategori', 1); // Kategori 1 = rencana awal
                    }]);

                    $monitoringAnggaran = $monitoringAnggaranQuery->get();

                    foreach ($monitoringAnggaran as $anggaran) {
                        if ($anggaran->sumberAnggaran && $anggaran->pagu->isNotEmpty()) {
                            $key = $this->reverseMapNamaSumberAnggaran($anggaran->sumberAnggaran->nama);
                            if ($key) {
                                $sumberAnggaranData['rencana_awal'][$key] = $anggaran->pagu->first()->dana ?? 0;
                            }
                        }
                    }

                    \Log::debug("Rencana Awal data for task {$tugas->id}:", $sumberAnggaranData['rencana_awal']);

                    // Get parsial data (kategori 2) if periode is selected
                    if ($periodeId) {
                        $monitoringAnggaranParsial = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                            ->with(['sumberAnggaran'])
                            ->with(['pagu' => function($query) use ($periodeId) {
                                $query->where('kategori', 2) // Kategori 2 = parsial
                                      ->where('periode_id', $periodeId);
                            }])
                            ->get();

                        foreach ($monitoringAnggaranParsial as $anggaran) {
                            if ($anggaran->sumberAnggaran && $anggaran->pagu->isNotEmpty()) {
                                $key = $this->reverseMapNamaSumberAnggaran($anggaran->sumberAnggaran->nama);
                                if ($key) {
                                    $sumberAnggaranData['parsial'][$key] = $anggaran->pagu->first()->dana ?? 0;
                                }
                            }
                        }
                    }

                    \Log::debug("Parsial data for task {$tugas->id}:", $sumberAnggaranData['parsial']);

                    // Check if parsial is enabled for this subkegiatan
                    $isParsialEnabled = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                        ->whereHas('pagu', function($query) {
                            $query->where('kategori', 2); // Check if any parsial data exists
                        })
                        ->exists();

                    // Prepare data structure for frontend (parsial mode)
                    $allKeys = ['dak', 'dak_peruntukan', 'dak_fisik', 'dak_non_fisik', 'blud'];
                    $sumberAnggaranFlags = [];
                    
                    foreach ($allKeys as $key) {
                        $sumberAnggaranFlags[$key] = isset($sumberAnggaranData['rencana_awal'][$key]) || isset($sumberAnggaranData['parsial'][$key]);
                    }

                    // Save data per SKPD tugas
                    $dataAnggaranTerakhir[$tugas->id] = [
                        'sumber_anggaran' => $sumberAnggaranFlags,
                        'values' => [
                            'rencana_awal' => $sumberAnggaranData['rencana_awal'],
                            'parsial' => $sumberAnggaranData['parsial']
                        ],
                        'is_parsial_enabled' => $isParsialEnabled
                    ];

                    \Log::debug("Final data for task {$tugas->id}:", $dataAnggaranTerakhir[$tugas->id]);
                } else {
                    // No monitoring data exists, create empty structure
                    $dataAnggaranTerakhir[$tugas->id] = [
                        'sumber_anggaran' => [
                            'dak' => false,
                            'dak_peruntukan' => false,
                            'dak_fisik' => false,
                            'dak_non_fisik' => false,
                            'blud' => false,
                        ],
                        'values' => [
                            'rencana_awal' => [],
                            'parsial' => []
                        ],
                        'is_parsial_enabled' => false
                    ];
                }
            }
        }

        return Inertia::render('MonitoringAnggaran/Sumberdana', [
            'user' => $user,
            'skpdTugas' => $skpdTugas,
            'urusanList' => $urusanList,
            'bidangUrusanList' => $bidangUrusanList,
            'periodeAktif' => $periodeAktif,
            'tahunAktif' => $tahunAktif,
            'semuaPeriodeAktif' => $semuaPeriodeAktif,
            'dataAnggaranTerakhir' => $dataAnggaranTerakhir,
            'selectedPeriodeId' => $periodeId,
            'isParsialMode' => true, // Flag to indicate this is parsial mode
            'pageTitle' => 'Manajemen Anggaran Parsial'
        ]);
    }

    /**
     * Open parsial period - enable parsial editing
     */
    public function openParsial(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'confirm' => 'required|boolean|accepted'
        ]);

        try {
            // Check if there's an active triwulan period
            $aktivPeriode = Periode::where('status', 1)
                ->whereHas('tahap', function($query) {
                    $query->whereIn('tahap', ['Triwulan 1', 'Triwulan 2', 'Triwulan 3', 'Triwulan 4']);
                })
                ->first();

            if (!$aktivPeriode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada periode triwulan yang aktif untuk membuka pagu parsial.'
                ], 422);
            }

            // Here you can add logic to mark parsial as enabled for this user/skpd
            // For now, we'll just return success as the enablement is handled by data existence

            return response()->json([
                'success' => true,
                'message' => 'Pagu parsial berhasil dibuka untuk periode ' . $aktivPeriode->tahap->tahap . '.',
                'periode' => $aktivPeriode
            ]);

        } catch (\Exception $e) {
            \Log::error('Error opening parsial', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save parsial data
     */
    public function saveParsial(Request $request)
    {
        \Log::info('=== SAVE PARSIAL DEBUG ===');
        \Log::info('Request data:', $request->all());

        // Check if there's an active triwulan period
        $aktivPeriode = Periode::where('status', 1)
            ->whereHas('tahap', function($query) {
                $query->whereIn('tahap', ['Triwulan 1', 'Triwulan 2', 'Triwulan 3', 'Triwulan 4']);
            })
            ->first();

        if (!$aktivPeriode) {
            \Log::warning('No active triwulan periode found');
            return response()->json([
                'success' => false,
                'message' => 'Periode triwulan belum dibuka. Pagu parsial hanya dapat diisi pada periode triwulan yang aktif.'
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

        DB::beginTransaction();

        try {
            $skpdTugas = SkpdTugas::findOrFail($validated['skpd_tugas_id']);

            $monitoring = Monitoring::where('skpd_tugas_id', $validated['skpd_tugas_id'])
                ->where('tahun', date('Y'))
                ->first();

            if (!$monitoring) {
                // If no monitoring exists, create one
                $monitoring = new Monitoring();
                $monitoring->skpd_tugas_id = $validated['skpd_tugas_id'];
                $monitoring->periode_id = $aktivPeriode->id;
                $monitoring->tahun = date('Y');
                $monitoring->deskripsi = '';
                $monitoring->nama_pptk = '';
                $monitoring->save();
            }

            foreach ($validated['sumber_anggaran'] as $key => $value) {
                if ($value) {
                    $mappedName = $this->mapNamaSumberAnggaran($key);
                    $sumberAnggaran = SumberAnggaran::where('nama', $mappedName)->first();

                    if (!$sumberAnggaran) {
                        continue;
                    }

                    // Find or create monitoring_anggaran
                    $monitoringAnggaran = MonitoringAnggaran::firstOrCreate([
                        'monitoring_id' => $monitoring->id,
                        'sumber_anggaran_id' => $sumberAnggaran->id,
                    ]);

                    // Save or update parsial pagu (kategori 2)
                    $monitoringPagu = MonitoringPagu::where('monitoring_anggaran_id', $monitoringAnggaran->id)
                        ->where('periode_id', $aktivPeriode->id)
                        ->where('kategori', 2) // Kategori 2 = parsial
                        ->first();

                    $danaValue = (int)$validated['values'][$key];

                    if ($monitoringPagu) {
                        $monitoringPagu->dana = $danaValue;
                        $monitoringPagu->save();
                    } else {
                        $monitoringPagu = new MonitoringPagu();
                        $monitoringPagu->monitoring_anggaran_id = $monitoringAnggaran->id;
                        $monitoringPagu->periode_id = $aktivPeriode->id;
                        $monitoringPagu->kategori = 2; // Kategori 2 = parsial
                        $monitoringPagu->dana = $danaValue;
                        $monitoringPagu->save();
                    }
                }
            }

            // Remove parsial data for unchecked sources
            foreach ($validated['sumber_anggaran'] as $key => $value) {
                if ($value === false && $validated['values'][$key] == 0) {
                    $mappedName = $this->mapNamaSumberAnggaran($key);
                    $sumberAnggaran = SumberAnggaran::where('nama', $mappedName)->first();

                    if ($sumberAnggaran) {
                        $monitoringAnggaran = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                            ->where('sumber_anggaran_id', $sumberAnggaran->id)
                            ->first();

                        if ($monitoringAnggaran) {
                            MonitoringPagu::where('monitoring_anggaran_id', $monitoringAnggaran->id)
                                ->where('periode_id', $aktivPeriode->id)
                                ->where('kategori', 2) // Only delete parsial data
                                ->delete();
                        }
                    }
                }
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data pagu parsial berhasil disimpan untuk periode ' . $aktivPeriode->tahap->tahap . '.'
                ]);
            }

            return back()->with('success', 'Data pagu parsial berhasil disimpan untuk periode ' . $aktivPeriode->tahap->tahap . '.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving parsial data', [
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
     * Show rencana awal page in parsial mode for detailed editing
     */
    public function showRencanaAwalParsial($id, Request $request)
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

        // Get periods for parsial (should be triwulan periods, not rencana)
        $periodeAktif = Periode::with(['tahap', 'tahun'])
            ->where('status', 1)
            ->whereHas('tahap', function($query) {
                $query->whereIn('tahap', ['Triwulan 1', 'Triwulan 2', 'Triwulan 3', 'Triwulan 4']);
            })
            ->get();

        $semuaPeriodeAktif = Periode::with(['tahap', 'tahun'])
            ->whereHas('tahap', function($query) {
                $query->whereIn('tahap', ['Rencana', 'Triwulan 1', 'Triwulan 2', 'Triwulan 3', 'Triwulan 4']);
            })
            ->orderBy('tahun_id', 'desc')
            ->orderBy('tahap_id', 'asc')
            ->get();

        // Get current year
        $tahunAktif = $semuaPeriodeAktif->isNotEmpty() ? $semuaPeriodeAktif->first()->tahun : null;

        // Get funding data for each subkegiatan including both rencana awal and parsial
        $dataAnggaranTerakhir = [];
        $periodeId = null;

        // Logic for periode selection
        if ($request->has('periode_id') && $request->periode_id) {
            $periodeId = $request->periode_id;
        } elseif ($periodeAktif->isNotEmpty()) {
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

                    // PERBAIKAN 5: Hanya filter berdasarkan periode jika ada periode yang dipilih
                    if ($periodeId) {
                        $monitoringAnggaranQuery->with(['pagu' => function($query) use ($periodeId) {
                            $query->where('kategori', 1) // Category 1 = pokok
                                  ->where('periode_id', $periodeId); // Filter by period
                        }]);
                    } else {
                        // Jika tidak ada periode spesifik, ambil data terbaru
                        $monitoringAnggaranQuery->with(['pagu' => function($query) {
                            $query->where('kategori', 1) // Category 1 = pokok
                                  ->latest('created_at'); // Ambil yang terbaru
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

        return Inertia::render('Monitoring/RencanaAwal', [
            'tugas' => $tugas,
            'bidangurusanTugas' => $bidangurusanTugas,
            'programTugas' => $programTugas,
            'kegiatanTugas' => $kegiatanTugas,
            'subkegiatanTugas' => $subkegiatanTugas,
            'kepalaSkpd' => $kepalaSkpd,
            'user' => [
                'id' => $skpdUser?->id ?? $tugas->skpd_id,
                'nama_skpd' => $tugas->skpd->nama_skpd ?? $tugas->skpd->nama_dinas,
                'skpd_id' => $tugas->skpd_id
            ],
            'periodeAktif' => $periodeAktif,
            'tahunAktif' => $tahunAktif,
            'semuaPeriodeAktif' => $semuaPeriodeAktif,
            'dataAnggaranTerakhir' => $dataAnggaranTerakhir,
            'bidangUrusanList' => $bidangurusanTugas,
            'selectedPeriodeId' => $periodeId,
            'isParsialMode' => true, // Flag to indicate this is parsial mode
            'pageTitle' => 'Rencana Awal - Mode Parsial'
                ]);
    }

    /**
     * Enable parsial mode for specific user/SKPD
     */
    public function enableParsialForUser(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'confirm' => 'required|boolean|accepted'
        ]);

        try {
            // Check if there's an active triwulan period
            $aktivPeriode = Periode::where('status', 1)
                ->whereHas('tahap', function($query) {
                    $query->whereIn('tahap', ['Triwulan 1', 'Triwulan 2', 'Triwulan 3', 'Triwulan 4']);
                })
                ->first();

            if (!$aktivPeriode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada periode triwulan yang aktif untuk membuka mode parsial.'
                ], 422);
            }

            // Get user and SKPD name for better message
            $user = User::with('skpd')->findOrFail($validated['user_id']);
            $skpd = $user->skpd->first();
            $skpdName = $skpd ? $skpd->nama_skpd : 'SKPD';

            // Add user to enabled parsial list in session
            $enabledParsialUsers = session('enabled_parsial_users', []);
            if (!in_array($validated['user_id'], $enabledParsialUsers)) {
                $enabledParsialUsers[] = $validated['user_id'];
                session(['enabled_parsial_users' => $enabledParsialUsers]);
            }

            return response()->json([
                'success' => true,
                'message' => "Mode parsial berhasil diaktifkan untuk {$skpdName}.",
                'periode' => $aktivPeriode
            ]);

        } catch (\Exception $e) {
            \Log::error('Error enabling parsial for user', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if parsial mode is enabled for specific user
     */
    private function isParsialEnabledForUser($userId)
    {
        $enabledParsialUsers = session('enabled_parsial_users', []);
        return in_array((int)$userId, $enabledParsialUsers);
    }

    /**
     * Enable budget change mode for all departments when Triwulan 4 is active
     */
    public function enableBudgetChangeForAll(Request $request)
    {
        try {
            // Check if Triwulan 4 is active
            $triwulan4Aktif = Periode::with(['tahap', 'tahun'])
                ->where('status', 1)
                ->whereHas('tahap', function($query) {
                    $query->where('tahap', 'Triwulan 4');
                })
                ->first();

            if (!$triwulan4Aktif) {
                return response()->json([
                    'success' => false,
                    'message' => 'Periode Triwulan 4 belum dibuka. Perubahan anggaran hanya dapat dilakukan pada periode Triwulan 4.'
                ], 422);
            }

            // Get all perangkat daerah users
            $perangkatDaerahUsers = User::role('perangkat_daerah')
                ->with('skpd')
                ->get();

            // Enable budget change for all departments
            $enabledBudgetChangeUsers = [];
            foreach ($perangkatDaerahUsers as $user) {
                $enabledBudgetChangeUsers[] = $user->id;
            }

            // Store in session
            session(['enabled_budget_change_users' => $enabledBudgetChangeUsers]);
            session(['budget_change_periode_id' => $triwulan4Aktif->id]);

            \Log::info('Budget change enabled for all departments', [
                'periode' => $triwulan4Aktif->toArray(),
                'enabled_users' => $enabledBudgetChangeUsers
            ]);

            return response()->json([
                'success' => true,
                'message' => "Mode perubahan anggaran berhasil diaktifkan untuk semua perangkat daerah pada periode Triwulan 4 tahun {$triwulan4Aktif->tahun->tahun}.",
                'periode' => $triwulan4Aktif,
                'enabled_count' => count($enabledBudgetChangeUsers)
            ]);

        } catch (\Exception $e) {
            \Log::error('Error enabling budget change for all', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if budget change mode is enabled for specific user
     */
    private function isBudgetChangeEnabledForUser($userId)
    {
        $enabledBudgetChangeUsers = session('enabled_budget_change_users', []);
        return in_array((int)$userId, $enabledBudgetChangeUsers);
    }

    /**
     * Show budget change mode for specific user
     */
    public function showBudgetChange(string $id, Request $request)
    {
        // Check if budget change is enabled for this user
        if (!$this->isBudgetChangeEnabledForUser($id)) {
            return redirect()->back()->with('error', 'Mode perubahan anggaran belum diaktifkan untuk perangkat daerah ini.');
        }

        // Check if Triwulan 4 is active
        $triwulan4Aktif = Periode::with(['tahap', 'tahun'])
            ->where('status', 1)
            ->whereHas('tahap', function($query) {
                $query->where('tahap', 'Triwulan 4');
            })
            ->first();

        if (!$triwulan4Aktif) {
            return redirect()->back()->with('error', 'Periode Triwulan 4 tidak aktif. Perubahan anggaran tidak dapat dilakukan.');
        }

        $user = User::with([
            'skpd' => function($q) {
                $q->with(['kepalaAktif.user', 'operatorAktif.operator']);
            },
            'userDetail'
        ])->findOrFail($id);

        $skpd = $user->skpd->first();

        if (!$skpd) {
            return redirect()->back()->with('error', 'User tidak memiliki SKPD yang terkait.');
        }

        // Transform user data
        $user->nama_dinas = $skpd?->nama_skpd;
        $user->operator_name = $skpd?->operatorAktif?->operator?->name;
        $user->kepala_name = $skpd?->kepalaAktif?->user?->name;
        $user->kode_organisasi = $skpd?->kode_organisasi;

        // Load all SKPD tasks including subkegiatan
        $skpdTugas = SkpdTugas::where('skpd_id', $skpd->id)
            ->where('is_aktif', 1)
            ->with(['kodeNomenklatur.details'])
            ->get();

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

        // Get current year
        $tahunAktif = $triwulan4Aktif->tahun;

        // Get funding data for each subkegiatan including both original and budget change data
        $subkegiatanIds = $skpdTugas->where('kode_nomenklatur.jenis_nomenklatur', 4)->pluck('id');

        $dataAnggaranTerakhir = [];

        foreach ($skpdTugas as $tugas) {
            if ($tugas->kodeNomenklatur->jenis_nomenklatur == 4) { // Only sub kegiatan
                // Find monitoring related to this SKPD tugas
                $monitoring = Monitoring::where('skpd_tugas_id', $tugas->id)
                    ->latest()
                    ->first();

                if ($monitoring) {
                    // Get funding data for rencana awal (kategori 1), parsial (kategori 2), and budget change (kategori 3)
                    $sumberAnggaranData = [
                        'rencana_awal' => [],
                        'parsial' => [],
                        'budget_change' => []
                    ];

                    // Get rencana awal data (kategori 1)
                    $monitoringAnggaranRencana = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                        ->with(['sumberAnggaran'])
                        ->with(['pagu' => function($query) {
                            $query->where('kategori', 1); // Kategori 1 = rencana awal
                        }])
                        ->get();

                    foreach ($monitoringAnggaranRencana as $anggaran) {
                        if ($anggaran->sumberAnggaran && $anggaran->pagu->isNotEmpty()) {
                            $key = $this->reverseMapNamaSumberAnggaran($anggaran->sumberAnggaran->nama);
                            if ($key) {
                                $sumberAnggaranData['rencana_awal'][$key] = $anggaran->pagu->first()->dana ?? 0;
                            }
                        }
                    }

                    // Get ALL parsial data (kategori 2) from any triwulan periode (not just active)
                    // Data parsial bisa tersimpan di Triwulan 1, 2, 3, atau 4
                    $monitoringAnggaranParsial = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                        ->with(['sumberAnggaran'])
                        ->with(['pagu' => function($query) {
                            $query->where('kategori', 2); // Kategori 2 = parsial
                        }])
                        ->get();

                    // Aggregate all parsial data regardless of periode
                    foreach ($monitoringAnggaranParsial as $anggaran) {
                        if ($anggaran->sumberAnggaran && $anggaran->pagu->isNotEmpty()) {
                            $key = $this->reverseMapNamaSumberAnggaran($anggaran->sumberAnggaran->nama);
                            if ($key) {
                                // Sum up parsial values from all triwulan periods
                                $currentValue = $sumberAnggaranData['parsial'][$key] ?? 0;
                                foreach ($anggaran->pagu as $pagu) {
                                    $currentValue += $pagu->dana ?? 0;
                                }
                                $sumberAnggaranData['parsial'][$key] = $currentValue;
                            }
                        }
                    }

                    \Log::debug("DEBUG: Parsial data for monitoring {$monitoring->id} (tugas {$tugas->id}):", [
                        'parsial_data' => $sumberAnggaranData['parsial'],
                        'rencana_awal_data' => $sumberAnggaranData['rencana_awal'],
                        'monitoring_anggaran_parsial_count' => $monitoringAnggaranParsial->count(),
                        'raw_monitoring_anggaran_parsial' => $monitoringAnggaranParsial->map(function($item) {
                            return [
                                'id' => $item->id,
                                'sumber_anggaran' => $item->sumberAnggaran?->nama,
                                'pagu_count' => $item->pagu->count(),
                                'pagu_data' => $item->pagu->map(function($pagu) {
                                    return [
                                        'id' => $pagu->id,
                                        'kategori' => $pagu->kategori,
                                        'periode_id' => $pagu->periode_id,
                                        'dana' => $pagu->dana
                                    ];
                                })
                            ];
                        })
                    ]);

                    // Get budget change data (kategori 3) for Triwulan 4
                    $monitoringAnggaranBudgetChange = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                        ->with(['sumberAnggaran'])
                        ->with(['pagu' => function($query) use ($triwulan4Aktif) {
                            $query->where('kategori', 3) // Kategori 3 = budget change
                                  ->where('periode_id', $triwulan4Aktif->id);
                        }])
                        ->get();

                    foreach ($monitoringAnggaranBudgetChange as $anggaran) {
                        if ($anggaran->sumberAnggaran && $anggaran->pagu->isNotEmpty()) {
                            $key = $this->reverseMapNamaSumberAnggaran($anggaran->sumberAnggaran->nama);
                            if ($key) {
                                $sumberAnggaranData['budget_change'][$key] = $anggaran->pagu->first()->dana ?? 0;
                            }
                        }
                    }

                    // Prepare data structure for frontend (budget change mode) - similar to parsial mode
                    $allKeys = ['dak', 'dak_peruntukan', 'dak_fisik', 'dak_non_fisik', 'blud'];
                    $sumberAnggaranFlags = [];
                    
                    foreach ($allKeys as $key) {
                        $sumberAnggaranFlags[$key] = isset($sumberAnggaranData['rencana_awal'][$key]) || 
                                                   isset($sumberAnggaranData['parsial'][$key]) || 
                                                   isset($sumberAnggaranData['budget_change'][$key]);
                    }

                    // Save data per SKPD tugas with structure similar to parsial mode
                    $dataAnggaranTerakhir[$tugas->id] = [
                        'sumber_anggaran' => $sumberAnggaranFlags,
                        'values' => [
                            'rencana_awal' => $sumberAnggaranData['rencana_awal'],
                            'parsial' => $sumberAnggaranData['parsial'],
                            'budget_change' => $sumberAnggaranData['budget_change']
                        ],
                        'is_budget_change_enabled' => true
                    ];

                } else {
                    // No monitoring data exists, create empty structure
                    $dataAnggaranTerakhir[$tugas->id] = [
                        'sumber_anggaran' => [
                            'dak' => false,
                            'dak_peruntukan' => false,
                            'dak_fisik' => false,
                            'dak_non_fisik' => false,
                            'blud' => false,
                        ],
                        'values' => [
                            'rencana_awal' => [],
                            'parsial' => [],
                            'budget_change' => []
                        ],
                        'is_budget_change_enabled' => true
                    ];
                }
            }
        }

        return Inertia::render('MonitoringAnggaran/Sumberdana', [
            'user' => $user,
            'skpdTugas' => $skpdTugas,
            'urusanList' => $urusanList,
            'bidangUrusanList' => $bidangUrusanList,
            'periodeAktif' => [$triwulan4Aktif], // Pass as array for consistency
            'tahunAktif' => $tahunAktif,
            'semuaPeriodeAktif' => [$triwulan4Aktif],
            'dataAnggaranTerakhir' => $dataAnggaranTerakhir,
            'selectedPeriodeId' => $triwulan4Aktif->id,
            'isBudgetChangeMode' => true, // Flag to indicate this is budget change mode
            'pageTitle' => 'Perubahan Anggaran - Triwulan 4'
        ]);
    }

    /**
     * Save budget change data
     */
    public function saveBudgetChange(Request $request)
    {
        \Log::info('=== SAVE BUDGET CHANGE DEBUG ===');
        \Log::info('Request data:', $request->all());

        // Check if Triwulan 4 is active
        $triwulan4Aktif = Periode::where('status', 1)
            ->whereHas('tahap', function($query) {
                $query->where('tahap', 'Triwulan 4');
            })
            ->first();

        if (!$triwulan4Aktif) {
            \Log::warning('No active Triwulan 4 periode found');
            return response()->json([
                'success' => false,
                'message' => 'Periode Triwulan 4 belum dibuka. Perubahan anggaran hanya dapat dilakukan pada periode Triwulan 4 yang aktif.'
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

        DB::beginTransaction();

        try {
            $skpdTugas = SkpdTugas::findOrFail($validated['skpd_tugas_id']);

            $monitoring = Monitoring::where('skpd_tugas_id', $validated['skpd_tugas_id'])
                ->where('tahun', date('Y'))
                ->first();

            if (!$monitoring) {
                // If no monitoring exists, create one
                $monitoring = new Monitoring();
                $monitoring->skpd_tugas_id = $validated['skpd_tugas_id'];
                $monitoring->periode_id = $triwulan4Aktif->id;
                $monitoring->tahun = date('Y');
                $monitoring->deskripsi = '';
                $monitoring->nama_pptk = '';
                $monitoring->save();
            }

            foreach ($validated['sumber_anggaran'] as $key => $value) {
                if ($value) {
                    $mappedName = $this->mapNamaSumberAnggaran($key);
                    $sumberAnggaran = SumberAnggaran::where('nama', $mappedName)->first();

                    if (!$sumberAnggaran) {
                        continue;
                    }

                    // Find or create monitoring_anggaran
                    $monitoringAnggaran = MonitoringAnggaran::firstOrCreate([
                        'monitoring_id' => $monitoring->id,
                        'sumber_anggaran_id' => $sumberAnggaran->id,
                    ]);

                    // Save or update budget change pagu (kategori 3)
                    $monitoringPagu = MonitoringPagu::where('monitoring_anggaran_id', $monitoringAnggaran->id)
                        ->where('periode_id', $triwulan4Aktif->id)
                        ->where('kategori', 3) // Kategori 3 = budget change
                        ->first();

                    $danaValue = (int)$validated['values'][$key];

                    if ($monitoringPagu) {
                        $monitoringPagu->dana = $danaValue;
                        $monitoringPagu->save();
                    } else {
                        $monitoringPagu = new MonitoringPagu();
                        $monitoringPagu->monitoring_anggaran_id = $monitoringAnggaran->id;
                        $monitoringPagu->periode_id = $triwulan4Aktif->id;
                        $monitoringPagu->kategori = 3; // Kategori 3 = budget change
                        $monitoringPagu->dana = $danaValue;
                        $monitoringPagu->save();
                    }
                }
            }

            // Remove budget change data for unchecked sources
            foreach ($validated['sumber_anggaran'] as $key => $value) {
                if ($value === false && $validated['values'][$key] == 0) {
                    $mappedName = $this->mapNamaSumberAnggaran($key);
                    $sumberAnggaran = SumberAnggaran::where('nama', $mappedName)->first();

                    if ($sumberAnggaran) {
                        $monitoringAnggaran = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                            ->where('sumber_anggaran_id', $sumberAnggaran->id)
                            ->first();

                        if ($monitoringAnggaran) {
                            MonitoringPagu::where('monitoring_anggaran_id', $monitoringAnggaran->id)
                                ->where('periode_id', $triwulan4Aktif->id)
                                ->where('kategori', 3) // Only delete budget change data
                                ->delete();
                        }
                    }
                }
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data perubahan anggaran berhasil disimpan untuk periode Triwulan 4.'
                ]);
            }

            return back()->with('success', 'Data perubahan anggaran berhasil disimpan untuk periode Triwulan 4.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving budget change data', [
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

 
}

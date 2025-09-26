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
use App\Models\PeriodeTahun;
use App\Services\UserActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ManajemenAnggaranController extends Controller
{
    /**
     * Get tahun aktif atau tahun yang dipilih
     */
    private function getTahunAktif(Request $request)
    {
        // Jika ada parameter tahun_id di request, gunakan itu
        if ($request->has('tahun_id') && $request->tahun_id) {
            return PeriodeTahun::find($request->tahun_id);
        }

        // Jika tidak, ambil tahun aktif
        return PeriodeTahun::getTahunAktif();
    }

    /**
     * Get semua tahun untuk dropdown
     */
    private function getAllTahun()
    {
        return PeriodeTahun::orderByDesc('tahun')->get();
    }
    public function index(Request $request, $tahun = null)
    {
        $user = auth()->user();

        if ($user->hasRole('perangkat_daerah')) {
            return redirect()->route('manajemenanggaran.show', $user->id);
        }

        $query = User::role('perangkat_daerah')
            ->with(['skpd' => function($q) {
                $q->with(['kepalaAktif.user.userDetail', 'operatorAktif.operator.userDetail']);
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
            
            // Add missing NIP fields - matching MonitoringController pattern
            $user->operator_nip = $skpd?->operatorAktif?->operator?->userDetail?->nip;
            $user->kepala_nip = $skpd?->kepalaAktif?->user?->userDetail?->nip;

            return $user;
        });

        // Check if Triwulan 4 is active for budget change functionality
        $triwulan4Aktif = Periode::with(['tahap', 'tahun'])
            ->where('status', 1)
            ->whereHas('tahap', function($query) {
                $query->where('tahap', 'Triwulan 4');
            })
            ->first();

        // Get tahun aktif dan semua tahun untuk dropdown
        // Jika ada parameter tahun, gunakan tahun tersebut
        if ($tahun) {
            $tahunAktif = PeriodeTahun::where('tahun', $tahun)->first();
            if (!$tahunAktif) {
                $tahunAktif = PeriodeTahun::getTahunAktif();
            }
        } else {
            $tahunAktif = $this->getTahunAktif($request);
        }
        $allTahun = $this->getAllTahun();

        return Inertia::render('ManajemenAnggaran', [
            'users' => $transformedUsers,
            'enabledParsialUsers' => session('enabled_parsial_users', []),
            'triwulan4Aktif' => $triwulan4Aktif,
            'isBudgetChangeAvailable' => $triwulan4Aktif !== null,
            'tahunAktif' => $tahunAktif,
            'allTahun' => $allTahun,
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
        // Get tahun aktif atau tahun yang dipilih
        $tahunAktif = $this->getTahunAktif($request);
        $allTahun = $this->getAllTahun();

        // PERBAIKAN 1: Get active periods dengan filtering tahun (WAJIB filter berdasarkan tahun aktif)
        $periodeAktif = Periode::with(['tahap', 'tahun'])
            ->where('status', 1)
            ->whereHas('tahap', function($query) {
                $query->where('tahap', 'Rencana');
            })
            ->where('tahun_id', $tahunAktif->id) // WAJIB filter berdasarkan tahun aktif
            ->get();

        \Log::debug('Periode aktif:', ['count' => $periodeAktif->count(), 'data' => $periodeAktif->toArray(), 'tahun_aktif' => $tahunAktif->tahun]);

        // PERBAIKAN 2: Get all periods for the dropdown dengan filtering tahun (WAJIB filter berdasarkan tahun aktif)
        $semuaPeriodeAktif = Periode::with(['tahap', 'tahun'])
            ->where('tahun_id', $tahunAktif->id) // WAJIB filter berdasarkan tahun aktif
            ->whereHas('tahap', function($query) {
                $query->whereIn('tahap', ['Rencana', 'Triwulan 1', 'Triwulan 2', 'Triwulan 3', 'Triwulan 4']);
            })
            ->orderBy('tahap_id', 'asc')
            ->get();

        // PERBAIKAN 3: Jika tidak ada periode Rencana aktif, ambil periode Rencana terakhir untuk tahun aktif
        $periodeRencanaFallback = null;
        if ($periodeAktif->isEmpty()) {
            $periodeRencanaFallback = Periode::with(['tahap', 'tahun'])
                ->whereHas('tahap', function($query) {
                    $query->where('tahap', 'Rencana');
                })
                ->where('tahun_id', $tahunAktif->id) // WAJIB filter berdasarkan tahun aktif
                ->latest('created_at')
                ->first();
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
                // ✅ FIXED: Ambil monitoring yang memiliki data anggaran, prioritaskan tahun aktif tapi fallback ke tahun lain
                $monitoring = Monitoring::where('skpd_tugas_id', $tugas->id)
                    ->where('tahun', $tahunAktif->tahun)
                    ->whereHas('anggaran') // ✅ FIXED: Nama relasi yang benar adalah 'anggaran'
                    ->latest()
                    ->first();

                // Fallback: jika tidak ada data untuk tahun aktif, cari di tahun lain
                if (!$monitoring) {
                    $monitoring = Monitoring::where('skpd_tugas_id', $tugas->id)
                        ->whereHas('anggaran')
                        ->latest()
                        ->first();
                }



                if ($monitoring) {
                    // PERBAIKAN: Ambil data sumber anggaran untuk rencana awal dan parsial
                    $sumberAnggaranData = [
                        'rencana_awal' => [],
                        'parsial' => []
                    ];

                    // Get rencana awal data (kategori 1) - PERBAIKAN: Ambil semua data rencana awal, tidak filter berdasarkan periode
                    $monitoringAnggaranRencana = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                        ->with(['sumberAnggaran'])
                        ->with(['pagu' => function($query) {
                            $query->where('kategori', 1) // Kategori 1 = rencana awal
                                  ->latest('created_at'); // Ambil data terbaru untuk setiap sumber anggaran
                        }])
                        ->get();

                    foreach ($monitoringAnggaranRencana as $anggaran) {
                        if ($anggaran->sumberAnggaran) {
                            $key = $this->reverseMapNamaSumberAnggaran($anggaran->sumberAnggaran->nama);
                            if ($key) {
                                // PERBAIKAN: Ambil data terbaru untuk setiap sumber anggaran, bahkan jika pagu kosong
                                if ($anggaran->pagu->isNotEmpty()) {
                                    // Ambil data pagu terbaru untuk sumber anggaran ini
                                    $latestPagu = $anggaran->pagu->sortByDesc('created_at')->first();
                                    $sumberAnggaranData['rencana_awal'][$key] = $latestPagu->dana ?? 0;
                                } else {
                                    // Jika tidak ada pagu, tetap masukkan dengan nilai 0 untuk menunjukkan sumber anggaran sudah dipilih
                                    $sumberAnggaranData['rencana_awal'][$key] = 0;
                                }
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

                    \Log::debug("Sumber anggaran data for task {$tugas->id}:", $sumberAnggaranData);
                    \Log::debug("Monitoring anggaran rencana count:", ['count' => $monitoringAnggaranRencana->count()]);
                    \Log::debug("Monitoring anggaran parsial count:", ['count' => $monitoringAnggaranParsial->count()]);

                    // Check if there's any parsial data in database (historical data)
                    $hasParsialHistory = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                        ->whereHas('pagu', function($query) {
                            $query->where('kategori', 2); // kategori 2 = parsial
                        })
                        ->exists();

                    // Prepare data structure for frontend (normal mode with parsial history)
                    $allKeys = ['dau', 'dau_peruntukan', 'dak_fisik', 'dak_non_fisik', 'blud']; // ✅ FIXED: dau, dau_peruntukan
                    $sumberAnggaranFlags = [];

                    foreach ($allKeys as $key) {
                        // PERBAIKAN: Pastikan flag sumber anggaran menunjukkan apakah sumber anggaran pernah dipilih
                        $sumberAnggaranFlags[$key] = array_key_exists($key, $sumberAnggaranData['rencana_awal']) || array_key_exists($key, $sumberAnggaranData['parsial']);
                    }

                    // PERBAIKAN: Untuk mode normal, kirim struktur data sederhana yang diharapkan frontend
                    $dataAnggaranTerakhir[$tugas->id] = [
                        'monitoring_id' => $monitoring->id,
                        'sumber_anggaran' => $sumberAnggaranFlags, // Flags boolean untuk checkbox
                        'sumber_anggaran_flags' => $sumberAnggaranFlags,
                        'values' => [
                            // Kirim data rencana_awal sebagai struktur sederhana untuk mode normal
                            'dau' => $sumberAnggaranData['rencana_awal']['dau'] ?? 0, // ✅ FIXED: dau
                            'dau_peruntukan' => $sumberAnggaranData['rencana_awal']['dau_peruntukan'] ?? 0, // ✅ FIXED: dau_peruntukan
                            'dak_fisik' => $sumberAnggaranData['rencana_awal']['dak_fisik'] ?? 0,
                            'dak_non_fisik' => $sumberAnggaranData['rencana_awal']['dak_non_fisik'] ?? 0,
                            'blud' => $sumberAnggaranData['rencana_awal']['blud'] ?? 0,
                            // Simpan juga data lengkap untuk keperluan lain
                            'rencana_awal' => $sumberAnggaranData['rencana_awal'],
                            'parsial' => $sumberAnggaranData['parsial']
                        ],
                        'is_parsial_enabled' => false, // Normal mode but with parsial history
                        'is_budget_change_enabled' => false,
                        'has_parsial_history' => $hasParsialHistory // Flag untuk frontend
                    ];
                } else {
                    // No monitoring data found
                    $dataAnggaranTerakhir[$tugas->id] = [
                        'monitoring_id' => null,
                        'sumber_anggaran' => [
                            'dau' => false, // ✅ FIXED: dau
                            'dau_peruntukan' => false, // ✅ FIXED: dau_peruntukan
                            'dak_fisik' => false,
                            'dak_non_fisik' => false,
                            'blud' => false,
                        ],
                        'values' => [
                            'rencana_awal' => [],
                            'parsial' => []
                        ],
                        'is_parsial_enabled' => false,
                        'is_budget_change_enabled' => false,
                        'has_parsial_history' => false // TAMBAHAN: No parsial history
                    ];
                }
            }
        }
        \Log::info('🚨 DEBUG showRencanaAwalData - Data anggaran terakhir:', ['data' => $dataAnggaranTerakhir]);
        \Log::info('🚨 DEBUG showRencanaAwalData - Periode ID yang digunakan:', ['periode_id' => $periodeId]);
        \Log::info('🚨 DEBUG showRencanaAwalData - Periode aktif count:', ['count' => $periodeAktif->count()]);
        \Log::info('🚨 DEBUG showRencanaAwalData - Semua periode aktif count:', ['count' => $semuaPeriodeAktif->count()]);
        \Log::info('🚨 DEBUG showRencanaAwalData - isParsialMode:', ['isParsialMode' => false]);

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
        // Get tahun aktif atau tahun yang dipilih
        $tahunAktif = $this->getTahunAktif($request);
        $allTahun = $this->getAllTahun();

        // Get periods for parsial (should be triwulan periods, not rencana) dengan filtering tahun
        $periodeAktif = Periode::with(['tahap', 'tahun'])
            ->where('status', 1)
            ->whereHas('tahap', function($query) {
                $query->whereIn('tahap', ['Triwulan 1', 'Triwulan 2', 'Triwulan 3', 'Triwulan 4']);
            })
            ->where('tahun_id', $tahunAktif->id) // WAJIB filter berdasarkan tahun aktif
            ->get();

        $semuaPeriodeAktif = Periode::with(['tahap', 'tahun'])
            ->whereHas('tahap', function($query) {
                $query->whereIn('tahap', ['Rencana', 'Triwulan 1', 'Triwulan 2', 'Triwulan 3', 'Triwulan 4']);
            })
            ->where('tahun_id', $tahunAktif->id) // WAJIB filter berdasarkan tahun aktif
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
                // ✅ FIXED: Ambil monitoring yang memiliki data anggaran, bukan yang terbaru
                \Log::info("🚨 DEBUG PARSIAL: Looking for monitoring with anggaran for task {$tugas->id}, tahun {$tahunAktif->tahun}");

                $monitoring = Monitoring::where('skpd_tugas_id', $tugas->id)
                    ->where('tahun', $tahunAktif->tahun)
                    ->whereHas('anggaran') // ✅ FIXED: Nama relasi yang benar adalah 'anggaran'
                    ->latest()
                    ->first();

                // Fallback: jika tidak ada data untuk tahun aktif, cari di tahun lain
                if (!$monitoring) {
                    $monitoring = Monitoring::where('skpd_tugas_id', $tugas->id)
                        ->whereHas('anggaran')
                        ->latest()
                        ->first();
                }

                \Log::info("🚨 DEBUG PARSIAL: Selected monitoring_id: " . ($monitoring ? $monitoring->id : 'NULL'));

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
                    $allKeys = ['dau', 'dau_peruntukan', 'dak_fisik', 'dak_non_fisik', 'blud']; // ✅ FIXED: dau, dau_peruntukan
                    $sumberAnggaranFlags = [];

                    // ✅ PERBAIKAN: Di mode parsial, tampilkan checkbox jika ada data rencana_awal ATAU parsial
                    // Ini memungkinkan user untuk mengisi parsial berdasarkan data rencana awal yang sudah ada
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
                            'dau' => false, // ✅ FIXED: dau
                            'dau_peruntukan' => false, // ✅ FIXED: dau_peruntukan
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

    public function showRencanaAwal($id, Request $request, $tahun = null)
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

        // Get tahun aktif atau tahun yang dipilih
        if ($tahun) {
            $tahunAktif = PeriodeTahun::where('tahun', $tahun)->first();
            if (!$tahunAktif) {
                $tahunAktif = PeriodeTahun::getTahunAktif();
            }
        } else {
            // Get current year
            $tahunAktif = null;
            if ($semuaPeriodeAktif->isNotEmpty()) {
                $tahunAktif = $semuaPeriodeAktif->first()->tahun;
            } elseif ($periodeRencanaFallback) {
                $tahunAktif = $periodeRencanaFallback->tahun;
            }
        }

        // Get all tahun for dropdown
        $allTahun = PeriodeTahun::orderByDesc('tahun')->get();

        // Tentukan tahun yang akan digunakan untuk filtering data
        $currentYear = $tahunAktif ? $tahunAktif->tahun : date('Y');

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
                // ✅ FIXED: Ambil monitoring yang memiliki data anggaran, prioritaskan tahun aktif tapi fallback ke tahun lain
                $monitoring = Monitoring::where('skpd_tugas_id', $tugas->id)
                    ->where('tahun', $currentYear)
                    ->whereHas('anggaran') // ✅ FIXED: Nama relasi yang benar adalah 'anggaran'
                    ->latest()
                    ->first();

                // Fallback: jika tidak ada data untuk tahun aktif, cari di tahun lain
                if (!$monitoring) {
                    $monitoring = Monitoring::where('skpd_tugas_id', $tugas->id)
                        ->whereHas('anggaran')
                        ->latest()
                        ->first();
                }

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
                    $allKeys = ['dau', 'dau_peruntukan', 'dak_fisik', 'dak_non_fisik', 'blud']; // ✅ FIXED: dau, dau_peruntukan
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

                    \Log::info("🚨 DEBUG: RencanaAwal data for task {$tugas->id}:", [
                        'rencana_awal' => $sumberAnggaranData['rencana_awal'],
                        'parsial' => $sumberAnggaranData['parsial'],
                        'budget_change' => $sumberAnggaranData['budget_change'],
                        'is_parsial_enabled' => $isParsialEnabled,
                        'is_budget_change_enabled' => $isBudgetChangeEnabled,
                        'final_data_structure' => $dataAnggaranTerakhir[$tugas->id],
                        'sumber_anggaran_flags' => $sumberAnggaranFlags
                    ]);
                }
            }
        }

        // Load target data for each subkegiatan with separate targets per sumber anggaran
        $subkegiatanWithTargets = $subkegiatanTugas->map(function($subkegiatan) use ($tahunAktif) {
            // Find monitoring for this subkegiatan filtered by year
            $monitoring = Monitoring::where('skpd_tugas_id', $subkegiatan->id)
                ->where('tahun', $tahunAktif->tahun)
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
            'allTahun' => $allTahun,
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

        // Log aktivitas mengisi manajemen anggaran
        UserActivityService::logManajemenAnggaran('menyimpan data monitoring anggaran', [
            'monitoring_id' => $monitoring->id,
            'skpd_id' => $validated['skpd_id'],
            'tahun' => $validated['tahun'],
            'pagu_pokok' => $validated['pagu_pokok'],
            'pagu_parsial' => $validated['pagu_parsial'] ?? 0,
            'pagu_perubahan' => $validated['pagu_perubahan'] ?? 0,
            'jumlah_targets' => count($validated['targets'])
        ]);

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
        $aktivPeriode = Periode::with(['tahun', 'tahap'])->where('status', 1)
            ->whereHas('tahap', function($query) {
                $query->where('tahap', 'Rencana');
            })
            ->whereHas('tahun', function($query) {
                $query->where('status', 1); // Tahun harus aktif
            })
            ->where('tanggal_selesai', '>=', now()->toDateString()) // Periode belum selesai
            ->first();

        \Log::info('Active periode found:', ['periode' => $aktivPeriode ? $aktivPeriode->toArray() : null]);

        if (!$aktivPeriode) {
            \Log::warning('No active Rencana periode found');
            return response()->json([
                'success' => false,
                'message' => 'Periode Rencana sudah selesai atau belum dibuka. Sumber dana hanya dapat diisi pada periode Rencana yang aktif.'
            ], 422);
        }

        $validated = $request->validate([
            'skpd_tugas_id' => 'required|exists:skpd_tugas,id',
            'sumber_anggaran' => 'required|array',
            'sumber_anggaran.dau' => 'boolean',                             // ✅ FIXED: Optional boolean
            'sumber_anggaran.dau_peruntukan' => 'boolean',                  // ✅ FIXED: Optional boolean
            'sumber_anggaran.dak_fisik' => 'boolean',
            'sumber_anggaran.dak_non_fisik' => 'boolean',
            'sumber_anggaran.blud' => 'boolean',
            'values' => 'required|array',
            'values.dau' => 'numeric|min:0',                                // ✅ FIXED: Optional numeric
            'values.dau_peruntukan' => 'numeric|min:0',                     // ✅ FIXED: Optional numeric
            'values.dak_fisik' => 'numeric|min:0',
            'values.dak_non_fisik' => 'numeric|min:0',
            'values.blud' => 'numeric|min:0',
        ]);

        \Log::info('Validation passed, validated data:', ['validated' => $validated]);

        DB::beginTransaction();

        try {
            $skpdTugas = SkpdTugas::findOrFail($validated['skpd_tugas_id']);
            \Log::info('SKPD Tugas found:', ['skpd_tugas' => $skpdTugas->toArray()]);

            $monitoring = Monitoring::where('skpd_tugas_id', $validated['skpd_tugas_id'])
                ->where('tahun', $aktivPeriode->tahun->tahun)
                ->first();

            \Log::info('Existing monitoring:', ['monitoring' => $monitoring ? $monitoring->toArray() : null]);

            if (!$monitoring) {
                \Log::info('Creating new monitoring record');
                $monitoring = new Monitoring();
                $monitoring->skpd_tugas_id = $validated['skpd_tugas_id'];
                $monitoring->periode_id = $aktivPeriode->id; // ← FIX: Set periode_id
                $monitoring->tahun = $aktivPeriode->tahun->tahun;
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
            
            // Store success data in session to persist across redirects
            session([
                'manajemen_anggaran_success' => true,
                'manajemen_anggaran_user_id' => $validated['skpd_tugas_id'],
                'manajemen_anggaran_data' => $validated
            ]);
            
            // PERBAIKAN: Gunakan back() untuk kembali ke halaman sebelumnya dengan method yang benar
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
            'dau' => 'DAU',                    // ✅ FIXED: dau -> DAU (bukan dak)
            'dau_peruntukan' => 'DAU Peruntukan',
            'dak_fisik' => 'DAK Fisik',
            'dak_non_fisik' => 'DAK Non Fisik',
            'blud' => 'BLUD',
        ];

        return $mapping[$key] ?? $key;
    }

    private function reverseMapNamaSumberAnggaran(string $nama): string
    {
        $reverseMapping = [
            'DAU' => 'dau',                    // ✅ FIXED: DAU -> dau (bukan dak)
            'DAU Peruntukan' => 'dau_peruntukan', // ✅ FIXED: DAU Peruntukan -> dau_peruntukan
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

        // Debug log
        \Log::info("=== SHOW PARSIAL DEBUG ===");
        \Log::info("Periode aktif count: " . $periodeAktif->count());
        \Log::info("Selected periode_id: " . ($periodeId ?? 'NULL'));
        \Log::info("Tahun aktif: " . ($tahunAktif->tahun ?? 'NULL'));

        foreach ($skpdTugas as $tugas) {
            if ($tugas->kodeNomenklatur->jenis_nomenklatur == 4) { // Only sub kegiatan
                // Find monitoring related to this SKPD tugas filtered by year
                // ✅ PERBAIKAN: Pilih monitoring dengan data rencana awal paling lengkap
                $monitoringCandidates = Monitoring::where('skpd_tugas_id', $tugas->id)
                    ->where('tahun', $tahunAktif->tahun)
                    ->get();

                $monitoring = null;
                $maxRencanaAwalCount = 0;

                foreach ($monitoringCandidates as $candidate) {
                    $rencanaAwalCount = MonitoringPagu::whereHas('anggaran', function($query) use ($candidate) {
                        $query->where('monitoring_id', $candidate->id);
                    })->where('kategori', 1)->count();

                    if ($rencanaAwalCount > $maxRencanaAwalCount) {
                        $maxRencanaAwalCount = $rencanaAwalCount;
                        $monitoring = $candidate;
                    }
                }

                // Fallback ke latest jika tidak ada yang memiliki rencana awal
                if (!$monitoring) {
                    $monitoring = $monitoringCandidates->sortByDesc('created_at')->first();
                }

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
                        \Log::info("Fetching parsial data for monitoring_id: {$monitoring->id}, periode_id: {$periodeId}");

                        $monitoringAnggaranParsial = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                            ->with(['sumberAnggaran'])
                            ->with(['pagu' => function($query) use ($periodeId) {
                                $query->where('kategori', 2) // Kategori 2 = parsial
                                      ->where('periode_id', $periodeId);
                            }])
                            ->get();

                        \Log::info("Found " . $monitoringAnggaranParsial->count() . " monitoring anggaran records");

                        foreach ($monitoringAnggaranParsial as $anggaran) {
                            \Log::info("Processing anggaran ID: {$anggaran->id}, sumber: " . ($anggaran->sumberAnggaran->nama ?? 'NULL') . ", pagu count: " . $anggaran->pagu->count());

                            if ($anggaran->sumberAnggaran && $anggaran->pagu->isNotEmpty()) {
                                $key = $this->reverseMapNamaSumberAnggaran($anggaran->sumberAnggaran->nama);
                                if ($key) {
                                    $dana = $anggaran->pagu->first()->dana ?? 0;
                                    $sumberAnggaranData['parsial'][$key] = $dana;
                                    \Log::info("Set parsial data: {$key} = {$dana}");
                                }
                            }
                        }
                    } else {
                        \Log::warning("No periode_id selected, skipping parsial data fetch");
                    }

                    \Log::debug("Parsial data for task {$tugas->id}:", $sumberAnggaranData['parsial']);

                    // ✅ PERBAIKAN: Di mode parsial, enable jika ada data rencana_awal ATAU parsial
                    // Ini memungkinkan user untuk membuat parsial berdasarkan rencana awal yang sudah ada
                    $isParsialEnabled = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                        ->whereHas('pagu', function($query) {
                            $query->whereIn('kategori', [1, 2]); // Enable if rencana_awal OR parsial data exists
                        })
                        ->exists();

                    // Prepare data structure for frontend (parsial mode)
                    $allKeys = ['dau', 'dau_peruntukan', 'dak_fisik', 'dak_non_fisik', 'blud']; // ✅ FIXED: dau, dau_peruntukan
                    $sumberAnggaranFlags = [];

                    // ✅ PERBAIKAN: Di mode parsial, tampilkan checkbox jika ada data rencana_awal ATAU parsial
                    // Ini memungkinkan user untuk mengisi parsial berdasarkan data rencana awal yang sudah ada
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
                            'dau' => false, // ✅ FIXED: dau
                            'dau_peruntukan' => false, // ✅ FIXED: dau_peruntukan
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

        // 🚨 DEBUG: Log data yang dikirim ke frontend
        \Log::info("=== DATA SENT TO FRONTEND ===");
        \Log::info("dataAnggaranTerakhir count: " . count($dataAnggaranTerakhir));
        \Log::info("dataAnggaranTerakhir keys: " . implode(', ', array_keys($dataAnggaranTerakhir)));
        foreach ($dataAnggaranTerakhir as $taskId => $data) {
            \Log::info("Task {$taskId} data: " . json_encode($data));
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
            $aktivPeriode = Periode::with(['tahun', 'tahap'])->where('status', 1)
                ->whereHas('tahap', function($query) {
                    $query->whereIn('tahap', ['Triwulan 1', 'Triwulan 2', 'Triwulan 3', 'Triwulan 4']);
                })
                ->whereHas('tahun', function($query) {
                    $query->where('status', 1); // Tahun harus aktif
                })
                ->where('tanggal_selesai', '>=', now()->toDateString()) // Periode belum selesai
                ->first();

            if (!$aktivPeriode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Periode triwulan sudah selesai atau tidak ada yang aktif untuk membuka pagu parsial.'
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
        $aktivPeriode = Periode::with(['tahun', 'tahap'])->where('status', 1)
            ->whereHas('tahap', function($query) {
                $query->whereIn('tahap', ['Triwulan 1', 'Triwulan 2', 'Triwulan 3', 'Triwulan 4']);
            })
            ->whereHas('tahun', function($query) {
                $query->where('status', 1); // Tahun harus aktif
            })
            ->where('tanggal_selesai', '>=', now()->toDateString()) // Periode belum selesai
            ->first();

        if (!$aktivPeriode) {
            \Log::warning('No active triwulan periode found');
            return response()->json([
                'success' => false,
                'message' => 'Periode triwulan sudah selesai atau belum dibuka. Pagu parsial hanya dapat diisi pada periode triwulan yang aktif.'
            ], 422);
        }

        $validated = $request->validate([
            'skpd_tugas_id' => 'required|exists:skpd_tugas,id',
            'sumber_anggaran' => 'required|array',
            'sumber_anggaran.dau' => 'boolean',                             // ✅ FIXED: Optional boolean
            'sumber_anggaran.dau_peruntukan' => 'boolean',                  // ✅ FIXED: Optional boolean
            'sumber_anggaran.dak_fisik' => 'boolean',
            'sumber_anggaran.dak_non_fisik' => 'boolean',
            'sumber_anggaran.blud' => 'boolean',
            'values' => 'required|array',
            'values.dau' => 'numeric|min:0',                                // ✅ FIXED: Optional numeric
            'values.dau_peruntukan' => 'numeric|min:0',                     // ✅ FIXED: Optional numeric
            'values.dak_fisik' => 'numeric|min:0',
            'values.dak_non_fisik' => 'numeric|min:0',
            'values.blud' => 'numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $skpdTugas = SkpdTugas::findOrFail($validated['skpd_tugas_id']);

            $monitoring = Monitoring::where('skpd_tugas_id', $validated['skpd_tugas_id'])
                ->where('tahun', $aktivPeriode->tahun->tahun)
                ->first();

            if (!$monitoring) {
                // If no monitoring exists, create one
                $monitoring = new Monitoring();
                $monitoring->skpd_tugas_id = $validated['skpd_tugas_id'];
                $monitoring->periode_id = $aktivPeriode->id;
                $monitoring->tahun = $aktivPeriode->tahun->tahun;
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
    public function showRencanaAwalParsial($id, Request $request, $tahun = null)
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

        // Get tahun aktif atau tahun yang dipilih
        if ($tahun) {
            $tahunAktif = PeriodeTahun::where('tahun', $tahun)->first();
            if (!$tahunAktif) {
                $tahunAktif = PeriodeTahun::getTahunAktif();
            }
        } else {
            // Get current year
            $tahunAktif = $semuaPeriodeAktif->isNotEmpty() ? $semuaPeriodeAktif->first()->tahun : null;
        }

        // Get all tahun for dropdown
        $allTahun = PeriodeTahun::orderByDesc('tahun')->get();

        // Tentukan tahun yang akan digunakan untuk filtering data
        $currentYear = $tahunAktif ? $tahunAktif->tahun : date('Y');

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
                // Find monitoring related to this SKPD tugas filtered by year
                $monitoring = Monitoring::where('skpd_tugas_id', $tugas->id)
                    ->where('tahun', $currentYear)
                    ->latest()
                    ->first();

                if ($monitoring) {
                    // Get funding data for this monitoring filtered by period
                    $sumberAnggaranData = [];

                    $monitoringAnggaranQuery = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                        ->with(['sumberAnggaran', 'target' => function($query) {
                            $query->orderBy('periode_id');
                        }]);

                    // PERBAIKAN: Ambil semua data rencana awal tanpa filter periode untuk menghindari data hilang
                    $monitoringAnggaranQuery->with(['pagu' => function($query) {
                        $query->where('kategori', 1) // Category 1 = pokok (rencana awal)
                              ->latest('created_at'); // Ambil data terbaru untuk setiap sumber anggaran
                    }]);

                    $monitoringAnggaran = $monitoringAnggaranQuery->get();

                    foreach ($monitoringAnggaran as $anggaran) {
                        if ($anggaran->sumberAnggaran) {
                            $key = $this->reverseMapNamaSumberAnggaran($anggaran->sumberAnggaran->nama);
                            if ($key) {
                                // PERBAIKAN: Ambil data terbaru dan selalu masukkan sumber anggaran
                                if ($anggaran->pagu->isNotEmpty()) {
                                    // Ambil data pagu terbaru untuk sumber anggaran ini
                                    $latestPagu = $anggaran->pagu->sortByDesc('created_at')->first();
                                    $sumberAnggaranData[$key] = $latestPagu->dana ?? 0;
                                } else {
                                    // Jika tidak ada pagu, tetap masukkan dengan nilai 0
                                    $sumberAnggaranData[$key] = 0;
                                }
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
            'allTahun' => $allTahun,
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
        \Log::info('enableParsialForUser called', [
            'request_data' => $request->all(),
            'headers' => $request->headers->all(),
            'method' => $request->method(),
            'url' => $request->url(),
            'content_type' => $request->header('Content-Type'),
            'accept' => $request->header('Accept')
        ]);

        // Test response to check if we reach this point
        if ($request->has('test')) {
            return response()->json([
                'success' => true,
                'message' => 'Test endpoint reached successfully',
                'data' => $request->all()
            ]);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'confirm' => 'required|boolean|accepted'
        ]);

        try {
            // Check if there's an active triwulan period (1, 2, or 3 for parsial)
            $aktivPeriode = Periode::with(['tahun', 'tahap'])->where('status', 1)
                ->whereHas('tahap', function($query) {
                    $query->whereIn('tahap', ['Triwulan 1', 'Triwulan 2', 'Triwulan 3']); // Only allow parsial for Triwulan 1-3
                })
                ->whereHas('tahun', function($query) {
                    $query->where('status', 1); // Tahun harus aktif
                })
                ->where('tanggal_selesai', '>=', now()->toDateString()) // Periode belum selesai
                ->first();

            if (!$aktivPeriode) {
                // Check if there are any Triwulan 1-3 periods available but not active
                $availablePeriods = Periode::with(['tahun', 'tahap'])
                    ->whereHas('tahap', function($query) {
                        $query->whereIn('tahap', ['Triwulan 1', 'Triwulan 2', 'Triwulan 3']);
                    })
                    ->whereHas('tahun', function($query) {
                        $query->where('status', 1);
                    })
                    ->get();

                if ($availablePeriods->isNotEmpty()) {
                    $periodNames = $availablePeriods->pluck('tahap.tahap')->unique()->implode(', ');
                    return response()->json([
                        'success' => false,
                        'message' => "Mode parsial hanya dapat diaktifkan saat periode Triwulan 1, 2, atau 3 sedang aktif. Periode yang tersedia: {$periodNames}. Silakan hubungi administrator untuk mengaktifkan periode yang diperlukan."
                    ], 422);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak ada periode Triwulan 1, 2, atau 3 yang tersedia untuk tahun aktif. Mode parsial hanya dapat digunakan pada periode tersebut.'
                    ], 422);
                }
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
                'message' => "Mode parsial berhasil diaktifkan untuk {$skpdName}. Anda sekarang dapat mengakses data {$aktivPeriode->tahap->tahap} dalam mode parsial.",
                'periode' => $aktivPeriode,
                'available_triwulan' => $aktivPeriode->tahap->tahap
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in enableParsialForUser', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid: ' . implode(', ', array_flatten($e->errors()))
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Error enabling parsial for user', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Disable parsial mode for specific user/SKPD
     */
    public function disableParsialForUser(Request $request)
    {
        \Log::info('disableParsialForUser called', [
            'request_data' => $request->all(),
            'headers' => $request->headers->all(),
            'method' => $request->method(),
            'url' => $request->url()
        ]);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'confirm' => 'required|boolean|accepted'
        ]);

        try {
            // Get user and SKPD name for better message
            $user = User::with('skpd')->findOrFail($validated['user_id']);
            $skpd = $user->skpd->first();
            $skpdName = $skpd ? $skpd->nama_skpd : 'SKPD';

            // Remove user from enabled parsial list in session
            $enabledParsialUsers = session('enabled_parsial_users', []);
            $enabledParsialUsers = array_filter($enabledParsialUsers, function($id) use ($validated) {
                return (int)$id !== (int)$validated['user_id'];
            });

            // Re-index array to avoid gaps
            $enabledParsialUsers = array_values($enabledParsialUsers);
            session(['enabled_parsial_users' => $enabledParsialUsers]);

            return response()->json([
                'success' => true,
                'message' => "Mode parsial berhasil ditutup untuk {$skpdName}.",
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in disableParsialForUser', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid: ' . implode(', ', array_flatten($e->errors()))
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Error disabling parsial for user', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
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
                ->whereHas('tahun', function($query) {
                    $query->where('status', 1); // Tahun harus aktif
                })
                ->where('tanggal_selesai', '>=', now()->toDateString()) // Periode belum selesai
                ->first();

            if (!$triwulan4Aktif) {
                return response()->json([
                    'success' => false,
                    'message' => 'Periode Triwulan 4 sudah selesai atau belum dibuka. Perubahan anggaran hanya dapat dilakukan pada periode Triwulan 4 yang aktif.'
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
            ->whereHas('tahun', function($query) {
                $query->where('status', 1); // Tahun harus aktif
            })
            ->where('tanggal_selesai', '>=', now()->toDateString()) // Periode belum selesai
            ->first();

        if (!$triwulan4Aktif) {
            return redirect()->back()->with('error', 'Periode Triwulan 4 sudah selesai atau tidak aktif. Perubahan anggaran tidak dapat dilakukan.');
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
                // Find monitoring related to this SKPD tugas filtered by year
                // ✅ PERBAIKAN: Pilih monitoring dengan data rencana awal paling lengkap
                $monitoringCandidates = Monitoring::where('skpd_tugas_id', $tugas->id)
                    ->where('tahun', $tahunAktif->tahun)
                    ->get();

                $monitoring = null;
                $maxRencanaAwalCount = 0;

                foreach ($monitoringCandidates as $candidate) {
                    $rencanaAwalCount = MonitoringPagu::whereHas('anggaran', function($query) use ($candidate) {
                        $query->where('monitoring_id', $candidate->id);
                    })->where('kategori', 1)->count();

                    if ($rencanaAwalCount > $maxRencanaAwalCount) {
                        $maxRencanaAwalCount = $rencanaAwalCount;
                        $monitoring = $candidate;
                    }
                }

                // Fallback ke latest jika tidak ada yang memiliki rencana awal
                if (!$monitoring) {
                    $monitoring = $monitoringCandidates->sortByDesc('created_at')->first();
                }

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
                    $allKeys = ['dau', 'dau_peruntukan', 'dak_fisik', 'dak_non_fisik', 'blud'];
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
                            'dau' => false,
                            'dau_peruntukan' => false,
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
        $triwulan4Aktif = Periode::with(['tahun', 'tahap'])->where('status', 1)
            ->whereHas('tahap', function($query) {
                $query->where('tahap', 'Triwulan 4');
            })
            ->whereHas('tahun', function($query) {
                $query->where('status', 1); // Tahun harus aktif
            })
            ->where('tanggal_selesai', '>=', now()->toDateString()) // Periode belum selesai
            ->first();

        if (!$triwulan4Aktif) {
            \Log::warning('No active Triwulan 4 periode found');
            return response()->json([
                'success' => false,
                'message' => 'Periode Triwulan 4 sudah selesai atau belum dibuka. Perubahan anggaran hanya dapat dilakukan pada periode Triwulan 4 yang aktif.'
            ], 422);
        }

        $validated = $request->validate([
            'skpd_tugas_id' => 'required|exists:skpd_tugas,id',
            'sumber_anggaran' => 'required|array',
            'sumber_anggaran.dau' => 'boolean',                             // ✅ FIXED: Optional boolean
            'sumber_anggaran.dau_peruntukan' => 'boolean',                  // ✅ FIXED: Optional boolean
            'sumber_anggaran.dak_fisik' => 'boolean',
            'sumber_anggaran.dak_non_fisik' => 'boolean',
            'sumber_anggaran.blud' => 'boolean',
            'values' => 'required|array',
            'values.dau' => 'numeric|min:0',                                // ✅ FIXED: Optional numeric
            'values.dau_peruntukan' => 'numeric|min:0',                     // ✅ FIXED: Optional numeric
            'values.dak_fisik' => 'numeric|min:0',
            'values.dak_non_fisik' => 'numeric|min:0',
            'values.blud' => 'numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $skpdTugas = SkpdTugas::findOrFail($validated['skpd_tugas_id']);

            $monitoring = Monitoring::where('skpd_tugas_id', $validated['skpd_tugas_id'])
                ->where('tahun', $triwulan4Aktif->tahun->tahun)
                ->first();

            if (!$monitoring) {
                // If no monitoring exists, create one
                $monitoring = new Monitoring();
                $monitoring->skpd_tugas_id = $validated['skpd_tugas_id'];
                $monitoring->periode_id = $triwulan4Aktif->id;
                $monitoring->tahun = $triwulan4Aktif->tahun->tahun;
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

    /**
     * Export Rencana Awal data to CSV
     */
    public function exportRencanaAwalCsv($tugasId, $tahun = null)
    {
        try {
            // Get SKPD data with proper relationships
            $skpdTugas = SkpdTugas::with([
                'kodeNomenklatur',
                'skpd' => function($query) {
                    $query->with([
                        'kepala' => function($q) {
                            $q->with(['user' => function($u) {
                                $u->with('userDetail');
                            }]);
                        }
                    ]);
                }
            ])->find($tugasId);
            
            if (!$skpdTugas) {
                throw new \Exception("SKPD Tugas dengan ID {$tugasId} tidak ditemukan");
            }
            
            // Determine year
            $tahunRecord = $tahun ? PeriodeTahun::where('tahun', $tahun)->first() : PeriodeTahun::getTahunAktif();
            $currentYear = $tahunRecord ? $tahunRecord->tahun : date('Y');
            
            // Get monitoring data
            $monitoring = Monitoring::where('skpd_tugas_id', $tugasId)
                ->where('tahun', $currentYear)
                ->with([
                    'monitoring_target',
                    'monitoring_anggaran' => function($query) {
                        $query->with([
                            'sumberAnggaran',
                            'pagu' => function($q) {
                                $q->orderBy('kategori');
                            }
                        ]);
                    }
                ])
                ->first();
            
            // Get periode rencana
            $periodeRencana = null;
            if ($tahunRecord) {
                $periodeRencana = Periode::with(['tahap', 'tahun'])
                    ->whereHas('tahap', function($query) {
                        $query->where('tahap', 'Rencana');
                    })
                    ->where('tahun_id', $tahunRecord->id)
                    ->first();
            }
            
            // Generate CSV content
            $csvContent = "\xEF\xBB\xBF"; // UTF-8 BOM
            
            // Header information
            $csvContent .= "Data Rencana Awal\n";
            $csvContent .= "SKPD: " . ($skpdTugas->skpd->nama_dinas ?? $skpdTugas->skpd->nama_skpd ?? 'Tidak tersedia') . "\n";
            $csvContent .= "Kode Organisasi: " . ($skpdTugas->skpd->kode_organisasi ?? 'Tidak tersedia') . "\n";
            $csvContent .= "Tahun: {$currentYear}\n";
            
            if ($periodeRencana && $periodeRencana->tahap) {
                $csvContent .= "Periode: {$periodeRencana->tahap->tahap}\n";
            }
            
            $csvContent .= "Tanggal Export: " . date('d/m/Y H:i:s') . "\n";
            
            // Safe kepala SKPD access
            $kepalaSkpd = 'Tidak tersedia';
            $nipKepalaSkpd = 'Tidak tersedia';
            
            try {
                if ($skpdTugas->skpd && 
                    is_object($skpdTugas->skpd->kepala) && 
                    $skpdTugas->skpd->kepala->count() > 0) {
                    
                    $kepala = $skpdTugas->skpd->kepala->first();
                    if ($kepala && $kepala->user) {
                        $kepalaSkpd = $kepala->user->name ?? 'Tidak tersedia';
                        if ($kepala->user->userDetail) {
                            $nipKepalaSkpd = $kepala->user->userDetail->nip ?? 'Tidak tersedia';
                        }
                    }
                }
            } catch (\Exception $e) {
                // Keep default values if any error in accessing kepala SKPD
                \Log::warning('Error accessing kepala SKPD: ' . $e->getMessage());
            }
            
            $csvContent .= "Kepala SKPD: {$kepalaSkpd}\n";
            $csvContent .= "NIP Kepala SKPD: {$nipKepalaSkpd}\n\n";
            
            // CSV headers
            $headers = [
                'No',
                'Kode',
                'Bidang Urusan/Program/Kegiatan/Sub Kegiatan',
                'Sumber Dana',
                'Pagu Pokok (Rp)',
                'Pagu Parsial (Rp)',
                'Pagu Perubahan (Rp)',
                'Target Fisik (%)',
                'Target Keuangan (Rp)',
                'Indikator',
                'Target Capaian'
            ];
            
            $csvContent .= implode(',', array_map(function($header) {
                return '"' . str_replace('"', '""', $header) . '"';
            }, $headers)) . "\n";
            
            // Data row
            $no = 1;
            if ($monitoring) {
                // Initialize program data
                $programData = [
                    'kode' => $skpdTugas->kodeNomenklatur->nomor_kode ?? '',
                    'program' => $skpdTugas->kodeNomenklatur->nomenklatur ?? '',
                    'pokok' => 0,
                    'parsial' => 0,
                    'perubahan' => 0,
                    'sumber_dana' => [],
                    'target_fisik' => 0,
                    'target_keuangan' => 0,
                    'indikator' => '',
                    'target' => ''
                ];
                
                // Get monitoring target data safely
                if ($monitoring->monitoring_target) {
                    $programData['target_fisik'] = $monitoring->monitoring_target->target_kinerja_fisik ?? 0;
                    $programData['target_keuangan'] = $monitoring->monitoring_target->target_keuangan ?? 0;
                    $programData['indikator'] = $monitoring->monitoring_target->indikator ?? '';
                    $programData['target'] = $monitoring->monitoring_target->target ?? '';
                }
                
                // Calculate pagu from monitoring anggaran
                try {
                    if ($monitoring->monitoring_anggaran && $monitoring->monitoring_anggaran->count() > 0) {
                        foreach ($monitoring->monitoring_anggaran as $anggaran) {
                            // Collect sumber dana
                            if ($anggaran && $anggaran->sumberAnggaran) {
                                $programData['sumber_dana'][] = $anggaran->sumberAnggaran->nama;
                            }
                            
                            // Calculate pagu by category
                            if ($anggaran && $anggaran->pagu && $anggaran->pagu->count() > 0) {
                                foreach ($anggaran->pagu as $pagu) {
                                    if ($pagu && isset($pagu->kategori) && isset($pagu->dana)) {
                                        switch ($pagu->kategori) {
                                            case 1: // Pokok
                                                $programData['pokok'] += (float)$pagu->dana;
                                                break;
                                            case 2: // Parsial
                                                $programData['parsial'] += (float)$pagu->dana;
                                                break;
                                            case 3: // Perubahan
                                                $programData['perubahan'] += (float)$pagu->dana;
                                                break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('Error processing monitoring anggaran: ' . $e->getMessage());
                }
                
                $row = [
                    $no++,
                    $programData['kode'],
                    $programData['program'],
                    implode(', ', array_unique($programData['sumber_dana'])),
                    number_format($programData['pokok'], 0, ',', '.'),
                    number_format($programData['parsial'], 0, ',', '.'),
                    number_format($programData['perubahan'], 0, ',', '.'),
                    number_format($programData['target_fisik'], 2, ',', '.'),
                    number_format($programData['target_keuangan'], 0, ',', '.'),
                    $programData['indikator'],
                    $programData['target']
                ];
            } else {
                // No monitoring data
                $row = [
                    $no++,
                    $skpdTugas->kodeNomenklatur->nomor_kode ?? '',
                    $skpdTugas->kodeNomenklatur->nomenklatur ?? '',
                    'Belum ada data',
                    '0',
                    '0',
                    '0',
                    '0.00',
                    '0',
                    'Belum ada data',
                    'Belum ada data'
                ];
            }
            
            $csvContent .= implode(',', array_map(function($cell) {
                return '"' . str_replace('"', '""', $cell) . '"';
            }, $row)) . "\n";
            
            // Generate filename
            $skpdName = str_replace([' ', '/', '\\'], '_', $skpdTugas->skpd->nama_dinas ?? $skpdTugas->skpd->nama_skpd ?? 'SKPD');
            $filename = "Rencana_Awal_{$skpdName}_{$currentYear}.csv";
            
            // Log activity
            UserActivityService::logExportData('CSV Rencana Awal', [
                'tugas_id' => $tugasId,
                'skpd_id' => $skpdTugas->skpd->id ?? 0,
                'tahun' => $currentYear,
                'filename' => $filename
            ]);
            
            // Return CSV download
            return response($csvContent, 200, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error exporting Rencana Awal CSV: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }



}

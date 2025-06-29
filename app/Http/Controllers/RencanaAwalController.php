<?php

namespace App\Http\Controllers;

use App\Models\SkpdTugas;
use App\Models\KodeNomenklatur;
use App\Models\Monitoring;
use App\Models\MonitoringTarget;
use App\Models\MonitoringAnggaran;
use App\Models\MonitoringPagu;
use App\Models\SumberAnggaran;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class RencanaAwalController extends Controller
{
    public function show($id, Request $request)
    {
        $requestedUrusanId = $request->input('urusan_id');

        // Load basic data using helper methods
        $tugas = $this->loadSkpdData($id);

        // DEBUG: Log relasi yang ter-load
        \Log::info('=== TUGAS DATA LOADED ===', [
            'tugas_id' => $tugas->id,
            'skpd_id' => $tugas->skpd_id,
            'skpd_exists' => $tugas->skpd ? true : false,
            'kepala_count' => $tugas->skpd && $tugas->skpd->kepala ? $tugas->skpd->kepala->count() : 0,
            'tim_kerja_count' => $tugas->skpd && $tugas->skpd->timKerja ? $tugas->skpd->timKerja->count() : 0,
            'kepala_first' => $tugas->skpd && $tugas->skpd->kepala && $tugas->skpd->kepala->first() ? [
                'id' => $tugas->skpd->kepala->first()->id,
                'is_aktif' => $tugas->skpd->kepala->first()->is_aktif,
                'user_exists' => $tugas->skpd->kepala->first()->user ? true : false,
                'user_name' => $tugas->skpd->kepala->first()->user ? $tugas->skpd->kepala->first()->user->name : null,
                'user_detail_exists' => $tugas->skpd->kepala->first()->user && $tugas->skpd->kepala->first()->user->userDetail ? true : false,
                'user_nip' => $tugas->skpd->kepala->first()->user && $tugas->skpd->kepala->first()->user->userDetail ? $tugas->skpd->kepala->first()->user->userDetail->nip : null
            ] : null
        ]);

        $periodeAktif = $this->getActivePeriode();
        $skpdTugas = $this->loadSkpdTugasWithMonitoring($tugas->skpd_id, $periodeAktif);

        // Get available urusan and determine current urusan
        $availableUrusans = $this->getAvailableUrusans($skpdTugas);
        $urusanId = $requestedUrusanId ?? ($availableUrusans[0] ?? null);

        if (!$urusanId && $tugas->kodeNomenklatur->details->first()) {
            $urusanId = $tugas->kodeNomenklatur->details->first()->id_urusan;
        }

        // Get bidang urusan data
        $bidangUrusan = KodeNomenklatur::where('jenis_nomenklatur', 1)
            ->whereHas('details', function($query) use ($urusanId) {
                $query->where('id_urusan', $urusanId);
            })
            ->with(['details' => function($query) {
                $query->select('id', 'id_nomenklatur', 'id_urusan', 'id_bidang_urusan');
            }])
            ->first();

        // Filter tugas by jenis using helper method
        $bidangurusanTugas = $this->filterTugasByJenisAndUrusan($skpdTugas, 1, $urusanId);
        $programTugas = $this->filterTugasByJenisAndUrusan($skpdTugas, 2, $urusanId);
        $kegiatanTugas = $this->filterTugasByJenisAndUrusan($skpdTugas, 3, $urusanId);
        $subkegiatanTugas = $this->filterTugasByJenisAndUrusan($skpdTugas, 4, $urusanId);

        // Get kepala SKPD name using helper method
        $kepalaSkpd = $this->getKepalaSkpdName($tugas);

        // Process anggaran data using helper method
        $dataAnggaranTerakhir = $this->processAnggaranData($subkegiatanTugas, $periodeAktif);

        // Debug: Log SKPD Tugas data
        \Log::info('RencanaAwal SKPD Tugas debugging', [
            'tugas_id' => $tugas->id,
            'skpd_id' => $tugas->skpd_id,
            'tugas_kode_nomenklatur' => $tugas->kodeNomenklatur->toArray(),
            'skpd_tugas_count' => $skpdTugas->count(),
            'skpd_tugas_sample' => $skpdTugas->take(3)->map(function($item) {
                return [
                    'id' => $item->id,
                    'kode_nomenklatur_id' => $item->kode_nomenklatur_id,
                    'jenis_nomenklatur' => $item->kodeNomenklatur->jenis_nomenklatur,
                    'nomor_kode' => $item->kodeNomenklatur->nomor_kode,
                    'details_count' => $item->kodeNomenklatur->details->count(),
                    'first_detail' => $item->kodeNomenklatur->details->first() ? $item->kodeNomenklatur->details->first()->toArray() : null
                ];
            })->toArray()
        ]);

        // Get all available urusan for this SKPD - PERBAIKAN: Ambil semua urusan yang terkait
        $availableUrusanIds = collect();

        foreach($skpdTugas as $item) {
            if ($item->kodeNomenklatur && $item->kodeNomenklatur->details->isNotEmpty()) {
                $detail = $item->kodeNomenklatur->details->first();
                if ($detail->id_urusan) {
                    $availableUrusanIds->push($detail->id_urusan);
                }
            }
        }

        // Jika tidak ada urusan dari details, coba ambil dari tugas utama
        if ($availableUrusanIds->isEmpty() && $tugas->kodeNomenklatur->details->isNotEmpty()) {
            $mainDetail = $tugas->kodeNomenklatur->details->first();
            if ($mainDetail->id_urusan) {
                $availableUrusanIds->push($mainDetail->id_urusan);
            }
        }

        // Fallback: Jika masih kosong, ambil semua urusan
        if ($availableUrusanIds->isEmpty()) {
            $allUrusans = KodeNomenklatur::where('jenis_nomenklatur', 0)->get();
        } else {
            $availableUrusanIds = $availableUrusanIds->unique()->filter()->values();
            $allUrusans = KodeNomenklatur::where('jenis_nomenklatur', 0)
                ->whereIn('id', $availableUrusanIds)
                ->get();
        }

        \Log::info('RencanaAwal available urusan FINAL', [
            'available_urusan_ids' => $availableUrusanIds->toArray(),
            'all_urusan_count' => $allUrusans->count(),
            'urusan_data' => $allUrusans->map(function($urusan) {
                return [
                    'id' => $urusan->id,
                    'nomor_kode' => $urusan->nomor_kode,
                    'nomenklatur' => $urusan->nomenklatur
                ];
            })->toArray()
        ]);

        // Get SKPD data yang lebih lengkap
        $skpdData = $this->getDetailedSkpdData($tugas);

        // Get user penanggung jawab (operator)
        $penanggungJawab = $this->getPenanggungJawabData($tugas);

        // DEBUG: Log data yang akan dikirim ke frontend
        \Log::info('=== FINAL DATA TO FRONTEND ===', [
            'kepalaSkpd' => $kepalaSkpd,
            'skpdData' => $skpdData,
            'penanggungJawab' => $penanggungJawab,
            'skpd_kepala_structure' => $skpdData['skpd_kepala'] ?? 'NULL',
            'tugas_skpd_kepala' => $tugas->skpd->kepala->toArray(),
            'tugas_skpd_timkerja' => $tugas->skpd->timKerja->toArray()
        ]);

        // Mengirimkan data ke tampilan
        return Inertia::render('Monitoring/RencanaAwal', [
            'tugas' => $tugas,
            'bidangurusanTugas' => $bidangurusanTugas,
            'programTugas' => $programTugas,
            'kegiatanTugas' => $kegiatanTugas,
            'subkegiatanTugas' => $subkegiatanTugas,
            'kepalaSkpd' => $kepalaSkpd,
            'bidangUrusan' => $bidangUrusan,
            'dataAnggaranTerakhir' => $dataAnggaranTerakhir,
            'periodeAktif' => $periodeAktif ? [$periodeAktif] : [],
            'availableUrusans' => $allUrusans,
            'selectedUrusanId' => $urusanId,
            'skpd' => $skpdData,
            'user' => $penanggungJawab,
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }

    /**
     * Helper to normalize source keys
     */
    private function normalizeKey($key) {
        $key = strtolower($key);

        // Map DAU to dak for compatibility with existing code
        if ($key === 'dau') {
            return 'dak';
        }
        if ($key === 'dau_peruntukan') {
            return 'dak_peruntukan';
        }

        return $key;
    }

    /**
     * Load basic SKPD data with relations
     */
    private function loadSkpdData($id)
    {
        return SkpdTugas::with([
            'kodeNomenklatur.details',
            'skpd' => function($query) {
                $query->with([
                    'kepala' => function($q) {
                        $q->with(['user.userDetail'])->orderByDesc('is_aktif'); // Load semua kepala, prioritas yang aktif
                    },
                    'timKerja' => function($q) {
                        $q->with(['operator.userDetail'])->orderByDesc('is_aktif'); // Load semua tim kerja
                    }
                ]);
            }
        ])->findOrFail($id);
    }

    /**
     * Get active periode for Rencana phase
     */
    private function getActivePeriode()
    {
        return Periode::where('status', 1)
            ->whereHas('tahap', function($query) {
                $query->where('tahap', 'like', '%Rencana%');
            })
            ->with(['tahap', 'tahun'])
            ->first();
    }

    /**
     * Load SKPD tugas with monitoring data (optimized to prevent N+1)
     */
    private function loadSkpdTugasWithMonitoring($skpdId, $periodeAktif = null)
    {
        return SkpdTugas::where('skpd_id', $skpdId)
            ->where('is_aktif', 1)
            ->with([
                'kodeNomenklatur.details',
                'monitoring' => function($query) use ($periodeAktif) {
                    $query->where('deskripsi', 'Rencana Awal')
                        ->where('tahun', date('Y'))
                        ->when($periodeAktif, function($q) use ($periodeAktif) {
                            return $q->where('periode_id', $periodeAktif->id);
                        })
                        ->with([
                            'anggaran.sumberAnggaran',
                            'anggaran.pagu' => function($query) use ($periodeAktif) {
                                $query->when($periodeAktif, function($q) use ($periodeAktif) {
                                    return $q->where('periode_id', $periodeAktif->id);
                                });
                            },
                            'anggaran.target' => function($query) {
                                $query->orderBy('periode_id');
                            }
                        ]);
                }
            ])
            ->get();
    }

    /**
     * Filter tugas by jenis nomenklatur and urusan
     */
    private function filterTugasByJenisAndUrusan($skpdTugas, $jenisNomenklatur, $urusanId)
    {
        return $skpdTugas->filter(function($item) use ($jenisNomenklatur, $urusanId) {
            return $item->kodeNomenklatur->jenis_nomenklatur == $jenisNomenklatur
                && $item->kodeNomenklatur->details->first()
                && $item->kodeNomenklatur->details->first()->id_urusan == $urusanId;
        })->values();
    }

    /**
     * Get kepala SKPD name
     */
    private function getKepalaSkpdName($tugas)
    {
        $kepalaSkpd = '-';
        $kepala = $tugas->skpd->kepala->first();

        if ($kepala && $kepala->user) {
            // PERBAIKAN: Gunakan user.name, bukan user_detail.nama
            $kepalaSkpd = $kepala->user->name;
        }

        return $kepalaSkpd;
    }

    /**
     * Get detailed SKPD data with kepala information
     */
    private function getDetailedSkpdData($tugas)
    {
        $skpd = $tugas->skpd;

        // Coba berbagai cara untuk mendapatkan kepala SKPD
        $kepala = null;

        // Method 1: Cari kepala yang aktif
        if ($skpd->kepala && $skpd->kepala->count() > 0) {
            $kepala = $skpd->kepala->where('is_aktif', 1)->first();
            if (!$kepala) {
                $kepala = $skpd->kepala->first(); // Ambil kepala pertama jika tidak ada yang aktif
            }
        }

        // Method 2: Query langsung dari database dengan eager loading
        if (!$kepala) {
            $kepala = \App\Models\SkpdKepala::where('skpd_id', $skpd->id)
                ->where('is_aktif', 1)
                ->with(['user.userDetail'])
                ->first();
        }

        // Method 3: Query tanpa filter is_aktif jika masih tidak ada
        if (!$kepala) {
            $kepala = \App\Models\SkpdKepala::where('skpd_id', $skpd->id)
                ->with(['user.userDetail'])
                ->latest()
                ->first();
        }

        \Log::info('RencanaAwal getDetailedSkpdData FIXED', [
            'skpd_id' => $skpd->id,
            'kepala_exists' => $kepala ? true : false,
            'kepala_id' => $kepala ? $kepala->id : null,
            'kepala_user_exists' => $kepala && $kepala->user ? true : false,
            'kepala_user_name' => $kepala && $kepala->user ? $kepala->user->name : null,
            'kepala_user_detail_exists' => $kepala && $kepala->user && $kepala->user->userDetail ? true : false,
            'kepala_nip' => $kepala && $kepala->user && $kepala->user->userDetail ? $kepala->user->userDetail->nip : null
        ]);

        return [
            'id' => $skpd->id,
            'nama_dinas' => $skpd->nama_dinas ?? $skpd->nama_skpd,
            'nama_skpd' => $skpd->nama_skpd,
            'kode_organisasi' => $skpd->kode_organisasi,
            'no_dpa' => $skpd->no_dpa,
            'skpd_kepala' => $kepala ? [[
                'user' => [
                    'name' => $kepala->user ? $kepala->user->name : null,
                    'user_detail' => [
                        'nama' => $kepala->user ? $kepala->user->name : null,
                        'nip' => $kepala->user && $kepala->user->userDetail ? $kepala->user->userDetail->nip : null
                    ]
                ]
            ]] : []
        ];
    }

    /**
     * Get penanggung jawab (operator) data
     */
    private function getPenanggungJawabData($tugas)
    {
        $penanggungJawab = null;
        $method = 'none';

        // Method 1: Cari tim kerja aktif untuk SKPD ini
        $timKerja = \App\Models\TimKerja::where('skpd_id', $tugas->skpd_id)
            ->where('is_aktif', 1)
            ->with(['operator.userDetail'])
            ->first();

        if ($timKerja && $timKerja->operator) {
            $penanggungJawab = [
                'id' => $timKerja->operator->id,
                'name' => $timKerja->operator->name,
                'nip' => $timKerja->operator->userDetail ? $timKerja->operator->userDetail->nip : null,
                'nama_skpd' => $tugas->skpd->nama_skpd
            ];
            $method = 'tim_kerja';
        }

        // Method 2: Fallback ke user SKPD jika tidak ada tim kerja
        if (!$penanggungJawab && $tugas->skpd->user_id) {
            $user = \App\Models\User::with('userDetail')->find($tugas->skpd->user_id);
            if ($user) {
                $penanggungJawab = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'nip' => $user->userDetail ? $user->userDetail->nip : null,
                    'nama_skpd' => $tugas->skpd->nama_skpd
                ];
                $method = 'skpd_user';
            }
        }

        // Method 3: Coba cari user yang memiliki role perangkat_daerah untuk SKPD ini
        if (!$penanggungJawab) {
            $perangkatDaerah = \App\Models\User::whereHas('skpd', function($query) use ($tugas) {
                    $query->where('skpd.id', $tugas->skpd_id);
                })
                ->whereHas('roles', function($query) {
                    $query->where('name', 'perangkat_daerah');
                })
                ->with('userDetail')
                ->first();

            if ($perangkatDaerah) {
                $penanggungJawab = [
                    'id' => $perangkatDaerah->id,
                    'name' => $perangkatDaerah->name,
                    'nip' => $perangkatDaerah->userDetail ? $perangkatDaerah->userDetail->nip : null,
                    'nama_skpd' => $tugas->skpd->nama_skpd
                ];
                $method = 'perangkat_daerah_role';
            }
        }

        // Method 4: Coba cari user berdasarkan SkpdKepala (kepala sebagai fallback)
        if (!$penanggungJawab) {
            $kepalaAktif = \App\Models\SkpdKepala::where('skpd_id', $tugas->skpd_id)
                ->where('is_aktif', 1)
                ->with(['user.userDetail'])
                ->first();

            if ($kepalaAktif && $kepalaAktif->user) {
                $penanggungJawab = [
                    'id' => $kepalaAktif->user->id,
                    'name' => $kepalaAktif->user->name,
                    'nip' => $kepalaAktif->user->userDetail ? $kepalaAktif->user->userDetail->nip : null,
                    'nama_skpd' => $tugas->skpd->nama_skpd
                ];
                $method = 'kepala_as_fallback';
            }
        }

        // Method 5: Coba cari user yang punya role perangkat_daerah dari semua user
        if (!$penanggungJawab) {
            $anyPerangkatDaerah = \App\Models\User::whereHas('roles', function($query) {
                    $query->where('name', 'perangkat_daerah');
                })
                ->with('userDetail')
                ->first();

            if ($anyPerangkatDaerah) {
                $penanggungJawab = [
                    'id' => $anyPerangkatDaerah->id,
                    'name' => $anyPerangkatDaerah->name,
                    'nip' => $anyPerangkatDaerah->userDetail ? $anyPerangkatDaerah->userDetail->nip : null,
                    'nama_skpd' => $tugas->skpd->nama_skpd
                ];
                $method = 'any_perangkat_daerah';
            }
        }

        \Log::info('RencanaAwal getPenanggungJawabData COMPREHENSIVE', [
            'skpd_id' => $tugas->skpd_id,
            'method_used' => $method,
            'tim_kerja_exists' => $timKerja ? true : false,
            'tim_kerja_operator_exists' => $timKerja && $timKerja->operator ? true : false,
            'skpd_user_id' => $tugas->skpd->user_id ?? 'null',
            'final_penanggung_jawab' => $penanggungJawab,
            'result_name' => $penanggungJawab ? $penanggungJawab['name'] : 'NOT_FOUND',
            'result_nip' => $penanggungJawab ? $penanggungJawab['nip'] : 'NOT_FOUND'
        ]);

        // Return result atau default fallback dengan struktur yang sesuai untuk frontend
        if ($penanggungJawab) {
            return [
                'id' => $penanggungJawab['id'],
                'name' => $penanggungJawab['name'],
                'nip' => $penanggungJawab['nip'],
                'nama_skpd' => $penanggungJawab['nama_skpd'],
                // Tambahan untuk kompatibilitas dengan frontend
                'user_detail' => [
                    'nip' => $penanggungJawab['nip']
                ]
            ];
        }

        return [
            'id' => $tugas->skpd_id,
            'name' => 'Tidak tersedia',
            'nip' => null,
            'nama_skpd' => $tugas->skpd->nama_skpd,
            'user_detail' => [
                'nip' => null
            ]
        ];
    }

    /**
     * Get available urusan IDs for SKPD
     */
    private function getAvailableUrusans($skpdTugas)
    {
        return $skpdTugas
            ->filter(function($item) {
                return $item->kodeNomenklatur->jenis_nomenklatur == 0
                    && $item->kodeNomenklatur->details->first();
            })
            ->pluck('kodeNomenklatur.details.0.id_urusan')
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Process anggaran data for subkegiatan (optimized)
     */
    private function processAnggaranData($subkegiatanTugas, $periodeAktif)
    {
        $dataAnggaranTerakhir = [];

        foreach ($subkegiatanTugas as $subkegiatan) {
            $monitoring = $subkegiatan->monitoring->first();

            if (!$monitoring) {
                $dataAnggaranTerakhir[$subkegiatan->id] = $this->getEmptyAnggaranData();
                continue;
            }

            $sumberAnggaranValues = $this->getEmptyAnggaranStructure();
            $sumberDanaValues = $this->getEmptyAnggaranStructure();

            // Use already loaded relations instead of querying again
            foreach ($monitoring->anggaran as $anggaran) {
                if (!$anggaran->sumberAnggaran) continue;

                $key = $this->normalizeKey(strtolower(str_replace(' ', '_', $anggaran->sumberAnggaran->nama)));

                if (array_key_exists($key, $sumberAnggaranValues)) {
                    $sumberAnggaranValues[$key] = true;

                    // Use already loaded pagu relations
                    $latestPagu = $anggaran->pagu
                        ->where('kategori', 1)
                        ->when($periodeAktif, function($collection) use ($periodeAktif) {
                            return $collection->where('periode_id', $periodeAktif->id);
                        })
                        ->sortByDesc('created_at')
                        ->first();

                    if ($latestPagu) {
                        $sumberDanaValues[$key] = $latestPagu->dana;
                    }
                }
            }

            $dataAnggaranTerakhir[$subkegiatan->id] = [
                'sumber_anggaran' => $sumberAnggaranValues,
                'values' => $sumberDanaValues
            ];
        }

        return $dataAnggaranTerakhir;
    }

    /**
     * Get empty anggaran data structure
     */
    private function getEmptyAnggaranData()
    {
        return [
            'sumber_anggaran' => $this->getEmptyAnggaranStructure(),
            'values' => $this->getEmptyAnggaranStructure()
        ];
    }

    /**
     * Get empty anggaran structure
     */
    private function getEmptyAnggaranStructure()
    {
        return [
            'dak' => false,
            'dak_peruntukan' => false,
            'dak_fisik' => false,
            'dak_non_fisik' => false,
            'blud' => false
        ];
    }

    /**
     * Prepare data for Inertia render (used by finalize methods)
     */
    private function prepareInertiaData($tugasId, $urusanId = null)
    {
        // Load basic data using the same helper methods as show()
        $tugas = $this->loadSkpdData($tugasId);
        $periodeAktif = $this->getActivePeriode();
        $skpdTugas = $this->loadSkpdTugasWithMonitoring($tugas->skpd_id, $periodeAktif);

        if (!$urusanId) {
            $urusanId = $tugas->kodeNomenklatur->details->first()->id_urusan;
        }

        $bidangurusanTugas = $this->filterTugasByJenisAndUrusan($skpdTugas, 1, $urusanId);
        $programTugas = $this->filterTugasByJenisAndUrusan($skpdTugas, 2, $urusanId);
        $kegiatanTugas = $this->filterTugasByJenisAndUrusan($skpdTugas, 3, $urusanId);
        $subkegiatanTugas = $this->filterTugasByJenisAndUrusan($skpdTugas, 4, $urusanId);

        $kepalaSkpd = $this->getKepalaSkpdName($tugas);

        // Process anggaran data using helper method
        $dataAnggaranTerakhir = $this->processAnggaranData($subkegiatanTugas, $periodeAktif);

        // Get SKPD data yang lebih lengkap
        $skpdData = $this->getDetailedSkpdData($tugas);

        // Get user penanggung jawab (operator)
        $penanggungJawab = $this->getPenanggungJawabData($tugas);

        return [
            'tugas' => $tugas,
            'bidangurusanTugas' => $bidangurusanTugas,
            'programTugas' => $programTugas,
            'kegiatanTugas' => $kegiatanTugas,
            'subkegiatanTugas' => $subkegiatanTugas,
            'kepalaSkpd' => $kepalaSkpd,
            'dataAnggaranTerakhir' => $dataAnggaranTerakhir,
            'periodeAktif' => $periodeAktif ? [$periodeAktif] : [],
            'skpd' => $skpdData,
            'user' => $penanggungJawab
        ];
    }

    public function create()
    {
        $kodeNomenklatur = KodeNomenklatur::all();
        return Inertia::render('Monitoring/Create', [
            'kodeNomenklatur' => $kodeNomenklatur,
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
            'pagu_pokok' => 'required|numeric',
            'pagu_parsial' => 'nullable|numeric',
            'pagu_perubahan' => 'nullable|numeric',
            'targets' => 'required|array',
            'targets.*.kinerja_fisik' => 'required|numeric',
            'targets.*.keuangan' => 'required|numeric',
            'tugas_id' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();

            // Get the tugas to determine its type (program, kegiatan, or subkegiatan)
            $tugas = SkpdTugas::with('kodeNomenklatur')->findOrFail($validated['tugas_id']);
            $jenisNomenklatur = $tugas->kodeNomenklatur->jenis_nomenklatur;

            // Cari atau buat monitoring untuk SKPD ini
            $monitoring = Monitoring::where('skpd_tugas_id', $validated['tugas_id'])
                ->where('deskripsi', $validated['deskripsi'])
                ->where('tahun', $validated['tahun'])
                ->where('sumber_dana', $validated['sumber_dana']) // penting!
                ->first();


            if (!$monitoring) {
                $monitoring = Monitoring::create([
                    'skpd_tugas_id' => $validated['tugas_id'],
                    'sumber_dana' => $validated['sumber_dana'],
                    'periode_id' => $validated['periode_id'],
                    'tahun' => $validated['tahun'],
                    'deskripsi' => $validated['deskripsi'],
                    'pagu_pokok' => $validated['pagu_pokok'],
                    'pagu_parsial' => $validated['pagu_parsial'],
                    'pagu_perubahan' => $validated['pagu_perubahan'],
                    'is_finalized' => false,
                ]);
            } else if (!$monitoring->is_finalized) {
                $monitoring->update([
                    'sumber_dana' => $validated['sumber_dana'],
                    'pagu_pokok' => $validated['pagu_pokok'],
                    'pagu_parsial' => $validated['pagu_parsial'],
                    'pagu_perubahan' => $validated['pagu_perubahan'],
                ]);

                // // Hapus target lama
                // $monitoring->targets()->delete();
            } else {
                DB::rollBack();
                return back()->with('error', 'Data monitoring sudah difinalisasi dan tidak dapat diubah');
            }

            // Simpan target baru
            foreach ($validated['targets'] as $index => $target) {
                MonitoringTarget::create([
                    'monitoring_id' => $monitoring->id,
                    'periode_id' => ($index + 1),
                    'kinerja_fisik' => $target['kinerja_fisik'],
                    'keuangan' => $target['keuangan']
                ]);
            }

            DB::commit();

            // Redirect ke halaman show dengan pesan sukses
            return redirect()->route('monitoring.rencanaawal', ['id' => $validated['tugas_id']])
                ->with('success', 'Data monitoring berhasil disimpan');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }


    public function finalizeMonitoring(Request $request)
    {
        $validated = $request->validate([
            'skpd_id' => 'required|exists:skpd,id',
            'tahun' => 'required|digits:4|integer',
            'tugas_id' => 'required|numeric',
        ]);

        try {
            $monitoring = Monitoring::where('skpd_tugas_id', $validated['tugas_id'])
                ->where('deskripsi', 'Rencana Awal')
                ->where('tahun', $validated['tahun'])
                ->first();

            if (!$monitoring) {
                return back()->with('error', 'Data monitoring tidak ditemukan');
            }

            $monitoring->update(['is_finalized' => true]);

            // Use helper method to prepare data
            $data = $this->prepareInertiaData($validated['tugas_id']);

            return Inertia::render('Monitoring/RencanaAwal', array_merge($data, [
                'isFinalized' => true,
                'flash' => [
                    'success' => 'Data monitoring berhasil difinalisasi'
                ]
            ]));

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memfinalisasi data: ' . $e->getMessage());
        }
    }

    public function finalizeRow(Request $request)
    {
        $validated = $request->validate([
            'skpd_id' => 'required|exists:skpd,id',
            'tahun' => 'required|digits:4|integer',
            'tugas_id' => 'required|numeric',
        ]);

        try {
            $skpdTugas = SkpdTugas::find($validated['tugas_id']);
            if ($skpdTugas) {
                $skpdTugas->update(['is_finalized' => true]);
            }

            // Get monitoring data to check finalization status
            $monitoring = Monitoring::where('skpd_tugas_id', $validated['tugas_id'])
                ->where('deskripsi', 'Rencana Awal')
                ->where('tahun', $validated['tahun'])
                ->first();

            // Use helper method to prepare data
            $data = $this->prepareInertiaData($validated['tugas_id']);

            return Inertia::render('Monitoring/RencanaAwal', array_merge($data, [
                'isFinalized' => $monitoring ? $monitoring->is_finalized : false,
                'flash' => [
                    'success' => 'Data baris berhasil difinalisasi'
                ]
            ]));

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memfinalisasi baris: ' . $e->getMessage());
        }
    }

    /**
     * Delete a rencana awal monitoring item
     *
     * @param int $id The ID of the item to delete (skpd_tugas_id)
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        try {
            // Find monitoring records for this tugas
            $monitoring = Monitoring::where('skpd_tugas_id', $id)
                ->where('deskripsi', 'Rencana Awal')
                ->first();

            if ($monitoring) {
                // Delete related targets
                $monitoring->targets()->delete();

                // Delete monitoring anggaran and related pagu records
                foreach ($monitoring->anggaran as $anggaran) {
                    // Delete related pagu records
                    $anggaran->pagu()->delete();
                    // Delete the anggaran record
                    $anggaran->delete();
                }

                // Delete the monitoring record
                $monitoring->delete();

                return response()->json(['success' => true, 'message' => 'Item berhasil dihapus']);
            }

            return response()->json(['success' => false, 'message' => 'Item tidak ditemukan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus item: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Save target data for a specific task (subkegiatan)
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function saveTarget(Request $request)
    {
        // Log incoming request for debugging
        Log::info('Received data for saveTarget', $request->all());

        $validated = $request->validate([
            'skpd_tugas_id' => 'required|numeric',
            'tahun' => 'required',
            // PERBAIKAN: Hapus validation untuk deskripsi karena tidak diperlukan di RencanaAwal
            // 'deskripsi' => 'required|string', // Field ini hanya untuk TriwulanController
            'targets' => 'required|array',
            'targets.*.kinerja_fisik' => 'required|numeric|min:0|max:100',
            'targets.*.keuangan' => 'required|numeric|min:0',
            'targets.*.triwulan' => 'required|numeric|min:1|max:4',
            'sumber_anggaran_id' => 'required|numeric',
            'periode_id' => 'nullable|numeric',
            // PERBAIKAN: Hapus validation untuk pagu karena tidak diperlukan di RencanaAwal
            // 'pagu' => 'required|array', // Pagu hanya diset di ManajemenAnggaranController
            // 'pagu.pokok' => 'nullable|numeric|min:0',
            // 'pagu.parsial' => 'nullable|numeric|min:0',
            // 'pagu.perubahan' => 'nullable|numeric|min:0',
        ]);



        try {
            // Check if any period is currently open
            $anyOpenPeriode = Periode::where('status', 1)->first();

            // If no period is open, return error
            if (!$anyOpenPeriode) {
                return redirect()->route('monitoring.rencanaawal', ['id' => $validated['skpd_tugas_id']])
                    ->with('error', 'Tidak ada periode yang aktif. Data Rencana Awal tidak dapat disimpan saat ini.');
            }

            DB::beginTransaction();

            // Log input untuk debugging
            Log::info('Received data for saveTarget', $request->all());

            // PERBAIKAN: Cari monitoring yang sudah ada dari Manajemen Anggaran
            // Prioritas: 1) Monitoring dengan deskripsi kosong (dari Manajemen Anggaran)
            //           2) Monitoring dengan deskripsi "Rencana Awal"
            //           3) Buat baru jika tidak ada
            $monitoring = Monitoring::where('skpd_tugas_id', $validated['skpd_tugas_id'])
                ->where('tahun', $validated['tahun'])
                ->orderByRaw("CASE WHEN deskripsi = '' THEN 1 WHEN deskripsi = 'Rencana Awal' THEN 2 ELSE 3 END")
                ->first();

            if (!$monitoring) {
                // Buat monitoring baru jika tidak ada sama sekali
                // PERBAIKAN: RencanaAwal TIDAK boleh mengisi deskripsi dan nama_pptk
                // Field ini hanya boleh diisi di TriwulanController
                $monitoring = Monitoring::create([
                    'skpd_tugas_id' => $validated['skpd_tugas_id'],
                    'tahun' => $validated['tahun'],
                    'periode_id' => $validated['periode_id'],
                    // deskripsi dan nama_pptk akan diisi nanti di TriwulanController
                ]);
            } else {
                // Update monitoring yang sudah ada HANYA dengan periode_id
                // PERBAIKAN: TIDAK update deskripsi dan nama_pptk
                $monitoring->update([
                    'periode_id' => $validated['periode_id'],
                    // deskripsi dan nama_pptk tetap seperti semula (tidak diubah)
                ]);
            }

            Log::info('Created or found monitoring record', ['id' => $monitoring->id]);

            // PERBAIKAN: RencanaAwal TIDAK boleh membuat monitoring_anggaran baru!
            // Hanya cari monitoring_anggaran yang sudah ada (dibuat di ManajemenAnggaran)
            $monitoringAnggaran = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                ->where('sumber_anggaran_id', $validated['sumber_anggaran_id'])
                ->first();

            if (!$monitoringAnggaran) {
                Log::warning('MonitoringAnggaran not found - sumber anggaran belum diset', [
                    'monitoring_id' => $monitoring->id,
                    'sumber_anggaran_id' => $validated['sumber_anggaran_id']
                ]);

                return redirect()->route('monitoring.rencanaawal', ['id' => $validated['skpd_tugas_id']])
                    ->with('error', 'Sumber anggaran belum diset. Silakan set sumber anggaran di Manajemen Anggaran terlebih dahulu.');
            }

            Log::info('Found existing monitoring_anggaran record', ['id' => $monitoringAnggaran->id]);

            // PERBAIKAN: RencanaAwal TIDAK boleh mengubah pagu!
            // Pagu hanya boleh diset di ManajemenAnggaranController
            // RencanaAwal hanya boleh menyimpan target

            // HAPUS: Kode penyimpanan pagu karena tidak sesuai dengan separation of concerns
            // Pagu sudah diset di ManajemenAnggaranController dan tidak boleh diubah di sini

            // Update atau buat target baru untuk setiap triwulan
            foreach ($validated['targets'] as $target) {
                // Pemetaan triwulan ke periode_id yang benar
                $periode_id = $target['triwulan'] + 1; // Triwulan 1 -> periode_id 2, Triwulan 2 -> periode_id 3, dst

                MonitoringTarget::updateOrCreate(
                    [
                        'monitoring_anggaran_id' => $monitoringAnggaran->id,
                        'periode_id' => $periode_id, // Gunakan periode_id yang sudah dipetakan
                    ],
                    [
                        'kinerja_fisik' => $target['kinerja_fisik'],
                        'keuangan' => $target['keuangan'],
                    ]
                );
            }

            DB::commit();

            // Load ulang data SKPD tugas untuk memastikan data terbaru
            $skpdTugas = SkpdTugas::with([
                'kodeNomenklatur.details',
                'monitoring' => function($query) use ($validated) {
                    // PERBAIKAN: Tidak filter berdasarkan deskripsi karena deskripsi kosong di RencanaAwal
                    $query->where('tahun', $validated['tahun']);
                },
                'monitoring.anggaran.target' => function($query) {
                    $query->orderBy('periode_id');
                },
                'monitoring.anggaran.sumberAnggaran',
                'monitoring.anggaran.pagu'
            ])->find($validated['skpd_tugas_id']);

            // Load data monitoring yang baru disimpan dengan relasinya
            $updatedMonitoring = Monitoring::with([
                'anggaran' => function($query) use ($validated) {
                    $query->where('sumber_anggaran_id', $validated['sumber_anggaran_id']);
                },
                'anggaran.target' => function($query) {
                    $query->orderBy('periode_id');
                },
                'anggaran.sumberAnggaran',
                'anggaran.pagu'
            ])->find($monitoring->id);

            // Get all pagu data for this monitoring anggaran (ambil ulang dari database setelah simpan)
            $paguData = [
                'pokok' => 0,
                'parsial' => 0,
                'perubahan' => 0
            ];

            $allPagu = \App\Models\MonitoringPagu::where('monitoring_anggaran_id', $monitoringAnggaran->id)
                ->when($validated['periode_id'], function($query) use ($validated) {
                    return $query->where('periode_id', $validated['periode_id']);
                })
                ->get();

            foreach ($allPagu as $pagu) {
                switch ($pagu->kategori) {
                    case 1:
                        $paguData['pokok'] = $pagu->dana;
                        break;
                    case 2:
                        $paguData['parsial'] = $pagu->dana;
                        break;
                    case 3:
                        $paguData['perubahan'] = $pagu->dana;
                        break;
                }
            }

            // Get sumber anggaran data
            $sumberAnggaranData = [];
            $sumberAnggaranTypes = ['dau', 'dau_peruntukan', 'dak_fisik', 'dak_non_fisik', 'blud'];  // PERBAIKAN: 'dau' dan 'dau_peruntukan'

            foreach ($sumberAnggaranTypes as $type) {
                $sumberAnggaranData[$type] = [
                    'active' => false,
                    'value' => 0
                ];
            }

            if ($monitoringAnggaran->sumberAnggaran) {
                $key = $this->normalizeKey(strtolower(str_replace(' ', '_', $monitoringAnggaran->sumberAnggaran->nama)));
                if (array_key_exists($key, $sumberAnggaranData)) {
                    $sumberAnggaranData[$key] = [
                        'active' => true,
                        'value' => $paguData['pokok']
                    ];
                }
            }

            // Return response dengan data yang diperbarui dan lengkap
            // Tambahkan log untuk debugging
            \Log::info('Target berhasil disimpan dengan pemetaan triwulan->periode_id', [
                'targets' => $updatedMonitoring->anggaran->first() ?
                    $updatedMonitoring->anggaran->first()->target->map(function($target) {
                        return [
                            'id' => $target->id,
                            'periode_id' => $target->periode_id,
                            'kinerja_fisik' => $target->kinerja_fisik,
                            'keuangan' => $target->keuangan
                        ];
                    }) : []
            ]);

            // Return redirect with success message
            return redirect()->route('monitoring.rencanaawal', ['id' => $validated['skpd_tugas_id']])
                ->with('success', 'Target berhasil disimpan');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in saveTarget: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('monitoring.rencanaawal', ['id' => $validated['skpd_tugas_id']])
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Delete target data for a specific monitoring anggaran
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function deleteTarget(Request $request)
    {
        $validated = $request->validate([
            'skpd_tugas_id' => 'required|numeric',
            'sumber_anggaran_id' => 'required|numeric',
            'tahun' => 'required',
            'periode_id' => 'nullable|numeric',
        ]);

        try {
            Log::debug('Received data for deleteTarget', $request->all());

            // Cari monitoring berdasarkan skpd_tugas_id dan tahun
            $monitoring = Monitoring::where('skpd_tugas_id', $validated['skpd_tugas_id'])
                ->where('tahun', $validated['tahun'])
                ->first();

            if (!$monitoring) {
                Log::warning('Monitoring record not found', [
                    'skpd_tugas_id' => $validated['skpd_tugas_id'],
                    'tahun' => $validated['tahun']
                ]);
                return response()->json(['success' => false, 'message' => 'Data monitoring tidak ditemukan'], 404);
            }

            // Cari monitoring anggaran berdasarkan monitoring_id dan sumber_anggaran_id
            $monitoringAnggaran = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                ->where('sumber_anggaran_id', $validated['sumber_anggaran_id'])
                ->first();

            if (!$monitoringAnggaran) {
                Log::warning('MonitoringAnggaran record not found', [
                    'monitoring_id' => $monitoring->id,
                    'sumber_anggaran_id' => $validated['sumber_anggaran_id']
                ]);

                // Jika tidak ditemukan, coba lihat apa saja sumber anggaran yang ada
                $availableAnggarans = MonitoringAnggaran::where('monitoring_id', $monitoring->id)->get();
                Log::info('Available monitoring anggaran records', [
                    'count' => $availableAnggarans->count(),
                    'records' => $availableAnggarans->pluck('sumber_anggaran_id')->toArray()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Data anggaran tidak ditemukan',
                    'available_anggaran_ids' => $availableAnggarans->pluck('sumber_anggaran_id')->toArray()
                ], 404);
            }

            // Hapus semua target untuk monitoring_anggaran_id ini
            $deletedCount = MonitoringTarget::where('monitoring_anggaran_id', $monitoringAnggaran->id)->delete();
            Log::info('Deleted targets', ['count' => $deletedCount]);

            return response()->json([
                'success' => true,
                'message' => 'Target berhasil dihapus',
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            Log::error('Error in deleteTarget: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}

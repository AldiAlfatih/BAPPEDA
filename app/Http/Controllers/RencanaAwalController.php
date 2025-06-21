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

        // Get all available urusan
        $allUrusans = KodeNomenklatur::where('jenis_nomenklatur', 0)
            ->whereIn('id', function($query) use ($tugas) {
                $query->select('id_nomenklatur')
                    ->from('nomenklatur_details')
                    ->where('id_urusan', function($subQuery) use ($tugas) {
                        $subQuery->select('id_urusan')
                            ->from('nomenklatur_details')
                            ->where('id_skpd', $tugas->skpd_id);
                    });
            })
            ->get();

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
            'user' => [
                'id' => $tugas->skpd_id,
                'nama_skpd' => $tugas->skpd->nama_skpd
            ],
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
            'skpd.kepala.user.userDetail',
            'skpd.kepala' => function($query) {
                $query->where('is_aktif', 1);
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

        if ($kepala) {
            if ($kepala->user && $kepala->user->userDetail && $kepala->user->userDetail->nama) {
                $kepalaSkpd = $kepala->user->userDetail->nama;
            } elseif ($kepala->user && $kepala->user->name) {
                $kepalaSkpd = $kepala->user->name;
            }
        }

        return $kepalaSkpd;
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

        return [
            'tugas' => $tugas,
            'bidangurusanTugas' => $bidangurusanTugas,
            'programTugas' => $programTugas,
            'kegiatanTugas' => $kegiatanTugas,
            'subkegiatanTugas' => $subkegiatanTugas,
            'kepalaSkpd' => $kepalaSkpd,
            'dataAnggaranTerakhir' => $dataAnggaranTerakhir,
            'periodeAktif' => $periodeAktif ? [$periodeAktif] : [],
            'user' => [
                'id' => $tugas->skpd_id,
                'nama_skpd' => $tugas->skpd->nama_skpd
            ]
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

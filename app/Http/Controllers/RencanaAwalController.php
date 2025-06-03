<?php

namespace App\Http\Controllers;

use App\Models\SkpdTugas;
use App\Models\KodeNomenklatur;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Monitoring;
use App\Models\MonitoringTarget;
use App\Models\MonitoringAnggaran;
use App\Models\MonitoringPagu;
use App\Models\SumberAnggaran;
use App\Models\Periode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RencanaAwalController extends Controller
{
    public function show($id, Request $request)
    {
        // Get the specified bidang urusan ID from the request (if provided)
        $requestedUrusanId = $request->input('urusan_id');

        // Ambil tugas SKPD berdasarkan ID
        $tugas = SkpdTugas::with([
            'kodeNomenklatur',
            'skpd.skpdKepala.user.userDetail',
            'skpd.skpdKepala' => function($query) {
                $query->where('is_aktif', 1);
            }
        ])->findOrFail($id);

        // Get active periode
        $periodeAktif = Periode::where('is_aktif', 1)
            ->where('tahap', 'like', '%Rencana%')
            ->with(['tahap', 'tahun'])
            ->first();

        // Ambil program, kegiatan, dan subkegiatan terkait dengan tugas SKPD
        $skpdTugas = SkpdTugas::where('skpd_id', $tugas->skpd_id)
            ->where('is_aktif', 1)
            ->with(['kodeNomenklatur.details', 'monitoring.targets'])
            ->get();

        // Get all available urusan for this SKPD
        $availableUrusans = $skpdTugas
            ->filter(function($item) {
                return $item->kodeNomenklatur->jenis_nomenklatur == 0 // Urusan
                    && $item->kodeNomenklatur->details->first();
            })
            ->pluck('kodeNomenklatur.details.0.id_urusan')
            ->unique()
            ->values()
            ->toArray();

        // If no specific urusan requested, use the first available one
        $urusanId = $requestedUrusanId ?? ($availableUrusans[0] ?? null);

        // If we still don't have a valid urusan ID, try to get it from the current task
        if (!$urusanId && $tugas->kodeNomenklatur->details->first()) {
            $urusanId = $tugas->kodeNomenklatur->details->first()->id_urusan;
        }

        // Ambil data monitoring untuk SKPD ini
        $monitoring = Monitoring::where('skpd_id', $tugas->skpd_id)
            ->where('deskripsi', 'Rencana Awal')
            ->where('tahun', date('Y'))
            ->with('targets')
            ->first();

        // Ambil kepala SKPD
        $kepalaSkpd = '-';
        $kepala = $tugas->skpd->skpdKepala->first();
        if ($kepala) {
            if ($kepala->user && $kepala->user->userDetail && $kepala->user->userDetail->nama) {
                $kepalaSkpd = $kepala->user->userDetail->nama;
            } elseif ($kepala->user && $kepala->user->name) {
                $kepalaSkpd = $kepala->user->name;
            }
        }

        // Get bidang urusan data for the current urusan
        $bidangUrusan = KodeNomenklatur::where('jenis_nomenklatur', 1)
            ->whereHas('details', function($query) use ($urusanId) {
                $query->where('id_urusan', $urusanId);
            })
            ->with(['details' => function($query) {
                $query->select('id', 'id_nomenklatur', 'id_urusan', 'id_bidang_urusan');
            }])
            ->first();

        // Filter program, kegiatan, and subkegiatan for the selected urusan
        $programTugas = $skpdTugas->filter(function($item) use ($urusanId) {
            return $item->kodeNomenklatur->jenis_nomenklatur == 2
                && $item->kodeNomenklatur->details->first()
                && $item->kodeNomenklatur->details->first()->id_urusan == $urusanId;
        })->map(function($item) use ($monitoring) {
            if ($monitoring) {
                $item->monitoring = $monitoring;
            }
            return $item;
        })->values();

        $kegiatanTugas = $skpdTugas->filter(function($item) use ($urusanId) {
            return $item->kodeNomenklatur->jenis_nomenklatur == 3
                && $item->kodeNomenklatur->details->first()
                && $item->kodeNomenklatur->details->first()->id_urusan == $urusanId;
        })->map(function($item) use ($monitoring) {
            if ($monitoring) {
                $item->monitoring = $monitoring;
            }
            return $item;
        })->values();

        $subkegiatanTugas = $skpdTugas->filter(function($item) use ($urusanId) {
            return $item->kodeNomenklatur->jenis_nomenklatur == 4
                && $item->kodeNomenklatur->details->first()
                && $item->kodeNomenklatur->details->first()->id_urusan == $urusanId;
        })->map(function($item) use ($monitoring) {
            if ($monitoring) {
                $item->monitoring = $monitoring;
            }
            return $item;
        })->values();

        // Get all sumber dana options
        $sumberDanaOptions = SumberAnggaran::all();

        // Get the latest pagu data for each subkegiatan and each funding source
        $subkegiatanWithSumberDana = [];
        $dataAnggaranTerakhir = [];

        foreach ($subkegiatanTugas as $subkegiatan) {
            // Find the latest monitoring for this subkegiatan
            $monitoring = Monitoring::where('tugas_id', $subkegiatan->id)
                ->where('tahun', date('Y'))
                ->latest()
                ->first();

            if (!$monitoring) {
                // If no monitoring exists for this subkegiatan, create an empty record for display
                $dataAnggaranTerakhir[$subkegiatan->id] = [
                    'sumber_anggaran' => [
                        'dak' => false,
                        'dak_peruntukan' => false,
                        'dak_fisik' => false,
                        'dak_non_fisik' => false,
                        'blud' => false
                    ],
                    'values' => [
                        'dak' => 0,
                        'dak_peruntukan' => 0,
                        'dak_fisik' => 0,
                        'dak_non_fisik' => 0,
                        'blud' => 0
                    ]
                ];
                continue;
            }

            // Get all monitoring_anggaran for this monitoring with the latest pagu
            $monitoringAnggarans = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                ->with(['sumberAnggaran'])
                ->get();

            $sumberAnggaranValues = [
                'dak' => false,
                'dak_peruntukan' => false,
                'dak_fisik' => false,
                'dak_non_fisik' => false,
                'blud' => false
            ];

            $sumberDanaValues = [
                'dak' => 0,
                'dak_peruntukan' => 0,
                'dak_fisik' => 0,
                'dak_non_fisik' => 0,
                'blud' => 0
            ];

            foreach ($monitoringAnggarans as $anggaran) {
                if (!$anggaran->sumberAnggaran) continue;

                // Get the latest pagu for this anggaran
                $latestPagu = MonitoringPagu::where('monitoring_anggaran_id', $anggaran->id)
                    ->where('kategori', 1) // Pokok
                    ->when($periodeAktif, function($query) use ($periodeAktif) {
                        return $query->where('periode_id', $periodeAktif->id);
                    })
                    ->latest()
                    ->first();

                if ($latestPagu) {
                    // Map the sumberAnggaran name to our keys
                    $key = strtolower(str_replace(' ', '_', $anggaran->sumberAnggaran->nama));
                    $key = $this->normalizeKey($key);

                    if (array_key_exists($key, $sumberAnggaranValues)) {
                        $sumberAnggaranValues[$key] = true;
                        $sumberDanaValues[$key] = $latestPagu->dana;

                        // Also add to the subkegiatanWithSumberDana array for display
                        $subkegiatanWithSumberDana[] = [
                            'id' => $subkegiatan->id,
                            'kode_nomenklatur' => $subkegiatan->kodeNomenklatur,
                            'sumber_anggaran' => $anggaran->sumberAnggaran->nama,
                            'dana' => $latestPagu->dana,
                            'monitoring' => $monitoring
                        ];
                    }
                }
            }

            // Store the combined data
            $dataAnggaranTerakhir[$subkegiatan->id] = [
                'sumber_anggaran' => $sumberAnggaranValues,
                'values' => $sumberDanaValues
            ];
            
            // If no sumber dana entries were found, add a placeholder
            if (empty(array_filter($sumberAnggaranValues))) {
                $subkegiatanWithSumberDana[] = [
                    'id' => $subkegiatan->id,
                    'kode_nomenklatur' => $subkegiatan->kodeNomenklatur,
                    'sumber_anggaran' => null,
                    'dana' => 0,
                    'monitoring' => $monitoring
                ];
            }
        }

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

        // Assign bidangurusanTugas before returning
        $bidangurusanTugas = $skpdTugas->filter(function($item) use ($urusanId) {
            return $item->kodeNomenklatur->jenis_nomenklatur == 1
                && $item->kodeNomenklatur->details->first()
                && $item->kodeNomenklatur->details->first()->id_urusan == $urusanId;
        })->values();

        // Mengirimkan data ke tampilan
        return Inertia::render('Monitoring/RencanaAwal', [
            'tugas' => $tugas,
            'bidangurusanTugas' => $bidangurusanTugas,
            'programTugas' => $programTugas,
            'kegiatanTugas' => $kegiatanTugas,
            'subkegiatanTugas' => $subkegiatanTugas,
            'subkegiatanWithSumberDana' => $subkegiatanWithSumberDana,
            'kepalaSkpd' => $kepalaSkpd,
            'isFinalized' => $monitoring ? $monitoring->is_finalized : false,
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
            $monitoring = Monitoring::where('skpd_id', $validated['skpd_id'])
                ->where('deskripsi', $validated['deskripsi'])
                ->where('tahun', $validated['tahun'])
                ->where('tugas_id', $validated['tugas_id'])
                ->first();

            if (!$monitoring) {
                $monitoring = Monitoring::create([
                    'skpd_id' => $validated['skpd_id'],
                    'tugas_id' => $validated['tugas_id'],
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

                // Hapus target lama
                $monitoring->targets()->delete();
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
            return redirect()->route('rencana-awal.show', ['id' => $validated['tugas_id']])
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
        ]);

        try {
            $monitoring = Monitoring::where('skpd_id', $validated['skpd_id'])
                ->where('deskripsi', 'Rencana Awal')
                ->where('tahun', $validated['tahun'])
                ->first();

            if (!$monitoring) {
                return back()->with('error', 'Data monitoring tidak ditemukan');
            }

            $monitoring->update([
                'is_finalized' => true
            ]);

            // Get updated data with monitoring
            $tugas = SkpdTugas::with([
                'kodeNomenklatur',
                'skpd.skpdKepala.user.userDetail',
                'skpd.skpdKepala' => function($query) {
                    $query->where('is_aktif', 1);
                },
                'monitoring.targets'
            ])->where('skpd_id', $validated['skpd_id'])->first();

            $skpdTugas = SkpdTugas::where('skpd_id', $tugas->skpd_id)
                ->where('is_aktif', 1)
                ->with(['kodeNomenklatur.details', 'monitoring.targets'])
                ->get();

            $urusanId = $tugas->kodeNomenklatur->details->first()->id_urusan;

            $bidangurusanTugas = $skpdTugas->filter(fn($item) =>
                $item->kodeNomenklatur->jenis_nomenklatur == 1 &&
                $item->kodeNomenklatur->details->first()?->id_urusan == $urusanId
            )->values();

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

            return Inertia::render('Monitoring/RencanaAwal', [
                'tugas' => $tugas,
                'bidangurusanTugas' => $bidangurusanTugas,
                'programTugas' => $programTugas,
                'kegiatanTugas' => $kegiatanTugas,
                'subkegiatanTugas' => $subkegiatanTugas,
                'kepalaSkpd' => $kepalaSkpd,
                'isFinalized' => true,
                'user' => [
                    'id' => $tugas->skpd_id,
                    'nama_skpd' => $tugas->skpd->nama_skpd
                ],
                'flash' => [
                    'success' => 'Data monitoring berhasil difinalisasi'
                ]
            ]);

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
                $skpdTugas->update([
                    'is_finalized' => true
                ]);
            }

            // Get updated data with monitoring
            $tugas = SkpdTugas::with([
                'kodeNomenklatur',
                'skpd.skpdKepala.user.userDetail',
                'skpd.skpdKepala' => function($query) {
                    $query->where('is_aktif', 1);
                },
                'monitoring.targets'
            ])->find($validated['tugas_id']);

            $skpdTugas = SkpdTugas::where('skpd_id', $tugas->skpd_id)
                ->where('is_aktif', 1)
                ->with(['kodeNomenklatur.details', 'monitoring.targets'])
                ->get();

            $urusanId = $tugas->kodeNomenklatur->details->first()->id_urusan;

            $bidangurusanTugas = $skpdTugas->filter(fn($item) =>
                $item->kodeNomenklatur->jenis_nomenklatur == 1 &&
                $item->kodeNomenklatur->details->first()?->id_urusan == $urusanId
            )->values();

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

            // Get monitoring data
            $monitoring = Monitoring::where('skpd_id', $validated['skpd_id'])
                ->where('deskripsi', 'Rencana Awal')
                ->where('tahun', $validated['tahun'])
                ->first();

            return Inertia::render('Monitoring/RencanaAwal', [
                'tugas' => $tugas,
                'bidangurusanTugas' => $bidangurusanTugas,
                'programTugas' => $programTugas,
                'kegiatanTugas' => $kegiatanTugas,
                'subkegiatanTugas' => $subkegiatanTugas,
                'kepalaSkpd' => $kepalaSkpd,
                'isFinalized' => $monitoring ? $monitoring->is_finalized : false,
                'user' => [
                    'id' => $tugas->skpd_id,
                    'nama_skpd' => $tugas->skpd->nama_skpd
                ],
                'flash' => [
                    'success' => 'Data baris berhasil difinalisasi'
                ]
            ]);

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
                foreach ($monitoring->monitoringAnggaran as $anggaran) {
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
        $validated = $request->validate([
            'skpd_tugas_id' => 'required|numeric',
            'tahun' => 'required',
            'deskripsi' => 'required|string',
            'targets' => 'required|array',
            'sumber_anggaran_id' => 'required|numeric',
            'periode_id' => 'nullable|numeric',
            'nama_pptk' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();
            
            // Log input untuk debugging
            Log::debug('Received data for saveTarget', $request->all());
            Log::info('Processing targets data with sumber anggaran ID: ' . $validated['sumber_anggaran_id']);
            
            // Cari atau buat monitoring baru
            $monitoring = Monitoring::firstOrCreate(
                [
                    'skpd_tugas_id' => $validated['skpd_tugas_id'],
                    'tahun' => $validated['tahun'],
                    'deskripsi' => $validated['deskripsi'],
                ],
                [
                    'nama_pptk' => $request->input('nama_pptk', ''),
                    'periode_id' => $validated['periode_id'],
                ]
            );
            
            Log::info('Created or found monitoring record', ['id' => $monitoring->id]);
            
            // Cari atau buat monitoring_anggaran
            $monitoringAnggaran = MonitoringAnggaran::firstOrCreate(
                [
                    'monitoring_id' => $monitoring->id,
                    'sumber_anggaran_id' => $validated['sumber_anggaran_id'],
                ]
            );
            
            Log::info('Created or found monitoring_anggaran record', ['id' => $monitoringAnggaran->id]);
            
            // Hapus target lama jika ada
            $deletedCount = MonitoringTarget::where('monitoring_anggaran_id', $monitoringAnggaran->id)->delete();
            Log::info('Deleted old targets', ['count' => $deletedCount]);
            
            // Simpan target baru
            foreach ($validated['targets'] as $target) {
                $newTarget = MonitoringTarget::create([
                    'monitoring_anggaran_id' => $monitoringAnggaran->id,
                    'periode_id' => $target['triwulan'] ?? null,
                    'kinerja_fisik' => $target['kinerja_fisik'],
                    'keuangan' => $target['keuangan'],
                ]);
                
                Log::info('Created target', [
                    'id' => $newTarget->id,
                    'triwulan' => $target['triwulan'] ?? null,
                    'kinerja_fisik' => $target['kinerja_fisik'],
                    'keuangan' => $target['keuangan']
                ]);
            }
            
            DB::commit();
            return response()->json([
                'success' => true, 
                'message' => 'Target berhasil disimpan',
                'data' => [
                    'monitoring_id' => $monitoring->id,
                    'monitoring_anggaran_id' => $monitoringAnggaran->id
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in saveTarget: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
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
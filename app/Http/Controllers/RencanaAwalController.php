<?php

namespace App\Http\Controllers;

use App\Models\SkpdTugas;
use App\Models\KodeNomenklatur;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Monitoring;
use App\Models\MonitoringTarget;
use Illuminate\Support\Facades\DB;

class RencanaAwalController extends Controller
{
    public function show($id)
    {
        // Ambil tugas SKPD berdasarkan ID
        $tugas = SkpdTugas::with([
            'kodeNomenklatur',
            'skpd.skpdKepala.user.userDetail',
            'skpd.skpdKepala' => function($query) {
                $query->where('is_aktif', 1);
            }
        ])->findOrFail($id);

        // Ambil program, kegiatan, dan subkegiatan terkait dengan tugas SKPD
        $skpdTugas = SkpdTugas::where('skpd_id', $tugas->skpd_id)
            ->where('is_aktif', 1)
            ->with(['kodeNomenklatur.details', 'monitoring.targets'])
            ->get();

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

        // Filter program, kegiatan, dan subkegiatan
        $urusanId = $tugas->kodeNomenklatur->details->first()->id_urusan;

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


        // Ambil data sumber dana untuk setiap subkegiatan
        $subkegiatanWithSumberDana = [];
        
        foreach ($subkegiatanTugas as $subkegiatan) {
            $monitoring = Monitoring::where('tugas_id', $subkegiatan->id)
                ->where('tahun', date('Y'))
                ->where('deskripsi', 'Rencana Awal')
                ->with(['monitoringAnggaran.sumberAnggaran', 'monitoringAnggaran.pagu' => function($query) {
                    $query->where('kategori', 1); // Hanya ambil pagu dengan kategori 1 (pokok)
                }])
                ->first();
            
            if ($monitoring && $monitoring->monitoringAnggaran->isNotEmpty()) {
                foreach ($monitoring->monitoringAnggaran as $anggaran) {
                    $pagu = $anggaran->pagu->first();
                    $subkegiatanWithSumberDana[] = [
                        'id' => $subkegiatan->id,
                        'kode_nomenklatur' => $subkegiatan->kodeNomenklatur,
                        'sumber_anggaran' => $anggaran->sumberAnggaran->nama,
                        'dana' => $pagu ? $pagu->dana : 0,
                        'monitoring' => $monitoring
                    ];
                }
            } else {
                // Jika tidak ada sumber dana, tetap tampilkan subkegiatan dengan sumber dana kosong
                $subkegiatanWithSumberDana[] = [
                    'id' => $subkegiatan->id,
                    'kode_nomenklatur' => $subkegiatan->kodeNomenklatur,
                    'sumber_anggaran' => null,
                    'dana' => 0,
                    'monitoring' => $monitoring
                ];
            }
        }

        // Mengirimkan data ke tampilan
        return Inertia::render('Monitoring/RencanaAwal', [
            'tugas' => $tugas,
            'programTugas' => $programTugas,
            'kegiatanTugas' => $kegiatanTugas,
            'subkegiatanTugas' => $subkegiatanTugas,
            'subkegiatanWithSumberDana' => $subkegiatanWithSumberDana,
            'kepalaSkpd' => $kepalaSkpd,
            'isFinalized' => $monitoring ? $monitoring->is_finalized : false,
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
}

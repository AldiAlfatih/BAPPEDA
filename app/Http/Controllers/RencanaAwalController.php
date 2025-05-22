<?php

namespace App\Http\Controllers;

use App\Models\SkpdTugas;
use App\Models\KodeNomenklatur;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Monitoring;
<<<<<<< HEAD

class RencanaAwalController extends Controller
{
   
=======
use App\Models\MonitoringTarget;
use Illuminate\Support\Facades\DB;

class RencanaAwalController extends Controller
{
    public function show($id)
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

        // Ambil status finalisasi dari monitoring untuk tugas ini
        $monitoring = Monitoring::where('skpd_id', $tugas->skpd_id)
            ->where('deskripsi', 'Rencana Awal')
            ->where('tahun', date('Y'))
            ->first();

        $isFinalized = false;
        if ($monitoring) {
            $isFinalized = $monitoring->is_finalized;
        }

        return Inertia::render('Monitoring/RencanaAwal', [
            'tugas' => $tugas,
            'programTugas' => $programTugas,
            'kegiatanTugas' => $kegiatanTugas,
            'subkegiatanTugas' => $subkegiatanTugas,
            'kepalaSkpd' => $kepalaSkpd,
            'isFinalized' => $isFinalized,
            'user' => [
                'id' => $tugas->skpd_id,
                'nama_skpd' => $tugas->skpd->nama_skpd
            ]
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

            // Update data pada tugas SKPD dengan ID yang diberikan
            $skpdTugas = SkpdTugas::find($validated['tugas_id']);
            if ($skpdTugas) {
                $skpdTugas->update([
                    'pokok' => $validated['pagu_pokok'],
                    'parsial' => $validated['pagu_parsial'],
                    'perubahan' => $validated['pagu_perubahan'],
                    'sumber_dana' => $validated['sumber_dana'],
                ]);
            }

            // Cari atau buat monitoring untuk SKPD ini
            $monitoring = Monitoring::where('skpd_id', $validated['skpd_id'])
                ->where('deskripsi', $validated['deskripsi'])
                ->where('tahun', $validated['tahun'])
                ->first();

            if (!$monitoring) {
                $monitoring = Monitoring::create([
                    'skpd_id' => $validated['skpd_id'],
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
                $monitoring->targets()->create([
                    'periode_id' => ($index + 1),
                    'kinerja_fisik' => $target['kinerja_fisik'],
                    'keuangan' => $target['keuangan']
                ]);
            }

            DB::commit();

            // Get updated data
            $tugas = SkpdTugas::with([
                'kodeNomenklatur',
                'skpd.skpdKepala.user.userDetail',
                'skpd.skpdKepala' => function($query) {
                    $query->where('is_aktif', 1);
                }
            ])->find($validated['tugas_id']);

            $skpdTugas = SkpdTugas::where('skpd_id', $tugas->skpd_id)
                ->where('is_aktif', 1)
                ->with('kodeNomenklatur.details')
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
                'isFinalized' => $monitoring->is_finalized,
                'user' => [
                    'id' => $tugas->skpd_id,
                    'nama_skpd' => $tugas->skpd->nama_skpd
                ],
                'flash' => [
                    'success' => 'Data monitoring berhasil disimpan'
                ]
            ]);

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
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
}

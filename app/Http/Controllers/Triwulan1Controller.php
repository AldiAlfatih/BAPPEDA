<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Skpd;
use App\Models\SkpdTugas;
use App\Models\KodeNomenklatur;
use App\Models\Monitoring;
use App\Models\MonitoringAnggaran;
use App\Models\Periode;
use App\Models\PeriodeTahap;
use App\Models\PeriodeTahun;
use App\Models\MonitoringRealisasi;

class Triwulan1Controller extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('perangkat_daerah')) {
            return redirect()->route('triwulan1.show', $user->id);
        }

        if ($user->hasRole('operator')) {
            $skpdUserIds = Skpd::where('nama_operator', $user->name)->pluck('user_id');
            $users = User::whereIn('id', $skpdUserIds)
                ->role('perangkat_daerah')
                ->with('skpd')
                ->paginate(1000);

            return Inertia::render('Triwulan1', [
                'users' => $users,
            ]);
        }
        $users = User::role('perangkat_daerah')->with('skpd')->paginate(1000);
        return Inertia::render('Triwulan1', [
            'users' => $users,
        ]);
    }

     public function show(string $id)
   {
       $user = User::with('skpd')->findOrFail($id);

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
                   'urusan_id' => $item->details->first() ? $item->details->first()->id_urusan : null
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
                   'bidang_urusan_id' => $item->details->first() ? $item->details->first()->id_bidang_urusan : null
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
                   'program_id' => $item->details->first() ? $item->details->first()->id_program : null
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
                   'kegiatan_id' => $item->details->first() ? $item->details->first()->id_kegiatan : null
               ];
           });

       $skpdTugas = SkpdTugas::where('skpd_id', $user->skpd->id)
           ->where('is_aktif', 1)
           ->with('kodeNomenklatur')
           ->get();

       return Inertia::render('Triwulan1/Show', [
           'user' => $user,
           'skpdTugas' => $skpdTugas,
           'urusanList' => $urusanList,
           'bidangUrusanList' => $bidangUrusanList,
           'programList' => $programList,
           'kegiatanList' => $kegiatanList,
           'subkegiatanList' => $subkegiatanList,
       ]);
   }

   public function showDetail($id)
    {
        $tugas = SkpdTugas::with([
            'kodeNomenklatur',
            'skpd.skpdKepala.user.userDetail',
            'skpd.skpdKepala' => function($query) {
                $query->where('is_aktif', 1);
            },
        ])->findOrFail($id);

        $skpdTugas = SkpdTugas::where('skpd_id', $tugas->skpd_id)
            ->where('is_aktif', 1)
            ->with([
                'kodeNomenklatur.details', 
                'monitoring' => function($query) {
                    $query->with(['monitoringAnggaran.monitoringTarget.periode', 'monitoringAnggaran.monitoringRealisasi.periode']);
                }
            ])
            ->get();

        $urusanId = $tugas->kodeNomenklatur->details->first()->id_urusan;

        // Get bidang urusan data
        $bidangUrusan = KodeNomenklatur::where('jenis_nomenklatur', 1)
            ->whereHas('details', function($query) use ($urusanId) {
                $query->where('id_urusan', $urusanId);
            })
            ->first();

        // Find any monitoring that might have deskripsi for bidang urusan
        $bidangUrusanDeskripsi = '-';
        if ($bidangUrusan) {
            // Try to find monitoring entry with this bidang urusan
            $monitoring = \App\Models\Monitoring::whereHas('skpdTugas', function($query) use ($bidangUrusan) {
                $query->whereHas('kodeNomenklatur', function($query) use ($bidangUrusan) {
                    $query->where('id', $bidangUrusan->id);
                });
            })
            ->select('deskripsi')
            ->first();
            
            if ($monitoring && !empty($monitoring->deskripsi)) {
                $bidangUrusanDeskripsi = $monitoring->deskripsi;
            }
        }

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

        // Get monitoring target data with descriptions and realisasi
        $monitoringTargets = [];
        $monitoringRealisasi = [];
        $allTasks = collect()->concat($programTugas)->concat($kegiatanTugas)->concat($subkegiatanTugas);
        
        // Direct query to get all related monitoring targets for these tasks
        $taskIds = $allTasks->pluck('id')->toArray();
        
        // Get all monitoring records for these tasks
        $monitorings = \App\Models\Monitoring::whereIn('skpd_tugas_id', $taskIds)
            ->with(['monitoringAnggaran.monitoringTarget.periode', 'monitoringAnggaran.monitoringRealisasi.periode'])
            ->get();
        
        // Process monitoring data
        foreach ($monitorings as $monitoring) {
            $taskId = $monitoring->skpd_tugas_id;
            
            // If no monitoring anggaran, add default entry with deskripsi
            if ($monitoring->monitoringAnggaran->isEmpty()) {
                $monitoringTargets[] = [
                    'id' => null,
                    'kinerja_fisik' => 0,
                    'keuangan' => 0,
                    'periode' => null,
                    'monitoring_id' => $monitoring->id,
                    'task_id' => $taskId,
                    'deskripsi' => $monitoring->deskripsi,
                    'nama_pptk' => $monitoring->nama_pptk ?? '-'
                ];
            } else {
                // Process monitoring anggaran and targets
                foreach ($monitoring->monitoringAnggaran as $anggaran) {
                    // If no targets, add default entry with deskripsi
                    if ($anggaran->monitoringTarget->isEmpty()) {
                        $monitoringTargets[] = [
                            'id' => null,
                            'kinerja_fisik' => 0,
                            'keuangan' => 0,
                            'periode' => null,
                            'monitoring_id' => $monitoring->id,
                            'task_id' => $taskId,
                            'deskripsi' => $monitoring->deskripsi,
                            'nama_pptk' => $monitoring->nama_pptk ?? '-'
                        ];
                    } else {
                        // Process target data
                        foreach ($anggaran->monitoringTarget as $target) {
                            $monitoringTargets[] = [
                                'id' => $target->id,
                                'kinerja_fisik' => $target->kinerja_fisik,
                                'keuangan' => $target->keuangan,
                                'periode' => $target->periode ? $target->periode->nama : null,
                                'monitoring_id' => $monitoring->id,
                                'task_id' => $taskId,
                                'deskripsi' => $monitoring->deskripsi,
                                'nama_pptk' => $monitoring->nama_pptk ?? '-'
                            ];
                        }
                    }
                    
                    // Process realisasi data
                    if (!$anggaran->monitoringRealisasi->isEmpty()) {
                        foreach ($anggaran->monitoringRealisasi as $realisasi) {
                            $monitoringRealisasi[] = [
                                'id' => $realisasi->id,
                                'kinerja_fisik' => $realisasi->kinerja_fisik,
                                'keuangan' => $realisasi->keuangan,
                                'periode' => $realisasi->periode ? $realisasi->periode->tahap->tahap : 'Triwulan 1',
                                'monitoring_id' => $monitoring->id,
                                'task_id' => $taskId,
                                'monitoring_anggaran_id' => $anggaran->id,
                                'deskripsi' => $monitoring->deskripsi,
                                'nama_pptk' => $monitoring->nama_pptk ?? '-'
                            ];
                        }
                    }
                }
            }
        }

        // We don't need urusan data, only bidang urusan
        return Inertia::render('Triwulan1/Detail', [
            'tugas' => $tugas,
            'programTugas' => $programTugas,
            'kegiatanTugas' => $kegiatanTugas,
            'subkegiatanTugas' => $subkegiatanTugas,
            'kepalaSkpd' => $kepalaSkpd,
            'monitoringTargets' => $monitoringTargets,
            'monitoringRealisasi' => $monitoringRealisasi,
            'bidangUrusan' => $bidangUrusan ? [
                'id' => $bidangUrusan->id,
                'nomor_kode' => $bidangUrusan->nomor_kode,
                'nomenklatur' => $bidangUrusan->nomenklatur,
                'deskripsi' => $bidangUrusanDeskripsi
            ] : null,
            'user' => [
                'id' => $tugas->skpd_id,
                'nama_skpd' => $tugas->skpd->nama_skpd
            ]
        ]);
    }

    /**
     * Save realization data for a subkegiatan
     */
    public function saveRealisasi(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
            'realisasi_fisik' => 'required|numeric',
            'realisasi_keuangan' => 'required|numeric',
            'capaian_fisik' => 'required|numeric',
            'capaian_keuangan' => 'required|numeric',
            'keterangan' => 'nullable|string',
            'nama_pptk' => 'nullable|string',
        ]);

        // Get the Monitoring record associated with this task
        $task = SkpdTugas::findOrFail($request->id);
        
        // Get or create a monitoring record for this task
        $monitoring = Monitoring::firstOrCreate(
            ['skpd_tugas_id' => $task->id],
            [
                'skpd_id' => $task->skpd_id,
                'tahun' => date('Y'),
                'deskripsi' => $request->keterangan ?? 'Realisasi ' . $task->kodeNomenklatur->nomenklatur,
                'nama_pptk' => $request->nama_pptk ?? '-'
            ]
        );
        
        // Update monitoring info if provided
        if ($request->has('keterangan') || $request->has('nama_pptk')) {
            $monitoring->deskripsi = $request->keterangan ?? $monitoring->deskripsi;
            $monitoring->nama_pptk = $request->nama_pptk ?? $monitoring->nama_pptk;
            $monitoring->save();
        }
        
        // Get or create monitoring anggaran
        $anggaran = MonitoringAnggaran::firstOrCreate(
            ['monitoring_id' => $monitoring->id],
            ['sumber_anggaran_id' => 1] // Default sumber anggaran
        );
        
        // Get active current period (Triwulan 1)
        $periode = Periode::whereHas('tahap', function($query) {
            $query->where('tahap', 'like', '%Triwulan 1%');
        })->where('status', 1)->first();
            
        if (!$periode) {
            // If no active Triwulan 1 period, get any Triwulan 1
            $periode = Periode::whereHas('tahap', function($query) {
                $query->where('tahap', 'like', '%Triwulan 1%');
            })->first();
            
            if (!$periode) {
                // Create a new period if none exists
                $tahap = \App\Models\PeriodeTahap::firstOrCreate(['tahap' => 'Triwulan 1']);
                $tahun = \App\Models\PeriodeTahun::first() ?? \App\Models\PeriodeTahun::create(['tahun' => date('Y')]);
                
                $periode = Periode::create([
                    'tahap_id' => $tahap->id,
                    'tahun_id' => $tahun->id,
                    'tanggal_mulai' => date('Y-01-01'),
                    'tanggal_selesai' => date('Y-03-31'),
                    'status' => 1
                ]);
            }
        }
        
        // Try to find existing target for this angaran and period
        $realisasi = MonitoringRealisasi::firstOrCreate(
            [
                'monitoring_anggaran_id' => $anggaran->id,
                'periode_id' => $periode->id
            ],
            [
                'kinerja_fisik' => $request->realisasi_fisik,
                'keuangan' => $request->realisasi_keuangan
            ]
        );
        
        // Update values if record already existed
        if ($realisasi->wasRecentlyCreated === false) {
            $realisasi->kinerja_fisik = $request->realisasi_fisik;
            $realisasi->keuangan = $request->realisasi_keuangan;
            $realisasi->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Data realisasi berhasil disimpan'
        ]);
    }
}
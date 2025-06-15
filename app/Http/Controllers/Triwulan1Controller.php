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
use App\Models\MonitoringPagu;

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

            return Inertia::render('Triwulan', [
                'users' => $users,
                // 'tid' => $tid,

            ]);
        }
        $users = User::role('perangkat_daerah')->with('skpd')->paginate(1000);
        return Inertia::render('Triwulan1', [
            'users' => $users,
        ]);
    }

     public function show(integer $tid, string $id)
   {
       $user = User::with(['skpd', 'userDetail'])->findOrFail($id);

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

   public function showDetail(integer $tid, $id)
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

        $bidangurusanTugas = $skpdTugas->filter(function($item) use ($urusanId) {
            return $item->kodeNomenklatur->jenis_nomenklatur == 1
                && $item->kodeNomenklatur->details->first()
                && $item->kodeNomenklatur->details->first()->id_urusan == $urusanId;
        })->values();

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
        
        // Get all monitoring records for these tasks with filtered monitoringTarget by periode_id = 2
        $monitorings = \App\Models\Monitoring::whereIn('skpd_tugas_id', $taskIds)
            ->with(['monitoringAnggaran' => function($query) {
                $query->with(['monitoringTarget' => function($query) {
                    // Filter monitoringTarget untuk hanya mengambil dengan periode_id = 2
                    $query->where('periode_id', 2);
                    $query->with('periode');
                }, 'monitoringRealisasi' => function($query) {
                    // Filter monitoringRealisasi untuk hanya mengambil dengan periode_id = 2 
                    $query->where('periode_id', 2);
                    $query->with('periode');
                }]);
            }])
            ->get();
        
        // Debug - log jumlah monitoring
        \Log::info('Total monitoring records fetched: ' . $monitorings->count());
        
        // Process monitoring data - khusus periode_id = 2
        foreach ($monitorings as $monitoring) {
            $taskId = $monitoring->skpd_tugas_id;
            
            if ($monitoring->monitoringAnggaran->isEmpty()) {
                // Skip if no anggaran data
                continue;
            }
            
            foreach ($monitoring->monitoringAnggaran as $anggaran) {
                // Process monitoring targets - periode_id = 2 only
                if (!$anggaran->monitoringTarget->isEmpty()) {
                    foreach ($anggaran->monitoringTarget as $target) {
                        // Double check - hanya ambil dengan periode_id = 2
                        if ($target->periode_id != 2) {
                            continue;
                        }
                        
                        $monitoringTargets[] = [
                            'id' => $target->id,
                            'kinerja_fisik' => $target->kinerja_fisik,
                            'keuangan' => $target->keuangan,
                            'periode_id' => $target->periode_id, // Include periode_id explicitly
                            'periode' => $target->periode ? $target->periode->nama : null,
                            'monitoring_id' => $monitoring->id,
                            'task_id' => $taskId,
                            'deskripsi' => $monitoring->deskripsi,
                            'nama_pptk' => $monitoring->nama_pptk ?? '-'
                        ];
                    }
                }
                
                // Process monitoring realisasi - periode_id = 2 only
                if (!$anggaran->monitoringRealisasi->isEmpty()) {
                    foreach ($anggaran->monitoringRealisasi as $realisasi) {
                        // Double check - hanya ambil dengan periode_id = 2
                        if ($realisasi->periode_id != 2) {
                            continue;
                        }
                        
                        $monitoringRealisasi[] = [
                            'id' => $realisasi->id,
                            'kinerja_fisik' => $realisasi->kinerja_fisik,
                            'keuangan' => $realisasi->keuangan,
                            'periode_id' => $realisasi->periode_id, // Include periode_id explicitly
                            'periode' => $realisasi->periode ? $realisasi->periode->nama : null,
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
        
        // Debug - log jumlah target dan realisasi yang ditemukan
        \Log::info('Total monitoring targets (periode_id = 2): ' . count($monitoringTargets));
        \Log::info('Total monitoring realisasi (periode_id = 2): ' . count($monitoringRealisasi));
        
        // We don't need urusan data, only bidang urusan
        return Inertia::render('Triwulan1/Detail', [
            'tugas' => $tugas,
            'bidangurusanTugas' => $bidangurusanTugas,
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

        // Check if the Triwulan 1 period is open
        $periodeTriwulan1 = Periode::whereHas('tahap', function($query) {
            $query->where('tahap', 'like', '%Triwulan 1%');
        })->where('status', 1)->first();
        
        // If Triwulan 1 period is not active, return error
        if (!$periodeTriwulan1) {
            return response()->json([
                'success' => false,
                'message' => 'Periode Triwulan 1 belum dibuka. Data tidak dapat disimpan.'
            ], 403);
        }

        // Get the task record
        $task = SkpdTugas::findOrFail($request->id);
        
        // First, find the Rencana Awal monitoring record to copy budget data from
        $rencanaAwalMonitoring = Monitoring::where('skpd_tugas_id', $task->id)
            ->where('deskripsi', 'Rencana Awal')
            ->first();
            
        // Get or create a monitoring record for REALIZATION specifically
        // Use both skpd_tugas_id and deskripsi to avoid overwriting "Rencana Awal" records
        $monitoring = Monitoring::firstOrCreate(
            [
                'skpd_tugas_id' => $task->id,
                'deskripsi' => 'Realisasi Triwulan 1' // Use specific deskripsi for Triwulan 1
            ],
            [
                'tahun' => date('Y'),
                'nama_pptk' => $request->nama_pptk ?? '-'
            ]
        );
        
        // Update monitoring info if provided
        if ($request->has('keterangan') || $request->has('nama_pptk')) {
            // Keep the deskripsi as 'Realisasi Triwulan 1' to prevent overwriting
            $monitoring->nama_pptk = $request->nama_pptk ?? $monitoring->nama_pptk;
            $monitoring->save();
        }
        
        // Get or create monitoring anggaran
        $anggaran = MonitoringAnggaran::firstOrCreate(
            ['monitoring_id' => $monitoring->id],
            ['sumber_anggaran_id' => 1] // Default sumber anggaran
        );
        
        // Copy budget data (pagu) from the Rencana Awal record if it exists
        if ($rencanaAwalMonitoring) {
            $rencanaAwalAnggaran = MonitoringAnggaran::where('monitoring_id', $rencanaAwalMonitoring->id)
                ->first();
                
            if ($rencanaAwalAnggaran) {
                // Get the pagu data categorized as pokok (category=1)
                $pokuPagu = MonitoringPagu::where('monitoring_anggaran_id', $rencanaAwalAnggaran->id)
                    ->where('kategori', 1) // Pokok
                    ->first();
            
                if ($pokuPagu) {
                    // Create or update the pagu data for the realization record
                    MonitoringPagu::updateOrCreate(
                        [
                            'monitoring_anggaran_id' => $anggaran->id,
                            'kategori' => 1, // Pokok
                            'periode_id' => $pokuPagu->periode_id
                        ],
                        [
                            'dana' => $pokuPagu->dana
                        ]
                    );
                }
                
                // Get the pagu data categorized as parsial (category=2)
                $parsialPagu = MonitoringPagu::where('monitoring_anggaran_id', $rencanaAwalAnggaran->id)
                    ->where('kategori', 2) // Parsial
                    ->first();
            
                if ($parsialPagu) {
                    // Create or update the pagu data for the realization record
                    MonitoringPagu::updateOrCreate(
                        [
                            'monitoring_anggaran_id' => $anggaran->id,
                            'kategori' => 2, // Parsial
                            'periode_id' => $parsialPagu->periode_id
                        ],
                        [
                            'dana' => $parsialPagu->dana
                        ]
                    );
                }
                
                // Get the pagu data categorized as perubahan (category=3)
                $perubahanPagu = MonitoringPagu::where('monitoring_anggaran_id', $rencanaAwalAnggaran->id)
                    ->where('kategori', 3) // Perubahan
                    ->first();
                    
                if ($perubahanPagu) {
                    // Create or update the pagu data for the realization record
                    MonitoringPagu::updateOrCreate(
                        [
                            'monitoring_anggaran_id' => $anggaran->id,
                            'kategori' => 3, // Perubahan
                            'periode_id' => $perubahanPagu->periode_id
                        ],
                        [
                            'dana' => $perubahanPagu->dana
                        ]
                    );
                }
            }
        }
        
        // Try to find existing realisasi for this anggaran and period
        $realisasi = MonitoringRealisasi::firstOrCreate(
            [
                'monitoring_anggaran_id' => $anggaran->id,
                'periode_id' => $periodeTriwulan1->id
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

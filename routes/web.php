<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\BantuanController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\PanduanController;
use App\Http\Controllers\KodeNomenklaturController;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\TriwulanController;
use App\Http\Controllers\SkpdTugasController;
use App\Http\Controllers\PerangkatDaerahController;
use App\Http\Controllers\RencanaAwalController;
use App\Http\Controllers\ManajemenAnggaranController;
use App\Http\Controllers\LaporanAkhirController;


Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Middleware authentication
Route::middleware(['auth', 'verified'])->group(function () {

    // Resource Routes for various controllers
    Route::resource('kodenomenklatur', KodeNomenklaturController::class)->names('kodenomenklatur');
    Route::resource('bantuan', BantuanController::class)->names('bantuan');
    Route::resource('rencana-awal', MonitoringController::class)->names('monitoring');
    Route::prefix('manajemen-tim')->group(function () {
        Route::resource('usermanagement', UserManagementController::class)->names('usermanagement');
        Route::resource('perangkatdaerah', PerangkatDaerahController::class)->names('perangkatdaerah');
    });

    Route::prefix('monitoring')->group(function () {
        Route::resource('periode', PeriodeController::class)->names('periode');
        Route::put('/periode/{id}/status', [PeriodeController::class, 'updateStatus']);
        Route::post('/periode/generate', [PeriodeController::class, 'generate'])->name('periode.generate');
        Route::post('/periode/lanjutkanKeTahunBerikutnya', [PeriodeController::class, 'lanjutkanKeTahunBerikutnya'])->name('periode.lanjutkanKeTahunBerikutnya');
        Route::get('/periode-belum-selesai', [PeriodeController::class, 'getPeriodeBelumSelesai'])->name('periode.belum-selesai');
        Route::get('/periode-belum-selesai-data', [PeriodeController::class, 'getPeriodeBelumSelesaiData'])->name('periode.belum-selesai.data');
        Route::get('/periode-list', [PeriodeController::class, 'getAllPeriodes'])->name('periode.list');
        Route::get('/periode-aktif', [PeriodeController::class, 'getPeriodeAktif'])->name('periode.aktif');
    });
    // Route::resource('periode', PeriodeController::class)->names('periode');
    Route::resource('panduan', PanduanController::class)->names('panduan');


    // Menambahkan rute khusus untuk menyimpan sumber dana
    Route::post('/rencana-awal-anggaran-save', [ManajemenAnggaranController::class, 'saveSumberDana'])->name('monitoring-anggaran.save');
    // Route untuk memperbaiki data periode_id yang NULL
    Route::post('/manajemen-anggaran/fix-null-periode', [ManajemenAnggaranController::class, 'fixNullPeriodeId'])->name('manajemen-anggaran.fix-null-periode');

    Route::prefix('triwulan')->group(function () {
        Route::get('/{tid}', [TriwulanController::class, 'index'])->name('triwulan.index');
        Route::get('/{tid}/{id}', [TriwulanController::class, 'show'])->name('triwulan.show');
        Route::get('/{tid}/{id}/detail/{taskId}', [TriwulanController::class, 'showDetail'])->name('triwulan.detail');
        Route::post('/{tid}/save-realisasi', [TriwulanController::class, 'saveRealisasi'])->name('triwulan.save-realisasi');
        Route::get('/{tid}/akumulasi-kinerja/{skpdTugasId}/{tahun?}', [TriwulanController::class, 'getAkumulasiKinerjaTahunan'])->name('triwulan.akumulasi-kinerja');
        Route::get('/{tid}/{id}/monitoring-target', [TriwulanController::class, 'showMonitoringTarget'])->name('triwulan.monitoring-target');
        Route::get('/{tid}/{id}/perbandingan/{periode?}', [TriwulanController::class, 'showPerbandingan'])->name('triwulan.perbandingan');
    });

    // Legacy routes redirected to unified TriwulanController
    Route::redirect('/triwulan1', '/triwulan/1');
    Route::redirect('/triwulan2', '/triwulan/2');
    Route::redirect('/triwulan3', '/triwulan/3');
    Route::redirect('/triwulan4', '/triwulan/4');


    Route::resource('tugas', SkpdTugasController::class)->names('tugas');
    Route::resource('manajemenanggaran', ManajemenAnggaranController::class)->names('manajemenanggaran');

    // Route untuk halaman rencana awal
    Route::get('/monitoring/rencanaawal/{id}', [ManajemenAnggaranController::class, 'showRencanaAwal'])->name('manajemenanggaran.rencanaawal');

    // Route untuk halaman parsial
    Route::get('/manajemenanggaran/{id}/parsial', [ManajemenAnggaranController::class, 'showParsial'])->name('manajemenanggaran.show_partial');
        Route::post('/manajemenanggaran/open-parsial', [ManajemenAnggaranController::class, 'openParsial'])->name('manajemenanggaran.open-parsial');
    Route::post('/manajemenanggaran/enable-parsial', [ManajemenAnggaranController::class, 'enableParsialForUser'])->name('manajemenanggaran.enable-parsial');
    Route::post('/manajemenanggaran/save-parsial', [ManajemenAnggaranController::class, 'saveParsial'])->name('manajemenanggaran.save-parsial');

    // Route untuk halaman rencana awal parsial (detail view)
    Route::get('/monitoring/rencanaawal/{id}/parsial', [ManajemenAnggaranController::class, 'showRencanaAwalParsial'])->name('manajemenanggaran.rencanaawal.parsial');

    // Routes untuk perubahan anggaran (Triwulan 4)
    Route::post('/manajemenanggaran/enable-budget-change-all', [ManajemenAnggaranController::class, 'enableBudgetChangeForAll'])->name('manajemenanggaran.enable-budget-change-all');
    Route::get('/manajemenanggaran/{id}/budget-change', [ManajemenAnggaranController::class, 'showBudgetChange'])->name('manajemenanggaran.budget-change');
    Route::post('/manajemenanggaran/save-budget-change', [ManajemenAnggaranController::class, 'saveBudgetChange'])->name('manajemenanggaran.save-budget-change');
    
    // Debug route untuk cek data parsial dan budget change
    Route::get('/debug/anggaran/{skpd_tugas_id}', function($skpd_tugas_id) {
        $monitoring = \App\Models\Monitoring::where('skpd_tugas_id', $skpd_tugas_id)->latest()->first();
        if (!$monitoring) {
            return response()->json(['error' => 'No monitoring found for SKPD tugas ID: ' . $skpd_tugas_id]);
        }
        
        $monitoringAnggaran = \App\Models\MonitoringAnggaran::where('monitoring_id', $monitoring->id)
            ->with(['sumberAnggaran', 'pagu' => function($query) {
                $query->orderBy('kategori')->orderBy('periode_id');
            }])
            ->get();
        
        // Group data by kategori for easier analysis
        $groupedData = [
            'rencana_awal' => [],
            'parsial' => [],
            'budget_change' => []
        ];
        
        foreach ($monitoringAnggaran as $item) {
            $sumberAnggaranNama = $item->sumberAnggaran?->nama;
            foreach ($item->pagu as $pagu) {
                $kategoriName = match($pagu->kategori) {
                    1 => 'rencana_awal',
                    2 => 'parsial', 
                    3 => 'budget_change',
                    default => 'unknown'
                };
                
                if (!isset($groupedData[$kategoriName][$sumberAnggaranNama])) {
                    $groupedData[$kategoriName][$sumberAnggaranNama] = [];
                }
                
                $groupedData[$kategoriName][$sumberAnggaranNama][] = [
                    'id' => $pagu->id,
                    'periode_id' => $pagu->periode_id,
                    'dana' => $pagu->dana,
                    'created_at' => $pagu->created_at
                ];
            }
        }
            
        $result = [
            'monitoring_id' => $monitoring->id,
            'skpd_tugas_id' => $skpd_tugas_id,
            'summary' => [
                'rencana_awal_count' => collect($groupedData['rencana_awal'])->flatten(1)->count(),
                'parsial_count' => collect($groupedData['parsial'])->flatten(1)->count(),
                'budget_change_count' => collect($groupedData['budget_change'])->flatten(1)->count(),
            ],
            'grouped_data' => $groupedData,
            'raw_monitoring_anggaran' => $monitoringAnggaran->map(function($item) {
                return [
                    'id' => $item->id,
                    'sumber_anggaran' => $item->sumberAnggaran?->nama,
                    'pagu_all' => $item->pagu->map(function($pagu) {
                        return [
                            'id' => $pagu->id,
                            'kategori' => $pagu->kategori,
                            'periode_id' => $pagu->periode_id,
                            'dana' => $pagu->dana,
                            'kategori_name' => match($pagu->kategori) {
                                1 => 'Rencana Awal',
                                2 => 'Parsial',
                                3 => 'Budget Change',
                                default => 'Unknown'
                            }
                        ];
                    })
                ];
            })
        ];
        
        return response()->json($result, 200, [], JSON_PRETTY_PRINT);
    })->name('debug.anggaran');

    // Debug route khusus untuk Triwulan - analisis data targets dan realisasi
    Route::get('/debug/triwulan/{skpd_tugas_id}/{triwulan_id?}', function($skpd_tugas_id, $triwulan_id = null) {
        $result = [
            'skpd_tugas_id' => $skpd_tugas_id,
            'triwulan_filter' => $triwulan_id,
            'all_monitoring' => [],
            'targets_by_periode' => [],
            'realisasi_by_periode' => [],
            'pagu_aggregation' => []
        ];

        // Get all monitoring for this SKPD tugas
        $allMonitoring = \App\Models\Monitoring::where('skpd_tugas_id', $skpd_tugas_id)
            ->with([
                'periode.tahap', 
                'anggaran.sumberAnggaran',
                'anggaran.pagu',
                'anggaran.target.periode',
                'anggaran.realisasi.periode'
            ])
            ->get();

        foreach ($allMonitoring as $monitoring) {
            $monitoringData = [
                'id' => $monitoring->id,
                'periode' => $monitoring->periode ? [
                    'id' => $monitoring->periode->id,
                    'tahap' => $monitoring->periode->tahap->tahap ?? 'Unknown'
                ] : null,
                'deskripsi' => $monitoring->deskripsi,
                'anggaran_count' => $monitoring->anggaran->count(),
                'anggaran_details' => []
            ];

            foreach ($monitoring->anggaran as $anggaran) {
                $anggaranDetail = [
                    'id' => $anggaran->id,
                    'sumber_anggaran' => $anggaran->sumberAnggaran?->nama,
                    'pagu_data' => [],
                    'targets' => [],
                    'realisasi' => []
                ];

                // Process pagu data with aggregation
                $paguByKategori = [1 => 0, 2 => 0, 3 => 0];
                foreach ($anggaran->pagu as $pagu) {
                    $paguByKategori[$pagu->kategori] += $pagu->dana;
                    $anggaranDetail['pagu_data'][] = [
                        'id' => $pagu->id,
                        'kategori' => $pagu->kategori,
                        'kategori_name' => match($pagu->kategori) {
                            1 => 'Rencana Awal',
                            2 => 'Parsial',
                            3 => 'Budget Change',
                            default => 'Unknown'
                        },
                        'periode_id' => $pagu->periode_id,
                        'dana' => $pagu->dana
                    ];
                }

                $anggaranDetail['pagu_aggregated'] = [
                    'rencana_awal' => $paguByKategori[1],
                    'parsial' => $paguByKategori[2],
                    'budget_change' => $paguByKategori[3],
                    'total' => array_sum($paguByKategori)
                ];

                // Process targets
                foreach ($anggaran->target as $target) {
                    $targetData = [
                        'id' => $target->id,
                        'periode_id' => $target->periode_id,
                        'periode_tahap' => $target->periode->tahap->tahap ?? 'Unknown',
                        'kinerja_fisik' => $target->kinerja_fisik,
                        'keuangan' => $target->keuangan
                    ];
                    $anggaranDetail['targets'][] = $targetData;
                    
                    // Group by periode for analysis
                    $periodeKey = $target->periode_id;
                    if (!isset($result['targets_by_periode'][$periodeKey])) {
                        $result['targets_by_periode'][$periodeKey] = [
                            'periode_id' => $periodeKey,
                            'periode_tahap' => $target->periode->tahap->tahap ?? 'Unknown',
                            'targets' => []
                        ];
                    }
                    $result['targets_by_periode'][$periodeKey]['targets'][] = $targetData;
                }

                // Process realisasi
                foreach ($anggaran->realisasi as $realisasi) {
                    $realisasiData = [
                        'id' => $realisasi->id,
                        'periode_id' => $realisasi->periode_id,
                        'periode_tahap' => $realisasi->periode->tahap->tahap ?? 'Unknown',
                        'kinerja_fisik' => $realisasi->kinerja_fisik,
                        'keuangan' => $realisasi->keuangan
                    ];
                    $anggaranDetail['realisasi'][] = $realisasiData;
                    
                    // Group by periode for analysis
                    $periodeKey = $realisasi->periode_id;
                    if (!isset($result['realisasi_by_periode'][$periodeKey])) {
                        $result['realisasi_by_periode'][$periodeKey] = [
                            'periode_id' => $periodeKey,
                            'periode_tahap' => $realisasi->periode->tahap->tahap ?? 'Unknown',
                            'realisasi' => []
                        ];
                    }
                    $result['realisasi_by_periode'][$periodeKey]['realisasi'][] = $realisasiData;
                }

                $monitoringData['anggaran_details'][] = $anggaranDetail;
            }

            $result['all_monitoring'][] = $monitoringData;
        }

        // Summary
        $result['summary'] = [
            'total_monitoring' => count($result['all_monitoring']),
            'total_targets_periode' => count($result['targets_by_periode']),
            'total_realisasi_periode' => count($result['realisasi_by_periode']),
            'periode_with_data' => array_unique(array_merge(
                array_keys($result['targets_by_periode']),
                array_keys($result['realisasi_by_periode'])
            ))
        ];

        return response()->json($result, 200, [], JSON_PRETTY_PRINT);
    })->name('debug.triwulan');

    // Route untuk PDF Download
    Route::prefix('pdf')->group(function () {
        // Rencana Awal PDF
        Route::get('/rencana-awal/{tugasId}/form', [\App\Http\Controllers\PDFController::class, 'showRencanaAwalPdfForm'])->name('pdf.rencana-awal.form');
        Route::post('/rencana-awal/{tugasId}/generate', [\App\Http\Controllers\PDFController::class, 'generateRencanaAwalPdf'])->name('pdf.rencana-awal.generate');
        
        // Triwulan PDF
        Route::get('/triwulan/{tid}/{tugasId}/form', [\App\Http\Controllers\PDFController::class, 'showTriwulanPdfForm'])->name('pdf.triwulan.form');
        Route::post('/triwulan/{tid}/{tugasId}/generate', [\App\Http\Controllers\PDFController::class, 'generateTriwulanPdf'])->name('pdf.triwulan.generate');
    });

    // Route untuk Arsip Monitoring (dulu Laporan Akhir)
    Route::prefix('arsip-monitoring')->group(function () {
        Route::get('/', [LaporanAkhirController::class, 'index'])->name('arsip-monitoring.index');
        Route::get('/{id}', [LaporanAkhirController::class, 'show'])->name('arsip-monitoring.show');
        Route::get('/detail/{tugasId}', [LaporanAkhirController::class, 'detail'])->name('arsip-monitoring.detail');
        
        // Routes untuk file operations
        Route::post('/upload', [LaporanAkhirController::class, 'uploadArsip'])->name('arsip-monitoring.upload');
        Route::get('/download/{id}', [LaporanAkhirController::class, 'downloadArsip'])->name('arsip-monitoring.download');
        Route::get('/view/{id}', [LaporanAkhirController::class, 'viewArsip'])->name('arsip-monitoring.view');
        Route::delete('/delete/{id}', [LaporanAkhirController::class, 'deleteArsip'])->name('arsip-monitoring.delete');
        
        // Test route untuk debug storage - simple test
        Route::get('/test-file/{id}', function($id) {
            try {
                $arsip = \App\Models\ArsipMonitoring::findOrFail($id);
                
                $storage = \Illuminate\Support\Facades\Storage::disk('public');
                $exists = $storage->exists($arsip->path_file);
                $fullPath = storage_path('app/public/' . $arsip->path_file);
                
                if (!$exists) {
                    return response()->json([
                        'error' => 'File not found in storage',
                        'path_file' => $arsip->path_file,
                        'full_path' => $fullPath,
                        'file_exists_php' => file_exists($fullPath),
                        'storage_exists' => $exists
                    ], 404);
                }
                
                // Try to serve the file directly
                if (strtolower($arsip->tipe_file) === 'pdf') {
                    $fileContent = $storage->get($arsip->path_file);
                    
                    return response($fileContent, 200)
                        ->header('Content-Type', 'application/pdf')
                        ->header('Content-Disposition', 'inline; filename="' . $arsip->nama_file . '"');
                }
                
                return response()->json([
                    'message' => 'File found but not PDF',
                    'arsip' => $arsip,
                    'file_exists' => $exists
                ]);
                
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }
                 })->name('arsip-monitoring.test-file');
         
         // Alternative route for direct file access
         Route::get('/file/{id}', function($id) {
             $arsip = \App\Models\ArsipMonitoring::findOrFail($id);
             $filePath = storage_path('app/public/' . $arsip->path_file);
             
             if (!file_exists($filePath)) {
                 abort(404, 'File not found');
             }
             
             return response()->file($filePath, [
                 'Content-Type' => 'application/pdf',
                 'Content-Disposition' => 'inline; filename="' . $arsip->nama_file . '"'
             ]);
         })->name('arsip-monitoring.file');
        Route::get('/debug/{tugasId}', function($tugasId) {
            $currentYear = date('Y');
            
            // Get basic monitoring data
            $monitoring = \App\Models\Monitoring::where('skpd_tugas_id', $tugasId)
                ->where('tahun', $currentYear)
                ->with(['anggaran.sumberAnggaran', 'anggaran.pagu', 'anggaran.target', 'anggaran.realisasi'])
                ->get();
            
            // Get skpd tugas info
            $skpdTugas = \App\Models\SkpdTugas::with(['kodeNomenklatur', 'skpd'])->find($tugasId);
            
            // Count data in each table
            $counts = [
                'monitoring_total' => \App\Models\Monitoring::count(),
                'monitoring_current_year' => \App\Models\Monitoring::where('tahun', $currentYear)->count(),
                'monitoring_anggaran_total' => \App\Models\MonitoringAnggaran::count(),
                'monitoring_pagu_total' => \App\Models\MonitoringPagu::count(),
                'monitoring_target_total' => \App\Models\MonitoringTarget::count(),
                'monitoring_realisasi_total' => \App\Models\MonitoringRealisasi::count(),
                'sumber_anggaran_total' => \App\Models\SumberAnggaran::count(),
            ];
            
            // Get all sumber anggaran
            $sumberAnggaran = \App\Models\SumberAnggaran::all();
            
            return response()->json([
                'tugasId' => $tugasId,
                'currentYear' => $currentYear,
                'skpd_tugas' => $skpdTugas,
                'monitoring_count' => $monitoring->count(),
                'monitoring_data' => $monitoring->toArray(),
                'counts' => $counts,
                'sumber_anggaran' => $sumberAnggaran->toArray(),
                'message' => 'Debug data for troubleshooting'
            ]);
        })->name('laporan-akhir.debug');
        
        // Route untuk cek semua data monitoring secara umum
        Route::get('/debug-all/summary', function() {
            $currentYear = date('Y');
            
            // Get all monitoring with relations
            $allMonitoring = \App\Models\Monitoring::where('tahun', $currentYear)
                ->with(['anggaran.sumberAnggaran', 'anggaran.pagu', 'anggaran.target', 'anggaran.realisasi', 'tugas.kodeNomenklatur', 'tugas.skpd'])
                ->get();
            
            $summary = [
                'total_monitoring_records' => $allMonitoring->count(),
                'monitoring_with_anggaran' => $allMonitoring->filter(function($m) { return $m->anggaran->count() > 0; })->count(),
                'monitoring_with_pagu' => $allMonitoring->filter(function($m) { 
                    return $m->anggaran->filter(function($a) { return $a->pagu->count() > 0; })->count() > 0; 
                })->count(),
                'monitoring_with_target' => $allMonitoring->filter(function($m) { 
                    return $m->anggaran->filter(function($a) { return $a->target->count() > 0; })->count() > 0; 
                })->count(),
                'monitoring_with_realisasi' => $allMonitoring->filter(function($m) { 
                    return $m->anggaran->filter(function($a) { return $a->realisasi->count() > 0; })->count() > 0; 
                })->count(),
            ];
            
            return response()->json([
                'year' => $currentYear,
                'summary' => $summary,
                'sample_data' => $allMonitoring->take(3)->toArray()
            ]);
        })->name('laporan-akhir.debug-all');
        
        // Route untuk cek data yang diinput user
        Route::get('/check-user-data', function() {
            $stats = [
                'monitoring_count' => \App\Models\Monitoring::count(),
                'monitoring_anggaran_count' => \App\Models\MonitoringAnggaran::count(),
                'monitoring_pagu_count' => \App\Models\MonitoringPagu::count(),
                'monitoring_target_count' => \App\Models\MonitoringTarget::count(),
                'monitoring_realisasi_count' => \App\Models\MonitoringRealisasi::count(),
            ];
            
            // Get sample data jika ada
            $sampleMonitoring = \App\Models\Monitoring::with(['anggaran.sumberAnggaran', 'anggaran.pagu', 'anggaran.target', 'anggaran.realisasi'])
                ->take(3)
                ->get();
            
            return response()->json([
                'message' => 'Data check completed',
                'stats' => $stats,
                'sample_monitoring' => $sampleMonitoring->toArray(),
                'total_data_exist' => array_sum($stats) > 0
            ]);
        })->name('laporan-akhir.check-data');
    });


    // Route::get('/manajemen-tim/usermanagement/{user}/edit', [UserManagementController::class, 'edit'])->name('usermanagement.edit');
    // Route::put('/manajemen-tim/usermanagement/{user}', [UserManagementController::class, 'update'])->name('usermanagement.update');


    // Route::put('/monitoring/periode/{id}/status', [PeriodeController::class, 'updateStatus']);
    // Route::post('/monitoring/periode/generate', [PeriodeController::class, 'generate'])->name('periode.generate');
    // Route::post('/monitoring/periode/lanjutkanKeTahunBerikutnya', [PeriodeController::class, 'lanjutkanKeTahunBerikutnya'])->name('periode.lanjutkanKeTahunBerikutnya');
    // Route::get('/monitoring/periode-belum-selesai', [PeriodeController::class, 'getPeriodeBelumSelesai'])->name('periode.belum-selesai');
    // Route::get('/monitoring/periode-belum-selesai-data', [PeriodeController::class, 'getPeriodeBelumSelesaiData'])->name('periode.belum-selesai.data');
    // Route::get('/monitoring/periode-list', [PeriodeController::class, 'getAllPeriodes'])->name('periode.list');
    // Route::get('/monitoring/periode-aktif', [PeriodeController::class, 'getPeriodeAktif'])->name('periode.aktif');

    // Special routes for chat functionality in Bantuan
    Route::get('/bantuan/{bantuan}/chat', [BantuanController::class, 'chatForm'])->name('bantuan.chat');
    Route::post('/bantuan/{bantuan}/chat', [BantuanController::class, 'chatSend'])->name('bantuan.chat.send');
    Route::post('/bantuan/chat/selesai/{id}', [BantuanController::class, 'selesaikanChat'])->name('bantuan.chat.selesai');
    Route::post('/bantuan/update-status/{id}', [BantuanController::class, 'updateStatusToDiproses'])->name('bantuan.updateStatusToDiproses');


    // Redirect old routes to new consolidated routes
    Route::redirect('/rencana-awal/user/{id}', '/rencana-awal/{id}');
    Route::redirect('/rencana-awal/tugas/{id}', '/rencana-awal/rencanaawal/{id}');
    Route::get('/rencana-awal/rencanaawal/{id}', [ManajemenAnggaranController::class, 'showRencanaAwal'])->name('monitoring.rencanaawal');
    Route::post('/rencanaawal', [RencanaAwalController::class, 'saveMonitoringData'])->name('monitoring.save');
    // Rute dipindahkan di atas bersama dengan resource controller untuk menghindari konflik

    Route::post('/rencanaawal/save-monitoring', [RencanaAwalController::class, 'saveMonitoringData'])->name('rencanaawal.save-monitoring');
    Route::post('/rencanaawal/finalize', [RencanaAwalController::class, 'finalizeMonitoring'])
        ->name('rencanaawal.finalize');
    Route::post('/rencanaawal/finalize-row', [RencanaAwalController::class, 'finalizeRow'])->name('rencanaawal.finalize-row');
    Route::post('/rencanaawal/save-target', [RencanaAwalController::class, 'saveTarget'])->name('rencanaawal.save-target');


});

// Debug route untuk cek data anggaran
Route::get('/debug-data/{skpd_tugas_id}', function($skpd_tugas_id) {
    $monitoring = \App\Models\Monitoring::where('skpd_tugas_id', $skpd_tugas_id)
        ->where('tahun', date('Y'))
        ->first();

    if (!$monitoring) {
        return "No monitoring found for skpd_tugas_id: $skpd_tugas_id";
    }

    $monitoringAnggaran = \App\Models\MonitoringAnggaran::where('monitoring_id', $monitoring->id)
        ->with(['sumberAnggaran', 'pagu'])
        ->get();

    $result = "<h1>Debug Data for SKPD Tugas ID: $skpd_tugas_id</h1>";
    $result .= "<h2>Monitoring ID: {$monitoring->id}</h2>";
    $result .= "<h3>Monitoring Anggaran:</h3>";

    foreach ($monitoringAnggaran as $ma) {
        $result .= "<div style='border:1px solid #ccc; margin:10px; padding:10px;'>";
        $result .= "<h4>Sumber: {$ma->sumberAnggaran->nama}</h4>";
        $result .= "<p>Monitoring Anggaran ID: {$ma->id}</p>";

        foreach ($ma->pagu as $pagu) {
            $result .= "<p>- Pagu ID: {$pagu->id}, Periode: {$pagu->periode_id}, Kategori: {$pagu->kategori}, Dana: {$pagu->dana}</p>";
        }

        if ($ma->pagu->isEmpty()) {
            $result .= "<p><strong>No pagu data found!</strong></p>";
        }

        $result .= "</div>";
    }

    return $result;
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

// Debugging route
Route::get('/debug-skpd-data/{id}', function($id) {
    $tugas = \App\Models\SkpdTugas::with([
        'skpd.kepala.user.userDetail',
        'skpd.timKerja.operator.userDetail'
    ])->findOrFail($id);
    
    $data = [
        'tugas_id' => $tugas->id,
        'skpd_id' => $tugas->skpd_id,
        'skpd_nama' => $tugas->skpd->nama_skpd ?? 'NULL',
        'kepala_count' => $tugas->skpd->kepala->count(),
        'kepala_data' => $tugas->skpd->kepala->map(function($kepala) {
            return [
                'id' => $kepala->id,
                'is_aktif' => $kepala->is_aktif,
                'user_name' => $kepala->user ? $kepala->user->name : 'NULL',
                'user_nip' => $kepala->user && $kepala->user->userDetail ? $kepala->user->userDetail->nip : 'NULL'
            ];
        }),
        'tim_kerja_count' => $tugas->skpd->timKerja->count(),
        'tim_kerja_data' => $tugas->skpd->timKerja->map(function($timKerja) {
            return [
                'id' => $timKerja->id,
                'is_aktif' => $timKerja->is_aktif,
                'operator_name' => $timKerja->operator ? $timKerja->operator->name : 'NULL',
                'operator_nip' => $timKerja->operator && $timKerja->operator->userDetail ? $timKerja->operator->userDetail->nip : 'NULL'
            ];
        })
    ];
    
    return response()->json($data, 200, [], JSON_PRETTY_PRINT);
})->name('debug.skpd-data');

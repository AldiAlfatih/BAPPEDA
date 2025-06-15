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
    
    Route::prefix('triwulan')->group(function () {
        Route::get('/{tid}', [TriwulanController::class, 'index'])->name('triwulan.index');
        Route::get('/{tid}/{id}', [TriwulanController::class, 'show'])->name('triwulan.show');
        Route::get('/{tid}/{id}/detail/{taskId}', [TriwulanController::class, 'showDetail'])->name('triwulan.detail');
        Route::post('/{tid}/save-realisasi', [TriwulanController::class, 'saveRealisasi'])->name('triwulan.save-realisasi');
        Route::get('/{tid}/{id}/monitoring-target', [TriwulanController::class, 'showMonitoringTarget'])->name('triwulan.monitoring-target');
        Route::get('/{tid}/{id}/perbandingan/{periode?}', [TriwulanController::class, 'showPerbandingan'])->name('triwulan.perbandingan');
    });


    Route::resource('skpdtugas', SkpdTugasController::class)->names('skpdtugas');
    Route::resource('manajemenanggaran', ManajemenAnggaranController::class)->names('manajemenanggaran');
    

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


    Route::get('/rencana-awal/user/{id}', [MonitoringController::class, 'showUserMonitoring']);
    Route::get('/rencana-awal/tugas/{id}', [MonitoringController::class, 'showTugas']);
    Route::get('/rencana-awal/rencanaawal/{id}', [MonitoringController::class, 'showRencanaAwal'])->name('monitoring.rencanaawal');
    Route::post('/rencanaawal', [MonitoringController::class, 'saveMonitoringData'])->name('monitoring.save');
    // Rute dipindahkan di atas bersama dengan resource controller untuk menghindari konflik

    Route::post('/rencanaawal/save-monitoring', [RencanaAwalController::class, 'saveMonitoringData'])->name('rencanaawal.save-monitoring');
    Route::post('/rencanaawal/finalize', [RencanaAwalController::class, 'finalizeMonitoring'])
        ->name('rencanaawal.finalize');
    Route::post('/rencanaawal/finalize-row', [RencanaAwalController::class, 'finalizeRow'])->name('rencanaawal.finalize-row');
    Route::post('/rencanaawal/save-target', [RencanaAwalController::class, 'saveTarget'])->name('rencanaawal.save-target');





});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

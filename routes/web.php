<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\BantuanController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\PanduanController;
use App\Http\Controllers\KodeNomenklaturController;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\Triwulan1Controller;
use App\Http\Controllers\Triwulan2Controller;
use App\Http\Controllers\Triwulan3Controller;
use App\Http\Controllers\Triwulan4Controller;
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
    Route::resource('monitoring', MonitoringController::class)->names('monitoring');
    Route::resource('usermanagement', UserManagementController::class)->names('usermanagement');
    Route::resource('panduan', PanduanController::class)->names('panduan');
    Route::resource('periode', PeriodeController::class)->names('periode');
    Route::resource('manajemenanggaran', ManajemenAnggaranController::class)->names('manajemenanggaran');
    // Menambahkan rute khusus untuk menyimpan sumber dana
    Route::post('/monitoring-anggaran-save', [ManajemenAnggaranController::class, 'saveSumberDana'])->name('monitoring-anggaran.save');
    

    Route::resource('triwulan1', Triwulan1Controller::class)->names('triwulan1');
    Route::get('triwulan1/Detail/{id}', [Triwulan1Controller::class, 'showDetail'])->name('triwulan1.showDetail');
    Route::resource('triwulan2', Triwulan2Controller::class)->names('triwulan2');
    Route::get('triwulan2/Detail/{id}', [Triwulan2Controller::class, 'showDetail'])->name('triwulan2.showDetail');
    Route::resource('triwulan3', Triwulan3Controller::class)->names('triwulan3');
    Route::get('triwulan3/Detail/{id}', [Triwulan3Controller::class, 'showDetail'])->name('triwulan3.showDetail');
    Route::resource('triwulan4', Triwulan4Controller::class)->names('triwulan4');
    Route::get('triwulan4/Detail/{id}', [Triwulan4Controller::class, 'showDetail'])->name('triwulan4.showDetail');
    Route::resource('triwulan2', Triwulan2Controller::class)->names('triwulan2');
    Route::resource('triwulan3', Triwulan3Controller::class)->names('triwulan3');
    Route::resource('triwulan4', Triwulan4Controller::class)->names('triwulan4');
    Route::resource('skpdtugas', SkpdTugasController::class)->names('skpdtugas');
    Route::resource('perangkatdaerah', PerangkatDaerahController::class)->names('perangkatdaerah');

    // Route::get('/usermanagement/{user}/edit', [UserManagementController::class, 'edit'])->name('usermanagement.edit');
    // Route::put('/usermanagement/{user}', [UserManagementController::class, 'update'])->name('usermanagement.update');


    Route::put('/periode/{id}/status', [PeriodeController::class, 'updateStatus']);
    Route::post('/periode/generate', [PeriodeController::class, 'generate'])->name('periode.generate');
    Route::post('/periode/lanjutkanKeTahunBerikutnya', [PeriodeController::class, 'lanjutkanKeTahunBerikutnya'])->name('periode.lanjutkanKeTahunBerikutnya');
    Route::get('/periode-belum-selesai', [PeriodeController::class, 'getPeriodeBelumSelesai']);

    // Special routes for chat functionality in Bantuan
    Route::get('/bantuan/{bantuan}/chat', [BantuanController::class, 'chatForm'])->name('bantuan.chat');
    Route::post('/bantuan/{bantuan}/chat', [BantuanController::class, 'chatSend'])->name('bantuan.chat.send');
    Route::post('/bantuan/chat/selesai/{id}', [BantuanController::class, 'selesaikanChat'])->name('bantuan.chat.selesai');
    Route::post('/bantuan/update-status/{id}', [BantuanController::class, 'updateStatusToDiproses'])->name('bantuan.updateStatusToDiproses');


    Route::get('/monitoring/user/{id}', [MonitoringController::class, 'showUserMonitoring']);
    Route::get('/monitoring/tugas/{id}', [MonitoringController::class, 'showTugas']);
    Route::get('/monitoring/rencanaawal/{id}', [MonitoringController::class, 'showRencanaAwal'])->name('rencanaawal');
    Route::post('/rencanaawal', [MonitoringController::class, 'saveMonitoringData'])->name('monitoring.save');
    // Rute dipindahkan di atas bersama dengan resource controller untuk menghindari konflik

    Route::post('/rencanaawal/save-monitoring', [RencanaAwalController::class, 'saveMonitoringData'])->name('rencanaawal.save-monitoring');
    Route::post('/rencanaawal/finalize', [RencanaAwalController::class, 'finalizeMonitoring'])
        ->name('rencanaawal.finalize');
    Route::post('/rencanaawal/finalize-row', [RencanaAwalController::class, 'finalizeRow'])->name('rencanaawal.finalize-row');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

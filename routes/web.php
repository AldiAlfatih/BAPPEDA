<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\BantuanController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\PanduanController;
use App\Http\Controllers\KodeNomenklaturController;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\RencanaAwalController;
use App\Http\Controllers\Triwulan1Controller;

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
    Route::resource('rencanaawal', RencanaAwalController::class)->names('rencanaawal');
    Route::resource('triwulan1', Triwulan1Controller::class)->names('triwulan1');

    Route::put('/periode/{id}/status', [PeriodeController::class, 'updateStatus']);
    Route::post('/periode/generate', [PeriodeController::class, 'generate'])->name('periode.generate');
    Route::post('/periode/lanjutkanKeTahunBerikutnya', [PeriodeController::class, 'lanjutkanKeTahunBerikutnya'])->name('periode.lanjutkanKeTahunBerikutnya');
    Route::get('/periode-belum-selesai', [PeriodeController::class, 'getPeriodeBelumSelesai']);

    // Special routes for chat functionality in Bantuan
    Route::get('/bantuan/{bantuan}/chat', [BantuanController::class, 'chatForm'])->name('bantuan.chat');
    Route::post('/bantuan/{bantuan}/chat', [BantuanController::class, 'chatSend'])->name('bantuan.chat.send');
    Route::post('/bantuan/chat/selesai/{id}', [BantuanController::class, 'selesaikanChat'])->name('bantuan.chat.selesai');
    Route::post('/bantuan/update-status/{id}', [BantuanController::class, 'updateStatusToDiproses'])->name('bantuan.updateStatusToDiproses');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

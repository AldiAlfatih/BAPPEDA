<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\NomenklaturController;
use App\Http\Controllers\BantuanController;
use App\Http\Controllers\PemberitahuanController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\PanduanController;
use App\Http\Controllers\KodeNomenklaturController;
use App\Http\Controllers\BantuanFaqController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('kodenomenklatur', KodeNomenklaturController::class)->names('kodenomenklatur');
    Route::resource('bantuan', BantuanController::class)->names('bantuan');
    Route::resource('bantuanfaq', BantuanFaqController::class)->names('bantuanfaq');
    Route::resource('monitoring', MonitoringController::class)->names('monitoring');
    Route::resource('usermanagement', UserManagementController::class)->names('usermanagement');
    Route::resource('panduan', PanduanController::class)->names('panduan');
    Route::resource('pemberitahuan', PemberitahuanController::class)->names('pemberitahuan');

    // Routes khusus untuk fitur chat pada Bantuan
    Route::get('/bantuan/{bantuan}/chat', [BantuanController::class, 'chatForm'])->name('bantuan.chat');
    Route::post('/bantuan/{bantuan}/chat', [BantuanController::class, 'chatSend'])->name('bantuan.chat.send');
});



require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

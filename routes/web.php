<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\KodeNomenklaturController;
use App\Http\Controllers\BantuanController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\PanduanController;
use App\Http\Controllers\PerangkatDaerahController;
use App\Http\Controllers\SkpdTugasController;
use App\Http\Controllers\PemberitahuanController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::resource('kodenomenklatur', KodeNomenklaturController::class)->middleware(['auth', 'verified'])->names('kodenomenklatur');
Route::resource('bantuan', BantuanController::class)->middleware(['auth', 'verified'])->names('bantuan');
Route::resource('monitoring', MonitoringController::class)->middleware(['auth', 'verified'])->names('monitoring');
Route::resource('usermanagement', UserManagementController::class)->middleware(['auth', 'verified'])->names('usermanagement');
Route::resource('panduan', PanduanController::class)->middleware(['auth', 'verified'])->names('panduan');
Route::resource('perangkatdaerah', PerangkatDaerahCOntroller::class)->middleware(['auth', 'verified'])->names('perangkatdaerah');
Route::resource('pemberitahuan', PemberitahuanController::class)->middleware(['auth', 'verified'])->names('pemberitahuan');
Route::resource('skpdtugas', SkpdTugasController::class)->middleware(['auth', 'verified'])->names('skpdtugas');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

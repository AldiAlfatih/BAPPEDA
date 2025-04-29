<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\NomenklaturController;
use App\Http\Controllers\BantuanController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\UserManagementController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::resource('nomenklatur', NomenklaturController::class)->middleware(['auth', 'verified'])->names('nomenklatur');
Route::resource('bantuan', BantuanController::class)->middleware(['auth', 'verified'])->names('bantuan');
Route::resource('monitoring', MonitoringController::class)->middleware(['auth', 'verified'])->names('monitoring');
Route::resource('user_management', UserManagementController::class)->middleware(['auth', 'verified'])->names('user_management');
Route::resource('panduan', PanduanController::class)->middleware(['auth', 'verified'])->names('panduan');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

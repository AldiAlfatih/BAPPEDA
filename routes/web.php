<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\BantuanController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route::group(['middleware' => ['role:admin|role:perangkat_daerah']], function () {
//     Route::get('/program', [PermissionController::class, 'create']);
// });
Route::get('monitoring', function () {
    return Inertia::render('Monitoring');
})->middleware(['auth', 'verified'])->name('monitoring');

Route::get('pemberitahuan', function () {
    return Inertia::render('Pemberitahuan');
})->middleware(['auth', 'verified'])->name('pemberitahuan');

Route::get('bantuan', function () {
    return Inertia::render('Bantuan');
})->middleware(['auth', 'verified'])->name('bantuan');

Route::group(['middleware' => ['permission:create program']], function () {
    Route::get('/program/create', [PermissionController::class, 'create']);
});

// Menambahkan route untuk mengambil data
Route::get('/popup-data', [BantuanController::class, 'getDataForPopup']);
Route: 

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

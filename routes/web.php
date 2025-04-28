<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\NomenklaturController;
use App\Http\Controllers\BantuanController;
use App\Http\Controllers\MonitoringController;


Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route::get('nomenklatur', function () {
//     return Inertia::render('Nomenklatur');
// })->middleware(['auth', 'verified'])->name('nomeklatur');

// Route::get('monitoring', function () {
//     return Inertia::render('Monitoring');
// })->middleware(['auth', 'verified'])->name('monitoring');

Route::resource('nomenklatur', NomenklaturController::class)->middleware(['auth', 'verified'])->names('nomenklatur');
Route::resource('bantuan', BantuanController::class)->middleware(['auth', 'verified'])->names('bantuan');
Route::resource('monitoring', MonitoringController::class)->middleware(['auth', 'verified'])->names('monitoring');
// Route::get('/nomenklatur/edit/{id}', [NomenklaturController::class, 'edit'])->name('nomenklatur.edit');


// Route::get('bantuan', function () {
//     return Inertia::render('Bantuan');
// })->middleware(['auth', 'verified'])->name('bantuan');

// Route::get('/popup-data', [BantuanController::class, 'getDataForPopup']);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

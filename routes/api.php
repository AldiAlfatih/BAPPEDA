<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\DashboardController;
// use App\Http\Controllers\Api\NomenklaturController;
// use App\Http\Controllers\Api\BantuanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/login', [AuthenticatedSessionController::class, 'store']);

// Rute untuk mendapatkan periode aktif
// Route::get('/monitoring/periode-aktif', [PeriodeController::class, 'getPeriodeAktif']);

// Route untuk mendapatkan periode selesai
Route::get('/periode/selesai', [PeriodeController::class, 'getPeriodeSelesaiData']);

// Route untuk activity logging
Route::middleware(['auth'])->group(function () {
    Route::post('/log-activity', [DashboardController::class, 'logActivity'])
        ->name('api.log-activity');
});

// Route untuk selective PDF download
Route::middleware(['auth'])->group(function () {
    Route::get('/triwulan/{tid}/tugas/{tugasId}/sumber-dana', [App\Http\Controllers\TriwulanController::class, 'getAvailableSumberDana'])
        ->name('api.triwulan.sumber-dana');
    Route::post('/triwulan/{tid}/tugas/{tugasId}/preview', [App\Http\Controllers\TriwulanController::class, 'previewSelectiveData'])
        ->name('api.triwulan.preview');
});

// Routes untuk Nomenklatur (CRUD)
// Route::middleware(['auth:sanctum'])->group(function () {
//     // Menampilkan semua data nomenklatur
//     Route::get('/nomenklatur', [NomenklaturController::class, 'index'])
//         ->middleware('permission:view nomenklatur')
//         ->name('api.nomenklatur.index');

//     // Menambahkan nomenklatur baru
//     Route::post('/nomenklatur', [NomenklaturController::class, 'store'])
//         ->middleware('role:admin|perangkat_daerah')
//         ->name('api.nomenklatur.store');

//     // Mengupdate nomenklatur yang ada
//     Route::put('/nomenklatur/{post}', [NomenklaturController::class, 'update'])
//         ->middleware('role:admin|perangkat_daerah')
//         ->name('api.nomenklatur.update');

//     // Menghapus nomenklatur
//     Route::delete('/nomenklatur/{post}', [NomenklaturController::class, 'destroy'])
//         ->middleware('role:admin|perangkat_daerah')
//         ->name('api.nomenklatur.destroy');

//     // Menampilkan detail nomenklatur berdasarkan ID
//     Route::get('/nomenklatur/{post}', [NomenklaturController::class, 'show'])
//         ->middleware('role:super_admin|admin|operator|perangkat_daerah')
//         ->name('api.nomenklatur.show');
// });

// Route::middleware(['auth:sanctum'])->group(function () {
//     Route::get('/bantuan', [BantuanController::class, 'index'])
//         ->middleware('permission:view bantuan')
//         ->name('api.bantuan.index');
//     Route::post('/bantuan', [BantuanController::class, 'store'])
//         ->middleware('role:admin')
//         ->name('api.bantuan.store');
//     Route::put('/bantuan/{post}', [BantuanController::class, 'update'])
//         ->middleware('role:admin')
//         ->name('api.bantuan.update');
//     Route::delete('/bantuan/{post}', [BantuanController::class, 'destroy'])
//         ->middleware('role:admin')
//         ->name('api.bantuan.destroy');
// });

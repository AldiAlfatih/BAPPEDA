<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProgramController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

<<<<<<< HEAD
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthenticatedSessionController::class, 'store']);
// Route::post('/programs', [ProgramController::class, 'store'])->middleware('auth:sanctum');
=======
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/login', [AuthenticatedSessionController::class, 'store']);

// Route::post('/program', [ProgramController::class, 'store'])->middleware('auth:sanctum');
>>>>>>> bd447f1 (database pertama)

Route::middleware(['auth:sanctum'])->group(function() {
    Route::get('/program', [ProgramController::class, 'index'])->middleware('permission:view program');
    Route::post('/program', [ProgramController::class, 'store'])->middleware('role:admin|perangkat_daerah');
    Route::put('/program/{post}', [ProgramController::class, 'update'])->middleware('role:admin|perangkat_daerah');
    Route::delete('/program/{post}', [ProgramController::class, 'destroy'])->middleware('role:admin|perangkat_daerah');
    Route::get('/program/{post}', [ProgramController::class, 'show'])->middleware('role:super_admin|admin|operator|perangkat_daerah');
<<<<<<< HEAD
=======
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/kegiatan', [KegiatanCOntroller::class, 'index'])->middleware('permission:view kegiatan');
    Route::post('/kegiatan', [KegiatanController::class, 'store'])->middleware('role:admin|perangkat_daerah');
    Route::put('/kegiatan/{post}', [KegiatanController::class, 'update'])->middleware('role:admin|perangkat_daerah');
    Route::delete('/kegiatan/{post}', [KegiatanController::class, 'destroy'])->middleware('role:admin|perangkat_daerah');
    Route::get('/kegiatan/{post}', [KegiatanController::class, 'show'])->middleware('role:super_admin|admin|operator|perangkat_daerah');
>>>>>>> bd447f1 (database pertama)
});
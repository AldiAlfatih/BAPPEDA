<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProgramController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthenticatedSessionController::class, 'store']);
// Route::post('/programs', [ProgramController::class, 'store'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function() {
    Route::get('/program', [ProgramController::class, 'index'])->middleware('permission:view program');
    Route::post('/program', [ProgramController::class, 'store'])->middleware('permission:create program');
    Route::put('/program/{post}', [ProgramController::class, 'update'])->middleware('role:admin|perangkat_daerah');
    Route::delete('/program/{post}', [ProgramController::class, 'destroy'])->middleware('role:admin|perangkat_daerah');
    Route::get('/program/{post}', [ProgramController::class, 'show'])->middleware('role:super_admin|admin|operator|perangkat_daerah');
});
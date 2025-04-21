<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

<<<<<<< HEAD
=======


>>>>>>> bd447f1 (database pertama)
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route::group(['middleware' => ['role:admin|role:perangkat_daerah']], function () {
//     Route::get('/program', [PermissionController::class, 'create']);
// });

Route::group(['middleware' => ['permission:create program']], function () {
<<<<<<< HEAD
    Route::get('/posts/create', [PermissionController::class, 'create']);
=======
    Route::get('/program/create', [PermissionController::class, 'create']);
>>>>>>> bd447f1 (database pertama)
});

Route: 

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

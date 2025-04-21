<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    Route::get('/posts/create', [PermissionController::class, 'create']);
});

Route: 

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

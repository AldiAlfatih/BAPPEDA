<?php

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', '/settings/profiledetail');

    // Profile utama
    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::match(['patch', 'put'], 'settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Tambahan: Profile Detail
    Route::middleware(['auth'])->group(function () {
    Route::get('/settings/profiledetail', [ProfileController::class, 'editDetail'])->name('profile.detail');
    Route::put('/settings/profiledetail', [ProfileController::class, 'updateDetail'])->name('profile.detail.update');
});


    // Password
    Route::get('settings/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('settings/password', [PasswordController::class, 'update'])->name('password.update');

    // Appearance
    Route::get('settings/appearance', function () {
        return Inertia::render('settings/Appearance');
    })->name('appearance.edit');
});


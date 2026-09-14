<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\FamilyController;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);


Route::middleware(['auth'])->group(function () {
    Route::get('/family/setup', [FamilyController::class, 'showSetup']);
    Route::post('/family/create', [FamilyController::class, 'store'])->name('family.create');
    Route::post('/family/join', [FamilyController::class, 'join'])->name('family.join');

    Route::get('/dashboard', function () {
        return inertia('Dashboard');
    })->name('dashboard');
});


require __DIR__ . '/settings.php';

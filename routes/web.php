<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\DashboardController;

Route::inertia('/', 'Welcome')->name('home');

Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);


Route::middleware('auth')->group(function () {
    Route::get('/family/setup', [FamilyController::class, 'showSetup'])->name('family.setup');
    Route::post('/family/create', [FamilyController::class, 'store'])->name('family.create');
    Route::post('/family/join', [FamilyController::class, 'join'])->name('family.join');
});
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

require __DIR__ . '/settings.php';

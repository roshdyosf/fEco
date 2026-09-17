<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;


Route::inertia('/', 'Welcome')->name('home');

// Authentication routes
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');


// Google OAuth routes
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// Family routes
Route::middleware('auth')->group(function () {
    Route::get('/family/setup', [FamilyController::class, 'showSetup'])->name('family.setup');
    Route::post('/family/create', [FamilyController::class, 'store'])->name('family.create');
    Route::post('/family/join', [FamilyController::class, 'join'])->name('family.join');
});

// Transaction routes
Route::middleware('auth')->group(function () {
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index')->can('view-transactions');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
});


require __DIR__ . '/settings.php';

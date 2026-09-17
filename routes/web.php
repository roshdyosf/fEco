<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

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
    Route::post('/family/leave', [FamilyController::class, 'leave'])->name('family.leave');
    Route::delete('/family', [FamilyController::class, 'destroy'])->name('family.destroy');
    Route::delete('/family/member/{member}', [FamilyController::class, 'removeMember'])->name('family.member.destroy');
});

// Transaction routes
Route::middleware('auth')->group(function () {
    Route::get('/transactions', [TransactionController::class, 'index'])
        ->name('transactions.index');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});

require __DIR__.'/settings.php';

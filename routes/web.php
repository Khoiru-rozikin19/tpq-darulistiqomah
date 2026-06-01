<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SantriController;
use App\Http\Controllers\SppController;
use App\Http\Controllers\KasController;
use App\Http\Controllers\PegawaiController;

// Redirect root to dashboard/login
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard & CRUD modules (Protected by auth middleware)
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Santri CRUD
    Route::resource('santri', SantriController::class);

    // SPP Payments CRUD
    Route::resource('spp', SppController::class)->except(['edit', 'update']);

    // KAS Flow CRUD
    Route::resource('kas', KasController::class)->except(['show']);

    // Pegawai CRUD
    Route::resource('pegawai', PegawaiController::class);
});

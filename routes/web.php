<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\AbsensiController;   // 1. Impor AbsensiController
use App\Http\Controllers\Web\DashboardController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Rute-rute ini dimuat oleh RouteServiceProvider dalam grup yang
| berisi middleware "web". Rute-rute ini menggunakan sesi.
|
*/

// Rute untuk halaman selamat datang (halaman utama sebelum login)
Route::get('/', function () {
    // Arahkan ke halaman login jika pengguna belum terautentikasi
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return Inertia::render('Auth/Login', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

// Rute /dashboard yang diarahkan ke DashboardController
// Ini memastikan data peran pengguna dikirim ke frontend.
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// 2. Tambahkan rute untuk menampilkan halaman Absensi GPS
Route::get('/absen/create', [AbsensiController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('absen.create');

// Rute standar Breeze untuk manajemen profil pengguna
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Memuat rute-rute autentikasi dari Breeze (login, register, dll.)
require __DIR__.'/auth.php';


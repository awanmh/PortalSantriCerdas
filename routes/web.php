<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\AbsensiController;
use App\Http\Controllers\Web\CatatanPelanggaranController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\JurusanController;
use App\Http\Controllers\Web\KelasController;
use App\Http\Controllers\Web\JadwalController;
use App\Http\Controllers\Web\LaporanAbsensiController;
use App\Http\Controllers\Web\LiveMapController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\ZonaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

/**
 * Rute Halaman Utama (Publik)
 */
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : Inertia::render('Welcome');
})->name('home');

/**
 * Grup rute yang memerlukan otentikasi dan verifikasi email.
 */
Route::middleware(['auth', 'verified'])->group(function () {

    // --- RUTE UMUM PENGGUNA ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::post('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // --- RUTE SPESIFIK SISWA ---
    Route::middleware('role:siswa')->prefix('absen/{jadwal}')->name('absen.')->group(function () {
        Route::get('/create', [AbsensiController::class, 'create'])->name('create');
        Route::post('/', [AbsensiController::class, 'store'])->name('store');
    });

    // --- RUTE UMUM UNTUK GURU, BK, & IT ---
    Route::resource('catatan-pelanggaran', CatatanPelanggaranController::class)
        ->except(['show'])
        ->middleware('role:guru|bk|it');

    Route::get('/laporan/absensi', [LaporanAbsensiController::class, 'index'])
        ->name('laporan.absensi.index')
        ->middleware('can:view laporan absensi');

    Route::get('/live-map', [LiveMapController::class, 'index'])
        ->name('live-map.index')
        ->middleware('role:guru|bk|it');

    // --- GRUP RUTE KHUSUS ADMIN / IT ---
    Route::prefix('admin')->name('admin.')->middleware('role:it')->group(function() {
        Route::resource('users', UserController::class)->except(['show'])->middleware('can:manage users');
        Route::resource('zona', ZonaController::class)->only(['index', 'store', 'update', 'destroy'])->middleware('can:manage zona');
        Route::resource('jurusan', JurusanController::class)->except(['show', 'create', 'edit'])->middleware('can:manage jurusan');
        Route::resource('kelas', KelasController::class)->except(['show', 'create', 'edit'])->middleware('can:manage kelas');
        Route::resource('jadwal', JadwalController::class)->except(['show', 'create', 'edit'])->middleware('can:manage jadwal');
            
        // Laporan Absensi (versi Admin dengan nama rute yang unik)
        Route::get('/laporan/absensi', [LaporanAbsensiController::class, 'index'])
            ->name('admin.laporan.absensi.index') // <-- NAMA DIPERBAIKI
            ->middleware('can:view laporan absensi');
    });
});

/**
 * Memuat rute-rute otentikasi dari file terpisah.
 */
require __DIR__.'/auth.php';


<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProfileController;

// --- Controller untuk Role Siswa ---
use App\Http\Controllers\Web\AbsensiController;

// --- Controller untuk Role Guru, BK, IT ---
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\CatatanPelanggaranController;
use App\Http\Controllers\Web\LaporanAbsensiController;
use App\Http\Controllers\Web\LiveMapController;
use App\Http\Controllers\Web\AbsensiSiswaController;

// --- Controller Khusus Admin/IT ---
use App\Http\Controllers\Web\JurusanController;
use App\Http\Controllers\Web\KelasController;
use App\Http\Controllers\Web\JadwalController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\ZonaController;
use App\Http\Controllers\Web\KelasSiswaController;

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
        Route::match(['post', 'patch'], '/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // --- RUTE SPESIFIK SISWA ---
    Route::middleware('role:siswa')->prefix('absen/{jadwal}')->name('absen.')->group(function () {
        Route::get('/create', [AbsensiController::class, 'create'])->name('create');
        Route::post('/', [AbsensiController::class, 'store'])->name('store');
    });

    // --- RUTE LAPORAN ABSENSI (Hanya untuk BK) ---
    // URL: /laporan/absensi
    Route::get('/laporan/absensi', [LaporanAbsensiController::class, 'adminIndex'])
        ->name('laporan.absensi.index')
        ->middleware('role:bk'); // Hanya untuk BK, tidak ada prefix 'admin'


    // --- RUTE CETAK ABSENSI SISWA (Hanya untuk Guru) ---
    Route::prefix('guru/absensi-siswa')->name('guru.absensi.siswa.')->middleware('role:guru')->group(function () {
        Route::get('/', [AbsensiSiswaController::class, 'index'])->name('index');
        // --- PERBAIKAN DI SINI ---
        Route::get('/data-kelas/{jadwal}', [AbsensiSiswaController::class, 'getDataForAbsensi'])->name('data');
        Route::post('/store', [AbsensiSiswaController::class, 'store'])->name('store');
    });

    // --- RUTE UMUM UNTUK GURU, BK, & IT ---
    Route::resource('catatan-pelanggaran', CatatanPelanggaranController::class)
        ->except(['show'])
        ->middleware('role:guru|bk|it');

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

        // --- RUTE UNTUK MENGELOLA SISWA DI DALAM KELAS ---
        Route::prefix('kelas/{kelas}')->name('kelas.')->group(function () {
            Route::get('/manage-siswa', [KelasSiswaController::class, 'index'])->name('manage-siswa');
            Route::post('/assign-siswa', [KelasSiswaController::class, 'assign'])->name('assign-siswa');
            Route::delete('/detach-siswa/{siswa}', [KelasSiswaController::class, 'detach'])->name('detach-siswa');
        });

        // --- RUTE LAPORAN ABSENSI ADMIN/IT ---
        // URL: /admin/laporan/absensi
        Route::get('/laporan/absensi', [LaporanAbsensiController::class, 'adminIndex'])
            ->name('laporan.absensi.index') // Nama lengkapnya: 'admin.laporan.absensi.index'
            ->middleware('can:view laporan absensi'); // Hanya untuk IT
    });
});

/**
 * Memuat rute-rute otentikasi dari file terpisah.
 */
require __DIR__.'/auth.php';
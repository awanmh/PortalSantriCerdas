<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CatatanPelanggaranController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\AbsensiGuruController;
use App\Http\Controllers\AbsensiSiswaController;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth','role:guru'])->group(function () {
    Route::get('/guru/pelanggaran', [CatatanPelanggaranController::class, 'create'])->name('pelanggaran.create');
    Route::post('/guru/pelanggaran', [CatatanPelanggaranController::class, 'store'])->name('pelanggaran.store');
});

Route::middleware(['auth','role:guru_bk'])->group(function () {
    Route::get('/guru-bk/pelanggaran', [CatatanPelanggaranController::class, 'index'])->name('pelanggaran.index');
});

Route::middleware(['auth','role:guru'])->group(function(){
    Route::resource('jadwal',JadwalController::class);
});

Route::middleware(['auth','role:guru'])->group(function () {
    // absensi guru
    Route::post('/guru/absensi', [AbsensiGuruController::class, 'store'])->name('guru.absensi.store');
});

Route::middleware(['auth','role:siswa'])->group(function () {
    // absensi siswa
    Route::post('/siswa/absensi', [AbsensiSiswaController::class, 'store'])->name('siswa.absensi.store');
});

require __DIR__ . '/auth.php';
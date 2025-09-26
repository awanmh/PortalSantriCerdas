<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\Api\Auth\ApiLoginController;
use App\Http\Controllers\Api\AbsensiGuruController;
use App\Http\Controllers\Api\AbsensiSiswaController;
use App\Http\Controllers\Api\CatatanPelanggaranController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\JadwalController;
use App\Http\Controllers\Api\KelasController;
use App\Http\Controllers\Api\LiveAbsensiController;
use App\Http\Controllers\Api\LiveTrackingController; // <-- Pastikan ini di-import
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\UserManagementController;
use App\Http\Controllers\Api\ZonaController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 🔹 Login (Public)
Route::post('/login', [ApiLoginController::class, 'store'])->name('api.login');

// 🔹 Protected Routes (Require Auth)
Route::middleware('auth:sanctum')->group(function () {

    // Auth Info & Logout
    Route::post('/logout', [ApiLoginController::class, 'logout'])->name('api.logout');
    Route::get('/user', fn (Request $request) => response()->json([
        'id'    => $request->user()->id,
        'name'  => $request->user()->name,
        'email' => $request->user()->email,
        'roles' => $request->user()->getRoleNames(),
    ]))->name('api.user');

    // 🔹 PROFILE API (GET, POST, PATCH, DELETE)
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('api.profile.edit');
        Route::match(['post', 'patch'], '/', [ProfileController::class, 'update'])->name('api.profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('api.profile.destroy');
    });

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('api.dashboard');

    // 🔹 Absensi Guru
    Route::prefix('absen-guru')->name('api.absen.guru.')->middleware('role:guru')->group(function () {
        Route::post('/store', [AbsensiGuruController::class, 'store'])->name('store');
    });

    // 🔹 Absensi Siswa
    Route::post('/absen-siswa', [AbsensiSiswaController::class, 'store'])
        ->middleware('role:siswa')
        ->name('api.absen.siswa');

    // 🔹 Live Tracking
    Route::prefix('live')->name('api.live.')->group(function () {
        Route::post('/sesi/mulai', [LiveAbsensiController::class, 'mulaiSesi'])
            ->middleware('role:guru|bk|it')
            ->name('sesi.mulai');

        // --- PERBAIKAN DI SINI ---
        // Hapus 'auth:sanctum' yang berlebihan dari sini
        Route::post('/lokasi/update', [LiveTrackingController::class, 'updateLokasi'])
            ->middleware('role:siswa') // <-- HANYA periksa rolenya
            ->name('lokasi.update');
    });

    // 🔹 Catatan Pelanggaran (Guru & BK)
    Route::apiResource('catatan-pelanggaran', CatatanPelanggaranController::class)
        ->names('api.catatan')
        ->middleware('role:guru|bk');

    // 🔹 Jadwal
    Route::get('/jadwal', [JadwalController::class, 'index'])
        ->middleware('role:guru|it')
        ->name('api.jadwal.index');

    // 🔹 Export Data
    Route::prefix('export')->name('api.export.')->middleware('role:guru|bk|it')->group(function () {
        Route::get('/absensi/siswa', [ExportController::class, 'exportAbsensiSiswa'])->name('absensi.siswa');
        Route::get('/absensi/siswa/pdf', [ExportController::class, 'exportAbsensiSiswaPdf'])->name('absensi.siswa.pdf');
        Route::get('/pelanggaran', [ExportController::class, 'exportPelanggaran'])->name('pelanggaran');
        Route::get('/jadwal', [ExportController::class, 'exportJadwal'])->name('jadwal');
    });

    // 🔹 Notifikasi
    Route::prefix('notifications')->name('api.notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/unread', [NotificationController::class, 'unread'])->name('unread');
        Route::post('/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('readAll');
    });

    // 🔹 Manajemen Khusus IT
    Route::middleware('role:it')->group(function () {
        // Resources
        Route::apiResources([
            'zona'  => ZonaController::class,
            'kelas' => KelasController::class,
            'users' => UserManagementController::class,
            'jadwal'=> JadwalController::class,
        ], [
            'names' => [
                'zona'  => 'api.zona',
                'kelas' => 'api.kelas',
                'users' => 'api.users',
                'jadwal'=> 'api.jadwal',
            ],
        ]);

        // Users tambahan (tanpa store)
        Route::apiResource('users', UserManagementController::class)
            ->except(['store'])
            ->names('api.users');

        // Relasi Kelas - Siswa
        Route::post('/kelas/{kelas}/siswa', [KelasController::class, 'addSiswa'])->name('api.kelas.siswa.add');
        Route::delete('/kelas/{kelas}/siswa/{user}', [KelasController::class, 'removeSiswa'])->name('api.kelas.siswa.remove');

        // User Management Extra
        Route::prefix('users')->name('api.users.')->group(function () {
            Route::put('/{user}/role', [UserManagementController::class, 'updateRole'])->name('updateRole');
            Route::put('/{user}/kelas', [UserManagementController::class, 'updateKelas'])->name('updateKelas');
            Route::post('/{user}/foto', [UserManagementController::class, 'uploadProfilePicture'])->name('uploadFoto');
            Route::post('/import', [UserManagementController::class, 'importUsers'])->name('import');
            Route::get('/export', [UserManagementController::class, 'exportUsers'])->name('export');
        });
    });
});

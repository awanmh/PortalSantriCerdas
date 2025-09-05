<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\ApiLoginController;
use App\Http\Controllers\Api\AbsensiSiswaController;
use App\Http\Controllers\Api\AbsensiGuruController;
use App\Http\Controllers\Api\CatatanPelanggaranController;
use App\Http\Controllers\Api\JadwalController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ZonaController;
use App\Http\Controllers\Api\KelasController;
use App\Http\Controllers\Api\UserManagementController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\LiveAbsensiController;
use Spatie\Permission\Middleware\RoleMiddleware;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Ini adalah file routing final yang sudah stabil dan bersih.
| Rute-rute dikelompokkan berdasarkan fungsionalitas dan middleware.
*/

// --- AUTHENTICATION (Rute Publik) ---
Route::post('/login', [ApiLoginController::class, 'store']);

// --- GRUP RUTE YANG MEMERLUKAN OTENTIKASI ---
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [ApiLoginController::class, 'destroy']);

    // --- USER INFO ---
    Route::get('/user', function (Request $request) {
        return response()->json([
            'id'    => $request->user()->id,
            'name'  => $request->user()->name,
            'email' => $request->user()->email,
            'roles' => $request->user()->getRoleNames(),
        ]);
    });

    // --- DASHBOARD ---
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(RoleMiddleware::class . ':siswa|guru|bk|it')
        ->name('api.dashboard'); // Diberi nama untuk Ziggy

    // --- ABSENSI ---
    Route::prefix('absen')->group(function () {
        Route::post('/siswa', [AbsensiSiswaController::class, 'absenSiswa'])
            ->middleware(RoleMiddleware::class . ':siswa');
        Route::post('/guru/masuk', [AbsensiGuruController::class, 'absenMasuk'])
            ->middleware(RoleMiddleware::class . ':guru');
        Route::post('/guru/pulang', [AbsensiGuruController::class, 'absenPulang'])
            ->middleware(RoleMiddleware::class . ':guru');
    });

    // --- LIVE ABSENSI (REAL-TIME) ---
    Route::prefix('live')->group(function () {
        Route::post('/sesi/mulai', [LiveAbsensiController::class, 'mulaiSesi'])
            ->middleware(RoleMiddleware::class . ':guru|it|bk');
        Route::post('/lokasi/update', [LiveAbsensiController::class, 'updateLokasi'])
            ->middleware(RoleMiddleware::class . ':siswa');
    });

    // --- FITUR UMUM (GURU, BK, IT) ---
    Route::apiResource('catatan-pelanggaran', CatatanPelanggaranController::class)
         ->middleware(RoleMiddleware::class . ':guru|bk');

    Route::get('/jadwal', [JadwalController::class, 'index'])
         ->middleware(RoleMiddleware::class . ':guru|it');

    Route::prefix('export')->middleware(RoleMiddleware::class . ':it|guru|bk')->group(function () {
        Route::get('/absensi/siswa', [ExportController::class, 'exportAbsensiSiswa']);
        Route::get('/absensi/siswa/pdf', [ExportController::class, 'exportAbsensiSiswaPdf']);
        Route::get('/pelanggaran', [ExportController::class, 'exportPelanggaran']);
        Route::get('/jadwal', [ExportController::class, 'exportJadwal']);
    });

    // --- FITUR MANAJEMEN (HANYA IT) ---
    Route::middleware(RoleMiddleware::class . ':it')->group(function () {
        Route::apiResource('zona', ZonaController::class);
        Route::apiResource('kelas', KelasController::class);
        Route::apiResource('users', UserManagementController::class)->except(['store']);
        Route::apiResource('jadwal', JadwalController::class)->except(['index']);

        Route::post('/kelas/{kelas}/siswa', [KelasController::class, 'addSiswa']);
        Route::delete('/kelas/{kelas}/siswa/{user}', [KelasController::class, 'removeSiswa']);

        Route::put('/users/{user}/role', [UserManagementController::class, 'updateRole']);
        Route::put('/users/{user}/kelas', [UserManagementController::class, 'updateKelas']);
        Route::post('/users/{user}/foto', [UserManagementController::class, 'uploadProfilePicture']);

        Route::post('/users/import', [UserManagementController::class, 'importUsers']);
        Route::get('/users/export', [UserManagementController::class, 'exportUsers']);
    });

    // --- NOTIFIKASI ---
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread', [NotificationController::class, 'unread']);
        Route::post('/{notification}/mark-as-read', [NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);
    });
});
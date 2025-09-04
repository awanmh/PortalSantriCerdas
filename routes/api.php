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

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Ini adalah file routing final yang sudah stabil dan bersih.
*/

// --- AUTHENTICATION ---
Route::post('/login', [ApiLoginController::class, 'store']);
Route::post('/logout', [ApiLoginController::class, 'destroy'])->middleware('auth:sanctum');

// --- USER INFO ---
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'id'    => $request->user()->id,
        'name'  => $request->user()->name,
        'email' => $request->user()->email,
        'roles' => $request->user()->getRoleNames(),
    ]);
});

// --- ABSENSI (SISWA) ---
Route::middleware(['auth:sanctum', \Spatie\Permission\Middleware\RoleMiddleware::class . ':siswa'])->group(function () {
    Route::post('/absen/siswa', [AbsensiSiswaController::class, 'absenSiswa']);
});

// --- ABSENSI (GURU) ---
Route::middleware(['auth:sanctum', \Spatie\Permission\Middleware\RoleMiddleware::class . ':guru'])->group(function () {
    Route::post('/absen/guru/masuk', [AbsensiGuruController::class, 'absenMasuk']);
    Route::post('/absen/guru/pulang', [AbsensiGuruController::class, 'absenPulang']);
});

// --- CATATAN PELANGGARAN ---
Route::middleware(['auth:sanctum', \Spatie\Permission\Middleware\RoleMiddleware::class . ':guru|bk'])->group(function () {
    Route::apiResource('catatan-pelanggaran', CatatanPelanggaranController::class);
});

// --- JADWAL PELAJARAN ---
Route::middleware(['auth:sanctum', \Spatie\Permission\Middleware\RoleMiddleware::class . ':guru|it'])->group(function () {
    Route::get('/jadwal', [JadwalController::class, 'index']);
    Route::middleware(\Spatie\Permission\Middleware\RoleMiddleware::class . ':it')->group(function () {
        Route::post('/jadwal', [JadwalController::class, 'store']);
        Route::put('/jadwal/{id}', [JadwalController::class, 'update']);
        Route::delete('/jadwal/{id}', [JadwalController::class, 'destroy']);
    });
});

// --- MANAJEMEN ZONA (IT) ---
Route::middleware(['auth:sanctum', \Spatie\Permission\Middleware\RoleMiddleware::class . ':it'])->group(function () {
    Route::apiResource('zona', ZonaController::class);
});

// --- MANAJEMEN KELAS (IT) ---
Route::middleware(['auth:sanctum', \Spatie\Permission\Middleware\RoleMiddleware::class . ':it'])->group(function () {
    Route::apiResource('kelas', KelasController::class);
    Route::post('/kelas/{id}/siswa', [KelasController::class, 'addSiswa']);
    Route::delete('/kelas/{id}/siswa', [KelasController::class, 'removeSiswa']);
});

// --- MANAJEMEN USER (IT) ---
Route::middleware(['auth:sanctum', \Spatie\Permission\Middleware\RoleMiddleware::class . ':it'])->group(function () {
    Route::apiResource('users', UserManagementController::class)->except(['store']);
    Route::put('/users/{id}/role', [UserManagementController::class, 'updateRole']);
    Route::put('/users/{id}/kelas', [UserManagementController::class, 'updateKelas']);
    Route::post('/users/import', [UserManagementController::class, 'importUsers']);
    Route::get('/users/export', [UserManagementController::class, 'exportUsers']);
    Route::post('/users/{id}/foto', [UserManagementController::class, 'uploadProfilePicture']);
});

// --- EXPORT DATA ---
Route::middleware(['auth:sanctum', \Spatie\Permission\Middleware\RoleMiddleware::class . ':it|guru|bk'])->group(function () {
    Route::get('/export/absensi/siswa', [ExportController::class, 'exportAbsensiSiswa']);
    Route::get('/export/absensi/siswa/pdf', [ExportController::class, 'exportAbsensiSiswaPdf']);
    Route::get('/export/pelanggaran', [ExportController::class, 'exportPelanggaran']);
    Route::get('/export/jadwal', [ExportController::class, 'exportJadwal']);
});

// --- DASHBOARD ---
Route::middleware(['auth:sanctum', \Spatie\Permission\Middleware\RoleMiddleware::class . ':siswa|guru|bk|it'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});

// --- NOTIFIKASI ---
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread', [NotificationController::class, 'unread']);
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);
});

// --- LIVE ABSENSI (REAL-TIME) ---
Route::middleware('auth:sanctum')->group(function () {
    // Endpoint untuk guru memulai sesi
    Route::post('/live/sesi/mulai', [LiveAbsensiController::class, 'mulaiSesi'])
        ->middleware(\Spatie\Permission\Middleware\RoleMiddleware::class . ':guru|it|bk');

    // Endpoint untuk siswa mengirim pembaruan lokasi
    Route::post('/live/lokasi/update', [LiveAbsensiController::class, 'updateLokasi'])
        ->middleware(\Spatie\Permission\Middleware\RoleMiddleware::class . ':siswa');
});


<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbsensiGuruController;
use App\Http\Controllers\AbsensiSiswaController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::post('/login', [AuthenticatedSessionController::class, 'loginApi']);
Route::post('/logout', [AuthenticatedSessionController::class, 'logoutApi'])
    ->middleware('auth:sanctum');

// Cek API jalan
Route::get('/ping', fn() => response()->json(['message' => 'API OK']));

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('absensi-guru', AbsensiGuruController::class);
    Route::apiResource('absensi-siswa', AbsensiSiswaController::class);
});
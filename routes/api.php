<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AbsensiGuruController;

// Cek API jalan atau nggak
Route::get('/ping', function () {
    return response()->json(['message' => 'API OK']);
});

// CRUD Absensi Guru
Route::apiResource('absensi-guru', AbsensiGuruController::class);

// CRUD Ansensi Siswa 
Route::apiResource('absensi-siswa', AbsensiSiswaController::class);
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbsensiGuruController;
use App\Http\Controllers\AbsensiSiswaController;

// Cek API jalan atau nggak
Route::get('/ping', function () {
    return response()->json(['message' => 'API OK']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('absensi-guru', AbsensiGuruController::class);
    Route::apiResource('absensi-siswa', AbsensiSiswaController::class);
});

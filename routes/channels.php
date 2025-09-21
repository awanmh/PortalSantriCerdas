<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Di sini Anda mendaftarkan semua channel broadcasting event yang didukung
| oleh aplikasi Anda. Callback otorisasi channel digunakan untuk
| memeriksa apakah pengguna yang diautentikasi dapat mendengarkan channel.
|
*/

// Channel default untuk notifikasi pribadi ke user
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// --- PERBAIKAN DI SINI ---
// Tambahkan channel untuk fitur Live Map
Broadcast::channel('lokasi-siswa.{siswaId}', function ($user, $siswaId) {
    // Izinkan jika user memiliki salah satu dari role berikut.
    // Variabel $siswaId tidak perlu digunakan di sini, tapi wajib ada
    // agar nama channelnya cocok.
    if ($user->hasAnyRole(['it', 'guru', 'bk'])) {
        return true;
    }
    return false;
});

// --- CHANNEL UNTUK FITUR LIVE ABSENSI ---
// Hanya guru/it/bk yang bisa mendengarkan lokasi siswa di kelas tertentu.
Broadcast::channel('live-absensi.{kelasId}', function ($user, $kelasId) {
    if ($user->hasAnyRole(['guru', 'it', 'bk'])) {
        // Logika lebih lanjut bisa ditambahkan di sini,
        // misalnya, cek apakah guru ini adalah wali kelas dari kelasId.
        return true;
    }
    return false;
});


// --- CHANNEL UNTUK NOTIFIKASI PRIBADI SPESIFIK ---
// Hanya user yang bersangkutan yang bisa menerima notifikasi ini.

Broadcast::channel('absensi.terlambat.{userId}', function (User $user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('pelanggaran.baru.{userId}', function (User $user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('jadwal.akan-dimulai.{userId}', function (User $user, $userId) {
    return (int) $user->id === (int) $userId;
});

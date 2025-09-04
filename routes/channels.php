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

// Channel default untuk model User
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// --- CHANNEL UNTUK FITUR LIVE ABSENSI ---
// Hanya guru/it/bk yang bisa mendengarkan lokasi siswa di kelas tertentu.
Broadcast::channel('live-absensi.{kelasId}', function ($user, $kelasId) {
    if ($user->hasRole(['guru', 'it', 'bk'])) {
        // Logika lebih lanjut bisa ditambahkan di sini,
        // misalnya, cek apakah guru ini adalah wali kelas dari kelasId.
        return true;
    }
    return false;
});


// --- CHANNEL UNTUK NOTIFIKASI PRIBADI ---
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


<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('absensi.terlambat.{userId}', function (User $user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('pelanggaran.baru.{userId}', function (User $user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('jadwal.akan-dimulai.{userId}', function (User $user, $userId) {
    return (int) $user->id === (int) $userId;
});

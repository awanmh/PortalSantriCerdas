<?php

namespace App\Notifications;

use App\Models\AbsensiSiswa;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class AbsensiTerlambat extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public AbsensiSiswa $absensi) {}

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Absensi Terlambat',
            'message' => 'Siswa ' . $this->absensi->siswa->name . ' terlambat masuk',
            'type' => 'warning',
            'url' => '/absensi/' . $this->absensi->id,
            'timestamp' => now()->toIso8601String()
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => 'Absensi Terlambat',
            'message' => 'Siswa ' . $this->absensi->siswa->name . ' terlambat masuk',
            'type' => 'warning',
            'url' => '/absensi/' . $this->absensi->id,
            'timestamp' => now()->toIso8601String()
        ]);
    }
}
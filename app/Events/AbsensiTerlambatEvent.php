<?php

namespace App\Events;

use App\Models\AbsensiSiswa;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AbsensiTerlambatEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public AbsensiSiswa $absensi) {}

    public function broadcastOn()
    {
        return new PrivateChannel('absensi.terlambat.' . $this->absensi->siswa_id);
    }

    public function broadcastWith()
    {
        return [
            'title' => 'Absensi Terlambat',
            'message' => 'Siswa ' . $this->absensi->siswa->name . ' terlambat masuk',
            'type' => 'warning',
            'url' => '/absensi/' . $this->absensi->id,
            'timestamp' => now()->toIso8601String()
        ];
    }
}

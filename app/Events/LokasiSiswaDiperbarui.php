<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LokasiSiswaDiperbarui implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * User (siswa) yang lokasinya diperbarui.
     *
     * @var \App\Models\User
     */
    public User $siswa;

    /**
     * Data lokasi baru [lat, lng].
     *
     * @var array
     */
    public array $lokasi;

    /**
     * Buat instance event baru.
     *
     * @param \App\Models\User $siswa
     * @param array $lokasi
     */
    public function __construct(User $siswa, array $lokasi)
    {
        $this->siswa = $siswa;
        $this->lokasi = $lokasi;
    }

    /**
     * Dapatkan channel tempat event ini akan disiarkan.
     *
     * Ini adalah private channel yang hanya bisa didengarkan oleh pengguna yang berwenang (guru/bk/it).
     * Channel dinamai secara unik untuk setiap siswa.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('lokasi-siswa.' . $this->siswa->id),
        ];
    }
}
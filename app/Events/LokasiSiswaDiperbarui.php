<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class LokasiSiswaDiperbarui implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $siswa;
    public $lokasi;

    /**
     * Create a new event instance.
     */
    public function __construct(User $siswa, array $lokasi)
    {
        $this->siswa = $siswa;
        $this->lokasi = $lokasi;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Broadcast ke channel privat untuk kelas siswa tersebut
        $kelasId = $this->siswa->kelas->first()->id ?? 'default';
        return [
            new PrivateChannel('live-absensi.' . $kelasId),
        ];
    }
}

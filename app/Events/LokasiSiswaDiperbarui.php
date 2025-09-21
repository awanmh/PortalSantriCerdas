<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LokasiSiswaDiperbarui implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $siswa;
    public array $lokasi;
    
    /**
     * ID Kelas tempat event ini akan disiarkan.
     * @var int
     */
    private int $kelasId;

    /**
     * Buat instance event baru.
     */
    public function __construct(User $siswa, array $lokasi)
    {
        $this->siswa = $siswa;
        $this->lokasi = $lokasi;
        
        // Asumsi: Seorang siswa tergabung dalam satu kelas.
        // Dapatkan kelas pertama yang diikuti siswa.
        // Anda mungkin perlu menambahkan error handling jika siswa tidak punya kelas.
        $this->kelasId = $siswa->kelas()->first()->id; 
    }

    /**
     * Dapatkan channel tempat event ini akan disiarkan.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // UBAH INI: Siarkan ke channel kelas, bukan channel siswa
        return [
            new PrivateChannel('live-absensi.' . $this->kelasId),
        ];
    }
}
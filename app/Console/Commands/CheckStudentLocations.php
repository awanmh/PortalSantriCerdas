<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Zona;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CheckStudentLocations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-student-locations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memeriksa lokasi siswa, mendeteksi yang bolos, dan mengirim notifikasi.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pengecekan lokasi siswa...');

        $now = Carbon::now();
        $schoolStartTime = Carbon::today()->setTime(7, 0);
        $schoolEndTime = Carbon::today()->setTime(15, 0);

        // 1. Hanya jalankan pengecekan selama jam sekolah
        if (!$now->between($schoolStartTime, $schoolEndTime)) {
            $this->info('Di luar jam sekolah. Pengecekan dilewati.');
            return 0;
        }

        // 2. Ambil semua siswa dan zona aktif
        $siswas = User::role('siswa')->get();
        $zonaAktif = Zona::where('is_active', true)->first();

        if (!$zonaAktif) {
            $this->warn('Tidak ada zona aktif. Pengecekan dibatalkan.');
            return 1;
        }

        foreach ($siswas as $siswa) {
            // 3. Cek siswa yang GPS-nya tidak aktif (tidak ada update lebih dari 5 menit)
            if (is_null($siswa->last_seen_at) || Carbon::parse($siswa->last_seen_at)->diffInMinutes($now) > 5) {
                $this->warn("Siswa '{$siswa->name}' terdeteksi tidak aktif (GPS mati).");
                // TODO: Tambahkan logika untuk mengubah status absensi menjadi Alfa dan kirim notifikasi
                continue; // Lanjut ke siswa berikutnya
            }

            // 4. Cek siswa yang berada di luar zona
            $lokasi = json_decode($siswa->last_known_location, true);
            $distance = $this->calculateDistance($zonaAktif->lat, $zonaAktif->lng, $lokasi['lat'], $lokasi['lng']);

            if ($distance > $zonaAktif->radius) {
                $this->error("Siswa '{$siswa->name}' terdeteksi di luar zona sekolah!");
                // TODO: Tambahkan logika untuk mengubah status absensi menjadi Alfa dan kirim notifikasi
            }
        }

        $this->info('Pengecekan lokasi siswa selesai.');
        return 0;
    }

     /**
     * Menghitung jarak antara dua titik geografis.
     * @return float Jarak dalam meter.
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        // ... (Implementasi fungsi Haversine seperti di controller Anda)
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}

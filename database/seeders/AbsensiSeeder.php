<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AbsensiSiswa;
use App\Models\User;
use App\Models\Jadwal;
use Illuminate\Support\Carbon;

class AbsensiSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk membuat data absensi dummy.
     */
    public function run(): void
    {
        // 1. Ambil semua jadwal untuk hari ini
        $jadwalHariIni = Jadwal::where('tanggal', Carbon::today()->toDateString())->get();
        $siswaCollection = User::role('siswa')->get();

        // 2. Lakukan pengecekan untuk memastikan data yang dibutuhkan ada
        if ($jadwalHariIni->isEmpty() || $siswaCollection->isEmpty()) {
            $this->command->warn('⚠️ Seeder Absensi dilewati: Tidak ada jadwal atau siswa untuk hari ini.');
            return;
        }

        $absensiDibuat = 0;
        $statusPilihan = ['hadir', 'hadir', 'hadir', 'izin', 'sakit', 'alfa']; // Memberi probabilitas lebih besar untuk 'hadir'

        // 3. Loop melalui setiap jadwal hari ini
        foreach ($jadwalHariIni as $jadwal) {
            // Ambil 3 siswa secara acak untuk diabsenkan pada jadwal ini
            $siswaUntukDiabsen = $siswaCollection->random(min(3, $siswaCollection->count()));

            foreach ($siswaUntukDiabsen as $siswa) {
                $status = $statusPilihan[array_rand($statusPilihan)];
                $keterangan = null;

                if ($status === 'izin') {
                    $keterangan = 'Ada acara keluarga.';
                } elseif ($status === 'sakit') {
                    $keterangan = 'Demam dan batuk.';
                }

                AbsensiSiswa::create([
                    'user_id' => $siswa->id,
                    'jadwal_id' => $jadwal->id,
                    'status' => $status,
                    'waktu_absensi' => now(),
                    // Kolom lain seperti foto dan GPS bisa dikosongkan untuk data seeder
                    'keterangan' => $keterangan,
                ]);
                $absensiDibuat++;
            }
        }

        $this->command->info("✅ {$absensiDibuat} data absensi siswa berhasil dibuat.");
    }
}

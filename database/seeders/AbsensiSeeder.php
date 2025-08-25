<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AbsensiGuru;
use App\Models\AbsensiSiswa;
use App\Models\User;
use App\Models\Jadwal;

class AbsensiSeeder extends Seeder
{
    public function run(): void
    {
        $guru = User::role('guru')->first();
        $siswa = User::role('siswa')->first();
        $jadwal = Jadwal::first();

        // Absensi Guru
        AbsensiGuru::create([
            'guru_id' => $guru->id,
            'tanggal' => now()->toDateString(),
            'status' => 'hadir',
            'keterangan' => 'Masuk tepat waktu',
        ]);

        // Absensi Siswa
        AbsensiSiswa::create([
            'siswa_id' => $siswa->id,
            'jadwal_id' => $jadwal->id,
            'waktu' => now(),
            'foto_path' => 'absensi/foto1.jpg',
            'lat' => '-7.2504450',
            'lng' => '112.7688450',
            'valid_zona' => true,
            'device_info' => ['browser' => 'Chrome', 'os' => 'Windows'],
            'keterangan' => 'Hadir',
        ]);
    }
}

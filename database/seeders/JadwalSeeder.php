<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\User;

class JadwalSeeder extends Seeder
{
    public function run(): void
    {
        $kelas   = Kelas::first();    // ambil kelas pertama
        $jurusan = Jurusan::first();  // ambil jurusan pertama
        $guru    = User::first();     // ambil user pertama (anggap dia guru)

        if ($kelas && $jurusan && $guru) {
            Jadwal::create([
                'judul_event' => 'Matematika',
                'deskripsi'   => 'Pembelajaran Matematika Dasar',
                'tanggal'     => '2025-08-20',
                'jam_mulai'   => '07:00:00',
                'jam_selesai' => '08:30:00',
                'guru_id'     => $guru->id,
                'kelas_id'    => $kelas->id,
                'jurusan_id'  => $jurusan->id,
                'jenjang'     => '10',
                'tipe'        => 'pelajaran',
            ]);
        } else {
            $this->command->warn('⚠️ Seeder gagal karena kelas/jurusan/guru belum ada.');
        }
    }
}
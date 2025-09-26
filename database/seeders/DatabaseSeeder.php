<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Jalankan seeder sesuai urutan supaya tidak terjadi error
     * foreign key constraint.
     */
    public function run(): void
    {
        $this->call([
            // 1. Buat Peran & Izin terlebih dahulu
            RolePermissionSeeder::class,
            
            // 2. Buat Jurusan
            JurusanSeeder::class,
            
            // 3. Buat Pengguna (guru dengan subject_taught & siswa)
            UserSeeder::class,
            
            // 4. Buat Kelas (memerlukan Jurusan & Guru)
            KelasSeeder::class,
            
            // 5. Buat Mata Pelajaran (diperlukan oleh Jadwal)
            MataPelajaranSeeder::class,
            
            // 6. Buat Jadwal (memerlukan Kelas, Guru, dan Mata Pelajaran)
            JadwalSeeder::class,
        ]);
    }
}

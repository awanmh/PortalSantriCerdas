<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * This method defines the execution order of all seeders.
     * The order is crucial to prevent foreign key constraint errors.
     */
    public function run(): void
    {
        $this->call([
            // 1. Buat Peran & Izin terlebih dahulu
            RolePermissionSeeder::class,
            
            // 2. Buat Jurusan
            JurusanSeeder::class,
            
            // 3. Buat Pengguna (termasuk guru dengan subject_taught & siswa)
            // UserSeeder memerlukan Jurusan untuk membuat data siswa terkait.
            UserSeeder::class,
            
            // 4. Buat Kelas (memerlukan Jurusan & Guru)
            KelasSeeder::class,
            
            // 5. Buat Jadwal (memerlukan Kelas & Guru)
            JadwalSeeder::class,
        ]);
    }
}
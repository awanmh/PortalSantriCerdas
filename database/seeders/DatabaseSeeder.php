<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // 1. Buat Roles & Permissions terlebih dahulu
            RolePermissionSeeder::class,

            // 2. Buat data master seperti Jurusan
            JurusanSeeder::class,
            KelasSeeder::class,

            // 3. Baru buat User setelah Roles & Jurusan ada
            UserSeeder::class,

            // 4. Seeder lain yang bergantung pada User dan Kelas
            // Pastikan Anda sudah membuat file WaliKelasSeeder.php
            // WaliKelasSeeder::class,
            JadwalSeeder::class,
            AbsensiSeeder::class,
        ]);


        // User dummy tambahan (jika diperlukan untuk testing)
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}

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
            RolePermissionSeeder::class,
            UserSeeder::class,       // User (termasuk guru) dibuat
            KelasSeeder::class,      // Kelas dibuat
            JurusanSeeder::class,
            WaliKelasSeeder::class,  // Wali kelas ditetapkan setelah user dan kelas ada
            JadwalSeeder::class,
            AbsensiSeeder::class,
        ]);


        // User dummy tambahan (ini sudah benar)
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
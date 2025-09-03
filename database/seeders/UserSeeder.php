<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Admin/IT
        User::create([
            'name' => 'Admin IT',
            'email' => 'admin.it@gmail.com',
            'password' => Hash::make('password'),
        ])->assignRole('it');

        User::create([
            'name' => 'Teknisi 1',
            'email' => 'teknisi1.teknisi@gmail.com',
            'password' => Hash::make('password'),
        ])->assignRole('it');

        // Guru
        User::create([
            'name' => 'Guru Matematika',
            'email' => 'guru.matematika@gmail.com',
            'password' => Hash::make('password'),
        ])->assignRole('guru');

        User::create([
            'name' => 'Pengajar IPA',
            'email' => 'guru.ipa@gmail.com',
            'password' => Hash::make('password'),
        ])->assignRole('guru');

        // BK
        User::create([
            'name' => 'BK Utama',
            'email' => 'bk.utama@gmail.com',
            'password' => Hash::make('password'),
        ])->assignRole('bk');

        User::create([
            'name' => 'Konselor',
            'email' => 'bk.konselor@gmail.com',
            'password' => Hash::make('password'),
        ])->assignRole('bk');

        // Siswa
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'name' => 'Siswa ' . $i,
                'email' => 'siswa' . $i . '.siswa@gmail.com',
                'password' => Hash::make('password'),
            ])->assignRole('siswa');
        }
    }
}

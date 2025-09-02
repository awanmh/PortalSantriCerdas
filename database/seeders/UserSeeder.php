<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Pastikan kelas ada
        if (Kelas::count() === 0) {
            Kelas::create([
                'nama_kelas' => 'XII RPL 1',
                'jurusan' => 'Rekayasa Perangkat Lunak',
                'wali_kelas' => 'Guru Wali'
            ]);
        }

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
            'email' => 'guru1.guru@gmail.com',
            'password' => Hash::make('password'),
        ])->assignRole('guru');

        User::create([
            'name' => 'Pengajar IPA',
            'email' => 'guru2.pengajar@gmail.com',
            'password' => Hash::make('password'),
        ])->assignRole('guru');

        // BK
        User::create([
            'name' => 'BK Utama',
            'email' => 'bk1.bk@gmail.com',
            'password' => Hash::make('password'),
        ])->assignRole('bk');

        User::create([
            'name' => 'Konselor',
            'email' => 'konseling1.konseling@gmail.com',
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
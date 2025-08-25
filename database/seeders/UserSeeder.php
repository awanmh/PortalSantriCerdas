<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Siswa contoh
        User::create([
            'name' => 'Siswa 1',
            'email' => 'siswa1@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Guru contoh
        User::create([
            'name' => 'Guru 1',
            'email' => 'guru1@example.com',
            'password' => Hash::make('password123'),
        ]);
    }
}
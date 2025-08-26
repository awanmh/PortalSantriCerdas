<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Kosongkan tabel users sebelum seed (reset ID juga)
        DB::table('users')->truncate();

        $users = [
            [
                'name' => 'Admin Sekolah',
                'email' => 'admin@smk.local',
                'password' => 'password',
            ],
            [
                'name' => 'Pak Budi (Guru)',
                'email' => 'guru@smk.local',
                'password' => 'password',
            ],
            [
                'name' => 'Bu Sari (Guru BK)',
                'email' => 'guru_bk@smk.local',
                'password' => 'password',
            ],
            [
                'name' => 'Andi (Siswa)',
                'email' => 'siswa@smk.local',
                'password' => 'password',
            ],
            [
                'name' => 'Siswa 1',
                'email' => 'siswa1@example.com',
                'password' => 'password',
            ],
            [
                'name' => 'Guru 1',
                'email' => 'guru1@example.com',
                'password' => 'password',
            ],
            [
                'name' => 'Test User',
                'email' => 'dummy_test@smk.local', // ganti biar gak bentrok dengan test bawaan Laravel
                'password' => 'password',
            ],
        ];

        foreach ($users as $u) {
            User::create([
                'name' => $u['name'],
                'email' => $u['email'],
                'password' => Hash::make($u['password']),
                'email_verified_at' => now(),
            ]);
        }
    }
}
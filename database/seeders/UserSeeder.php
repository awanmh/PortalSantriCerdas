<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk membuat user awal.
     */
    public function run(): void
    {
        // Mendefinisikan domain email sekolah untuk konsistensi
        $schoolDomain = 'smkalikhlash.sch.id';

        // Menggunakan transaction untuk memastikan semua data berhasil dibuat atau tidak sama sekali
        DB::transaction(function () use ($schoolDomain) {

            // ===== Admin IT (super admin) =====
            $admin = User::firstOrCreate(
                ['email' => 'admin@' . $schoolDomain],
                [
                    'name' => 'Admin IT',
                    'password' => Hash::make('password'),
                ]
            );
            $admin->assignRole('it');


            // ===== Guru =====
            $guru1 = User::firstOrCreate(
                ['email' => 'guru.matematika@' . $schoolDomain],
                [
                    'name' => 'Guru Matematika',
                    'password' => Hash::make('password'),
                ]
            );
            $guru1->assignRole('guru');

            $guru2 = User::firstOrCreate(
                ['email' => 'guru.ipa@' . $schoolDomain],
                [
                    'name' => 'Pengajar IPA',
                    'password' => Hash::make('password'),
                ]
            );
            $guru2->assignRole('guru');


            // ===== BK =====
            $bk1 = User::firstOrCreate(
                ['email' => 'bk.utama@' . $schoolDomain],
                [
                    'name' => 'BK Utama',
                    'password' => Hash::make('password'),
                ]
            );
            $bk1->assignRole('bk');


            // ===== Siswa (otomatis 5 akun dummy dengan data siswa terkait) =====
            $jurusanIds = Jurusan::pluck('id'); // Ambil semua ID jurusan yang ada
            $angkatans = ['2022', '2023', '2024']; // Contoh tahun angkatan

            if ($jurusanIds->isEmpty()) {
                // Jika tidak ada jurusan, hentikan seeder siswa untuk menghindari error.
                // Pastikan JurusanSeeder berjalan sebelum UserSeeder.
                return;
            }

            for ($i = 1; $i <= 5; $i++) {
                $userSiswa = User::firstOrCreate(
                    ['email' => "siswa{$i}@" . $schoolDomain],
                    [
                        'name' => "Siswa Dummy {$i}",
                        'password' => Hash::make('password'),
                    ]
                );

                // Beri role 'siswa'
                $userSiswa->assignRole('siswa');

                // Buat data siswa terkait jika belum ada
                $userSiswa->siswa()->firstOrCreate(
                    [], // Cek berdasarkan user_id saja
                    [
                        'nama' => $userSiswa->name,
                        'jurusan_id' => $jurusanIds->random(), // Pilih ID jurusan secara acak
                        'angkatan' => $angkatans[array_rand($angkatans)], // Pilih angkatan secara acak
                    ]
                );
            }
        });
    }
}

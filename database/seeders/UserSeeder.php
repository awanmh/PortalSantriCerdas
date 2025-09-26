<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Siswa;
use App\Models\Jurusan;
use App\Models\Kelas; // Tambahkan ini
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk membuat data pengguna awal.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $schoolDomain = 'smkalikhlash.sch.id';

            // ===== Admin IT =====
            $admin = User::updateOrCreate(
                ['email' => 'admin@' . $schoolDomain],
                [
                    'name' => 'Admin IT',
                    'password' => Hash::make('password'),
                    'subject_taught' => null,
                ]
            );
            $admin->assignRole('it');

            // ===== Guru Dummy dengan Mata Pelajaran Spesifik =====
            $guruData = [
                ['name' => 'Budi Santoso', 'email' => 'guru.matematika@' . $schoolDomain, 'subject_taught' => 'Matematika Wajib'],
                ['name' => 'Citra Dewi', 'email' => 'guru.ipa@' . $schoolDomain, 'subject_taught' => 'Ilmu Pengetahuan Alam'],
                ['name' => 'Eko Prasetyo', 'email' => 'guru.bindo@' . $schoolDomain, 'subject_taught' => 'Bahasa Indonesia'],
                ['name' => 'Fahri Ramadhan', 'email' => 'guru.bing@' . $schoolDomain, 'subject_taught' => 'Bahasa Inggris'],
                ['name' => 'Gita Permata', 'email' => 'guru.ddg@' . $schoolDomain, 'subject_taught' => 'Dasar Desain Grafis'],
            ];

            foreach ($guruData as $data) {
                $guru = User::updateOrCreate(
                    ['email' => $data['email']],
                    [
                        'name' => $data['name'],
                        'password' => Hash::make('password'),
                        'subject_taught' => $data['subject_taught'],
                    ]
                );
                $guru->assignRole('guru');
            }

            // ===== BK =====
            $bk = User::updateOrCreate(
                ['email' => 'bk.utama@' . $schoolDomain],
                [
                    'name' => 'BK Utama',
                    'password' => Hash::make('password'),
                    'subject_taught' => null,
                ]
            );
            $bk->assignRole('bk');

            // ===== Siswa Dummy =====
            if (Jurusan::count() > 0 && Kelas::count() > 0) {
                $jurusanIds = Jurusan::pluck('id');
                $kelasIds = Kelas::pluck('id'); // Ambil semua ID kelas yang ada
                $angkatans = ['2022', '2023', '2024'];

                for ($i = 1; $i <= 30; $i++) {
                    $userSiswa = User::firstOrCreate(
                        ['email' => "siswa{$i}@" . $schoolDomain],
                        [
                            'name' => "Siswa Dummy {$i}",
                            'password' => Hash::make('password')
                        ]
                    );
                    $userSiswa->assignRole('siswa');

                    // Menugaskan siswa ke kelas secara acak
                    $userSiswa->kelas()->sync([$kelasIds->random()]);
                    
                    // Membuat data siswa terkait di tabel 'siswa' jika ada modelnya
                    if (class_exists(Siswa::class)) {
                        Siswa::firstOrCreate(
                            ['user_id' => $userSiswa->id],
                            [
                                'nama' => $userSiswa->name,
                                'jurusan_id' => $jurusanIds->random(),
                                'angkatan' => $angkatans[array_rand($angkatans)],
                            ]
                        );
                    }
                }
            } else {
                $this->command->warn('⚠️ Seeder Siswa dilewati: Data Jurusan atau Kelas belum ada.');
            }
        });

        $this->command->info('✅ User Seeder berhasil dijalankan!');
    }
}
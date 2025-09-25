<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::transaction(function () {
            // Memastikan data Jurusan dan Guru sudah ada sebelum membuat Kelas
            $jurusans = Jurusan::all();
            if ($jurusans->isEmpty()) {
                $this->command->warn('⚠️ Seeder Kelas gagal: Data Jurusan belum ada. Jalankan JurusanSeeder terlebih dahulu.');
                return;
            }
            
            $gurus = User::role('guru')->get();
            if ($gurus->isEmpty()) {
                $this->command->warn('⚠️ Seeder Kelas gagal: Tidak ada user dengan role guru.');
                return;
            }

            // Menghapus data kelas lama untuk menghindari duplikasi
            Kelas::truncate();

            $jenjang = ['X', 'XI', 'XII'];
            $nomorUrutKelas = ['1', '2']; // Membuat 2 kelas paralel untuk setiap jenjang/jurusan

            foreach ($jenjang as $j) {
                foreach ($jurusans as $jurusan) {
                    foreach ($nomorUrutKelas as $nomor) {
                        Kelas::create([
                            // Menggunakan nama_singkat dari Jurusan untuk nama kelas yang rapi
                            'nama_kelas' => "{$j} {$jurusan->nama_singkat} {$nomor}",
                            'jenjang' => $j,
                            'jurusan_id' => $jurusan->id,
                            // Menugaskan Wali Kelas secara acak dari daftar guru yang ada
                            'wali_kelas_id' => $gurus->random()->id,
                        ]);
                    }
                }
            }
        });

        $this->command->info('✅ Kelas Seeder berhasil dijalankan!');
    }
}


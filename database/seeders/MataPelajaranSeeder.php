<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MataPelajaran;
use Illuminate\Support\Facades\DB;

class MataPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Mapel Umum
            $umum = [
                'Pendidikan Agama dan Budi Pekerti',
                'Pendidikan Pancasila',
                'PPKn (Pendidikan Pancasila dan Kewarganegaraan)',
                'Sejarah',
                'Bahasa Indonesia',
                'Bahasa Inggris',
                'Matematika Wajib',
                'Informatika',
                'IPAS (Ilmu Pengetahuan Alam & Sosial)',
                'Penjasorkes (Pendidikan Jasmani, Olahraga, dan Kesehatan)',
                'Seni Budaya',
                'Akhlaq',
                'FIQIH',
                "Al Qur'an",
            ];
            foreach ($umum as $mapel) {
                MataPelajaran::firstOrCreate([
                    'nama' => $mapel,
                    'kategori' => 'Umum',
                ]);
            }

            // 2. Produktif (AKT)
            $produktif = [
                'Administrasi Umum',
                'Administrasi Pajak',
                'Komputer Akuntansi',
                'Ekonomi Bisnis',
                'Perbankan Dasar',
                'Akuntansi Keuangan',
                'Praktikum Akuntansi Perusahaan Jasa, Dagang, dan Manufaktur',
                'Akuntansi Dasar',
                'Praktikum Akuntansi Lembaga/Instansi Pemerintah',
                'Produk Kreatif dan Kewirausahaan',
            ];
            foreach ($produktif as $mapel) {
                MataPelajaran::firstOrCreate([
                    'nama' => $mapel,
                    'kategori' => 'Produktif',
                    'jurusan' => 'AKT',
                ]);
            }

            // 3. DKV
            $dkv = [
                'Desain Grafis Percetakan',
                'Dasar Desain Grafis',
                'Teknik Pengolahan Audio Video',
                'Teknik Animasi 2D dan 3D',
                'Desain Media Interaktif',
                'Etika Profesi',
            ];
            foreach ($dkv as $mapel) {
                MataPelajaran::firstOrCreate([
                    'nama' => $mapel,
                    'kategori' => 'Produktif',
                    'jurusan' => 'DKV',
                ]);
            }

            // 4. TSM
            $tsm = [
                'Pekerjaan Dasar Otomotif',
                'Teknologi Dasar Otomotif',
                'Perawatan dan Perbaikan Sasis Sepeda Motor',
                'Perawatan dan Perbaikan Engine Sepeda Motor',
                'Perawatan dan Perbaikan Sepeda Motor Listrik dan Hybrid',
                'Perawatan dan Perbaikan Sistem Pemindahan Tenaga Mesin Sepeda Motor',
                'Gambar Teknik Otomotif',
                'Pengelolaan Bengkel',
                'Pemeliharaan Bengkel',
            ];
            foreach ($tsm as $mapel) {
                MataPelajaran::firstOrCreate([
                    'nama' => $mapel,
                    'kategori' => 'Produktif',
                    'jurusan' => 'TSM',
                ]);
            }

            // 5. TKJ
            $tkj = [
                'Dasar-Dasar Teknik Jaringan Komputer dan Telekomunikasi',
                'Pemasangan dan Konfigurasi Perangkat Jaringan',
                'Sistem Keamanan Jaringan',
                'FTTx',
                'Perencanaan dan Pengalamatan Jaringan',
                'Teknologi Jaringan Kabel dan Nirkabel',
                'Pemrograman Dasar',
                'Sistem Komputer',
                'Administrasi Sistem Jaringan',
            ];
            foreach ($tkj as $mapel) {
                MataPelajaran::firstOrCreate([
                    'nama' => $mapel,
                    'kategori' => 'Produktif',
                    'jurusan' => 'TKJ',
                ]);
            }
        });

        $this->command->info('✅ Mata Pelajaran Seeder berhasil dijalankan!');
    }
}

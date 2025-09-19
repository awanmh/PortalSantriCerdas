<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;
use App\Models\Jurusan;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // 1. Pengecekan Keamanan: Pastikan Jurusan sudah ada.
        $jurusans = Jurusan::all();
        if ($jurusans->count() < 2) {
            $this->command->error('Tabel Jurusan harus memiliki setidaknya 2 data. Silakan periksa JurusanSeeder Anda.');
            return;
        }

        // 2. Hapus data lama untuk menghindari duplikasi
        Kelas::truncate();

        // 3. Ambil DUA data Jurusan pertama yang tersedia, apapun namanya.
        $jurusanSatu = $jurusans->get(0);
        $jurusanDua = $jurusans->get(1);

        // 4. Definisikan data kelas dalam sebuah array agar lebih bersih
        $kelasData = [
            // Kelas untuk Jurusan Pertama
            ['nama_kelas' => 'X ' . $jurusanSatu->nama_singkat . ' 1', 'jenjang' => 'X', 'jurusan_id' => $jurusanSatu->id],
            ['nama_kelas' => 'XI ' . $jurusanSatu->nama_singkat . ' 1', 'jenjang' => 'XI', 'jurusan_id' => $jurusanSatu->id],
            ['nama_kelas' => 'XII ' . $jurusanSatu->nama_singkat . ' 1', 'jenjang' => 'XII', 'jurusan_id' => $jurusanSatu->id],

            // Kelas untuk Jurusan Kedua
            ['nama_kelas' => 'X ' . $jurusanDua->nama_singkat . ' 1', 'jenjang' => 'X', 'jurusan_id' => $jurusanDua->id],
            ['nama_kelas' => 'XI ' . $jurusanDua->nama_singkat . ' 1', 'jenjang' => 'XI', 'jurusan_id' => $jurusanDua->id],
            ['nama_kelas' => 'XII ' . $jurusanDua->nama_singkat . ' 1', 'jenjang' => 'XII', 'jurusan_id' => $jurusanDua->id],
        ];

        // 5. Looping untuk membuat data kelas
        foreach ($kelasData as $data) {
            Kelas::create($data);
        }
        
        $this->command->info('Seeder Kelas berhasil dijalankan.');
    }
}


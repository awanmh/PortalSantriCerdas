<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kelas;

class WaliKelasSeeder extends Seeder
{
    public function run(): void
    {
        // Cari kelas X RPL 1
        $kelasRpl10 = Kelas::where('nama_kelas', 'X RPL 1')->first();
        // Cari guru yang akan menjadi wali kelasnya
        $guruWaliRpl10 = User::where('email', 'guru.budi@gmail.com')->first();

        // Jika keduanya ditemukan, tetapkan wali kelas
        if ($kelasRpl10 && $guruWaliRpl10) {
            $kelasRpl10->wali_kelas_id = $guruWaliRpl10->id; // Asumsi nama kolomnya wali_kelas_id
            $kelasRpl10->save();
        }

        // --- Ulangi untuk kelas lain ---
        $kelasRpl11 = Kelas::where('nama_kelas', 'XI RPL 1')->first();
        $guruWaliRpl11 = User::where('email', 'guru.ani@gmail.com')->first();

        if ($kelasRpl11 && $guruWaliRpl11) {
            $kelasRpl11->wali_kelas_id = $guruWaliRpl11->id;
            $kelasRpl11->save();
        }
    }
}
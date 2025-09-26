<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Zona;

class ZonaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama untuk memastikan kebersihan data
        Zona::truncate();

        // Buat zona sekolah utama dan langsung aktifkan
        Zona::create([
            'nama_zona' => 'Area Sekolah Utama',
            'lat'       => -7.275441, // Ganti dengan latitude sekolah Anda
            'lng'       => 112.750839, // Ganti dengan longitude sekolah Anda
            'radius'    => 150.00, // Radius dalam meter
            'is_active' => true,
        ]);

        // Buat zona lain (jika ada) sebagai contoh, tapi tidak aktif
        Zona::create([
            'nama_zona' => 'Lapangan Olahraga',
            'lat'       => -7.276500, // Contoh latitude
            'lng'       => 112.751000, // Contoh longitude
            'radius'    => 50.00,
            'is_active' => false,
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jurusan;
use Illuminate\Support\Facades\DB;

class JurusanSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $jurusans = [
                ['nama' => 'Teknik Sepeda Motor', 'nama_singkat' => 'TSM'],
                ['nama' => 'Teknik Komputer & Jaringan', 'nama_singkat' => 'TKJ'],
                ['nama' => 'Desain Komunikasi Visual', 'nama_singkat' => 'DKV'],
                ['nama' => 'Akuntansi', 'nama_singkat' => 'AKT'],
            ];

            foreach ($jurusans as $j) {
                Jurusan::firstOrCreate(
                    ['nama_singkat' => $j['nama_singkat']],
                    $j
                );
            }
        });

        $this->command->info('✅ Jurusan Seeder berhasil dijalankan!');
    }
}

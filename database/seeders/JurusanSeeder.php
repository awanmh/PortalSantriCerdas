<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jurusan;
use Illuminate\Support\Facades\DB;

class JurusanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::transaction(function () {
            Jurusan::firstOrCreate(
                ['nama' => 'Rekayasa Perangkat Lunak'],
                ['nama_singkat' => 'RPL']
            );
            Jurusan::firstOrCreate(
                ['nama' => 'Teknik Jaringan Komputer'],
                ['nama_singkat' => 'TKJ']
            );
            Jurusan::firstOrCreate(
                ['nama' => 'Multimedia'],
                ['nama_singkat' => 'MM']
            );
        });

        $this->command->info('✅ Jurusan Seeder berhasil dijalankan!');
    }
}
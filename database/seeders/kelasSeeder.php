<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Kelas::create([
            'nama_kelas' => 'X RPL 1',
            'tingkat' => 10,
            'jurusan' => 'Rekayasa Perangkat Lunak', // Ditambahkan
        ]);

        Kelas::create([
            'nama_kelas' => 'XI RPL 1',
            'tingkat' => 11,
            'jurusan' => 'Rekayasa Perangkat Lunak', // Ditambahkan
        ]);

        Kelas::create([
            'nama_kelas' => 'XII RPL 1',
            'tingkat' => 12,
            'jurusan' => 'Rekayasa Perangkat Lunak', // Ditambahkan
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        Kelas::create([
            'nama' => 'X RPL 1',
            'tingkat' => '10',
        ]);

        Kelas::create([
            'nama' => 'XI RPL 1',
            'tingkat' => '11',
        ]);

        Kelas::create([
            'nama' => 'XII RPL 1',
            'tingkat' => '12',
        ]);
    }
}
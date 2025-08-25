<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jurusan;

class JurusanSeeder extends Seeder
{
    public function run(): void
    {
        Jurusan::create(['nama' => 'Rekayasa Perangkat Lunak']);
        Jurusan::create(['nama' => 'Teknik Jaringan Komputer']);
        Jurusan::create(['nama' => 'Multimedia']);
    }
}

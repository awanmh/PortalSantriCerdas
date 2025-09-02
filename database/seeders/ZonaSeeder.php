<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Zona;

class ZonaSeeder extends Seeder
{
    public function run()
    {
        // Koordinat zona sekolah (ganti dengan koordinat sebenarnya)
        $coordinates = [
            [106.827153, -6.175392], // longitude, latitude
            [106.827200, -6.175400],
            [106.827180, -6.175410],
            [106.827153, -6.175392] // kembali ke titik awal
        ];

        // Format WKT untuk polygon
        $wktPolygon = 'POLYGON((' .
            implode(', ', array_map(function ($coord) {
                return $coord[0] . ' ' . $coord[1];
            }, $coordinates)) .
            '))';

        // Buat zona sekolah
        Zona::create([
            'name' => 'Sekolah SMK',
            'polygon' => \DB::raw("ST_GeomFromText('{$wktPolygon}', 4326)"),
            'description' => 'Zona sekolah utama'
        ]);
    }
}
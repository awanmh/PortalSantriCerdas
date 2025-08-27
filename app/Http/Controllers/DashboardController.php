<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Data Statistik
        $stats = [
            'jumlahSantri' => 152,
            'jumlahGuru' => 25,
            'kelasAktif' => 12,
        ];

        // Data Dummy untuk Pengumuman
        $pengumuman = [
            ['id' => 1, 'judul' => 'Ujian Akhir Semester', 'tanggal' => '20 Agu 2025', 'isi' => 'Ujian akan dilaksanakan mulai minggu depan. Harap mempersiapkan diri.'],
            ['id' => 2, 'judul' => 'Kerja Bakti Akbar', 'tanggal' => '18 Agu 2025', 'isi' => 'Diwajibkan bagi seluruh santri untuk mengikuti kerja bakti di lingkungan pesantren.'],
        ];

        // Data Dummy untuk Jadwal Hari Ini
        $jadwalHariIni = [
            ['id' => 1, 'jam' => '07:00 - 08:30', 'mapel' => 'Fikih', 'guru' => 'Ustadz Ahmad'],
            ['id' => 2, 'jam' => '08:30 - 10:00', 'mapel' => 'Aqidah Akhlak', 'guru' => 'Ustadz Hasan'],
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'pengumuman' => $pengumuman,
            'jadwalHariIni' => $jadwalHariIni,
        ]);
    }
}
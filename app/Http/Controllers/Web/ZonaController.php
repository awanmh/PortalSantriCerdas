<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Zona;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Class ZonaController
 * Mengelola semua logika yang terkait dengan halaman Manajemen Zona Sekolah.
 */
class ZonaController extends Controller
{
    /**
     * Menampilkan halaman utama untuk manajemen zona.
     * Mengambil data zona pertama dari database atau memberikan data default jika belum ada.
     *
     * @return \Inertia\Response
     */
     public function index(): Response
    {
        // Untuk sistem sekolah, biasanya kita hanya mengelola satu zona utama.
        // Logika ini mengambil zona pertama, atau membuat instance baru dengan
        // data default jika tabel zona masih kosong.
        $zona = Zona::firstOrNew(
            ['id' => 1], // Kunci pencarian
            [            // Data default jika tidak ditemukan
                'nama_zona' => 'Zona Utama Sekolah',
                'lat' => -6.9023225, // Default SMK Al Ikhlash
                'lng' => 112.4772704,
                'radius' => 50,
                'is_active' => true,
                'jam_masuk' => '07:00:00',
                'jam_pulang' => '15:00:00',
            ]
        );

        return Inertia::render('Admin/Zona/Index', [
            'zona' => $zona,
        ]);
    }

    /**
     * Menyimpan atau memperbarui data zona sekolah.
     * Menggunakan metode updateOrCreate untuk menyederhanakan logika.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi data yang masuk dari form
        $validatedData = $request->validate([
            'nama_zona' => 'required|string|max:255',
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:10', // Radius minimal 10 meter
            'jam_masuk' => 'required|date_format:H:i',
            'jam_pulang' => 'required|date_format:H:i|after:jam_masuk',
        ]);

        // Karena hanya ada satu zona aktif, kita pastikan semua zona lain non-aktif
        Zona::where('id', '!=', 1)->update(['is_active' => false]);

        // Menggunakan updateOrCreate:
        // - Jika record dengan 'id' = 1 ada, akan di-update.
        // - Jika tidak ada, akan dibuat record baru dengan data ini.
        Zona::updateOrCreate(
            ['id' => 1],
            array_merge($validatedData, ['is_active' => true])
        );

        // Kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->route('zona.index')
                         ->with('success', 'Pengaturan zona sekolah berhasil diperbarui!');
    }
}



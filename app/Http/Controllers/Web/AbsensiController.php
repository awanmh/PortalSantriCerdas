<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Zona;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Controller AbsensiController untuk Web.
 *
 * Bertanggung jawab untuk menangani request HTTP terkait halaman absensi
 * yang di-render menggunakan Inertia.js (sisi web).
 */
class AbsensiController extends Controller
{
    /**
     * Menampilkan halaman untuk membuat atau melakukan absensi.
     *
     * Metode ini mengambil data zona absensi yang sedang aktif dari database.
     * Data tersebut (latitude, longitude, dan radius) kemudian dikirimkan
     * sebagai props ke komponen frontend 'Absensi/Create.vue' agar
     * frontend dapat melakukan validasi lokasi secara real-time.
     *
     * @return \Inertia\Response
     */
    public function create(): Response
    {
        // 1. Ambil satu-satunya zona yang sedang aktif dari database.
        $zonaAktif = Zona::where('is_active', true)->first();

        // 2. Render komponen Vue 'Absensi/Create.vue' menggunakan Inertia.
        return Inertia::render('Absensi/Create', [
            // 3. Kirim data zona aktif sebagai prop ke frontend.
            // Jika tidak ada zona aktif, kirim null agar frontend bisa
            // menampilkan pesan error yang sesuai.
            'zonaAktif' => $zonaAktif ? [
                'lat'    => (float) $zonaAktif->lat,
                'lng'    => (float) $zonaAktif->lng,
                'radius' => (int) $zonaAktif->radius,
            ] : null,
        ]);
    }
}


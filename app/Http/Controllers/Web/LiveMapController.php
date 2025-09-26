<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Zona;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LiveMapController extends Controller
{
    /**
     * Menampilkan halaman peta pemantauan langsung.
     */
    public function index(): Response
    {
        // Ambil semua siswa untuk ditampilkan di daftar pilihan
        $siswas = User::role('siswa')->orderBy('name')->get(['id', 'name']);
        
        // Ambil zona sekolah untuk digambar di peta
        $zona = Zona::where('is_active', true)->first();

        return Inertia::render('Admin/LiveMap', [
            'siswas' => $siswas,
            'zona' => $zona,
        ]);
    }
}

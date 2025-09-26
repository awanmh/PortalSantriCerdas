<?php

namespace App\Http\Controllers;

use App\Models\CatatanPelanggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Notifications\NewPelanggaranNotification;

class CatatanPelanggaranController extends Controller
{
    /**
     * Menampilkan daftar catatan pelanggaran (khusus guru BK)
     */
    public function index()
    {
        $catatan = CatatanPelanggaran::with(['guru', 'siswa'])->get();
        return view('pelanggaran.index', compact('catatan'));
    }

    /**
     * Form input pelanggaran (khusus guru)
     */
    public function create()
    {
        $siswa = User::role('siswa')->get();
        return view('pelanggaran.create', compact('siswa'));
    }

    /**
     * Menyimpan catatan pelanggaran
     */
    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:users,id',
            'deskripsi' => 'required|string|max:2000',
        ]);

        $pelanggaran = CatatanPelanggaran::create([
            'guru_id' => Auth::id(),
            'siswa_id' => $request->siswa_id,
            'deskripsi' => $request->deskripsi,
        ]);

        // Kirim notifikasi ke semua guru BK
        $guruBK = User::role('guru_bk')->get();
        foreach ($guruBK as $g) {
            $g->notify(new NewPelanggaranNotification($pelanggaran));
        }

        return redirect()->back()->with('success', 'Catatan pelanggaran berhasil ditambahkan.');
    }
}
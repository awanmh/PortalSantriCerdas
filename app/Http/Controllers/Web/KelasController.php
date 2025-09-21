<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\KelasRequest;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Controller untuk mengelola data master Kelas.
 */
class KelasController extends Controller
{
    /**
     * Menampilkan halaman daftar semua kelas.
     * Form untuk menambah/mengedit juga ada di sini, jadi kita kirim data yang diperlukan.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Kelas/Index', [
            // Data utama: daftar semua kelas, diurutkan berdasarkan NAMA_KELAS yang benar
            'kelas' => Kelas::with(['jurusan', 'waliKelas'])->orderBy('nama_kelas')->get(),

            // Data pendukung untuk form: daftar semua jurusan
            'jurusan' => Jurusan::orderBy('nama')->get(['id', 'nama']),
            
            // Data pendukung untuk form: daftar semua guru
            'guru' => User::role('guru')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Menyimpan data kelas baru ke dalam database.
     */
    public function store(KelasRequest $request): RedirectResponse
    {
        // Validasi sudah otomatis dijalankan oleh KelasRequest.
        Kelas::create($request->validated());

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui data kelas yang ada di database.
     * Variabel $kelas diikat secara otomatis dari database berkat Route Model Binding.
     */
    public function update(KelasRequest $request, Kelas $kela): RedirectResponse
    {
        $kela->update($request->validated());

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data kelas berhasil diperbarui.');
    }

    /**
     * Menghapus data kelas dari database.
     */
    public function destroy(Kelas $kela): RedirectResponse
    {
        // Pengecekan integritas data: jangan hapus kelas jika masih ada siswa di dalamnya.
        // Catatan: Ini memerlukan relasi `siswa()` di model `Kelas` agar berfungsi.
        if ($kela->siswa()->exists()) {
            return redirect()->route('admin.kelas.index')
                ->with('error', 'Kelas tidak dapat dihapus karena masih memiliki siswa.');
        }

        $kela->delete();

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }
}


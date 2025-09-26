<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\JurusanRequest; // Menggunakan Form Request untuk validasi
use App\Models\Jurusan;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Controller untuk mengelola data master Jurusan.
 */
class JurusanController extends Controller
{
    /**
     * Menampilkan halaman daftar semua jurusan.
     *
     * @return \Inertia\Response
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Jurusan/Index', [
            'jurusan' => Jurusan::orderBy('nama')->get(),
        ]);
    }

    /**
     * Menyimpan data jurusan baru ke dalam database.
     *
     * @param  \App\Http\Requests\JurusanRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(JurusanRequest $request): RedirectResponse
    {
        // Validasi sudah otomatis dijalankan oleh JurusanRequest.
        // Jika validasi gagal, user akan otomatis di-redirect kembali dengan pesan error.
        Jurusan::create($request->validated());

        return redirect()->route('admin.jurusan.index')
            ->with('success', 'Jurusan baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui data jurusan yang ada di database.
     *
     * @param  \App\Http\Requests\JurusanRequest  $request
     * @param  \App\Models\Jurusan  $jurusan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(JurusanRequest $request, Jurusan $jurusan): RedirectResponse
    {
        // Validasi juga otomatis dijalankan di sini.
        // $jurusan diambil otomatis dari database berkat Route Model Binding.
        $jurusan->update($request->validated());

        return redirect()->route('admin.jurusan.index')
            ->with('success', 'Data jurusan berhasil diperbarui.');
    }

    /**
     * Menghapus data jurusan dari database.
     *
     * @param  \App\Models\Jurusan  $jurusan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Jurusan $jurusan): RedirectResponse
    {
        // Pengecekan penting untuk menjaga integritas data.
        // Mencegah penghapusan jika jurusan ini masih digunakan oleh data kelas.
        if ($jurusan->kelas()->exists()) {
            return redirect()->route('admin.jurusan.index')
                ->with('error', 'Jurusan tidak dapat dihapus karena masih memiliki kelas terkait.');
        }

        $jurusan->delete();

        return redirect()->route('admin.jurusan.index')
            ->with('success', 'Jurusan berhasil dihapus.');
    }
}


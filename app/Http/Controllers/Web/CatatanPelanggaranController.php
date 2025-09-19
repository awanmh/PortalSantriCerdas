<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\CatatanPelanggaranRequest;
use App\Models\CatatanPelanggaran;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CatatanPelanggaranController extends Controller
{
    /**
     * Menampilkan halaman daftar semua catatan pelanggaran.
     */
    public function index(): Response
    {
        // Mengambil data catatan pelanggaran dengan paginasi
        $catatanPelanggaran = CatatanPelanggaran::with(['siswa:id,name', 'pelapor:id,name'])
            ->latest('tanggal')
            ->paginate(10)
            ->through(fn ($item) => [
                'id' => $item->id,
                'user_id' => $item->user_id,
                'jenis' => $item->jenis,
                'deskripsi' => $item->deskripsi,
                'poin' => $item->poin,
                'tanggal' => $item->tanggal->format('Y-m-d'), // Format tanggal untuk form edit
                'tanggal_formatted' => $item->tanggal->format('d M Y'),
                'siswa' => $item->siswa,
                'pelapor' => $item->pelapor,
            ]);

        // Mengambil daftar siswa untuk ditampilkan di dropdown form
        $siswas = User::role('siswa')->orderBy('name')->get(['id', 'name']);

        // Merender halaman Vue dan mengirimkan data yang dibutuhkan sebagai props
        return Inertia::render('CatatanPelanggaran/Index', [
            'catatanPelanggaran' => $catatanPelanggaran,
            'siswas' => $siswas,
        ]);
    }

    /**
     * Menyimpan catatan pelanggaran baru ke dalam database.
     */
    public function store(CatatanPelanggaranRequest $request): RedirectResponse
    {
        // Menggabungkan ID pelapor (user yang sedang login) dengan data tervalidasi
        $data = array_merge($request->validated(), [
            'pelapor_id' => Auth::id(),
        ]);

        CatatanPelanggaran::create($data);

        return redirect()->route('catatan-pelanggaran.index')
            ->with('success', 'Catatan pelanggaran berhasil ditambahkan.');
    }

    /**
     * Memperbarui catatan pelanggaran yang ada di database.
     */
    public function update(CatatanPelanggaranRequest $request, CatatanPelanggaran $catatanPelanggaran): RedirectResponse
    {
        $catatanPelanggaran->update($request->validated());

        return redirect()->route('catatan-pelanggaran.index')
            ->with('success', 'Data catatan pelanggaran berhasil diperbarui.');
    }

    /**
     * Menghapus catatan pelanggaran dari database.
     */
    public function destroy(CatatanPelanggaran $catatanPelanggaran): RedirectResponse
    {
        $catatanPelanggaran->delete();

        return redirect()->route('catatan-pelanggaran.index')
            ->with('success', 'Catatan pelanggaran berhasil dihapus.');
    }
}


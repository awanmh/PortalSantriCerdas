<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\JadwalRequest;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Controller untuk mengelola data master Jadwal Pelajaran.
 */
class JadwalController extends Controller
{
    /**
     * Menampilkan halaman daftar semua jadwal.
     * Juga mengirimkan data pendukung untuk form tambah/edit.
     */
    public function index(): Response
    {
        // Ambil data jadwal dengan paginasi dan relasinya (eager loading)
        $jadwals = Jadwal::with(['kelas.jurusan', 'guru'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_mulai', 'asc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Jadwal/Index', [
            // --- PERUBAHAN NAMA KEY DI BAWAH INI ---
            'jadwal' => $jadwals,                             // Diubah dari 'jadwals'
            'kelasOptions' => Kelas::orderBy('nama_kelas')->get(['id', 'nama_kelas']), // Diubah dari 'kelas'
            'guruOptions' => User::role('guru')->orderBy('name')->get(['id', 'name']),   // Diubah dari 'gurus'
        ]);
    }

    /**
     * Menyimpan jadwal baru ke database.
     */
    public function store(JadwalRequest $request): RedirectResponse
    {
        Jadwal::create($request->validated());

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui jadwal di database.
     */
    public function update(JadwalRequest $request, Jadwal $jadwal): RedirectResponse
    {
        $jadwal->update($request->validated());

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal berhasil diperbarui.');
    }

    /**
     * Menghapus jadwal dari database.
     */
    public function destroy(Jadwal $jadwal): RedirectResponse
    {
        $jadwal->delete();

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal berhasil dihapus.');
    }
}


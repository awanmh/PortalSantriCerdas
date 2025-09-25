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

class JadwalController extends Controller
{
    /**
     * Menampilkan halaman daftar semua jadwal.
     * Menyertakan data pendukung untuk form tambah/edit.
     */
    public function index(): Response
    {
        // Ambil semua jadwal dengan relasi kelas dan guru
        // Urutkan berdasarkan hari (Senin–Minggu) dan jam_mulai, serta tanggal untuk acara
        $jadwals = Jadwal::with(['kelas.jurusan', 'guru'])
            ->orderByRaw("
                CASE hari
                    WHEN 'Senin' THEN 1
                    WHEN 'Selasa' THEN 2
                    WHEN 'Rabu' THEN 3
                    WHEN 'Kamis' THEN 4
                    WHEN 'Jumat' THEN 5
                    WHEN 'Sabtu' THEN 6
                    WHEN 'Minggu' THEN 7
                    ELSE 8 -- Untuk jadwal tanpa hari (misal, acara berdasarkan tanggal) agar berada di akhir daftar hari
                END
            ")
            ->orderBy('tanggal', 'asc') // Tambahkan sorting by tanggal untuk acara atau jika hari null
            ->orderBy('jam_mulai', 'asc')
            ->get();

        return Inertia::render('Admin/Jadwal/Index', [
            'jadwal'       => $jadwals,
            'kelasOptions' => Kelas::orderBy('nama_kelas')->get(['id', 'nama_kelas']),
            'guruOptions'  => User::role('guru')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Simpan jadwal baru ke database.
     */
    public function store(JadwalRequest $request): RedirectResponse
    {
        Jadwal::create($request->validated());

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal baru berhasil ditambahkan.');
    }

    /**
     * Perbarui jadwal di database.
     */
    public function update(JadwalRequest $request, Jadwal $jadwal): RedirectResponse
    {
        $jadwal->update($request->validated());

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal berhasil diperbarui.');
    }

    /**
     * Hapus jadwal dari database.
     */
    public function destroy(Jadwal $jadwal): RedirectResponse
    {
        $jadwal->delete();

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal berhasil dihapus.');
    }
}
<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\JadwalRequest;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\User;
use App\Models\MataPelajaran;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class JadwalController extends Controller
{
    /**
     * Tampilkan daftar semua jadwal dengan relasi terkait.
     */
    public function index(): Response
    {
        $jadwals = Jadwal::with(['kelas.jurusan', 'guru', 'mataPelajaran'])
            ->orderByRaw("
                CASE hari
                    WHEN 'Senin'  THEN 1
                    WHEN 'Selasa' THEN 2
                    WHEN 'Rabu'   THEN 3
                    WHEN 'Kamis'  THEN 4
                    WHEN 'Jumat'  THEN 5
                    WHEN 'Sabtu'  THEN 6
                    WHEN 'Minggu' THEN 7
                    ELSE 8
                END
            ")
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->get();

        return Inertia::render('Admin/Jadwal/Index', [
            'jadwal'               => $jadwals,
            'kelasOptions'         => Kelas::orderBy('nama_kelas')->get(['id', 'nama_kelas']),
            'guruOptions'          => User::role('guru')->orderBy('name')->get(['id', 'name']),
            'mataPelajaranOptions' => MataPelajaran::orderBy('nama')->get(['id', 'nama']),
        ]);
    }

    /**
     * Simpan jadwal baru.
     */
    public function store(JadwalRequest $request): RedirectResponse
    {
        // Pastikan mata_pelajaran_id terisi, mapping dari mata_pelajaran jika ada
        $data = $request->validated();
        if (!isset($data['mata_pelajaran_id']) && $request->has('mata_pelajaran')) {
            $data['mata_pelajaran_id'] = $request->input('mata_pelajaran');
        }

        Jadwal::create($data);

        return redirect()
            ->route('admin.jadwal.index')
            ->with('success', 'Jadwal baru berhasil ditambahkan.');
    }

    /**
     * Perbarui jadwal.
     */
    public function update(JadwalRequest $request, Jadwal $jadwal): RedirectResponse
    {
        $data = $request->validated();
        if (!isset($data['mata_pelajaran_id']) && $request->has('mata_pelajaran')) {
            $data['mata_pelajaran_id'] = $request->input('mata_pelajaran');
        }

        $jadwal->update($data);

        return redirect()
            ->route('admin.jadwal.index')
            ->with('success', 'Jadwal berhasil diperbarui.');
    }

    /**
     * Hapus jadwal.
     */
    public function destroy(Jadwal $jadwal): RedirectResponse
    {
        $jadwal->delete();

        return redirect()
            ->route('admin.jadwal.index')
            ->with('success', 'Jadwal berhasil dihapus.');
    }
}

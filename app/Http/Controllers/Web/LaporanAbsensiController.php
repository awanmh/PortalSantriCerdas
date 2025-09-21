<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AbsensiSiswa;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class LaporanAbsensiController extends Controller
{
    /**
     * Menampilkan halaman laporan absensi siswa dengan filter.
     */
    public function index(Request $request): Response
    {
        // 1. Validasi input filter dari request
        $validated = $request->validate([
            'tanggal' => 'nullable|date_format:Y-m-d',
            'kelas_id' => 'nullable|integer|exists:kelas,id',
        ]);

        // 2. Siapkan nilai default untuk filter
        $filters = [
            'tanggal' => $validated['tanggal'] ?? Carbon::today()->format('Y-m-d'),
            'kelas_id' => isset($validated['kelas_id']) ? (int) $validated['kelas_id'] : null,
        ];

        $laporan = [];

        // 3. Hanya jalankan query laporan jika sebuah kelas telah dipilih
        if ($filters['kelas_id']) {
            // Ambil semua siswa dari kelas yang dipilih, diurutkan berdasarkan nama
            $siswasDiKelas = User::role('siswa')
                ->whereHas('kelas', fn ($query) => $query->where('kelas.id', $filters['kelas_id']))
                ->orderBy('name')
                ->get(['id', 'name']); // Hanya ambil kolom yang dibutuhkan

            // Ambil semua ID siswa di kelas tersebut untuk efisiensi query
            $siswaIds = $siswasDiKelas->pluck('id');

            // Ambil data absensi dengan memfilter langsung pada tanggal di tabel absensi_siswa
            $absensiRecords = AbsensiSiswa::whereIn('user_id', $siswaIds)
                ->whereDate('waktu_absensi', $filters['tanggal']) // <-- Filter yang benar
                ->with('jadwal:id,mata_pelajaran') // Eager load untuk performa
                ->get()
                ->keyBy('user_id'); // Gunakan keyBy untuk memetakan hasil agar mudah diakses

            // 4. Gabungkan data siswa dengan data absensinya
            $laporan = $siswasDiKelas->map(function ($siswa) use ($absensiRecords) {
                $absensi = $absensiRecords->get($siswa->id);

                // Gunakan nullsafe operator (?->) untuk menghindari error jika $absensi null
                return [
                    'id' => $siswa->id,
                    'nama' => $siswa->name,
                    'status' => $absensi?->status ?? 'alfa',
                    'waktu_absensi' => $absensi ? Carbon::parse($absensi->waktu_absensi)->format('H:i') : '-',
                    'mata_pelajaran' => $absensi?->jadwal?->mata_pelajaran ?? '-',
                    'keterangan' => $absensi?->keterangan ?? '-',
                ];
            });
        }

        // 5. Render halaman Inertia dengan data yang diperlukan
        return Inertia::render('Admin/Laporan/Absensi', [
            'filters' => $filters,
            'laporan' => $laporan,
            'kelasOptions' => Kelas::orderBy('nama_kelas')->get(['id', 'nama_kelas']),
        ]);
    }

    /**
     * --- PERBAIKAN DI SINI ---
     * Menampilkan halaman laporan absensi untuk Admin.
     * Method ini dipanggil oleh route 'admin.laporan.absensi.index'.
     */
    public function adminIndex(Request $request): Response
    {
        // Karena logikanya sama, kita hanya perlu memanggil method index() yang sudah ada.
        return $this->index($request);
    }
}


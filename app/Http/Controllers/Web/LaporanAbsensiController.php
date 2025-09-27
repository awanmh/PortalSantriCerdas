<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use App\Models\AbsensiSiswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Database\Eloquent\Builder;

class LaporanAbsensiController extends Controller
{
    /**
     * Menampilkan halaman laporan absensi untuk Admin/IT dan BK.
     */
    public function adminIndex(Request $request): Response
    {
        // 1. Validasi input filter dari request
        $validated = $request->validate([
            'tanggal' => 'nullable|date_format:Y-m-d',
            'kelas_id' => 'nullable|integer|exists:kelas,id',
            'type' => 'required|string|in:siswa,guru',
        ]);

        // 2. Siapkan nilai default dan filter
        $filters = [
            'tanggal' => $validated['tanggal'] ?? Carbon::today()->format('Y-m-d'),
            'kelas_id' => isset($validated['kelas_id']) ? (int) $validated['kelas_id'] : null,
            'type' => $validated['type'],
        ];

        $laporanData = [];

        if ($filters['type'] === 'siswa') {
            $laporanData = $this->getLaporanAbsensiSiswa($filters);
        } elseif ($filters['type'] === 'guru') {
            $laporanData = $this->getLaporanAbsensiGuru($filters);
        }

        // 3. Render halaman Inertia dengan data yang diperlukan
        return Inertia::render('Admin/Laporan/Absensi', [
            'filters' => $filters,
            'laporanData' => $laporanData,
            'kelasOptions' => Kelas::orderBy('nama_kelas')->get(['id', 'nama_kelas']),
        ]);
    }

    /**
     * Helper method untuk mengambil data laporan absensi siswa.
     * [FIXED] Logika query diubah total untuk memperbaiki error 500 dan akurasi data.
     */
    protected function getLaporanAbsensiSiswa(array $filters): array
    {
        if (empty($filters['kelas_id'])) {
            return []; // Jika kelas tidak dipilih, kembalikan array kosong
        }

        return AbsensiSiswa::with([
                'siswa:id,name', // Eager load nama siswa
                'jadwal.mataPelajaran:id,nama' // Eager load nama mapel melalui jadwal
            ])
            ->whereDate('waktu_absensi', $filters['tanggal'])
            ->whereHas('jadwal', function (Builder $query) use ($filters) {
                $query->where('kelas_id', $filters['kelas_id']);
            })
            ->get()
            ->map(function ($absensi) {
                return [
                    'id' => $absensi->id,
                    'nama' => optional($absensi->siswa)->name ?? 'Siswa tidak ditemukan',
                    'status' => $absensi->status,
                    'waktu_absensi' => $absensi->waktu_absensi->format('H:i'),
                    'mata_pelajaran' => optional(optional($absensi->jadwal)->mataPelajaran)->nama ?? 'N/A',
                    'keterangan' => $absensi->keterangan ?? '-',
                ];
            })
            ->toArray();
    }


    /**
     * Helper method untuk mengambil data laporan absensi guru.
     */
    protected function getLaporanAbsensiGuru(array $filters): array
    {
        return AbsensiGuru::with('user:id,name')
            ->whereDate('tanggal', $filters['tanggal'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($absensi) {
                $status = 'Alfa';
                if ($absensi->status) {
                    $status = ucfirst($absensi->status);
                } else {
                    if ($absensi->waktu_masuk && $absensi->waktu_pulang) {
                        $status = 'Hadir Penuh';
                    } elseif ($absensi->waktu_masuk) {
                        $status = 'Hanya Masuk';
                    } elseif ($absensi->waktu_pulang) {
                        $status = 'Hanya Pulang';
                    }
                }

                return [
                    'id' => $absensi->id,
                    'nama_guru' => $absensi->user?->name ?? 'N/A',
                    'tanggal' => Carbon::parse($absensi->tanggal ?? $absensi->created_at)->format('Y-m-d'),
                    'waktu_masuk' => $absensi->waktu_masuk ? Carbon::parse($absensi->waktu_masuk)->format('H:i') : '-',
                    'waktu_pulang' => $absensi->waktu_pulang ? Carbon::parse($absensi->waktu_pulang)->format('H:i') : '-',
                    'status' => $status,
                    'keterangan' => $absensi->keterangan ?? '-',
                ];
            })
            ->toArray();
    }
}

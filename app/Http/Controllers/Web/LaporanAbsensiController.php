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
     *
     * Metode ini mengambil data siswa dari kelas yang dipilih dan menggabungkannya
     * dengan data absensi pada tanggal yang dipilih untuk membuat laporan harian.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
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
                ->get();

            // Ambil semua ID siswa di kelas tersebut untuk efisiensi query
            $siswaIds = $siswasDiKelas->pluck('id');

            // Ambil semua data absensi untuk siswa-siswa tersebut pada tanggal yang dipilih
            // Gunakan keyBy('user_id') untuk memetakan hasil agar mudah diakses
            $absensiRecords = AbsensiSiswa::whereIn('user_id', $siswaIds)
                ->whereHas('jadwal', fn ($query) => $query->where('tanggal', $filters['tanggal']))
                ->with('jadwal:id,mata_pelajaran') // Eager load untuk performa
                ->get()
                ->keyBy('user_id');

            // 4. Gabungkan data siswa dengan data absensinya
            $laporan = $siswasDiKelas->map(function ($siswa) use ($absensiRecords) {
                $absensi = $absensiRecords->get($siswa->id);

                return [
                    'id' => $siswa->id,
                    'nama' => $siswa->name,
                    'status' => $absensi->status ?? 'alfa', // Default 'alfa' jika tidak ada catatan absensi
                    'waktu_absensi' => $absensi ? Carbon::parse($absensi->waktu_absensi)->format('H:i') : '-',
                    'mata_pelajaran' => $absensi->jadwal->mata_pelajaran ?? '-',
                    'keterangan' => $absensi->keterangan ?? '-',
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
}
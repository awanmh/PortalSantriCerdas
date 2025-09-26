<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use App\Models\AbsensiSiswa;
use App\Models\User;
use App\Models\Zona; // <-- 1. Impor model Zona
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Mengambil data dashboard berdasarkan peran pengguna yang sedang login.
     */
    public function index(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $role = $user->roles->first()?->name ?? 'siswa';

        // Ambil status zona aktif dan detail zona
        $zonaAktif = Zona::where('is_active', true)->exists();
        $zonaAktifDetail = Zona::where('is_active', true)
            ->get(['id', 'nama_zona', 'lat', 'lng', 'radius']);

        // Log info
        Log::debug('Cek dashboard user', [
            'user_id' => $user->id,
            'role' => $role,
            'zonaAktif' => $zonaAktif,
            'zonaDetailCount' => $zonaAktifDetail->count(),
        ]);

        // Mengarahkan ke metode yang sesuai berdasarkan peran
        return match ($role) {
            'siswa' => $this->getSiswaDashboard($user, $zonaAktif, $zonaAktifDetail),
            'guru'  => $this->getGuruDashboard($user, $zonaAktif, $zonaAktifDetail),
            'bk'    => $this->getBkDashboard(),
            'it'    => $this->getItDashboard(),
            default => response()->json(['message' => 'Peran tidak dikenali.'], 403),
        };

         $zonaAktif = Zona::where('is_active', true)->exists();

        // tambahkan log ke storage/logs/laravel.log
//         Log::info('Cek zona aktif:', [
//             'zona_aktif' => $zonaAktif,
//             'count_aktif' => Zona::where('is_active', true)->count(),
//             'raw_query' => Zona::all(['id', 'nama_zona', 'is_active'])->toArray(),
//         ]);

//         return response()->json([
//             'success' => true,
//             'message' => 'Dashboard data',
//             'data' => [
//             'zona_aktif' => $zonaAktif,
//             ]
//         ]);

//         Log::info('Dashboard Data', [
//             'zona_aktif' => $zonaAktif,
//             'zones' => Zona::all(['id', 'nama_zona', 'is_active'])
// ]);

    }

    /**
     * Menyiapkan data untuk dashboard Siswa.
     */
    private function getSiswaDashboard(User $user, $zonaAktif, $zonaDetail): JsonResponse
    {
        $today = now()->toDateString();

        $absensiHariIni = AbsensiSiswa::where('siswa_id', $user->id)
            ->whereDate('waktu', $today)
            ->first();

        // 2. Kirim status zona aktif ke siswa juga
       //zonaAktif = Zona::where('is_active', true)->exists();

        return response()->json([
            'absensi_hari_ini' => $absensiHariIni,
            'rekap_absensi'    => $this->getRekapAbsensiSiswa($user->id),
            'zonaAktif'        => $zonaAktif,
            'zonaDetail'       => $zonaDetail,
        ]);
    }

    /**
     * Menyiapkan data untuk dashboard Guru.
     */
    private function getGuruDashboard(User $user, $zonaAktif, $zonaDetail): JsonResponse
{
    $today = now()->toDateString();

    $absensiGuru = AbsensiGuru::where('guru_id', $user->id)
        ->whereDate('tanggal', $today)
        ->first();

   //zonaAktif = Zona::where('is_active', true)->exists();

    Log::info("Guru {$user->id} cek dashboard", [
        'zonaAktif' => $zonaAktif,
        'absensiHariIni' => $absensiGuru,
    ]);

    return response()->json([
        'absensi_masuk_hari_ini'  => $absensiGuru ? ['waktu' => $absensiGuru->jam_masuk] : null,
        'absensi_pulang_hari_ini' => $absensiGuru ? ['waktu' => $absensiGuru->jam_keluar] : null,
        'rekap_absensi'           => $this->getRekapAbsensiGuru($user->id),
        'rekap_siswa'             => $this->getRekapSiswaUntukGuru($user),
        'zonaAktif'               => $zonaAktif,
        'zonaDetail'              => $zonaDetail,
    ]);
}

    /**
     * Menyiapkan data untuk dashboard Bimbingan Konseling (BK).
     */
    private function getBkDashboard(): JsonResponse
    {
        // Logika dashboard untuk BK bisa dikembangkan di sini
        return response()->json(['message' => 'Dashboard BK akan segera tersedia.']);
    }

    /**
     * Menyiapkan data untuk dashboard IT.
     */
    private function getItDashboard(): JsonResponse
    {
        // Logika dashboard untuk IT bisa dikembangkan di sini
        return response()->json([
            'total_pengguna' => User::count(),
            'total_siswa'    => User::role('siswa')->count(),
            'total_guru'     => User::role(['guru', 'bk'])->count(),
            'zona_aktif'     => Zona::where('is_active', true)->count(),
        ]);
    }

    /**
     * Menghitung rekapitulasi absensi untuk seorang siswa.
     */
    private function getRekapAbsensiSiswa(int $siswaId): array
    {
        $rekap = AbsensiSiswa::where('siswa_id', $siswaId)
            ->selectRaw("
                SUM(CASE WHEN keterangan = 'hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN keterangan = 'izin' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN keterangan = 'sakit' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN keterangan = 'alpha' THEN 1 ELSE 0 END) as alpha
            ")->first();

        return [
            'hadir' => (int) ($rekap->hadir ?? 0),
            'izin'  => (int) ($rekap->izin ?? 0),
            'sakit' => (int) ($rekap->sakit ?? 0),
            'alpha' => (int) ($rekap->alpha ?? 0),
        ];
    }

    /**
     * Menghitung rekapitulasi absensi untuk seorang guru.
     */
    private function getRekapAbsensiGuru(int $guruId): array
    {
        $rekap = AbsensiGuru::where('guru_id', $guruId)
            ->selectRaw("
                SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN status = 'izin' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN status = 'sakit' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN status = 'alpha' THEN 1 ELSE 0 END) as alpha
            ")->first();

        return [
            'hadir' => (int) ($rekap->hadir ?? 0),
            'izin'  => (int) ($rekap->izin ?? 0),
            'sakit' => (int) ($rekap->sakit ?? 0),
            'alpha' => (int) ($rekap->alpha ?? 0),
        ];
    }

    /**
     * Menyiapkan rekapitulasi siswa untuk dashboard Guru. (Placeholder)
     */
    private function getRekapSiswaUntukGuru(User $user): array
    {
        // TODO: Implementasikan logika untuk mengambil siswa yang diajar oleh guru ini.
        // Untuk saat ini, kembalikan data kosong.
        return [
            'total_siswa' => 0,
            'sudah_absen' => 0,
            'belum_absen' => 0,
            'siswa'       => [['id' => 1, 'name' => 'Ali', 'status' => 'Hadir'],
                ['id' => 2, 'name' => 'Budi', 'status' => 'Belum Hadir'],
                ['id' => 3, 'name' => 'Citra', 'status' => 'Belum Hadir'],
                ['id' => 4, 'name' => 'Dewi', 'status' => 'Hadir'],
                ['id' => 5, 'name' => 'Eko', 'status' => 'Belum Hadir'],
                ['id' => 6, 'name' => 'Fajar', 'status' => 'Belum Hadir'],
                ['id' => 7, 'name' => 'Gita', 'status' => 'Belum Hadir'],
                ['id' => 8, 'name' => 'Hendra', 'status' => 'Belum Hadir'],
                ['id' => 9, 'name' => 'Indra', 'status' => 'Hadir'],
                ['id' => 10,'name' => 'Joko', 'status' => 'Belum Hadir'],
            ],
        ];
    }
}


<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use App\Models\AbsensiSiswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama setelah pengguna login.
     *
     * @return \Inertia\Response
     */
    public function index(): Response
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load('roles');
        $role = $user->getRoleNames()->first() ?? 'siswa';

        $dashboardData = [];

        // Menggunakan switch untuk memanggil metode yang sesuai berdasarkan peran
        switch ($role) {
            case 'siswa':
                $dashboardData = $this->getSiswaData($user);
                break;
            case 'guru':
                // Anda bisa membuat metode getGuruData($user) dengan logika yang sama
                $dashboardData = $this->getGuruData($user);
                break;
            case 'bk':
                 // Anda bisa membuat metode getBkData($user)
                $dashboardData = []; // Placeholder
                break;
            case 'it':
                 // Anda bisa membuat metode getItData($user)
                $dashboardData = []; // Placeholder
                break;
        }

        // Render komponen Vue 'Dashboard.vue' dan kirimkan semua data yang diperlukan
        return Inertia::render('Dashboard', [
            'auth' => [
                'user' => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames(),
                ],
            ],
            // KIRIM DATA SPESIFIK DASHBOARD SEBAGAI PROP BARU
            'dashboardData' => $dashboardData,
        ]);
    }

    /**
     * Mengambil data yang diperlukan untuk dashboard siswa.
     *
     * @param \App\Models\User $user
     * @return array
     */
    private function getSiswaData(User $user): array
    {
        return [
            'absensi_hari_ini' => AbsensiSiswa::where('user_id', $user->id)
                ->whereDate('tanggal', now()->toDateString())
                ->first(),
            'total_hadir' => AbsensiSiswa::where('user_id', $user->id)
                ->where('status', 'hadir')
                ->count(),
            'total_izin' => AbsensiSiswa::where('user_id', $user->id)
                ->where('status', 'izin')
                ->count(),
            'total_sakit' => AbsensiSiswa::where('user_id', $user->id)
                ->where('status', 'sakit')
                ->count(),
            'total_alpha' => AbsensiSiswa::where('user_id', $user->id)
                ->where('status', 'alpha')
                ->count(),
        ];
    }

    /**
     * Mengambil data yang diperlukan untuk dashboard guru.
     * (Anda bisa melengkapi logika ini sesuai kebutuhan)
     *
     * @param \App\Models\User $user
     * @return array
     */
    private function getGuruData(User $user): array
    {
        return [
            'absensi_masuk_hari_ini' => AbsensiGuru::where('user_id', $user->id)
                ->whereDate('tanggal', now()->toDateString())
                ->where('tipe', 'masuk')
                ->first(),
            'absensi_pulang_hari_ini' => AbsensiGuru::where('user_id', $user->id)
                ->whereDate('tanggal', now()->toDateString())
                ->where('tipe', 'pulang')
                ->first(),
            'total_kehadiran_bulan_ini' => AbsensiGuru::where('user_id', $user->id)
                ->where('status', 'hadir')
                ->whereMonth('tanggal', now()->month)
                ->count(),
        ];
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use App\Models\AbsensiSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->roles->first()->name ?? 'siswa';

        switch ($role) {
            case 'siswa':
                return $this->dashboardSiswa($user);
            case 'guru':
                return $this->dashboardGuru($user);
            case 'bk':
                return $this->dashboardBk($user);
            case 'it':
                return $this->dashboardIt($user);
            default:
                return $this->dashboardSiswa($user);
        }
    }

    private function dashboardSiswa($user)
    {
        $absensiHariIni = AbsensiSiswa::where('user_id', $user->id)
            ->whereDate('tanggal', now()->format('Y-m-d'))
            ->first();

        return response()->json([
            'role' => 'siswa',
            'absensi' => $absensiHariIni,
            'total_absensi_hadir' => AbsensiSiswa::where('user_id', $user->id)
                ->where('status', 'hadir')
                ->count(),
            'total_absensi_izin' => AbsensiSiswa::where('user_id', $user->id)
                ->where('status', 'izin')
                ->count(),
            'total_absensi_sakit' => AbsensiSiswa::where('user_id', $user->id)
                ->where('status', 'sakit')
                ->count(),
            'total_absensi_alpha' => AbsensiSiswa::where('user_id', $user->id)
                ->where('status', 'alpha')
                ->count(),
        ]);
    }

    // ... method untuk role lainnya ...
}
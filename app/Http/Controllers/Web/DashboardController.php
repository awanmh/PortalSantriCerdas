<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use App\Models\AbsensiSiswa;
use App\Models\CatatanPelanggaran;
use App\Models\Jadwal;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\User;
use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Mengelola tampilan dashboard berdasarkan peran pengguna yang sedang login.
 */
class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard yang sesuai berdasarkan peran prioritas pengguna.
     */
    public function index(Request $request): Response
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->loadMissing('roles');

        $rolePriority = ['it', 'admin', 'bk', 'guru', 'siswa'];
        $userRole = null;

        foreach ($rolePriority as $role) {
            if ($user->hasRole($role)) {
                $userRole = $role;
                break;
            }
        }

        return match ($userRole) {
            'it', 'admin' => $this->renderItAdminDashboard(),
            'guru'        => $this->renderGuruDashboard($user),
            'bk'          => $this->renderBkDashboard($user),
            'siswa'       => $this->renderSiswaDashboard($user),
            default       => Inertia::render('Profile/Edit'),
        };
    }

    /**
     * Merender dashboard untuk peran IT/Admin dengan statistik sistem.
     */
    private function renderItAdminDashboard(): Response
    {
        $stats = [
            'total_pengguna'    => User::count(),
            'total_siswa'       => User::role('siswa')->count(),
            'total_guru_bk'     => User::role(['guru', 'bk'])->count(),
            'total_jurusan'     => Jurusan::count(),
            'total_kelas'       => Kelas::count(),
            'total_jadwal_rutin' => Jadwal::where('tipe', 'pelajaran')->count(),
            'total_zona_aktif'  => Zona::count(),
        ];

        return Inertia::render('Dashboard/IT', [
            'stats' => $stats,
        ]);
    }

    /**
     * Merender dashboard untuk peran Guru.
     */
    private function renderGuruDashboard(User $user): Response
    {
        $today = Carbon::today()->toDateString();
        $dayOfWeek = Carbon::today()->isoFormat('dddd');

        $absensiGuruHariIni = AbsensiGuru::where('guru_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        $guruMataPelajaran = $user->subject_taught;

        $jadwalMengajarHariIni = Jadwal::where('guru_id', $user->id)
            ->where(function ($query) use ($today, $dayOfWeek) {
                $query->where('hari', $dayOfWeek)->orWhere('tanggal', $today);
            })
            ->when($guruMataPelajaran, function ($query, $subject) {
    $query->whereHas('mataPelajaran', function ($q) use ($subject) {
        $q->where('nama', $subject);
    });
})

            ->with('kelas:id,nama_kelas')
            ->orderBy('jam_mulai')
            ->get();

        $rekapSiswa = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alfa' => 0];
        $jadwalIdsHariIni = $jadwalMengajarHariIni->pluck('id')->toArray();

        if (!empty($jadwalIdsHariIni)) {
            $absensiSiswaRekap = AbsensiSiswa::whereIn('jadwal_id', $jadwalIdsHariIni)
                ->whereDate('waktu_absensi', $today)
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            $rekapSiswa = [
                'hadir' => $absensiSiswaRekap->get('hadir', 0),
                'izin'  => $absensiSiswaRekap->get('izin', 0),
                'sakit' => $absensiSiswaRekap->get('sakit', 0),
                'alfa'  => $absensiSiswaRekap->get('alfa', 0),
            ];
        }

        return Inertia::render('Dashboard/Guru', [
            'dashboardData' => [
                'absensi_hari_ini' => $absensiGuruHariIni,
                'jadwal_mengajar' => $jadwalMengajarHariIni,
                'rekap_siswa' => $rekapSiswa,
            ],
        ]);
    }

    /**
     * Merender dashboard untuk peran BK.
     */
    private function renderBkDashboard(User $user): Response
    {
        $stats = [
            'pelanggaran_hari_ini' => CatatanPelanggaran::whereDate('tanggal', today())->count(),
            'total_poin_hari_ini' => CatatanPelanggaran::whereDate('tanggal', today())->sum('poin'),
        ];
        $pelanggaranTerbaru = CatatanPelanggaran::with(['siswa:id,name', 'pelapor:id,name'])->latest()->limit(5)->get();
        $siswaBermasalah = User::role('siswa')
            ->whereHas('catatanPelanggaran')
            ->withSum('catatanPelanggaran as total_poin', 'poin')
            ->orderByDesc('total_poin')
            ->limit(5)
            ->get();

        return Inertia::render('Dashboard/BK', [
            'stats' => $stats,
            'pelanggaranTerbaru' => $pelanggaranTerbaru,
            'siswaBermasalah' => $siswaBermasalah,
        ]);
    }

    /**
     * Merender dashboard untuk peran Siswa.
     */
    private function renderSiswaDashboard(User $user): Response
    {
        $user->loadMissing('kelas.jurusan');
        $kelasSiswa = $user->kelas->first();
        $jadwalHariIni = collect();

        if ($kelasSiswa) {
            $today = Carbon::today()->toDateString();
            $dayOfWeek = Carbon::today()->isoFormat('dddd');
            $jadwalHariIni = Jadwal::where('kelas_id', $kelasSiswa->id)
                ->where(function ($query) use ($today, $dayOfWeek) {
                    $query->where('hari', $dayOfWeek)->orWhere('tanggal', $today);
                })
                ->with('guru:id,name')
                ->orderBy('jam_mulai', 'asc')
                ->get();
        }

        return Inertia::render('Dashboard/Siswa', [
            'jadwalHariIni' => $jadwalHariIni,
            'kelasSiswa' => $kelasSiswa,
        ]);
    }
}


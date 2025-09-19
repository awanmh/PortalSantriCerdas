<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use App\Models\AbsensiSiswa; // DITAMBAHKAN
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

/**
 * Mengelola tampilan dashboard berdasarkan peran pengguna.
 */
class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard yang sesuai berdasarkan peran prioritas pengguna.
     */
    public function index(Request $request): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user()->load(['siswa', 'kelas']);

        $rolePriority = ['it', 'bk', 'guru', 'siswa'];
        $userRole = null;

        foreach ($rolePriority as $role) {
            if ($user->hasRole($role)) {
                $userRole = $role;
                break;
            }
        }

        switch ($userRole) {
            case 'it':
                return $this->renderItDashboard();
            case 'guru':
                return $this->renderGuruDashboard($user);
            case 'bk':
                return $this->renderBkDashboard($user);
            case 'siswa':
                return $this->renderSiswaDashboard($user);
            default:
                return Inertia::render('Profile/Edit');
        }
    }

    /**
     * Merender dashboard untuk peran IT dengan statistik lengkap.
     */
    private function renderItDashboard(): Response
    {
        $stats = [
            'total_pengguna' => User::count(),
            'total_siswa'    => User::role('siswa')->count(),
            'total_guru'     => User::role(['guru', 'bk'])->count(),
            'zona_aktif'     => Zona::count(),
            'total_jurusan'  => Jurusan::count(),
            'total_kelas'    => Kelas::count(),
            'total_jadwal'   => Jadwal::where('tipe', 'pelajaran')->count(),
        ];

        return Inertia::render('Dashboard/IT', ['stats' => $stats]);
    }

    /**
     * Merender dashboard untuk peran Guru.
     * --- LOGIKA DIPERBARUI UNTUK MENAMBAHKAN REKAP SISWA ---
     */
    private function renderGuruDashboard(User $user): Response
    {
        $today = Carbon::today()->toDateString();

        $absensiHariIni = AbsensiGuru::where('guru_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        $jadwalMengajarHariIni = Jadwal::where('guru_id', $user->id)
            ->where('tanggal', $today)
            ->with('kelas.jurusan')
            ->orderBy('jam_mulai')
            ->get();

        // --- LOGIKA BARU: MENGAMBIL REKAP ABSENSI SISWA ---
        $rekapSiswa = [];
        $jadwalIds = $jadwalMengajarHariIni->pluck('id');

        if ($jadwalIds->isNotEmpty()) {
            // Hitung status absensi siswa untuk jadwal-jadwal tersebut.
            $absensiSiswa = AbsensiSiswa::whereIn('jadwal_id', $jadwalIds)
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            $rekapSiswa = [
                'hadir' => $absensiSiswa->get('hadir', 0),
                'izin' => $absensiSiswa->get('izin', 0),
                'sakit' => $absensiSiswa->get('sakit', 0),
                'alfa' => $absensiSiswa->get('alfa', 0),
            ];
        }

        return Inertia::render('Dashboard/Guru', [
            'dashboardData' => [
                'absensi_hari_ini' => $absensiHariIni,
                'jadwal_mengajar' => $jadwalMengajarHariIni,
                'rekap_siswa' => $rekapSiswa, // <-- DATA BARU DIKIRIM KE FRONTEND
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

        $pelanggaranTerbaru = CatatanPelanggaran::with(['siswa:id,name', 'pelapor:id,name'])
            ->latest()
            ->limit(5)
            ->get();

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
        $jadwalHariIni = [];
        $kelasSiswa = $user->kelas->first();

        if ($kelasSiswa) {
            $jadwalHariIni = Jadwal::where('kelas_id', $kelasSiswa->id)
                ->where('tanggal', Carbon::today()->toDateString())
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
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

        // Set locale Carbon ke Bahasa Indonesia untuk konsistensi nama hari
        Carbon::setLocale('id_ID');

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
            'total_pengguna'     => User::count(),
            'total_siswa'        => User::role('siswa')->count(),
            'total_guru_bk'      => User::role(['guru', 'bk'])->count(),
            'total_jurusan'      => Jurusan::count(),
            'total_kelas'        => Kelas::count(),
            'total_jadwal_rutin' => Jadwal::where('tipe', 'pelajaran')->count(),
            'total_zona_aktif'   => Zona::count(),
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

        $jadwalQuery = Jadwal::where('guru_id', $user->id)
            ->where(function ($query) use ($today, $dayOfWeek) {
                $query->where('hari', $dayOfWeek)
                      ->orWhere('tanggal', $today);
            })
            ->with(['kelas:id,nama_kelas', 'mataPelajaran:id,nama'])
            ->orderBy('jam_mulai')
            ->get();

        $jadwalIdsHariIni = $jadwalQuery->pluck('id')->toArray();

        $jadwalMengajarHariIni = $jadwalQuery->map(function ($jadwal) {
            return [
                'id' => $jadwal->id,
                'jam_mulai' => $jadwal->jam_mulai->format('H:i'),
                'jam_selesai' => $jadwal->jam_selesai->format('H:i'),
                'kelas' => $jadwal->kelas,
                'mata_pelajaran' => optional($jadwal->mataPelajaran)->nama,
            ];
        });

        $rekapSiswa = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alfa' => 0];

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
        $absensiHariIni = collect(); // Default collection kosong

        if ($kelasSiswa) {
            $today = Carbon::today()->toDateString();
            $dayOfWeek = Carbon::today()->isoFormat('dddd');

            $jadwalQuery = Jadwal::where('kelas_id', $kelasSiswa->id)
                ->where(function ($query) use ($today, $dayOfWeek) {
                    $query->where('hari', $dayOfWeek)
                          ->orWhere('tanggal', 'like', $today);
                })
                ->with(['guru:id,name', 'mataPelajaran:id,nama'])
                ->orderBy('jam_mulai', 'asc')
                ->get();
            
            // [NEW] Mengambil data absensi siswa untuk jadwal hari ini
            $jadwalIds = $jadwalQuery->pluck('id');
            $absensiHariIni = AbsensiSiswa::where('user_id', $user->id)
                ->whereIn('jadwal_id', $jadwalIds)
                ->whereDate('waktu_absensi', $today)
                ->pluck('jadwal_id'); // Hanya ambil ID jadwal yang sudah diabsen

            $jadwalHariIni = $jadwalQuery->map(function ($jadwal) {
                return [
                    'id' => $jadwal->id,
                    'jam_mulai' => $jadwal->jam_mulai->format('H:i'),
                    'jam_selesai' => $jadwal->jam_selesai->format('H:i'),
                    'guru' => $jadwal->guru,
                    'mata_pelajaran' => optional($jadwal->mataPelajaran)->nama,
                ];
            });
        }

        return Inertia::render('Dashboard/Siswa', [
            'jadwalHariIni' => $jadwalHariIni,
            'kelasSiswa' => $kelasSiswa,
            'absensiHariIni' => $absensiHariIni, // Kirim data absensi ke frontend
        ]);
    }
}

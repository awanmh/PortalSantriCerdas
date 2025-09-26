<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AbsensiSiswa;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse; // Tambahkan ini
use Illuminate\Support\Facades\DB;

/**
 * Controller untuk menangani fitur absensi siswa oleh guru.
 */
class AbsensiSiswaController extends Controller
{
    /**
     * Menampilkan halaman untuk mencatat absensi siswa.
     */
    public function index(Request $request): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $today = Carbon::today();
        $dayOfWeek = $today->isoFormat('dddd');

        $jadwalMengajar = Jadwal::where('guru_id', $user->id)
            ->where('tipe', 'pelajaran')
            ->where(function ($query) use ($today, $dayOfWeek) {
                $query->where('hari', $dayOfWeek)
                      ->orWhere('tanggal', $today->toDateString());
            })
            ->with('kelas:id,nama_kelas')
            ->orderBy('jam_mulai')
            ->get();

        return Inertia::render('Guru/CetakAbsensiSiswa', [
            'jadwalMengajarHariIni' => $jadwalMengajar,
            'todayDate' => $today->isoFormat('D MMMM YYYY'),
        ]);
    }

    /**
     * Mengambil data siswa dan status absensi mereka untuk jadwal tertentu.
     */
    public function getDataForAbsensi(Request $request, Jadwal $jadwal): JsonResponse
    {
        if ($jadwal->guru_id !== $request->user()->id) {
            abort(403, 'Akses ditolak.');
        }

        if (!$jadwal->kelas_id) {
            return response()->json(['error' => 'Jadwal ini tidak terhubung dengan kelas manapun.'], 404);
        }
        
        $jadwal->load('kelas');

        $siswaDiKelas = $jadwal->kelas->users()
            ->role('siswa')
            ->orderBy('name')
            ->get(['users.id', 'users.name']);
        
        $absensiSudahAda = AbsensiSiswa::where('jadwal_id', $jadwal->id)
            ->whereDate('waktu_absensi', Carbon::today())
            ->whereIn('user_id', $siswaDiKelas->pluck('id'))
            ->get()
            ->keyBy('user_id');

        $dataSiswa = $siswaDiKelas->map(function ($siswa) use ($absensiSudahAda) {
            $absensi = $absensiSudahAda->get($siswa->id);
            return [
                'id' => $siswa->id,
                'name' => $siswa->name,
                'status' => $absensi->status ?? 'alfa',
                'keterangan' => $absensi->keterangan ?? null,
            ];
        });

        return response()->json([
            'kelas_name' => $jadwal->kelas->nama_kelas,
            'mata_pelajaran' => $jadwal->mata_pelajaran,
            'siswa' => $dataSiswa,
        ]);
    }

    /**
     * Menyimpan atau memperbarui data absensi siswa.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $validated = $request->validate([
            'jadwal_id' => [
                'required',
                Rule::exists('jadwal', 'id')->where(fn ($query) => $query->where('guru_id', $user->id)),
            ],
            'absensi_data' => ['required', 'array'],
            'absensi_data.*.user_id' => ['required', 'exists:users,id'],
            'absensi_data.*.status' => ['required', 'in:hadir,sakit,izin,alfa'],
            'absensi_data.*.keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $jadwalId = $validated['jadwal_id'];
        $today = Carbon::today()->toDateString();

        DB::transaction(function () use ($validated, $jadwalId, $today) {
            foreach ($validated['absensi_data'] as $data) {
                // Cari absensi yang sudah ada untuk siswa, jadwal, dan tanggal ini
                $absensi = AbsensiSiswa::where('user_id', $data['user_id'])
                    ->where('jadwal_id', $jadwalId)
                    ->whereDate('waktu_absensi', $today)
                    ->first();

                if ($absensi) {
                    // Jika ada, perbarui datanya
                    $absensi->update([
                        'status' => $data['status'],
                        'keterangan' => $data['keterangan'],
                    ]);
                } else {
                    // Jika tidak ada, buat data baru
                    AbsensiSiswa::create([
                        'user_id' => $data['user_id'],
                        'jadwal_id' => $jadwalId,
                        'status' => $data['status'],
                        'keterangan' => $data['keterangan'],
                        'waktu_absensi' => now(),
                    ]);
                }
            }
        });

        // Alihkan kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', 'Absensi siswa berhasil disimpan.');
    }
}
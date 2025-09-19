<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AbsensiSiswa;
use App\Models\Jadwal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // DITAMBAHKAN
use Illuminate\Validation\Rule;       // DITAMBAHKAN
use Inertia\Inertia;
use Inertia\Response;

class AbsensiController extends Controller
{
    /**
     * Menampilkan halaman untuk melakukan absensi berdasarkan jadwal tertentu.
     * Siswa akan diarahkan ke sini saat mengklik jadwal di dashboard.
     */
    public function create(Jadwal $jadwal): Response|RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Cek apakah siswa sudah melakukan absensi untuk jadwal ini
        $sudahAbsen = AbsensiSiswa::where('user_id', $user->id)
            ->where('jadwal_id', $jadwal->id)
            ->exists();
            
        // Jika sudah absen, kembalikan ke dashboard dengan pesan.
        if ($sudahAbsen) {
            return redirect()->route('dashboard')->with('info', 'Anda sudah melakukan absensi untuk jadwal ini.');
        }

        // Jika belum, tampilkan halaman absensi dengan data jadwal yang relevan.
        return Inertia::render('Absensi/Create', [
            // Eager load relasi untuk efisiensi
            'jadwal' => $jadwal->load('guru:id,name'),
            'sudahAbsen' => $sudahAbsen,
        ]);
    }

    /**
     * Menyimpan data absensi baru ke dalam database, termasuk bukti foto.
     */
    public function store(Request $request, Jadwal $jadwal): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // --- VALIDASI DIPERBARUI ---
        $validated = $request->validate([
            'status' => ['required', Rule::in(['hadir', 'sakit', 'izin'])],
            'keterangan' => 'nullable|string|max:255',
            // Bukti foto wajib jika statusnya 'hadir'
            'bukti_foto' => ['required_if:status,hadir', 'nullable', 'image', 'max:2048'],
        ]);

        // Mencegah absensi ganda (double check untuk keamanan)
        if (AbsensiSiswa::where('user_id', $user->id)->where('jadwal_id', $jadwal->id)->exists()) {
            return redirect()->route('dashboard')->with('error', 'Gagal, Anda sudah absen sebelumnya.');
        }

        // --- LOGIKA PENYIMPANAN FOTO ---
        $fotoPath = null;
        if ($request->hasFile('bukti_foto')) {
            // Simpan file di storage/app/public/absensi_bukti
            // Pastikan Anda sudah menjalankan `php artisan storage:link`
            $fotoPath = $request->file('bukti_foto')->store('absensi_bukti', 'public');
        }

        // Simpan data absensi ke database
        AbsensiSiswa::create([
            'user_id' => $user->id,
            'jadwal_id' => $jadwal->id,
            'status' => $validated['status'],
            'waktu_absensi' => Carbon::now(),
            'keterangan' => $validated['keterangan'],
            'bukti_foto_path' => $fotoPath,
        ]);

        return redirect()->route('dashboard')->with('success', 'Absensi berhasil dicatat!');
    }
}
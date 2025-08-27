<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalController extends Controller
{
    //
    public function index()
    {
        // Untuk sekarang, kita gunakan data dummy
        $jadwals = [
            ['id' => 1, 'hari' => 'Senin', 'jam' => '07:00 - 08:30', 'mapel' => 'Fikih', 'guru' => 'Ustadz Ahmad'],
            ['id' => 2, 'hari' => 'Senin', 'jam' => '08:30 - 10:00', 'mapel' => 'Aqidah Akhlak', 'guru' => 'Ustadz Hasan'],
            ['id' => 3, 'hari' => 'Selasa', 'jam' => '07:00 - 08:30', 'mapel' => 'Bahasa Arab', 'guru' => 'Ustadzah Fatimah'],
            ['id' => 4, 'hari' => 'Selasa', 'jam' => '08:30 - 10:00', 'mapel' => 'Al-Qur\'an Hadits', 'guru' => 'Ustadz Abdullah'],
        ];

        return Inertia::render('Jadwal/Index', [
            'jadwals' => $jadwals
        ]);
    }
    public function create()
    {
        $kelas = Kelas::all();
        $jurusan = Jurusan::all();
        return view('jadwal.create', compact('kelas', 'jurusan'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'judul_event' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'kelas_id' => 'required|exist:jurusan,id',
            'jenjang' => 'required|in:10,11,12',
            'tipe' => 'required|in:pelajaran,event',
        ]);
        Jadwal::create([
            'judul_event' => $request->judul_event,
            'deskripsi' => $request->deskripsi,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'guru_id' => Auth::id(),
            'kelas_id' => $request->kelas_id,
            'jurusan_id' => $request->jurusan_id,
            'jenjang' => $request->jenjang,
            'tipe' => $request->tipe,

        ]);
        return redirect()->route('jadwal.index')->with('SUKSES', 'JADWAL BERHASIL DI TAMBAHKAN');
    }

    public function destroy($id)
    {
        $jadwal = Jadwal::findOrFaiil($id);
        $jadwal->delete();
        return redirect()->route('jadwal.index')->with('SUKSES', 'JADWAL BERHASIL DI HAPUS');
    }
}
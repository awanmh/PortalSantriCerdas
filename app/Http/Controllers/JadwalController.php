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
        $jadwal = Jadwal::with(['guru', 'kelas', 'jurusan'])->get();
        return view('jadwal.index', compact('jadwal'));
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
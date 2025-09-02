<?php

namespace App\Http\Controllers\Api;

use App\Models\Jadwal;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JadwalController extends Controller
{
    /**
     * Tampilkan semua jadwal
     */
    public function index(Request $request)
    {
        $query = Jadwal::with('kelas', 'guru');

        // Filter berdasarkan kelas jika role guru atau siswa
        if ($request->has('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Filter berdasarkan hari
        if ($request->has('hari')) {
            $query->where('hari', $request->hari);
        }

        $jadwal = $query->orderBy('hari')->orderBy('jam_mulai')->get();

        return response()->json($jadwal);
    }

    /**
     * Tambah jadwal baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kelas_id' => 'required|exists:kelas,id',
            'guru_id' => 'required|exists:users,id',
            'mata_pelajaran' => 'required|string|max:255',
            'hari' => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Pastikan guru_id adalah guru
        $guru = \App\Models\User::find($request->guru_id);
        if (!$guru->hasRole('guru')) {
            return response()->json([
                'message' => 'User yang dipilih bukan guru'
            ], 400);
        }

        $jadwal = Jadwal::create($request->all());

        return response()->json([
            'message' => 'Jadwal berhasil ditambahkan',
            'data' => $jadwal
        ], 201);
    }
}
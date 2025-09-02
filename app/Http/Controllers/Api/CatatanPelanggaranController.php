<?php

namespace App\Http\Controllers\Api;

use App\Models\CatatanPelanggaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CatatanPelanggaranController extends Controller
{
    /**
     * Tampilkan semua catatan pelanggaran
     */
    public function index(Request $request)
    {
        $query = CatatanPelanggaran::with('siswa');

        // Filter berdasarkan kelas jika role guru
        if (Auth::user()->hasRole('guru')) {
            $kelasId = $request->query('kelas_id');
            if ($kelasId) {
                $siswaIds = User::whereHas('kelas', function ($query) use ($kelasId) {
                    $query->where('kelas_id', $kelasId);
                })->pluck('id');

                $query->whereIn('user_id', $siswaIds);
            }
        }

        // Filter berdasarkan tanggal
        if ($request->has('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $catatan = $query->orderBy('tanggal', 'desc')->paginate(10);

        return response()->json($catatan);
    }

    /**
     * Tambah catatan pelanggaran baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'tanggal' => 'required|date',
            'jenis_pelanggaran' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tingkat_keparahan' => 'required|in:ringan,sedang,berat',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Pastikan user_id adalah siswa
        $siswa = User::find($request->user_id);
        if (!$siswa->hasRole('siswa')) {
            return response()->json([
                'message' => 'User yang dipilih bukan siswa'
            ], 400);
        }

        $catatan = CatatanPelanggaran::create([
            'user_id' => $request->user_id,
            'tanggal' => $request->tanggal,
            'jenis_pelanggaran' => $request->jenis_pelanggaran,
            'deskripsi' => $request->deskripsi,
            'tingkat_keparahan' => $request->tingkat_keparahan,
            'guru_id' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Catatan pelanggaran berhasil ditambahkan',
            'data' => $catatan
        ], 201);
    }

    /**
     * Update catatan pelanggaran
     */
    public function update(Request $request, $id)
    {
        $catatan = CatatanPelanggaran::find($id);

        if (!$catatan) {
            return response()->json(['message' => 'Catatan tidak ditemukan'], 404);
        }

        // Hanya guru yang membuat atau BK yang bisa mengedit
        if ($catatan->guru_id != Auth::id() && !Auth::user()->hasRole('bk')) {
            return response()->json(['message' => 'Anda tidak memiliki izin untuk mengedit catatan ini'], 403);
        }

        $validator = Validator::make($request->all(), [
            'tanggal' => 'sometimes|required|date',
            'jenis_pelanggaran' => 'sometimes|required|string|max:255',
            'deskripsi' => 'sometimes|required|string',
            'tingkat_keparahan' => 'sometimes|required|in:ringan,sedang,berat',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $catatan->update($request->only([
            'tanggal',
            'jenis_pelanggaran',
            'deskripsi',
            'tingkat_keparahan'
        ]));

        return response()->json([
            'message' => 'Catatan pelanggaran berhasil diperbarui',
            'data' => $catatan
        ]);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        $query = CatatanPelanggaran::query();

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
            'siswa' => 'required|string|max:255', // sesuai migration
            'deskripsi' => 'nullable|string',
            'tingkat' => 'required|in:ringan,sedang,berat',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $catatan = CatatanPelanggaran::create([
            'siswa' => $request->siswa,
            'guru_bk' => Auth::user()->name, // simpan nama guru BK / guru pelapor
            'tanggal' => now()->toDateString(),
            'deskripsi' => $request->deskripsi,
            'tingkat' => $request->tingkat,
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

        $validator = Validator::make($request->all(), [
            'siswa' => 'sometimes|required|string|max:255',
            'tanggal' => 'sometimes|required|date',
            'deskripsi' => 'sometimes|required|string',
            'tingkat' => 'sometimes|required|in:ringan,sedang,berat',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $catatan->update($request->only([
            'siswa',
            'tanggal',
            'deskripsi',
            'tingkat'
        ]));

        return response()->json([
            'message' => 'Catatan pelanggaran berhasil diperbarui',
            'data' => $catatan
        ]);
    }
}

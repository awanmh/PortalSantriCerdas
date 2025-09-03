<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AbsensiGuru;

class AbsensiGuruController extends Controller
{
    public function index()
    {
        return AbsensiGuru::with('guru')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'guru_id' => 'required|exists:users,id',
            'tanggal' => 'required|date',
            'status' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        $absensi = AbsensiGuru::create($validated);
        return response()->json($absensi, 201);
    }
    public function update(Request $request, $id)
    {
        $absensi = AbsensiGuru::findOrFail($id);
        $absensi->update($request->all());
        return response()->json($absensi);
    }
    public function destroy($id)
    {
        AbsensiGuru::destroy($id);
        return response()->json(['Pesan' => 'DATA ABSENSI GURU DIHAPUS']);
    }
}

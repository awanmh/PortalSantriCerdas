<?php

namespace App\Http\Controllers;

use App\Models\AbsensiSiswa;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;

class AbsensiSiswaController extends Controller
{
    // GET /api/absensi-siswa
    public function index()
    {
        return AbsensiSiswa::with(['siswa:id,name', 'jadwal:id,nama'])->latest()->get();
    }

    // POST /api/absensi-siswa
    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:users,id',
            'jadwal_id' => 'nullable|exists:jadwal,id',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'foto' => 'nullable|image|max:2048',
            'keterangan' => 'nullable|string',
        ]);

        $path = null;
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('absensi', 'public');
        }

        $absen = AbsensiSiswa::create([
            'siswa_id' => $request->siswa_id,
            'jadwal_id' => $request->jadwal_id,
            'waktu' => now(),
            'foto_path' => $path,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'valid_zona' => false, // nanti diganti pakai PostGIS check
            'device_info' => $request->device_info ? json_decode($request->device_info, true) : null,
            'keterangan' => $request->keterangan,
        ]);

        return response()->json([
            'ok' => true,
            'data' => $absen,
        ]);
    }

    // GET /api/absensi-siswa/{id}
    public function show($id)
    {
        return AbsensiSiswa::with(['siswa:id,name', 'jadwal:id,nama'])->findOrFail($id);
    }

    // PUT /api/absensi-siswa/{id}
    public function update(Request $request, $id)
    {
        $absen = AbsensiSiswa::findOrFail($id);

        $absen->update($request->only(['keterangan', 'valid_zona']));

        return response()->json(['ok' => true, 'data' => $absen]);
    }

    // DELETE /api/absensi-siswa/{id}
    public function destroy($id)
    {
        $absen = AbsensiSiswa::findOrFail($id);
        $absen->delete();

        return response()->json(['ok' => true, 'message' => 'deleted']);
    }
}

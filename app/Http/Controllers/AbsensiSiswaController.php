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
    // POST /api/absensi-siswa
    public function store(Request $request)
    {
        $request->validate([
            'siswa_id'   => 'required|exists:users,id',
            'jadwal_id'  => 'nullable|exists:jadwal,id',
            'lat'        => 'required|numeric',
            'lng'        => 'required|numeric',
            'foto'       => 'nullable|image|max:2048',
            'device_info' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        // Simpan foto absensi (jika ada)
        $path = null;
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('absensi', 'public');
        }

        // Titik pusat sekolah (contoh koordinat)
        $schoolLat = -7.2756;
        $schoolLng = 112.6426;
        $radius = 100; // meter

        // Hitung jarak siswa dari sekolah
        $distance = $this->calculateDistance(
            $schoolLat,
            $schoolLng,
            $request->lat,
            $request->lng
        );

        $validZona = $distance <= $radius;

        $absen = AbsensiSiswa::create([
            'siswa_id'    => $request->siswa_id,
            'jadwal_id'   => $request->jadwal_id,
            'waktu'       => now(),
            'foto_path'   => $path,
            'lat'         => $request->lat,
            'lng'         => $request->lng,
            'valid_zona'  => $validZona,
            'device_info' => $request->device_info ? json_decode($request->device_info, true) : null,
            'keterangan'  => $request->keterangan,
        ]);

        return response()->json([
            'ok'       => $validZona,
            'message'  => $validZona ? 'Absensi berhasil' : 'Di luar radius sekolah (absensi tidak sah)',
            'distance' => round($distance, 2) . ' meter',
            'data'     => $absen,
        ]);
    }

    // Haversine Formula untuk menghitung jarak
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
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
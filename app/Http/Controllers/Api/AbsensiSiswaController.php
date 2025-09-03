<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller; // Pastikan ini di-import
use App\Models\AbsensiSiswa;
use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AbsensiSiswaController extends Controller
{
    public function absenSiswa(Request $request)
    {
        // 1. VALIDASI DIPERBAIKI: Menggunakan 'lat' dan 'lng'
        $validator = Validator::make($request->all(), [
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'lat'  => 'required|numeric|between:-90,90',
            'lng'  => 'required|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Cek apakah sudah absen hari ini
        $sudahAbsen = AbsensiSiswa::where('siswa_id', Auth::id())
            ->whereDate('waktu', now())
            ->exists();

        if ($sudahAbsen) {
            return response()->json(['message' => 'Anda sudah melakukan absensi hari ini.'], 400);
        }

        $zona = Zona::where('is_active', true)->first();
        if (!$zona) {
            return response()->json(['message' => 'Zona absensi belum diatur'], 400);
        }

        $distance = $this->calculateDistance(
            $request->lat, // Menggunakan lat
            $request->lng, // Menggunakan lng
            $zona->lat,
            $zona->lng
        );

        $dalamZona = $distance <= $zona->radius;

        if (!$dalamZona) {
            return response()->json([
                'message' => 'Anda berada di luar radius absensi',
                'distance' => round($distance, 2),
                'radius' => $zona->radius
            ], 403);
        }

        $fotoPath = $request->file('foto')->store('absensi/siswa', 'public');

        // 3. PEMBUATAN DATA DIPERBAIKI: Kolom disesuaikan dengan migrasi
        $absensi = AbsensiSiswa::create([
            'siswa_id'    => Auth::id(),
            'waktu'       => now(),
            'foto_path'   => $fotoPath,
            'lat'         => $request->lat,
            'lng'         => $request->lng,
            'valid_zona'  => $dalamZona,
            'device_info' => ['user_agent' => $request->userAgent()],
            'keterangan'  => 'Hadir via API',
        ]);

        return response()->json([
            'message' => 'Absensi berhasil',
            'data' => $absensi
        ], 201); // Gunakan status 201 Created
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // dalam meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Hasil sudah dalam meter
    }
}

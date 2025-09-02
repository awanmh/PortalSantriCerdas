// app/Http/Controllers/Api/AbsensiSiswaController.php
<?php

namespace App\Http\Controllers\Api;

use App\Models\AbsensiSiswa;
use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AbsensiSiswaController extends Controller
{
    public function absenSiswa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $zona = Zona::where('is_active', true)->first();
        if (!$zona) {
            return response()->json([
                'message' => 'Zona absensi belum diatur'
            ], 400);
        }

        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $zona->latitude,
            $zona->longitude
        );

        if ($distance > $zona->radius) {
            return response()->json([
                'message' => 'Anda berada di luar radius absensi',
                'distance' => round($distance, 2),
                'radius' => $zona->radius
            ], 403);
        }

        $fotoPath = $request->file('foto')->store('absensi/siswa', 'public');

        $absensi = AbsensiSiswa::create([
            'user_id' => Auth::id(),
            'tanggal' => now()->format('Y-m-d'),
            'jam_masuk' => now(),
            'foto' => $fotoPath,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'status' => 'hadir'
        ]);

        return response()->json([
            'message' => 'Absensi berhasil',
            'data' => $absensi
        ]);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance * 1000; // meter
    }
}
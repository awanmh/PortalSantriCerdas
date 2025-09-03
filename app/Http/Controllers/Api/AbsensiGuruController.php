<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AbsensiGuruController extends Controller
{
    public function absenMasuk(Request $request)
    {
        $validator = Validator::make($request->all(), [
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
            $zona->lat,
            $zona->lng
        );

        if ($distance > $zona->radius) {
            return response()->json([
                'message' => 'Anda berada di luar radius absensi',
                'distance' => round($distance, 2),
                'radius' => $zona->radius
            ], 403);
        }

        $absensiHariIni = AbsensiGuru::where('guru_id', Auth::id())
            ->whereDate('tanggal', now()->format('Y-m-d'))
            ->first();

        if ($absensiHariIni && $absensiHariIni->jam_masuk) {
            return response()->json([
                'message' => 'Anda sudah melakukan absensi masuk hari ini'
            ], 400);
        }

        $absensi = AbsensiGuru::updateOrCreate(
            [
                'guru_id' => Auth::id(),
                'tanggal' => now()->format('Y-m-d'),
            ],
            [
                'jam_masuk' => now(),
                'lat_masuk' => $request->latitude,
                'lng_masuk' => $request->longitude,
            ]
        );

        return response()->json([
            'message' => 'Absensi masuk berhasil',
            'data' => $absensi
        ], 201);
    }

    public function absenPulang(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $zona = Zona::where('is_active', true)->first();
        if (!$zona) {
            return response()->json(['message' => 'Zona absensi belum diatur'], 400);
        }

        $distance = $this->calculateDistance($request->latitude, $request->longitude, $zona->lat, $zona->lng);

        if ($distance > $zona->radius) {
            return response()->json(['message' => 'Anda berada di luar radius absensi'], 403);
        }

        $absensiHariIni = AbsensiGuru::where('guru_id', Auth::id())
            ->whereDate('tanggal', now()->format('Y-m-d'))
            ->first();

        if (!$absensiHariIni || !$absensiHariIni->jam_masuk) {
            return response()->json(['message' => 'Anda belum melakukan absensi masuk hari ini'], 400);
        }

        if ($absensiHariIni->jam_keluar) {
            return response()->json(['message' => 'Anda sudah melakukan absensi pulang hari ini'], 400);
        }

        $absensiHariIni->update([
            'jam_keluar' => now(),
            'lat_pulang' => $request->latitude,
            'lng_pulang' => $request->longitude,
        ]);

        return response()->json([
            'message' => 'Absensi pulang berhasil',
            'data' => $absensiHariIni
        ]);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius bumi dalam km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;
        return $distance * 1000; // Mengembalikan jarak dalam meter
    }
}


<?php

namespace App\Http\Controllers\Api;

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

        $absensiHariIni = AbsensiGuru::where('user_id', Auth::id())
            ->whereDate('tanggal', now()->format('Y-m-d'))
            ->first();

        if ($absensiHariIni && $absensiHariIni->jam_masuk) {
            return response()->json([
                'message' => 'Anda sudah melakukan absensi masuk hari ini'
            ], 400);
        }

        if ($absensiHariIni) {
            $absensiHariIni->update([
                'jam_masuk' => now(),
                'latitude_masuk' => $request->latitude,
                'longitude_masuk' => $request->longitude
            ]);
            $absensi = $absensiHariIni;
        } else {
            $absensi = AbsensiGuru::create([
                'user_id' => Auth::id(),
                'tanggal' => now()->format('Y-m-d'),
                'jam_masuk' => now(),
                'latitude_masuk' => $request->latitude,
                'longitude_masuk' => $request->longitude
            ]);
        }

        return response()->json([
            'message' => 'Absensi masuk berhasil',
            'data' => $absensi
        ]);
    }

    // ... method absenPulang dan calculateDistance ...
}
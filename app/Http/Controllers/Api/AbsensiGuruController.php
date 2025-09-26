<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use App\Models\Zona;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AbsensiGuruController extends Controller
{
    /**
     * Menyimpan data absensi (masuk atau pulang) untuk guru.
     *
     * Metode ini menerima data dari pemindai QR, melakukan serangkaian validasi
     * (QR, lokasi), dan kemudian mencatat waktu absensi ke database.
     */
    public function store(Request $request): JsonResponse
    {
        // 1. Validasi input: pastikan semua data yang dibutuhkan ada dan valid.
        $validated = $request->validate([
            'qr_data' => 'required|string',
            'tipe' => ['required', Rule::in(['masuk', 'pulang'])],
            'location' => 'required|array',
            'location.lat' => 'required|numeric|between:-90,90',
            'location.lng' => 'required|numeric|between:-180,180',
        ]);

        // 2. Validasi QR Code: Pastikan data QR valid.
        // TODO: Implementasikan validasi QR yang lebih aman, misalnya dengan memeriksa
        // token terenkripsi atau timestamp yang ada di dalam QR code.
        if ($validated['qr_data'] !== 'SMK-ALIKHLASH-ABSENSI-GURU') {
            return response()->json(['message' => 'QR Code tidak valid atau sudah kedaluwarsa.'], 422);
        }

        // 3. Validasi Zona & Jarak
        $zonaAktif = Zona::where('is_active', true)->first();
        if (!$zonaAktif) {
            return response()->json(['message' => 'Saat ini tidak ada zona absensi yang aktif.'], 400);
        }

        $distance = $this->calculateDistance(
            $zonaAktif->lat, $zonaAktif->lng,
            $validated['location']['lat'], $validated['location']['lng']
        );

        if ($distance > $zonaAktif->radius) {
            return response()->json(['message' => 'Anda berada di luar zona absensi yang diizinkan.'], 403);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $today = now()->toDateString();

        // 4. Ambil atau buat data absensi baru untuk guru pada hari ini.
        $absensi = AbsensiGuru::firstOrNew([
            'user_id' => $user->id,
            'tanggal' => $today,
        ]);

        // 5. Proses berdasarkan tipe absensi (masuk atau pulang).
        if ($validated['tipe'] === 'masuk') {
            if ($absensi->waktu_masuk) {
                return response()->json(['message' => 'Anda sudah melakukan absensi masuk hari ini.'], 409); // 409 Conflict
            }
            $absensi->waktu_masuk = now();
            $absensi->lokasi_masuk = json_encode($validated['location']);
        } else { // tipe 'pulang'
            if (!$absensi->waktu_masuk) {
                return response()->json(['message' => 'Anda harus melakukan absensi masuk terlebih dahulu.'], 400);
            }
            if ($absensi->waktu_pulang) {
                return response()->json(['message' => 'Anda sudah melakukan absensi pulang hari ini.'], 409);
            }
            $absensi->waktu_pulang = now();
            $absensi->lokasi_pulang = json_encode($validated['location']);
        }

        $absensi->status = 'hadir';
        $absensi->save();

        return response()->json([
            'message' => "Absensi {$validated['tipe']} berhasil direkam.",
            'data' => $absensi
        ]);
    }

    /**
     * Menghitung jarak antara dua titik geografis menggunakan formula Haversine.
     *
     * @return float Jarak dalam meter.
     */
    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // Radius bumi dalam meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}


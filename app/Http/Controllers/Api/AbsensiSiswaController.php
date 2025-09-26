<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AbsensiSiswa;
use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AbsensiSiswaController extends Controller
{
    /**
     * Menyimpan data absensi baru dari siswa.
     */
    public function store(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'foto' => 'required|string', // Menerima base64 string
            'keterangan' => 'required|in:hadir,izin,sakit,alpha',
        ]);

        $user = Auth::user();
        $today = now()->toDateString();

        // Cek apakah siswa sudah absen hari ini
        $existingAbsensi = AbsensiSiswa::where('siswa_id', $user->id)
            ->whereDate('waktu', $today)
            ->first();

        if ($existingAbsensi) {
            return response()->json(['message' => 'Anda sudah melakukan absensi hari ini.'], 409); // 409 Conflict
        }

        // Cek validasi zona
        $zonaAktif = Zona::where('is_active', true)->first();
        if (!$zonaAktif) {
             return response()->json(['message' => 'Tidak ada zona absensi yang aktif.'], 400);
        }

        $distance = $this->calculateDistance(
            $zonaAktif->lat, $zonaAktif->lng,
            $request->lat, $request->lng
        );

        if ($distance > $zonaAktif->radius) {
            return response()->json(['message' => 'Anda berada di luar zona absensi yang diizinkan.'], 403); // 403 Forbidden
        }

        // Proses dan simpan foto
        $fotoData = $request->foto;
        $fotoName = 'absensi/' . $user->id . '_' . time() . '.png';
        
        // Menghapus header data URL (misal: "data:image/png;base64,")
        if (preg_match('/^data:image\/(\w+);base64,/', $fotoData, $type)) {
            $fotoData = substr($fotoData, strpos($fotoData, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif

            if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
                throw new \Exception('invalid image type');
            }
            $fotoData = base64_decode($fotoData);

            if ($fotoData === false) {
                 throw new \Exception('base64_decode failed');
            }
        } else {
             throw new \Exception('did not match data URI with image data');
        }

        Storage::disk('public')->put($fotoName, $fotoData);

        // Simpan data absensi
        $absensi = AbsensiSiswa::create([
            'siswa_id' => $user->id,
            'waktu' => now(),
            'lat' => $request->lat,
            'lng' => $request->lng,
            'foto_path' => $fotoName,
            'keterangan' => $request->keterangan,
        ]);

        return response()->json([
            'message' => 'Absensi berhasil disimpan.',
            'data' => $absensi
        ], 201);
    }
    
    /**
     * Menghitung jarak antara dua titik geografis (Haversine formula).
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371000; // meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}

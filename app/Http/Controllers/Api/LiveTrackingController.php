<?php

namespace App\Http\Controllers\Api;

use App\Events\LokasiSiswaDiperbarui;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LiveTrackingController extends Controller
{
    /**
     * Menerima pembaruan lokasi dari siswa, menyimpannya, dan menyiarkannya.
     */
    public function updateLokasi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        /** @var \App\Models\User $siswa */
        $siswa = $request->user();

        // --- LOGIKA BARU DITAMBAHKAN DI SINI ---
        // Simpan lokasi terakhir dan timestamp ke database.
        // Ini penting agar command terjadwal bisa mendeteksi siswa yang tidak aktif.
        $siswa->update([
            'last_seen_at' => now(),
            'last_known_location' => $validated,
        ]);

        // Memicu event untuk disiarkan secara real-time ke guru/bk/it
        broadcast(new LokasiSiswaDiperbarui($siswa, $validated))->toOthers();

        return response()->json(['message' => 'Lokasi berhasil diperbarui.']);
    }
}
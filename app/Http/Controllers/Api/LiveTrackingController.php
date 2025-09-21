<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LiveTrackingController extends Controller
{
    public function updateLokasi(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'lat' => 'required|numeric|between:-90,90',
                'lng' => 'required|numeric|between:-180,180',
            ]);

            $user = Auth::user();

            // Debug: Log user dan data
            Log::info('Update lokasi attempt', [
                'user_id' => $user->id,
                'user_roles' => $user->getRoleNames(),
                'data' => $validated
            ]);

            // Pastikan user adalah siswa
            if (!$user->hasRole('siswa')) {
                return response()->json([
                    'message' => 'Hanya siswa yang dapat mengupdate lokasi'
                ], 403);
            }

            // Update data user
            $user->update([
                'last_seen_at' => now(),
                'latitude' => $validated['lat'],
                'longitude' => $validated['lng'],
                'last_known_location' => [
                    'lat' => $validated['lat'],
                    'lng' => $validated['lng']
                ],
            ]);

            // Coba broadcasting (jika terkonfigurasi)
            try {
                if (class_exists('App\Events\LokasiSiswaDiperbarui')) {
                    broadcast(new \App\Events\LokasiSiswaDiperbarui($user, $validated))->toOthers();
                }
            } catch (\Exception $e) {
                Log::warning('Broadcasting failed: ' . $e->getMessage());
            }

            return response()->json([
                'message' => 'Lokasi berhasil diperbarui',
                'data' => $validated
            ]);

        } catch (\Exception $e) {
            Log::error('Error updateLokasi: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan server',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
            ], 500);
        }
    }
}
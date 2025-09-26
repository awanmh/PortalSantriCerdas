<?php

namespace App\Http\Controllers\Api;

use App\Events\LokasiSiswaDiperbarui;
use App\Events\SesiLiveDimulai;
use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LiveAbsensiController extends Controller
{
    /**
     * Memulai sesi absensi live oleh seorang guru untuk kelas tertentu.
     * Ini akan mem-broadcast event ke semua siswa di kelas tersebut.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function mulaiSesi(Request $request)
    {
        // 1. Validasi input
        $validator = validator($request->all(), [
            'kelas_id' => 'required|integer|exists:kelas,id',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // 2. Otorisasi (Contoh sederhana: pastikan user adalah guru)
        // TODO: Tambahkan logika untuk memastikan guru ini mengajar di kelas_id tersebut.
        if (!Auth::user()->hasRole('guru')) {
            return response()->json(['message' => 'Anda tidak memiliki izin untuk memulai sesi'], 403);
        }

        $kelas = Kelas::findOrFail($request->kelas_id);

        // 3. Broadcast event bahwa sesi telah dimulai
        broadcast(new SesiLiveDimulai($kelas))->toOthers();

        return response()->json([
            'message' => 'Sesi absensi live untuk kelas ' . $kelas->nama_kelas . ' telah dimulai.',
            'channel_name' => 'live-absensi.' . $kelas->id,
        ]);
    }

    /**
     * Menerima pembaruan lokasi dari siswa dan mem-broadcast-nya.
     * Endpoint ini akan dipanggil oleh aplikasi siswa setiap beberapa detik.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateLokasi(Request $request)
    {
        // 1. Validasi input koordinat
        $validator = validator($request->all(), [
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $siswa = Auth::user();
        $lokasi = $request->only(['lat', 'lng']);

        // 2. Broadcast event pembaruan lokasi ke channel kelas siswa
        // Menggunakan toOthers() agar tidak mengirim kembali ke siswa yang sama.
        broadcast(new LokasiSiswaDiperbarui($siswa, $lokasi))->toOthers();

        return response()->json(['status' => 'lokasi berhasil diperbarui']);
    }
}


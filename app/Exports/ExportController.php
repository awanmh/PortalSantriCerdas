<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\AbsensiSiswaExport; // Menggunakan namespace yang benar
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class ExportController extends Controller
{
    /**
     * Export data absensi siswa ke file Excel dengan filter tanggal.
     */
    public function exportAbsensiSiswa(Request $request)
    {
        // 1. Validasi input tanggal dari request
        $validator = Validator::make($request->all(), [
            'tanggal_awal'  => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Ambil tanggal dari request
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');

        // 3. Buat nama file yang dinamis
        $fileName = 'laporan-absensi-' . $tanggalAwal . '-sampai-' . $tanggalAkhir . '.xlsx';

        // 4. Panggil class Export dengan menyertakan parameter tanggal
        return Excel::download(new AbsensiSiswaExport($tanggalAwal, $tanggalAkhir), $fileName);
    }

    // Anda bisa menambahkan method export lain di sini di masa depan
    // public function exportPelanggaran(Request $request) { ... }
    // public function exportJadwal(Request $request) { ... }
}


<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AbsensiSiswa;
use App\Models\CatatanPelanggaran;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\AbsensiSiswaExport;
use App\Exports\CatatanPelanggaranExport;

class ExportController extends Controller
{
    /**
     * Ekspor data absensi siswa ke Excel
     */
    public function exportAbsensiSiswa(Request $request)
    {
        $tanggalAwal = $request->query('tanggal_awal', now()->startOfMonth()->format('Y-m-d'));
        $tanggalAkhir = $request->query('tanggal_akhir', now()->endOfMonth()->format('Y-m-d'));

        return Excel::download(
            new AbsensiSiswaExport($tanggalAwal, $tanggalAkhir),
            'absensi_siswa_' . now()->format('Ymd') . '.xlsx'
        );
    }

    /**
     * Ekspor data absensi siswa ke PDF
     */
    public function exportAbsensiSiswaPdf(Request $request)
    {
        $tanggalAwal = $request->query('tanggal_awal', now()->startOfMonth()->format('Y-m-d'));
        $tanggalAkhir = $request->query('tanggal_akhir', now()->endOfMonth()->format('Y-m-d'));

        $absensi = AbsensiSiswa::whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
            ->with('user')
            ->get();

        $pdf = Pdf::loadView('exports.absensi_siswa', [
            'absensi' => $absensi,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir
        ]);

        return $pdf->download('absensi_siswa_' . now()->format('Ymd') . '.pdf');
    }

    /**
     * Ekspor data pelanggaran ke Excel
     */
    public function exportPelanggaran(Request $request)
    {
        $tanggalAwal = $request->query('tanggal_awal', now()->startOfMonth()->format('Y-m-d'));
        $tanggalAkhir = $request->query('tanggal_akhir', now()->endOfMonth()->format('Y-m-d'));

        return Excel::download(
            new CatatanPelanggaranExport($tanggalAwal, $tanggalAkhir),
            'pelanggaran_' . now()->format('Ymd') . '.xlsx'
        );
    }
}
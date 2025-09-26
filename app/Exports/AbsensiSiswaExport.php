<?php

namespace App\Exports;

use App\Models\AbsensiSiswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AbsensiSiswaExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $tanggalAwal;
    protected $tanggalAkhir;

    public function __construct($tanggalAwal, $tanggalAkhir)
    {
        $this->tanggalAwal = $tanggalAwal;
        $this->tanggalAkhir = $tanggalAkhir;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // MENGGUNAKAN whereDate('waktu', ...) YANG BENAR
        return AbsensiSiswa::whereDate('waktu', '>=', $this->tanggalAwal)
            ->whereDate('waktu', '<=', $this->tanggalAkhir)
            ->with('siswa.kelas')
            ->get();
    }

    /**
     * Mendefinisikan header untuk kolom Excel.
     */
    public function headings(): array
    {
        return [
            'ID Absen',
            'Nama Siswa',
            'Kelas',
            'Tanggal',
            'Waktu Absen',
            'Status Zona',
            'Keterangan',
        ];
    }

    /**
     * Memetakan data dari collection ke baris Excel.
     */
    public function map($absensi): array
    {
        return [
            $absensi->id,
            $absensi->siswa->name ?? 'N/A',
            $absensi->siswa->kelas->first()->nama_kelas ?? 'N/A', // Mengambil kelas pertama
            $absensi->waktu->format('d-m-Y'), // MENGGUNAKAN 'waktu'
            $absensi->waktu->format('H:i:s'), // MENGGUNAKAN 'waktu'
            $absensi->valid_zona ? 'Di Dalam Zona' : 'Di Luar Zona',
            $absensi->keterangan,
        ];
    }
}

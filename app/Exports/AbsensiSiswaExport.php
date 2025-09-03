<?php

namespace App\Exports;

use App\Models\AbsensiSiswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AbsensiSiswaExport implements FromCollection, WithHeadings, WithMapping
{
    protected $tanggalAwal;
    protected $tanggalAkhir;

    public function __construct($tanggalAwal, $tanggalAkhir)
    {
        $this->tanggalAwal = $tanggalAwal;
        $this->tanggalAkhir = $tanggalAkhir;
    }

    public function collection()
    {
        return AbsensiSiswa::whereBetween('tanggal', [$this->tanggalAwal, $this->tanggalAkhir])
            ->with('user')
            ->get();
    }

    public function map($absensi): array
    {
        return [
            $absensi->user->name,
            $absensi->user->email,
            $absensi->tanggal,
            $absensi->jam_masuk,
            $absensi->status,
            $absensi->latitude,
            $absensi->longitude,
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Siswa',
            'Email',
            'Tanggal',
            'Jam Masuk',
            'Status',
            'Latitude',
            'Longitude',
        ];
    }
}
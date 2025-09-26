<?php

namespace App\Exports;

use App\Models\CatatanPelanggaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CatatanPelanggaranExport implements FromCollection, WithHeadings, WithMapping
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
        return CatatanPelanggaran::whereBetween('tanggal', [$this->tanggalAwal, $this->tanggalAkhir])
            ->with('siswa', 'guru')
            ->get();
    }

    public function map($pelanggaran): array
    {
        return [
            $pelanggaran->siswa->name,
            $pelanggaran->tanggal,
            $pelanggaran->jenis_pelanggaran,
            $pelanggaran->tingkat_keparahan,
            $pelanggaran->deskripsi,
            $pelanggaran->guru->name,
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Siswa',
            'Tanggal',
            'Jenis Pelanggaran',
            'Tingkat Keparahan',
            'Deskripsi',
            'Guru',
        ];
    }
}
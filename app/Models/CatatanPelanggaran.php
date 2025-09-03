<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatatanPelanggaran extends Model
{
    protected $table = 'catatan_pelanggaran';

    protected $fillable = [
        'siswa',
        'guru_bk',
        'deskripsi',
        'tindak_lanjut',
        'tingkat',
        'tanggal'
    ];

    protected $casts = [
        'tanggal' => 'date'
    ];
}
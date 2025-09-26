<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiGuru extends Model
{
    protected $table = 'absensi_guru';

    protected $fillable = [
        'guru_id',       // ini harus digunakan, bukan user_id
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status',
        'keterangan',
        'lat_masuk',
        'lng_masuk',
        'lat_pulang',
        'lng_pulang',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsenSiswa extends Model
{
    // DATA MODEL ABSENSI

    protected $table = 'absensi_siswa';

    protected $fillable = [
        'siswa_id',
        'jadwal_id',
        'waktu',
        'foto_path',
        'lat',
        'lng',
        'valid_zona',
        'device_info',
        'keterangan',
    ];

    protected $casts = [
        'waktu' => 'datetime',
        'device_info' => 'array',
        'valid_zone' => 'boolean',

    ];

    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }
    public function jadwal()
    {
        return $this->belogsTo(Jadwal::class, 'jadwal_id');
    }
}

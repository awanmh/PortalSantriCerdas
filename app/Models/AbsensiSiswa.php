<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiSiswa extends Model
{
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
        'valid_zona' => 'boolean',
    ];

    /**
     * Relasi ke siswa (User)
     */
    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    /**
     * Relasi ke jadwal
     */
    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }
}

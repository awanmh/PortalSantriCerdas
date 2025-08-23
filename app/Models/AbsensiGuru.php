<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiGuru extends Model
{
    protected $table = 'absensi_guru';

    protected $fillable = [
        'guru_id',
        'tanggal',
        'status',
        'keterangan',
    ];

    /**
     * Relasi ke guru (User)
     */
    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }
}

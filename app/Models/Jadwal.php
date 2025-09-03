<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'jadwal';

    protected $fillable = [
        'guru',
        'kelas',
        'mata_pelajaran',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'ruang'
    ];
}
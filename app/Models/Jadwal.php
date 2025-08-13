<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;
    protected $fillable = [
        'judul_event',
        'deskripsi',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'guru_id',
        'tipe',

    ];
    //
    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }
}
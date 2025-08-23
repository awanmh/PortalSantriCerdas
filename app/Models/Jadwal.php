<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    protected $table = 'jadwal';

    protected $fillable = [
        'judul_event',
        'deskripsi',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'guru_id',
        'kelas_id',   // ✅ tambahin, biar relasi bisa dipakai
        'jurusan_id', // ✅ tambahin juga
        'tipe',
    ];

    /**
     * Relasi ke guru (User)
     */
    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    /**
     * Relasi ke kelas
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Relasi ke jurusan
     */
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa'; // Tambahkan ini (tanpa "s")

    protected $fillable = [
        'user_id',
        'nis',
        'nama',
        'kelas',
        'jurusan'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
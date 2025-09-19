<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Siswa extends Model
{
    protected $table = 'siswa';

    /**
     * Kolom yang dapat diisi secara massal, disesuaikan dengan form registrasi.
     */
    protected $fillable = [
        'user_id',
        'nama',
        'jurusan_id', // Diubah dari 'jurusan'
        'angkatan',   // Ditambahkan
        'nis',        // Opsional, bisa diisi nanti
        'kelas',      // Opsional, bisa diisi nanti
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }
}

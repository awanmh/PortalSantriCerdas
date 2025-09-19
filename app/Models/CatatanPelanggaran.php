<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatatanPelanggaran extends Model
{
    use HasFactory;

    protected $table = 'catatan_pelanggaran';

    /**
     * Atribut yang dapat diisi secara massal, disesuaikan dengan migrasi.
     */
    protected $fillable = [
        'user_id',    // Merujuk ke siswa
        'pelapor_id', // Merujuk ke guru/bk yang melapor
        'jenis',
        'deskripsi',
        'poin',
        'tanggal',
    ];

    /**
     * Casts untuk memastikan tipe data yang benar.
     */
    protected $casts = [
        'tanggal' => 'date',
        'poin' => 'integer',
    ];

    /**
     * Relasi ke User (sebagai siswa yang melanggar).
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke User (sebagai guru/bk yang melapor).
     */
    public function pelapor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }
}
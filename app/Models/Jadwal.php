<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jadwal extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini.
     *
     * @var string
     */
    protected $table = 'jadwal';

    /**
     * Atribut yang dapat diisi secara massal.
     * Disesuaikan dengan file migrasi terbaru.
     *
     * @var array<int, string>
     */
    protected $fillable = [
    'mata_pelajaran',
    'deskripsi',
    'hari',
    'jam_mulai',
    'jam_selesai',
    'tipe',
    'kelas_id',
    'guru_id',
];

protected $casts = [
    'jam_mulai' => 'datetime:H:i',
    'jam_selesai' => 'datetime:H:i',
];


    /**
     * Mendefinisikan relasi ke model Kelas.
     * Satu jadwal hanya dimiliki oleh satu kelas.
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    /**
     * Mendefinisikan relasi ke model User (sebagai Guru).
     * Satu jadwal hanya diajar oleh satu guru.
     * Kita perlu mendefinisikan foreign key 'guru_id' secara eksplisit.
     */
    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id');
    }
}


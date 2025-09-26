<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jadwal extends Model
{
    use HasFactory;

    protected $table = 'jadwal';

    protected $fillable = [
        'mata_pelajaran_id',
        'deskripsi',
        'hari',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'tipe',
        'kelas_id',
        'guru_id',
    ];

    protected $casts = [
        'tanggal'     => 'date',
        'jam_mulai'   => 'datetime:H:i',   // simpan sebagai time di DB, cast ke Carbon instance
        'jam_selesai' => 'datetime:H:i',
    ];

    /**
     * Relasi ke kelas.
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'id');
    }

    /**
     * Relasi ke guru (User).
     */
    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id', 'id');
    }

    /**
     * Relasi ke mata pelajaran.
     * Penting: gunakan foreign key 'mata_pelajaran_id'
     */
    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id', 'id');
    }

    /**
     * Scope untuk mengambil jadwal hari ini
     * (menggabungkan jadwal rutin dan insidental).
     */
    public function scopeToday($query)
    {
        $todayDate = Carbon::today()->toDateString();
        $todayDay  = Carbon::today()->isoFormat('dddd'); // contoh: "Jumat"

        return $query->where(function ($q) use ($todayDate, $todayDay) {
            // Jadwal rutin (berdasarkan hari, tanpa tanggal spesifik)
            $q->where(function ($sub) use ($todayDay) {
                $sub->where('hari', $todayDay)
                    ->where('tipe', 'pelajaran')
                    ->whereNull('tanggal');
            });

            // Atau jadwal insidental (punya tanggal spesifik)
            $q->orWhere(function ($sub) use ($todayDate) {
                $sub->where('tanggal', $todayDate);
            });
        });
    }
}

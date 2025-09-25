<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Jadwal extends Model
{
    use HasFactory;

    protected $table = 'jadwal';

    protected $fillable = [
        'mata_pelajaran', 'deskripsi', 'hari', 'tanggal',
        'jam_mulai', 'jam_selesai', 'tipe', 'kelas_id', 'guru_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    /**
     * Scope untuk mengambil jadwal hari ini (menggabungkan jadwal rutin dan insidental).
     */
    public function scopeToday($query)
    {
        $todayDate = Carbon::today()->toDateString();
        $todayDay = Carbon::today()->isoFormat('dddd'); // Misal: "Rabu"

        return $query->where(function ($q) use ($todayDate, $todayDay) {
            // Kondisi 1: Jadwal rutin (punya 'hari', tipe 'pelajaran', dan tidak punya 'tanggal' spesifik)
            $q->where('hari', $todayDay)
              ->where('tipe', 'pelajaran')
              ->whereNull('tanggal');

            // ATAU Kondisi 2: Jadwal insidental (punya 'tanggal' spesifik, tidak peduli harinya)
            $q->orWhere('tanggal', $todayDate);
        });
    }
}

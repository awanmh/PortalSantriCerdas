<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiSiswa extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini.
     *
     * @var string
     */
    protected $table = 'absensi_siswa';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'jadwal_id',
        'status',
        'waktu_absensi',
        'bukti_foto_path',
        'latitude',
        'longitude',
        'valid_zona',
        'device_info',
        'keterangan',
    ];

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'waktu_absensi' => 'datetime',
        'device_info' => 'array',
        'valid_zona' => 'boolean',
    ];

    /**
     * Mendefinisikan relasi "belongsTo" ke model User (sebagai siswa).
     * Setiap catatan absensi dimiliki oleh satu user.
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Mendefinisikan relasi "belongsTo" ke model Jadwal.
     * Setiap catatan absensi terikat pada satu jadwal pelajaran.
     */
    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(Jadwal::class);
    }
}


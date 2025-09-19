<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Zona
 *
 * Merepresentasikan sebuah zona geografis untuk keperluan absensi.
 * Model ini terhubung dengan tabel 'zone' di database.
 *
 * @property int $id
 * @property string $nama_zona Nama identifikasi untuk zona (mis: "Zona Sekolah").
 * @property float $lat Garis lintang (latitude) dari titik pusat zona.
 * @property float $lng Garis bujur (longitude) dari titik pusat zona.
 * @property int $radius Jarak radius dari titik pusat dalam satuan meter.
 * @property bool $is_active Menandakan apakah zona ini aktif digunakan untuk absensi.
 * @property string $jam_masuk Waktu mulai absensi masuk (format: HH:mm:ss).
 * @property string $jam_pulang Waktu mulai absensi pulang (format: HH:mm:ss).
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Zona extends Model
{
    use HasFactory;

    /**
     * Nama tabel database yang terhubung dengan model ini.
     *
     * @var string
     */
    protected $table = 'zone';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     * Menggunakan $fillable lebih aman dan eksplisit daripada $guarded.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_zona',
        'lat',
        'lng',
        'radius',
        'is_active',
        'jam_masuk',   // Pastikan ini ditambahkan sesuai migrasi terakhir
        'jam_pulang',  // Pastikan ini ditambahkan sesuai migrasi terakhir
    ];

    /**
     * Atribut yang harus di-cast ke tipe data tertentu secara otomatis.
     * Ini memastikan konsistensi data saat mengambil dari database.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'radius' => 'integer',
        'is_active' => 'boolean',
    ];
}


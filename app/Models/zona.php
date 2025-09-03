<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model ini.
     *
     * @var string
     */
    protected $table = 'zone'; // WAJIB: Agar tidak mencari tabel 'zonas'

    /**
     * The attributes that are mass assignable.
     * Kolom harus cocok dengan migrasi database.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_zona', // Nama kolom yang benar
        'lat',       // Nama kolom yang benar
        'lng',       // Nama kolom yang benar
        'radius',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     * Ini adalah tambahan yang bagus dari kode Anda.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean'
    ];
}


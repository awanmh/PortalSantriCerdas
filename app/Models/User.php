<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke jadwal (jika guru)
     */
    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'guru_id');
    }

    /**
     * Relasi ke absensi guru
     */
    public function absensiGuru()
    {
        return $this->hasMany(AbsensiGuru::class, 'guru_id'); // ✅ fix, defaultnya tadi cari user_id
    }

    /**
     * Relasi ke absensi siswa
     */
    public function absensiSiswa()
    {
        return $this->hasMany(AbsensiSiswa::class, 'siswa_id'); // ✅ fix
    }

    /**
     * Relasi ke kelas (via pivot)
     */
    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_user', 'user_id', 'kelas_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * Atribut yang dapat diisi secara massal.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path',
        // --- DITAMBAHKAN ---
        'last_seen_at',
        'last_known_location',
    ];

    /**
     * Atribut yang harus disembunyikan.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atribut yang harus di-cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        // --- DITAMBAHKAN ---
        'last_seen_at' => 'datetime',
        'last_known_location' => 'array',
    ];

    /**
     * Atribut yang harus ditambahkan ke representasi array/JSON model.
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function profilePhotoUrl(): Attribute
    {
        return Attribute::get(function () {
            return $this->profile_photo_path
                ? Storage::disk('public')->url($this->profile_photo_path)
                : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
        });
    }

    public function siswa(): HasOne
    {
        return $this->hasOne(Siswa::class);
    }

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_user');
    }

    public function catatanPelanggaran(): HasMany
    {
        return $this->hasMany(CatatanPelanggaran::class, 'user_id');
    }
}
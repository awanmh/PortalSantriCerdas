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
        'last_seen_at',
        'last_known_location',
        'latitude', // Ditambahkan untuk konsistensi
        'longitude', // Ditambahkan untuk konsistensi
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
        'last_seen_at' => 'datetime',
        'last_known_location' => 'array', 
    ];

    /**
     * Atribut yang harus ditambahkan ke representasi array/JSON model.
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Aksesor untuk URL foto profil.
     */
    public function profilePhotoUrl(): Attribute
    {
        return Attribute::get(function () {
            return $this->profile_photo_path
                ? Storage::disk('public')->url($this->profile_photo_path)
                : $this->defaultProfilePhotoUrl();
        });
    }

    /**
     * URL foto profil default.
     */
    protected function defaultProfilePhotoUrl(): string
    {
        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Mutator untuk lokasi terakhir.
     * Memastikan konsistensi antara last_known_location dan latitude/longitude.
     */
    public function setLastKnownLocationAttribute($value): void
    {
        if (is_array($value) && isset($value['lat']) && isset($value['lng'])) {
            $this->attributes['last_known_location'] = json_encode($value);
            $this->attributes['latitude'] = $value['lat'];
            $this->attributes['longitude'] = $value['lng'];
        }
    }

    /**
     * Hubungan one-to-one dengan model Siswa.
     */
    public function siswa(): HasOne
    {
        return $this->hasOne(Siswa::class);
    }

    /**
     * Hubungan many-to-many dengan model Kelas.
     */
    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_user');
    }

    /**
     * Hubungan one-to-many dengan model CatatanPelanggaran.
     */
    public function catatanPelanggaran(): HasMany
    {
        return $this->hasMany(CatatanPelanggaran::class, 'user_id');
    }

    /**
     * Scope untuk mendapatkan hanya user dengan role siswa.
     */
    public function scopeSiswa($query)
    {
        return $query->role('siswa');
    }

    /**
     * Scope untuk mendapatkan hanya user dengan role guru.
     */
    public function scopeGuru($query)
    {
        return $query->role('guru');
    }

    /**
     * Scope untuk mendapatkan user yang aktif (dilihat dalam 5 menit terakhir).
     */
    public function scopeAktif($query)
    {
        return $query->where('last_seen_at', '>=', now()->subMinutes(5));
    }

    /**
     * Scope untuk mendapatkan user berdasarkan lokasi.
     */
    public function scopeDekatDengan($query, $latitude, $longitude, $radius = 1)
    {
        return $query->whereRaw("
            (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
            cos(radians(longitude) - radians(?)) + sin(radians(?)) * 
            sin(radians(latitude)))) < ?
        ", [$latitude, $longitude, $latitude, $radius]);
    }

    /**
     * Memeriksa apakah user sedang online.
     */
    public function isOnline(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->greaterThan(now()->subMinutes(5));
    }

    /**
     * Memeriksa apakah user berada dalam zona tertentu.
     */
    public function isInZona($zona): bool
    {
        if (!$this->latitude || !$this->longitude || !$zona->latitude || !$zona->longitude) {
            return false;
        }

        $distance = $this->calculateDistance(
            $this->latitude, 
            $this->longitude, 
            $zona->latitude, 
            $zona->longitude
        );

        return $distance <= $zona->radius;
    }

    /**
     * Menghitung jarak antara dua titik koordinat (dalam kilometer).
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + 
                cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        return $dist * 60 * 1.1515 * 1.609344; // Konversi ke kilometer
    }
}
<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path',
        'last_seen_at',
        'last_known_location',
        'latitude',
        'longitude',
        'subject_taught', // Untuk menyimpan mata pelajaran utama guru
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_seen_at' => 'datetime',
        'last_known_location' => 'array',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    // --- RELATIONS ---

    /**
     * The classes that the user (as a student) belongs to.
     */
    public function kelas(): BelongsToMany
    {
        return $this->belongsToMany(Kelas::class, 'kelas_user');
    }

    /**
     * The class for which the user is the homeroom teacher (wali kelas).
     */
    public function waliKelas(): HasOne
    {
        return $this->hasOne(Kelas::class, 'wali_kelas_id');
    }

    /**
     * The schedules that the user (as a teacher) is assigned to.
     */
    public function jadwalMengajar(): HasMany
    {
        return $this->hasMany(Jadwal::class, 'guru_id');
    }

    /**
     * The additional student data associated with this user.
     */
    public function siswa(): HasOne
    {
        return $this->hasOne(Siswa::class);
    }

    /**
     * The infraction records for this user (as a student).
     */
    public function catatanPelanggaran(): HasMany
    {
        return $this->hasMany(CatatanPelanggaran::class, 'user_id');
    }
    
    /**
     * Get all of the absence records for the User (as a student).
     */
    public function absensiSiswa(): HasMany
    {
        return $this->hasMany(AbsensiSiswa::class, 'user_id');
    }

    /**
     * Get all of the absence records for the User (as a teacher).
     */
    public function absensiGuru(): HasMany
    {
        return $this->hasMany(AbsensiGuru::class, 'guru_id');
    }

    /**
     * The infraction records reported by this user (as staff).
     */
    public function pelanggaranDilaporkan(): HasMany
    {
        return $this->hasMany(CatatanPelanggaran::class, 'pelapor_id');
    }


    // --- SCOPES ---

    /**
     * Scope a query to only include users with the 'siswa' role.
     */
    public function scopeSiswa($query)
    {
        return $query->role('siswa');
    }

    /**
     * Scope a query to only include users with the 'guru' role.
     */
    public function scopeGuru($query)
    {
        return $query->role('guru');
    }
    
    /**
     * Scope a query to only include active users (seen within the last 5 minutes).
     */
    public function scopeAktif($query)
    {
        return $query->where('last_seen_at', '>=', now()->subMinutes(5));
    }

    /**
     * Scope a query to only include users near a specific location.
     */
    public function scopeDekatDengan($query, float $latitude, float $longitude, int $radius = 1)
    {
        // Haversine formula for distance calculation in SQL
        return $query->whereRaw(
            "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) < ?",
            [$latitude, $longitude, $latitude, $radius]
        );
    }


    // --- ACCESSORS & MUTATORS ---

    /**
     * Get the URL of the user's profile photo.
     */
    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(function () {
            return $this->profile_photo_path
                ? Storage::disk('public')->url($this->profile_photo_path)
                : $this->defaultProfilePhotoUrl();
        });
    }

    /**
     * Set the last known location attribute and associated latitude & longitude.
     */
    public function setLastKnownLocationAttribute($value): void
    {
        if (is_array($value) && isset($value['lat']) && isset($value['lng'])) {
            $this->attributes['last_known_location'] = json_encode($value);
            $this->attributes['latitude'] = $value['lat'];
            $this->attributes['longitude'] = $value['lng'];
        } else {
            $this->attributes['last_known_location'] = null;
            $this->attributes['latitude'] = null;
            $this->attributes['longitude'] = null;
        }
    }

    // --- OTHER METHODS ---

    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     */
    protected function defaultProfilePhotoUrl(): string
    {
        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=7F9CF5&background=EBF4FF';
    }
    
    /**
     * Check if the user is currently online.
     */
    public function isOnline(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->greaterThan(now()->subMinutes(5));
    }

    /**
     * Check if the user is within a specified zone.
     */
    public function isInZona(Zona $zona): bool
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
     * Calculate the distance between two geographical points (in kilometers).
     */
    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +
                cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        return $dist * 60 * 1.1515 * 1.609344; // Convert to kilometers
    }
}
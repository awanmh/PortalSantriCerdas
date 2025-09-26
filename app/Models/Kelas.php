<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'nama_kelas',
        'jenjang',
        'jurusan_id',
        'wali_kelas_id',
    ];

    /**
     * Get the Jurusan that owns the Kelas.
     */
    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    /**
     * Get the User (Wali Kelas) that owns the Kelas.
     */
    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'wali_kelas_id');
    }

    /**
     * Get the users (students) that belong to the Kelas.
     * Uses a many-to-many relationship through the 'kelas_user' pivot table.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'kelas_user');
    }

    /**
     * Get the schedules for the class.
     */
    public function jadwal(): HasMany
    {
        return $this->hasMany(Jadwal::class);
    }
}


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
    protected $fillable = ['nama', 'tingkat'];

    // 🔗 Relasi ke jurusan (1 kelas punya 1 jurusan)
    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    // 🔗 Relasi ke user (siswa/guru)
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'kelas_user')
                    ->withTimestamps();
    }

    // 🔗 Relasi ke jadwal
    public function jadwal(): HasMany
    {
        return $this->hasMany(Jadwal::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatatanPelanggaran extends Model
{
    use HasFactory;
    //
    protected $table = 'pelanggaran';
    protected $fillable = ['guru_id', 'siswa_id', 'deskrpsi'];

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }
    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }
}

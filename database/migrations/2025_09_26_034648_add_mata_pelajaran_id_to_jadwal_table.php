<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tidak perlu menambahkan kolom mata_pelajaran_id lagi
        // karena sudah dibuat di migrasi create_jadwal_table.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak ada yang perlu dihapus karena tidak ada perubahan di up().
    }
};

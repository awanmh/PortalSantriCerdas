<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('absensi_guru', function (Blueprint $table) {
            $table->id();
            
            // --- DIUBAH KEMBALI SESUAI PERMINTAAN ---
            $table->foreignId('guru_id')->constrained('users')->onDelete('cascade');
            
            $table->date('tanggal')->default(now());
            $table->dateTime('waktu_masuk')->nullable();
            $table->dateTime('waktu_pulang')->nullable();
            $table->string('status')->default('hadir');
            $table->string('keterangan')->nullable();
            
            $table->json('lokasi_masuk')->nullable();
            $table->json('lokasi_pulang')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_guru');
    }
};


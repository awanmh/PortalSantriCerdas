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
        Schema::create('absensi_siswa', function (Blueprint $table) {
            $table->id();

            // Kolom relasi inti (menggunakan user_id untuk konsistensi)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('jadwal_id')->constrained('jadwal')->onDelete('cascade');

            // --- Kolom Kunci yang Ditambahkan ---
            $table->enum('status', ['hadir', 'sakit', 'izin', 'alfa'])->default('hadir');
            
            // Mengambil kolom-kolom canggih dari versi Anda
            $table->timestamp('waktu_absensi');
            $table->string('bukti_foto_path')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('valid_zona')->default(false);
            $table->json('device_info')->nullable();
            $table->text('keterangan')->nullable();

            $table->timestamps();

            // Menambahkan unique constraint untuk mencegah absensi ganda
            $table->unique(['user_id', 'jadwal_id'], 'absensi_unik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_siswa');
    }
};


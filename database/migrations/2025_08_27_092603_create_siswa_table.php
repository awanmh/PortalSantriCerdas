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
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // --- PERUBAHAN PENTING ADA DI SINI ---

            $table->string('nama');
            // NIS bisa jadi null saat pendaftaran awal, diisi oleh admin nanti
            $table->string('nis')->unique()->nullable();
            
            // Menggunakan foreign key ke tabel 'jurusan', bukan enum
            $table->foreignId('jurusan_id')->constrained('jurusan');

            // Menambahkan kolom angkatan sesuai kebutuhan form registrasi
            $table->string('angkatan');

            // Sebaiknya kelas juga menggunakan foreign key, dibuat nullable
            // karena siswa mungkin belum masuk kelas saat mendaftar.
            $table->foreignId('kelas_id')->nullable()->constrained('kelas');

            $table->string('no_hp_ortu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};

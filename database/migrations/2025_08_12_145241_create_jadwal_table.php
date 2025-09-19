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
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id();
            $table->string('mata_pelajaran');
            $table->text('deskripsi')->nullable();
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            
            // Enum untuk tipe jadwal agar lebih terkontrol
            $table->enum('tipe', ['pelajaran', 'acara'])->default('pelajaran');

            // Relasi ke tabel lain
            // kelas_id dibuat NULLABLE untuk mengakomodasi event umum sekolah
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('cascade');
            $table->foreignId('guru_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();

            // Menambahkan index untuk performa query yang lebih cepat
            $table->index('tanggal');
            $table->index('kelas_id');
            $table->index('guru_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};


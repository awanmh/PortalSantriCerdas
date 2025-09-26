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

            // Relasi ke tabel mata_pelajarans
            $table->foreignId('mata_pelajaran_id')
                ->constrained('mata_pelajarans')
                ->onDelete('cascade');

            $table->text('deskripsi')->nullable();

            // Untuk jadwal reguler (Senin–Jumat)
            $table->string('hari')->nullable();

            // Untuk event tertentu
            $table->date('tanggal')->nullable();

            $table->time('jam_mulai');
            $table->time('jam_selesai');

            $table->enum('tipe', ['pelajaran', 'acara'])->default('pelajaran');

            $table->foreignId('kelas_id')
                ->nullable()
                ->constrained('kelas')
                ->onDelete('cascade');

            $table->foreignId('guru_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->timestamps();
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

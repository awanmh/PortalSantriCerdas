<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id();
            $table->string('mata_pelajaran');
            // Menambahkan kolom 'deskripsi' yang hilang
            $table->text('deskripsi')->nullable();
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');

            // Menambahkan kembali relasi yang penting
            $table->foreignId('guru_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('cascade');
            $table->foreignId('jurusan_id')->nullable()->constrained('jurusan')->onDelete('set null');

            $table->string('jenjang')->nullable();
            $table->string('tipe')->default('pelajaran'); // 'pelajaran' atau 'event'

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};


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
        Schema::create('catatan_pelanggaran', function (Blueprint $table) {
            $table->id();

            // Menghubungkan ke siswa (user dengan peran siswa)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Menghubungkan ke pelapor (user dengan peran guru/bk/it)
            $table->foreignId('pelapor_id')->constrained('users')->onDelete('cascade');
            
            $table->string('jenis');
            $table->text('deskripsi');
            $table->integer('poin')->default(1); // Kolom poin yang hilang
            $table->date('tanggal');
            
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('catatan_pelanggaran');
    }
};

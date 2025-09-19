<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Nama class disesuaikan dengan konvensi nama file
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas');
            $table->enum('jenjang', ['X', 'XI', 'XII']);

            // MENGGUNAKAN FOREIGN KEY, BUKAN STRING
            $table->foreignId('jurusan_id')->constrained('jurusan')->onDelete('cascade');
            
            // Relasi ke wali kelas (user dengan role 'guru')
            $table->foreignId('wali_kelas_id')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};

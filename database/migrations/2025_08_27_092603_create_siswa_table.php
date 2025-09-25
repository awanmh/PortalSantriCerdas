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
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');

            $table->string('nama');
            $table->string('nis')->unique()->nullable();
            $table->string('angkatan'); // Kolom ini tidak nullable, yang menyebabkan error
            
            $table->foreignId('jurusan_id')->constrained('jurusan');
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('set null');

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

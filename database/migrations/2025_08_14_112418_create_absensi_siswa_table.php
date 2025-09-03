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

            // relasi ke tabel users
            $table->foreignId('siswa_id')
                ->constrained('users')
                ->onDelete('cascade');

            // relasi ke tabel jadwal
            $table->foreignId('jadwal_id')
                ->nullable()
                ->constrained('jadwal')
                ->onDelete('set null');

            $table->timestamp('waktu');
            $table->string('foto_path')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->boolean('valid_zona')->default(false);
            $table->json('device_info')->nullable();
            $table->text('keterangan')->nullable();

            $table->timestamps();
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

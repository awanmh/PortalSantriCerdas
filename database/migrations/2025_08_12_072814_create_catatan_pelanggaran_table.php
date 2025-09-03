<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catatan_pelanggaran', function (Blueprint $table) {
            $table->id();
            $table->string('siswa'); // Input manual, bukan foreign key
            $table->string('guru_bk'); // Input manual, bukan foreign key
            $table->text('deskripsi');
            $table->text('tindak_lanjut')->nullable();
            $table->string('tingkat')->default('ringan'); // ringan, sedang, berat
            $table->date('tanggal')->default(now());
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catatan_pelanggaran');
    }
};
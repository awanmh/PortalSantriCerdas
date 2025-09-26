<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mata_pelajarans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');                // Nama mata pelajaran
            $table->string('kategori')->nullable(); // Umum / Produktif
            $table->string('jurusan')->nullable();  // TSM, TKJ, DKV, AKT (null untuk mapel umum)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_pelajarans');
    }
};

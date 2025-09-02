<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('siswa', function (Blueprint $table) { // Perhatikan: 'siswa' tanpa "s"
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nis')->unique();
            $table->string('nama');
            $table->enum('kelas', ['X', 'XI', 'XII']);
            $table->enum('jurusan', ['RPL', 'TKJ', 'TKRO', 'TBSM']);
            $table->string('no_hp_ortu')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('siswa'); // Tanpa "s"
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKelasTable extends Migration
{
    // database/migrations/2025_08_12_145226_create_kelas_table.php
public function up()
{
    Schema::create('kelas', function (Blueprint $table) {
        $table->id();
        $table->string('nama_kelas');
        $table->integer('tingkat'); // <--- TAMBAHKAN BARIS INI
        $table->string('jurusan');

        // Kolom wali_kelas dipindahkan ke migrasi lain
        // $table->string('wali_kelas');

        $table->timestamps();
    });
}
    public function down()
    {
        Schema::dropIfExists('kelas_user');
        Schema::dropIfExists('kelas');
    }
}
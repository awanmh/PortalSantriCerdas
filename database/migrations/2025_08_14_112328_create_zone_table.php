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
        Schema::create('zone', function (Blueprint $table) {
            $table->id();
            $table->string('nama_zona'); // Nama kolom yang benar
            $table->decimal('lat', 10, 7); // Kolom untuk latitude
            $table->decimal('lng', 10, 7); // Kolom untuk longitude
            $table->decimal('radius', 8, 2); // Kolom untuk radius dalam meter
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zone');
    }
};

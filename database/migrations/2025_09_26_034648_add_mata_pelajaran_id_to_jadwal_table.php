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
        Schema::table('jadwal', function (Blueprint $table) {
            $table->foreignId('mata_pelajaran_id')->nullable()->constrained('mata_pelajarans')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('jadwal', function (Blueprint $table) {
            $table->dropColumn('mata_pelajaran_id');
        });
    }

};

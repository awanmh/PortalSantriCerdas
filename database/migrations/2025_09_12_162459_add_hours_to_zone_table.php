<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('zone', function (Blueprint $table) {
            // Tambahkan kolom-kolom ini setelah 'is_active'
            $table->time('jam_masuk')->default('07:00:00');
            $table->time('jam_pulang')->default('15:00:00');
        });
    }

    public function down(): void
    {
        Schema::table('zone', function (Blueprint $table) {
            $table->dropColumn(['jam_masuk', 'jam_pulang']);
        });
    }
};

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
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_seen_at')->nullable()->after('profile_photo_path');
            // --- TAMBAHKAN KOLOM INI ---
            // Menambahkan kolom json untuk menyimpan data lokasi terstruktur
            $table->json('last_known_location')->nullable()->after('last_seen_at');
            // -------------------------
            $table->decimal('latitude', 10, 8)->nullable()->after('last_known_location');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Pastikan kolom baru juga dihapus saat rollback
            $table->dropColumn(['last_seen_at', 'last_known_location', 'latitude', 'longitude']);
        });
    }
};


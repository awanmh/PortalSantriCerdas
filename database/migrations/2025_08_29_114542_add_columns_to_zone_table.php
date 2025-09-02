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
        Schema::table(
            'zone',
            function (Blueprint $table) {
                if (!Schema::hasColumn('zone', 'name')) {
                    $table->string('name')->after('id');
                }
                if (!Schema::hasColumn('zone', 'polygon')) {
                    $table->geometry('polygon')->nullable()->after('name');
                }
                if (!Schema::hasColumn('zone', 'description')) {
                    $table->string('description')->nullable()->after('polygon');
                }
                if (!Schema::hasColumn('zone', 'color')) {
                    $table->string('color')->default('#3498db')->after('description');
                }
                if (!Schema::hasColumn('zone', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('color');
                }
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zone', function (Blueprint $table) {
            $table->dropColumn(['polygon', 'description', 'color', 'is_active']);
        });
    }
};
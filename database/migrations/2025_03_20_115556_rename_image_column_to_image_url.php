<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('smartphones', function (Blueprint $table) {
            // Jika kolom image ada, rename menjadi image_url
            if (Schema::hasColumn('smartphones', 'image')) {
                $table->renameColumn('image', 'image_url');
            }
            // Jika kolom image tidak ada, buat kolom image_url
            else if (!Schema::hasColumn('smartphones', 'image_url')) {
                $table->string('image_url')->nullable()->after('battery_score');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('smartphones', function (Blueprint $table) {
            if (Schema::hasColumn('smartphones', 'image_url')) {
                $table->renameColumn('image_url', 'image');
            }
        });
    }
};
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
            // Tambahkan kolom release_year
            $table->year('release_year')->comment('Tahun rilis smartphone')->after('battery_score')->default(now()->year);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('smartphones', function (Blueprint $table) {
            // Hapus kolom release_year jika rollback
            $table->dropColumn('release_year');
        });
    }
};
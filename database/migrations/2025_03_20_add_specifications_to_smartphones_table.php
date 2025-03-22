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
            $table->integer('ram')->default(0)->comment('RAM dalam GB')->after('release_year');
            $table->integer('storage')->default(0)->comment('Storage dalam GB')->after('ram');
            $table->string('processor')->default('')->comment('Prosesor')->after('storage');
            $table->integer('battery')->default(0)->comment('Kapasitas baterai dalam mAh')->after('processor');
            $table->integer('camera')->default(0)->comment('Resolusi kamera dalam MP')->after('battery');
            $table->decimal('screen_size', 3, 1)->default(0)->comment('Ukuran layar dalam inci')->after('camera');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('smartphones', function (Blueprint $table) {
            $table->dropColumn([
                'ram',
                'storage',
                'processor',
                'battery',
                'camera',
                'screen_size',
            ]);
        });
    }
};
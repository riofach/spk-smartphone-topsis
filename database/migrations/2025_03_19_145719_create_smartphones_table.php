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
        Schema::create('smartphones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 12, 2);
            $table->string('image_url');
            $table->text('description');
            $table->decimal('camera_score', 3, 1);
            $table->decimal('performance_score', 3, 1);
            $table->decimal('design_score', 3, 1);
            $table->decimal('battery_score', 3, 1);
            $table->year('release_year')->comment('Tahun rilis smartphone');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smartphones');
    }
};
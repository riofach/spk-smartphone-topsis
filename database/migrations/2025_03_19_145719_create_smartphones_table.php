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
            $table->integer('price');
            $table->float('camera_score', 4, 2)->comment('Skor kamera (1-10)');
            $table->float('performance_score', 4, 2)->comment('Skor performa (1-10)');
            $table->float('design_score', 4, 2)->comment('Skor desain (1-10)');
            $table->float('battery_score', 4, 2)->comment('Skor baterai (1-10)');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
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
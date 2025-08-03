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
        Schema::create('trail_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trail_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->string('image_type')->default('photo'); // photo, map, elevation
            $table->string('caption')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trail_images');
    }
};

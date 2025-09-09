<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hiking_routes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('osm_id')->index();
            $table->string('name')->nullable();
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->integer('ascent')->nullable();
            $table->integer('descent')->nullable();
            $table->json('raw_tags')->nullable();
            $table->timestamps();
            $table->unique(['osm_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('hiking_routes');
    }
};

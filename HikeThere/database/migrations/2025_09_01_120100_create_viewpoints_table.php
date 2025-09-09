<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('viewpoints', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('osm_id')->index();
            $table->string('name')->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->json('raw_tags')->nullable();
            $table->timestamps();
            $table->unique(['osm_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('viewpoints');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mountains', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->integer('elevation')->nullable();
            $table->integer('prominence')->nullable();
            $table->boolean('is_volcano')->default(false);
            $table->string('coordinate_string')->nullable();
            $table->decimal('latitude', 10, 7); // lat ~ -90..90
            $table->decimal('longitude', 10, 7); // lon ~ -180..180
            $table->json('provinces');
            $table->json('regions');
            $table->string('island_group');
            $table->json('alt_names')->nullable();
            $table->json('style')->nullable();
            $table->json('source_properties');
            $table->timestamps();
            $table->index(['island_group', 'elevation']);
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mountains');
    }
};

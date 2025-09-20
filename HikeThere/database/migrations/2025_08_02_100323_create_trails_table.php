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
        Schema::create('trails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('difficulty', ['beginner-friendly', 'moderate', 'challenging', 'strenuous']);
            $table->text('description')->nullable();
            $table->foreignId('region_id')->constrained()->onDelete('cascade');
            $table->decimal('distance', 8, 2)->nullable(); // in kilometers
            $table->decimal('length', 8, 2); // in kilometers
            $table->integer('elevation_gain'); // in meters
            $table->integer('elevation_high'); // highest point in meters
            $table->integer('elevation_low'); // lowest point in meters
            $table->integer('estimated_time'); // in minutes
            $table->text('summary');
            $table->json('features')->nullable(); // ["scenic views", "waterfalls", "camping"]
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('gpx_file')->nullable(); // GPS track file
            $table->json('coordinates')->nullable(); // trail coordinates
            $table->timestamps();
          
           
          

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trails');
    }
};

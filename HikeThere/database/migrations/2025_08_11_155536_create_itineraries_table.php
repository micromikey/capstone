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
        Schema::create('itineraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('trail_name');
            $table->string('difficulty_level');
            $table->string('estimated_duration');
            $table->string('distance');
            $table->string('elevation_gain');
            $table->string('best_time_to_hike');
            $table->text('weather_conditions');
            $table->json('gear_recommendations');
            $table->json('safety_tips');
            $table->text('route_description');
            $table->json('waypoints');
            $table->json('emergency_contacts');
            $table->json('schedule')->nullable();
            $table->json('stopovers')->nullable();
            $table->json('sidetrips')->nullable();
            $table->string('transportation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itineraries');
    }
};

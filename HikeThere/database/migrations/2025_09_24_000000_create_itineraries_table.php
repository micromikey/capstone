<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('itineraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('trail_id')->nullable()->constrained('trails')->nullOnDelete();
            $table->string('title')->nullable();
            $table->integer('duration_days')->default(1);
            $table->integer('nights')->default(0);
            $table->time('start_time')->nullable();
            $table->date('start_date')->nullable();
            $table->json('build')->nullable();
            $table->json('weather_data')->nullable();
            $table->json('daily_schedule')->nullable();
            $table->json('transport_details')->nullable();
            $table->json('departure_info')->nullable();
            $table->json('arrival_info')->nullable();
            $table->json('route_data')->nullable();
            $table->json('route_summary')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('itineraries');
    }
};

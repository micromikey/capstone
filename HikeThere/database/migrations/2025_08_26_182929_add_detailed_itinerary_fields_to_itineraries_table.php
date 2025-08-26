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
        Schema::table('itineraries', function (Blueprint $table) {
            $table->json('route_coordinates')->nullable()->after('waypoints');
            $table->json('daily_schedule')->nullable()->after('route_coordinates');
            $table->json('transport_details')->nullable()->after('daily_schedule');
            $table->json('departure_info')->nullable()->after('transport_details');
            $table->json('arrival_info')->nullable()->after('departure_info');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('itineraries', function (Blueprint $table) {
            $table->dropColumn([
                'route_coordinates',
                'daily_schedule',
                'transport_details',
                'departure_info',
                'arrival_info',
            ]);
        });
    }
};

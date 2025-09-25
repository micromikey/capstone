<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('itineraries', function (Blueprint $table) {
            // Add columns only if they don't already exist to avoid migration failures on partial setups
            if (! Schema::hasColumn('itineraries', 'trail_name')) {
                $table->string('trail_name')->nullable()->after('user_id');
            }

            if (! Schema::hasColumn('itineraries', 'weather_conditions')) {
                $table->json('weather_conditions')->nullable()->after('route_summary');
            }

            if (! Schema::hasColumn('itineraries', 'route_description')) {
                $table->text('route_description')->nullable()->after('weather_conditions');
            }

            if (! Schema::hasColumn('itineraries', 'stopovers')) {
                $table->json('stopovers')->nullable()->after('route_description');
            }

            if (! Schema::hasColumn('itineraries', 'sidetrips')) {
                $table->json('sidetrips')->nullable()->after('stopovers');
            }

            if (! Schema::hasColumn('itineraries', 'route_coordinates')) {
                $table->json('route_coordinates')->nullable()->after('sidetrips');
            }
        });
    }

    public function down()
    {
        Schema::table('itineraries', function (Blueprint $table) {
            if (Schema::hasColumn('itineraries', 'route_coordinates')) {
                $table->dropColumn('route_coordinates');
            }
            if (Schema::hasColumn('itineraries', 'sidetrips')) {
                $table->dropColumn('sidetrips');
            }
            if (Schema::hasColumn('itineraries', 'stopovers')) {
                $table->dropColumn('stopovers');
            }
            if (Schema::hasColumn('itineraries', 'route_description')) {
                $table->dropColumn('route_description');
            }
            if (Schema::hasColumn('itineraries', 'weather_conditions')) {
                $table->dropColumn('weather_conditions');
            }
            if (Schema::hasColumn('itineraries', 'trail_name')) {
                $table->dropColumn('trail_name');
            }
        });
    }
};

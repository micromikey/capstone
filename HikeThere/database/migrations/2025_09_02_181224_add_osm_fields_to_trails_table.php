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
        Schema::table('trails', function (Blueprint $table) {
            // Add OSM fields for OpenStreetMap integration
            $table->bigInteger('osm_id')->unique()->nullable()->after('id');
            $table->string('name')->nullable()->after('osm_id');
            $table->json('geometry')->nullable()->after('coordinates');
            $table->string('region')->nullable()->after('geometry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trails', function (Blueprint $table) {
            // Drop OSM fields
            $table->dropColumn(['osm_id', 'name', 'geometry', 'region']);
        });
    }
};

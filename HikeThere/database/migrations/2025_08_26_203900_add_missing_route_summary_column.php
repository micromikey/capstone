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
            if (! Schema::hasColumn('itineraries', 'route_summary')) {
                $table->json('route_summary')->nullable()->after('route_data');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('itineraries', function (Blueprint $table) {
            if (Schema::hasColumn('itineraries', 'route_summary')) {
                $table->dropColumn('route_summary');
            }
        });
    }
};

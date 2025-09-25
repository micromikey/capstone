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
        Schema::table('trail_packages', function (Blueprint $table) {
            // store simple time values (HH:MM) for schedule-related fields
            $table->time('opening_time')->nullable()->after('side_trips');
            $table->time('closing_time')->nullable()->after('opening_time');
            $table->time('pickup_time')->nullable()->after('closing_time');
            $table->time('departure_time')->nullable()->after('pickup_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trail_packages', function (Blueprint $table) {
            $table->dropColumn(['opening_time', 'closing_time', 'pickup_time', 'departure_time']);
        });
    }
};

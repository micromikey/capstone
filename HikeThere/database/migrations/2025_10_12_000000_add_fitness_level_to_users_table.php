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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('fitness_level', ['beginner', 'intermediate', 'advanced'])
                  ->default('intermediate')
                  ->after('preferences_onboarded_at')
                  ->comment('User fitness level for personalized itinerary pacing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('fitness_level');
        });
    }
};

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
            if (!Schema::hasColumn('trails', 'commute_legs')) {
                // Store commute legs as JSON/text
                $table->text('commute_legs')->nullable()->after('transportation_details');
            }
            if (!Schema::hasColumn('trails', 'commute_summary')) {
                $table->string('commute_summary')->nullable()->after('commute_legs');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trails', function (Blueprint $table) {
            if (Schema::hasColumn('trails', 'commute_summary')) {
                $table->dropColumn('commute_summary');
            }
            if (Schema::hasColumn('trails', 'commute_legs')) {
                $table->dropColumn('commute_legs');
            }
        });
    }
};

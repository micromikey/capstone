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
            // Add new transport_details if not present
            if (!Schema::hasColumn('trails', 'transport_details')) {
                $table->text('transport_details')->nullable()->after('transport_included');
            }

            // Drop old columns if they exist
            if (Schema::hasColumn('trails', 'transport_options')) {
                $table->dropColumn('transport_options');
            }
            if (Schema::hasColumn('trails', 'departure_point')) {
                $table->dropColumn('departure_point');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trails', function (Blueprint $table) {
            // Recreate old columns if missing
            if (!Schema::hasColumn('trails', 'departure_point')) {
                $table->string('departure_point')->nullable()->after('permit_process');
            }
            if (!Schema::hasColumn('trails', 'transport_options')) {
                $table->text('transport_options')->nullable()->after('departure_point');
            }

            if (Schema::hasColumn('trails', 'transport_details')) {
                $table->dropColumn('transport_details');
            }
        });
    }
};

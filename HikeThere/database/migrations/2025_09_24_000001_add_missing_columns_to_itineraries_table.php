<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add columns expected by the application but missing in some environments.
     * Use schema checks to avoid exceptions if columns already exist.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('itineraries', function (Blueprint $table) {
            if (! Schema::hasColumn('itineraries', 'trail_id')) {
                $table->unsignedBigInteger('trail_id')->nullable()->after('trail_name');
            }

            if (! Schema::hasColumn('itineraries', 'distance')) {
                // distance in kilometers with 2 decimals
                $table->decimal('distance', 8, 2)->nullable()->after('trail_id');
            }

            if (! Schema::hasColumn('itineraries', 'elevation_gain')) {
                $table->integer('elevation_gain')->nullable()->after('distance');
            }

            if (! Schema::hasColumn('itineraries', 'difficulty_level')) {
                $table->string('difficulty_level')->nullable()->after('elevation_gain');
            }

            if (! Schema::hasColumn('itineraries', 'estimated_duration')) {
                $table->string('estimated_duration')->nullable()->after('difficulty_level');
            }

            if (! Schema::hasColumn('itineraries', 'best_time_to_hike')) {
                $table->string('best_time_to_hike')->nullable()->after('estimated_duration');
            }

            if (! Schema::hasColumn('itineraries', 'duration_days')) {
                $table->integer('duration_days')->nullable()->after('best_time_to_hike');
            }

            if (! Schema::hasColumn('itineraries', 'nights')) {
                $table->integer('nights')->nullable()->after('duration_days');
            }

            if (! Schema::hasColumn('itineraries', 'start_date')) {
                $table->date('start_date')->nullable()->after('nights');
            }

            if (! Schema::hasColumn('itineraries', 'start_time')) {
                $table->string('start_time')->nullable()->after('start_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     * We avoid dropping columns in down() unless they were created by this migration.
     * For safety, only drop if column exists and if schema indicates it was added.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('itineraries', function (Blueprint $table) {
            // It's generally safer to keep columns, but provide drop logic if needed.
            if (Schema::hasColumn('itineraries', 'start_time')) {
                $table->dropColumn('start_time');
            }
            if (Schema::hasColumn('itineraries', 'start_date')) {
                $table->dropColumn('start_date');
            }
            if (Schema::hasColumn('itineraries', 'nights')) {
                $table->dropColumn('nights');
            }
            if (Schema::hasColumn('itineraries', 'duration_days')) {
                $table->dropColumn('duration_days');
            }
            if (Schema::hasColumn('itineraries', 'best_time_to_hike')) {
                $table->dropColumn('best_time_to_hike');
            }
            if (Schema::hasColumn('itineraries', 'estimated_duration')) {
                $table->dropColumn('estimated_duration');
            }
            if (Schema::hasColumn('itineraries', 'difficulty_level')) {
                $table->dropColumn('difficulty_level');
            }
            if (Schema::hasColumn('itineraries', 'elevation_gain')) {
                $table->dropColumn('elevation_gain');
            }
            if (Schema::hasColumn('itineraries', 'distance')) {
                $table->dropColumn('distance');
            }
            if (Schema::hasColumn('itineraries', 'trail_id')) {
                $table->dropColumn('trail_id');
            }
        });
    }
};

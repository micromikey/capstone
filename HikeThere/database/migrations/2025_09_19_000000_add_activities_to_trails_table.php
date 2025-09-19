<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trails', function (Blueprint $table) {
            if (!Schema::hasColumn('trails', 'activities')) {
                $table->json('activities')->nullable()->after('features')->comment('Array of activity tags (e.g., hiking, camping, trail_running)');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trails', function (Blueprint $table) {
            if (Schema::hasColumn('trails', 'activities')) {
                $table->dropColumn('activities');
            }
        });
    }
};

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
            if (!Schema::hasColumn('trails', 'transport_included')) {
                // Don't rely on specific column order; append to the end to be robust
                $table->boolean('transport_included')->default(false);
            }
            if (!Schema::hasColumn('trails', 'transportation_details')) {
                $table->text('transportation_details')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trails', function (Blueprint $table) {
            if (Schema::hasColumn('trails', 'transportation_details')) {
                $table->dropColumn('transportation_details');
            }
            if (Schema::hasColumn('trails', 'transport_included')) {
                $table->dropColumn('transport_included');
            }
        });
    }
};

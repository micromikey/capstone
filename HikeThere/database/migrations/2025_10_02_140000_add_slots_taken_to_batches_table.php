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
        Schema::table('batches', function (Blueprint $table) {
            // Track how many slots are currently reserved/taken
            $table->integer('slots_taken')->default(0)->after('capacity');
            
            // Add index for faster slot availability queries
            $table->index(['starts_at', 'slots_taken']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->dropIndex(['starts_at', 'slots_taken']);
            $table->dropColumn('slots_taken');
        });
    }
};

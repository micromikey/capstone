<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Trail;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Clear hardcoded coordinates from trails
        // This will force regeneration using Google APIs
        Trail::whereNotNull('coordinates')->update(['coordinates' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot restore previous coordinates
        // They will need to be regenerated
    }
};

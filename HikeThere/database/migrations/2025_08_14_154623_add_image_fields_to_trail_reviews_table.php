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
        Schema::table('trail_reviews', function (Blueprint $table) {
            // Add image fields directly to trail_reviews table
            $table->text('review_images')->nullable()->after('moderation_feedback');
            $table->text('image_captions')->nullable()->after('review_images');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trail_reviews', function (Blueprint $table) {
            $table->dropColumn(['review_images', 'image_captions']);
        });
    }
};

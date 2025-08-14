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
            $table->boolean('is_approved')->default(true)->after('conditions');
            $table->integer('moderation_score')->default(100)->after('is_approved');
            $table->json('moderation_feedback')->nullable()->after('moderation_score');
            
            $table->index(['is_approved', 'moderation_score']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trail_reviews', function (Blueprint $table) {
            $table->dropIndex(['is_approved', 'moderation_score']);
            $table->dropColumn(['is_approved', 'moderation_score', 'moderation_feedback']);
        });
    }
};

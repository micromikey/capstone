<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('trails', function (Blueprint $table) {
            if (!Schema::hasColumn('trails','mountain_id')) {
                $table->foreignId('mountain_id')->nullable()->after('location_id')->constrained('mountains')->nullOnDelete();
                $table->index(['mountain_id','difficulty']);
            }
            // Optional composite uniqueness to avoid duplicate mountain+trail pairs
            if (!Schema::hasColumn('trails','mountain_name')) {
                // already exists; do nothing
            }
        });
        // Add composite index if not existing (Laravel doesn't expose direct existence check; wrapped in try)
        try {
            Schema::table('trails', function (Blueprint $table) {
                $table->unique(['mountain_id','trail_name']);
            });
        } catch(\Throwable $e) {
            // silently ignore if already present or fails
        }
    }

    public function down(): void
    {
        Schema::table('trails', function (Blueprint $table) {
            if (Schema::hasColumn('trails','mountain_id')) {
                $table->dropUnique(['mountain_id','trail_name']);
                $table->dropConstrainedForeignId('mountain_id');
            }
        });
    }
};

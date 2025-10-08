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
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'emergency_readiness_id')) {
                $table->foreignId('emergency_readiness_id')->nullable()->after('payment_verified_by')->constrained('emergency_readiness')->onDelete('set null');
            }
            if (!Schema::hasColumn('bookings', 'feedback_requested_at')) {
                $table->timestamp('feedback_requested_at')->nullable()->after('emergency_readiness_id');
            }
            if (!Schema::hasColumn('bookings', 'feedback_submitted_at')) {
                $table->timestamp('feedback_submitted_at')->nullable()->after('feedback_requested_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['emergency_readiness_id']);
            $table->dropColumn(['emergency_readiness_id', 'feedback_requested_at', 'feedback_submitted_at']);
        });
    }
};

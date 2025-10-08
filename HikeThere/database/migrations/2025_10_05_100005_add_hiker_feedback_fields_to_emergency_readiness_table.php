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
        Schema::table('emergency_readiness', function (Blueprint $table) {
            // Add fields for hiker feedback
            if (!Schema::hasColumn('emergency_readiness', 'organization_id')) {
                $table->foreignId('organization_id')->nullable()->after('trail_id')->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('emergency_readiness', 'submitted_by')) {
                $table->foreignId('submitted_by')->nullable()->after('assessed_by')->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('emergency_readiness', 'first_aid_score')) {
                $table->integer('first_aid_score')->nullable()->after('communication_status')->comment('0-100 score for first aid readiness');
            }
            if (!Schema::hasColumn('emergency_readiness', 'emergency_access_score')) {
                $table->integer('emergency_access_score')->nullable()->after('first_aid_score')->comment('0-100 score for emergency access');
            }
            if (!Schema::hasColumn('emergency_readiness', 'overall_score')) {
                $table->integer('overall_score')->nullable()->after('emergency_access_score')->comment('Overall readiness score');
            }
            if (!Schema::hasColumn('emergency_readiness', 'readiness_level')) {
                $table->string('readiness_level')->nullable()->after('overall_score');
            }
            if (!Schema::hasColumn('emergency_readiness', 'comments')) {
                $table->text('comments')->nullable()->after('recommendations');
            }
            if (!Schema::hasColumn('emergency_readiness', 'assessment_date')) {
                $table->timestamp('assessment_date')->nullable()->after('comments');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emergency_readiness', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropForeign(['submitted_by']);
            $table->dropColumn([
                'organization_id',
                'submitted_by',
                'first_aid_score',
                'emergency_access_score',
                'overall_score',
                'readiness_level',
                'comments',
                'assessment_date'
            ]);
        });
    }
};

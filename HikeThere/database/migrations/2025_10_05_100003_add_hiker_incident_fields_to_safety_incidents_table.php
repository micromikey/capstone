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
        Schema::table('safety_incidents', function (Blueprint $table) {
            if (!Schema::hasColumn('safety_incidents', 'organization_id')) {
                $table->foreignId('organization_id')->nullable()->after('trail_id')->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('safety_incidents', 'incident_type')) {
                $table->string('incident_type')->nullable()->after('reported_by'); // injury, accident, hazard, wildlife, weather, equipment, other
            }
            if (!Schema::hasColumn('safety_incidents', 'location')) {
                $table->string('location', 500)->nullable()->after('incident_type');
            }
            if (!Schema::hasColumn('safety_incidents', 'incident_date')) {
                $table->date('incident_date')->nullable()->after('location');
            }
            if (!Schema::hasColumn('safety_incidents', 'incident_time')) {
                $table->time('incident_time')->nullable()->after('incident_date');
            }
            
            // Update severity enum to use lowercase values
            $table->string('severity', 20)->default('medium')->change();
            
            // Update status enum to use lowercase values
            $table->string('status', 20)->default('reported')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('safety_incidents', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn(['organization_id', 'incident_type', 'location', 'incident_date', 'incident_time']);
        });
    }
};

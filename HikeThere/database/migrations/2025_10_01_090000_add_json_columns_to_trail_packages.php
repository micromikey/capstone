<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trail_packages', function (Blueprint $table) {
            $table->json('package_inclusions_json')->nullable()->after('package_inclusions');
            $table->json('side_trips_json')->nullable()->after('side_trips');
        });
    }

    public function down(): void
    {
        Schema::table('trail_packages', function (Blueprint $table) {
            $table->dropColumn(['package_inclusions_json', 'side_trips_json']);
        });
    }
};

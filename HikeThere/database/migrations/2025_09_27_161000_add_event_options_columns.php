<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('duration')->nullable()->after('end_at');
            $table->boolean('always_available')->default(false)->after('duration');
            $table->boolean('manual_batches')->default(false)->after('always_available');
        });
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['duration','always_available','manual_batches']);
        });
    }
};

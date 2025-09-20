<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('user_type', ['admin', 'guide', 'hiker', 'moderator'])->default('hiker')->after('email');
            $table->enum('status', ['active', 'suspended', 'banned', 'inactive'])->default('active')->after('user_type');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['user_type', 'status']);
        });
    }
};
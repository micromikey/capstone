<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('emergency_readiness', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trail_id')->constrained()->onDelete('cascade');
            $table->enum('equipment_status', ['excellent', 'good', 'fair', 'poor', 'critical']);
            $table->enum('staff_availability', ['excellent', 'good', 'fair', 'poor', 'critical']);
            $table->enum('communication_status', ['excellent', 'good', 'fair', 'poor', 'critical']);
            $table->date('last_inspection_date')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('emergency_readiness');
    }
};

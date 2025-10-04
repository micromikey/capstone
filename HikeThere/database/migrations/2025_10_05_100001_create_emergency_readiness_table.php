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
        Schema::create('emergency_readiness', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trail_id')->constrained()->onDelete('cascade');
            $table->integer('equipment_status')->default(0)->comment('0-100 score');
            $table->integer('staff_availability')->default(0)->comment('0-100 score');
            $table->integer('communication_status')->default(0)->comment('0-100 score');
            $table->text('equipment_notes')->nullable();
            $table->text('staff_notes')->nullable();
            $table->text('communication_notes')->nullable();
            $table->text('recommendations')->nullable();
            $table->foreignId('assessed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergency_readiness');
    }
};

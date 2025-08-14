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
        Schema::create('user_follows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hiker_id'); // The hiker who is following
            $table->unsignedBigInteger('organization_id'); // The organization being followed
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('hiker_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('organization_id')->references('id')->on('users')->onDelete('cascade');
            
            // Ensure a hiker can only follow an organization once
            $table->unique(['hiker_id', 'organization_id']);
            
            // Index for faster queries
            $table->index(['hiker_id']);
            $table->index(['organization_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_follows');
    }
};

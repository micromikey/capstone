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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // e.g., 'trail_update', 'security_alert', 'system', etc.
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // Additional data (trail_id, link, etc.)
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['user_id', 'read_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

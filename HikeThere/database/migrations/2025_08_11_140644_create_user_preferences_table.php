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
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Notification preferences (non-sensitive)
            $table->boolean('email_notifications')->default(true);
            $table->boolean('push_notifications')->default(true);
            $table->boolean('trail_updates')->default(true);
            $table->boolean('security_alerts')->default(true);
            $table->boolean('newsletter')->default(false);
            
            // Privacy settings (non-sensitive)
            $table->enum('profile_visibility', ['public', 'private'])->default('public');
            $table->boolean('show_email')->default(false);
            $table->boolean('show_phone')->default(false);
            $table->boolean('show_location')->default(true);
            $table->boolean('show_birth_date')->default(false);
            $table->boolean('show_hiking_preferences')->default(true);
            
            // Account settings
            $table->boolean('two_factor_required')->default(false);
            $table->string('timezone', 50)->default('Asia/Manila');
            $table->string('language', 10)->default('en');
            
            $table->timestamps();
            
            // Ensure one preference record per user
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};

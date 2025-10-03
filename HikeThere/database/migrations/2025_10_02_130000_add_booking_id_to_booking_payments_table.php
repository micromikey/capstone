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
        Schema::table('booking_payments', function (Blueprint $table) {
            // Add foreign key to link with bookings table
            $table->foreignId('booking_id')->nullable()->after('id')->constrained('bookings')->onDelete('cascade');
            
            // Add user_id for quick reference (redundant but useful for queries)
            $table->foreignId('user_id')->nullable()->after('booking_id')->constrained('users')->onDelete('cascade');
            
            // Add index for faster queries
            $table->index(['booking_id', 'payment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_payments', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);
            $table->dropForeign(['user_id']);
            $table->dropIndex(['booking_id', 'payment_status']);
            $table->dropColumn(['booking_id', 'user_id']);
        });
    }
};

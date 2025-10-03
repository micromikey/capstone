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
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('payment_proof_path')->nullable();
            $table->string('transaction_number')->nullable();
            $table->text('payment_notes')->nullable();
            $table->enum('payment_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->enum('payment_method_used', ['manual', 'automatic'])->nullable();
            $table->timestamp('payment_verified_at')->nullable();
            $table->unsignedBigInteger('payment_verified_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'payment_proof_path',
                'transaction_number',
                'payment_notes',
                'payment_status',
                'payment_method_used',
                'payment_verified_at',
                'payment_verified_by'
            ]);
        });
    }
};

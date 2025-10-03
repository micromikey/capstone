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
        Schema::create('organization_payment_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // organization user
            
            // PayMongo credentials
            $table->text('paymongo_secret_key')->nullable();
            $table->text('paymongo_public_key')->nullable();
            
            // Xendit credentials (hardcoded for now, but stored for future use)
            $table->text('xendit_api_key')->nullable();
            
            // Active payment gateway
            $table->enum('active_gateway', ['paymongo', 'xendit'])->default('paymongo');
            
            // Status
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_payment_credentials');
    }
};

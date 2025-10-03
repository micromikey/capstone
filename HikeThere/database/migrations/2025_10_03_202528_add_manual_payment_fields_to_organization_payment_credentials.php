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
        Schema::table('organization_payment_credentials', function (Blueprint $table) {
            $table->string('qr_code_path')->nullable()->after('active_gateway');
            $table->enum('payment_method', ['manual', 'automatic'])->default('automatic')->after('qr_code_path');
            $table->text('manual_payment_instructions')->nullable()->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organization_payment_credentials', function (Blueprint $table) {
            $table->dropColumn(['qr_code_path', 'payment_method', 'manual_payment_instructions']);
        });
    }
};

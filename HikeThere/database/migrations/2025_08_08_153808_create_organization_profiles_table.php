<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('organization_name');
            $table->text('organization_description');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('name');
            // Store hashed password for security
            $table->string('password');
            $table->string('business_permit_path');
            $table->string('government_id_path');
            $table->json('additional_docs')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_profiles');
    }
};
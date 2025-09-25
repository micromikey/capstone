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
        Schema::create('trail_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trail_id')->constrained('trails')->onDelete('cascade');
            $table->decimal('price', 10, 2)->nullable();
            $table->text('package_inclusions')->nullable();
            $table->string('duration')->nullable();
            $table->boolean('permit_required')->default(false);
            $table->text('permit_process')->nullable();
            $table->boolean('transport_included')->default(false);
            $table->text('transport_details')->nullable();
            $table->text('transportation_details')->nullable();
            $table->text('commute_legs')->nullable();
            $table->string('commute_summary')->nullable();
            $table->text('side_trips')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trail_packages');
    }
};

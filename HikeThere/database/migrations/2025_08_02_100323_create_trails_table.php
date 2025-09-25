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
        Schema::create('trails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Organization that owns the trail
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
            $table->string('mountain_name');
            $table->string('trail_name');
            $table->string('slug')->unique();
            // price, package_inclusions moved to trail_packages table
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced']);
            $table->text('difficulty_description')->nullable(); // Description of difficulty level
            $table->string('best_season'); // Best months or season to hike
            $table->text('terrain_notes'); // Trail characteristics
            $table->text('other_trail_notes')->nullable(); // Additional notes, rules, guidelines
            // duration, permit_required, permit_process, departure_point, side_trips moved to trail_packages
            $table->text('packing_list'); // Suggested items hikers should bring
            $table->text('health_fitness'); // Fitness requirements
            $table->text('requirements')->nullable(); // Other requirements
            $table->text('emergency_contacts'); // Emergency numbers or local rescue units
            $table->text('campsite_info')->nullable(); // Campsite availability info
            $table->text('guide_info')->nullable(); // Guide information
            $table->text('environmental_practices')->nullable(); // Environmental practices
            $table->text('customers_feedback')->nullable(); // Customer feedback and comments
            $table->text('testimonials_faqs')->nullable(); // Testimonials and common FAQs
            $table->decimal('length', 8, 2)->nullable(); // in kilometers
            $table->integer('elevation_gain')->nullable(); // in meters
            $table->integer('elevation_high')->nullable(); // highest point in meters
            $table->integer('elevation_low')->nullable(); // lowest point in meters
            $table->integer('estimated_time')->nullable(); // in minutes
            $table->text('summary')->nullable();
            $table->text('description')->nullable();
            $table->json('features')->nullable(); // ["scenic views", "waterfalls", "camping"]
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('gpx_file')->nullable(); // GPS track file
            $table->json('coordinates')->nullable(); // trail coordinates
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trails');
    }
};

<?php

namespace Database\Factories;

use App\Models\Trail;
use App\Models\User;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TrailFactory extends Factory
{
    protected $model = Trail::class;

    public function definition(): array
    {
        $mountain = $this->faker->unique()->word().' Mountain';
        $trailName = $this->faker->unique()->word().' Trail';
        return [
            'user_id' => User::factory(),
            'location_id' => Location::query()->inRandomOrder()->value('id') ?? Location::factory(),
            'mountain_name' => $mountain,
            'trail_name' => $trailName,
            'slug' => Str::slug($trailName.'-'.$mountain.'-'.uniqid()),
            'difficulty' => $this->faker->randomElement(['beginner','intermediate','advanced']),
            // package fields moved to TrailPackage; created in afterCreating callback
            'best_season' => 'November - February',
            'terrain_notes' => 'Rocky sections, forested ridge',
            'packing_list' => 'Water, Food, Headlamp',
            'health_fitness' => 'Good cardio fitness required',
            'emergency_contacts' => 'Local Rescue: 000-000-0000',
            'length' => null,
            'elevation_gain' => null,
            'elevation_high' => null,
            'elevation_low' => null,
            'estimated_time' => null,
            'is_active' => true,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (\App\Models\Trail $trail) {
            \App\Models\TrailPackage::create([
                'trail_id' => $trail->id,
                'price' => $this->faker->randomFloat(2, 50, 5000),
                'package_inclusions' => 'Guide, Permit, Meals',
                'duration' => '1 day',
            ]);
        });
    }
}

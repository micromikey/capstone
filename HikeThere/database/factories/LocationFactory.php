<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->city(),
            'province' => $this->faker->state(),
            'region' => 'Region '. $this->faker->numberBetween(1,12),
        ];
    }
}

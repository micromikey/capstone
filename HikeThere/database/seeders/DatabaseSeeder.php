<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Trail;
use App\Models\Location;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $mtPulag = Location::create([
            'name' => 'Mt. Pulag',
            'slug' => 'mt-pulag',
            'province' => 'Benguet',
            'region' => 'Cordillera Administrative Region',
            'latitude' => 16.5969,
            'longitude' => 120.8958,
            'description' => 'The highest peak in Luzon and third highest mountain in the Philippines.',
        ]);

        // Create trails for Mt. Pulag
        $trails = [
            [
                'name' => 'Ambangeg Trail',
                'difficulty' => 'beginner-friendly',
                'length' => 14.6,
                'elevation_gain' => 830,
                'elevation_high' => 2922,
                'elevation_low' => 2092,
                'estimated_time' => 328, // 5h 28m
                'summary' => 'Head out on this 14.6-km out-and-back trail near Kabayan, Benguet. Generally considered a challenging route, it takes an average of 5 h 28 min to complete.',
                'features' => ['sea of clouds', 'grassland', 'summit views'],
            ],
            [
                'name' => 'Akiki Trail',
                'difficulty' => 'challenging',
                'length' => 16.2,
                'elevation_gain' => 1122,
                'elevation_high' => 2922,
                'elevation_low' => 1800,
                'estimated_time' => 420, // 7h
                'summary' => 'The most challenging but rewarding trail to Mt. Pulag summit. Known for its killer trail and stunning views.',
                'features' => ['mossy forest', 'challenging terrain', 'diverse flora'],
            ],
        ];

        foreach ($trails as $trailData) {
            $trailData['location_id'] = $mtPulag->id;
            $trailData['slug'] = Str::slug($trailData['name']);
            $trailData['average_rating'] = fake()->randomFloat(1, 4.0, 5.0);
            $trailData['total_reviews'] = fake()->numberBetween(20, 100);

            Trail::create($trailData);
        }
    }
}

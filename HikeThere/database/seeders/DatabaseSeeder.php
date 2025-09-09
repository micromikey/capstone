<?php

namespace Database\Seeders;

use App\Models\Location;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Trail;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed peak data first (required for auto-route functionality)
        $this->call([
            LuzonPeaksSeeder::class,
        ]);

        // Create a sample organization user
        $organizationUser = User::create([
            'name' => 'Sample Organization',
            'email' => 'org@example.com',
            'password' => bcrypt('password'),
            'user_type' => 'organization',
            'approval_status' => 'approved',
            'email_verified_at' => now(),
        ]);

        // Create sample locations
        $locations = [
            [
                'name' => 'Mt. Pulag',
                'slug' => 'mt-pulag',
                'province' => 'Benguet',
                'region' => 'Cordillera Administrative Region',
                'latitude' => 16.5969,
                'longitude' => 120.8958,
                'description' => 'The highest peak in Luzon and third highest mountain in the Philippines.',
            ],
            [
                'name' => 'Mt. Arayat',
                'slug' => 'mt-arayat',
                'province' => 'Pampanga',
                'region' => 'Central Luzon',
                'latitude' => 15.2000,
                'longitude' => 120.7500,
                'description' => 'A potentially active stratovolcano in Pampanga.',
            ],
            [
                'name' => 'Mt. Pinatubo',
                'slug' => 'mt-pinatubo',
                'province' => 'Zambales',
                'region' => 'Central Luzon',
                'latitude' => 15.1300,
                'longitude' => 120.3500,
                'description' => 'An active stratovolcano in the Zambales Mountains.',
            ],
            [
                'name' => 'Mt. Batulao',
                'slug' => 'mt-batulao',
                'province' => 'Batangas',
                'region' => 'Calabarzon',
                'latitude' => 14.0333,
                'longitude' => 120.8000,
                'description' => 'A popular hiking destination in Batangas.',
            ],
            [
                'name' => 'Mt. Daraitan',
                'slug' => 'mt-daraitan',
                'province' => 'Rizal',
                'region' => 'Calabarzon',
                'latitude' => 14.6167,
                'longitude' => 121.4333,
                'description' => 'A mountain in Rizal with scenic views of Sierra Madre.',
            ],
            [
                'name' => 'Mt. Ulap',
                'slug' => 'mt-ulap',
                'province' => 'Benguet',
                'region' => 'Cordillera Administrative Region',
                'latitude' => 16.4167,
                'longitude' => 120.7500,
                'description' => 'A mountain in Benguet known for its rolling hills.',
            ],
            [
                'name' => 'Mt. Pico de Loro',
                'slug' => 'mt-pico-de-loro',
                'province' => 'Cavite',
                'region' => 'Calabarzon',
                'latitude' => 14.2167,
                'longitude' => 120.6333,
                'description' => 'A mountain in Cavite with a distinctive rock formation.',
            ],
            [
                'name' => 'Mt. Tapulao',
                'slug' => 'mt-tapulao',
                'province' => 'Zambales',
                'region' => 'Central Luzon',
                'latitude' => 15.4833,
                'longitude' => 120.1167,
                'description' => 'The highest mountain in Zambales.',
            ],
        ];

        foreach ($locations as $locationData) {
            Location::create($locationData);
        }

        // Get Mt. Pulag for trail creation
        $mtPulag = Location::where('name', 'Mt. Pulag')->first();

        // Create trails for Mt. Pulag
        $trails = [
            [
                'user_id' => $organizationUser->id,
                'trail_name' => 'Ambangeg Trail',
                'mountain_name' => 'Mt. Pulag',
                'difficulty' => 'beginner',
                'length' => 14.6,
                'elevation_gain' => 830,
                'elevation_high' => 2922,
                'elevation_low' => 2092,
                'estimated_time' => 328, // 5h 28m
                'summary' => 'Head out on this 14.6-km out-and-back trail near Kabayan, Benguet. Generally considered a challenging route, it takes an average of 5 h 28 min to complete.',
                'features' => ['sea of clouds', 'grassland', 'summit views'],
                'price' => 2500.00,
                'package_inclusions' => 'Guide, Meals, Environmental Fee',
                'duration' => '5-6 hours',
                'best_season' => 'November to March',
                'terrain_notes' => 'Grassland, forest, rocky terrain',
                'departure_point' => 'Baguio City',
                'transport_options' => 'Bus to Baguio, Van to jump-off',
                'packing_list' => 'Water, Flashlight, Raincoat, First Aid Kit',
                'health_fitness' => 'Moderate fitness required',
                'emergency_contacts' => 'Barangay Rescue - 0917xxxxxxx',
            ],
            [
                'user_id' => $organizationUser->id,
                'trail_name' => 'Akiki Trail',
                'mountain_name' => 'Mt. Pulag',
                'difficulty' => 'advanced',
                'length' => 16.2,
                'elevation_gain' => 1122,
                'elevation_high' => 2922,
                'elevation_low' => 1800,
                'estimated_time' => 420, // 7h
                'summary' => 'The most challenging but rewarding trail to Mt. Pulag summit. Known for its killer trail and stunning views.',
                'features' => ['mossy forest', 'challenging terrain', 'diverse flora'],
                'price' => 3000.00,
                'package_inclusions' => 'Guide, Meals, Environmental Fee, Transportation',
                'duration' => '7-8 hours',
                'best_season' => 'November to March',
                'terrain_notes' => 'Steep slopes, mossy forest, challenging terrain',
                'departure_point' => 'Baguio City',
                'transport_options' => 'Bus to Baguio, Van to jump-off',
                'packing_list' => 'Water, Flashlight, Raincoat, First Aid Kit, Snacks',
                'health_fitness' => 'High fitness level required',
                'emergency_contacts' => 'Barangay Rescue - 0917xxxxxxx',
            ],
        ];

        foreach ($trails as $trailData) {
            $trailData['location_id'] = $mtPulag->id;
            $trailData['slug'] = Str::slug($trailData['trail_name']);
            $trailData['is_active'] = true; // Ensure trails are active

            // Add missing required fields
            if (! isset($trailData['description'])) {
                $trailData['description'] = $trailData['summary'];
            }
            if (! isset($trailData['coordinates'])) {
                $trailData['coordinates'] = null;
            }
            if (! isset($trailData['gpx_file'])) {
                $trailData['gpx_file'] = null;
            }

            $trail = Trail::create($trailData);

            // Create trail images using the image service
            $this->createTrailImages($trail);
        }

        // Create trails for other mountains too
        $this->createMoreTrails($organizationUser);

        // Seed profile data
        $this->call(ProfileSeeder::class);
    }

    /**
     * Create trail images for a trail
     */
    protected function createTrailImages($trail)
    {
        // Create primary image
        \App\Models\TrailImage::create([
            'trail_id' => $trail->id,
            'image_path' => "https://picsum.photos/seed/{$trail->id}/800/600",
            'image_type' => 'primary',
            'caption' => "Beautiful view of {$trail->mountain_name}",
            'sort_order' => 1,
            'is_primary' => true,
        ]);

        // Create additional photos
        for ($i = 2; $i <= 4; $i++) {
            \App\Models\TrailImage::create([
                'trail_id' => $trail->id,
                'image_path' => 'https://picsum.photos/seed/'.($trail->id + $i * 100).'/800/600',
                'image_type' => 'photo',
                'caption' => 'Trail view '.($i - 1),
                'sort_order' => $i,
                'is_primary' => false,
            ]);
        }

        // Create map image
        \App\Models\TrailImage::create([
            'trail_id' => $trail->id,
            'image_path' => 'https://picsum.photos/seed/'.($trail->id + 1000).'/800/600',
            'image_type' => 'map',
            'caption' => "{$trail->trail_name} Trail Map",
            'sort_order' => 5,
            'is_primary' => false,
        ]);
    }

    /**
     * Create trails for other mountains
     */
    protected function createMoreTrails($organizationUser)
    {
        $locations = Location::all();

        $additionalTrails = [
            // Mt. Arayat trails
            [
                'location_name' => 'Mt. Arayat',
                'trails' => [
                    [
                        'trail_name' => 'White Rock Trail',
                        'difficulty' => 'intermediate',
                        'length' => 8.5,
                        'elevation_gain' => 950,
                        'elevation_high' => 1026,
                        'elevation_low' => 76,
                        'estimated_time' => 240, // 4h
                        'summary' => 'A scenic trail leading to the White Rock formation with panoramic views.',
                        'features' => ['rock formations', 'city views', 'moderate difficulty'],
                        'price' => 1800.00,
                        'duration' => '4-5 hours',
                        'best_season' => 'October to April',
                    ],
                ],
            ],
            // Mt. Pinatubo trails
            [
                'location_name' => 'Mt. Pinatubo',
                'trails' => [
                    [
                        'trail_name' => 'Crater Lake Trail',
                        'difficulty' => 'beginner',
                        'length' => 12.0,
                        'elevation_gain' => 600,
                        'elevation_high' => 1486,
                        'elevation_low' => 886,
                        'estimated_time' => 300, // 5h
                        'summary' => 'Journey to the famous crater lake of Mt. Pinatubo through lahar formations.',
                        'features' => ['crater lake', 'lahar formations', 'historical significance'],
                        'price' => 2200.00,
                        'duration' => '5-6 hours',
                        'best_season' => 'November to May',
                    ],
                ],
            ],
            // Mt. Batulao trails
            [
                'location_name' => 'Mt. Batulao',
                'trails' => [
                    [
                        'trail_name' => 'New Trail',
                        'difficulty' => 'intermediate',
                        'length' => 10.2,
                        'elevation_gain' => 693,
                        'elevation_high' => 811,
                        'elevation_low' => 118,
                        'estimated_time' => 240, // 4h
                        'summary' => 'Rolling hills and grasslands make this a favorite among day hikers.',
                        'features' => ['rolling hills', 'grasslands', 'day hike'],
                        'price' => 1500.00,
                        'duration' => '4-5 hours',
                        'best_season' => 'October to March',
                    ],
                ],
            ],
            // Mt. Daraitan trails
            [
                'location_name' => 'Mt. Daraitan',
                'trails' => [
                    [
                        'trail_name' => 'Tinipak River Trail',
                        'difficulty' => 'beginner',
                        'length' => 6.8,
                        'elevation_gain' => 420,
                        'elevation_high' => 739,
                        'elevation_low' => 319,
                        'estimated_time' => 180, // 3h
                        'summary' => 'Combine mountain hiking with river trekking for a unique adventure.',
                        'features' => ['river trekking', 'rock formations', 'swimming'],
                        'price' => 1200.00,
                        'duration' => '3-4 hours',
                        'best_season' => 'March to June',
                        'coordinates' => json_encode(['lat' => 14.6167, 'lng' => 121.4000]), // Mt. Daraitan coordinates
                    ],
                ],
            ],
            // Mt. Pico de Loro trails
            [
                'location_name' => 'Mt. Pico de Loro',
                'trails' => [
                    [
                        'trail_name' => 'Monolith Trail',
                        'difficulty' => 'advanced',
                        'length' => 9.4,
                        'elevation_gain' => 664,
                        'elevation_high' => 664,
                        'elevation_low' => 0,
                        'estimated_time' => 360, // 6h
                        'summary' => 'Challenge yourself with technical climbing to reach the famous monolith.',
                        'features' => ['monolith climbing', 'technical sections', 'sea views'],
                        'price' => 2800.00,
                        'duration' => '6-7 hours',
                        'best_season' => 'November to April',
                    ],
                ],
            ],
        ];

        foreach ($additionalTrails as $locationTrails) {
            $location = $locations->where('name', $locationTrails['location_name'])->first();

            if ($location) {
                foreach ($locationTrails['trails'] as $trailData) {
                    $trailData['user_id'] = $organizationUser->id;
                    $trailData['location_id'] = $location->id;
                    $trailData['mountain_name'] = $location->name;
                    $trailData['slug'] = Str::slug($trailData['trail_name']);
                    $trailData['is_active'] = true;
                    $trailData['package_inclusions'] = 'Guide, Environmental Fee, Basic Meals';
                    $trailData['departure_point'] = 'Manila';
                    $trailData['transport_options'] = 'Bus or Private Vehicle';
                    $trailData['packing_list'] = 'Water, Snacks, First Aid, Flashlight';
                    $trailData['health_fitness'] = 'Basic to moderate fitness required';
                    $trailData['emergency_contacts'] = 'Local Guide - 0917xxxxxxx';
                    $trailData['terrain_notes'] = 'Mixed terrain with forest and rocky sections';
                    $trailData['other_trail_notes'] = 'Contact guide 24 hours in advance';
                    $trailData['permit_required'] = false;
                    $trailData['permit_process'] = null;
                    $trailData['side_trips'] = 'Photography spots along the trail';
                    $trailData['requirements'] = 'Valid ID and waiver form';
                    $trailData['campsite_info'] = 'Day hike only - no camping allowed';
                    $trailData['guide_info'] = 'Local certified mountain guide required';
                    $trailData['environmental_practices'] = 'Leave no trace - pack out all trash';
                    $trailData['customers_feedback'] = '';
                    $trailData['testimonials_faqs'] = '';
                    $trailData['description'] = $trailData['summary'];
                    $trailData['coordinates'] = null;
                    $trailData['gpx_file'] = null;

                    $trail = Trail::create($trailData);
                    $this->createTrailImages($trail);
                }
            }
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Peak;

class PeakSeeder extends Seeder
{
    public function run(): void
    {
        $peaks = [
            [
                'osm_id' => 1001,
                'name' => 'Mount Pulag',
                'latitude' => 16.5938,
                'longitude' => 120.8875,
                'elevation' => 2922,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Pulag'])
            ],
            [
                'osm_id' => 1002,
                'name' => 'Mount Apo',
                'latitude' => 7.0081,
                'longitude' => 125.2734,
                'elevation' => 2954,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Apo'])
            ],
            [
                'osm_id' => 1003,
                'name' => 'Mount Mayon',
                'latitude' => 13.2572,
                'longitude' => 123.6856,
                'elevation' => 2463,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Mayon'])
            ],
            [
                'osm_id' => 1004,
                'name' => 'Mount Pinatubo',
                'latitude' => 15.1430,
                'longitude' => 120.3526,
                'elevation' => 1486,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Pinatubo'])
            ],
            [
                'osm_id' => 1005,
                'name' => 'Mount Ulap',
                'latitude' => 16.4733,
                'longitude' => 120.7233,
                'elevation' => 1846,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Ulap'])
            ],
            [
                'osm_id' => 1006,
                'name' => 'Mount Batulao',
                'latitude' => 14.0200,
                'longitude' => 120.9200,
                'elevation' => 811,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Batulao'])
            ]
        ];

        foreach ($peaks as $peakData) {
            Peak::updateOrCreate(
                ['osm_id' => $peakData['osm_id']],
                $peakData
            );
        }

        $this->command->info('Created/updated ' . count($peaks) . ' peak records');
    }
}

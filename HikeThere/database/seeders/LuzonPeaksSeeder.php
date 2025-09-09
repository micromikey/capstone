<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Peak;

class LuzonPeaksSeeder extends Seeder
{
    public function run(): void
    {
        $luzonPeaks = [
            // Cordillera Administrative Region (CAR)
            [
                'osm_id' => 1001,
                'name' => 'Mount Pulag',
                'latitude' => 16.5938,
                'longitude' => 120.8875,
                'elevation' => 2922,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Pulag', 'region' => 'CAR'])
            ],
            [
                'osm_id' => 1002,
                'name' => 'Mount Ulap',
                'latitude' => 16.4733,
                'longitude' => 120.7233,
                'elevation' => 1846,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Ulap', 'region' => 'CAR'])
            ],
            [
                'osm_id' => 1003,
                'name' => 'Mount Napulauan',
                'latitude' => 16.4333,
                'longitude' => 120.6833,
                'elevation' => 1642,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Napulauan', 'region' => 'CAR'])
            ],
            [
                'osm_id' => 1004,
                'name' => 'Mount Timbac',
                'latitude' => 16.4000,
                'longitude' => 120.6000,
                'elevation' => 2717,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Timbac', 'region' => 'CAR'])
            ],
            [
                'osm_id' => 1005,
                'name' => 'Mount Tabayoc',
                'latitude' => 16.5500,
                'longitude' => 120.8000,
                'elevation' => 2842,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Tabayoc', 'region' => 'CAR'])
            ],
            [
                'osm_id' => 1006,
                'name' => 'Mount Pico de Loro',
                'latitude' => 16.4167,
                'longitude' => 120.6500,
                'elevation' => 2102,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Pico de Loro', 'region' => 'CAR'])
            ],

            // Central Luzon
            [
                'osm_id' => 1007,
                'name' => 'Mount Pinatubo',
                'latitude' => 15.1430,
                'longitude' => 120.3526,
                'elevation' => 1486,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Pinatubo', 'region' => 'Central Luzon'])
            ],
            [
                'osm_id' => 1008,
                'name' => 'Mount Arayat',
                'latitude' => 15.2000,
                'longitude' => 120.7333,
                'elevation' => 1033,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Arayat', 'region' => 'Central Luzon'])
            ],

            // CALABARZON (Cavite, Laguna, Batangas, Rizal, Quezon)
            [
                'osm_id' => 1009,
                'name' => 'Mount Batulao',
                'latitude' => 14.0200,
                'longitude' => 120.9200,
                'elevation' => 811,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Batulao', 'region' => 'CALABARZON'])
            ],
            [
                'osm_id' => 1010,
                'name' => 'Mount Makiling',
                'latitude' => 14.1333,
                'longitude' => 121.2000,
                'elevation' => 1090,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Makiling', 'region' => 'CALABARZON'])
            ],
            [
                'osm_id' => 1011,
                'name' => 'Mount Banahaw',
                'latitude' => 14.0667,
                'longitude' => 121.4833,
                'elevation' => 2170,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Banahaw', 'region' => 'CALABARZON'])
            ],
            [
                'osm_id' => 1012,
                'name' => 'Mount Talamitam',
                'latitude' => 14.0500,
                'longitude' => 120.9167,
                'elevation' => 630,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Talamitam', 'region' => 'CALABARZON'])
            ],
            [
                'osm_id' => 1013,
                'name' => 'Mount Maculot',
                'latitude' => 14.0833,
                'longitude' => 121.0500,
                'elevation' => 930,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Maculot', 'region' => 'CALABARZON'])
            ],
            [
                'osm_id' => 1014,
                'name' => 'Mount Daguldol',
                'latitude' => 13.9833,
                'longitude' => 120.8833,
                'elevation' => 672,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Daguldol', 'region' => 'CALABARZON'])
            ],
            [
                'osm_id' => 1015,
                'name' => 'Mount Gulugod Baboy',
                'latitude' => 13.9167,
                'longitude' => 120.9333,
                'elevation' => 525,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Gulugod Baboy', 'region' => 'CALABARZON'])
            ],
            [
                'osm_id' => 1016,
                'name' => 'Mount Taal',
                'latitude' => 14.0167,
                'longitude' => 120.9833,
                'elevation' => 311,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Taal', 'region' => 'CALABARZON'])
            ],
            [
                'osm_id' => 1017,
                'name' => 'Mount Pundaquit',
                'latitude' => 14.8833,
                'longitude' => 120.0333,
                'elevation' => 464,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Pundaquit', 'region' => 'CALABARZON'])
            ],
            [
                'osm_id' => 1018,
                'name' => 'Mount Cristobal',
                'latitude' => 14.0333,
                'longitude' => 121.4500,
                'elevation' => 1470,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Cristobal', 'region' => 'CALABARZON'])
            ],

            // Rizal Province
            [
                'osm_id' => 1019,
                'name' => 'Mount Pamitinan',
                'latitude' => 14.7167,
                'longitude' => 121.2500,
                'elevation' => 426,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Pamitinan', 'region' => 'CALABARZON'])
            ],
            [
                'osm_id' => 1020,
                'name' => 'Mount Binacayan',
                'latitude' => 14.7000,
                'longitude' => 121.2667,
                'elevation' => 424,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Binacayan', 'region' => 'CALABARZON'])
            ],
            [
                'osm_id' => 1021,
                'name' => 'Mount Daraitan',
                'latitude' => 14.7333,
                'longitude' => 121.3000,
                'elevation' => 739,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Daraitan', 'region' => 'CALABARZON'])
            ],

            // Quezon Province
            [
                'osm_id' => 1022,
                'name' => 'Mount Marami',
                'latitude' => 14.1000,
                'longitude' => 121.2333,
                'elevation' => 1405,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Marami', 'region' => 'CALABARZON'])
            ],
            [
                'osm_id' => 1023,
                'name' => 'Mount Pinagbanderahan',
                'latitude' => 14.4167,
                'longitude' => 121.5833,
                'elevation' => 1858,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Pinagbanderahan', 'region' => 'CALABARZON'])
            ],

            // Nueva Ecija
            [
                'osm_id' => 1024,
                'name' => 'Mount Balungao',
                'latitude' => 15.8667,
                'longitude' => 120.6833,
                'elevation' => 388,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Balungao', 'region' => 'Central Luzon'])
            ],

            // Ilocos Region
            [
                'osm_id' => 1025,
                'name' => 'Mount Sicapoo',
                'latitude' => 17.0833,
                'longitude' => 120.6500,
                'elevation' => 1865,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Sicapoo', 'region' => 'Ilocos'])
            ],

            // Aurora Province
            [
                'osm_id' => 1026,
                'name' => 'Mount Angara',
                'latitude' => 15.7500,
                'longitude' => 121.4167,
                'elevation' => 1633,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Angara', 'region' => 'Central Luzon'])
            ],

            // Popular hiking destinations with alternate names
            [
                'osm_id' => 1027,
                'name' => 'Pico de Loro',
                'latitude' => 14.1667,
                'longitude' => 120.8833,
                'elevation' => 664,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Pico de Loro', 'region' => 'CALABARZON'])
            ],
            [
                'osm_id' => 1028,
                'name' => 'Mount Sembrano',
                'latitude' => 14.0167,
                'longitude' => 120.9500,
                'elevation' => 458,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Sembrano', 'region' => 'CALABARZON'])
            ],
            [
                'osm_id' => 1029,
                'name' => 'Mount Hapunang Banoi',
                'latitude' => 16.5167,
                'longitude' => 120.8333,
                'elevation' => 2198,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Hapunang Banoi', 'region' => 'CAR'])
            ],
            [
                'osm_id' => 1030,
                'name' => 'Mount Singakalsa',
                'latitude' => 14.2000,
                'longitude' => 121.1500,
                'elevation' => 1686,
                'raw_tags' => json_encode(['natural' => 'peak', 'name' => 'Mount Singakalsa', 'region' => 'CALABARZON'])
            ]
        ];

        foreach ($luzonPeaks as $peakData) {
            Peak::updateOrCreate(
                ['osm_id' => $peakData['osm_id']],
                $peakData
            );
        }

        $this->command->info('✅ Created/updated ' . count($luzonPeaks) . ' Luzon peak records');
        $this->command->info('🏔️  Major peaks included: Mount Pulag, Mount Pinatubo, Mount Batulao, Mount Makiling, Mount Banahaw, etc.');
    }
}

<?php

// database/seeders/ReportSystemSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;
use App\Models\Trail;
use App\Models\User;
use App\Models\Booking;
use App\Models\LoginLog;
use App\Models\Feedback;
use App\Models\SafetyIncident;
use App\Models\EmergencyReadiness;
use App\Models\CommunityPost;
use App\Models\ContentFlag;
use App\Models\ModerationAction;
use Carbon\Carbon;

class ReportSystemSeeder extends Seeder
{
    public function run()
    {


        
        // Create Regions
        $regions = [
            ['name' => 'Northern Mountains', 'description' => 'Cold climate mountain trails'],
            ['name' => 'Southern Valleys', 'description' => 'Temperate valley hiking paths'],
            ['name' => 'Eastern Forests', 'description' => 'Dense forest trail network'],
            ['name' => 'Western Coastline', 'description' => 'Coastal hiking routes']
        ];

        foreach ($regions as $regionData) {
            Region::create($regionData);
        }

        $locations = [
        ['name' => 'Eagle Peak', 'slug' => 'eagle-peak', 'province' => 'Mountain Province', 'region_id' => 1, 'latitude' => 16.5, 'longitude' => 120.8, 'description' => 'Eagle Peak main location.'],
        ['name' => 'Valley Base', 'slug' => 'valley-base', 'province' => 'Valley Province', 'region_id' => 2, 'latitude' => 16.6, 'longitude' => 121.0, 'description' => 'Valley starting point.'],
        ['name' => 'Forest Point', 'slug' => 'forest-point', 'province' => 'Forest Province', 'region_id' => 3, 'latitude' => 16.7, 'longitude' => 121.2, 'description' => 'Forest trail base.'],
        ['name' => 'Coastal View', 'slug' => 'coastal-view', 'province' => 'Coastal Province', 'region_id' => 4, 'latitude' => 16.8, 'longitude' => 121.4, 'description' => 'Coastal trail head.'],
        ];

        foreach ($locations as $locationData) {
            \App\Models\Location::create($locationData);
        }


        // Create Trails
        $trails = [
            [
                'name' => 'Eagle Peak Trail',
                'description' => 'Challenging mountain peak with stunning views',
                'difficulty' => 'challenging',
                'region_id' => 1,
                'location_id' => 1,
                'distance' => 12.5,
                'elevation_gain' => 800
            ],
            [
                'name' => 'Valley Loop',
                'description' => 'Easy family-friendly trail through meadows',
                'difficulty' => 'beginner-friendly',
                'region_id' => 2,
                'location_id' => 2,
                'distance' => 5.2,
                'elevation_gain' => 100
            ],
            [
                'name' => 'Forest Canopy Walk',
                'description' => 'Moderate trail through ancient forest',
                'difficulty' => 'moderate',
                'region_id' => 3,
                'location_id' => 3,
                'distance' => 8.7,
                'elevation_gain' => 350
            ],
            [
                'name' => 'Coastal Cliffs',
                'description' => 'Spectacular ocean views along dramatic cliffs',
                'difficulty' => 'moderate',
                'region_id' => 4,
                'location_id' => 4,
                'distance' => 15.3,
                'elevation_gain' => 450
            ]
        ];

        foreach ($trails as $trailData) {
            Trail::create($trailData);
        }

        // Create sample users if they don't exist
        $this->createSampleUsers();

        // Create Bookings (last 60 days)
        $this->createSampleBookings();

        // Create Login Logs (last 30 days)
        $this->createSampleLoginLogs();

        // Create Feedback
        $this->createSampleFeedback();

        // Create Safety Incidents
        $this->createSampleSafetyIncidents();

        // Create Emergency Readiness records
        $this->createEmergencyReadiness();

        // Create Community Posts
        $this->createSampleCommunityPosts();

        // Create Content Flags
        $this->createSampleContentFlags();

        // Create Moderation Actions
        $this->createSampleModerationActions();
    }

    private function createSampleUsers()
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@hiking.com',
                'password' => bcrypt('password'),
                'user_type' => 'admin',
                'status' => 'active'
            ],
            [
                'name' => 'Guide Smith',
                'email' => 'guide@hiking.com',
                'password' => bcrypt('password'),
                'user_type' => 'guide',
                'status' => 'active'
            ],
            [
                'name' => 'John Hiker',
                'email' => 'hiker1@hiking.com',
                'password' => bcrypt('password'),
                'user_type' => 'hiker',
                'status' => 'active'
            ],
            [
                'name' => 'Jane Explorer',
                'email' => 'hiker2@hiking.com',
                'password' => bcrypt('password'),
                'user_type' => 'hiker',
                'status' => 'active'
            ]
        ];

        foreach ($users as $userData) {
            if (!User::where('email', $userData['email'])->exists()) {
                User::create($userData);
            }
        }
    }

    private function createSampleBookings()
    {
        $users = User::all();
        $trails = Trail::all();

        for ($i = 0; $i < 100; $i++) {
            Booking::create([
                'user_id' => $users->random()->id,
                'trail_id' => $trails->random()->id,
                'booking_date' => Carbon::now()->subDays(rand(0, 60)),
                'status' => collect(['confirmed', 'pending', 'cancelled', 'completed'])->random(),
                'group_size' => rand(1, 8),
                'notes' => 'Sample booking for testing'
            ]);
        }
    }

    private function createSampleLoginLogs()
    {
        $users = User::all();

        for ($i = 0; $i < 200; $i++) {
            LoginLog::create([
                'user_id' => $users->random()->id,
                'ip_address' => '192.168.1.' . rand(1, 255),
                'user_agent' => 'Mozilla/5.0 (Test Browser)',
                'login_successful' => rand(0, 10) > 1, // 90% success rate
                'created_at' => Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23))
            ]);
        }
    }

    private function createSampleFeedback()
    {
        $users = User::all();
        $trails = Trail::all();

        for ($i = 0; $i < 50; $i++) {
            Feedback::create([
                'user_id' => $users->random()->id,
                'trail_id' => $trails->random()->id,
                'rating' => rand(1, 5),
                'comment' => collect([
                    'Great trail with amazing views!',
                    'Well maintained path, perfect for families.',
                    'Challenging but rewarding hike.',
                    'Beautiful scenery throughout the journey.',
                    'Could use better trail markers.',
                    'Excellent trail conditions and facilities.'
                ])->random(),
                'experience_date' => Carbon::now()->subDays(rand(1, 90))
            ]);
        }
    }

    private function createSampleSafetyIncidents()
    {
        $trails = Trail::all();
        $users = User::all();

        for ($i = 0; $i < 15; $i++) {
            SafetyIncident::create([
                'trail_id' => $trails->random()->id,
                'reported_by' => $users->random()->id,
                'incident_type' => collect(['injury', 'weather', 'equipment', 'wildlife', 'other'])->random(),
                'severity' => collect(['low', 'medium', 'high', 'critical'])->random(),
                'description' => 'Sample safety incident description for testing purposes.',
                'incident_date' => Carbon::now()->subDays(rand(1, 180)),
                'resolution_status' => collect(['pending', 'investigating', 'resolved', 'closed'])->random()
            ]);
        }
    }

    private function createEmergencyReadiness()
    {
        $trails = Trail::all();

        foreach ($trails as $trail) {
            EmergencyReadiness::create([
                'trail_id' => $trail->id,
                'equipment_status' => collect(['excellent', 'good', 'fair', 'poor'])->random(),
                'staff_availability' => collect(['excellent', 'good', 'fair', 'poor'])->random(),
                'communication_status' => collect(['excellent', 'good', 'fair', 'poor'])->random(),
                'last_inspection_date' => Carbon::now()->subDays(rand(1, 30))
            ]);
        }
    }

    private function createSampleCommunityPosts()
    {
        $users = User::all();

        for ($i = 0; $i < 30; $i++) {
            CommunityPost::create([
                'user_id' => $users->random()->id,
                'title' => collect([
                    'Amazing sunrise at Eagle Peak!',
                    'Trail conditions update for Valley Loop',
                    'Wildlife spotted on Forest Canopy Walk',
                    'Tips for hiking Coastal Cliffs safely',
                    'Best photography spots on the trails'
                ])->random(),
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. #hiking #nature #adventure',
                'status' => collect(['active', 'hidden'])->random(),
                'likes_count' => rand(0, 50),
                'comments_count' => rand(0, 20),
                'created_at' => Carbon::now()->subDays(rand(1, 60))
            ]);
        }
    }

    private function createSampleContentFlags()
    {
        $posts = CommunityPost::all();
        $users = User::all();

        for ($i = 0; $i < 10; $i++) {
            ContentFlag::create([
                'post_id' => $posts->random()->id,
                'user_id' => $users->random()->id,
                'reason' => collect(['spam', 'inappropriate', 'harassment', 'false_info', 'other'])->random(),
                'description' => 'Sample flag description for testing',
                'status' => collect(['pending', 'reviewed', 'dismissed', 'acted_upon'])->random()
            ]);
        }
    }

    private function createSampleModerationActions()
    {
        $users = User::where('user_type', 'hiker')->get();
        $moderators = User::whereIn('user_type', ['admin', 'moderator'])->get();

        for ($i = 0; $i < 8; $i++) {
            $moderator = $moderators->random();
            ModerationAction::create([
                'user_id' => $users->random()->id,
                'moderator_id' => $moderator->id,
                'action_type' => collect(['warning', 'suspension', 'restriction'])->random(),
                'reason' => 'Sample moderation action for testing purposes',
                'duration' => rand(1, 30),
                'expires_at' => Carbon::now()->addDays(rand(1, 30))
            ]);
        }
    }
}





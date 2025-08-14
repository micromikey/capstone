<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\OrganizationProfile;

class ProfileSeeder extends Seeder
{
    public function run(): void
    {
        // Update existing users with sample profile data
        $users = User::all();
        
        foreach ($users as $user) {
            if ($user->user_type === 'hiker') {
                $this->seedHikerProfile($user);
            } elseif ($user->user_type === 'organization') {
                $this->seedOrganizationProfile($user);
            }
        }
    }

    private function seedHikerProfile(User $user): void
    {
        $user->update([
            'phone' => '+1 (555) ' . rand(100, 999) . '-' . rand(1000, 9999),
            'bio' => 'Passionate hiker who loves exploring new trails and connecting with nature. Always looking for the next adventure!',
            'location' => ['New York, NY', 'Los Angeles, CA', 'Chicago, IL', 'Houston, TX', 'Phoenix, AZ'][array_rand(['New York, NY', 'Los Angeles, CA', 'Chicago, IL', 'Houston, TX', 'Phoenix, AZ'])],
            'birth_date' => now()->subYears(rand(20, 60)),
            'gender' => ['male', 'female', 'other'][array_rand(['male', 'female', 'other'])],
            'hiking_preferences' => ['Day Hiking', 'Backpacking', 'Photography'],
            'emergency_contact_name' => 'Emergency Contact',
            'emergency_contact_phone' => '+1 (555) ' . rand(100, 999) . '-' . rand(1000, 9999),
            'emergency_contact_relationship' => 'Family Member',
        ]);
    }

    private function seedOrganizationProfile(User $user): void
    {
        // Create or update organization profile
        $orgProfile = $user->organizationProfile;
        
        if (!$orgProfile) {
            $orgProfile = new OrganizationProfile();
            $orgProfile->user_id = $user->id;
        }
        
        $orgProfile->update([
            'phone' => '+1 (555) ' . rand(100, 999) . '-' . rand(1000, 9999),
            'website' => 'https://www.example-org.com',
            'mission_statement' => 'To provide safe and enjoyable hiking experiences while promoting environmental conservation and outdoor education.',
            'services_offered' => 'Guided hiking tours, trail maintenance, outdoor education programs, and safety training.',
            'operating_hours' => 'Monday - Friday: 8:00 AM - 6:00 PM, Weekends: 9:00 AM - 5:00 PM',
            'contact_person' => 'John Smith',
            'contact_position' => 'Operations Manager',
            'specializations' => ['Mountain Hiking', 'Family Tours', 'Educational Programs'],
            'founded_year' => '2018',

        ]);
    }
}

# Enhanced Profile Features for HikeThere

## Overview
This document outlines the enhanced profile functionality that has been added to both hiker and organization profiles in the HikeThere application.

## New Features

### 1. Profile Pictures
- **Hikers**: Can upload personal profile pictures
- **Organizations**: Can upload organization logos
- **Default Avatars**: Fallback images for users without profile pictures
- **File Support**: JPG, PNG, GIF up to 2MB
- **Storage**: Images stored in `storage/app/public/profile-pictures/`

### 2. Enhanced Hiker Profiles

#### Personal Information
- Full name
- Email address
- Phone number
- Bio/About section
- Location (City, State/Province)
- Birth date (with age calculation)
- Gender (Male, Female, Other, Prefer not to say)

#### Hiking Preferences
- Day Hiking
- Backpacking
- Trail Running
- Mountain Biking
- Rock Climbing
- Camping
- Photography
- Wildlife Watching
- Solo Hiking
- Group Hiking
- Family Hiking
- Adventure Racing

#### Emergency Contact Information
- Emergency contact name
- Emergency contact phone
- Emergency contact relationship

### 3. Enhanced Organization Profiles

#### Basic Information
- Organization name
- Email address
- Phone number
- Website URL
- Organization description

#### Mission & Values
- Mission statement
- Services offered
- Operating hours
- Founded year
- Team size

#### Contact Information
- Primary contact person
- Contact position
- Specializations (array)

### 4. Profile Completion Tracking
- **Hikers**: Tracks completion of name, email, phone, bio, location, and profile picture
- **Organizations**: Tracks completion of organization name, description, email, phone, profile picture, website, and mission statement
- Visual progress bar showing completion percentage
- Encourages users to complete their profiles

### 5. Modern UI/UX Design
- **Cover Images**: Beautiful gradient backgrounds for profile headers
- **Responsive Design**: Works on all device sizes
- **Card-based Layout**: Organized information in clean, modern cards
- **Interactive Elements**: Hover effects, transitions, and smooth animations
- **Icon Integration**: Meaningful icons for different information types
- **Color Scheme**: Consistent with HikeThere brand colors

## Technical Implementation

### Database Changes
- New migration: `2025_01_15_000000_add_profile_enhancements.php`
- Added fields to `users` table
- Added fields to `organization_profiles` table

### New Models & Controllers
- **ProfileController**: Handles profile CRUD operations
- Enhanced **User** model with new attributes and methods
- Enhanced **OrganizationProfile** model with new attributes and methods

### New Routes
```php
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/picture', [ProfileController::class, 'deleteProfilePicture'])->name('profile.picture.delete');
});
```

### New Views
- `resources/views/profile/hiker-show.blade.php` - Enhanced hiker profile display
- `resources/views/profile/organization-show.blade.php` - Enhanced organization profile display
- `resources/views/profile/hiker-edit.blade.php` - Hiker profile edit form
- `resources/views/profile/organization-edit.blade.php` - Organization profile edit form

### Helper Methods
- `getProfilePictureUrlAttribute()` - Returns profile picture URL or default avatar
- `getAgeAttribute()` - Calculates user age from birth date
- `hasCompletedProfile()` - Checks if profile is complete
- `getProfileCompletionPercentageAttribute()` - Returns profile completion percentage

## Usage

### For Hikers
1. Navigate to Profile from navigation menu
2. Click "Edit Profile" to modify information
3. Upload profile picture
4. Fill in personal details, hiking preferences, and emergency contacts
5. Save changes

### For Organizations
1. Navigate to Profile from navigation menu
2. Click "Edit Profile" to modify information
3. Upload organization logo
4. Fill in organization details, mission statement, and contact information
5. Save changes

## File Structure
```
resources/views/profile/
├── hiker-show.blade.php          # Hiker profile display
├── hiker-edit.blade.php          # Hiker profile edit form
├── organization-show.blade.php   # Organization profile display
└── organization-edit.blade.php   # Organization profile edit form

app/Http/Controllers/
└── ProfileController.php         # Profile management controller

app/Models/
├── User.php                      # Enhanced user model
└── OrganizationProfile.php       # Enhanced organization profile model

database/migrations/
└── 2025_01_15_000000_add_profile_enhancements.php

public/img/
├── default-hiker-avatar.png      # Default hiker avatar
└── default-org-avatar.png        # Default organization avatar
```

## Benefits

### For Users
- **Personalization**: Custom profile pictures and detailed information
- **Safety**: Emergency contact information for hiking activities
- **Community**: Share hiking preferences and experiences
- **Professionalism**: Complete profiles build trust and credibility

### For Organizations
- **Branding**: Organization logos and professional appearance
- **Trust**: Detailed information builds credibility with hikers
- **Services**: Clear communication of offerings and specializations
- **Contact**: Easy access to contact information and operating hours

### For Platform
- **Engagement**: Users spend more time on detailed profiles
- **Data Quality**: Rich user data improves platform functionality
- **User Experience**: Modern, intuitive interface enhances satisfaction
- **Scalability**: Well-structured code for future enhancements

## Future Enhancements
- Profile verification badges
- Social media integration
- Profile analytics and insights
- Advanced privacy settings
- Profile sharing and networking features
- Integration with hiking achievements and badges

## Installation & Setup

### 1. Run Migration
```bash
php artisan migrate
```

### 2. Create Storage Link (if not exists)
```bash
php artisan storage:link
```

### 3. Seed Sample Data (optional)
```bash
php artisan db:seed --class=ProfileSeeder
```

### 4. Add Default Avatar Images
Replace the placeholder files in `public/img/` with actual PNG images:
- `default-hiker-avatar.png` (200x200px recommended)
- `default-org-avatar.png` (200x200px recommended)

## Notes
- Profile pictures are stored in the public storage disk
- Old profile pictures are automatically deleted when new ones are uploaded
- All new fields are optional and don't break existing functionality
- The system gracefully handles missing profile data
- Profile completion tracking encourages user engagement

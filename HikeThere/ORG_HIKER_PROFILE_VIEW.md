# Organization View: Hiker Profile Feature

## Overview
This feature allows organizations to view detailed profiles of hikers who have **booked and confirmed payment** for their trail events. This ensures organizations have access to important safety information for hikers participating in their activities.

## Access Control
- **Only organizations** can access hiker profiles
- **Only hikers with confirmed bookings** (payment status = "paid" and booking status = "confirmed" or "completed") can have their profiles viewed
- Organizations can only view profiles of hikers who booked their trails

## Route
```php
Route: /org/community/hiker/{hikerId}
Name: org.community.hiker-profile
Method: GET
Middleware: auth:sanctum, check.approval, user.type:organization
Controller: App\Http\Controllers\Organization\HikerProfileController@show
```

## What Information is Displayed

### 1. **Hiker Basic Information**
- Profile picture (or initial avatar)
- Full name
- Email address
- Phone number (if provided)
- Physical address (if provided)
- Verified status badge
- Member since date

### 2. **Pre-Hike Self-Assessment Results** (if completed)
- **Overall Readiness Score** - Total percentage score
- **Category Scores** with visual progress bars:
  - Health Score
  - Fitness Score
  - Experience Score
  - Weather Score
  - Emergency Score
  - Environment Score
- Assessment completion date
- Readiness level indicator (Excellent/Good/Needs Improvement)

### 3. **Emergency Contact Information** (if provided in assessment)
- Emergency contact name
- Relationship to hiker
- Primary phone number
- Alternative phone number (if provided)
- **Critical for safety**: Displayed in a prominent red-bordered section

### 4. **Hiking Itinerary** (if submitted)
- Start date and time
- Expected return date and time
- Trip duration
- Group size
- Group member names
- Planned route description
- Equipment and gear list
- Special notes

### 5. **Booking Information**
- Trail name
- Booking date
- Hiking date
- Number of hikers in booking
- Booking status (confirmed/completed)
- Payment status (paid)

## UI Features

### Design Elements
- **Gradient header** with animated particles
- **Color-coded sections**:
  - Emerald/Teal for general info
  - Red for emergency contacts
  - Blue for itinerary
  - Green for assessment scores
- **Responsive layout** - Works on mobile, tablet, and desktop
- **Visual progress bars** for assessment scores
- **Empty state messages** when data is not available

### Status Badges
- **Verified Badge** - Shows if hiker's email is verified
- **Confirmed Booking Badge** - Highlights payment verification
- **Score Indicators** - Color-coded based on assessment performance

## Use Cases

### For Safety & Emergency Planning
1. Organizations can review emergency contact information before the hike
2. Assessment scores help organizations understand hiker preparedness
3. Itinerary details help with route planning and group coordination

### For Trip Management
1. View who is coming and their contact details
2. Check hiker experience levels and physical fitness
3. Identify hikers who may need additional support or guidance

### For Communication
1. Access hiker's contact information for trip updates
2. Reach emergency contacts if needed during the hike
3. Follow up after the trip

## Security Considerations

### Privacy Protection
- **Booking verification required** - Organizations cannot view random hiker profiles
- **Payment confirmation required** - Only paid bookings grant access
- **Organization-specific access** - Can only view hikers who booked their own trails
- **403 Forbidden error** if access requirements are not met

### Error Messages
```php
abort(403, 'Unauthorized access. This hiker has not booked any of your trails or payment is not confirmed.');
```

## Empty States

### No Assessment Data
Shows a friendly message: "This hiker hasn't completed a pre-hike self-assessment yet."

### No Itinerary
Shows a friendly message: "This hiker hasn't submitted an itinerary for this booking yet."

### No Emergency Contact
Section is hidden if emergency contact information is not available

## Controller Logic

```php
// Verify hiker exists and is of type 'hiker'
$hiker = User::where('id', $hikerId)
    ->where('user_type', 'hiker')
    ->firstOrFail();

// Verify confirmed booking exists
$booking = Booking::where('user_id', $hiker->id)
    ->whereHas('trail', function($query) use ($organization) {
        $query->where('organization_id', $organization->id);
    })
    ->where('payment_status', 'paid')
    ->whereIn('status', ['confirmed', 'completed'])
    ->latest()
    ->first();

// Deny access if no valid booking
if (!$booking) {
    abort(403);
}
```

## Integration with Booking System

### How to Link to Hiker Profile
From your bookings list or booking details page:

```blade
<a href="{{ route('org.community.hiker-profile', $booking->user_id) }}"
   class="text-emerald-600 hover:text-emerald-800 font-semibold">
    View Hiker Profile
</a>
```

### Example in Booking Details View
```blade
<div class="flex items-center gap-4">
    <img src="{{ $booking->user->profile_picture_url }}" class="w-10 h-10 rounded-full">
    <div>
        <p class="font-semibold">{{ $booking->user->name }}</p>
        <a href="{{ route('org.community.hiker-profile', $booking->user_id) }}"
           class="text-sm text-emerald-600 hover:underline">
            View Full Profile
        </a>
    </div>
</div>
```

## Future Enhancements

### Potential Features
1. **PDF Export** - Download hiker information for offline reference
2. **Group View** - View all hikers in a booking together
3. **Notes System** - Organizations can add private notes about hiker interactions
4. **Communication Log** - Track messages sent to hikers
5. **Safety Checklist** - Organizations can mark safety requirements as verified

## Testing Checklist

- [ ] Organization can view hiker profile with confirmed booking
- [ ] Organization cannot view hiker without confirmed booking
- [ ] Organization cannot view hiker from another organization's trail
- [ ] Assessment scores display correctly with proper percentages
- [ ] Emergency contact information displays when available
- [ ] Itinerary information displays when available
- [ ] Empty states show when data is not available
- [ ] Responsive design works on mobile devices
- [ ] 403 error shows for unauthorized access attempts

## Related Files

- **View**: `resources/views/org/community/hiker-profile.blade.php`
- **Controller**: `app/Http/Controllers/Organization/HikerProfileController.php`
- **Route**: `routes/web.php` (line ~315)
- **Models Used**: User, Booking, AssessmentResult, Itinerary

## Dependencies

- User model with relationships: `latestAssessmentResult`, `latestItinerary`
- Booking model with relationship: `trail`, `user`
- Middleware: `auth:sanctum`, `check.approval`, `user.type:organization`

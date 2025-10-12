# Hiker Profile Access Guide

## How to View Hiker Profile from Organization Side

### Quick Answer
**Navigate to: Manage Bookings → Click "View Profile" next to any hiker's name**

---

## Step-by-Step Instructions

### 1. Go to Bookings Page
- Log in as an **Organization** user
- Click on **"Manage Bookings"** in your navigation menu
- Route: `/org/bookings`

### 2. Find the Hiker
- In the bookings table, you'll see a list of all bookings
- Each row shows:
  - Booking ID
  - **Hiker name and email** (redacted for privacy)
  - Trail name
  - Date
  - Party size
  - Amount
  - Payment status
  - Booking status

### 3. Click "View Profile"
- Next to each hiker's name, you'll now see a **"View Profile"** link
- Click on it to view the full hiker profile

---

## What You'll See on the Hiker Profile Page

The hiker profile page (`hiker-profile.blade.php`) displays:

### 📋 Personal Information
- Full name (unredacted)
- Email address
- Phone number
- Home address
- Profile picture
- Verification status
- Member since date

### 📊 Pre-Hike Assessment Results
- Overall readiness score
- Category breakdowns:
  - Health Score
  - Fitness Score
  - Experience Score
  - Weather Score
  - Emergency Score
  - Environment Score

### 🚨 Emergency Contact Information
- Contact name
- Relationship
- Primary phone number
- Alternative phone number (if provided)

### 🗺️ Hiking Itinerary
- Start date and time
- Expected return date and time
- Duration
- Group size and members
- Planned route description
- Equipment list
- Special notes

### 📅 Booking Details
- Trail name
- Booking date
- Hiking date
- Number of hikers
- Booking status
- Payment status
- Group participants list

---

## Security & Access Control

### Who Can Access?
✅ **Organizations can view profiles ONLY if:**
- The hiker has a **confirmed** or **completed** booking
- The payment status is **"paid"**
- The booking is for one of **your trails**

❌ **Access is denied if:**
- Payment is not verified/paid
- Booking is cancelled or pending
- The hiker hasn't booked any of your trails

---

## Recent Changes Made

### 1. Updated Bookings Index (`org/bookings/index.blade.php`)
- Added **"View Profile"** link next to each hiker's name
- Link appears in the "Hiker" column of the bookings table
- Styled with emerald green color to match theme

### 2. Enhanced Controller (`HikerProfileController.php`)
- Now accepts optional `booking` parameter
- Shows itinerary specific to the selected booking
- Improved security checks

---

## Technical Details

### Route
```php
Route::get('/org/community/hiker/{hiker}', [HikerProfileController::class, 'show'])
    ->name('org.community.hiker-profile');
```

### URL Format
```
/org/community/hiker/{hiker_id}?booking={booking_id}
```

### Example
```
/org/community/hiker/42?booking=123
```

---

## Design Features

### Visual Elements
- **Gradient header** with emerald-to-teal colors
- **Verified badge** for email-verified users
- **Card-based layout** with shadows and rounded corners
- **Progress bars** for assessment scores
- **Color-coded badges** for status indicators
- **Responsive design** for mobile and desktop

### Color Scheme
- Primary: Emerald/Teal gradient
- Success: Green
- Warning: Yellow/Orange
- Error: Red
- Info: Blue

---

## Privacy & Data Protection

### Redacted Information (on Bookings List)
- Hiker names: `J*** T***` (first letter + asterisks)
- Email addresses: `j****@g***.com`

### Full Information (on Profile Page)
- Complete name and contact details
- Only visible after payment confirmation
- Access controlled by booking status

---

## Tips for Organizations

1. **Verify payments first** before viewing detailed profiles
2. **Check assessment scores** to ensure hiker readiness
3. **Review emergency contacts** for safety purposes
4. **Confirm itinerary details** match the booking
5. **Use the information** to prepare for the hike properly

---

## Troubleshooting

### "Unauthorized access" error?
- Check if the booking payment is marked as "paid"
- Verify the booking status is "confirmed" or "completed"
- Ensure the booking is for your organization's trail

### Can't see "View Profile" link?
- Clear your browser cache
- Refresh the page
- Check that you're on the Manage Bookings page

### Profile shows wrong data?
- The profile shows the latest assessment and itinerary
- If multiple bookings exist, it prioritizes the specific booking

---

## Contact & Support

If you encounter any issues or have questions about accessing hiker profiles, please contact your system administrator.

---

**Last Updated:** October 12, 2025

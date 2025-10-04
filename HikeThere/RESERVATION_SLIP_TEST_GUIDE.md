# Quick Test Guide - Reservation Slip Download

## How to Test the Feature

### Prerequisites
1. You need a confirmed booking with verified payment
2. You must be logged in as the hiker who made the booking

### Testing Steps

1. **Navigate to Bookings**
   - Go to: `/hiker/booking` or click "My Bookings" from the navigation

2. **Find a Confirmed Booking**
   - Look for a booking card with:
     - Status badge: "Confirmed" (green)
     - Payment badge: "Payment Verified" (green)

3. **Click the Download Button**
   - Click the blue "Download" button (right side of the action buttons)
   - The button has a download icon that bounces on hover

4. **Expected Result**
   - A PDF file should automatically download to your Downloads folder
   - Filename format: `Reservation-Slip-{BOOKING_ID}-{YYYYMMDD}.pdf`
   - Example: `Reservation-Slip-42-20251003.pdf`

### What to Verify in the PDF

✅ **Header Section**
- HikeThere branding
- "RESERVATION SLIP" title
- Booking number badge

✅ **Booking Status Section**
- Booking status (CONFIRMED)
- Payment status (VERIFIED)
- Booking date
- Payment verification date

✅ **Hiker Information**
- Full name
- Email address
- Phone (if available)

✅ **Trail Details**
- Trail name
- Hiking date
- Party size
- Notes (if any)

✅ **Payment Information**
- Payment method
- Transaction number
- Amount paid (₱ format)

✅ **Important Reminders**
- Yellow alert box with hiking tips

✅ **Footer**
- Generation timestamp
- Official notice

### Error Cases to Test

#### 1. Unauthorized Access
- Try accessing: `/hiker/booking/{OTHER_USER_BOOKING_ID}/download-slip`
- **Expected**: Authorization error / redirect

#### 2. Pending Booking
- Try downloading a slip for a pending booking
- **Expected**: Error message "Reservation slip is only available for confirmed bookings with verified payment."

#### 3. Unverified Payment
- Try downloading a slip for a booking with pending payment
- **Expected**: Error message about verification requirement

### Browser Compatibility
Test on:
- [ ] Chrome
- [ ] Firefox
- [ ] Edge
- [ ] Safari (if available)

### Mobile Testing
- [ ] Test button visibility on mobile screens
- [ ] Verify PDF downloads on mobile browsers
- [ ] Check PDF readability on mobile devices

## Troubleshooting

### PDF Not Generating?
1. Check if DomPDF is installed:
   ```bash
   composer show barryvdh/laravel-dompdf
   ```

2. Clear cache:
   ```bash
   php artisan config:clear
   php artisan view:clear
   ```

### Download Not Starting?
1. Check browser popup blocker
2. Verify browser download settings
3. Check browser console for errors

### PDF Looks Wrong?
1. Verify the blade template exists at: `resources/views/hiker/booking/slip-pdf.blade.php`
2. Check for any PHP errors in the template
3. Try clearing the view cache

## Quick Links

- Route: `/hiker/booking/{booking}/download-slip`
- Controller: `App\Http\Controllers\Hiker\BookingController@downloadSlip`
- View: `resources/views/hiker/booking/slip-pdf.blade.php`
- Index View: `resources/views/hiker/booking/index.blade.php`

## Success Criteria

✅ Download button appears only for confirmed bookings with verified payment
✅ PDF downloads automatically when clicked
✅ PDF contains all required booking and payment information
✅ PDF is professionally formatted and readable
✅ Unauthorized users cannot download other users' slips
✅ Pending/unverified bookings show appropriate error messages

# 🔗 Booking & Payment Integration - Complete Guide

## Overview
Your **Booking** and **Payment** systems are now fully integrated! Users can book trails and seamlessly proceed to payment, with automatic status tracking and linking.

---

## 🎯 Integrated Flow

```
┌─────────────────┐
│  User Books     │
│  Trail/Event    │
│  (BookingForm)  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ BookingController│
│    store()      │ Creates Booking
│                 │ Status: 'confirmed'
└────────┬────────┘
         │
         ▼ (redirects to)
┌─────────────────┐
│  Payment Form   │
│  (Pre-filled)   │ booking_id passed
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│PaymentController│
│ processPayment()│ Creates/Updates BookingPayment
│                 │ Links to Booking via booking_id
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   PayMongo      │
│  Checkout Page  │ User completes payment
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Webhook       │
│   Triggered     │ Payment status: paid
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Update Database │
│ • BookingPayment│ → payment_status: 'paid'
│ • Booking       │ → status: 'confirmed'
└─────────────────┘
         │
         ▼
┌─────────────────┐
│  Success Page   │
│  Shows Details  │
└─────────────────┘
```

---

## 📊 Database Relationships

### Tables & Foreign Keys

```sql
bookings
├── id (PRIMARY KEY)
├── user_id (FK → users)
├── trail_id (FK → trails)
├── batch_id (FK → batches)
├── event_id (FK → events)
├── date
├── party_size
├── status ('confirmed', 'cancelled', etc.)
├── notes
├── price_cents
├── created_at
└── updated_at

booking_payments
├── id (PRIMARY KEY)
├── booking_id (FK → bookings) ← NEW!
├── user_id (FK → users) ← NEW!
├── fullname
├── email
├── phone
├── mountain
├── amount
├── hike_date
├── participants
├── paymongo_link_id
├── paymongo_payment_id
├── payment_status (ENUM: pending/paid/failed/refunded)
├── paid_at
├── created_at
└── updated_at
```

### Model Relationships

**Booking.php**
```php
// One booking can have one payment
public function payment()
{
    return $this->hasOne(BookingPayment::class);
}

// Helper methods
public function isPaid(): bool
public function hasPaymentPending(): bool
public function getAmountInPesos(): int
```

**BookingPayment.php**
```php
// Payment belongs to one booking
public function booking()
{
    return $this->belongsTo(Booking::class);
}

// Payment belongs to one user
public function user()
{
    return $this->belongsTo(User::class);
}

// Helper methods
public function isPaid(): bool
public function isPending(): bool
public function markAsPaid(string $paymentId = null): void
public function markAsFailed(): void
```

---

## 🔄 Workflow Details

### 1. User Creates Booking

**Controller**: `BookingController@store`

```php
// After validation and batch availability check
$booking = Booking::create([
    'user_id' => Auth::id(),
    'trail_id' => $validated['trail_id'],
    'batch_id' => $batch->id,
    'date' => $validated['date'],
    'party_size' => $validated['party_size'],
    'status' => 'confirmed',
    'price_cents' => $trail->price * 100,
]);

// Redirect to payment with booking_id
return redirect()->route('payment.create', [
    'booking_id' => $booking->id,
]);
```

### 2. Payment Form Pre-filled

**Controller**: `PaymentController@create`

```php
public function create(Request $request)
{
    $booking = null;
    
    if ($request->has('booking_id')) {
        $booking = Booking::with(['trail', 'user', 'batch'])
            ->findOrFail($request->booking_id);
        
        // Verify ownership
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Check if already paid
        if ($booking->payment && $booking->payment->isPaid()) {
            return redirect()->route('booking.show', $booking)
                ->with('info', 'Already paid');
        }
    }
    
    return view('payment.pay', compact('booking'));
}
```

**View**: Pre-fills form fields from booking
- Fullname → `$booking->user->name`
- Email → `$booking->user->email`
- Mountain → `$booking->trail->trail_name`
- Date → `$booking->date`
- Participants → `$booking->party_size`
- Amount → `$booking->getAmountInPesos()`

### 3. Payment Processing

**Controller**: `PaymentController@processPayment`

```php
// Validates booking_id
'booking_id' => 'nullable|exists:bookings,id',

// If booking exists, link payment to it
if ($booking && $booking->payment) {
    // Update existing payment
    $payment = $booking->payment;
    $payment->update($paymentData);
} else {
    // Create new payment
    $payment = BookingPayment::create($paymentData);
}

// PayMongo reference includes payment ID
'reference_number' => 'PAYMENT-' . $payment->id,
```

### 4. Webhook Updates Status

**Controller**: `PaymentController@webhook`

```php
if ($eventType === 'link.payment.paid') {
    $paymentId = (int) str_replace('PAYMENT-', '', $referenceNumber);
    $payment = BookingPayment::find($paymentId);
    
    if ($payment && $payment->isPending()) {
        // Mark payment as paid
        $payment->markAsPaid($paymentData['id']);
        
        // Update linked booking status
        if ($payment->booking) {
            $payment->booking->update(['status' => 'confirmed']);
        }
    }
}
```

---

## 🔍 Query Examples

### Check if booking is paid

```php
$booking = Booking::find(1);

if ($booking->isPaid()) {
    echo "Paid!";
}

if ($booking->hasPaymentPending()) {
    echo "Payment pending";
}
```

### Get payment for a booking

```php
$booking = Booking::with('payment')->find(1);
$payment = $booking->payment;

echo $payment->payment_status; // 'paid', 'pending', etc.
echo $payment->paymongo_payment_id;
```

### Get booking for a payment

```php
$payment = BookingPayment::with('booking.trail')->find(1);
$booking = $payment->booking;

echo $booking->trail->trail_name;
echo $booking->party_size;
```

### Get all paid bookings for user

```php
$paidBookings = Booking::where('user_id', Auth::id())
    ->whereHas('payment', function($q) {
        $q->where('payment_status', 'paid');
    })
    ->with(['trail', 'payment'])
    ->get();
```

### Get all pending payments

```php
$pendingPayments = BookingPayment::where('payment_status', 'pending')
    ->where('created_at', '>', now()->subHours(24)) // Last 24h
    ->with(['booking.trail', 'user'])
    ->get();
```

---

## 🎨 UI Integration

### Booking Index Page

Show payment status for each booking:

```blade
@foreach($bookings as $booking)
    <div class="booking-card">
        <h3>{{ $booking->trail->trail_name }}</h3>
        <p>Party Size: {{ $booking->party_size }}</p>
        
        @if($booking->isPaid())
            <span class="badge badge-success">✓ Paid</span>
        @elseif($booking->hasPaymentPending())
            <span class="badge badge-warning">⏳ Payment Pending</span>
            <a href="{{ route('payment.create', ['booking_id' => $booking->id]) }}">
                Complete Payment
            </a>
        @else
            <a href="{{ route('payment.create', ['booking_id' => $booking->id]) }}">
                Pay Now
            </a>
        @endif
    </div>
@endforeach
```

### Booking Show Page

```blade
<h2>Booking #{{ $booking->id }}</h2>

<!-- Payment Status -->
@if($booking->payment)
    <div class="payment-info">
        <h3>Payment Information</h3>
        <p>Status: {{ ucfirst($booking->payment->payment_status) }}</p>
        <p>Amount: ₱{{ number_format($booking->payment->amount, 2) }}</p>
        
        @if($booking->payment->isPaid())
            <p>Paid on: {{ $booking->payment->paid_at->format('M d, Y') }}</p>
        @else
            <a href="{{ route('payment.create', ['booking_id' => $booking->id]) }}">
                Complete Payment
            </a>
        @endif
    </div>
@else
    <a href="{{ route('payment.create', ['booking_id' => $booking->id]) }}">
        Pay for this Booking
    </a>
@endif
```

---

## 🧪 Testing the Integration

### 1. Create Test Booking

```bash
php artisan tinker
```

```php
$user = User::first();
$trail = Trail::first();
$batch = Batch::first();

$booking = Booking::create([
    'user_id' => $user->id,
    'trail_id' => $trail->id,
    'batch_id' => $batch->id,
    'date' => now()->addDays(7),
    'party_size' => 2,
    'status' => 'confirmed',
    'price_cents' => 100000, // ₱1000
]);

echo "Booking ID: " . $booking->id;
```

### 2. Visit Payment Page

```
http://localhost:8000/payment/create?booking_id=1
```

**Expected**: Form pre-filled with booking data

### 3. Complete Payment

- Click "Proceed to Payment"
- Redirected to PayMongo
- Use test card: `4120 0000 0000 0007`
- Complete payment

### 4. Verify Database

```php
$booking = Booking::with('payment')->find(1);

echo "Booking Status: " . $booking->status; // 'confirmed'
echo "Payment Status: " . $booking->payment->payment_status; // 'paid'
echo "Paid At: " . $booking->payment->paid_at; // timestamp
```

---

## 📱 API Endpoints (Optional Enhancement)

For mobile apps or AJAX calls:

```php
// routes/api.php

// Get booking with payment status
Route::get('/bookings/{booking}', function(Booking $booking) {
    return $booking->load(['trail', 'payment']);
});

// Check payment status
Route::get('/payments/{payment}', function(BookingPayment $payment) {
    return $payment->load('booking');
});
```

---

## 🚨 Important Notes

### Payment Status Flow

```
Booking Created → status: 'confirmed' (provisional)
         ↓
Payment Created → payment_status: 'pending'
         ↓
User Pays → payment_status: 'paid'
         ↓
Webhook → booking status: 'confirmed' (final)
```

### Handling Edge Cases

**1. User abandons payment**
- Booking exists with status 'confirmed'
- Payment exists with status 'pending'
- **Solution**: Show "Complete Payment" button on booking page

**2. Payment fails**
- Payment status: 'failed'
- **Solution**: Allow retry by redirecting to payment form with same booking_id

**3. User tries to pay twice**
- Check `$booking->payment->isPaid()` before allowing payment
- Redirect to booking page if already paid

**4. Orphaned bookings (no payment)**
- Booking exists but no payment record
- **Solution**: Create payment link from booking page

---

## 🔮 Future Enhancements

### 1. Email Notifications

```php
// After payment is marked as paid
Mail::to($payment->user)->send(new PaymentConfirmation($payment));
```

### 2. Booking Expiration

```php
// Cancel bookings with pending payments after 24 hours
$expiredBookings = Booking::whereHas('payment', function($q) {
    $q->where('payment_status', 'pending')
      ->where('created_at', '<', now()->subHours(24));
})->get();

foreach ($expiredBookings as $booking) {
    $booking->update(['status' => 'expired']);
}
```

### 3. Refund System

```php
public function refund(BookingPayment $payment)
{
    // Call PayMongo refund API
    // Update payment status to 'refunded'
    // Update booking status to 'cancelled'
}
```

### 4. Payment History Page

```blade
<!-- resources/views/user/payment-history.blade.php -->
@foreach($payments as $payment)
    <div class="payment-item">
        <p>Payment #{{ $payment->id }}</p>
        <p>Booking #{{ $payment->booking_id }}</p>
        <p>Amount: ₱{{ number_format($payment->amount, 2) }}</p>
        <p>Status: {{ $payment->payment_status }}</p>
    </div>
@endforeach
```

---

## ✅ Integration Checklist

- [x] Migration: Added `booking_id` and `user_id` to `booking_payments`
- [x] Models: Added relationships between Booking and BookingPayment
- [x] BookingController: Redirects to payment after booking creation
- [x] PaymentController: Accepts and validates `booking_id`
- [x] PaymentController: Links payment to booking
- [x] Webhook: Updates both payment and booking status
- [x] Views: Payment form pre-fills from booking data
- [x] Views: Success page shows booking information
- [ ] TODO: Add email notifications
- [ ] TODO: Create payment history page for users
- [ ] TODO: Add admin dashboard for payment management
- [ ] TODO: Implement refund functionality

---

## 🎉 You're All Set!

Your booking and payment systems are now fully integrated! Users can:

1. Book a trail → Automatically redirected to payment
2. Complete payment → Booking status automatically updated
3. View payment status on booking page
4. Retry payment if it fails

**Test the flow:**
1. Create a booking via booking form
2. Get redirected to payment page
3. Complete payment with test card
4. See success confirmation with both booking and payment details

---

**Documentation Files:**
- `PAYMENT_SYSTEM_DOCUMENTATION.md` - Payment system standalone guide
- `PAYMENT_SYSTEM_REVIEW.md` - Initial implementation review
- `PAYMENT_QUICK_REFERENCE.md` - Quick reference card
- `BOOKING_PAYMENT_INTEGRATION.md` - This file (integration guide)

**Need Help?** Check the logs: `storage/logs/laravel.log`

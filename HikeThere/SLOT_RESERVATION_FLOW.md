# 🎫 Slot Reservation Flow Diagram

## Complete User Journey with Slot Management

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         SLOT RESERVATION SYSTEM                          │
│                                                                          │
│  Example: Mt. Pulag Trail - Batch on Oct 15, 2025                      │
│  Initial State: 50 capacity, 20 slots taken, 30 available              │
└─────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────┐
│ STEP 1: User Browses Trail                                              │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  User views: Mt. Pulag Trail Page                                       │
│  ├─ Sees available batches/dates                                        │
│  ├─ Clicks "Book This Trail" button                                     │
│  └─ Opens booking form                                                  │
│                                                                          │
│  🎯 Batch Info Display:                                                 │
│  ┌────────────────────────────────────┐                                 │
│  │ Oct 15, 2025 - 6:00 AM             │                                 │
│  │                                     │                                 │
│  │ 📊 30 slots available               │                                 │
│  │ ▓▓▓▓▓▓▓▓░░░░░░░ 40% full           │                                 │
│  │                                     │                                 │
│  │ [Select This Date]                  │                                 │
│  └────────────────────────────────────┘                                 │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ STEP 2: Fill Booking Form                                               │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  📝 Booking Form:                                                        │
│  ┌────────────────────────────────────┐                                 │
│  │ Trail: Mt. Pulag                   │                                 │
│  │ Date: Oct 15, 2025                 │                                 │
│  │ Party Size: [10] people            │ ◄── User enters 10              │
│  │                                     │                                 │
│  │ ✅ 30 slots available               │                                 │
│  │                                     │                                 │
│  │ [Continue to Payment]               │                                 │
│  └────────────────────────────────────┘                                 │
│                                                                          │
│  ⚡ Real-time Validation (AJAX):                                        │
│     GET /api/slots/batch/1?party_size=10                                │
│     Response: { "has_enough_slots": true, "available_slots": 30 }       │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ STEP 3: Slot Validation in BookingController                            │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  🔒 Database Transaction Begins                                         │
│  ├─ Lock batch row: lockForUpdate()                                     │
│  ├─ Check: hasAvailableSlots(10) → TRUE ✅                              │
│  ├─ Create booking with status = 'pending'                              │
│  └─ Commit transaction                                                  │
│                                                                          │
│  📊 Batch State (UNCHANGED):                                            │
│  ├─ Capacity: 50                                                        │
│  ├─ Slots Taken: 20  ◄── No change yet!                                │
│  └─ Available: 30                                                       │
│                                                                          │
│  💡 Why not reserve now?                                                │
│     → Prevents holding slots for unpaid bookings                        │
│     → User might abandon payment                                        │
│     → Slots reserved only after payment confirmation                    │
│                                                                          │
│  🎫 Booking Created:                                                    │
│  ┌────────────────────────────────────┐                                 │
│  │ Booking #123                       │                                 │
│  │ ├─ User: John Doe                  │                                 │
│  │ ├─ Trail: Mt. Pulag                │                                 │
│  │ ├─ Party Size: 10                  │                                 │
│  │ ├─ Status: pending ⏳               │                                 │
│  │ └─ Amount: ₱5,000                  │                                 │
│  └────────────────────────────────────┘                                 │
│                                                                          │
│  → Redirect to: /payment/create?booking_id=123                          │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ STEP 4: Payment Form                                                    │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  💳 Payment Form (Pre-filled from Booking):                             │
│  ┌────────────────────────────────────┐                                 │
│  │ 📋 Booking #123                    │                                 │
│  │                                     │                                 │
│  │ Full Name: John Doe                │ (readonly)                      │
│  │ Email: john@example.com            │ (readonly)                      │
│  │ Mountain: Mt. Pulag                │ (readonly)                      │
│  │ Hike Date: Oct 15, 2025            │ (readonly)                      │
│  │ Participants: 10                   │ (readonly)                      │
│  │                                     │                                 │
│  │ Amount: ₱5,000                     │ (readonly)                      │
│  │ Note: ₱500 per person × 10         │                                 │
│  │                                     │                                 │
│  │ Phone: [___________]                │ (user fills)                    │
│  │                                     │                                 │
│  │ [Pay with PayMongo]                 │                                 │
│  └────────────────────────────────────┘                                 │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ STEP 5: PayMongo Payment Processing                                     │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  User clicks "Pay with PayMongo"                                        │
│  ├─ PaymentController::processPayment()                                 │
│  ├─ Creates BookingPayment record:                                      │
│  │   ├─ booking_id: 123                                                 │
│  │   ├─ amount: 500000 (₱5,000 in cents)                               │
│  │   └─ payment_status: pending                                         │
│  │                                                                       │
│  ├─ Calls PayMongo API                                                  │
│  └─ Redirects user to PayMongo checkout page                            │
│                                                                          │
│  📊 Batch State (STILL UNCHANGED):                                      │
│  ├─ Capacity: 50                                                        │
│  ├─ Slots Taken: 20  ◄── Still no change!                              │
│  └─ Available: 30                                                       │
│                                                                          │
│  User completes payment on PayMongo...                                  │
│  ├─ Enters card: 4120 0000 0000 0007                                    │
│  └─ Confirms payment ✅                                                 │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ STEP 6: PayMongo Webhook (SLOT RESERVATION HAPPENS HERE!)               │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  📡 PayMongo sends webhook:                                             │
│     POST /payment/webhook                                               │
│     {                                                                   │
│       "data": {                                                         │
│         "type": "link.payment.paid",                                    │
│         "reference_number": "PAYMENT-123"                               │
│       }                                                                 │
│     }                                                                   │
│                                                                          │
│  ⚙️  PaymentController::webhook()                                       │
│  ├─ Find payment #123                                                   │
│  ├─ Load booking with batch                                             │
│  ├─ Mark payment as 'paid'                                              │
│  ├─ Update booking status: 'confirmed'                                  │
│  └─ ✨ RESERVE SLOTS:                                                   │
│      $batch->reserveSlots(10);                                          │
│                                                                          │
│  🔥 Batch State UPDATED:                                                │
│  ┌────────────────────────────────────┐                                 │
│  │ Capacity: 50                       │                                 │
│  │ Slots Taken: 30 ◄── +10 RESERVED!  │                                 │
│  │ Available: 20                      │                                 │
│  └────────────────────────────────────┘                                 │
│                                                                          │
│  🎫 Booking #123 CONFIRMED:                                             │
│  ┌────────────────────────────────────┐                                 │
│  │ Booking #123                       │                                 │
│  │ ├─ Status: confirmed ✅             │                                 │
│  │ ├─ Payment: paid ✅                 │                                 │
│  │ └─ Slots: 10 RESERVED ✅            │                                 │
│  └────────────────────────────────────┘                                 │
│                                                                          │
│  📝 Logged:                                                              │
│     "Slots Reserved Successfully"                                       │
│     - booking_id: 123                                                   │
│     - party_size: 10                                                    │
│     - slots_remaining: 20                                               │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ STEP 7: Success Page                                                    │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  User redirected to: /payment/success?payment_id=123                    │
│                                                                          │
│  🎉 Success Page Display:                                               │
│  ┌────────────────────────────────────┐                                 │
│  │ ✅ Payment Successful!              │                                 │
│  │                                     │                                 │
│  │ Booking #123                       │                                 │
│  │ Payment ID: PAY-123456              │                                 │
│  │                                     │                                 │
│  │ Trail: Mt. Pulag                   │                                 │
│  │ Date: Oct 15, 2025                 │                                 │
│  │ Participants: 10                   │                                 │
│  │ Amount Paid: ₱5,000.00             │                                 │
│  │                                     │                                 │
│  │ Status: ✅ Confirmed                │                                 │
│  │                                     │                                 │
│  │ Your 10 slots are now RESERVED!    │                                 │
│  │                                     │                                 │
│  │ [View Booking Details]              │                                 │
│  └────────────────────────────────────┘                                 │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ IMPACT: Other Users Now See Updated Availability                        │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  🌍 Public Trail Page Now Shows:                                        │
│  ┌────────────────────────────────────┐                                 │
│  │ Oct 15, 2025 - 6:00 AM             │                                 │
│  │                                     │                                 │
│  │ ⚠️  20 slots available              │ ◄── Updated!                   │
│  │ ▓▓▓▓▓▓▓▓▓▓▓▓░░░ 60% full           │ ◄── 30/50 reserved             │
│  │                                     │                                 │
│  │ [Select This Date]                  │                                 │
│  └────────────────────────────────────┘                                 │
│                                                                          │
│  If another user tries to book 25 people:                               │
│  ❌ "Only 20 slot(s) available. You requested 25."                      │
│  💡 "Try these dates: Oct 20, Oct 25, Nov 1"                            │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘


┌─────────────────────────────────────────────────────────────────────────┐
│ ALTERNATIVE FLOW: Booking Cancellation                                  │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  User wants to cancel Booking #123                                      │
│                                                                          │
│  🔍 Check if cancellable:                                               │
│  $booking->canBeCancelled()                                             │
│  ├─ Status not 'cancelled' ✅                                           │
│  └─ Batch hasn't started yet ✅                                         │
│                                                                          │
│  ✅ Cancellation Allowed                                                │
│                                                                          │
│  ⚙️  Execute:                                                            │
│  $booking->cancel();                                                    │
│                                                                          │
│  What happens:                                                          │
│  ├─ Update booking status: 'cancelled'                                  │
│  └─ Release slots: $batch->releaseSlots(10)                             │
│                                                                          │
│  🔥 Batch State UPDATED:                                                │
│  ┌────────────────────────────────────┐                                 │
│  │ Capacity: 50                       │                                 │
│  │ Slots Taken: 20 ◄── -10 RELEASED!  │                                 │
│  │ Available: 30                      │                                 │
│  └────────────────────────────────────┘                                 │
│                                                                          │
│  Result:                                                                │
│  ├─ User's 10 slots released                                            │
│  ├─ Availability increased to 30                                        │
│  └─ Other users can now book those slots                                │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘


┌─────────────────────────────────────────────────────────────────────────┐
│ EDGE CASE: Race Condition Prevention                                    │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  Scenario: Two users try to book the LAST 5 slots simultaneously        │
│                                                                          │
│  Time: 10:00:00.000                                                     │
│  ├─ Batch: 50 capacity, 45 slots taken, 5 available                     │
│  ├─ User A: Wants to book 5 people                                      │
│  └─ User B: Wants to book 5 people                                      │
│                                                                          │
│  Time: 10:00:00.100 - User A's request arrives first                    │
│  ┌────────────────────────────────────┐                                 │
│  │ DB::beginTransaction()              │                                 │
│  │ $batch = Batch::lockForUpdate()    │ ◄── LOCKED for User A           │
│  │                                     │                                 │
│  │ Check: hasAvailableSlots(5)        │                                 │
│  │ → TRUE (5 available)                │                                 │
│  │                                     │                                 │
│  │ Create booking...                   │                                 │
│  │ DB::commit()                        │ ◄── Lock released               │
│  └────────────────────────────────────┘                                 │
│                                                                          │
│  Time: 10:00:00.150 - User B's request tries to lock                    │
│  ┌────────────────────────────────────┐                                 │
│  │ DB::beginTransaction()              │                                 │
│  │ $batch = Batch::lockForUpdate()    │ ◄── WAITS for User A            │
│  │ ...waiting...                       │                                 │
│  │ ...User A commits...                │                                 │
│  │ ...Lock acquired!                   │                                 │
│  │                                     │                                 │
│  │ Check: hasAvailableSlots(5)        │                                 │
│  │ → TRUE (0 available now!)           │ ◄── User A took them            │
│  │                                     │                                 │
│  │ ❌ Return error                     │                                 │
│  │ DB::rollBack()                      │                                 │
│  └────────────────────────────────────┘                                 │
│                                                                          │
│  Result:                                                                │
│  ✅ User A: Booking successful (got last 5 slots)                       │
│  ❌ User B: Error "Only 0 slots available"                              │
│  ✅ NO OVERBOOKING!                                                     │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘


┌─────────────────────────────────────────────────────────────────────────┐
│ SUMMARY: Slot State Through The Journey                                 │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  Initial State:                                                         │
│  Capacity: 50 │ Slots Taken: 20 │ Available: 30                         │
│                                                                          │
│  After User Creates Booking (Step 3):                                   │
│  Capacity: 50 │ Slots Taken: 20 │ Available: 30  ◄── No change         │
│  Booking Status: pending ⏳                                              │
│                                                                          │
│  After Payment Success (Step 6):                                        │
│  Capacity: 50 │ Slots Taken: 30 │ Available: 20  ◄── 10 reserved!      │
│  Booking Status: confirmed ✅                                            │
│                                                                          │
│  If User Cancels:                                                       │
│  Capacity: 50 │ Slots Taken: 20 │ Available: 30  ◄── 10 released!      │
│  Booking Status: cancelled ❌                                            │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘


┌─────────────────────────────────────────────────────────────────────────┐
│ KEY DESIGN DECISIONS                                                    │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  1️⃣  Why not reserve slots immediately at booking creation?              │
│     → Prevents holding slots for users who abandon payment              │
│     → Slots only locked after payment confirmation                      │
│     → Maximizes availability for serious bookings                       │
│                                                                          │
│  2️⃣  Why use database row locking?                                       │
│     → Prevents race conditions with concurrent bookings                 │
│     → Ensures atomic slot checking and reservation                      │
│     → Eliminates overbooking risk                                       │
│                                                                          │
│  3️⃣  Why suggest alternative dates?                                      │
│     → Better user experience when preferred date is full                │
│     → Increases conversion (user books different date)                  │
│     → Reduces frustration and abandonment                               │
│                                                                          │
│  4️⃣  Why track slots_taken instead of calculating from bookings?         │
│     → Much faster queries (no need to sum bookings)                     │
│     → Indexed field for performance                                     │
│     → Simple, reliable slot counting                                    │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## Quick Reference

### Formula
```
Available Slots = capacity - slots_taken
```

### Key Events
1. **Booking Created** → Slots NOT reserved (status = pending)
2. **Payment Confirmed** → Slots RESERVED (status = confirmed)
3. **Booking Cancelled** → Slots RELEASED (status = cancelled)

### Protection Mechanisms
- ✅ Database row locking (prevents race conditions)
- ✅ Real-time validation (before booking creation)
- ✅ Atomic operations (increment/decrement)
- ✅ Transaction safety (rollback on error)

---

**Last Updated**: October 2, 2025

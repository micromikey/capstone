# 🐛 Bug Fix: Slot Deduction Issue

## Problem

When a user booked with **party_size = 10**, only **1 slot** was being deducted from the available slots instead of **10 slots**.

### Example:
```
Batch Capacity: 50
User books: 10 people
Expected: 50 - 10 = 40 slots remaining
Actual: 50 - 1 = 49 slots remaining ❌
```

---

## Root Cause

The `trailBatches` method in `BookingController.php` was using the **old slot counting method**:

```php
// OLD CODE (Line 580):
->withCount(['bookings as booked_count' => function($q){ 
    $q->where('status','!=','cancelled'); 
}])

// This counts NUMBER OF BOOKINGS, not party_size!
// So 1 booking with 10 people = counted as 1, not 10
```

Then it calculated remaining slots:
```php
// OLD CODE (Line 595):
$remaining = max(0, ($b->capacity ?? 0) - ($b->booked_count ?? 0));
// ❌ booked_count = number of bookings (1)
// ❌ Not counting actual people (10)
```

---

## The Fix

### Changed Line 575-580:
**Removed** the `withCount` that was counting bookings:
```php
// BEFORE:
$query = Batch::where('trail_id', $trail->id)->whereNotNull('event_id')
    ->with('event')
    ->withCount(['bookings as booked_count' => function($q){ 
        $q->where('status','!=','cancelled'); 
    }])  // ❌ REMOVED THIS
    ->whereIn('event_id', $datedEventIds);

// AFTER:
$query = Batch::where('trail_id', $trail->id)->whereNotNull('event_id')
    ->with('event')  // ✅ No longer counting bookings
    ->whereIn('event_id', $datedEventIds);
```

### Changed Line 595:
**Use the `slots_taken` field** from the Batch model:
```php
// BEFORE:
$remaining = max(0, ($b->capacity ?? 0) - ($b->booked_count ?? 0));
// ❌ Used booked_count (number of bookings)

// AFTER:
$remaining = $b->getAvailableSlots();
// ✅ Uses: capacity - slots_taken
// ✅ slots_taken = sum of all party_size values
```

---

## How It Works Now

### Database Schema:
```sql
batches
├── capacity (50)         -- Total slots
└── slots_taken (0 → 10)  -- Actual people reserved
```

### Slot Reservation Flow:

```
1. User books 10 people
   └─ Booking created with party_size = 10

2. Payment confirmed (webhook)
   └─ $batch->reserveSlots(10)
      └─ $batch->increment('slots_taken', 10)
         └─ slots_taken: 0 → 10 ✅

3. Frontend requests available slots
   └─ $batch->getAvailableSlots()
      └─ Returns: capacity (50) - slots_taken (10)
         └─ Result: 40 slots ✅
```

---

## Testing

### Before Fix:
```
Batch capacity: 50
Slots taken: 0

User books 10 people
→ 1 booking created

Frontend shows: 49 slots remaining ❌
Database slots_taken: 10 (correct)

Issue: Frontend using booked_count (1) 
       instead of slots_taken (10)
```

### After Fix:
```
Batch capacity: 50
Slots taken: 0

User books 10 people
→ 1 booking created

Frontend shows: 40 slots remaining ✅
Database slots_taken: 10 ✅

Fixed: Frontend now using slots_taken (10)
```

---

## Files Modified

### `app/Http/Controllers/Hiker/BookingController.php`

**Line 575-580**: Removed `withCount` query
- Deleted: `->withCount(['bookings as booked_count' => ...])`

**Line 595**: Changed slot calculation
- Before: `$remaining = max(0, ($b->capacity ?? 0) - ($b->booked_count ?? 0));`
- After: `$remaining = $b->getAvailableSlots();`

---

## Why `getAvailableSlots()` is Correct

The `Batch` model has this method:

```php
public function getAvailableSlots(): int
{
    return max(0, $this->capacity - $this->slots_taken);
}
```

Where:
- `capacity` = Total slots (e.g., 50)
- `slots_taken` = Sum of all party_size from confirmed bookings
- Result = Accurate available slots

When payment is confirmed:
```php
$batch->reserveSlots($booking->party_size);
// This does: $batch->increment('slots_taken', $party_size)
```

So if `party_size = 10`:
- `slots_taken` increases by 10 ✅
- `getAvailableSlots()` returns correct count ✅

---

## Verification

To verify the fix is working:

```php
// Create a test booking with party_size = 10
$batch = Batch::first();
echo "Before: " . $batch->getAvailableSlots(); // 50

// Simulate payment confirmation
$batch->reserveSlots(10);
echo "After: " . $batch->getAvailableSlots(); // 40 ✅

// Check database
echo "Slots taken: " . $batch->slots_taken; // 10 ✅
```

---

## Impact

### ✅ Fixed Issues:
1. Slot deduction now matches party_size
2. Frontend shows accurate availability
3. Multiple people bookings work correctly
4. Prevents overbooking

### ⚠️ Note:
The `slots_taken` field is only updated **after payment confirmation** (via webhook), not when booking is created. This is intentional to prevent holding slots for unpaid bookings.

---

## Related Documentation

- **SLOT_MANAGEMENT_SYSTEM.md** - Complete slot management docs
- **SLOT_RESERVATION_FLOW.md** - Visual flow diagrams
- **AUTOMATIC_PRICE_CALCULATION.md** - Price calculation system

---

**Status**: ✅ Fixed  
**Date**: October 2, 2025  
**Impact**: Critical bug fix for accurate slot tracking

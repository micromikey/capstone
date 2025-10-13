# Hiker Profile Access - Complete Conditions Analysis

## URL Structure
```
/org/community/hiker/{hiker_id}?booking={booking_id}
Example: /org/community/hiker/9?booking=8
```

---

## ALL CONDITIONS TO ACCESS THE PROFILE

### 1️⃣ ROUTE MIDDLEWARE (Must Pass All 3)
```php
Route::middleware(['auth:sanctum', 'check.approval', 'user.type:organization'])
```

**Conditions:**
- ✅ User must be **authenticated** (logged in)
- ✅ User's organization must be **approved** (approval_status = 'approved')
- ✅ User type must be **'organization'** (not 'hiker' or 'admin')

**If ANY fail:** Laravel returns **404** (not 403, for security)

---

### 2️⃣ HIKER VALIDATION (Line 22-24)
```php
$hiker = User::where('id', $hikerId)
    ->where('user_type', 'hiker')
    ->firstOrFail();
```

**Conditions:**
- ✅ Hiker ID must **exist** in users table
- ✅ User with that ID must have **user_type = 'hiker'**

**If fails:** `404 Not Found` (firstOrFail throws ModelNotFoundException)

---

### 3️⃣ BOOKING VALIDATION (Line 30-48)

#### If booking ID is provided (from URL query parameter):
```php
$booking = Booking::where('id', $bookingId)
    ->where('user_id', $hiker->id)
    ->whereHas('trail', function($query) use ($organization) {
        $query->where('user_id', $organization->id);
    })
    ->where('payment_status', 'paid')
    ->whereIn('status', ['confirmed', 'completed'])
    ->firstOrFail();
```

**Conditions:**
- ✅ Booking must **exist** with the given booking_id
- ✅ Booking must belong to the **hiker** (booking.user_id = hiker.id)
- ✅ Booking's trail must belong to the **organization** (trail.user_id = organization.id)
- ✅ Payment status must be **'paid'** (not 'pending', 'failed', etc.)
- ✅ Booking status must be **'confirmed' OR 'completed'** (not 'pending' or 'cancelled')

**If fails:** `404 Not Found` (firstOrFail)

#### If NO booking ID provided:
```php
$booking = Booking::where('user_id', $hiker->id)
    ->whereHas('trail', function($query) use ($organization) {
        $query->where('user_id', $organization->id);
    })
    ->where('payment_status', 'paid')
    ->whereIn('status', ['confirmed', 'completed'])
    ->latest()
    ->first();
```

**Same conditions but:**
- ⚠️ Gets the **latest** booking matching criteria
- ⚠️ Uses `first()` instead of `firstOrFail()` (can be null)

---

### 4️⃣ FINAL BOOKING CHECK (Line 51-53)
```php
if (!$booking) {
    abort(403, 'Unauthorized access...');
}
```

**Condition:**
- ✅ At least ONE valid booking must exist

**If fails:** `403 Forbidden` with message

---

## COMMON REASONS FOR 404 ERROR

### Most Likely Issues:

1. **Organization Not Approved** ❌
   - `check.approval` middleware fails
   - Laravel shows 404 instead of revealing auth state
   - **Check:** Is the organization's `approval_status = 'approved'`?

2. **Payment Status Not 'paid'** ❌
   - Booking exists but `payment_status` is:
     - 'pending'
     - 'pending_verification'
     - 'verified' (not the same as 'paid'!)
     - null
   - **Check:** What is the exact `payment_status` value?

3. **Booking Status Not Confirmed** ❌
   - Booking's `status` is:
     - 'pending'
     - 'cancelled'
     - null
   - **Check:** What is the `status` column value?

4. **Trail Doesn't Belong to Organization** ❌
   - The trail's `user_id` doesn't match the logged-in organization's ID
   - **Check:** Does `trails.user_id` = logged-in organization's ID?

5. **Wrong Hiker ID** ❌
   - URL has wrong hiker ID
   - Hiker doesn't exist or is not type 'hiker'

---

## DEBUGGING CHECKLIST

Run these queries on Railway database to diagnose:

### 1. Check the Booking Details
```sql
SELECT 
    b.id as booking_id,
    b.user_id as hiker_id,
    b.trail_id,
    b.payment_status,
    b.status as booking_status,
    t.user_id as trail_owner_id,
    t.trail_name,
    u.name as hiker_name,
    u.user_type as hiker_type
FROM bookings b
JOIN trails t ON b.trail_id = t.id
JOIN users u ON b.user_id = u.id
WHERE b.id = 8;
```

**Expected Results:**
- `payment_status` = `'paid'`
- `booking_status` = `'confirmed'` or `'completed'`
- `hiker_type` = `'hiker'`
- `trail_owner_id` = (logged in organization's ID)

### 2. Check Organization Status
```sql
SELECT 
    id,
    name,
    user_type,
    approval_status,
    approved_at
FROM users
WHERE id = 10; -- Replace with your org ID
```

**Expected Results:**
- `user_type` = `'organization'`
- `approval_status` = `'approved'`
- `approved_at` IS NOT NULL

### 3. Check Hiker
```sql
SELECT 
    id,
    name,
    email,
    user_type
FROM users
WHERE id = 9; -- Hiker ID from URL
```

**Expected Results:**
- `user_type` = `'hiker'`

---

## PAYMENT STATUS VALUES TO CHECK

The code specifically looks for `'paid'`, but your system might be using:
- ❌ `'pending'`
- ❌ `'pending_verification'`
- ❌ `'verified'` ← **Common issue!**
- ✅ `'paid'` ← **Required!**

Check the `bookings` table schema:
```sql
DESCRIBE bookings;
```

Check actual values:
```sql
SELECT DISTINCT payment_status FROM bookings;
```

---

## POSSIBLE FIXES

### If payment_status is 'verified' but should be 'paid':

**Option 1: Update the specific booking**
```sql
UPDATE bookings 
SET payment_status = 'paid' 
WHERE id = 8 AND payment_status = 'verified';
```

**Option 2: Update the controller to accept 'verified'**
```php
->whereIn('payment_status', ['paid', 'verified'])
```

### If booking status is wrong:
```sql
UPDATE bookings 
SET status = 'confirmed' 
WHERE id = 8;
```

### If organization not approved:
```sql
UPDATE users 
SET approval_status = 'approved',
    approved_at = NOW()
WHERE id = 10 AND user_type = 'organization';
```

---

## TESTING SEQUENCE

1. ✅ Check organization is logged in and approved
2. ✅ Go to `/org/bookings` page
3. ✅ Find a booking in the table
4. ✅ Verify the booking shows in the list
5. ✅ Click "View Profile" link
6. ✅ Check browser Network tab for actual HTTP status code
7. ✅ Check Railway logs for error messages

---

## ACTUAL DATABASE QUERY THAT RUNS

When you click "View Profile" with booking ID 8:

```sql
SELECT * FROM bookings 
WHERE id = 8 
  AND user_id = 9 
  AND EXISTS (
    SELECT * FROM trails 
    WHERE bookings.trail_id = trails.id 
      AND trails.user_id = 10
  ) 
  AND payment_status = 'paid' 
  AND status IN ('confirmed', 'completed')
LIMIT 1;
```

**If this returns 0 rows → 404 error**

---

## NEXT STEPS

1. **Check Railway database** with the SQL queries above
2. **Identify which condition is failing**
3. **Fix the data OR update the code** based on findings

Would you like me to help you:
- Connect to Railway database to run these queries?
- Update the controller to accept more payment statuses?
- Add better error logging to identify the issue?

# 📝 Booking Edit/Update/Delete Feature

## What Was Added

### Routes (`routes/web.php`)
```php
Route::get('/hiker/booking/{booking}/edit', [BookingController::class, 'edit'])
    ->name('booking.edit');

Route::patch('/hiker/booking/{booking}', [BookingController::class, 'update'])
    ->name('booking.update');

Route::delete('/hiker/booking/{booking}', [BookingController::class, 'destroy'])
    ->name('booking.destroy');
```

---

## Controller Methods

### 1. `edit()` - Show Edit Form
```php
public function edit(Booking $booking)
{
    $this->authorize('update', $booking);
    
    // Restrictions:
    // ❌ Cannot edit confirmed & paid bookings
    // ❌ Cannot edit if event has started
    
    return view('hiker.booking.edit', compact('booking'));
}
```

### 2. `update()` - Update Booking
```php
public function update(Request $request, Booking $booking)
{
    $this->authorize('update', $booking);
    
    // Validates: date, party_size, notes
    // Updates booking
    
    return redirect()->route('booking.show', $booking)
        ->with('success', 'Booking updated successfully!');
}
```

### 3. `destroy()` - Cancel Booking
```php
public function destroy(Booking $booking)
{
    $this->authorize('delete', $booking);
    
    // Uses: $booking->cancel()
    // Automatically releases slots if confirmed
    
    return redirect()->route('booking.index')
        ->with('success', 'Booking cancelled. Slots released.');
}
```

---

## View (`resources/views/hiker/booking/edit.blade.php`)

### Features:
- ✅ Edit date, party size, notes
- ✅ Shows current price breakdown
- ✅ Shows slot availability info
- ✅ Cancel booking button (with confirmation)
- ✅ Form validation
- ✅ Responsive design

### Restrictions Shown:
- ⚠️ Cannot edit confirmed & paid bookings
- ⚠️ Cannot edit if event has started
- ⚠️ Date must be in the future

---

## User Flow

```
1. User views bookings list
   └─ Clicks "Edit" link

2. Redirected to edit form
   ├─ Pre-filled with current booking data
   ├─ Shows price calculation
   └─ Shows slot availability

3. User updates fields
   ├─ Change date
   ├─ Change party size
   └─ Update notes

4. Clicks "Update Booking"
   ├─ Validates changes
   ├─ Updates database
   └─ Redirects to booking details

5. Optional: Cancel Booking
   ├─ Clicks "Cancel Booking" button
   ├─ Confirms action
   ├─ Booking status → 'cancelled'
   └─ Slots released (if confirmed)
```

---

## Protection Rules

### Cannot Edit When:
1. ❌ Booking is confirmed AND payment is made
2. ❌ Event/batch has already started
3. ❌ User is not the booking owner (authorization)

### Can Cancel When:
1. ✅ Booking status is NOT 'cancelled'
2. ✅ Event hasn't started yet
3. ✅ User is the booking owner

---

## Validation Rules

```php
'date' => 'required|date|after:today',
'party_size' => 'required|integer|min:1|max:50',
'notes' => 'nullable|string|max:500',
```

---

## Integration with Slot Management

### On Update:
- Party size can be changed
- If batch has limited slots, validation may fail
- Price recalculated automatically (trail.price × new party_size)

### On Cancel:
```php
$booking->cancel();
// Automatically calls:
// $batch->releaseSlots($booking->party_size);
```

**Result**: Slots immediately available for other users! ✅

---

## Example Usage

### Edit Link in Index View:
```blade
<a href="{{ route('booking.edit', $booking) }}" 
   class="text-sm text-gray-600">
    Edit
</a>
```

### Cancel Form:
```blade
<form action="{{ route('booking.destroy', $booking) }}" 
      method="POST" 
      onsubmit="return confirm('Are you sure?');">
    @csrf
    @method('DELETE')
    <button type="submit">Cancel Booking</button>
</form>
```

---

## Status

✅ **Routes**: Registered  
✅ **Controller**: Methods added  
✅ **View**: Created  
✅ **Authorization**: Protected  
✅ **Slot Management**: Integrated

**Last Updated**: October 2, 2025

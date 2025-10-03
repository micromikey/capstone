# 🎫 Slot Management Quick Reference

## 🚀 Quick Start

### Check Available Slots

```php
$batch = Batch::find(1);

// Get available slots
$available = $batch->getAvailableSlots(); // Returns: 30

// Check if enough slots
if ($batch->hasAvailableSlots(10)) {
    echo "✅ Can book 10 people";
}

// Check if full
if ($batch->isFull()) {
    echo "⛔ Fully booked";
}
```

---

## 📊 Common Use Cases

### 1. Booking Creation

```php
// Before creating booking - validate slots
if (!$batch->hasAvailableSlots($partySize)) {
    return back()->withErrors([
        'batch_id' => "Only {$batch->getAvailableSlots()} slots available"
    ]);
}

// Create booking (status = 'pending')
$booking = Booking::create([
    'status' => 'pending', // Don't reserve yet
    'party_size' => $partySize,
    // ...
]);
```

### 2. After Payment Success (Webhook)

```php
// Reserve slots when payment confirmed
$payment = BookingPayment::with('booking.batch')->find($paymentId);

if ($payment->booking->batch) {
    $payment->booking->batch->reserveSlots($payment->booking->party_size);
    $payment->booking->update(['status' => 'confirmed']);
}
```

### 3. Booking Cancellation

```php
$booking = Booking::find(1);

if ($booking->canBeCancelled()) {
    $booking->cancel(); // Automatically releases slots
}
```

---

## 🔌 API Endpoints

### Check Slot Availability

```bash
GET /api/slots/batch/{batchId}?party_size=5
```

**Response:**
```json
{
  "available_slots": 30,
  "has_enough_slots": true,
  "message": "30 slot(s) available"
}
```

### Get Alternative Dates

```bash
GET /api/slots/trail/{trailId}/alternatives?party_size=10
```

**Response:**
```json
{
  "alternatives": [
    {
      "batch_id": 2,
      "date": "Oct 20, 2025",
      "available_slots": 45
    }
  ]
}
```

---

## 🎨 Blade Templates

### Display Slot Status

```blade
@if($batch->isFull())
    <span class="badge badge-danger">FULLY BOOKED</span>
@else
    <span class="badge badge-success">
        {{ $batch->getAvailableSlots() }} slots available
    </span>
@endif
```

### Progress Bar

```blade
<div class="progress">
    <div class="progress-bar" 
         style="width: {{ $batch->getOccupancyPercentage() }}%">
        {{ $batch->getOccupancyPercentage() }}% full
    </div>
</div>
```

---

## ⚡ Key Methods

### Batch Model

| Method | Returns | Description |
|--------|---------|-------------|
| `getAvailableSlots()` | `int` | Number of available slots |
| `hasAvailableSlots($needed)` | `bool` | Check if enough slots |
| `isFull()` | `bool` | Check if fully booked |
| `reserveSlots($count)` | `bool` | Reserve slots (returns success) |
| `releaseSlots($count)` | `void` | Release slots back |
| `getOccupancyPercentage()` | `int` | % of slots filled (0-100) |

### Booking Model

| Method | Returns | Description |
|--------|---------|-------------|
| `cancel()` | `void` | Cancel and release slots |
| `canBeCancelled()` | `bool` | Check if cancellable |

---

## 🔒 Race Condition Prevention

```php
DB::beginTransaction();

$lockedBatch = Batch::where('id', $batchId)
    ->lockForUpdate() // ✨ Lock row
    ->first();

if ($lockedBatch->hasAvailableSlots($requested)) {
    $booking = Booking::create([...]);
}

DB::commit(); // Release lock
```

---

## 📐 Formula

```
Available Slots = capacity - slots_taken
```

**Example:**
- Capacity: 50
- Slots Taken: 20
- **Available: 30**

After booking 10 people:
- Slots Taken: 30
- **Available: 20**

---

## 🚨 Error Handling

### Not Enough Slots

```php
if (!$batch->hasAvailableSlots($partySize)) {
    $available = $batch->getAvailableSlots();
    
    // Suggest alternatives
    $alternatives = Batch::where('trail_id', $batch->trail_id)
        ->where('id', '!=', $batch->id)
        ->get()
        ->filter(fn($b) => $b->hasAvailableSlots($partySize))
        ->take(3);
    
    $error = $available > 0
        ? "Only {$available} slot(s) available"
        : "This date is fully booked";
    
    if ($alternatives->isNotEmpty()) {
        $dates = $alternatives->map(fn($b) => $b->starts_at->format('M d, Y'))
            ->implode(', ');
        $error .= " Try: {$dates}";
    }
    
    return back()->withErrors(['batch_id' => $error]);
}
```

---

## 📋 Checklist

Before going live, ensure:

- [x] Migration applied: `slots_taken` field exists
- [x] Booking validation checks available slots
- [x] Payment webhook reserves slots
- [x] Cancellation releases slots
- [x] Database locking prevents race conditions
- [ ] Frontend shows available slots
- [ ] Real-time slot checking (AJAX) implemented
- [ ] Alternative dates displayed when full

---

## 🧪 Quick Test

```php
// Create test batch
$batch = Batch::create([
    'capacity' => 50,
    'slots_taken' => 0,
    // ...
]);

// Reserve 10 slots
$batch->reserveSlots(10);
echo $batch->getAvailableSlots(); // 40

// Reserve 35 more
$batch->reserveSlots(35);
echo $batch->getAvailableSlots(); // 5

// Try to reserve 10 (should fail)
$result = $batch->reserveSlots(10);
echo $result ? 'Success' : 'Failed'; // Failed

// Release 20 slots
$batch->releaseSlots(20);
echo $batch->getAvailableSlots(); // 25
```

---

## 📚 Related Documentation

- **SLOT_MANAGEMENT_SYSTEM.md** - Complete system documentation
- **PAYMENT_SYSTEM_DOCUMENTATION.md** - Payment integration
- **BOOKING_PAYMENT_INTEGRATION.md** - Full workflow

---

**Quick Help:**
- Available slots formula: `capacity - slots_taken`
- Slots reserved: After payment confirmation
- Slots released: On booking cancellation
- Race conditions: Prevented by database locking

**Status**: ✅ Ready to Use  
**Last Updated**: October 2, 2025

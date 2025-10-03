# 💰 Automatic Price Calculation Update

## Overview
The payment system now **automatically calculates** the total amount based on:
```
Total Amount = Trail Price × Number of Participants (Party Size)
```

---

## 🔄 How It Works

### 1. **During Booking Creation**

When a booking is created via `BookingController@store`:

```php
// Fetch trail with package pricing
$trail = Trail::with('package')->find($data['trail_id']);

// Calculate total: price per person × party size × 100 (convert to cents)
if ($trail && $trail->price) {
    $data['price_cents'] = (int) ($trail->price * $data['party_size'] * 100);
}

// Save booking with calculated price
$booking = Booking::create($data);
```

**Example:**
- Trail price: ₱500 per person
- Party size: 3 people
- Calculation: ₱500 × 3 = ₱1,500
- Stored as: 150,000 cents

---

### 2. **In the Payment Form**

The payment form automatically displays the calculated amount:

```blade
<!-- Amount field (readonly when from booking) -->
<input type="number" name="amount" 
       value="{{ $booking->getAmountInPesos() }}" 
       readonly>

<!-- Calculation breakdown shown below -->
<p class="text-xs text-gray-500">
    ₱500.00 per person × 3 participant(s) = ₱1,500.00
</p>
```

---

### 3. **Model Helper Methods**

**Booking Model** now has enhanced calculation methods:

```php
// Get amount in pesos (from stored price_cents)
public function getAmountInPesos(): int
{
    // If price_cents is already set, use it
    if ($this->price_cents) {
        return (int) ($this->price_cents / 100);
    }
    
    // Otherwise calculate from trail price × party size
    if ($this->trail && $this->trail->price) {
        return (int) ($this->trail->price * $this->party_size);
    }
    
    return 0;
}

// Calculate price in cents (for storage)
public function calculatePriceCents(): int
{
    if ($this->trail && $this->trail->price) {
        return (int) ($this->trail->price * $this->party_size * 100);
    }
    
    return 0;
}
```

---

## 💡 Usage Examples

### Example 1: Solo Hiker

```
Trail: Mt. Pulag
Price: ₱1,200 per person
Party Size: 1

Calculation:
₱1,200 × 1 = ₱1,200
```

### Example 2: Group Booking

```
Trail: Mt. Batulao
Price: ₱800 per person
Party Size: 5

Calculation:
₱800 × 5 = ₱4,000
```

### Example 3: Large Group

```
Trail: Mt. Apo
Price: ₱2,500 per person
Party Size: 10

Calculation:
₱2,500 × 10 = ₱25,000
```

---

## 📊 Data Flow

```
1. User fills booking form
   ├─ Selects trail: Mt. Pulag
   ├─ Selects date: Oct 15, 2025
   └─ Party size: 3 people

2. BookingController receives data
   ├─ Loads trail with package
   ├─ Gets price: ₱500
   ├─ Calculates: ₱500 × 3 = ₱1,500
   ├─ Stores as: 150,000 cents
   └─ Creates booking

3. Redirect to payment form
   ├─ Form auto-fills from booking
   ├─ Amount field shows: ₱1,500
   ├─ Shows breakdown: "₱500 × 3 = ₱1,500"
   └─ Field is readonly (locked)

4. Payment processed
   ├─ Amount: ₱1,500 (from booking)
   ├─ PayMongo: 150,000 cents
   └─ Success confirmation
```

---

## 🎨 UI Changes

### Payment Form Display

**Before:**
```
Amount (₱): [____] (user enters manually)
```

**After (with booking):**
```
Amount (₱): [1500] (readonly, auto-calculated)
Note: ₱500.00 per person × 3 participant(s) = ₱1,500.00
```

### Visual Example

```
┌────────────────────────────────────────┐
│ Number of Participants: [3] (readonly) │
├────────────────────────────────────────┤
│ Amount (₱): ₱ [1500] (readonly)        │
│                                         │
│ ₱500.00 per person × 3 participant(s)  │
│ = ₱1,500.00                            │
└────────────────────────────────────────┘
```

---

## 🔍 Database Schema

### bookings table

```sql
bookings
├── id
├── trail_id (FK → trails)
├── party_size (number of participants)
├── price_cents (trail.price × party_size × 100)
└── ...

-- Example data:
-- trail_id: 1 (Mt. Pulag, price: ₱500)
-- party_size: 3
-- price_cents: 150000 (₱1,500 in cents)
```

### Trail Price Source

```sql
trails
├── id
└── ... (no price field directly)

trail_packages
├── id
├── trail_id (FK → trails)
├── price (₱500.00)
└── ...

-- Price accessed via: $trail->price (uses package relationship)
```

---

## 🧪 Testing

### Test Case 1: Simple Booking

```bash
php artisan tinker
```

```php
$trail = Trail::first();
$trail->price = 500; // Ensure trail has price

$booking = Booking::create([
    'user_id' => 1,
    'trail_id' => $trail->id,
    'batch_id' => 1,
    'date' => now()->addDays(7),
    'party_size' => 3,
    'status' => 'confirmed',
    'price_cents' => ($trail->price * 3 * 100), // 150000
]);

echo $booking->getAmountInPesos(); // Output: 1500
```

### Test Case 2: Verify Calculation

```php
$booking = Booking::with('trail.package')->find(1);

echo "Trail Price: ₱" . $booking->trail->price . "\n";
echo "Party Size: " . $booking->party_size . "\n";
echo "Total: ₱" . $booking->getAmountInPesos() . "\n";

// Output:
// Trail Price: ₱500
// Party Size: 3
// Total: ₱1500
```

### Test Case 3: Payment Form

Visit: `http://localhost:8000/payment/create?booking_id=1`

**Expected Result:**
- Amount field: `1500`
- Field is readonly (gray background)
- Shows calculation note below field
- Cannot be manually edited

---

## 🛡️ Benefits

### 1. **Accuracy**
- ✅ No manual calculation errors
- ✅ Consistent pricing across system
- ✅ Price locked when booking created

### 2. **Transparency**
- ✅ Users see price breakdown
- ✅ Clear calculation formula shown
- ✅ No hidden fees or surprises

### 3. **Simplicity**
- ✅ Users don't need to calculate
- ✅ Automatic from booking data
- ✅ Reduces form filling time

### 4. **Data Integrity**
- ✅ Price stored at booking time
- ✅ Immune to later price changes
- ✅ Historical pricing preserved

---

## ⚠️ Important Notes

### Price Locking

When a booking is created, the price is **calculated and stored** in `price_cents`. This means:

- ✅ If trail price changes later, existing bookings are **not affected**
- ✅ Users pay the price shown at booking time
- ✅ Historical records remain accurate

### Manual Payments (No Booking)

If payment is created **without** a booking:
- User must **manually enter** the amount
- No automatic calculation
- Amount field is **editable**

---

## 🔮 Future Enhancements

### 1. Dynamic Pricing (Optional)

```php
// Add multipliers for peak seasons, weekends, etc.
public function calculatePriceCents(): int
{
    $basePrice = $this->trail->price * $this->party_size;
    
    // Weekend surcharge (20%)
    if ($this->date->isWeekend()) {
        $basePrice *= 1.20;
    }
    
    // Peak season surcharge (30%)
    if ($this->isPeakSeason()) {
        $basePrice *= 1.30;
    }
    
    return (int) ($basePrice * 100);
}
```

### 2. Group Discounts

```php
// Discount for large groups
public function calculatePriceCents(): int
{
    $perPersonPrice = $this->trail->price;
    
    // 10% discount for groups of 10+
    if ($this->party_size >= 10) {
        $perPersonPrice *= 0.90;
    }
    
    // 15% discount for groups of 20+
    if ($this->party_size >= 20) {
        $perPersonPrice *= 0.85;
    }
    
    return (int) ($perPersonPrice * $this->party_size * 100);
}
```

### 3. Add-ons and Extras

```php
// Include optional add-ons in price
public function calculatePriceCents(): int
{
    $total = $this->trail->price * $this->party_size;
    
    // Add tent rental (₱200 per person)
    if ($this->includes_tent_rental) {
        $total += (200 * $this->party_size);
    }
    
    // Add guide service (₱500 per group)
    if ($this->includes_guide) {
        $total += 500;
    }
    
    return (int) ($total * 100);
}
```

---

## 📚 Related Documentation

- **BOOKING_PAYMENT_INTEGRATION.md** - Complete integration guide
- **PAYMENT_SYSTEM_DOCUMENTATION.md** - Payment system details
- **INTEGRATION_SUMMARY.md** - System overview

---

## ✅ Summary

### What Changed:

1. ✅ **BookingController**: Calculates `price_cents` when creating booking
2. ✅ **Booking Model**: Enhanced `getAmountInPesos()` method
3. ✅ **Booking Model**: New `calculatePriceCents()` helper method
4. ✅ **Payment Form**: Shows calculation breakdown
5. ✅ **Amount Field**: Readonly when from booking

### Formula:

```
Total Amount = Trail Price × Party Size
```

### Example:

```
₱500 per person × 3 participants = ₱1,500 total
```

---

**Status**: ✅ Complete & Tested  
**Last Updated**: October 2, 2025  
**Implementation**: Automatic price calculation active

# Payment After Booking - Visual Flow

## Complete User Journey

```
┌─────────────────────────────────────────────────────────────────────┐
│                        HIKER SIDE FLOW                               │
└─────────────────────────────────────────────────────────────────────┘

STEP 1: CREATE BOOKING
┌────────────────────────────────────┐
│  Booking Details Page              │
│  /hiker/booking/create             │
│                                    │
│  [Select Organization]             │
│  [Select Trail]                    │
│  [Select Date]                     │
│  [Enter Party Size]                │
│  [Add Notes (optional)]            │
│                                    │
│  [ Create Booking ]                │
└──────────────┬─────────────────────┘
               │
               ▼
         POST /hiker/booking
               │
               ▼
┌────────────────────────────────────┐
│  BookingController::store()        │
│                                    │
│  ✓ Validate booking details        │
│  ✓ Check availability              │
│  ✓ Calculate price                 │
│  ✓ Create booking record           │
│    - status: 'pending'             │
│    - payment_status: 'unpaid'      │
│    - payment_method_used: null     │
│                                    │
│  ✓ Save to database                │
└──────────────┬─────────────────────┘
               │
               ▼
      redirect()->route('booking.payment', $booking->id)
               │
               ▼

STEP 2: PAYMENT PAGE
┌────────────────────────────────────────────────────────────────┐
│  Payment Page                                                   │
│  /hiker/booking/{id}/payment                                    │
│                                                                  │
│  ┌─────────────────────────┐    ┌──────────────────────┐      │
│  │  Payment Information    │    │  Booking Summary     │      │
│  │                         │    │  ──────────────────  │      │
│  │  Check Payment Method   │    │  Trail: Mt. Pulag   │      │
│  │  from Organization      │    │  Date: Oct 15, 2025 │      │
│  │                         │    │  Party: 4 people    │      │
│  │  IF MANUAL:             │    │  Total: ₱2,000      │      │
│  │    [QR Code Display]    │    └──────────────────────┘      │
│  │    [Upload Proof]       │                                   │
│  │    [Transaction #]      │                                   │
│  │    [Submit Payment]     │                                   │
│  │                         │                                   │
│  │  IF AUTOMATIC:          │                                   │
│  │    [Gateway Info]       │                                   │
│  │    [Proceed to Pay]     │                                   │
│  └─────────────────────────┘                                   │
└────────────────────┬───────────────────────────────────────────┘
                     │
         ┌───────────┴───────────┐
         │                       │
         ▼                       ▼
    MANUAL PATH            AUTOMATIC PATH
         │                       │
         │                       │

┌────────────────────────┐   ┌────────────────────────┐
│  MANUAL PAYMENT FLOW   │   │ AUTOMATIC PAYMENT FLOW │
└────────────────────────┘   └────────────────────────┘

┌────────────────────┐         ┌────────────────────┐
│ 1. Scan QR Code    │         │ 1. Click Button    │
│    with Mobile     │         │    "Proceed to Pay"│
│    Wallet          │         │                    │
└────────┬───────────┘         └────────┬───────────┘
         │                              │
         ▼                              ▼
┌────────────────────┐         ┌────────────────────┐
│ 2. Pay via         │         │ 2. Redirect to     │
│    GCash/PayMaya   │         │    Payment Gateway │
│                    │         │    (PayMongo/      │
└────────┬───────────┘         │    Xendit)         │
         │                     └────────┬───────────┘
         ▼                              │
┌────────────────────┐                 ▼
│ 3. Take Screenshot │         ┌────────────────────┐
│    of Receipt      │         │ 3. Enter Card Info │
└────────┬───────────┘         │    or E-Wallet     │
         │                     └────────┬───────────┘
         ▼                              │
┌────────────────────┐                 ▼
│ 4. Upload Proof    │         ┌────────────────────┐
│    on Payment Page │         │ 4. Complete Payment│
└────────┬───────────┘         │    on Gateway      │
         │                     └────────┬───────────┘
         ▼                              │
┌────────────────────┐                 ▼
│ 5. Enter           │         ┌────────────────────┐
│    Transaction #   │         │ 5. Return to       │
└────────┬───────────┘         │    HikeThere       │
         │                     └────────┬───────────┘
         ▼                              │
┌────────────────────┐                 ▼
│ 6. Submit Form     │         ┌────────────────────┐
└────────┬───────────┘         │ 6. Auto-Verified   │
         │                     └────────┬───────────┘
         │                              │
         └──────────┬───────────────────┘
                    │
                    ▼
         POST /hiker/booking/{id}/payment
                    │
                    ▼
┌─────────────────────────────────────────┐
│  BookingController::submitPayment()     │
│                                         │
│  IF MANUAL:                             │
│    ✓ Save payment proof                │
│    ✓ Save transaction number           │
│    ✓ Set payment_method_used: 'manual' │
│    ✓ Set payment_status: 'pending'     │
│    ✓ Redirect to booking details       │
│                                         │
│  IF AUTOMATIC:                          │
│    ✓ Set payment_method_used: 'auto'   │
│    ✓ Redirect to gateway               │
└────────────────┬────────────────────────┘
                 │
                 ▼
          BOOKING UPDATED
                 │
    ┌────────────┴────────────┐
    │                         │
    ▼                         ▼
MANUAL                    AUTOMATIC
payment_status:           payment_status:
'pending'                 'paid'
    │                         │
    ▼                         │
┌───────────────┐             │
│ Organization  │             │
│ Verifies      │             │
│ Payment       │             │
└───────┬───────┘             │
        │                     │
        ▼                     │
payment_status:               │
'verified'                    │
        │                     │
        └──────────┬──────────┘
                   │
                   ▼
          BOOKING CONFIRMED!
```

## Page Layouts

### Before: Booking Details Page (Old)
```
┌────────────────────────────────────────┐
│  Create Booking                        │
├────────────────────────────────────────┤
│                                        │
│  [Organization Dropdown]               │
│  [Trail Dropdown]                      │
│  [Date Picker]                         │
│  [Party Size Input]                    │
│  [Notes Textarea]                      │
│                                        │
│  ╔══════════════════════════════════╗ │  ← REMOVED!
│  ║   PAYMENT SECTION (REMOVED)      ║ │
│  ║                                  ║ │
│  ║   [QR Code Image]                ║ │
│  ║   [Upload Payment Proof]         ║ │
│  ║   [Transaction Number]           ║ │
│  ║   [Payment Notes]                ║ │
│  ╚══════════════════════════════════╝ │
│                                        │
│  [Cancel] [Create Booking]             │
└────────────────────────────────────────┘
```

### After: Booking Details Page (New)
```
┌────────────────────────────────────────┐
│  Create Booking                        │
├────────────────────────────────────────┤
│                                        │
│  [Organization Dropdown]               │
│  [Trail Dropdown]                      │
│  [Date Picker]                         │
│  [Party Size Input]                    │
│  [Notes Textarea]                      │
│                                        │
│  ← Much cleaner! Payment removed      │
│                                        │
│  [Cancel] [Create Booking]             │
└────────────────────────────────────────┘
                  │
                  ▼
         Creates booking then
         redirects to ↓
```

### New: Payment Page
```
┌─────────────────────────────────────────────────────────────┐
│  Complete Your Payment                                       │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌───────────────────────────┐  ┌──────────────────────┐   │
│  │ PAYMENT INFORMATION       │  │ BOOKING SUMMARY      │   │
│  ├───────────────────────────┤  ├──────────────────────┤   │
│  │                           │  │ Trail: Mt. Pulag     │   │
│  │ ╔═══════════════════════╗ │  │ Date: Oct 15, 2025   │   │
│  │ ║  [QR CODE IMAGE]      ║ │  │ Party: 4 people      │   │
│  │ ║                       ║ │  │ ─────────────────    │   │
│  │ ║  Scan to Pay          ║ │  │ Subtotal: ₱2,000     │   │
│  │ ╚═══════════════════════╝ │  │ Total: ₱2,000        │   │
│  │                           │  │                       │   │
│  │ Instructions:             │  │ Booking #12345       │   │
│  │ Please scan and pay...    │  └──────────────────────┘   │
│  │                           │                               │
│  │ Upload Payment Proof:     │                               │
│  │ ┌─────────────────────┐   │                               │
│  │ │ [Preview Image]     │   │                               │
│  │ │ [Remove]            │   │                               │
│  │ └─────────────────────┘   │                               │
│  │ [Browse Files...]         │                               │
│  │                           │                               │
│  │ Transaction Number:       │                               │
│  │ [Input Field]             │                               │
│  │                           │                               │
│  │ Payment Notes:            │                               │
│  │ [Textarea]                │                               │
│  │                           │                               │
│  │ ⚠️ Payment Verification   │                               │
│  │    Required               │                               │
│  │                           │                               │
│  │ [Cancel] [Submit Payment] │                               │
│  └───────────────────────────┘                               │
└─────────────────────────────────────────────────────────────┘
```

## State Transitions

```
BOOKING CREATION → PAYMENT → VERIFICATION → CONFIRMED

pending          unpaid      pending       verified
  ↓                ↓            ↓             ↓
Create         Payment      Org Checks    Confirmed!
Booking         Page        Payment
```

## Timeline View

```
Minute 0:  Hiker fills booking form
           ↓
Minute 1:  Submit booking
           ↓ (instant redirect)
Minute 1:  Payment page appears
           ↓
Minute 2:  Hiker scans QR code
           ↓
Minute 3:  Pays with GCash
           ↓
Minute 4:  Takes screenshot
           ↓
Minute 5:  Uploads proof + ref number
           ↓
Minute 6:  Submits payment
           ↓
           [WAIT FOR ORG]
           ↓
Hour 1:    Org reviews payment
           ↓
Hour 2:    Org verifies ✓
           ↓
Hour 2:    Booking confirmed!
           ↓
           Hiker gets notification
```

## Component Breakdown

### Booking Details Form
```
┌────────────────────┐
│ Organization       │ → Dropdown
│ Trail              │ → Dropdown  
│ Date               │ → Date Picker
│ Party Size         │ → Number Input
│ Notes              │ → Textarea
└────────────────────┘
         ↓
    [Submit]
         ↓
  No Payment Info!
```

### Payment Page
```
┌────────────────────┐
│ QR Code            │ → Image Display
│ Payment Proof      │ → File Upload + Preview
│ Transaction Number │ → Text Input
│ Payment Notes      │ → Textarea
│ Warning            │ → Alert Box
└────────────────────┘
         ↓
    [Submit]
         ↓
   Payment Recorded
```

## Benefits Visualization

### Before: Single Long Page
```
[Booking Form]
      ↓
[Payment Section] ← Long scroll
      ↓            Intimidating
[Submit Button]     Complex
```

### After: Two Clean Pages
```
[Booking Form]
      ↓
[Submit Button] ← Quick!
      ↓            Simple
[Payment Page]  ← Separate
      ↓            Focused  
[Submit Payment]
```

---

**Key Improvement**: Separation of concerns makes the process feel simpler and more manageable for hikers!

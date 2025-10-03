# Payment Method Toggle - Visual Reference

## Toggle Switch Component

```
┌─────────────────────────────────────────────────────────────────────┐
│                    Active Payment Method                             │
│                                                                       │
│  Choose how hikers will pay for bookings. This determines what      │
│  payment option they see during checkout.                           │
│                                                                       │
│                         ┌──────────────────┐                        │
│                         │  🔄  ○          │  Manual Payment         │
│                         │  [Orange BG]    │  Using QR code          │
│                         └──────────────────┘                        │
│                                                                       │
│  OR (when toggled to Automatic)                                     │
│                                                                       │
│                         ┌──────────────────┐                        │
│                         │          ○  💳  │  Automatic Payment      │
│                         │  [Green BG]     │  Using payment gateway  │
│                         └──────────────────┘                        │
│                                                                       │
│  ℹ️  Hikers will see your QR code and upload payment proof         │
│     during checkout.                                                 │
└─────────────────────────────────────────────────────────────────────┘
```

## Toggle States

### State 1: Manual Payment (Default)
```
Toggle Position: LEFT
Background Color: Orange (#f59e0b)
Icon: QR Code (📱)
Label: "Manual Payment"
Description: "Using QR code"
Status: "Hikers will see your QR code and upload payment proof during checkout."
```

### State 2: Automatic Payment
```
Toggle Position: RIGHT
Background Color: Green (#10b981)
Icon: Credit Card (💳)
Label: "Automatic Payment"
Description: "Using payment gateway"
Status: "Hikers will be directed to the payment gateway to complete their booking."
```

## Full Page Layout

```
┌─────────────────────────────────────────────────────────────────────┐
│  Payment Setup                                                       │
│  Configure how you receive payments from hikers                     │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  ✓  Payment method updated successfully!                            │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  [TOGGLE SWITCH SECTION - See above]                                │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  [Manual Payment Tab] | [Automatic Payment Tab]                     │
├─────────────────────────────────────────────────────────────────────┤
│                                                                       │
│  Manual Payment with QR Code                                        │
│  Simple and direct! Hikers scan your QR code...                     │
│                                                                       │
│  Current Status                                                      │
│  ✓ QR Code Configured / ⚠ Not Configured Yet                       │
│                                                                       │
│  Upload Your Payment QR Code *                                      │
│  [File Upload Area with Preview]                                    │
│                                                                       │
│  Payment Instructions (Optional)                                    │
│  [Text Area]                                                         │
│                                                                       │
│  [Activate Manual Payment Button]                                   │
│                                                                       │
│  How Manual Payment Works                                           │
│  1. Hiker Books                                                      │
│  2. Hiker Pays                                                       │
│  3. Upload Proof                                                     │
│  4. You Verify                                                       │
│  5. Booking Confirmed                                                │
│                                                                       │
└─────────────────────────────────────────────────────────────────────┘
```

## Hiker Experience Flow

### When Organization Selects MANUAL Payment:

```
Hiker Booking Page:
┌─────────────────────────────────────────────────────────────────────┐
│  Payment Information                                                 │
├─────────────────────────────────────────────────────────────────────┤
│                                                                       │
│  Scan QR Code to Pay                                                │
│                                                                       │
│      ┌─────────────┐                                                │
│      │             │                                                 │
│      │  [QR CODE]  │                                                │
│      │             │                                                 │
│      └─────────────┘                                                 │
│                                                                       │
│  Instructions: Please include your booking reference...             │
│                                                                       │
│  Upload Payment Proof *                                             │
│  [File Upload]                                                       │
│                                                                       │
│  Transaction/Reference Number *                                     │
│  [Input Field]                                                       │
│                                                                       │
│  Payment Notes (Optional)                                           │
│  [Text Area]                                                         │
│                                                                       │
│  ⚠️  Payment Verification Required                                  │
│     The organization will verify your payment proof.                │
│                                                                       │
└─────────────────────────────────────────────────────────────────────┘
```

### When Organization Selects AUTOMATIC Payment:

```
Hiker Booking Page:
┌─────────────────────────────────────────────────────────────────────┐
│  Payment Information                                                 │
├─────────────────────────────────────────────────────────────────────┤
│                                                                       │
│  ℹ️  You'll be redirected to a secure payment page after           │
│     creating your booking.                                          │
│                                                                       │
│  [No QR Code Shown]                                                 │
│  [No Upload Fields]                                                 │
│                                                                       │
└─────────────────────────────────────────────────────────────────────┘
```

## Toggle Animation

```
Step 1: Click Toggle (Manual → Automatic)
┌──────────────────┐
│  🔄  ○          │  → Click
│  [Orange]       │
└──────────────────┘

Step 2: Animation (300ms)
┌──────────────────┐
│  🔄    ○        │  → Sliding
│  [Orange→Green] │
└──────────────────┘

Step 3: Final State
┌──────────────────┐
│          ○  💳  │  → Complete
│      [Green]    │
└──────────────────┘

Step 4: Form Submits
Loading indicator appears

Step 5: Page Refreshes
Success message shown
Toggle now in Automatic position
Tab switches to Automatic Payment
```

## Color Scheme

### Manual Payment Theme:
- Primary: Orange (#f59e0b)
- Icon: QR Code symbol
- Badge: Green "Recommended"

### Automatic Payment Theme:
- Primary: Green (#10b981)
- Icon: Credit card symbol
- Badge: Blue "Beta"

### Toggle Component Colors:
- Off State (Manual): Orange background (#f59e0b)
- On State (Automatic): Green background (#10b981)
- Toggle Dot: White (#ffffff)
- Toggle Border: Gray (#d1d5db)

## Responsive Behavior

### Desktop (>= 1024px):
- Toggle switch on right side
- Full width gradient card
- Large toggle size (80px × 40px)

### Tablet (768px - 1023px):
- Toggle switch moves below text
- Stacked layout
- Medium toggle size

### Mobile (< 768px):
- Vertical stack layout
- Toggle centered
- Smaller toggle size (64px × 32px)
- Text wraps appropriately

## Interaction States

### Hover:
- Toggle dot slightly enlarges
- Cursor changes to pointer
- Subtle shadow appears

### Active (Clicking):
- Toggle dot compresses slightly
- Quick visual feedback

### Loading (Form Submitting):
- Toggle disabled
- Loading spinner appears
- Prevent multiple submissions

### Success:
- Green checkmark animation
- Success message appears
- Toggle updates to new state

### Error:
- Toggle reverts to previous state
- Error message appears
- Allow retry

## Accessibility

- ✅ Keyboard navigable (Tab to focus)
- ✅ Screen reader friendly labels
- ✅ ARIA attributes included
- ✅ High contrast colors
- ✅ Large touch targets (44px minimum)
- ✅ Clear visual feedback
- ✅ Status announcements

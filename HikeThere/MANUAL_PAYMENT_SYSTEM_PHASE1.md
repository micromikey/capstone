# Manual Payment System Implementation - Phase 1

## Overview
We've implemented a two-tab payment system for organizations, allowing them to choose between **Manual Payment (QR Code)** and **Automatic Payment (Gateway - Beta)**.

---

## ✅ What's Been Completed

### 1. Database Structure
**Files Created:**
- `database/migrations/2025_10_03_202528_add_manual_payment_fields_to_organization_payment_credentials.php`
- `database/migrations/2025_10_03_202543_add_manual_payment_fields_to_bookings.php`

**Fields Added to `organization_payment_credentials`:**
- `qr_code_path` (string, nullable) - Stores the uploaded QR code image path
- `payment_method` (enum: 'manual', 'automatic') - Default: 'automatic'
- `manual_payment_instructions` (text, nullable) - Custom instructions from org

**Fields Added to `bookings`:**
- `payment_proof_path` (string, nullable) - Hiker's payment receipt screenshot
- `transaction_number` (string, nullable) - Transaction reference number
- `payment_notes` (text, nullable) - Additional notes from hiker
- `payment_status` (enum: 'pending', 'verified', 'rejected') - Default: 'pending'
- `payment_method_used` (enum: 'manual', 'automatic') - Which method was used
- `payment_verified_at` (timestamp, nullable) - When org verified the payment
- `payment_verified_by` (unsigned big integer, nullable) - Org user ID who verified

### 2. Model Updates
**File Modified:** `app/Models/OrganizationPaymentCredential.php`

**New Methods Added:**
- `hasManualPaymentConfigured()` - Checks if QR code is uploaded
- `isManualPayment()` - Checks if org is using manual payment mode

**Fillable Fields Updated:**
- Added: `qr_code_path`, `payment_method`, `manual_payment_instructions`

### 3. Payment Settings UI (Organization Side)
**Files Created/Modified:**
- `resources/views/org/payment/index.blade.php` - New tabbed interface
- `resources/views/org/payment/index-original-backup.blade.php` - Backup of original
- `resources/views/org/payment/partials/automatic-payment-form.blade.php` - Gateway settings partial

**Features:**
- **Two Tabs:**
  - **Left Tab (Manual Payment - QR Code)** - Marked as "Recommended"
  - **Right Tab (Automatic Payment - Gateway)** - Marked as "Beta"
- **Manual Payment Tab Includes:**
  - QR code image upload (PNG, JPG, GIF up to 10MB)
  - Visual preview of current QR code
  - Optional payment instructions textarea
  - "How It Works" section explaining the 5-step process
  - Current status indicator
- **Automatic Payment Tab:**
  - Contains the original PayMongo/Xendit gateway settings
  - Beta badge to indicate it's an advanced feature

### 4. Controller Updates
**File Modified:** `app/Http/Controllers/OrganizationPaymentController.php`

**New Method Added:**
- `updateManual(Request $request)` - Handles QR code upload and manual payment settings

**Updated Methods:**
- `update()` - Now sets `payment_method` to 'automatic' when saving gateway credentials

**Validation:**
- QR code: max 10MB, accepts jpeg, png, jpg, gif
- Instructions: max 1000 characters

**Storage:**
- QR codes stored in `storage/app/public/qr_codes/`
- Old QR code automatically deleted when uploading a new one

### 5. Routes
**File Modified:** `routes/web.php`

**New Route Added:**
```php
Route::put('/org/payment/manual', [OrganizationPaymentController::class, 'updateManual'])
    ->name('org.payment.update-manual');
```

---

## 🚧 What Still Needs to Be Done

### Phase 2: Hiker Booking Flow
1. **Update Booking Page for Hikers**
   - Detect if organization uses manual or automatic payment
   - Show QR code and payment instructions for manual payment orgs
   - Add file upload for payment proof (receipt screenshot)
   - Add fields for transaction number and notes
   - Hide PayMongo/Xendit checkout for manual payment bookings

2. **Update Booking Controller**
   - Handle payment proof upload
   - Store transaction details
   - Set initial status as 'pending'
   - Send notification to org when booking with proof is submitted

### Phase 3: Organization Verification Interface
1. **Bookings List Updates**
   - Add filter/tab for "Pending Payment Verification"
   - Show payment proof thumbnail in bookings list
   - Add "Verify" and "Reject" buttons for pending payments

2. **Booking Details Page**
   - Display full-size payment proof image
   - Show transaction number and notes
   - Add verification form with approve/reject actions
   - Log verification timestamp and verifier user ID

### Phase 4: Hiker Status Messages
1. **Booking Confirmation Page**
   - For manual payments: Show "Waiting for verification" message
   - For automatic payments: Show "Booking confirmed" immediately
   - Display different UI states based on payment_status

2. **Email Notifications**
   - Hiker: "Payment proof submitted, awaiting verification"
   - Hiker: "Payment verified! Booking confirmed"
   - Hiker: "Payment rejected, please resubmit"
   - Org: "New booking payment proof submitted"

---

## 📋 Testing Checklist

### Organization Side
- [ ] Navigate to Payment Setup page
- [ ] Switch between Manual and Automatic tabs
- [ ] Upload a QR code image
- [ ] Add payment instructions
- [ ] Save and verify QR code appears in preview
- [ ] Upload a different QR code and verify old one is replaced
- [ ] Switch to Automatic tab and verify gateway settings still work

### Hiker Side (After Phase 2)
- [ ] Book a trail from org using manual payment
- [ ] See QR code displayed on booking page
- [ ] Upload payment proof screenshot
- [ ] Enter transaction number
- [ ] Submit booking
- [ ] See "Pending verification" message

### Organization Verification (After Phase 3)
- [ ] See new booking in "Pending Verification" list
- [ ] Click to view payment proof
- [ ] Verify payment matches e-wallet records
- [ ] Approve or reject payment
- [ ] Verify hiker receives appropriate notification

---

## 🗂️ File Structure

```
HikeThere/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── OrganizationPaymentController.php (updated)
│   └── Models/
│       └── OrganizationPaymentCredential.php (updated)
│
├── database/
│   └── migrations/
│       ├── 2025_10_03_202528_add_manual_payment_fields_to_organization_payment_credentials.php
│       └── 2025_10_03_202543_add_manual_payment_fields_to_bookings.php
│
├── resources/
│   └── views/
│       └── org/
│           └── payment/
│               ├── index.blade.php (new tabbed version)
│               ├── index-original-backup.blade.php (backup)
│               ├── index-with-tabs.blade.php (staging file)
│               └── partials/
│                   └── automatic-payment-form.blade.php
│
├── routes/
│   └── web.php (updated)
│
└── storage/
    └── app/
        └── public/
            └── qr_codes/ (QR code uploads stored here)
```

---

## 💡 Key Design Decisions

1. **Manual Payment as Default Recommendation**
   - Lower barrier to entry for organizations
   - No gateway fees
   - Familiar process (similar to common e-wallet transactions in PH)

2. **Beta Label on Automatic Payments**
   - Sets expectations that gateway integration is more advanced
   - Encourages organizations to start with manual then upgrade later

3. **Separate Routes and Methods**
   - `update()` for automatic payment gateways
   - `updateManual()` for manual payment QR codes
   - Keeps code organized and prevents conflicts

4. **Payment Status Workflow**
   - `pending` → waiting for org verification
   - `verified` → org confirmed payment
   - `rejected` → org couldn't verify, needs resubmission

---

## 🚀 Next Steps

To continue development, start with **Phase 2** by modifying the booking flow for hikers. The key file to update is:

`resources/views/hiker/booking/booking-details.blade.php`

You'll need to:
1. Check the organization's payment method
2. Display QR code if manual
3. Add file upload and form fields for payment proof
4. Update BookingController to handle the submission

---

## 📝 Notes

- All migrations have been successfully run
- QR codes are stored in `storage/app/public/qr_codes/`
- Make sure to run `php artisan storage:link` if not already done
- The original payment page is backed up as `index-original-backup.blade.php`
- Payment verification should happen server-side (org checks their e-wallet)

---

**Date:** October 3, 2025
**Phase:** 1 of 4 Complete
**Status:** ✅ Organization Payment Setup Complete, Ready for Hiker Booking Flow

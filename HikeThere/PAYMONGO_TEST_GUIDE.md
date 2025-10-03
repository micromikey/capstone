# PayMongo Test Payment Guide

## Issue: Payment Page Keeps Reloading at Step 4

This usually happens when using:
- **Incorrect test card details** 
- **Missing billing information for GCash/e-wallets**
- **Triggering fraud detection** in PayMongo's test mode

---

## 💳 **Testing with CREDIT/DEBIT CARDS**

Use these **EXACT** details when testing card payments:

### **Card Number:**
```
4343 4343 4343 4345
```
or
```
4571 7360 0000 0183
```

### **Expiry Date:**
```
Any future date (e.g., 12/25 or 01/30)
```

### **CVC:**
```
123
```

### **Cardholder Name:**
```
Any name (e.g., Test User or John Doe)
```

---

## 📱 **Testing with GCASH (E-Wallet)**

**IMPORTANT:** GCash requires valid billing information (phone number) to work!

### **Test Phone Number for GCash:**
```
09123456789
```
or any valid PH mobile number format: `09XXXXXXXXX`

### **GCash OTP in Test Mode:**
When PayMongo redirects to GCash, use any 6-digit code:
```
123456
```

### **Known GCash Test Issues:**
- ⚠️ GCash test mode can be unreliable in local development
- ⚠️ Sometimes shows "Transaction failed" even with correct details
- ⚠️ May require actual GCash account link in production

### **GCash Alternative for Testing:**
If GCash keeps failing, use **Card payment** instead or the **manual test endpoint** below.

---

## 🚫 **Common Mistakes That Cause Reloading:**

1. **Wrong card number** - Using `4242 4242 4242 4242` (Stripe test card) won't work
2. **Past expiry date** - Using expired dates like `01/20`
3. **Wrong CVC** - PayMongo expects exactly 3 digits
4. **Missing phone number** - GCash requires valid PH phone format (09XXXXXXXXX)
5. **Invalid billing info** - E-wallets need name, email, and phone
6. **Triggering fraud detection** - Testing too many payments rapidly

---

## 🧪 **Testing Flow:**

### For Card Payments:
1. **Create a booking** (select trail, date, party size)
2. **Fill in phone number** (e.g., `09123456789`)
3. **Click "Proceed to Payment"** → redirects to PayMongo
4. **Select "Card"** as payment method
5. **Fill in card details:**
   - Card: `4343 4343 4343 4345`
   - Expiry: `12/25`
   - CVC: `123`
   - Name: `Test User`
6. **Click "Pay"**
7. **Should redirect to success page** ✅

### For GCash Payments:
1. **Create a booking** (select trail, date, party size)
2. **Fill in valid PH phone number** (e.g., `09123456789`) - **REQUIRED!**
3. **Click "Proceed to Payment"** → redirects to PayMongo
4. **Select "GCash"** as payment method
5. **Enter OTP** (any 6-digit code in test mode, e.g., `123456`)
6. **Should redirect to success page** ✅

**Note:** If GCash test mode fails, it's a known PayMongo limitation. Use card payment or manual confirmation instead.

---

## 🔧 **Local Testing Alternative (Bypass PayMongo):**

If PayMongo continues to have issues, use the test endpoint:

```
http://localhost:8000/test/confirm-payment/{payment_id}
```

**Example:**
```
http://localhost:8000/test/confirm-payment/4
```

This will:
- ✅ Mark payment as paid
- ✅ Update booking to confirmed
- ✅ Reserve slots automatically
- ✅ Bypass PayMongo entirely

---

## 📊 **Check Payment Status:**

### Via Browser:
```
http://localhost:8000/hiker/booking/{booking_id}
```

### Via Tinker:
```bash
php artisan tinker --execute="echo \App\Models\BookingPayment::find(4)->payment_status"
```

---

## 🔄 **Recent Changes Made:**

1. **Added billing information** - Name, email, and phone now sent to PayMongo
2. **Fixed GCash support** - E-wallets now receive required phone number
3. **Removed custom reference_number** - Let PayMongo auto-generate it
4. **Simplified description** - Removed long remarks that might cause issues
5. **Updated webhook parser** - Now extracts payment ID from remarks field
6. **Changed webhook to POST** - Was GET, now correctly POST

**Critical Fix for GCash:** The payment link now includes:
```php
'billing' => [
    'name' => 'Customer Name',
    'email' => 'customer@email.com',
    'phone' => '09123456789', // REQUIRED for GCash!
]
```

Without this billing information, GCash payments would fail at step 4.

---

## ⚠️ **Production Checklist:**

Before deploying to production:

1. **Remove test endpoint** (line 29 in routes/web.php)
   ```php
   Route::get('/test/confirm-payment/{paymentId}', ...); // DELETE THIS
   ```

2. **Setup webhook tunneling** (ngrok or Laravel Expose)
   ```bash
   composer require beyondcode/expose
   php artisan serve
   expose share http://localhost:8000
   ```

3. **Configure PayMongo webhook URL:**
   - Go to PayMongo dashboard
   - Add webhook: `https://your-exposed-url.com/payment/webhook`
   - Event: `link.payment.paid`

4. **Use production keys:**
   ```env
   PAYMONGO_SECRET_KEY=sk_live_xxxxxxxxxxxxx
   ```

---

## 📝 **Test Card Cheat Sheet:**

| Card Number | Result |
|-------------|--------|
| `4343 4343 4343 4345` | ✅ Success |
| `4571 7360 0000 0183` | ✅ Success |
| `4000 0000 0000 0002` | ❌ Declined |
| `4000 0000 0000 0069` | ❌ Expired |

Source: [PayMongo Test Cards Documentation](https://developers.paymongo.com/docs/testing)

---

## 🐛 **Debugging Tips:**

If payment still fails:

1. **Clear Laravel cache:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```

2. **Check logs:**
   ```bash
   Get-Content storage/logs/laravel.log -Tail 50 | Select-String "PayMongo"
   ```

3. **Verify PayMongo API key:**
   ```bash
   php artisan tinker --execute="echo env('PAYMONGO_SECRET_KEY')"
   ```

4. **Test with smaller amount:**
   - Try ₱100 instead of large amounts
   - PayMongo test mode has limits

---

## 💡 **Quick Fix Commands:**

### Manually confirm a payment:
```bash
# Visit in browser:
http://localhost:8000/test/confirm-payment/4

# Or use curl:
curl http://localhost:8000/test/confirm-payment/4
```

### Check if slots were reserved:
```bash
php artisan tinker --execute="echo \App\Models\Batch::find(47)->slots_taken"
```

### Check booking status:
```bash
php artisan tinker --execute="echo \App\Models\Booking::find(6)->status"
```

---

**Last Updated:** October 2, 2025  
**Status:** Payment system operational ✅  
**Known Issue:** PayMongo test mode occasionally fails - use test endpoint as fallback

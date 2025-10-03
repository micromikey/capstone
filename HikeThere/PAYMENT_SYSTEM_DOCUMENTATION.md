# 💳 Payment System Documentation - HikeThere

## Overview
Complete PayMongo integration for handling hiking booking payments with secure transaction tracking and webhook support.

---

## 📁 File Structure

```
HikeThere/
├── app/
│   ├── Http/Controllers/
│   │   └── PaymentController.php          ← Handles payment processing
│   └── Models/
│       ├── BookingPayment.php             ← Payment records model
│       └── Booking.php                    ← Trail booking model (separate)
├── database/migrations/
│   └── 2025_10_02_120000_create_booking_payments_table.php
├── resources/views/payment/
│   ├── pay.blade.php                      ← Payment form
│   └── success.blade.php                  ← Success page
└── routes/
    └── web.php                            ← Payment routes
```

---

## 🚀 Setup Instructions

### 1. Run Migration
```bash
cd HikeThere
php artisan migrate
```

### 2. Configure PayMongo Keys

Add to your `.env` file:
```env
PAYMONGO_PUBLIC_KEY=pk_test_your_public_key_here
PAYMONGO_SECRET_KEY=sk_test_your_secret_key_here
```

**Get your keys from:**
1. Go to [PayMongo Dashboard](https://dashboard.paymongo.com)
2. Navigate to **Developers** → **API Keys**
3. Copy your **Test Secret Key** (starts with `sk_test_`)
4. Copy your **Test Public Key** (starts with `pk_test_`)

### 3. Test the Payment System

Visit: `http://localhost:8000/payment/create`

---

## 🔄 Payment Flow

```
1. User fills payment form → /payment/create
2. Form submitted → /payment/process
3. PaymentController creates BookingPayment record (status: pending)
4. PayMongo API creates payment link
5. User redirected to PayMongo checkout
6. User completes payment on PayMongo
7. PayMongo webhook triggers → /payment/webhook
8. Payment status updated (status: paid)
9. User redirected to → /payment/success
```

---

## 📊 Database Schema

### `booking_payments` table

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `fullname` | varchar(255) | Customer name |
| `email` | varchar(255) | Customer email |
| `phone` | varchar(20) | Contact number |
| `mountain` | varchar(255) | Trail/mountain name |
| `amount` | integer | Amount in pesos |
| `hike_date` | date | Scheduled hike date |
| `participants` | integer | Number of hikers |
| `paymongo_link_id` | varchar(255) | PayMongo link ID |
| `paymongo_payment_id` | varchar(255) | PayMongo payment ID |
| `payment_status` | enum | `pending`, `paid`, `failed`, `refunded` |
| `paid_at` | timestamp | Payment completion time |
| `created_at` | timestamp | Record creation |
| `updated_at` | timestamp | Last update |

---

## 🎯 API Endpoints

### Public Routes (Authenticated Users)

| Method | Route | Description |
|--------|-------|-------------|
| GET | `/payment/create` | Show payment form |
| POST | `/payment/process` | Process payment & create PayMongo link |
| GET | `/payment/success?booking_id={id}` | Payment success page |
| GET/POST | `/payment/webhook` | PayMongo webhook handler |

---

## 🔐 PaymentController Methods

### `create()`
- Displays the payment form
- Returns: `resources/views/payment/pay.blade.php`

### `processPayment(Request $request)`
- Validates booking data
- Creates `BookingPayment` record with `pending` status
- Calls PayMongo API to create payment link
- Redirects user to PayMongo checkout

**Request Validation:**
```php
[
    'fullname' => 'required|string|max:255',
    'email' => 'required|email',
    'phone' => 'required|string|max:20',
    'mountain' => 'required|string|max:255',
    'amount' => 'required|integer|min:1',
    'hike_date' => 'required|date',
    'participants' => 'required|integer|min:1',
]
```

### `success(Request $request)`
- Shows payment confirmation
- Displays booking details
- Accepts `?booking_id={id}` parameter

### `webhook(Request $request)`
- Receives PayMongo payment events
- Updates payment status when `link.payment.paid` event received
- Logs all webhook activities

---

## 🛡️ BookingPayment Model Methods

### Helper Methods

```php
// Check payment status
$booking->isPaid()      // Returns true if status is 'paid'
$booking->isPending()   // Returns true if status is 'pending'

// Update payment status
$booking->markAsPaid($paymentId)  // Sets status to 'paid'
$booking->markAsFailed()          // Sets status to 'failed'
```

### Relationships
Currently standalone. **Future enhancement**: Link to `User` model.

---

## ⚙️ Configuration

### Environment Variables

```env
# PayMongo API Keys
PAYMONGO_PUBLIC_KEY=pk_test_...
PAYMONGO_SECRET_KEY=sk_test_...

# App Environment
APP_ENV=local              # Set to 'production' for live mode
```

### SSL Verification
- **Development**: SSL verification disabled (`CURLOPT_SSL_VERIFYPEER => false`)
- **Production**: SSL verification enabled automatically when `APP_ENV=production`

---

## 🧪 Testing Guide

### Test Payment Flow

1. **Fill out payment form:**
   ```
   Name: John Doe
   Email: john@example.com
   Phone: 09171234567
   Mountain: Mt. Pulag
   Amount: 1000
   Date: 2025-10-15
   Participants: 2
   ```

2. **Test Cards (PayMongo Test Mode):**
   ```
   Card Number: 4120 0000 0000 0007
   Expiry: Any future date
   CVC: Any 3 digits
   ```

3. **Check database:**
   ```bash
   php artisan tinker
   >>> BookingPayment::latest()->first()
   ```

### Test Webhook (Optional)

Use [Ngrok](https://ngrok.com) to expose your local server:
```bash
ngrok http 8000
```

Then configure webhook URL in PayMongo Dashboard:
```
https://your-ngrok-url.ngrok.io/payment/webhook
```

---

## 🔍 Logging & Debugging

All payment activities are logged:

```php
// View logs
tail -f storage/logs/laravel.log

// Logged events:
- PayMongo API responses
- Webhook payloads
- Payment status changes
- Errors and exceptions
```

---

## 🚨 Important Security Notes

### ⚠️ NEVER commit your `.env` file
```bash
# Already in .gitignore, but double check:
git check-ignore .env
```

### ⚠️ Use Environment Variables
```php
// ✅ GOOD
$secretKey = env('PAYMONGO_SECRET_KEY');

// ❌ BAD - Never hardcode
$secretKey = 'sk_test_ok5EFh3sAbFbSeaBWZeJdpKM';
```

### ⚠️ Production Checklist
- [ ] Enable SSL verification
- [ ] Use production PayMongo keys (starts with `sk_live_`)
- [ ] Set `APP_ENV=production`
- [ ] Configure proper webhook URL
- [ ] Enable HTTPS on your domain
- [ ] Test payment flow thoroughly

---

## 🔗 Integration with Existing Booking System

### Current State
You have **two separate booking systems**:

1. **`Booking` Model** (Trail/Event bookings)
   - Located: `app/Models/Booking.php`
   - Purpose: Trail bookings linked to users, trails, batches
   - Fields: `user_id`, `trail_id`, `batch_id`, `event_id`

2. **`BookingPayment` Model** (Payment records)
   - Located: `app/Models/BookingPayment.php`
   - Purpose: PayMongo payment transactions
   - Fields: `fullname`, `email`, `amount`, `payment_status`

### 🔮 Recommended Future Enhancement

**Link the two systems:**

```php
// Add to booking_payments migration:
$table->foreignId('booking_id')->nullable()->constrained()->onDelete('cascade');

// Add to BookingPayment model:
public function booking()
{
    return $this->belongsTo(Booking::class);
}

// Add to Booking model:
public function payment()
{
    return $this->hasOne(BookingPayment::class);
}
```

Then modify `BookingController@store` to create payment after booking:
```php
// After booking is created:
$booking = Booking::create([...]);

// Redirect to payment:
return redirect()->route('payment.create', [
    'booking_id' => $booking->id,
    'amount' => $booking->trail->price,
    'mountain' => $booking->trail->name,
    // ... pre-fill payment form
]);
```

---

## 📱 API Integration (For Mobile Apps)

If you need API endpoints for mobile apps:

```php
// Add to routes/api.php:
Route::post('/api/v1/payment/create', [PaymentController::class, 'apiCreate']);
Route::post('/api/v1/payment/verify', [PaymentController::class, 'apiVerify']);

// Returns JSON instead of redirects
```

---

## 🐛 Common Issues & Solutions

### Issue: "Payment link not created"
**Solution:** Check logs for API response, verify API keys

### Issue: "Webhook not receiving events"
**Solution:** 
1. Ensure webhook URL is publicly accessible (use Ngrok for local)
2. Check PayMongo dashboard → Webhooks → Event logs

### Issue: "Payment status not updating"
**Solution:** Verify webhook is configured and receiving `link.payment.paid` events

### Issue: "cURL SSL Error"
**Solution:** For local dev, SSL verification is disabled. For production, ensure valid SSL certificate.

---

## 📚 Resources

- [PayMongo Documentation](https://developers.paymongo.com/)
- [PayMongo API Reference](https://developers.paymongo.com/reference)
- [PayMongo Test Cards](https://developers.paymongo.com/docs/accepting-test-payments)
- [PayMongo Webhooks Guide](https://developers.paymongo.com/docs/webhooks)

---

## ✅ Next Steps

1. ✅ Run migration: `php artisan migrate`
2. ✅ Add PayMongo keys to `.env`
3. ✅ Test payment flow
4. 🔜 Link `BookingPayment` with `Booking` model
5. 🔜 Add email notifications after payment
6. 🔜 Create admin dashboard to view payments
7. 🔜 Add payment history for users
8. 🔜 Implement refund functionality

---

## 🎉 You're All Set!

Your payment system is ready to use. Visit `/payment/create` to start accepting payments!

**Questions?** Check the logs or PayMongo dashboard for debugging.

# 🎯 Payment System - Quick Reference Card

## 🔗 URLs

| Purpose | URL | Method |
|---------|-----|--------|
| Payment Form | `/payment/create` | GET |
| Process Payment | `/payment/process` | POST |
| Success Page | `/payment/success?booking_id=123` | GET |
| Webhook | `/payment/webhook` | POST |

## 📋 Payment Form Fields

```
✓ Full Name (required)
✓ Email (required)
✓ Phone (required)
✓ Mountain/Trail (required)
✓ Hike Date (required)
✓ Participants (required, min: 1)
✓ Amount in ₱ (required, min: 1)
```

## 💳 Test Card

```
Card Number: 4120 0000 0000 0007
Expiry:      12/25 (any future date)
CVC:         123 (any 3 digits)
Name:        Any name
```

## 🔄 Payment Status Flow

```
pending → (user pays) → paid
   ↓                      ↓
failed                 refunded
```

## 📊 BookingPayment Model

```php
// Check status
$payment->isPaid()      // true/false
$payment->isPending()   // true/false

// Update status
$payment->markAsPaid($paymentId)
$payment->markAsFailed()
```

## 🛠️ Quick Commands

```bash
# Run migration
php artisan migrate

# Check recent payments
php artisan tinker
>>> BookingPayment::latest()->first()

# View logs
tail -f storage/logs/laravel.log
```

## ⚙️ Environment Variables

```env
PAYMONGO_PUBLIC_KEY=pk_test_...
PAYMONGO_SECRET_KEY=sk_test_...
```

Get keys from: https://dashboard.paymongo.com

## 🎨 Views

```
resources/views/payment/
├── pay.blade.php      ← Form
└── success.blade.php  ← Confirmation
```

## 📱 Payment Flow Diagram

```
┌─────────────┐
│   User      │
│ Visits Page │
└──────┬──────┘
       │
       ▼
┌─────────────────┐
│  Payment Form   │ /payment/create
│  (pay.blade)    │
└──────┬──────────┘
       │ (submits form)
       ▼
┌─────────────────┐
│PaymentController│ /payment/process
│ processPayment()│
└──────┬──────────┘
       │
       ├─→ Create BookingPayment (status: pending)
       ├─→ Call PayMongo API
       └─→ Get checkout URL
       │
       ▼
┌─────────────────┐
│   PayMongo      │
│  Checkout Page  │ (external)
└──────┬──────────┘
       │
       ├─→ User pays with card
       │
       ▼
┌─────────────────┐
│   PayMongo      │
│ Sends Webhook   │ /payment/webhook
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│PaymentController│
│   webhook()     │
└──────┬──────────┘
       │
       └─→ Update status to 'paid'
       │
       ▼
┌─────────────────┐
│  Success Page   │ /payment/success
│(success.blade)  │
└─────────────────┘
       │
       └─→ Show confirmation
```

## 🗄️ Database Schema

```sql
CREATE TABLE booking_payments (
    id BIGINT PRIMARY KEY,
    fullname VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(20),
    mountain VARCHAR(255),
    amount INTEGER,           -- in pesos
    hike_date DATE,
    participants INTEGER,
    paymongo_link_id VARCHAR(255),
    paymongo_payment_id VARCHAR(255),
    payment_status ENUM('pending','paid','failed','refunded'),
    paid_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## 🔐 Security Checklist

- ✅ API keys in `.env` (not hardcoded)
- ✅ `.env` in `.gitignore`
- ✅ SSL verification enabled in production
- ✅ Input validation on all fields
- ✅ Error logging without exposing secrets
- ⚠️ TODO: Add webhook signature verification
- ⚠️ TODO: Add rate limiting on payment endpoints

## 🐛 Troubleshooting

### Payment link not created?
→ Check `storage/logs/laravel.log` for API errors
→ Verify API keys in `.env`

### Webhook not working?
→ Use Ngrok for local testing
→ Check PayMongo dashboard → Webhooks

### Status not updating?
→ Ensure webhook URL is configured
→ Check webhook logs in PayMongo dashboard

## 📞 Support Resources

- 📖 Full Docs: `PAYMENT_SYSTEM_DOCUMENTATION.md`
- 🔍 Review: `PAYMENT_SYSTEM_REVIEW.md`
- 💬 PayMongo: https://developers.paymongo.com
- 🎫 Support: support@paymongo.com

---

**Status**: ✅ Ready to Use!
**Last Updated**: October 2, 2025

# 💳 Payment Testing Quick Reference

## 🎯 **TL;DR - What to Use:**

### ✅ **For Card Payments:**
```
Card: 4343 4343 4343 4345
Exp:  12/25
CVC:  123
Name: Test User
```

### ✅ **For GCash Payments:**
```
Phone: 09123456789
OTP:   123456 (any 6 digits)
```

### ✅ **If Payment Fails:**
```
Manual test: http://localhost:8000/test/confirm-payment/{id}
```

---

## 🚨 **Common Issues & Instant Fixes:**

| Problem | Quick Fix |
|---------|-----------|
| "Step 4 keeps reloading" | Use card: `4343 4343 4343 4345` |
| "GCash OTP not received" | Enter any 6-digit code: `123456` |
| "Payment stuck pending" | Visit: `/test/confirm-payment/{payment_id}` |
| "Slots not deducting" | Webhooks disabled - use manual confirmation |
| "Amount too high error" | Test mode limit is ₱100,000 max |

---

## 📱 **Payment Methods Supported:**

| Method | Test Mode Status | Notes |
|--------|-----------------|-------|
| 💳 **Credit/Debit Card** | ✅ Reliable | Use `4343 4343 4343 4345` |
| 📱 **GCash** | ⚠️ Unreliable | Now fixed with billing info |
| 🚗 **GrabPay** | ⚠️ Untested | Should work with billing info |
| 💰 **PayMaya** | ⚠️ Untested | Should work with billing info |

---

## 🔧 **Dev Commands:**

### Check payment status:
```bash
php artisan tinker --execute="echo \App\Models\BookingPayment::find(4)->payment_status"
```

### Check slots reserved:
```bash
php artisan tinker --execute="echo \App\Models\Batch::find(47)->slots_taken"
```

### View recent payments:
```bash
Get-Content storage/logs/laravel.log -Tail 50 | Select-String "PayMongo"
```

### Manually confirm payment:
```bash
# Browser:
http://localhost:8000/test/confirm-payment/4

# PowerShell:
Invoke-WebRequest -Uri "http://localhost:8000/test/confirm-payment/4"
```

---

## 📋 **Pre-Production Checklist:**

- [ ] Remove test endpoint from `routes/web.php`
- [ ] Setup ngrok/expose for webhooks
- [ ] Configure PayMongo webhook URL
- [ ] Switch to production API keys
- [ ] Test with real GCash account
- [ ] Monitor webhook success rate
- [ ] Setup error alerting

---

## 🎓 **What Changed Today:**

1. ✅ Added payment-booking integration
2. ✅ Fixed slot deduction (party_size vs booking count)
3. ✅ Changed webhook from GET → POST
4. ✅ **Added billing info for GCash support** 🆕
5. ✅ Created manual test endpoint
6. ✅ Updated webhook to parse remarks field

---

**Last Updated:** October 2, 2025  
**Payment System Status:** ✅ Operational  
**Known Issue:** GCash test mode occasionally unreliable (use card or manual confirm)

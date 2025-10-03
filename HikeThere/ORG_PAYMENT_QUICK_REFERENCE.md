# Organization Booking & Payment - Quick Reference

## 🚀 Quick Access

### For Organizations:
- **View Bookings**: Main Menu → Bookings
- **Payment Setup**: Profile Dropdown → Payment Setup

## 📊 Booking Management

### Bookings Dashboard
- **URL**: `/org/bookings`
- **Features**:
  - Total bookings count
  - Total revenue (₱)
  - Paid bookings count
  - Pending bookings count
  - Full booking list with payment status

### Booking Details
- **URL**: `/org/bookings/{id}`
- **View**: 
  - Hiker details
  - Trail information
  - Payment amount & status
  - Payment ID and timestamp
- **Actions**: Update booking status

## 💳 Payment Setup

### Access
- **URL**: `/org/payment`
- **Who**: Organizations only (after approval)

### PayMongo Configuration
1. Go to [PayMongo Dashboard](https://dashboard.paymongo.com/)
2. Navigate to: Developers → API Keys
3. Copy Secret Key (starts with `sk_test_` or `sk_live_`)
4. Copy Public Key (starts with `pk_test_` or `pk_live_`)
5. Paste in Payment Setup form
6. Click "Save Configuration"
7. Test connection

### Xendit
- Currently using hardcoded credentials
- Configuration UI prepared for future use

## 🔐 Security

- ✅ All credentials encrypted
- ✅ Organization-only access
- ✅ Approval required
- ✅ Secure password fields
- ✅ Clear credentials option

## 📋 Key Routes

```
Bookings:
GET    /org/bookings              - List bookings
GET    /org/bookings/{id}         - Booking details
PATCH  /org/bookings/{id}/status  - Update status

Payment:
GET    /org/payment               - Setup page
PUT    /org/payment               - Update credentials
POST   /org/payment/test          - Test connection
DELETE /org/payment/clear         - Clear credentials
```

## 🎯 Payment Status Indicators

- 🟢 **Paid** - Payment received
- 🟡 **Pending** - Payment processing
- 🔴 **Failed** - Payment failed
- ⚫ **No Payment** - No payment record

## 📱 Mobile Support

All features available in responsive mobile menu:
- Bookings management
- Payment setup
- Booking status updates

## ⚡ Quick Actions

### Test Payment Gateway
```javascript
// Available in Payment Setup page
// Click "Test Connection" button
// Returns success/failure message
```

### Clear Credentials
```
Payment Setup → Clear Credentials button
Confirmation required
```

### Update Booking Status
```
1. Go to booking details
2. Select new status from dropdown
3. Click "Update"
```

## 🔄 Workflow

```
Hiker Books Trail
      ↓
Hiker Makes Payment (using org credentials)
      ↓
Org Views Booking (with payment info)
      ↓
Org Updates Status (confirm/complete)
      ↓
Money in Org's Account ✅
```

## 📊 Statistics Calculated

- **Total Bookings**: All bookings for org's trails
- **Total Revenue**: Sum of all paid bookings (price_cents)
- **Paid Bookings**: Count of bookings with paid status
- **Pending**: Count of pending status bookings

## 🛠️ Troubleshooting

**Payment setup not saving?**
- Check credentials format
- Ensure keys start with correct prefix
- Verify organization is approved

**Can't see bookings?**
- Ensure you have trails created
- Check organization approval status
- Verify trail ownership

**Test connection failing?**
- Double-check API keys
- Ensure using test keys in development
- Check PayMongo dashboard status

## 📝 Notes

- Each organization has their own credentials
- Payments go directly to organization's account
- All payment data is tracked per booking
- Booking status independent from payment status
- Credentials can be updated anytime
- Multiple trails = one payment account

## 🎨 UI Colors

- Primary: `#336d66` (Teal green)
- Success: Green
- Warning: Yellow
- Error: Red
- Info: Blue

## 📞 Support

Need help? Check:
1. Payment Setup → Help Section
2. ORG_BOOKING_PAYMENT_SYSTEM.md (full docs)
3. PayMongo Documentation

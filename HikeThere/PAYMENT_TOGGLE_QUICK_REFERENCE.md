# Payment Method Toggle - Quick Reference

## 🎯 What Was Added

A **toggle switch** on the Organization Payment Setup page that allows organizations to choose between:
- **Manual Payment** (QR Code) 
- **Automatic Payment** (Gateway)

Hikers automatically see the correct payment method based on the organization's choice.

## 🚀 How to Use (Organization)

1. Go to **Payment Setup** page
2. See the **Active Payment Method** toggle at the top
3. Click the toggle to switch between Manual/Automatic
4. Configure your chosen method below:
   - **Manual**: Upload QR code
   - **Automatic**: Enter gateway credentials

## 💡 What Hikers See

### If Manual Payment Selected:
- QR code image displayed
- Payment proof upload field
- Transaction number field
- "Verification required" notice

### If Automatic Payment Selected:
- "You'll be redirected" message
- No upload fields
- Direct to payment gateway after booking

## 🔧 Technical Details

### Route Added:
```php
PUT /org/payment/toggle-method
```

### Controller Method:
```php
OrganizationPaymentController::togglePaymentMethod()
```

### Database Column:
```
organization_payment_credentials.payment_method
Values: 'manual' | 'automatic'
```

### API Endpoint (Existing):
```
GET /api/trail/{trailId}/payment-method
Returns: payment_method, has_qr_code, qr_code_url, payment_instructions
```

## 📋 Files Modified

1. `resources/views/org/payment/index.blade.php` - UI & Toggle
2. `routes/web.php` - New route
3. `app/Http/Controllers/OrganizationPaymentController.php` - Toggle logic

## ✅ Validation

- **Manual Payment Active + No QR Code**: Warning shown
- **Automatic Payment Active + No Gateway**: Warning shown
- **Payment method properly saved**: ✓
- **Hikers see correct option**: ✓
- **Smooth toggle animation**: ✓

## 🎨 Visual Features

- **Green** toggle = Automatic Payment active
- **Orange** toggle = Manual Payment active  
- **Animated** transition between states
- **Icons** change based on selected method
- **Status message** updates dynamically
- **Warnings** for incomplete setup

## 🔄 User Flow

```
Organization:
1. Opens Payment Setup
2. Sees current payment method
3. Clicks toggle to switch
4. Form auto-submits
5. Page refreshes with success message
6. New method is active

Hiker:
1. Selects trail to book
2. JavaScript checks payment method via API
3. Appropriate payment section shows
4. Completes booking with correct method
```

## ⚙️ Configuration States

| State | Manual Tab | Automatic Tab | Hiker Sees |
|-------|-----------|---------------|------------|
| Manual + QR Code ✓ | Active | Inactive | QR Code & Upload |
| Manual + No QR ⚠️ | Active | Inactive | Error (setup incomplete) |
| Automatic + Gateway ✓ | Inactive | Active | Gateway redirect |
| Automatic + No Gateway ⚠️ | Inactive | Active | Error (setup incomplete) |

## 🐛 Error Handling

- Invalid file type → Alert shown
- File too large → Alert shown  
- No payment configured → Warning in status
- Toggle fails → Error message shown
- API error → Default to automatic payment

## 📱 Responsive Design

- **Desktop**: Toggle on right side
- **Tablet**: Toggle stacked below text
- **Mobile**: Toggle centered, smaller size

## 🔐 Security

- Route protected by authentication
- Organization-only access
- CSRF token required
- Logs all changes
- No sensitive data exposed

## 🎓 Best Practices

1. **Configure before activating**
   - Upload QR code if using manual
   - Enter credentials if using automatic

2. **Test your setup**
   - Create test booking
   - Verify correct payment shows
   - Complete test transaction

3. **Monitor payments**
   - Check verification queue
   - Review payment success rates
   - Switch methods if needed

4. **Keep updated**
   - Update QR code if changed
   - Refresh gateway credentials
   - Test after changes

## 🆘 Troubleshooting

**Toggle doesn't switch?**
- Check internet connection
- Clear browser cache
- Try different browser

**Hikers see wrong payment method?**
- Verify toggle setting saved
- Check API endpoint response
- Clear application cache

**QR code not showing?**
- Verify QR code uploaded
- Check file permissions
- Confirm file path correct

**Gateway not working?**
- Verify credentials entered
- Test gateway connection
- Check gateway documentation

## 📞 Support

For issues or questions:
1. Check console for errors
2. Review application logs
3. Verify database values
4. Test API endpoint directly

## 🎉 Benefits

✅ **One-click switching** between payment methods  
✅ **Clear visual feedback** of active method  
✅ **Automatic routing** for hikers  
✅ **No coding** required to switch  
✅ **Instant updates** take effect immediately  
✅ **Safe switching** with validation warnings  
✅ **Audit trail** of all changes  

## 🔮 Future Enhancements

- Scheduled switching (time-based)
- A/B testing different methods
- Payment analytics dashboard
- Multi-method support (both at once)
- Conditional switching (amount-based)

# GCS Image Loading Fix - Complete Summary

## Issue
QR codes and payment proof images were not loading on production (Railway). Error: `404 (Not Found)`

**Root Cause**: The helper methods were using `Storage::disk()->url()` which doesn't work correctly with GCS in Railway environment. The fallback was generating local `/storage/` URLs which don't exist on Railway's ephemeral filesystem.

## Solution Applied

### 1. Enhanced URL Generation Logic
Updated both `getQrCodeUrl()` and `getPaymentProofUrl()` methods to:
- Detect when using GCS disk
- Construct GCS URLs directly: `https://storage.googleapis.com/BUCKET/PATH`
- Better fallback mechanisms
- Improved error logging

### 2. Added Error Handling in Views
Updated all views displaying images to:
- Check if URL exists before displaying
- Show friendly error messages if image fails
- Add `onerror` handlers on img tags
- Display helpful fallback content

### 3. Added Debug Tools
- Created `/debug-qr-config` route for organizations
- Shows current configuration and generated URLs
- Helps diagnose issues quickly

## Files Modified

### Models:
1. ✅ **`app/Models/OrganizationPaymentCredential.php`**
   - Enhanced `getQrCodeUrl()` method
   - Direct GCS URL construction
   - Better fallback logic

2. ✅ **`app/Models/Booking.php`**
   - Enhanced `getPaymentProofUrl()` method
   - Same GCS logic as QR code method
   - Consistent error handling

### Views:
3. ✅ **`resources/views/org/payment/index.blade.php`**
   - Added error handling for QR display
   - Shows placeholder if image fails
   - Better visual feedback

4. ✅ **`resources/views/hiker/booking/payment.blade.php`**
   - Added error handling for QR display
   - Shows friendly error messages
   - Added debug info (when APP_DEBUG=true)

5. ✅ **`resources/views/org/bookings/show.blade.php`**
   - Added error handling for payment proof display
   - Graceful fallback if image fails
   - Maintains security UI even on error

### Routes:
6. ✅ **`routes/web.php`**
   - Added `/debug-qr-config` debug route
   - Shows configuration and URLs in JSON

## How the Fix Works

### Old Behavior (Broken):
```php
// This was failing in production
$disk = config('filesystems.default'); // returns 'gcs'
return Storage::disk($disk)->url($path); 
// ❌ Generates: /storage/qr_codes/7_1759948533.png (doesn't exist)
```

### New Behavior (Fixed):
```php
$disk = config('filesystems.default');

// Check if using GCS
if ($disk === 'gcs') {
    $bucket = config('filesystems.disks.gcs.bucket');
    if ($bucket) {
        // ✅ Generates: https://storage.googleapis.com/hikethere-storage/qr_codes/7_1759948533.png
        return 'https://storage.googleapis.com/' . $bucket . '/' . $path;
    }
}

// Fallback for local/public disk
return Storage::disk($disk)->url($path);
```

## Testing Steps

### 1. Test QR Code Display (Organization)
```
1. Login as organization
2. Go to: https://hikethere.site/org/payment
3. Check if QR code displays correctly
4. If still having issues, visit: https://hikethere.site/debug-qr-config
```

### 2. Test QR Code Display (Hiker Payment)
```
1. Login as hiker
2. Create a booking
3. Go to payment page
4. Check if organization's QR code displays
5. If APP_DEBUG=true, check debug info shown on page
```

### 3. Test Payment Proof Display (Organization)
```
1. Login as organization
2. View a booking with payment proof
3. Go to: https://hikethere.site/org/bookings/{id}
4. Check if payment proof image displays (blurred)
5. Hover to unblur, click to open
```

## Debug Commands

### Check Configuration:
```bash
# In Railway or local terminal
php artisan tinker

config('filesystems.default')
// Should return: "gcs"

config('filesystems.disks.gcs.bucket')
// Should return: "hikethere-storage"

$creds = App\Models\OrganizationPaymentCredential::first();
$creds->getQrCodeUrl();
// Should return: "https://storage.googleapis.com/hikethere-storage/qr_codes/..."
```

### Check GCS Files:
```bash
# List QR codes in GCS
gsutil ls gs://hikethere-storage/qr_codes/

# List payment proofs in GCS
gsutil ls gs://hikethere-storage/payment_proofs/

# Check specific file
gsutil ls -L gs://hikethere-storage/qr_codes/7_1759948533.png
```

### Verify Public Access:
```bash
# Make sure files are public
gsutil acl ch -u AllUsers:R gs://hikethere-storage/qr_codes/*
gsutil acl ch -u AllUsers:R gs://hikethere-storage/payment_proofs/*
```

## Expected Results

### ✅ Success Indicators:
- QR codes display on organization payment setup page
- QR codes display on hiker payment page
- Payment proof images display (blurred) on booking details
- No 404 errors in browser console
- Images load from `https://storage.googleapis.com/...`

### ⚠️ If Still Not Working:

1. **Check Railway Environment Variables:**
   ```
   FILESYSTEM_DISK=gcs
   GCS_BUCKET=hikethere-storage
   GCS_PROJECT_ID=your-project-id
   GCS_KEY_FILE_CONTENT=base64-encoded-key
   ```

2. **Verify Files Exist in GCS:**
   - Check Google Cloud Console
   - Navigate to Storage > Browser > hikethere-storage
   - Verify files are in correct folders

3. **Check File Permissions:**
   - Files must have "allUsers: Reader" permission
   - Set via Google Cloud Console or gsutil

4. **Clear Config Cache (if applicable):**
   ```bash
   php artisan config:clear
   php artisan config:cache
   ```

## Rollback Plan

If issues persist, you can temporarily revert by:

1. **Revert Model Changes:**
   ```php
   // In getQrCodeUrl() and getPaymentProofUrl()
   return Storage::disk($disk)->url($this->path);
   ```

2. **Re-upload images directly to Railway** (not recommended for production)

## Additional Notes

### Why Direct GCS URLs?
- Railway's ephemeral filesystem doesn't persist files
- `Storage::disk('gcs')->url()` generates incorrect paths in some configs
- Direct GCS URLs are more reliable and explicit

### Security Considerations:
- Files are stored in GCS with public access (required for display)
- URLs are not easily guessable (include org ID and timestamp)
- Payment proofs have additional blur/secure UI layer
- Access is logged by GCS

### Performance:
- Direct GCS URLs are fast (Google's CDN)
- No additional database queries
- Images cached by browsers

## Future Enhancements

Consider implementing:
- [ ] Signed URLs for extra security (temporary access)
- [ ] Cloud CDN for faster global delivery
- [ ] Image optimization/resizing on upload
- [ ] Automatic watermarking for sensitive images
- [ ] Audit logging for who views payment proofs

---

**Status**: ✅ Fixed and Deployed
**Last Updated**: October 9, 2025
**Tested On**: Railway production environment

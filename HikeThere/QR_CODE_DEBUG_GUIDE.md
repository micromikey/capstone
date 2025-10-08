# QR Code Not Displaying - Debugging Guide

## Issue
QR code uploaded by organization is not showing on the Payment Setup page.

## What We Fixed

### 1. Updated `OrganizationPaymentCredential.php` Model
**Location**: `app/Models/OrganizationPaymentCredential.php`

**Changes Made**:
- Enhanced `getQrCodeUrl()` method to handle GCS properly
- Added direct GCS URL construction when using GCS disk
- Added better fallback mechanisms
- Improved error logging

**New Logic**:
```php
1. Check if qr_code_path exists
2. If using GCS disk, construct URL directly: https://storage.googleapis.com/BUCKET/PATH
3. Otherwise use Storage::disk()->url()
4. If error, try GCS fallback
5. Final fallback to asset() helper
```

### 2. Updated `index.blade.php` View
**Location**: `resources/views/org/payment/index.blade.php`

**Changes Made**:
- Added error handling with `onerror` attribute on img tag
- Shows placeholder SVG if image fails to load
- Displays filename below QR code
- Shows warning if URL generation fails
- Better error messages for debugging

### 3. Added Debug Route
**Location**: `routes/web.php`

**New Route**: `/debug-qr-config`

**Purpose**: Check QR code configuration in real-time

## How to Debug

### Step 1: Check QR Code Configuration
1. Login as an organization user
2. Go to: `https://hikethere.site/debug-qr-config`
3. You should see JSON with:
   ```json
   {
     "qr_code_path": "qr_codes/10_1759946852.jpg",
     "qr_code_url": "https://storage.googleapis.com/hikethere-storage/qr_codes/10_1759946852.jpg",
     "filesystem_disk": "gcs",
     "gcs_bucket": "hikethere-storage",
     "is_gcs": true,
     "has_qr_code": true,
     "expected_gcs_url": "https://storage.googleapis.com/hikethere-storage/qr_codes/10_1759946852.jpg"
   }
   ```

### Step 2: Verify the URL Directly
1. Copy the `qr_code_url` from the debug output
2. Open it in a new browser tab
3. **If it loads**: Problem is in the page rendering
4. **If 404 error**: File not in GCS or wrong permissions

### Step 3: Check GCS Permissions
If you get 404, verify in Google Cloud Console:

1. Go to your GCS bucket: `hikethere-storage`
2. Navigate to `qr_codes/` folder
3. Find the file (e.g., `10_1759946852.jpg`)
4. Check permissions:
   - Should have "Public access"
   - Should show "Access granted to public prin"

**If not public**, make it public:
```bash
gsutil acl ch -u AllUsers:R gs://hikethere-storage/qr_codes/10_1759946852.jpg
```

Or use the Google Cloud Console:
1. Select the file
2. Click "Permissions" tab
3. Click "Add entry"
4. Entity: `allUsers`
5. Access: `Reader`
6. Save

### Step 4: Test on Payment Page
1. Go to: `https://hikethere.site/org/payment`
2. Refresh the page (Ctrl+F5 to clear cache)
3. Look for the QR code section

**Expected Behavior**:
- If QR exists: Shows image with filename below
- If loading fails: Shows placeholder with "Image Not Found"
- If no QR uploaded: Shows upload area only

### Step 5: Check Browser Console
1. Open DevTools (F12)
2. Go to Console tab
3. Look for errors related to image loading
4. Check Network tab for the QR image request
5. Verify the URL being requested matches GCS URL

## Common Issues & Solutions

### Issue 1: Image shows "Image Not Found"
**Cause**: File exists in database but not in GCS
**Solution**: Re-upload the QR code

### Issue 2: 403 Forbidden Error
**Cause**: GCS file not public
**Solution**: Set public permissions (see Step 3 above)

### Issue 3: 404 Not Found
**Cause**: File doesn't exist in GCS
**Solution**: 
1. Check if file is in correct folder: `qr_codes/`
2. Verify filename matches `qr_code_path` in database
3. Re-upload if needed

### Issue 4: CORS Error
**Cause**: GCS bucket needs CORS configuration
**Solution**:
```bash
# Create cors.json file:
[
  {
    "origin": ["https://hikethere.site", "https://*.railway.app"],
    "method": ["GET", "HEAD"],
    "responseHeader": ["Content-Type"],
    "maxAgeSeconds": 3600
  }
]

# Apply CORS:
gsutil cors set cors.json gs://hikethere-storage
```

### Issue 5: Image loads locally but not in production
**Cause**: Different filesystem configurations
**Solution**: 
1. Verify Railway env vars:
   - `FILESYSTEM_DISK=gcs`
   - `GCS_BUCKET=hikethere-storage`
   - `GCS_PROJECT_ID=your-project`
   - `GCS_KEY_FILE_CONTENT=base64-encoded-key`
2. Run `php artisan config:cache` in Railway

## Quick Tests

### Test 1: Direct GCS URL
```
https://storage.googleapis.com/hikethere-storage/qr_codes/YOUR_FILE.jpg
```
Should load the image directly in browser.

### Test 2: Check Database
```sql
SELECT id, user_id, qr_code_path, payment_method 
FROM organization_payment_credentials 
WHERE user_id = YOUR_ORG_ID;
```

### Test 3: Laravel Tinker
```php
php artisan tinker

$creds = App\Models\OrganizationPaymentCredential::find(1);
$creds->qr_code_path;
$creds->getQrCodeUrl();
```

## Expected Results After Fix

1. **Debug Route Shows**: Correct GCS URL with bucket name
2. **Image Loads**: QR code displays on payment setup page
3. **No Console Errors**: Browser console is clean
4. **Fallback Works**: If image fails, shows friendly error message
5. **Network Tab**: Shows successful 200 response for image

## Files Modified

- ✅ `app/Models/OrganizationPaymentCredential.php` - Better URL generation
- ✅ `resources/views/org/payment/index.blade.php` - Error handling
- ✅ `routes/web.php` - Debug route added

## Next Steps

1. Refresh the payment setup page
2. Visit `/debug-qr-config` to check configuration
3. If URL is correct, verify GCS permissions
4. If still not working, check browser console for specific errors
5. Contact support with debug output

---

**Last Updated**: October 9, 2025
**Status**: Debugging enhancements applied

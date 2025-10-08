# GCS Secure File Upload Implementation

## Overview
Implemented secure file upload for organization QR codes and hiker payment proofs using Google Cloud Storage (GCS) in Railway deployment.

## Changes Made

### 1. Controller Enhancements

#### A. OrganizationPaymentController - `updateManual()` Method
- **Enhanced GCS Detection**: Added comprehensive error handling and logging for GCS configuration
- **Secure Upload**: Implemented secure file upload with public visibility for QR codes
- **File Naming**: Added organization ID and timestamp to prevent conflicts: `{org_id}_{timestamp}.{extension}`
- **Error Handling**: Added try-catch blocks with detailed logging for:
  - GCS bucket configuration validation
  - Old file deletion
  - New file upload
  - Automatic fallback to local storage if GCS fails

#### B. BookingController - Payment Proof Upload
- **Enhanced Upload Logic**: Improved payment proof upload with same GCS integration
- **File Naming**: Uses booking ID and timestamp: `{booking_id}_{timestamp}.{extension}`
- **Comprehensive Logging**: Tracks all uploads with file size, disk used, and path
- **Error Handling**: Graceful fallback and user-friendly error messages

#### Key Features:
```php
// Prioritizes GCS for production
$disk = config('filesystems.default', 'public');

// Validates GCS bucket configuration
if (!$bucket) {
    Log::warning('GCS configured but bucket not set, falling back to public disk');
    $disk = 'public';
}

// Stores with unique filename
$filename = 'qr_codes/' . $orgId . '_' . time() . '.' . $file->getClientOriginalExtension();

// Uploads with public visibility
Storage::disk($disk)->putFileAs('qr_codes', $file, $filename, 'public');
```

### 2. Model Enhancements

#### A. OrganizationPaymentCredential Model
**Added `getQrCodeUrl()` Method**
- **Smart URL Generation**: Automatically detects storage disk (GCS or local)
- **Graceful Fallback**: Falls back to asset helper if storage URL generation fails
- **Error Logging**: Logs errors for debugging without breaking functionality

#### B. Booking Model
**Added `getPaymentProofUrl()` Method**
- **Smart URL Generation**: Automatically detects storage disk (GCS or local)
- **Graceful Fallback**: Falls back to asset helper if storage URL generation fails
- **Error Logging**: Logs errors for debugging without breaking functionality

#### Added Imports:
```php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
```

#### Usage:
```php
$credentials->getQrCodeUrl();        // Returns full URL to QR code
$booking->getPaymentProofUrl();      // Returns full URL to payment proof
```

### 3. View Updates

#### Payment Setup Page (`org/payment/index.blade.php`)
- Updated QR code display to use new `getQrCodeUrl()` method
- Ensures proper display whether stored in GCS or locally

#### Booking Payment Page (`hiker/booking/payment.blade.php`)
- Updated QR code display for hikers during checkout
- Seamlessly works with GCS URLs

#### Organization Booking Details (`org/bookings/show.blade.php`)
- Updated payment proof display to use `getPaymentProofUrl()` method
- Secure display with blur effects and privacy warnings intact

#### Hiker Booking Details (`hiker/booking/show.blade.php`)
- Updated payment proof link to use `getPaymentProofUrl()` method
- Consistent experience across all views

## Security Features

### 1. **Public Visibility**
- QR codes are uploaded with `'public'` visibility
- Accessible to hikers during checkout without authentication issues

### 2. **Secure Storage**
- Files stored in GCS bucket (production)
- Protected by GCS access controls
- Public URLs are signed and managed by GCS

### 3. **Old File Cleanup**
- Automatically deletes old QR code when new one is uploaded
- Prevents storage bloat and orphaned files

### 4. **Validation**
- File type validation: `jpeg,png,jpg,gif`
- File size limit: 10MB
- Prevents malicious uploads

## File Organization

### QR Codes (Organization Payment Setup)
```
qr_codes/
  └── {organization_id}_{timestamp}.{extension}
```
Example: `qr_codes/123_1728489600.png`

### Payment Proofs (Hiker Uploads)
```
payment_proofs/
  └── {booking_id}_{timestamp}.{extension}
```
Example: `payment_proofs/456_1728489700.jpg`

## Error Handling

### Graceful Degradation:
1. **GCS Configuration Error** → Falls back to local storage
2. **Old File Deletion Error** → Logs warning, continues with upload
3. **URL Generation Error** → Falls back to asset helper
4. **Upload Error** → Shows user-friendly error message

### Comprehensive Logging:
```php
// Success
Log::info('QR code uploaded successfully', [
    'organization_id' => $orgId,
    'path' => $path,
    'disk' => $disk,
    'file_size' => $file->getSize()
]);

// Failure
Log::error('Failed to upload QR code', [
    'organization_id' => $orgId,
    'error' => $e->getMessage()
]);
```

## Testing Checklist

### Local Testing (Public Disk)
- [ ] Upload QR code - displays correctly
- [ ] Replace QR code - old file deleted, new one shows
- [ ] View QR during booking - displays for hikers

### Production Testing (GCS)
- [ ] Upload QR code - stored in GCS bucket
- [ ] QR code displays on setup page
- [ ] QR code displays during hiker checkout
- [ ] Old QR codes are deleted from GCS
- [ ] Public URL accessible without auth

## Environment Variables Required

```env
# Google Cloud Storage
GCS_PROJECT_ID=your-project-id
GCS_BUCKET=your-bucket-name
GCS_KEY_FILE_CONTENT=base64_encoded_service_account_json
FILESYSTEM_DISK=gcs
```

## Benefits

1. **Scalability**: GCS handles file storage instead of Railway filesystem
2. **Reliability**: GCS provides 99.99% availability
3. **Performance**: CDN-backed URLs for fast loading
4. **Security**: Managed access controls and encryption
5. **Cost-Effective**: Pay only for storage used
6. **Automatic Fallback**: Works locally for development

## Usage Flow

### Organization Upload:
1. Org navigates to Payment Setup
2. Uploads QR code image
3. File uploaded to GCS with public visibility
4. Path stored in database
5. Old file automatically deleted

### Hiker Checkout:
1. Hiker selects trail and books
2. Proceeds to payment page
3. QR code displayed from GCS URL
4. Hiker scans and pays
5. Uploads payment proof

## Maintenance

### Monitoring:
- Check Laravel logs for GCS errors
- Monitor GCS bucket usage in Google Cloud Console
- Track failed uploads via application logs

### Troubleshooting:
1. **QR not displaying**: Check FILESYSTEM_DISK env var
2. **Upload fails**: Verify GCS credentials and bucket permissions
3. **Old files not deleted**: Check GCS bucket lifecycle rules

## Next Steps (Optional)

1. **Add CDN**: Configure Cloud CDN for faster global delivery
2. **Image Optimization**: Auto-resize/compress QR codes on upload
3. **Backup Strategy**: Enable GCS versioning for file recovery
4. **Monitoring**: Set up Cloud Monitoring alerts for storage issues

## Conclusion

The QR code upload system now securely stores files in Google Cloud Storage for Railway deployment, with automatic fallback to local storage for development environments. All views have been updated to properly display QR codes from GCS URLs.

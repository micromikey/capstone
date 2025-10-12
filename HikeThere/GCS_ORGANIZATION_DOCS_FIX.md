# Fix for 404 Error on Organization Registration Documents in Admin Email

## Problem
When viewing submitted documents from organization registration in the admin email, the links show **404 Not Found** errors. This is because the email templates were using local storage URLs (`url('storage/' . $path)`) instead of GCS URLs.

## Root Cause
1. The application is using **Google Cloud Storage (GCS)** for file storage
2. The admin email templates were generating URLs using `url('storage/' . $path)` which creates local file system paths
3. Since files are stored in GCS, these local paths don't exist and return 404 errors

## Files Fixed

### 1. **Email Template** (`resources/views/emails/organization-approval-admin.blade.php`)
**Changed from:**
```php
<a href="{{ url('storage/' . $organizationProfile->business_permit_path) }}" ...>
<a href="{{ url('storage/' . $organizationProfile->government_id_path) }}" ...>
<a href="{{ url('storage/' . $docPath) }}" ...>
```

**Changed to:**
```php
<a href="{{ \App\Helpers\StorageHelper::url($organizationProfile->business_permit_path) }}" ...>
<a href="{{ \App\Helpers\StorageHelper::url($organizationProfile->government_id_path) }}" ...>
<a href="{{ \App\Helpers\StorageHelper::url($docPath) }}" ...>
```

### 2. **StorageHelper** (`app/Helpers/StorageHelper.php`)
Updated to properly handle GCS URLs using the Storage facade's `url()` method with fallback to manual URL construction.

## Configuration Required

### Step 1: Verify FILESYSTEM_DISK Setting
Check your `.env` file and ensure it's set to use GCS:

```env
# Change this line:
FILESYSTEM_DISK=local

# To this:
FILESYSTEM_DISK=gcs
```

### Step 2: Verify GCS Configuration
Make sure your `.env` has the following GCS settings:

```env
# Google Cloud Storage
GCS_PROJECT_ID=your-project-id
GCS_BUCKET=your-bucket-name
GCS_KEY_FILE_CONTENT=your-base64-encoded-key-file
# OR
GCS_KEY_FILE=/path/to/service-account-key.json
```

### Step 3: Verify GCS Bucket Permissions
Ensure your GCS bucket allows public read access for uploaded documents:

1. Go to Google Cloud Console → Storage → Browser
2. Select your bucket
3. Click on "Permissions" tab
4. Add a new member:
   - **New members:** `allUsers`
   - **Role:** `Storage Object Viewer`
5. Click "Save"

**OR** configure CORS if you prefer authenticated access:

```json
[
  {
    "origin": ["https://your-domain.com"],
    "method": ["GET"],
    "responseHeader": ["Content-Type"],
    "maxAgeSeconds": 3600
  }
]
```

### Step 4: Test the Fix

#### Option A: Register a New Organization
1. Go to your registration page
2. Create a new organization account
3. Upload business permit and government ID
4. Check the admin email received
5. Click on the document links - they should now open correctly

#### Option B: Test with Existing Organization
If you already have a test organization registered, trigger a new approval email:

**Using Tinker:**
```bash
php artisan tinker
```

Then run:
```php
$user = User::where('user_type', 'organization')->first();
Mail::to('admin@hikethere.com')->send(new \App\Mail\OrganizationApprovalNotification($user));
```

Check the email and verify document links work.

## How It Works Now

1. **File Upload:** When an organization registers, files are stored in GCS at path like:
   ```
   organization_documents/xyz123.pdf
   ```

2. **URL Generation:** The `StorageHelper::url()` method:
   - Detects the storage disk (`gcs` from `FILESYSTEM_DISK`)
   - Uses Laravel's Storage facade to generate the proper GCS URL
   - Falls back to manual construction if needed
   - Returns URL like: `https://storage.googleapis.com/your-bucket/organization_documents/xyz123.pdf`

3. **Email Links:** The admin email now displays clickable links that point to the actual GCS URLs

## Expected URL Format

The document URLs in the email should look like:
```
https://storage.googleapis.com/your-bucket-name/organization_documents/filename.ext
```

## Troubleshooting

### Documents Still Show 404

**1. Check if files were uploaded to GCS:**
```bash
php artisan tinker
```
```php
use Illuminate\Support\Facades\Storage;
Storage::disk('gcs')->files('organization_documents');
```

**2. Check if the path is correct:**
```php
$user = User::where('user_type', 'organization')->first();
$profile = $user->organizationProfile;
echo $profile->business_permit_path;
echo "\n";
echo \App\Helpers\StorageHelper::url($profile->business_permit_path);
```

**3. Verify GCS bucket is public or CORS is configured**

**4. Check Laravel logs for errors:**
```bash
tail -f storage/logs/laravel.log
```

### Files Were Uploaded Before Switching to GCS

If documents were uploaded when `FILESYSTEM_DISK=local`, they need to be migrated to GCS:

**Migration Script:**
```php
// Run in tinker: php artisan tinker
use App\Models\OrganizationProfile;
use Illuminate\Support\Facades\Storage;

$profiles = OrganizationProfile::all();

foreach ($profiles as $profile) {
    // Migrate business permit
    if ($profile->business_permit_path) {
        $localPath = storage_path('app/public/' . $profile->business_permit_path);
        if (file_exists($localPath)) {
            $contents = file_get_contents($localPath);
            Storage::disk('gcs')->put($profile->business_permit_path, $contents);
            echo "Migrated: {$profile->business_permit_path}\n";
        }
    }
    
    // Migrate government ID
    if ($profile->government_id_path) {
        $localPath = storage_path('app/public/' . $profile->government_id_path);
        if (file_exists($localPath)) {
            $contents = file_get_contents($localPath);
            Storage::disk('gcs')->put($profile->government_id_path, $contents);
            echo "Migrated: {$profile->government_id_path}\n";
        }
    }
    
    // Migrate additional documents
    if ($profile->additional_docs && is_array($profile->additional_docs)) {
        foreach ($profile->additional_docs as $docPath) {
            $localPath = storage_path('app/public/' . $docPath);
            if (file_exists($localPath)) {
                $contents = file_get_contents($localPath);
                Storage::disk('gcs')->put($docPath, $contents);
                echo "Migrated: {$docPath}\n";
            }
        }
    }
}

echo "Migration complete!\n";
```

## Quick Verification Checklist

- [ ] `.env` has `FILESYSTEM_DISK=gcs`
- [ ] GCS bucket name is configured in `.env`
- [ ] GCS credentials are properly set
- [ ] GCS bucket has public read access (or CORS configured)
- [ ] Email template uses `StorageHelper::url()` instead of `url('storage/...')`
- [ ] Test email shows correct GCS URLs for documents
- [ ] Document links in email open successfully (no 404)

## Additional Notes

- The fix also ensures future document uploads will work correctly
- If you deploy to production, make sure to update the `.env` file there as well
- Consider adding rate limiting on document downloads if needed
- Monitor GCS costs as public buckets can incur bandwidth charges

## Support

If issues persist:
1. Check `storage/logs/laravel.log` for errors
2. Verify GCS bucket permissions in Google Cloud Console
3. Test URL generation manually using the StorageHelper
4. Ensure the Laravel GCS driver package is installed (`composer require superbalist/laravel-google-cloud-storage`)

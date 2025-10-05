# 🗄️ Google Cloud Storage Setup for HikeThere
**Date:** October 6, 2025  
**Bucket:** hikethere-storage  
**Purpose:** Store profile pictures, trail images, and other assets

---

## 📋 Prerequisites

✅ Google Cloud Storage package already installed: `google/cloud-storage: ^1.48`  
✅ GCS disk configuration exists in `config/filesystems.php`  
✅ Bucket created: `hikethere-storage`

---

## 🔑 Step 1: Get Service Account Key

### Option A: Using Existing Service Account
If you already have a service account JSON key:

1. Locate your service account JSON file
2. Copy the entire contents
3. Base64 encode it for Railway

### Option B: Create New Service Account

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Select your project
3. Navigate to **IAM & Admin** → **Service Accounts**
4. Click **Create Service Account**
   - Name: `hikethere-storage-sa`
   - Description: `Service account for HikeThere storage bucket`
5. Grant **Storage Admin** role
6. Click **Create Key** → Choose **JSON**
7. Download the JSON file

---

## 🔧 Step 2: Configure Environment Variables

### For Local Development (.env)

Add these to your `.env` file:

```env
# Google Cloud Storage
FILESYSTEM_DISK=gcs
GCS_PROJECT_ID=your-project-id
GCS_BUCKET=hikethere-storage
GCS_KEY_FILE=/path/to/service-account-key.json
```

### For Railway Production

You need to base64 encode your service account JSON and add it to Railway:

#### Encode the JSON file:

**PowerShell:**
```powershell
[Convert]::ToBase64String([System.IO.File]::ReadAllBytes("path\to\service-account.json"))
```

**Linux/Mac:**
```bash
base64 -w 0 service-account.json
```

#### Add to Railway Environment Variables:

1. Go to Railway Dashboard → Your Project
2. Click **Variables** tab
3. Add these variables:

| Variable | Value |
|----------|-------|
| `FILESYSTEM_DISK` | `gcs` |
| `GCS_PROJECT_ID` | Your Google Cloud Project ID |
| `GCS_BUCKET` | `hikethere-storage` |
| `GCS_KEY_FILE_CONTENT` | The base64 encoded JSON string |

---

## 🪣 Step 3: Configure Bucket for Public Access

Your profile pictures need to be publicly accessible. Configure the bucket:

### Make Bucket Public

```bash
# Using gcloud CLI
gsutil iam ch allUsers:objectViewer gs://hikethere-storage
```

Or in Google Cloud Console:
1. Go to [Cloud Storage Browser](https://console.cloud.google.com/storage/browser)
2. Click on `hikethere-storage`
3. Go to **Permissions** tab
4. Click **Grant Access**
5. Add principal: `allUsers`
6. Role: **Storage Object Viewer**
7. Save

### Set CORS Policy (if needed for direct uploads)

Create `cors.json`:
```json
[
  {
    "origin": ["https://hikethere-production.up.railway.app"],
    "method": ["GET", "HEAD", "PUT", "POST", "DELETE"],
    "responseHeader": ["Content-Type"],
    "maxAgeSeconds": 3600
  }
]
```

Apply CORS:
```bash
gsutil cors set cors.json gs://hikethere-storage
```

---

## 📝 Step 4: Update File Uploads

### Check Current Upload Code

Your profile picture uploads are likely using:
```php
$path = $request->file('profile_picture')->store('profile-pictures', 'public');
```

This stores files locally at `storage/app/public/profile-pictures/`.

### Update to Use GCS

No code changes needed! Just change `FILESYSTEM_DISK=gcs` and Laravel will automatically use GCS.

However, update how you generate URLs:

**Before (local storage):**
```php
$url = Storage::url($user->profile_picture_path);
// Returns: /storage/profile-pictures/xyz.jpg
```

**After (GCS):**
```php
$url = Storage::disk('gcs')->url($user->profile_picture_path);
// Returns: https://storage.googleapis.com/hikethere-storage/profile-pictures/xyz.jpg
```

---

## 🔍 Step 5: Find All File Upload Locations

Let me search for where profile pictures are being uploaded:

### Common Locations:
- `app/Http/Controllers/ProfileController.php`
- `app/Http/Controllers/Auth/RegisterController.php`
- `app/Http/Controllers/HikerAccountController.php`
- `app/Models/User.php` (accessor methods)

---

## 🧪 Step 6: Test the Setup

### Test File Upload
```php
// In tinker or a test controller
use Illuminate\Support\Facades\Storage;

// Upload a test file
Storage::disk('gcs')->put('test.txt', 'Hello from HikeThere!');

// Get the public URL
$url = Storage::disk('gcs')->url('test.txt');
echo $url;
// Should return: https://storage.googleapis.com/hikethere-storage/test.txt

// Verify it exists
$exists = Storage::disk('gcs')->exists('test.txt');
echo $exists ? 'File exists!' : 'File not found';

// Delete test file
Storage::disk('gcs')->delete('test.txt');
```

---

## 📦 Step 7: Migrate Existing Files (Optional)

If you have existing profile pictures in local storage, migrate them:

```php
// Migration script
use Illuminate\Support\Facades\Storage;
use App\Models\User;

$users = User::whereNotNull('profile_picture_path')->get();

foreach ($users as $user) {
    $localPath = $user->profile_picture_path;
    
    // Check if file exists locally
    if (Storage::disk('public')->exists($localPath)) {
        // Copy to GCS
        $contents = Storage::disk('public')->get($localPath);
        Storage::disk('gcs')->put($localPath, $contents);
        
        echo "Migrated: {$localPath}\n";
    }
}
```

---

## 🎯 Step 8: Update User Profile URLs

### Create Accessor in User Model

```php
// app/Models/User.php

public function getProfilePictureUrlAttribute(): ?string
{
    if (!$this->profile_picture_path) {
        return null;
    }
    
    // If using GCS, return GCS URL
    if (config('filesystems.default') === 'gcs') {
        return Storage::disk('gcs')->url($this->profile_picture_path);
    }
    
    // Otherwise return local URL
    return Storage::url($this->profile_picture_path);
}
```

### Use in Blade Templates

```blade
{{-- Before --}}
<img src="{{ asset('storage/' . $user->profile_picture_path) }}">

{{-- After --}}
<img src="{{ $user->profile_picture_url }}">
```

---

## ⚠️ Common Issues

### Issue 1: 404 Not Found
**Problem:** Images return 404  
**Solution:** Ensure bucket is public (Storage Object Viewer for allUsers)

### Issue 2: Authentication Error
**Problem:** "Could not authenticate"  
**Solution:** Verify GCS_KEY_FILE_CONTENT is correctly base64 encoded

### Issue 3: Wrong URL Format
**Problem:** URLs point to `/storage/` instead of GCS  
**Solution:** Use `Storage::disk('gcs')->url()` instead of `Storage::url()`

### Issue 4: CORS Errors
**Problem:** Browser blocks requests  
**Solution:** Set CORS policy on bucket (see Step 3)

---

## 🚀 Quick Setup Command Summary

```bash
# 1. Encode service account key
$base64 = [Convert]::ToBase64String([System.IO.File]::ReadAllBytes("service-account.json"))
Write-Output $base64

# 2. Add to Railway (in Railway dashboard)
# FILESYSTEM_DISK=gcs
# GCS_PROJECT_ID=your-project-id
# GCS_BUCKET=hikethere-storage
# GCS_KEY_FILE_CONTENT=<base64 string>

# 3. Make bucket public
gsutil iam ch allUsers:objectViewer gs://hikethere-storage

# 4. Test in Laravel
php artisan tinker
Storage::disk('gcs')->put('test.txt', 'test');
Storage::disk('gcs')->url('test.txt');
```

---

## 📊 Current Status Checklist

- [ ] Service account JSON key obtained
- [ ] Base64 encode the key
- [ ] Add environment variables to Railway
- [ ] Make bucket public (allUsers:objectViewer)
- [ ] Update User model with accessor
- [ ] Update blade templates to use accessor
- [ ] Test file upload
- [ ] Migrate existing files (optional)
- [ ] Update profile picture upload controller
- [ ] Deploy to Railway
- [ ] Verify images load correctly

---

## 🎯 Next Steps

1. **Provide me with:**
   - Do you have the service account JSON key?
   - What's your Google Cloud Project ID?
   
2. **I'll help you:**
   - Encode the key for Railway
   - Find all file upload locations
   - Update the code to use GCS
   - Test the setup

---

**Ready to proceed?** Let me know if you have the service account key, and I'll guide you through the setup!

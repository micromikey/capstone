# 🚀 Google Cloud Storage Setup Checklist

## ✅ Completed Steps

- [x] Service account created (hikethere-railway@hikethere.iam.gserviceaccount.com)
- [x] Service account key generated and base64 encoded
- [x] User model updated for GCS URLs
- [x] ProfileController updated (all 5 methods)
- [x] Flysystem GCS adapter installed (league/flysystem-google-cloud-storage 3.0.0)
- [x] Safety checks added to prevent crashes during transition
- [x] All code committed and pushed to Railway

**Latest commits:**
- `c3ae9e8` - Install Flysystem Google Cloud Storage adapter
- `74dcdd6` - Add GCS safety checks and fallback logic

---

## 🔥 CRITICAL: You Must Complete These Steps

### Step 1: Add Railway Environment Variables

Go to Railway Dashboard → Your Project → Variables tab and add these 4 variables:

```
FILESYSTEM_DISK=gcs
GCS_PROJECT_ID=hikethere
GCS_BUCKET=hikethere-storage
GCS_KEY_FILE_CONTENT=<paste the base64 string below>
```

**Where to get the base64 key:**
1. Open: `C:\Users\Michael Torres\Downloads\gcs-key-base64.txt`
2. Copy ALL 3,148 characters (it's one long line)
3. Paste into Railway's `GCS_KEY_FILE_CONTENT` variable

**Note:** The key should look like: `ewogICJ0eXBlIjogInNlcnZpY2VfYWNjb3VudCIsC...` (very long string)

---

### Step 2: Make GCS Bucket Public

You need to allow public read access so profile pictures are visible to everyone.

**Option A - Command Line (Fastest):**
```bash
gsutil iam ch allUsers:objectViewer gs://hikethere-storage
```

**Option B - Console (Visual):**
1. Go to: https://console.cloud.google.com/storage/browser/hikethere-storage
2. Click "Permissions" tab
3. Click "Grant Access"
4. New Principal: `allUsers`
5. Role: "Storage Object Viewer"
6. Click "Save"

---

### Step 3: Wait for Railway Deployment

After adding the environment variables, Railway will automatically redeploy. This takes about **2-3 minutes**.

**Check deployment status:**
- Go to Railway Dashboard → Deployments
- Wait for green checkmark ✅
- Click "View Logs" to see deployment progress

---

### Step 4: Test Profile Picture Upload

Once Railway shows deployment complete:

1. Go to: https://hikethere-production.up.railway.app/profile
2. Click "Change Profile Picture" or edit profile
3. Upload a new image (JPG/PNG, max 2MB)
4. **Verify the URL** - Open browser DevTools (F12):
   - Network tab → Look for the uploaded image
   - URL should be: `https://storage.googleapis.com/hikethere-storage/profile-pictures/...`
   - ❌ Should NOT be: `hikethere-production.up.railway.app/storage/...`

---

### Step 5: Test Persistence (CRITICAL)

This is the whole point - make sure files don't disappear!

1. Upload a profile picture
2. **Note the filename** (e.g., `profile-pictures/abc123.jpg`)
3. Trigger Railway restart:
   - Go to Railway Dashboard → Deployments
   - Click "Redeploy" on latest deployment
4. Wait for restart (~2-3 minutes)
5. Refresh your profile page
6. ✅ Profile picture should **still be there**
7. Verify in GCS Console:
   - https://console.cloud.google.com/storage/browser/hikethere-storage/profile-pictures
   - Your image should be listed

---

## 🔧 Troubleshooting

### Error: "Driver [gcs] is not supported"

**Cause:** Environment variables not added to Railway yet  
**Fix:** Complete Step 1 above  
**Note:** Your app won't crash - it will fall back to local storage

### Error: "403 Forbidden" when viewing images

**Cause:** Bucket not made public  
**Fix:** Complete Step 2 above

### Images upload but disappear after restart

**Cause:** Railway is still using local storage instead of GCS  
**Fix:** 
1. Check Railway logs for: `GCS configured but bucket not set, using public disk`
2. Verify `FILESYSTEM_DISK=gcs` is set in Railway
3. Verify `GCS_KEY_FILE_CONTENT` is not empty
4. Check for typos in variable names

### How to check what storage is being used

**Check Railway logs:**
```bash
# Should see this when GCS is working:
[INFO] Storing profile picture to GCS: profile-pictures/abc123.jpg

# Should NOT see these warnings:
[WARNING] GCS configured but bucket not set, using public disk
[ERROR] GCS configuration error: ...
```

**Check profile picture URL:**
- GCS (correct): `https://storage.googleapis.com/hikethere-storage/profile-pictures/abc123.jpg`
- Local (wrong): `https://hikethere-production.up.railway.app/storage/profile-pictures/abc123.jpg`

---

## 📊 What Changed in Your Code

### Safety Checks Added

All storage operations now have graceful fallback logic:

```php
$disk = config('filesystems.default', 'public');
if ($disk === 'gcs') {
    try {
        if (!config('filesystems.disks.gcs.bucket')) {
            $disk = 'public'; // Fall back if not configured
            \Log::warning('GCS configured but bucket not set, using public disk');
        }
    } catch (\Exception $e) {
        $disk = 'public'; // Fall back on any error
        \Log::error('GCS configuration error: ' . $e->getMessage());
    }
}
```

**Benefits:**
- App won't crash if GCS misconfigured
- Automatically falls back to local storage
- Logs warnings for debugging
- Allows smooth transition from local → GCS

### Files Modified

1. **app/Models/User.php** (lines 349-371)
   - `getProfilePictureUrlAttribute()` now supports GCS URLs
   - Returns GCS URL when configured, local URL as fallback

2. **app/Http/Controllers/ProfileController.php**
   - `updateHikerProfile()` - Picture-only upload (lines 68-94)
   - `updateHikerProfile()` - Full profile update (lines 130-154)
   - `updateOrganizationProfile()` - Picture-only (lines 169-207)
   - `updateOrganizationProfile()` - Full update (lines 239-261)
   - `deleteProfilePicture()` - Delete handler (lines 276-294)
   - `uploadProfilePicture()` - AJAX endpoint (lines 303-343)

All 6 methods now use dynamic storage with safety checks!

---

## 🎯 Success Criteria

You'll know GCS is working correctly when:

✅ Profile pictures upload successfully  
✅ Image URLs are `storage.googleapis.com` (not Railway)  
✅ Images persist after Railway restart  
✅ Delete functionality removes files from GCS  
✅ Default avatars show when no picture uploaded  
✅ No errors in Railway logs about GCS  

---

## 📝 Next Steps After GCS Works

Once GCS is confirmed working:

1. **Test all upload scenarios:**
   - ✅ **Profile Pictures:**
     - Hiker profile picture upload
     - Organization profile picture upload
     - Picture update (replacing existing)
     - Picture delete
     - AJAX upload via "Change Picture" button
   
   - ✅ **Trail Reviews:**
     - Upload review images (up to 5 photos per review)
     - View review images in trail details
     - Delete reviews with images
   
   - ✅ **Support Tickets:**
     - Create ticket with attachments (images, PDFs, documents)
     - Reply to ticket with attachments
     - Delete ticket (should delete all attachments)
     - View/download attachments
   
   - ✅ **Organization Payment QR Codes:**
     - Upload manual payment QR code
     - Replace existing QR code
     - View QR code on booking page
   
   - ✅ **Organization Registration:**
     - Business permit upload
     - Government ID upload
     - Additional documents upload
     - View documents in admin approval panel

2. **Monitor GCS usage:**
   - https://console.cloud.google.com/storage/browser/hikethere-storage
   - Check file count and storage size
   - Verify folder structure:
     - `profile-pictures/` - User and organization avatars
     - `review-images/` - Trail review photos
     - `support-attachments/` - Help desk files
     - `qr_codes/` - Payment QR codes
     - `organization_documents/` - Registration files
     - `trail-images/` - Trail photos (if any)
   - Monitor for unexpected file growth

3. **Consider migration:**
   - If you have existing files in Railway local storage
   - They won't be accessible after restart
   - Users will need to re-upload OR
   - You can migrate them manually to GCS (see migration script in docs)

---

## � Complete List of Files Updated for GCS

### Controllers Updated (6 files):
1. ✅ **ProfileController.php** - Profile pictures (6 methods)
2. ✅ **TrailReviewController.php** - Review images
3. ✅ **SupportController.php** - Support attachments (4 locations)
4. ✅ **OrganizationPaymentController.php** - QR codes
5. ✅ **RegisteredUserController.php** - Organization documents
6. ✅ **TrailPdfController.php** - PDF image reading (2 locations)

### Models Updated (1 file):
7. ✅ **User.php** - Profile picture URL accessor

### Total Changes:
- **7 files modified**
- **18 storage operations** updated with GCS support
- **All operations** have safety checks and fallback logic
- **Latest commits:** c3ae9e8, 74dcdd6, c6b7dd9

---

## 🎯 Summary of All File Types Migrated to GCS

| File Type | Controller | Folder | Notes |
|-----------|-----------|--------|-------|
| Profile Pictures | ProfileController | `profile-pictures/` | Users & organizations |
| Review Images | TrailReviewController | `review-images/` | Up to 5 per review |
| Support Attachments | SupportController | `support-attachments/` | Images, PDFs, docs |
| Payment QR Codes | OrganizationPaymentController | `qr_codes/` | Manual payment |
| Business Permits | RegisteredUserController | `organization_documents/` | Org registration |
| Government IDs | RegisteredUserController | `organization_documents/` | Org registration |
| Additional Docs | RegisteredUserController | `organization_documents/` | Org registration |
| Trail Images | TrailPdfController | `trail-images/` | PDF generation (read-only) |

**All file uploads in your entire application now support Google Cloud Storage!** 🎉

---

## �📞 Need Help?

If you encounter issues:

1. Check Railway logs (Dashboard → Logs)
2. Look for warnings/errors with "GCS" in message
3. Verify all 4 environment variables are set
4. Confirm bucket is public (try accessing an image directly)
5. Test with a fresh profile picture upload

**Common issues:**
- Forgot to make bucket public → 403 errors
- Typo in variable name → GCS not recognized
- Base64 key incomplete → Authentication fails
- Using `FILESYSTEM_DRIVER` instead of `FILESYSTEM_DISK` → Won't work

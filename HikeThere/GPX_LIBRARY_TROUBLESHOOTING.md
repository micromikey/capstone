# GPX Library Loading - Troubleshooting Guide

**Issue:** GPX Library not loading when clicking "Load from GPX Library" button  
**Date:** October 8, 2025  
**Status:** ✅ Fixed with enhanced error handling

---

## Root Cause

The GPX library was failing due to:

1. **Storage URL Generation Issue**: The `Storage::disk()->url()` method doesn't work properly with all disk types (especially GCS)
2. **Missing GPX Files**: If no GPX files exist in the `geojson/` folder, the library appears empty
3. **Poor Error Handling**: Errors were not being properly logged or displayed to users

---

## Fixes Applied

### 1. Enhanced GPX Library Controller

**File:** `app/Http/Controllers/GPXLibraryController.php`

✅ **Added comprehensive logging:**
```php
Log::info('Loading GPX library', ['disk' => $disk]);
Log::info('Found GPX files', ['count' => count($gpxFilesList)]);
```

✅ **Fixed URL generation for GCS:**
```php
if ($disk === 'gcs') {
    // For GCS, construct public URL manually
    $bucket = config('filesystems.disks.gcs.bucket');
    $url = "https://storage.googleapis.com/{$bucket}/{$filePath}";
} else {
    // For local/public disk, use the url() method
    $url = Storage::disk($disk)->url($filePath);
}
```

✅ **Added per-file error handling:**
```php
try {
    // Process each file
} catch (\Exception $fileError) {
    Log::warning('Error processing GPX file', ['file' => $filename]);
    continue; // Skip bad files instead of failing completely
}
```

✅ **Enhanced error response with debug info:**
```php
return response()->json([
    'success' => false,
    'message' => 'Failed to load GPX library: ' . $e->getMessage(),
    'files' => [],
    'debug' => [
        'disk' => config('filesystems.default'),
        'error' => $e->getMessage()
    ]
], 500);
```

### 2. Improved Frontend Error Display

**File:** `resources/views/org/trails/create.blade.php`

✅ **Better HTTP error handling:**
```javascript
.then(response => {
    if (!response.ok) {
        return response.json().then(err => {
            throw new Error(err.message || `HTTP ${response.status}`);
        });
    }
    return response.json();
})
```

✅ **Empty library detection:**
```javascript
if (data.files && data.files.length > 0) {
    displayGPXFiles(data.files);
} else {
    // Show helpful message about uploading files
    errorEl.innerHTML = '<p class="text-yellow-600">No GPX files found...</p>';
}
```

✅ **Detailed error messages:**
```javascript
errorEl.innerHTML = `<p class="text-red-600">${data.message}</p>`;
if (data.debug) {
    errorEl.innerHTML += `<p class="text-sm">Disk: ${data.debug.disk}<br>Error: ${data.debug.error}</p>`;
}
```

---

## How to Debug GPX Library Issues

### Step 1: Check Railway Logs

After clicking "Load from GPX Library", check Railway logs for:

```
[info] Loading GPX library {"disk":"gcs"}
[info] Found GPX files {"count":5,"files":["geojson/file1.gpx",...]}
[info] GPX library loaded successfully {"files_returned":5}
```

### Step 2: Check Browser Console

Open DevTools Console (F12) and look for:

```javascript
// Success:
console.log showing file list

// Error:
Error loading GPX library: [error message]
```

### Step 3: Check API Response

In DevTools Network tab:
1. Find the request to `/api/gpx-library`
2. Check the response:

**Success Response:**
```json
{
  "success": true,
  "files": [
    {
      "filename": "mt-pulag.gpx",
      "name": "Mt Pulag",
      "url": "https://storage.googleapis.com/your-bucket/geojson/mt-pulag.gpx",
      "size": 45678,
      "modified": 1728345600
    }
  ]
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Failed to load GPX library: [reason]",
  "debug": {
    "disk": "gcs",
    "error": "[detailed error]"
  }
}
```

---

## Common Issues & Solutions

### Issue 1: "No GPX files found"

**Symptoms:**
- Yellow warning message
- Empty library

**Solution:**
1. Upload GPX files to your storage
2. For GCS: Upload to `gs://your-bucket/geojson/`
3. For local: Place in `storage/app/public/geojson/`

**Upload to GCS:**
```bash
# Using gsutil
gsutil cp *.gpx gs://your-bucket-name/geojson/

# Or use GCP Console web interface
```

---

### Issue 2: "Call to undefined method url()"

**Symptoms:**
- Error in logs: `Call to undefined method 'url'`
- GPX library won't load

**Solution:**
✅ Already fixed! The code now manually constructs GCS URLs instead of using `url()` method

---

### Issue 3: Storage disk not configured

**Symptoms:**
- Error: "Disk [gcs] not configured"
- Library won't load

**Solution:**
Check Railway environment variables:
```env
FILESYSTEM_DISK=gcs
GCS_BUCKET=your-bucket-name
GCS_PROJECT_ID=your-project-id
GCS_KEY_FILE_CONTENT=base64_encoded_key
```

---

### Issue 4: GCS authentication error

**Symptoms:**
- Error: "Could not load credentials"
- 403 Forbidden errors

**Solution:**
1. Verify `GCS_KEY_FILE_CONTENT` is correctly base64 encoded
2. Check service account has "Storage Object Viewer" permission
3. Verify bucket name is correct

---

## File Requirements for GPX Library

### Required Folder Structure:

**GCS:**
```
your-bucket-name/
└── geojson/
    ├── mt-pulag.gpx
    ├── mt-apo.gpx
    ├── philippine-trails-luzon.gpx
    └── ... (other .gpx files)
```

**Local Development:**
```
storage/app/public/
└── geojson/
    ├── mt-pulag.gpx
    └── ... (other .gpx files)
```

### File Format:
- Must have `.gpx` extension
- Must be valid GPX XML format
- Files with "philippine" or "luzon" in filename get priority

---

## Testing Checklist

After fix deployment:

### Test 1: Basic Loading
- [ ] Click "Load from GPX Library"
- [ ] Modal opens with loading spinner
- [ ] Files list appears (or helpful error message)
- [ ] Check Railway logs for "Loading GPX library"

### Test 2: File Selection
- [ ] Click on a GPX file in the list
- [ ] Trail selection appears
- [ ] Select a trail
- [ ] Trail loads on map
- [ ] Trail statistics appear

### Test 3: Search Functionality
- [ ] Type in search box
- [ ] File list filters correctly
- [ ] Can find files by name

### Test 4: Error Handling
- [ ] If no files: Shows "No GPX files found" message
- [ ] If error: Shows detailed error with disk and error info
- [ ] Can close modal and try again

---

## Verification Commands

### Check if GPX files exist in GCS:
```bash
gsutil ls gs://your-bucket-name/geojson/
```

### Check file permissions:
```bash
gsutil ls -L gs://your-bucket-name/geojson/mt-pulag.gpx
```

### Test GCS connectivity:
```bash
curl "https://storage.googleapis.com/your-bucket-name/geojson/mt-pulag.gpx"
```

---

## Expected Behavior

### Local Development (FILESYSTEM_DISK=public):
1. Click "Load from GPX Library"
2. Reads from `storage/app/public/geojson/`
3. Shows list of local GPX files
4. URLs like: `http://localhost/storage/geojson/file.gpx`

### Production on Railway (FILESYSTEM_DISK=gcs):
1. Click "Load from GPX Library"
2. Reads from GCS bucket `geojson/` folder
3. Shows list of GCS GPX files
4. URLs like: `https://storage.googleapis.com/bucket/geojson/file.gpx`

---

## Still Having Issues?

If the GPX library still isn't loading:

1. **Check Railway logs** immediately after clicking the button
2. **Check browser console** for JavaScript errors
3. **Check Network tab** to see the API request/response
4. **Verify GPX files exist** in the correct storage location
5. **Test storage access** by trying to download a file directly

---

**Status: Enhanced error handling and logging added! 🔍**

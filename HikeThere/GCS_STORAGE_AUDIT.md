# Google Cloud Storage (GCS) Implementation Audit

**Date:** October 8, 2025  
**Environment:** Railway Deployment  
**Status:** ⚠️ **NEEDS FIXES**

---

## Executive Summary

Your application is **NOT fully using GCS** for file storage. While GCS is configured, the code is hardcoded to use the `public` disk (local storage) in critical areas. This means:

1. ❌ Trail images are being stored **locally** (not in GCS)
2. ❌ Google Places images are **fetched as URLs** (correct, no storage needed)
3. ⚠️ GPX files have **partial GCS support** but may fall back to local storage

---

## 🔍 Detailed Findings

### 1. **Trail Image Uploads** ❌ STORING LOCALLY

**File:** `app/Http/Controllers/OrganizationTrailController.php`  
**Method:** `handleTrailImages()`  
**Lines:** 615-695

#### Current Implementation (INCORRECT):
```php
// Line 620-623: Primary image
$primaryPath = $primaryFile->storeAs(
    'trail-images/primary',
    $primaryFile->hashName(),
    ['disk' => 'public', 'quality' => 100]  // ❌ HARDCODED 'public' disk
);

// Line 644-647: Additional images
$path = $file->storeAs(
    'trail-images/additional',
    $file->hashName(),
    ['disk' => 'public', 'quality' => 100]  // ❌ HARDCODED 'public' disk
);

// Line 670-673: Map image
$mapPath = $mapFile->storeAs(
    'trail-images/maps',
    $mapFile->hashName(),
    ['disk' => 'public', 'quality' => 100]  // ❌ HARDCODED 'public' disk
);
```

#### **Problem:**
- The code explicitly specifies `['disk' => 'public']` which **IGNORES** your `.env` setting
- Files are being saved to `storage/app/public/trail-images/` locally
- On Railway, this means files are stored **ephemerally** and will be **lost on redeploy**

#### **What Should Happen:**
- Use the default configured disk from `.env` (`FILESYSTEM_DISK=gcs`)
- Images should upload to your GCS bucket

---

### 2. **Google Places Images** ✅ CORRECT

**File:** `app/Services/TrailImageService.php`  
**Method:** `fetchGooglePlacesImagesForTrail()`  
**Lines:** 111-200

#### Current Implementation (CORRECT):
```php
// Line 169-174: Building Google Places Photo URL
$photoUrl = 'https://maps.googleapis.com/maps/api/place/photo?'.http_build_query([
    'maxwidth' => 1600,
    'photo_reference' => $photoReference,
    'key' => $this->googleMapsKey,
]);

$images[] = [
    'url' => $photoUrl,  // ✅ Direct URL, no storage needed
    'source' => 'google_places',
    // ...
];
```

#### **Status:**
- ✅ **CORRECT** - Google Places images are returned as direct URLs
- ✅ No local storage or GCS storage needed
- ✅ Images are served directly from Google's CDN
- ✅ API key is used for authentication

---

### 3. **GPX Library** ⚠️ MIXED IMPLEMENTATION

**File:** `app/Http/Controllers/GPXLibraryController.php`  
**Method:** `index()`  
**Lines:** 16-60

#### Current Implementation:
```php
// Line 19: Reading from public/geojson directory
$gpxDirectory = public_path('geojson');

if (is_dir($gpxDirectory)) {
    $files = glob($gpxDirectory . '/*.gpx');
    // ...
    'path' => 'geojson/' . $filename,
    'url' => asset('geojson/' . $filename),
}
```

#### **Status:**
- ⚠️ **PARTIALLY CORRECT** - Reads from local `public/geojson/` directory
- According to your notes: "gpx library is already at the gcs with folder of geojson"
- But the code is **still reading from local filesystem**

#### **Problem:**
- If GPX files are in GCS at `geojson/` folder, this code won't find them
- The code needs to be updated to read from GCS instead of `public_path()`

---

### 4. **GPX File Upload** ⚠️ PARTIAL GCS SUPPORT

**File:** `app/Http/Controllers/OrganizationTrailController.php`  
**Method:** `storeGPXFile()`  
**Lines:** 793-817

#### Current Implementation:
```php
// Line 793-809: Smart disk selection
$disk = config('filesystems.default', 'public');

// Try to use GCS if available
if ($disk === 'gcs') {
    try {
        if (!config('filesystems.disks.gcs.bucket')) {
            $disk = 'public';
            \Log::warning('GCS configured but bucket not set, using public disk');
        }
    } catch (\Exception $e) {
        $disk = 'public';
        \Log::error('GCS configuration error: ' . $e->getMessage());
    }
}

$filename = Str::slug($trail->trail_name . '-' . $trail->mountain_name) . '.gpx';
$path = $file->storeAs('trail-gpx', $filename, $disk);
```

#### **Status:**
- ✅ **GOOD** - This method respects the `FILESYSTEM_DISK` config
- ✅ Falls back gracefully if GCS is misconfigured
- ✅ Uses the configured disk from `.env`

---

## 🔧 Required Fixes

### **Fix #1: Update Trail Image Storage to Use GCS**

**File:** `app/Http/Controllers/OrganizationTrailController.php`

Replace the hardcoded `'public'` disk with the default configured disk:

```php
protected function handleTrailImages(Request $request, Trail $trail)
{
    try {
        // Get the configured default disk (should be 'gcs' in production)
        $disk = config('filesystems.default', 'public');
        
        // Handle primary image
        if ($request->hasFile('primary_image')) {
            $primaryFile = $request->file('primary_image');
            
            // Store to configured disk (GCS in production)
            $primaryPath = $primaryFile->storeAs(
                'trail-images/primary',
                $primaryFile->hashName(),
                $disk  // ✅ Use configured disk instead of hardcoded 'public'
            );
            
            TrailImage::create([
                'trail_id' => $trail->id,
                'image_path' => $primaryPath,
                'image_type' => 'primary',
                'caption' => 'Main trail photo',
                'sort_order' => 1,
                'is_primary' => true,
            ]);
            
            Log::info('Primary image uploaded', [
                'path' => $primaryPath,
                'disk' => $disk
            ]);
        }

        // Handle additional images
        if ($request->hasFile('additional_images')) {
            $sortOrder = 2;
            foreach ($request->file('additional_images') as $file) {
                if ($file) {
                    // Store to configured disk (GCS in production)
                    $path = $file->storeAs(
                        'trail-images/additional',
                        $file->hashName(),
                        $disk  // ✅ Use configured disk
                    );
                    
                    TrailImage::create([
                        'trail_id' => $trail->id,
                        'image_path' => $path,
                        'image_type' => 'photo',
                        'caption' => "Trail view {$sortOrder}",
                        'sort_order' => $sortOrder,
                        'is_primary' => false,
                    ]);
                    
                    $sortOrder++;
                    Log::info('Additional image uploaded', [
                        'path' => $path,
                        'disk' => $disk
                    ]);
                }
            }
        }

        // Handle map image
        if ($request->hasFile('map_image')) {
            $mapFile = $request->file('map_image');
            
            // Store to configured disk (GCS in production)
            $mapPath = $mapFile->storeAs(
                'trail-images/maps',
                $mapFile->hashName(),
                $disk  // ✅ Use configured disk
            );
            
            TrailImage::create([
                'trail_id' => $trail->id,
                'image_path' => $mapPath,
                'image_type' => 'map',
                'caption' => 'Trail map',
                'sort_order' => 99,
                'is_primary' => false,
            ]);
            
            Log::info('Map image uploaded', [
                'path' => $mapPath,
                'disk' => $disk
            ]);
        }
        
    } catch (\Exception $e) {
        Log::error('Image upload error', [
            'trail_id' => $trail->id,
            'error' => $e->getMessage()
        ]);
    }
}
```

---

### **Fix #2: Update GPX Library to Read from GCS**

**File:** `app/Http/Controllers/GPXLibraryController.php`

Update to read GPX files from GCS instead of local filesystem:

```php
use Illuminate\Support\Facades\Storage;

public function index()
{
    try {
        $gpxFiles = [];
        $disk = config('filesystems.default', 'public');
        
        // Get all .gpx files from geojson folder in configured storage
        $files = Storage::disk($disk)->files('geojson');
        $gpxFilesList = array_filter($files, function($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'gpx';
        });
        
        foreach ($gpxFilesList as $filePath) {
            $filename = basename($filePath);
            $name = pathinfo($filename, PATHINFO_FILENAME);
            
            // Prioritize Philippine trails files
            $priority = 0;
            if (str_contains($filename, 'philippine') || str_contains($filename, 'luzon')) {
                $priority = 100;
            } elseif (str_contains($filename, 'test')) {
                $priority = 10;
            }
            
            // Get file info from storage
            $size = Storage::disk($disk)->size($filePath);
            $modified = Storage::disk($disk)->lastModified($filePath);
            $url = Storage::disk($disk)->url($filePath);
            
            $gpxFiles[] = [
                'filename' => $filename,
                'name' => ucwords(str_replace(['_', '-'], ' ', $name)),
                'path' => $filePath,
                'url' => $url,
                'size' => $size,
                'modified' => $modified,
                'priority' => $priority
            ];
        }
        
        // Sort by priority (Philippine trails first)
        usort($gpxFiles, function($a, $b) {
            return $b['priority'] - $a['priority'];
        });
        
        return response()->json([
            'success' => true,
            'files' => $gpxFiles
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error loading GPX library', ['error' => $e->getMessage()]);
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to load GPX library',
            'files' => []
        ]);
    }
}

public function parseGPX(Request $request)
{
    $request->validate([
        'filename' => 'required|string'
    ]);
    
    try {
        $filename = $request->filename;
        $disk = config('filesystems.default', 'public');
        $filePath = 'geojson/' . $filename;
        
        // Check if file exists in storage
        if (!Storage::disk($disk)->exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'GPX file not found'
            ]);
        }
        
        // Read content from storage
        $gpxContent = Storage::disk($disk)->get($filePath);
        $gpxData = $this->parseGPXContent($gpxContent);
        
        return response()->json([
            'success' => true,
            'data' => $gpxData
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error parsing GPX file', [
            'filename' => $request->filename,
            'error' => $e->getMessage()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to parse GPX file'
        ]);
    }
}

public function searchTrails(Request $request)
{
    // ... validation ...
    
    try {
        $mountainName = strtolower($request->mountain_name);
        $trailName = strtolower($request->trail_name ?? '');
        $location = strtolower($request->location ?? '');
        
        $allMatches = [];
        $disk = config('filesystems.default', 'public');
        
        // Get all .gpx files from geojson folder
        $files = Storage::disk($disk)->files('geojson');
        $gpxFilesList = array_filter($files, function($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'gpx';
        });
        
        foreach ($gpxFilesList as $filePath) {
            $filename = basename($filePath);
            
            // Read content from storage
            $gpxContent = Storage::disk($disk)->get($filePath);
            $gpxData = $this->parseGPXContent($gpxContent);
            
            if ($gpxData && isset($gpxData['trails'])) {
                $matches = $this->findMatchingTrails($gpxData['trails'], $mountainName, $trailName, $location);
                
                foreach ($matches as $match) {
                    $match['source_file'] = $filename;
                    $allMatches[] = $match;
                }
            }
        }
        
        // Sort by match score
        usort($allMatches, function($a, $b) {
            return $b['match_score'] - $a['match_score'];
        });
        
        return response()->json([
            'success' => true,
            'trails' => array_slice($allMatches, 0, 10),
            'total_matches' => count($allMatches),
            'search_params' => [
                'mountain_name' => $request->mountain_name,
                'trail_name' => $request->trail_name,
                'location' => $request->location
            ]
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error searching trails', [
            'mountain_name' => $request->mountain_name,
            'error' => $e->getMessage()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to search trails',
            'trails' => []
        ]);
    }
}
```

---

## ✅ Verification Checklist

After applying fixes, verify:

### **Environment Configuration**
- [ ] `.env` has `FILESYSTEM_DISK=gcs`
- [ ] `.env` has all GCS credentials configured:
  - `GCS_PROJECT_ID`
  - `GCS_BUCKET`
  - `GCS_KEY_FILE_CONTENT` (base64 encoded service account key)

### **Trail Image Upload Test**
1. [ ] Create a new trail through the web interface
2. [ ] Upload a primary image
3. [ ] Check Railway logs for `"Primary image uploaded"` with `"disk":"gcs"`
4. [ ] Verify image appears in GCS bucket at `trail-images/primary/`
5. [ ] Verify image is accessible via GCS public URL

### **GPX Library Test**
1. [ ] Ensure GPX files are uploaded to GCS bucket at `geojson/` folder
2. [ ] Open trail creation page
3. [ ] Click "Browse GPX Library" button
4. [ ] Verify GPX files are listed
5. [ ] Check Railway logs - should NOT see any filesystem errors

### **Google Places Images Test**
1. [ ] View any trail detail page
2. [ ] Check browser DevTools Network tab
3. [ ] Verify image URLs start with `https://maps.googleapis.com/maps/api/place/photo?`
4. [ ] Verify images load successfully

---

## 📋 Railway Deployment Notes

### **Important:**
- Railway uses ephemeral filesystem
- Any files stored to `storage/app/public/` will be **LOST** on redeploy
- **MUST** use GCS for persistent file storage

### **Required Railway Environment Variables:**
```bash
FILESYSTEM_DISK=gcs
GCS_PROJECT_ID=your-project-id
GCS_BUCKET=your-bucket-name
GCS_KEY_FILE_CONTENT=base64encodedserviceaccountkey
```

### **GCS Bucket Setup:**
- Bucket should be **publicly readable** or use signed URLs
- Folders needed:
  - `trail-images/primary/`
  - `trail-images/additional/`
  - `trail-images/maps/`
  - `trail-gpx/`
  - `geojson/`

---

## 🎯 Summary

| Component | Current Status | Action Needed |
|-----------|----------------|---------------|
| Trail Image Uploads | ❌ Storing Locally | Fix code to use GCS |
| Google Places Images | ✅ Correct | No action needed |
| GPX Library Reading | ⚠️ Reading Local | Update to read from GCS |
| GPX File Upload | ✅ Mostly Correct | Ensure `.env` is set |

**Priority:** **HIGH** - Apply fixes immediately to prevent data loss on Railway redeployments.

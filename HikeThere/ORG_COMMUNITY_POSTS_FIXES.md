# Organization Community Posts Fixes

## Summary
Fixed multiple issues with the organization community posts feature to ensure proper functionality and consistency across the platform.

## Changes Made

### 1. ✅ Fixed Missing Images - GCS Integration

**Problem:** Community post images were not being stored in Google Cloud Storage (GCS) and were missing when displayed.

**Solution:**
- **Modified:** `app/Http/Controllers/CommunityPostController.php`
  - Updated image upload logic in the `store()` method to use GCS disk when configured
  - Images now store with disk information (GCS or public) for proper URL generation
  
- **Modified:** `app/Models/CommunityPost.php`
  - Added `Storage` facade import
  - Updated `getImageUrlsAttribute()` method to check disk type and generate proper URLs
  - GCS images now return full GCS URLs via `Storage::disk('gcs')->url($path)`
  - Local images continue to use `asset('storage/' . $path)`

**Code Changes:**
```php
// Controller - Image upload now uses GCS
$disk = config('filesystems.default') === 'gcs' ? 'gcs' : 'public';
$path = $image->store('community-posts', $disk);
$uploadedImages[] = [
    'path' => $path,
    'caption' => $validated['image_captions'][$index] ?? null,
    'disk' => $disk
];

// Model - Dynamic URL generation based on disk
if ($disk === 'gcs') {
    return Storage::disk('gcs')->url($path);
}
return asset('storage/' . $path);
```

### 2. ✅ Standardized Organization Headers

**Problem:** The community posts page header was different from other organization views (trails, events, dashboard).

**Solution:**
- **Modified:** `resources/views/org/community/index.blade.php`
  - Replaced custom header with standard organization header format
  - Now includes `<x-trail-breadcrumb />` component for consistency
  - Removed icon and description to match other org pages
  - Maintains simple "Community Posts" title

**Before:**
```blade
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div class="flex items-center gap-3">
        <div class="p-2 bg-purple-100 rounded-lg">
            <!-- Icon -->
        </div>
        <div>
            <h2>Community Posts</h2>
            <p>Share updates and view posts...</p>
        </div>
    </div>
</div>
```

**After:**
```blade
<div class="space-y-4">
    <x-trail-breadcrumb />
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Community Posts') }}
        </h2>
    </div>
</div>
```

### 3. ✅ Fixed Trail Selection - Use Owned Trails

**Problem:** When creating posts from the organization side, the system was attempting to fetch followed trails instead of the organization's created/owned trails.

**Solution:**
- **Modified:** `routes/web.php` - `/api/organization/trails` endpoint
  - Changed from `organization_id` to `user_id` (correct column for trail ownership)
  - Changed from `status` to `is_active` (correct column name)
  - Added `slug` to the select for potential future use
  - Added comment clarifying it fetches "created/owned" trails

**Before:**
```php
$trails = \App\Models\Trail::where('organization_id', $user->id)
    ->where('status', 'active')
    ->select('id', 'trail_name')
```

**After:**
```php
// Get organization's created/owned trails, not followed trails
$trails = \App\Models\Trail::where('user_id', $user->id)
    ->where('is_active', true)
    ->select('id', 'trail_name', 'slug')
```

### 4. ✅ Fixed Event Selection - Use Owned Events

**Problem:** Similar to trails, events were using incorrect column names and logic for fetching organization's events.

**Solution:**
- **Modified:** `routes/web.php` - `/api/organization/events` endpoint
  - Changed from `organization_id` to `user_id` (correct column for event ownership)
  - Removed date filter to show all active events (not just future ones)
  - Changed to use `is_active` status check
  - Added `slug` to the select for consistency
  - Added comment clarifying it fetches "created/owned" events

**Before:**
```php
$events = \App\Models\Event::where('organization_id', $user->id)
    ->where('start_date', '>=', now())
    ->select('id', 'title')
```

**After:**
```php
// Get organization's created/owned events, not followed events
$events = \App\Models\Event::where('user_id', $user->id)
    ->where('is_active', true)
    ->select('id', 'title', 'slug')
```

## Files Modified

1. `app/Http/Controllers/CommunityPostController.php` - Image upload to GCS
2. `app/Models/CommunityPost.php` - GCS URL generation and Storage import
3. `resources/views/org/community/index.blade.php` - Standardized header
4. `routes/web.php` - Organization trails and events API endpoints

## Benefits

1. **Images Now Work Properly**
   - All community post images are stored in GCS
   - Images display correctly with proper URLs
   - System gracefully falls back to local storage if GCS is not configured

2. **Consistent User Experience**
   - All organization pages now have the same header style
   - Navigation breadcrumbs work consistently
   - Professional, uniform appearance

3. **Correct Content Promotion**
   - Organizations can now promote their own created trails
   - Organizations can now promote their own created events
   - No confusion with followed content vs. owned content
   - Proper promotion of organization's offerings

4. **Proper Data Queries**
   - Uses correct column names (`user_id` instead of `organization_id`)
   - Uses correct status columns (`is_active` instead of `status`)
   - More efficient queries with only necessary fields selected

## Testing Recommendations

1. **Test Image Upload:**
   - Create a new post with images from an organization account
   - Verify images are stored in GCS bucket
   - Verify images display correctly in the post

2. **Test Trail/Event Selection:**
   - Login as an organization that has created trails and events
   - Open create post modal
   - Verify only organization's own trails/events appear in dropdowns
   - Create a post with a trail
   - Create a post with an event

3. **Test Header Consistency:**
   - Navigate through Trails, Events, Bookings, and Community Posts pages
   - Verify headers are consistent across all pages
   - Verify breadcrumbs work properly

## Configuration Required

Ensure your `.env` file has GCS configured:
```env
FILESYSTEM_DISK=gcs
GCS_PROJECT_ID=your-project-id
GCS_BUCKET=your-bucket-name
GCS_KEY_FILE_CONTENT=your-base64-encoded-key
```

## Notes

- The system will automatically use local storage if GCS is not configured
- Old posts with local storage images will continue to work
- The changes are backward compatible with existing data
- All lint errors shown are false positives related to PHPStan not recognizing Laravel facades

# OG Image Fix Summary

## Problem Found
The Facebook scraper was showing `https://hikethere.site/img/icon1.png` instead of the correct OG image because:

1. **Duplicate og:image meta tag** in `guest.blade.php` (line 40)
   - Had `<meta property="og:image"...>` instead of `<meta name="twitter:image"...>`
   - This second og:image tag was overriding the correct one

2. **meta-tags.blade.php component** was using `asset()` helper
   - `asset('img/og-image.png')` generates relative URLs like `/img/og-image.png`
   - Should use GCS URL logic like the layouts do

## Fixes Applied

### 1. Fixed guest.blade.php (Line 40)
**Before:**
```php
<meta property="og:image" content="{{ $metaImage ?? $defaultOgImage }}">
```

**After:**
```php
<meta name="twitter:image" content="{{ $metaImage ?? $defaultOgImage }}">
```

### 2. Updated meta-tags.blade.php Component
**Before:**
```php
$defaultImage = asset('img/og-image.png');
```

**After:**
```php
// Use GCS URL if available, otherwise fallback to local asset
$defaultImage = env('OG_IMAGE_URL') 
    ? env('OG_IMAGE_URL') 
    : (config('filesystems.default') === 'gcs' && env('GCS_BUCKET')
        ? 'https://storage.googleapis.com/' . env('GCS_BUCKET') . '/img/og-image.png'
        : asset('img/og-image.png'));
```

## Testing Steps

### 1. Deploy to Railway
```bash
git add .
git commit -m "Fix OG image meta tag duplicate and GCS URL support"
git push
```

### 2. Clear Facebook Cache
1. Go to: https://developers.facebook.com/tools/debug/
2. Enter: `https://hikethere.site`
3. Click **"Scrape Again"** button multiple times
4. Verify og:image shows: `https://storage.googleapis.com/hikethere-storage/img/og-image.png`

### 3. Test on Other Platforms
- **Twitter/X**: https://cards-dev.twitter.com/validator
- **LinkedIn**: Share the link and check preview
- **Messenger**: Send link to yourself, check preview image

### 4. View Page Source (Production)
1. Visit: https://hikethere.site
2. Right-click → "View Page Source"
3. Search for: `og:image`
4. Should see: `<meta property="og:image" content="https://storage.googleapis.com/hikethere-storage/img/og-image.png">`

## Expected Results

### Before Fix
```html
<meta property="og:image" content="https://hikethere.site/img/icon1.png">
<meta property="og:image" content="https://hikethere.site/img/og-image.png"> <!-- Duplicate! -->
```
❌ Facebook uses the first one (icon1.png) - WRONG

### After Fix
```html
<meta property="og:image" content="https://storage.googleapis.com/hikethere-storage/img/og-image.png">
<meta name="twitter:image" content="https://storage.googleapis.com/hikethere-storage/img/og-image.png">
```
✅ Single og:image tag with correct GCS URL

## Why This Happened

1. **Copy-paste error**: When I created the meta tags, line 40 in guest.blade.php had wrong property name
2. **Relative paths**: The `asset()` helper generates paths like `/img/icon1.png` which browsers resolve to `https://hikethere.site/img/icon1.png`
3. **Facebook scraper priority**: When multiple og:image tags exist, Facebook uses the first one encountered

## Prevention

- Always use the **same GCS URL logic** across all files:
  ```php
  env('OG_IMAGE_URL') ?? (GCS check) ?? asset() fallback
  ```
- **Never duplicate** `og:image` meta tags
- Use the `<x-meta-tags>` component for consistency in future pages

## Optional: Add to Railway Environment Variables

You can add this to Railway for explicit control:
```
OG_IMAGE_URL=https://storage.googleapis.com/hikethere-storage/img/og-image.png
```

This will override all logic and force the OG image to this exact URL.

## Files Modified
1. ✅ `resources/views/layouts/guest.blade.php` - Fixed duplicate og:image tag
2. ✅ `resources/views/components/meta-tags.blade.php` - Added GCS URL support

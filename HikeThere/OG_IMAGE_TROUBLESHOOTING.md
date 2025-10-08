# OG Image Troubleshooting Guide

## 🔍 Issue: OG Image Not Showing

Based on your setup, here's what's happening:

### Your GCS Setup:
- **Bucket**: `hikethere-storage`
- **Images location**: `/img/` folder
- **Files**: `og-image.png`, `1.png`, `2.png`, `3.png`
- **Access**: Public ✅

### Your Local .env:
❌ **Missing**: `GCS_BUCKET=hikethere-storage`

---

## ✅ Solution: Add GCS_BUCKET to .env

### Step 1: Update Local .env (for testing)

Add this line to your `.env` file:

```env
# Google Cloud Storage Configuration
GCS_BUCKET=hikethere-storage
GCS_PROJECT_ID=your-project-id
FILESYSTEM_DISK=gcs
```

### Step 2: Update Production Environment Variables

**On Railway (or your deployment platform):**

1. Go to your project settings
2. Add environment variables:
   ```
   GCS_BUCKET=hikethere-storage
   FILESYSTEM_DISK=gcs
   ```

### Step 3: Clear Config Cache

```bash
php artisan config:clear
php artisan cache:clear
```

---

## 🧪 Test the URL Manually

Your OG image should be accessible at:
```
https://storage.googleapis.com/hikethere-storage/img/og-image.png
```

**Test it:**
1. Open this URL in your browser: https://storage.googleapis.com/hikethere-storage/img/og-image.png
2. If you see the image ✅, the GCS setup is correct
3. If you get 403/404 ❌, check permissions

---

## 🔧 Alternative: Use Direct URL in .env

If you don't want to set GCS_BUCKET, you can specify the OG image URL directly:

```env
OG_IMAGE_URL=https://storage.googleapis.com/hikethere-storage/img/og-image.png
```

This will override all other logic and use this specific URL.

---

## 📝 Complete .env Configuration

Add these lines to your `.env`:

```env
# Google Cloud Storage Configuration
GCS_PROJECT_ID=your-gcs-project-id
GCS_BUCKET=hikethere-storage
GCS_KEY_FILE_CONTENT=your-base64-encoded-key
FILESYSTEM_DISK=gcs

# Direct OG Image URL (optional, overrides automatic GCS URL)
OG_IMAGE_URL=https://storage.googleapis.com/hikethere-storage/img/og-image.png
```

---

## 🚀 Quick Fix (Easiest)

Just add this ONE line to your `.env`:

```env
OG_IMAGE_URL=https://storage.googleapis.com/hikethere-storage/img/og-image.png
```

Then:
1. Clear cache: `php artisan config:clear`
2. Redeploy
3. Test with Facebook Debugger

---

## 🧪 How to Verify It's Working

### Method 1: Check Page Source
1. Visit your deployed site
2. Right-click → "View Page Source"
3. Search for `og:image`
4. Should see: `https://storage.googleapis.com/hikethere-storage/img/og-image.png`

### Method 2: Facebook Debugger
1. Go to: https://developers.facebook.com/tools/debug/
2. Enter your site URL: `https://hikethere.site/`
3. Click "Scrape Again" multiple times
4. Should show your OG image in preview

### Method 3: Direct Browser Test
Just open: https://storage.googleapis.com/hikethere-storage/img/og-image.png

If the image loads, your GCS is working! ✅

---

## 🐛 Common Issues

### Issue 1: "403 Forbidden"
**Fix:** Make the file public
```bash
gsutil acl ch -u AllUsers:R gs://hikethere-storage/img/og-image.png
```

### Issue 2: Image shows locally but not in production
**Fix:** Set environment variables in your deployment platform

### Issue 3: Old image still showing on Facebook
**Fix:** Clear Facebook cache
- Use "Scrape Again" button 3-5 times
- Wait 24 hours for cache to expire
- Or change the filename

---

## ✅ Recommended Setup

**For Local Development:**
```env
FILESYSTEM_DISK=local
# No GCS_BUCKET needed - uses local files
```

**For Production (Railway/deployment):**
```env
FILESYSTEM_DISK=gcs
GCS_BUCKET=hikethere-storage
GCS_PROJECT_ID=your-project-id
GCS_KEY_FILE_CONTENT=your-base64-key

# Or just use direct URL:
OG_IMAGE_URL=https://storage.googleapis.com/hikethere-storage/img/og-image.png
```

---

## 🎯 Next Steps

1. **Add to .env**: `OG_IMAGE_URL=https://storage.googleapis.com/hikethere-storage/img/og-image.png`
2. **Test URL**: Open the URL in browser to verify it loads
3. **Clear cache**: `php artisan config:clear`
4. **Deploy**: Push changes to production
5. **Update Railway**: Add `OG_IMAGE_URL` environment variable
6. **Test**: Use Facebook Debugger

That's it! 🎉

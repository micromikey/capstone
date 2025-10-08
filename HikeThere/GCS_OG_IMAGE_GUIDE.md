# 🌐 Using Google Cloud Storage for OG Images

## Why Use GCS for OG Images?

For deployed apps, hosting your Open Graph image on Google Cloud Storage ensures:
- ✅ Fast, reliable CDN delivery
- ✅ Always accessible (no local file issues)
- ✅ Better performance globally
- ✅ Professional setup

---

## 📋 Step-by-Step Guide

### Step 1: Generate Your OG Image

1. Open `http://localhost/generate-og-image.html`
2. Customize the design (colors, tagline, etc.)
3. Right-click the preview → **Save image as** → `og-image.png`
4. Save it temporarily to `public/img/og-image.png`

### Step 2: Prepare Your Tool Card Images

Make sure you have these images ready in `public/img/`:
- `1.png` - Build Itineraries card image
- `2.png` - Self Assessment card image  
- `3.png` - Bookings card image
- `icon1.png` - Your app logo

---

### Step 3: Upload to Google Cloud Storage

#### **Option A: Using Google Cloud Console (Easiest)**

1. Go to: https://console.cloud.google.com/storage
2. Click on your bucket (the one in your `.env` as `GCS_BUCKET`)
3. Create a folder called `assets` (or use existing)
4. Click **Upload files**
5. Select and upload these files:
   - `og-image.png` (social media preview)
   - `icon1.png` (app logo)
   - `1.png` (Build Itineraries card)
   - `2.png` (Self Assessment card)
   - `3.png` (Bookings card)
6. After upload, select all files → Click the 3 dots → **Edit permissions**
7. Click **Add Entry**:
   - Entity: `allUsers`
   - Name: `allUsers`
   - Access: `Reader`
8. Save permissions
9. Your images are now publicly accessible at:
   ```
   https://storage.googleapis.com/YOUR-BUCKET-NAME/assets/og-image.png
   https://storage.googleapis.com/YOUR-BUCKET-NAME/assets/icon1.png
   https://storage.googleapis.com/YOUR-BUCKET-NAME/assets/1.png
   https://storage.googleapis.com/YOUR-BUCKET-NAME/assets/2.png
   https://storage.googleapis.com/YOUR-BUCKET-NAME/assets/3.png
   ```

#### **Option B: Using the Upload Script**

1. Make sure all images are in `public/img/`:
   - `og-image.png`
   - `icon1.png`
   - `1.png`, `2.png`, `3.png`
2. Run the upload script:
   ```bash
   php upload-og-image.php
   ```
3. The script will:
   - Upload all images to GCS
   - Make them publicly accessible
   - Show you all public URLs
   - Upload to `/assets/` folder automatically

#### **Option C: Manual Upload via gsutil**

```bash
# Upload all files at once
gsutil cp public/img/og-image.png gs://YOUR-BUCKET-NAME/assets/og-image.png
gsutil cp public/img/icon1.png gs://YOUR-BUCKET-NAME/assets/icon1.png
gsutil cp public/img/1.png gs://YOUR-BUCKET-NAME/assets/1.png
gsutil cp public/img/2.png gs://YOUR-BUCKET-NAME/assets/2.png
gsutil cp public/img/3.png gs://YOUR-BUCKET-NAME/assets/3.png

# Make all files publicly accessible
gsutil acl ch -u AllUsers:R gs://YOUR-BUCKET-NAME/assets/*.png

# Verify uploads
gsutil ls gs://YOUR-BUCKET-NAME/assets/
```

---

### Step 4: No Need to Update .env!

### Step 4: No Need to Update .env!

**Good news!** Your code is already smart enough to use GCS automatically.

The system works like this:
1. If `GCS_BUCKET` is set in `.env`, images load from GCS
2. If not, images load from local `public/img/` folder
3. You can optionally add `OG_IMAGE_URL` for a specific OG image URL

**Optional:** Add specific OG image URL to `.env`:
```env
# Optional: Specific OG Image URL (overrides automatic GCS URL)
OG_IMAGE_URL=https://storage.googleapis.com/YOUR-BUCKET-NAME/assets/og-image.png
```

**But this is NOT required!** The system auto-generates the URL from `GCS_BUCKET`.

---

### Step 5: Deploy and Test

Your layout files are already configured to use the GCS URL!

The code automatically:
1. Checks for `OG_IMAGE_URL` in `.env`
2. Falls back to GCS bucket URL if configured
3. Falls back to local asset if neither is available

```php
@php
    // This is already in your app.blade.php and guest.blade.php
    $defaultOgImage = env('OG_IMAGE_URL') 
        ? env('OG_IMAGE_URL') 
        : (config('filesystems.default') === 'gcs' && env('GCS_BUCKET')
            ? 'https://storage.googleapis.com/' . env('GCS_BUCKET') . '/assets/og-image.png'
            : asset('img/og-image.png'));
@endphp
```

---

### Step 5: Test Your OG Image

1. **Test Direct Access:**
   - Open the GCS URL in your browser
   - You should see your OG image

2. **Test Meta Tags:**
   - Deploy your app
   - Visit any page
   - View page source (Ctrl+U)
   - Look for: `<meta property="og:image" content="..."`
   - Verify it shows your GCS URL

3. **Test Social Media Preview:**
   - Go to: https://developers.facebook.com/tools/debug/
   - Enter your site URL
   - Click **Scrape Again** (multiple times)
   - You should see your custom OG image!

4. **Test on Messenger:**
   - Send your site link to yourself
   - The preview should show your custom image

---

## 🔧 Current Configuration

Your layouts (`app.blade.php` and `guest.blade.php`) now have smart image URL resolution:

```php
Priority order:
1. $metaImage (if set per page)
2. env('OG_IMAGE_URL') (from .env)
3. GCS automatic URL (if GCS is configured)
4. asset('img/og-image.png') (local fallback)
```

---

## 📁 Recommended GCS Structure

```
your-bucket/
├── assets/
│   ├── og-image.png          ← Main OG image (1200x630)
│   ├── icon1.png              ← Your logo
│   ├── og-trail.png           ← Trail page OG image (optional)
│   ├── og-event.png           ← Event page OG image (optional)
│   └── og-emergency.png       ← Emergency page OG image (optional)
├── trails/
│   └── [trail images...]
├── events/
│   └── [event images...]
└── [other folders...]
```

---

## 🎨 Creating Page-Specific OG Images

You can upload different OG images for different sections:

### For Trail Pages:
```php
// In trail controller or view
$metaImage = 'https://storage.googleapis.com/YOUR-BUCKET/assets/og-trail.png';
```

### For Event Pages:
```php
$metaImage = 'https://storage.googleapis.com/YOUR-BUCKET/assets/og-event.png';
```

### For Emergency Readiness:
```php
$metaImage = 'https://storage.googleapis.com/YOUR-BUCKET/assets/og-emergency.png';
```

---

## ✅ Verification Checklist

- [ ] OG image generated from `generate-og-image.html`
- [ ] Image uploaded to GCS
- [ ] Image permissions set to public
- [ ] Public URL tested in browser
- [ ] `OG_IMAGE_URL` added to `.env`
- [ ] Changes deployed to production
- [ ] Tested with Facebook Debugger
- [ ] Tested on Messenger/WhatsApp
- [ ] Meta tags verified in page source

---

## 🐛 Troubleshooting

### Image Not Loading

**Issue:** GCS URL returns 403 Forbidden

**Solution:**
```bash
# Make the file public
gsutil acl ch -u AllUsers:R gs://YOUR-BUCKET/assets/og-image.png

# Or set bucket-wide public access (be careful!)
gsutil iam ch allUsers:objectViewer gs://YOUR-BUCKET
```

### Wrong Image Showing

**Issue:** Old image still appears on social media

**Solution:**
- Clear Facebook cache: Use "Scrape Again" button multiple times
- Wait 24-48 hours for cache to expire
- Change the filename (e.g., `og-image-v2.png`)

### URL Not Working in Meta Tags

**Issue:** `.env` variable not being read

**Solution:**
```bash
# Clear config cache
php artisan config:clear
php artisan cache:clear

# Verify the variable
php artisan tinker
> env('OG_IMAGE_URL')
```

---

## 📊 Image Optimization Tips

### Optimal Dimensions
- **1200 x 630 pixels** (recommended)
- **Minimum:** 600 x 315 pixels
- **Maximum file size:** 8 MB

### Format
- **PNG** for logos/graphics (better quality)
- **JPG** for photos (smaller size)

### Compression
```bash
# Optimize PNG (if you have optipng installed)
optipng -o7 og-image.png

# Or use online tools:
# - TinyPNG: https://tinypng.com
# - Squoosh: https://squoosh.app
```

---

## 🔐 Security Best Practices

1. **Only make OG images public** - don't expose sensitive data
2. **Use specific paths** - `/assets/` for public assets only
3. **Set CORS properly** if serving from different domain
4. **Use HTTPS** - always use secure URLs

---

## 💡 Pro Tips

### 1. Use Different Images for Different Pages

```php
// In your controller
public function show($trail) {
    return view('trails.show', [
        'trail' => $trail,
        'metaImage' => $trail->og_image_url ?? env('OG_IMAGE_URL'),
    ]);
}
```

### 2. Add Query String for Cache Busting

```php
$metaImage = env('OG_IMAGE_URL') . '?v=' . config('app.version');
```

### 3. Monitor Image Loading

Add to your GCS bucket analytics to track:
- Image load times
- Geographic distribution
- Cache hit rates

---

## 🎉 You're All Set!

Your OG images are now hosted on Google Cloud Storage and will load fast for users worldwide!

**Final URL will look like:**
```
https://storage.googleapis.com/YOUR-BUCKET-NAME/assets/og-image.png
```

Share your HikeThere link and watch the beautiful previews appear! 🚀

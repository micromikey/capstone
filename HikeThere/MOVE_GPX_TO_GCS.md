# Moving GPX Files to Google Cloud Storage

## Why Move GPX Files to GCS?

- **Scalability**: Add hundreds/thousands of trail files without rebuilding app
- **Performance**: Faster deployments (smaller Docker image)
- **Management**: Upload new trails via GCS console, no code deployment needed
- **Cost**: GCS storage is cheaper than Railway storage

## Step-by-Step Migration

### 1. Upload Existing GPX Files to GCS

After setting up your GCS bucket (`hikethere-storage`):

1. **Go to your GCS bucket**: https://console.cloud.google.com/storage
2. **Click on your bucket name** (`hikethere-storage`)
3. **Create folder**: Click "Create Folder" → Name it `geojson`
4. **Click on the `geojson` folder**
5. **Upload files**: Click "Upload Files" → Select all 7 GPX files from `public/geojson/`
6. **Make them public** (if not already):
   - Select all uploaded files
   - Click "Permissions"
   - Add `allUsers` with `Storage Object Viewer` role

### 2. Update Laravel Config

The GPX files will now be accessible at:
```
https://storage.googleapis.com/hikethere-storage/geojson/mount-cagua-philippines.gpx
https://storage.googleapis.com/hikethere-storage/geojson/mt-ayaas-x-espadang-bato-.gpx
etc...
```

### 3. Find and Update Code References

Search your code for where GPX files are loaded. You'll need to update URLs from:
```javascript
// OLD (local files)
const gpxUrl = '/geojson/mount-cagua-philippines.gpx';

// NEW (GCS files)
const gpxUrl = 'https://storage.googleapis.com/hikethere-storage/geojson/mount-cagua-philippines.gpx';
```

Or better yet, create a helper function:

```javascript
// In your JavaScript
function getGpxUrl(filename) {
    const GCS_BUCKET = 'hikethere-storage';
    return `https://storage.googleapis.com/${GCS_BUCKET}/geojson/${filename}`;
}

// Usage
const gpxUrl = getGpxUrl('mount-cagua-philippines.gpx');
```

Or in PHP:
```php
// In a helper or config
function gpx_url($filename) {
    $bucket = config('filesystems.disks.gcs.bucket');
    return "https://storage.googleapis.com/{$bucket}/geojson/{$filename}";
}

// Usage
$gpxUrl = gpx_url('mount-cagua-philippines.gpx');
```

### 4. Update .dockerignore

Add this line to `.dockerignore` to exclude local GPX files from Docker image:
```
public/geojson/*.gpx
```

### 5. Update CSP (if needed)

If you get CORS errors, add GCS to your Content Security Policy:

In `app/Http/Middleware/SecurityHeaders.php`:
```php
"connect-src 'self' ... https://storage.googleapis.com;"
```

### 6. Test

1. Deploy the changes
2. Visit your app
3. Try loading a trail map
4. Verify GPX data loads from GCS
5. Check browser Network tab to confirm URL

## Adding New Trails Later

**Super Easy!** Just:
1. Go to GCS Console → your bucket → `geojson/` folder
2. Click "Upload Files"
3. Upload new GPX file(s)
4. Update your database/trail list to reference the new filename
5. **No code deployment needed!** ✨

## Option: Keep Small Set in Docker

If you want to keep a few "featured" trails in the Docker image as fallback:
- Keep 3-5 most popular trails in `public/geojson/`
- Store the rest in GCS
- Your code can try local first, then GCS as fallback

## Current GPX Files to Upload

```
mount-cagua-philippines.gpx
mt-ayaas-x-espadang-bato-.gpx
mt-balingkilat.gpx
mt-baloy.gpx
mt-pulag-hike-ambangeg-trail-start-at-pao.gpx
sagada-mt-ampakaw-sagada07-01-2018.gpx
wow-mt-batulao.gpx
```

## Estimated Costs

**7 GPX files (~1MB total):**
- Storage: $0.02/month
- Bandwidth (1000 loads/month): ~$0.10/month
- **Total: ~$0.12/month** 💰

**1000 GPX files (~150MB total):**
- Storage: $3/month
- Bandwidth: ~$2/month
- **Total: ~$5/month** 💰

Still much cheaper than Railway storage!

# GCS Static Assets Deployment Guide

## Overview
This guide explains how to upload and manage static assets (images, CSS, JS) in Google Cloud Storage for your Railway deployment.

## Current Issue
```
GET https://hikethere.site/img/1.png 404 (Not Found)
GET https://hikethere.site/img/2.png 404 (Not Found)
GET https://hikethere.site/img/3.png 404 (Not Found)
```

These images are referenced in `hiking-tools.blade.php` but not available in your Railway deployment filesystem.

## Solution: Upload to GCS

### Option 1: Using Google Cloud Console (Web UI)

1. **Navigate to your GCS bucket**
   - Go to https://console.cloud.google.com/storage
   - Select your bucket (the one in `GCS_BUCKET` env var)

2. **Create folder structure**
   - Click "Create folder"
   - Name it: `img`

3. **Upload images**
   - Open the `img` folder
   - Click "Upload files"
   - Select these images from your local `public/img/` directory:
     - `1.png` (Build Itineraries card)
     - `2.png` (Self Assessment card)
     - `3.png` (Bookings card)

4. **Set public access**
   - Select all uploaded files
   - Click "Permissions" tab
   - Click "Add entry"
   - Entity: `allUsers`
   - Name: `allUsers`
   - Access: `Reader`
   - Click "Save"

### Option 2: Using gsutil CLI

1. **Install Google Cloud SDK**
   ```bash
   # Download from: https://cloud.google.com/sdk/docs/install
   ```

2. **Authenticate**
   ```bash
   gcloud auth login
   gcloud config set project YOUR-PROJECT-ID
   ```

3. **Upload images**
   ```bash
   # From your project root
   cd public
   
   # Upload all images to GCS
   gsutil -m cp -r img/* gs://YOUR-BUCKET-NAME/img/
   ```

4. **Set public access**
   ```bash
   # Make the img folder publicly readable
   gsutil -m acl ch -r -u AllUsers:R gs://YOUR-BUCKET-NAME/img/
   
   # Or make the entire bucket public
   gsutil iam ch allUsers:objectViewer gs://YOUR-BUCKET-NAME
   ```

### Option 3: Using Railway CLI + Script

Create a deployment script: `upload-assets-to-gcs.sh`

```bash
#!/bin/bash

# Configuration
BUCKET_NAME="${GCS_BUCKET}"
PROJECT_ID="${GCS_PROJECT_ID}"

# Authenticate using service account
echo "${GCS_KEY_FILE_CONTENT}" | base64 -d > /tmp/gcs-key.json
gcloud auth activate-service-account --key-file=/tmp/gcs-key.json
gcloud config set project ${PROJECT_ID}

# Upload static assets
echo "Uploading static assets to gs://${BUCKET_NAME}/..."
gsutil -m cp -r public/img/* gs://${BUCKET_NAME}/img/

# Set public access
echo "Setting public access..."
gsutil -m acl ch -r -u AllUsers:R gs://${BUCKET_NAME}/img/

# Cleanup
rm /tmp/gcs-key.json

echo "✅ Static assets uploaded successfully!"
```

## Verification

After uploading, verify your images are accessible:

```bash
# Test direct GCS URL
curl -I https://storage.googleapis.com/YOUR-BUCKET-NAME/img/1.png

# Should return: HTTP/1.1 200 OK
```

Or visit in browser:
- https://storage.googleapis.com/YOUR-BUCKET-NAME/img/1.png
- https://storage.googleapis.com/YOUR-BUCKET-NAME/img/2.png
- https://storage.googleapis.com/YOUR-BUCKET-NAME/img/3.png

## Implementation Status

### ✅ Already Implemented
- Smart image URL helper function in `hiking-tools.blade.php`
- Automatic fallback to local assets in development
- Environment-based URL generation

### 📋 TODO
1. Upload static images to GCS bucket
2. Set public read permissions
3. Verify URLs work in production
4. Optional: Add CDN (Cloud CDN) for faster delivery

## File Structure in GCS

After deployment, your GCS bucket should look like:

```
gs://your-bucket-name/
├── img/                              # Static images
│   ├── 1.png                        # Build Itineraries
│   ├── 2.png                        # Self Assessment  
│   ├── 3.png                        # Bookings
│   └── [other static images]
│
├── qr_codes/                        # Organization QR codes
│   ├── 10_1759946852.jpg           # Org ID 10's QR
│   ├── 15_1759947123.png           # Org ID 15's QR
│   └── ...
│
└── payment_proofs/                  # Hiker payment proofs
    ├── 123_1759948000.jpg          # Booking ID 123
    ├── 456_1759948100.png          # Booking ID 456
    └── ...
```

## URLs Generated

### Development (Local)
```
http://localhost/img/1.png
http://localhost/storage/qr_codes/10_1759946852.jpg
http://localhost/storage/payment_proofs/123_1759948000.jpg
```

### Production (Railway + GCS)
```
https://storage.googleapis.com/YOUR-BUCKET/img/1.png
https://storage.googleapis.com/YOUR-BUCKET/qr_codes/10_1759946852.jpg
https://storage.googleapis.com/YOUR-BUCKET/payment_proofs/123_1759948000.jpg
```

## Security Considerations

### Public Assets (Static Images)
- ✅ Safe to make public
- These are decorative/UI images
- No sensitive information

### User-Uploaded Content (QR Codes & Payment Proofs)
- ✅ Already secured with public visibility for display
- URLs are not easily guessable (includes org ID and timestamp)
- Access logged by GCS
- Can add signed URLs for extra security (future enhancement)

## Performance Optimization

### Optional: Enable Cloud CDN

1. **Create a load balancer** pointing to your GCS bucket
2. **Enable Cloud CDN** on the backend
3. **Update URLs** to use CDN domain

Benefits:
- Faster load times globally
- Reduced bandwidth costs
- Automatic caching

### Image Optimization

Consider optimizing images before upload:

```bash
# Install ImageMagick
sudo apt-get install imagemagick

# Optimize PNG files
for file in public/img/*.png; do
    convert "$file" -strip -quality 85 "$file"
done

# Then upload to GCS
gsutil -m cp -r public/img/* gs://YOUR-BUCKET-NAME/img/
```

## Troubleshooting

### Images not loading (404)
1. Verify bucket name in `GCS_BUCKET` env var
2. Check images exist in GCS bucket
3. Verify public read permissions
4. Clear browser cache

### Permission denied
```bash
# Re-set public permissions
gsutil iam ch allUsers:objectViewer gs://YOUR-BUCKET-NAME
```

### Wrong path
- Ensure images are in `gs://bucket/img/` not `gs://bucket/`
- Check helper function is using correct path

## Commands Quick Reference

```bash
# List bucket contents
gsutil ls gs://YOUR-BUCKET-NAME/

# List specific folder
gsutil ls gs://YOUR-BUCKET-NAME/img/

# Check file permissions
gsutil acl get gs://YOUR-BUCKET-NAME/img/1.png

# Make file public
gsutil acl ch -u AllUsers:R gs://YOUR-BUCKET-NAME/img/1.png

# Download file (for verification)
gsutil cp gs://YOUR-BUCKET-NAME/img/1.png ./test.png

# Get file metadata
gsutil stat gs://YOUR-BUCKET-NAME/img/1.png
```

## Next Steps

1. **Upload your images** using one of the options above
2. **Test the URLs** in browser
3. **Deploy to Railway** (if not already deployed)
4. **Verify** hiking-tools page loads images correctly
5. **Monitor** GCS usage in Google Cloud Console

## Cost Considerations

- **Storage**: ~$0.020 per GB/month
- **Network egress**: First 1 GB free, then ~$0.12 per GB
- **Operations**: Very low cost for GET requests

For a typical app with a few MB of static images:
- **Monthly cost**: < $1

## Additional Resources

- [GCS Documentation](https://cloud.google.com/storage/docs)
- [gsutil Tool](https://cloud.google.com/storage/docs/gsutil)
- [Cloud CDN Setup](https://cloud.google.com/cdn/docs/setting-up-cdn-with-bucket)

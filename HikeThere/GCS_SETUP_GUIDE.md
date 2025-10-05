# Google Cloud Storage Setup Guide for HikeThere

## Step 1: Create GCS Bucket

1. **Go to Google Cloud Console**: https://console.cloud.google.com/storage
2. **Click "Create Bucket"**
3. **Configure your bucket:**
   - **Name**: `hikethere-storage` (must be globally unique, add suffix if needed)
   - **Location type**: `Region`
   - **Region**: `asia-southeast1` (Singapore - same as Railway for low latency)
   - **Storage class**: `Standard`
   - **Access control**: `Uniform` (recommended)
   - **Public access**: `Not public` (we'll configure this)
4. **Click "Create"**

## Step 2: Make Bucket Public (for viewing images)

1. Go to your bucket → **Permissions** tab
2. Click **"Grant Access"**
3. **New principals**: `allUsers`
4. **Role**: `Storage Object Viewer`
5. **Click "Save"** → Confirm "Allow Public Access"

## Step 3: Create Service Account

1. **Go to IAM & Admin → Service Accounts**: https://console.cloud.google.com/iam-admin/serviceaccounts
2. **Click "Create Service Account"**
3. **Service account details:**
   - **Name**: `hikethere-storage-sa`
   - **Description**: `Service account for HikeThere file uploads`
4. **Click "Create and Continue"**
5. **Grant roles:**
   - Add role: `Storage Object Admin` (for upload/delete)
6. **Click "Continue"** → **"Done"**

## Step 4: Create Service Account Key

1. Click on the service account you just created
2. Go to **"Keys"** tab
3. Click **"Add Key"** → **"Create new key"**
4. **Key type**: `JSON`
5. **Click "Create"** - This downloads a JSON file
6. **IMPORTANT**: Save this file securely - you can't download it again!

## Step 5: Encode JSON Key to Base64

Run this in your HikeThere directory:

```powershell
# Replace with your actual downloaded key file path
$keyPath = "path\to\your\downloaded-key.json"
$base64 = [Convert]::ToBase64String([System.IO.File]::ReadAllBytes($keyPath))
$base64 | Set-Clipboard
Write-Host "Base64 key copied to clipboard!"
```

## Step 6: Add Railway Environment Variables

Go to Railway Dashboard → Your HikeThere service → Variables tab

Add these variables:

```
FILESYSTEM_DISK=gcs
GCS_BUCKET=hikethere-storage
GCS_PROJECT_ID=your-gcp-project-id
GCS_KEY_FILE_CONTENT=<paste-base64-from-clipboard>
```

**To find your GCP Project ID:**
- Go to Google Cloud Console home
- Look at the top of the page - your project name and ID are displayed
- Or copy from the JSON key file (look for `"project_id"` field)

## Step 7: Verify Configuration

After Railway redeploys with the new variables:

1. Visit your app: https://hikethere-production.up.railway.app
2. Try uploading a trail image or profile picture
3. Check your GCS bucket to see if files appear
4. Try viewing the uploaded image in your app

## Troubleshooting

**If uploads fail:**
1. Check Railway logs for GCS errors
2. Verify service account has `Storage Object Admin` role
3. Verify bucket name matches `GCS_BUCKET` variable
4. Verify base64 key is correctly copied (no line breaks)

**If images don't display:**
1. Check bucket has public read access (`allUsers` with `Storage Object Viewer`)
2. Check Laravel config: `php artisan config:cache` was run
3. Check browser console for CORS errors

## Cost Estimate

- **Storage**: ~$0.02/GB per month
- **Operations**: ~$0.004 per 10,000 operations
- **Network egress**: First 1GB free, then ~$0.12/GB

**Expected monthly cost for small app**: $1-5 💰

## Security Notes

- ✅ Never commit the JSON key file to Git
- ✅ Use base64 encoding for Railway environment variable
- ✅ Service account has minimal permissions (only Storage Object Admin)
- ✅ Bucket is public for reads only (not writes)
- ✅ Laravel validates and controls all uploads

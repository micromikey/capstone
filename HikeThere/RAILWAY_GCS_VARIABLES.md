# 🚂 Railway Environment Variables Setup

## 📋 Variables to Add

Go to your Railway dashboard:
https://railway.app/project/YOUR_PROJECT_ID/service/YOUR_SERVICE_ID/variables

Add these environment variables:

### 1. FILESYSTEM_DISK
```
FILESYSTEM_DISK=gcs
```
This tells Laravel to use Google Cloud Storage instead of local storage.

---

### 2. GCS_PROJECT_ID
```
GCS_PROJECT_ID=hikethere
```
Your Google Cloud project ID.

---

### 3. GCS_BUCKET
```
GCS_BUCKET=hikethere-storage
```
Your Google Cloud Storage bucket name.

---

### 4. GCS_KEY_FILE_CONTENT
```
GCS_KEY_FILE_CONTENT=<YOUR_BASE64_STRING>
```

**📋 The base64 string is in your clipboard and saved to:**
`C:\Users\Michael Torres\Downloads\gcs-key-base64.txt`

**To paste it:**
1. Click in the value field
2. Press `Ctrl + V`
3. The entire base64 string will be pasted (3148 characters)

---

## 🪣 Make Bucket Public

Before deploying, make sure your bucket allows public read access.

### Option 1: Using gcloud CLI (Recommended)

```bash
gsutil iam ch allUsers:objectViewer gs://hikethere-storage
```

### Option 2: Using Google Cloud Console

1. Go to: https://console.cloud.google.com/storage/browser/hikethere-storage?project=hikethere
2. Click **Permissions** tab
3. Click **GRANT ACCESS**
4. New principal: `allUsers`
5. Role: **Storage Object Viewer**
6. Click **SAVE**

---

## ✅ After Adding Variables

1. Railway will automatically restart your service
2. Wait ~2-3 minutes for deployment
3. Test by uploading a profile picture
4. Profile picture URLs will now be:
   ```
   https://storage.googleapis.com/hikethere-storage/profile-pictures/xyz.jpg
   ```

---

## 🧪 Testing

Once deployed, test:

1. **Upload Profile Picture:**
   - Go to your profile
   - Upload a new profile picture
   - Check browser Network tab
   - URL should be `storage.googleapis.com`

2. **View Profile Picture:**
   - Refresh the page
   - Profile picture should load from GCS
   - Right-click → "Open image in new tab"
   - URL should be: `https://storage.googleapis.com/hikethere-storage/profile-pictures/...`

3. **Delete Profile Picture:**
   - Delete your profile picture
   - File should be removed from GCS bucket
   - Default avatar should show

---

## 🔍 Verify Deployment

Check Railway logs for successful GCS connection:
```
✓ GCS disk configured
✓ Using GCS for file storage
```

If you see errors like "Could not authenticate", verify:
- GCS_KEY_FILE_CONTENT is correct
- No extra spaces or line breaks
- Base64 string is complete

---

## 📸 Expected Result

**Before (❌ 404 errors):**
```
https://hikethere-production.up.railway.app/storage/profile-pictures/xyz.jpg
❌ 404 Not Found (Railway ephemeral storage)
```

**After (✅ Works!):**
```
https://storage.googleapis.com/hikethere-storage/profile-pictures/xyz.jpg
✅ 200 OK (Persistent GCS storage)
```

---

## 🎯 Summary

| Variable | Value | Purpose |
|----------|-------|---------|
| `FILESYSTEM_DISK` | `gcs` | Use GCS instead of local |
| `GCS_PROJECT_ID` | `hikethere` | Your GC project |
| `GCS_BUCKET` | `hikethere-storage` | Bucket name |
| `GCS_KEY_FILE_CONTENT` | `<base64>` | Service account credentials |

✅ Code changes committed  
⏳ Waiting for you to add Railway variables  
🚀 Then push and deploy!

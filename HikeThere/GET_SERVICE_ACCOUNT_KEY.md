# 🔑 Getting Your Service Account Key for GCS

## Step-by-Step Guide

### 1. Go to Service Accounts Page
Visit: https://console.cloud.google.com/iam-admin/serviceaccounts?project=hikethere

### 2. Find Your Service Account
Look for: `hikethere-railway@hikethere.iam.gserviceaccount.com`

### 3. Create/Download Key

**Option A: If you already have the key**
- Find the JSON file you downloaded when creating the service account
- It should be named something like: `hikethere-xxxxx.json`

**Option B: If you need a new key**
1. Click on the service account email
2. Go to the **KEYS** tab
3. Click **ADD KEY** → **Create new key**
4. Select **JSON** format
5. Click **CREATE**
6. Save the downloaded JSON file

### 4. Once You Have the JSON File

**DO NOT share the raw JSON file publicly!** 

Instead, we'll base64 encode it for Railway. Run this in PowerShell:

```powershell
# Replace the path with your actual JSON file location
$jsonPath = "C:\path\to\your\hikethere-xxxxx.json"
$base64 = [Convert]::ToBase64String([System.IO.File]::ReadAllBytes($jsonPath))
Write-Output $base64
```

Copy the base64 output - this is what we'll use in Railway.

---

## 🪣 Make Bucket Public

Your bucket needs to allow public read access. Run this:

### Option 1: Using gcloud CLI (Recommended)
```bash
gsutil iam ch allUsers:objectViewer gs://hikethere-storage
```

### Option 2: Using Console (if you prefer UI)
1. Go to https://console.cloud.google.com/storage/browser/hikethere-storage?project=hikethere
2. Click **Permissions** tab
3. Click **GRANT ACCESS**
4. Enter principal: `allUsers`
5. Select role: **Storage Object Viewer**
6. Click **SAVE**

---

## ✅ What to Provide Me

Once you have:
1. **Base64 encoded service account key** (from PowerShell command above)
2. Confirmation that bucket is public

I'll immediately update the code and deploy!

---

## 🚨 Security Note

The base64 encoded key is safe to share with me as it will only be stored in Railway's encrypted environment variables. Never commit the raw JSON to git!

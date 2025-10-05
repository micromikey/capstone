# 🚀 Quick GCS Migration Guide
**Current Status:** Profile pictures stored locally at `/storage/profile-pictures/`  
**Target:** Move to Google Cloud Storage bucket `hikethere-storage`

---

## 📦 What You Need

1. **Service Account JSON Key** from Google Cloud
2. **Google Cloud Project ID**
3. **Bucket Name:** `hikethere-storage` (already exists)

---

## ⚡ Quick Setup (3 Steps)

### Step 1: Provide Service Account Details

**Do you have:**
- [ ] Service account JSON file?
- [ ] Google Cloud Project ID?

If NO:
1. Go to https://console.cloud.google.com/
2. IAM & Admin → Service Accounts → Create
3. Grant "Storage Admin" role
4. Download JSON key

---

### Step 2: I'll Update the Code

I need to update these files:
1. `app/Models/User.php` - Profile picture URL accessor
2. `app/Http/Controllers/ProfileController.php` - Upload logic
3. `.env` - Add GCS config
4. Railway environment variables

---

### Step 3: Deploy

After code changes:
1. Commit and push
2. Railway auto-deploys
3. Test profile picture upload
4. ✅ Images now served from GCS!

---

## 🔧 Code Changes Preview

### User.php - Profile Picture URL
```php
// BEFORE (returns /storage/...)
return asset('storage/' . $this->profile_picture);

// AFTER (returns https://storage.googleapis.com/...)
return Storage::disk('gcs')->url($this->profile_picture);
```

### ProfileController.php - Upload
```php
// BEFORE (saves to local public disk)
$path = $request->file('profile_picture')->store('profile-pictures', 'public');

// AFTER (saves to GCS)
$path = $request->file('profile_picture')->store('profile-pictures', 'gcs');
```

---

## 📊 Current Files Using Profile Pictures

Found these files that need updating:

| File | Usage | Status |
|------|-------|--------|
| `app/Models/User.php` | Profile picture URL accessor | ⚠️ Needs update |
| `app/Http/Controllers/ProfileController.php` | Upload handler | ⚠️ Needs update |
| `app/Http/Controllers/CommunityController.php` | Display profile pictures | ✅ Uses accessor |

---

## ⚠️ Important Notes

### URLs Will Change

**Current URL Format:**
```
https://hikethere-production.up.railway.app/storage/profile-pictures/xyz.jpg
```

**New URL Format:**
```
https://storage.googleapis.com/hikethere-storage/profile-pictures/xyz.jpg
```

### Existing Files

Current profile pictures in local storage won't automatically migrate. Options:
1. **Option A:** Let users re-upload (simplest)
2. **Option B:** Run migration script to copy to GCS
3. **Option C:** Support both temporarily (check GCS first, fallback to local)

---

## 🎯 Ready to Proceed?

**Tell me:**
1. Do you have the service account JSON key?
2. What's your Google Cloud Project ID?
3. Do you want to migrate existing profile pictures or start fresh?

Once you provide these, I'll:
1. ✅ Update all the code
2. ✅ Configure environment variables
3. ✅ Test the setup
4. ✅ Deploy to Railway

---

**Quick Answer Template:**
```
1. Service account JSON: [Yes/No - can send separately]
2. Project ID: [your-project-id]
3. Migrate existing files: [Yes/No]
```

# ☁️ Google Cloud Storage Setup Guide

Complete guide to integrate Google Cloud Storage with your HikeThere Laravel application for production file storage.

## 📋 Overview

This guide covers:
1. Setting up Google Cloud Platform project
2. Creating and configuring a storage bucket
3. Creating service account and credentials
4. Installing Laravel GCS package
5. Configuring Laravel filesystem
6. Testing the integration
7. Migration from local storage

---

## 🚀 Step 1: Google Cloud Platform Setup

### 1.1 Create a GCP Project

1. Go to https://console.cloud.google.com
2. Click "Select a Project" → "New Project"
3. Project name: `HikeThere` (or your preferred name)
4. Click "Create"
5. Note your Project ID (e.g., `hikethere-123456`)

### 1.2 Enable Cloud Storage API

1. Go to APIs & Services → Library
2. Search for "Cloud Storage API"
3. Click "Enable"

### 1.3 Enable Billing

1. Go to Billing
2. Link a billing account
3. Note: Google offers $300 free credit for new accounts

---

## 🪣 Step 2: Create Storage Bucket

### Option A: Using GCP Console

1. Go to Cloud Storage → Buckets
2. Click "Create Bucket"

**Configure bucket:**
- **Name:** `hikethere-production` (must be globally unique)
- **Location type:** Region
- **Region:** `asia-southeast1` (Singapore - closest to Philippines)
- **Storage class:** Standard
- **Access control:** Fine-grained (Uniform can also work)
- **Protection tools:** Enable versioning (recommended)
- **Encryption:** Google-managed key

3. Click "Create"

### Option B: Using gcloud CLI

```bash
# Install Google Cloud SDK first
# Then authenticate
gcloud auth login

# Set your project
gcloud config set project YOUR_PROJECT_ID

# Create bucket
gsutil mb -p YOUR_PROJECT_ID \
  -c STANDARD \
  -l asia-southeast1 \
  gs://hikethere-production

# Enable versioning (recommended)
gsutil versioning set on gs://hikethere-production
```

---

## 🔐 Step 3: Configure Bucket Permissions

### 3.1 Set CORS Policy

Create `cors.json`:
```json
[
  {
    "origin": ["https://your-domain.com", "https://*.railway.app"],
    "method": ["GET", "HEAD", "PUT", "POST", "DELETE"],
    "responseHeader": ["Content-Type", "Access-Control-Allow-Origin"],
    "maxAgeSeconds": 3600
  }
]
```

Apply CORS:
```bash
gsutil cors set cors.json gs://hikethere-production
```

### 3.2 Make Public (for publicly accessible files)

```bash
# Make all objects publicly readable
gsutil iam ch allUsers:objectViewer gs://hikethere-production

# Or set default ACL
gsutil defacl set public-read gs://hikethere-production
```

**Note:** If you need private files with signed URLs, skip this step and configure per-object permissions.

---

## 👤 Step 4: Create Service Account

### 4.1 Create Service Account

1. Go to IAM & Admin → Service Accounts
2. Click "Create Service Account"

**Configuration:**
- **Name:** `hikethere-railway`
- **Description:** Service account for HikeThere Laravel app on Railway
- **Role:** Storage Admin (full access to Cloud Storage)

3. Click "Create and Continue"
4. Click "Done"

### 4.2 Create Key File

1. Find your new service account in the list
2. Click the three dots (⋮) → "Manage Keys"
3. Click "Add Key" → "Create New Key"
4. Choose "JSON"
5. Click "Create"
6. **Save the downloaded JSON file securely!**

**Example key file structure:**
```json
{
  "type": "service_account",
  "project_id": "hikethere-123456",
  "private_key_id": "abc123...",
  "private_key": "-----BEGIN PRIVATE KEY-----\n...\n-----END PRIVATE KEY-----\n",
  "client_email": "hikethere-railway@hikethere-123456.iam.gserviceaccount.com",
  "client_id": "1234567890",
  "auth_uri": "https://accounts.google.com/o/oauth2/auth",
  "token_uri": "https://oauth2.googleapis.com/token",
  "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
  "client_x509_cert_url": "..."
}
```

---

## 📦 Step 5: Install Laravel GCS Package

### 5.1 Install Package

```bash
composer require google/cloud-storage
composer require superbalist/laravel-google-cloud-storage
```

### 5.2 Register Service Provider (Laravel 11+)

The package should auto-register via package discovery. Verify in `composer.json`:

```json
{
  "extra": {
    "laravel": {
      "providers": [
        "Superbalist\\LaravelGoogleCloudStorage\\GoogleCloudStorageServiceProvider"
      ]
    }
  }
}
```

---

## ⚙️ Step 6: Configure Laravel

### 6.1 Update Environment Variables

Add to `.env`:
```env
FILESYSTEM_DISK=gcs
GOOGLE_CLOUD_PROJECT_ID=hikethere-123456
GOOGLE_CLOUD_KEY_FILE=/app/storage/gcs-credentials.json
GOOGLE_CLOUD_STORAGE_BUCKET=hikethere-production
GOOGLE_CLOUD_STORAGE_PATH_PREFIX=
```

### 6.2 Filesystem Configuration

This is already added in `config/filesystems.php` on the `railway-deployment` branch:

```php
'gcs' => [
    'driver' => 'gcs',
    'project_id' => env('GOOGLE_CLOUD_PROJECT_ID'),
    'key_file' => env('GOOGLE_CLOUD_KEY_FILE'),
    'bucket' => env('GOOGLE_CLOUD_STORAGE_BUCKET'),
    'path_prefix' => env('GOOGLE_CLOUD_STORAGE_PATH_PREFIX', ''),
    'storage_api_uri' => env('GOOGLE_CLOUD_STORAGE_API_URI'),
    'visibility' => 'public',
],
```

### 6.3 Store Credentials in Railway

**Option A: As File (Recommended)**

1. Go to Railway → Your Service → Variables
2. Click "Raw Editor"
3. Add:
```env
GOOGLE_CLOUD_KEY_FILE=/app/storage/gcs-credentials.json
```
4. Then use Railway's file storage or mount feature to add the JSON file

**Option B: As Environment Variable**

1. Base64 encode the JSON file:
```bash
base64 -i gcs-key.json -o gcs-key-base64.txt
```

2. Add to Railway environment variables:
```env
GOOGLE_CLOUD_KEY_FILE_BASE64=<paste base64 content>
```

3. Create a startup script to decode and save:
```bash
# In your Procfile or start script
echo $GOOGLE_CLOUD_KEY_FILE_BASE64 | base64 -d > /app/storage/gcs-credentials.json
```

**Option C: Use Environment Variables Directly (Most Secure for Railway)**

Instead of a key file, use individual environment variables:

```env
GOOGLE_CLOUD_PROJECT_ID=your-project-id
GOOGLE_CLOUD_CLIENT_EMAIL=your-service-account@project.iam.gserviceaccount.com
GOOGLE_CLOUD_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----\n...\n-----END PRIVATE KEY-----\n"
GOOGLE_CLOUD_STORAGE_BUCKET=hikethere-production
```

Update `config/filesystems.php`:
```php
'gcs' => [
    'driver' => 'gcs',
    'project_id' => env('GOOGLE_CLOUD_PROJECT_ID'),
    'key_file' => [
        'type' => 'service_account',
        'project_id' => env('GOOGLE_CLOUD_PROJECT_ID'),
        'private_key' => env('GOOGLE_CLOUD_PRIVATE_KEY'),
        'client_email' => env('GOOGLE_CLOUD_CLIENT_EMAIL'),
    ],
    'bucket' => env('GOOGLE_CLOUD_STORAGE_BUCKET'),
    'path_prefix' => env('GOOGLE_CLOUD_STORAGE_PATH_PREFIX', ''),
    'visibility' => 'public',
],
```

---

## 🧪 Step 7: Test the Integration

### 7.1 Test Locally

Create a test file `test_gcs.php`:

```php
<?php

require __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\Storage;

// Test upload
Storage::disk('gcs')->put('test/hello.txt', 'Hello from HikeThere!');

// Test read
$content = Storage::disk('gcs')->get('test/hello.txt');
echo "Content: " . $content . "\n";

// Test URL
$url = Storage::disk('gcs')->url('test/hello.txt');
echo "URL: " . $url . "\n";

// Test exists
$exists = Storage::disk('gcs')->exists('test/hello.txt');
echo "Exists: " . ($exists ? 'Yes' : 'No') . "\n";

// Test delete
Storage::disk('gcs')->delete('test/hello.txt');
echo "Deleted successfully\n";
```

Run:
```bash
php artisan tinker

# Or directly
Storage::disk('gcs')->put('test.txt', 'Hello World');
Storage::disk('gcs')->url('test.txt');
```

### 7.2 Test in Browser

Upload a profile picture or trail image through your application and verify it appears in GCS bucket.

---

## 🔄 Step 8: Migrate Existing Files

### 8.1 Create Migration Script

Create `artisan` command:

```bash
php artisan make:command MigrateToGCS
```

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateToGCS extends Command
{
    protected $signature = 'storage:migrate-to-gcs {--dry-run}';
    protected $description = 'Migrate files from local storage to Google Cloud Storage';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $files = Storage::disk('public')->allFiles();
        
        $this->info("Found " . count($files) . " files to migrate");
        
        $bar = $this->output->createProgressBar(count($files));
        $bar->start();
        
        foreach ($files as $file) {
            if (!$dryRun) {
                $content = Storage::disk('public')->get($file);
                Storage::disk('gcs')->put($file, $content);
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info("Migration complete!");
    }
}
```

Run migration:
```bash
# Test first
php artisan storage:migrate-to-gcs --dry-run

# Actual migration
php artisan storage:migrate-to-gcs
```

---

## 🛠️ Step 9: Update Application Code

### 9.1 No Code Changes Needed!

Your existing code already uses the Storage facade:

```php
// Upload
$path = $request->file('image')->store('images', 'public');
// With GCS, this automatically goes to Google Cloud Storage

// Get URL
$url = Storage::url($path);
// Returns GCS public URL

// Delete
Storage::delete($path);
// Deletes from GCS
```

### 9.2 For Specific Disk Usage

If you explicitly use `Storage::disk('public')`, you have two options:

**Option A: Update to use default disk**
```php
// Change from:
Storage::disk('public')->put($path, $content);

// To:
Storage::put($path, $content);
```

**Option B: Keep code as-is and alias 'public' to 'gcs'**

In `config/filesystems.php`:
```php
'default' => env('FILESYSTEM_DISK', 'local'),

'disks' => [
    'public' => env('APP_ENV') === 'production' 
        ? config('filesystems.disks.gcs') 
        : [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            // ...
        ],
    // ...
],
```

---

## 🔒 Step 10: Security Best Practices

### 10.1 Private Files with Signed URLs

For files that shouldn't be public:

```php
// Store privately
Storage::disk('gcs')->put('private/invoice.pdf', $content, 'private');

// Generate signed URL (expires in 1 hour)
$url = Storage::disk('gcs')->temporaryUrl(
    'private/invoice.pdf',
    now()->addHour()
);
```

### 10.2 Bucket Permissions

- ✅ Use least privilege principle
- ✅ Don't make entire bucket public if not needed
- ✅ Use IAM roles instead of ACLs
- ✅ Enable versioning for backups
- ✅ Set lifecycle rules to delete old versions

### 10.3 Credential Security

- ✅ Never commit credentials to Git
- ✅ Use Railway's secret management
- ✅ Rotate credentials regularly
- ✅ Monitor unusual access patterns

---

## 📊 Step 11: Monitoring and Costs

### 11.1 View Usage

```bash
# List all files
gsutil ls -r gs://hikethere-production

# Check bucket size
gsutil du -sh gs://hikethere-production

# View bucket info
gsutil ls -L -b gs://hikethere-production
```

### 11.2 Cost Estimation

**Storage Costs (asia-southeast1):**
- First 5 GB/month: Free
- $0.020 per GB/month after that

**Network Costs:**
- Egress to same region: Free
- Egress to worldwide: $0.12 per GB

**Operations:**
- Class A (write): $0.05 per 10,000 operations
- Class B (read): $0.004 per 10,000 operations

**Example Monthly Cost:**
- 20 GB storage: $0.30
- 100 GB egress: $12.00
- 100k reads: $0.04
- **Total: ~$12.34/month**

### 11.3 Set Budget Alerts

1. Go to Billing → Budgets & alerts
2. Create budget
3. Set alert at 50%, 90%, 100% of budget
4. Add email notifications

---

## 🚨 Troubleshooting

### Error: "Could not load the default credentials"

**Solution:** Check your credentials file path and format.

```bash
# Verify JSON is valid
cat gcs-credentials.json | jq .

# Check environment variable
echo $GOOGLE_CLOUD_KEY_FILE
```

### Error: "403 Forbidden"

**Solution:** Check service account permissions.

1. Go to IAM & Admin → IAM
2. Find your service account
3. Ensure it has "Storage Admin" role
4. Try "Storage Object Admin" if Storage Admin doesn't work

### Error: "Bucket not found"

**Solution:** Verify bucket name and project ID.

```bash
# List your buckets
gsutil ls

# Check project ID
gcloud config get-value project
```

### Files Not Appearing in Bucket

**Solution:** Check disk configuration.

```php
// In tinker
config('filesystems.default');
config('filesystems.disks.gcs');

// Test write
Storage::disk('gcs')->put('debug.txt', 'test');
```

---

## ✅ Verification Checklist

Before going to production:

- [ ] GCP project created and billing enabled
- [ ] Storage bucket created in appropriate region
- [ ] CORS configured for your domain
- [ ] Service account created with Storage Admin role
- [ ] Credentials JSON downloaded and secured
- [ ] Laravel GCS package installed
- [ ] Filesystem configuration updated
- [ ] Environment variables set in Railway
- [ ] Local testing successful
- [ ] File upload tested in staging
- [ ] File URLs accessible
- [ ] Delete operations working
- [ ] Existing files migrated (if applicable)
- [ ] Monitoring and alerts configured
- [ ] Backup strategy in place

---

## 📚 Additional Resources

- [Google Cloud Storage Documentation](https://cloud.google.com/storage/docs)
- [Laravel Filesystem Documentation](https://laravel.com/docs/filesystem)
- [GCS Pricing Calculator](https://cloud.google.com/products/calculator)
- [superbalist/laravel-google-cloud-storage](https://github.com/Superbalist/laravel-google-cloud-storage)

---

**Your Google Cloud Storage is now ready for production! ☁️**

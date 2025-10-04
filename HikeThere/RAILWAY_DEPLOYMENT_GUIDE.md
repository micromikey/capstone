# 🚂 Railway Deployment Guide for HikeThere

This guide will walk you through deploying your HikeThere Laravel application to Railway with MySQL database and Google Cloud Storage.

## 📋 Prerequisites

Before starting, ensure you have:

- ✅ GitHub repository with your code pushed to `railway-deployment` branch
- ✅ Railway account (sign up at https://railway.app)
- ✅ Google Cloud Platform account with Storage enabled
- ✅ Production API keys for:
  - Google Maps API
  - OpenWeatherMap
  - OpenRouteService
  - PayMongo (live keys)

---

## 🚀 Step 1: Set Up Google Cloud Storage

### 1.1 Create a Storage Bucket

```bash
# Install Google Cloud SDK if not already installed
# Then authenticate
gcloud auth login

# Create a bucket (choose a unique name)
gsutil mb -p YOUR_PROJECT_ID -c STANDARD -l asia-southeast1 gs://hikethere-production

# Make bucket publicly readable for images
gsutil iam ch allUsers:objectViewer gs://hikethere-production

# Or configure CORS
gsutil cors set cors.json gs://hikethere-production
```

### 1.2 Create Service Account

1. Go to GCP Console → IAM & Admin → Service Accounts
2. Click "Create Service Account"
3. Name: `hikethere-railway`
4. Grant role: "Storage Admin"
5. Click "Create Key" → JSON
6. Download the JSON key file (keep it secure!)

### 1.3 Prepare the Key for Railway

The JSON key file will be added as an environment variable in Railway.

---

## 🚂 Step 2: Deploy to Railway

### 2.1 Create New Project

1. Go to https://railway.app
2. Click "New Project"
3. Select "Deploy from GitHub repo"
4. Authorize Railway to access your GitHub
5. Select your `capstone` repository
6. Select the `railway-deployment` branch

### 2.2 Add MySQL Database

1. In your project, click "+ New"
2. Select "Database" → "Add MySQL"
3. Railway will automatically provision a MySQL database
4. Note: Railway will automatically inject these variables:
   - `MYSQL_HOST`
   - `MYSQL_PORT`
   - `MYSQL_DATABASE`
   - `MYSQL_USER`
   - `MYSQL_PASSWORD`

### 2.3 Deploy ML Service (Optional)

If you want to deploy the ML recommender separately on Railway:

1. Click "+ New" → "Empty Service"
2. Name it "ml-recommender"
3. Connect to the same GitHub repo
4. Set "Root Directory" to `ml-prototype`
5. Railway will detect the Dockerfile and deploy it

---

## ⚙️ Step 3: Configure Environment Variables

Go to your Laravel service → Variables tab and add these:

### Application Settings
```env
APP_NAME=HikeThere
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_URL=https://${{RAILWAY_PUBLIC_DOMAIN}}
APP_TIMEZONE=Asia/Manila
```

**Generate APP_KEY locally:**
```bash
php artisan key:generate --show
```

### Database (Use Railway's variables)
```env
DB_CONNECTION=mysql
DB_HOST=${{MYSQL_HOST}}
DB_PORT=${{MYSQL_PORT}}
DB_DATABASE=${{MYSQL_DATABASE}}
DB_USERNAME=${{MYSQL_USER}}
DB_PASSWORD=${{MYSQL_PASSWORD}}
```

### Filesystem (Google Cloud Storage)
```env
FILESYSTEM_DISK=gcs
GOOGLE_CLOUD_PROJECT_ID=your-gcp-project-id
GOOGLE_CLOUD_STORAGE_BUCKET=hikethere-production
GOOGLE_CLOUD_STORAGE_PATH_PREFIX=
GOOGLE_CLOUD_KEY_FILE=/app/storage/gcs-credentials.json
```

**For the GCS key file, you have two options:**

**Option A: Use base64 encoded credentials (Recommended)**
```env
GOOGLE_CLOUD_KEY_FILE_CONTENTS=<paste your entire JSON key here>
```

Then create an artisan command or modify your deployment script to write this to a file.

**Option B: Use Railway's File Storage**
Upload the JSON file as a Railway secret file.

### External API Keys
```env
GOOGLE_MAPS_API_KEY=your_production_google_maps_key
ORS_API_KEY=your_production_openrouteservice_key
OPENWEATHER_API_KEY=your_production_openweather_key
```

### Payment Gateway (PRODUCTION KEYS)
```env
PAYMONGO_PUBLIC_KEY=pk_live_your_production_public_key
PAYMONGO_SECRET_KEY=sk_live_your_production_secret_key
```

### Mail Configuration (Example: Gmail SMTP)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-gmail-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@hikethere.com
MAIL_FROM_NAME=HikeThere
```

**Note:** For Gmail, generate an App Password from your Google Account settings.

### Session & Cache
```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### Logging
```env
LOG_CHANNEL=stack
LOG_LEVEL=info
```

### ML Recommender Service
If deployed separately on Railway:
```env
ML_RECOMMENDER_URL=https://ml-recommender-production.railway.app
```

If using internal Railway networking:
```env
ML_RECOMMENDER_URL=http://ml-recommender.railway.internal:8001
```

---

## 🔧 Step 4: Configure Build and Deploy Commands

Railway should auto-detect your Laravel app using `nixpacks.toml`, but you can override:

### Build Command (if needed)
```bash
composer install --optimize-autoloader --no-dev && npm ci --only=production && npm run build
```

### Start Command (if needed)
```bash
php artisan migrate --force && php artisan storage:link && php artisan serve --host=0.0.0.0 --port=$PORT
```

---

## 🌐 Step 5: Set Up Custom Domain (Optional)

1. Go to your service → Settings → Networking
2. Click "Generate Domain" for a Railway subdomain
3. Or click "Custom Domain" to add your own domain
4. Update your `APP_URL` environment variable with the new domain
5. Railway automatically provisions SSL certificates

---

## 🔄 Step 6: Deploy and Verify

### 6.1 Trigger Deployment

Railway will automatically deploy when you push to the `railway-deployment` branch.

### 6.2 Monitor Deployment

1. Go to Deployments tab
2. Watch the build logs
3. Verify successful deployment

### 6.3 Check Application Health

Visit: `https://your-app.railway.app/up`

You should see a 200 OK response.

### 6.4 Verify Database

Railway will run migrations automatically via the start command. Check logs to ensure migrations succeeded.

### 6.5 Test File Uploads

Upload a profile picture or trail image to verify Google Cloud Storage is working.

---

## 📊 Step 7: Monitoring and Maintenance

### View Logs
```
Railway Dashboard → Your Service → Deployments → View Logs
```

### Database Access

Railway provides a PostgreSQL/MySQL client. Click on your database service to get connection details.

### Metrics

Railway automatically tracks:
- CPU usage
- Memory usage
- Network traffic
- Response times

### Scaling

Railway automatically scales based on load, but you can also:
1. Go to Settings
2. Adjust CPU/RAM limits
3. Enable horizontal scaling (Pro plan)

---

## 🔒 Security Best Practices

### 1. Environment Variables
✅ Never commit `.env` files  
✅ Use Railway's secret management  
✅ Rotate API keys regularly  

### 2. Database Security
✅ Railway databases are private by default  
✅ Use strong passwords (auto-generated)  
✅ Enable automated backups (in settings)  

### 3. Application Security
✅ Ensure `APP_DEBUG=false` in production  
✅ Security headers middleware is active  
✅ HTTPS is enforced by Railway  

### 4. Google Cloud Storage
✅ Use signed URLs for private files  
✅ Configure bucket permissions carefully  
✅ Enable versioning for backups  

---

## 🚨 Troubleshooting

### Build Fails

**Check build logs:**
```
Railway → Deployments → Failed Build → View Logs
```

**Common issues:**
- Missing Composer dependencies: Add to `composer.json`
- Node build errors: Check `package.json` scripts
- PHP version mismatch: Verify `nixpacks.toml`

### Application Errors (500)

**Check application logs:**
```
Railway → Service → Deployments → View Logs
```

**Common issues:**
- Missing `APP_KEY`: Generate and set in environment variables
- Database connection: Verify `DB_*` variables
- File permissions: Check storage directories

### Database Connection Issues

**Verify variables:**
```bash
# In Railway shell (click service → Connect)
echo $MYSQL_HOST
echo $MYSQL_DATABASE
```

**Test connection:**
```bash
php artisan db:show
```

### File Upload Issues (GCS)

**Check credentials:**
- Verify `GOOGLE_CLOUD_KEY_FILE` path
- Ensure service account has Storage Admin role
- Check bucket name and permissions

**Test locally:**
```bash
# Install GCS package
composer require google/cloud-storage

# Test in tinker
php artisan tinker
Storage::disk('gcs')->put('test.txt', 'Hello World');
```

### ML Service Not Responding

**Check ML service:**
- Verify it's deployed and running
- Check `ML_RECOMMENDER_URL` is correct
- Test endpoint: `curl https://your-ml-service/health`

---

## 🔄 Continuous Deployment

Railway automatically deploys when you push to your connected branch:

```bash
# Make changes locally
git add .
git commit -m "Update feature"
git push origin railway-deployment

# Railway automatically detects push and redeploys
```

### Rollback

If a deployment fails:
1. Go to Deployments
2. Find the last successful deployment
3. Click "Redeploy"

---

## 📈 Performance Optimization

### 1. Enable OPcache (Already configured in Dockerfile)

### 2. Configure Route Caching
```bash
# Run in production
php artisan route:cache
php artisan config:cache
php artisan view:cache
```

These are already in the build process via `nixpacks.toml`.

### 3. Use CDN for Assets

Consider using Cloudflare or similar CDN for:
- Static images
- CSS/JS files
- User-uploaded content

### 4. Database Optimization

- Add indexes to frequently queried columns
- Use eager loading to avoid N+1 queries
- Enable query caching

---

## 💰 Cost Estimation

Railway pricing (as of 2025):

**Hobby Plan ($5/month):**
- $5 credit/month
- Perfect for development/staging
- Includes 1GB RAM, 1 vCPU

**Pro Plan ($20/month):**
- $20 credit/month
- Production-ready
- Scales automatically
- Team collaboration

**Typical Monthly Cost:**
- Laravel App: ~$5-10
- MySQL Database: ~$5-10
- ML Service: ~$5-10
- **Total: $15-30/month**

**Google Cloud Storage:**
- First 5GB free
- $0.020 per GB after that
- Typical cost: $1-5/month

---

## 📞 Support

### Railway Support
- Documentation: https://docs.railway.app
- Discord: https://discord.gg/railway
- Status: https://status.railway.app

### Laravel Support
- Documentation: https://laravel.com/docs
- Laracasts: https://laracasts.com
- Community: https://laravel.io

---

## ✅ Deployment Checklist

Before going live, ensure:

- [ ] All environment variables configured
- [ ] `APP_DEBUG=false`
- [ ] Production database configured
- [ ] Google Cloud Storage working
- [ ] Email sending configured
- [ ] PayMongo production keys set
- [ ] Custom domain configured (if applicable)
- [ ] SSL certificate active
- [ ] Migrations run successfully
- [ ] File uploads tested
- [ ] Payment flow tested
- [ ] ML recommendations working
- [ ] All external APIs responding
- [ ] Monitoring and logging active
- [ ] Backup strategy implemented
- [ ] Team has access to Railway project

---

**Congratulations! Your HikeThere app is now deployed to Railway! 🎉**

For issues specific to your deployment, check the logs in Railway dashboard or reach out to the Railway community.

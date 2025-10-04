# 🚀 Production Deployment Checklist

Use this checklist to ensure your HikeThere application is fully prepared for production deployment.

---

## 📋 Pre-Deployment Checklist

### 🔐 Security Configuration

- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate new `APP_KEY` for production
- [ ] Review and update all `.env` variables
- [ ] Remove any test/development credentials
- [ ] Enable HTTPS (Railway provides automatically)
- [ ] Verify CSRF protection is active on all forms
- [ ] Check XSS protection in Blade templates
- [ ] Verify SQL injection prevention (Eloquent ORM)
- [ ] Security headers middleware is active
- [ ] Rate limiting configured on sensitive endpoints
- [ ] File upload validation is strict
- [ ] API authentication via Sanctum is working
- [ ] Session configuration is secure

### 🗄️ Database Configuration

- [ ] Production database credentials configured (Railway MySQL)
- [ ] Database connection tested
- [ ] All migrations tested on clean database
- [ ] Seeders reviewed (remove test data)
- [ ] Database backup strategy implemented
- [ ] Indexes added for frequently queried columns
- [ ] Foreign key constraints verified

### ☁️ File Storage (Google Cloud Storage)

- [ ] GCP project created and billing enabled
- [ ] Storage bucket created and configured
- [ ] Service account created with appropriate permissions
- [ ] Credentials JSON secured (not in version control)
- [ ] GCS Laravel package installed (`google/cloud-storage`)
- [ ] Filesystem configuration updated to use GCS
- [ ] CORS configured for your domain
- [ ] File upload tested in staging environment
- [ ] File URLs accessible publicly (or signed URLs working)
- [ ] Existing files migrated to GCS (if applicable)

### 🔑 API Keys & External Services

- [ ] Google Maps API key (production)
  - [ ] Billing enabled on GCP
  - [ ] API restrictions configured
  - [ ] HTTP referrer restrictions set
- [ ] OpenWeatherMap API key (production plan if needed)
- [ ] OpenRouteService API key (production)
- [ ] PayMongo production keys (not test keys!)
  - [ ] `PAYMONGO_PUBLIC_KEY=pk_live_...`
  - [ ] `PAYMONGO_SECRET_KEY=sk_live_...`
- [ ] Email service configured (SMTP/SendGrid/Mailgun)
  - [ ] Test emails being delivered
  - [ ] SPF/DKIM records configured for custom domain

### 🤖 Machine Learning Service

- [ ] ML service containerized (Dockerfile in `ml-prototype/`)
- [ ] ML service deployed (Railway or separate service)
- [ ] `ML_RECOMMENDER_URL` configured correctly
- [ ] ML service health check endpoint working
- [ ] ML model artifacts present in container
- [ ] Test recommendations working

### 🌐 Domain & Hosting (Railway)

- [ ] Railway project created
- [ ] GitHub repository connected
- [ ] `railway-deployment` branch selected
- [ ] MySQL database provisioned
- [ ] All environment variables configured
- [ ] Custom domain configured (optional)
- [ ] SSL certificate active
- [ ] Health check endpoint (`/up`) responding

### 📧 Email Configuration

- [ ] Mail driver configured (SMTP recommended)
- [ ] Test email sending
- [ ] Email templates reviewed
- [ ] Unsubscribe links working
- [ ] Notification preferences respected
- [ ] Queue worker for emails (optional but recommended)

### 🔄 Deployment Configuration

- [ ] `Dockerfile` present and tested
- [ ] `.dockerignore` configured
- [ ] `nixpacks.toml` or `Procfile` present
- [ ] `railway.json` configured
- [ ] Build commands verified
- [ ] Start commands tested
- [ ] Database migrations in start command
- [ ] `storage:link` in start command

### 🧪 Testing

- [ ] All critical user flows tested in staging
- [ ] Authentication flows tested (registration, login, password reset)
- [ ] Payment flow tested with real payment methods
- [ ] File upload tested (profile pictures, trail images)
- [ ] Booking system tested end-to-end
- [ ] Email notifications tested
- [ ] ML recommendations tested
- [ ] Mobile responsiveness verified
- [ ] Cross-browser testing completed
- [ ] Load testing performed (optional)

### 📊 Monitoring & Logging

- [ ] Application logs configured (`LOG_CHANNEL=stack`)
- [ ] Error reporting set up (optional: Sentry, Bugsnag)
- [ ] Railway monitoring active
- [ ] Database query logging (for optimization)
- [ ] Performance monitoring (optional: New Relic, Scout)
- [ ] Uptime monitoring (optional: UptimeRobot, Pingdom)

### 🔧 Performance Optimization

- [ ] OPcache enabled (in Dockerfile)
- [ ] Route caching enabled (`php artisan route:cache`)
- [ ] Config caching enabled (`php artisan config:cache`)
- [ ] View caching enabled (`php artisan view:cache`)
- [ ] Assets compiled for production (`npm run build`)
- [ ] CDN configured for static assets (optional)
- [ ] Database queries optimized (eager loading, indexes)
- [ ] Image optimization enabled

### 📱 Application Configuration

- [ ] `APP_NAME` set to "HikeThere"
- [ ] `APP_URL` set to production domain
- [ ] `APP_TIMEZONE` set to "Asia/Manila"
- [ ] Session driver appropriate for production (`database`)
- [ ] Cache driver configured (`database` or `redis`)
- [ ] Queue driver configured (`database` or `redis`)
- [ ] Broadcasting configured if using real-time features

### 🔒 Compliance & Legal

- [ ] Privacy Policy accessible and up-to-date
- [ ] Terms and Conditions accessible
- [ ] Cookie consent implemented (if applicable)
- [ ] GDPR compliance (if targeting EU users)
- [ ] Data retention policy defined
- [ ] User data deletion process implemented

---

## 🚀 Deployment Day Checklist

### Before Deployment

- [ ] Announce maintenance window to users (if applicable)
- [ ] Backup current database (if migrating from existing app)
- [ ] Test deployment process in staging environment
- [ ] Prepare rollback plan
- [ ] Verify all team members have access to Railway
- [ ] Document deployment steps

### During Deployment

- [ ] Push code to `railway-deployment` branch
- [ ] Monitor Railway build logs
- [ ] Verify successful build
- [ ] Monitor deployment logs
- [ ] Check application starts successfully
- [ ] Verify database migrations complete
- [ ] Test health check endpoint

### Post-Deployment Verification

- [ ] Application accessible at production URL
- [ ] HTTPS working and valid certificate
- [ ] Homepage loads correctly
- [ ] User registration works
- [ ] User login works
- [ ] Email verification sends
- [ ] File uploads work
- [ ] Payment flow works (test with real payment)
- [ ] Database is accessible
- [ ] ML recommendations work
- [ ] All external APIs responding
- [ ] Mobile view renders correctly
- [ ] No console errors in browser

### Critical User Flows to Test

1. **New User Registration**
   - [ ] Can register new account
   - [ ] Receives verification email
   - [ ] Can verify email
   - [ ] Redirected to onboarding

2. **Booking Flow**
   - [ ] Can browse trails
   - [ ] Can view trail details
   - [ ] Can create booking
   - [ ] Can make payment
   - [ ] Receives booking confirmation email
   - [ ] Booking appears in dashboard

3. **Organization Features**
   - [ ] Organization can login
   - [ ] Can create/edit trails
   - [ ] Can view bookings
   - [ ] Can manage events
   - [ ] Receives booking notifications

4. **Admin Features**
   - [ ] Admin can login
   - [ ] Can approve organizations
   - [ ] Can view reports
   - [ ] Can manage users

---

## 🚨 Rollback Plan

If issues occur after deployment:

### Railway Rollback

1. Go to Railway Dashboard
2. Navigate to Deployments
3. Find the last stable deployment
4. Click "Redeploy"

### Database Rollback

```bash
# If migrations cause issues
php artisan migrate:rollback --step=1

# Restore from backup
# Use Railway's database backup or your backup strategy
```

### Code Rollback

```bash
# Revert to previous commit
git revert HEAD
git push origin railway-deployment

# Or force push previous commit
git reset --hard PREVIOUS_COMMIT_HASH
git push -f origin railway-deployment
```

---

## 📞 Emergency Contacts

**Technical Issues:**
- Railway Status: https://status.railway.app
- Railway Support: support@railway.app
- Railway Discord: https://discord.gg/railway

**Service Issues:**
- Google Cloud Status: https://status.cloud.google.com
- PayMongo Support: support@paymongo.com

**Team Contacts:**
- Lead Developer: [Your contact]
- DevOps: [Your contact]
- Product Owner: [Your contact]

---

## 📊 Monitoring Dashboards

After deployment, monitor these:

### Railway Dashboard
- CPU usage
- Memory usage
- Response times
- Error rates
- Deployment history

### Google Cloud Console
- Storage usage
- Egress bandwidth
- API requests
- Costs

### Application Metrics
- Active users
- Booking conversions
- Payment success rate
- API response times
- Error logs

---

## 🔄 Post-Deployment Tasks

### Immediate (First 24 Hours)

- [ ] Monitor error logs closely
- [ ] Watch performance metrics
- [ ] Respond to user feedback
- [ ] Fix any critical bugs
- [ ] Document any issues encountered

### First Week

- [ ] Set up automated backups
- [ ] Configure monitoring alerts
- [ ] Optimize slow queries
- [ ] Review and improve performance
- [ ] Gather user feedback

### First Month

- [ ] Analyze usage patterns
- [ ] Optimize costs (GCS, Railway)
- [ ] Plan scaling strategy
- [ ] Review security logs
- [ ] Update documentation

---

## ✅ Sign-Off

Before marking deployment as complete:

| Task | Completed By | Date | Notes |
|------|--------------|------|-------|
| Security Review | | | |
| Database Setup | | | |
| GCS Configuration | | | |
| API Keys Configured | | | |
| Testing Complete | | | |
| Deployment Successful | | | |
| Post-Deployment Verification | | | |
| Monitoring Active | | | |

---

## 📝 Notes & Issues

Document any issues encountered during deployment:

```
Date: 
Issue: 
Resolution: 
Time to resolve: 
```

---

**Deployment Date:** _______________
**Deployed By:** _______________
**Sign-off:** _______________

---

🎉 **Congratulations on your production deployment!** 🎉

Remember to:
- Monitor closely for the first 24-48 hours
- Respond quickly to any issues
- Document lessons learned
- Celebrate your success! 🍾

# Resend HTTP API Integration (SMTP Bypass)

## 🔴 Problem
Railway blocks outbound SMTP ports (465, 587), causing connection timeouts:
```
Connection could not be established with host "smtp.resend.com:587"
```

## ✅ Solution - Use Resend HTTP API

We've created a custom mail transport that uses Resend's HTTP API instead of SMTP. This bypasses Railway's port restrictions.

## 📁 Files Created

### 1. `app/Mail/ResendTransport.php`
Custom mail transport using Resend's REST API via HTTPS (port 443 - always open).

### 2. `app/Providers/ResendMailServiceProvider.php`
Service provider that registers the Resend transport with Laravel's mail system.

### 3. Updated Files:
- `bootstrap/providers.php` - Added ResendMailServiceProvider
- `config/services.php` - Added Resend API key configuration

## 🔧 Railway Variables (UPDATE THESE)

Go to **Railway Dashboard → Variables** and **REMOVE** the SMTP variables, then add these:

### Remove These:
```
❌ MAIL_HOST
❌ MAIL_PORT
❌ MAIL_USERNAME
❌ MAIL_PASSWORD
❌ MAIL_ENCRYPTION
```

### Add/Update These:
```
MAIL_MAILER=resend                    ← Change from smtp to resend
RESEND_API_KEY=re_xxxxxxxxxxxxx       ← Your Resend API key
MAIL_FROM_ADDRESS=onboarding@resend.dev
MAIL_FROM_NAME=HikeThere
```

## 📝 Summary of Changes

**What we changed:**
1. Created custom Resend transport using HTTP API (port 443)
2. Registered transport with Laravel's mail system
3. Added Resend configuration to services.php
4. Switched from SMTP (ports 465/587) to HTTP API (port 443)

**Why this works:**
- Port 443 (HTTPS) is never blocked
- Uses Resend's REST API directly
- Same email functionality, different delivery method

## 🚀 Deployment Steps

1. **Commit and push** (we'll do this next)
2. **Update Railway variables** (remove SMTP, add Resend API)
3. **Wait for redeploy** (1-2 minutes)
4. **Test registration** - email should work!

## 🧪 Testing

After Railway redeploys:

1. Go to: https://hikethere-production.up.railway.app/register
2. Fill in registration form
3. Submit
4. Check your email inbox ✅
5. Check Resend dashboard: https://resend.com/emails

## ✅ Expected Result

Registration emails (and all other emails) will send successfully via Resend's HTTP API, bypassing Railway's SMTP port restrictions.

## 🔍 Troubleshooting

### If emails still don't send:

**Check Railway logs:**
```bash
railway logs
```

**Look for:**
- "Resend API Error" messages
- Invalid API key errors
- From address not verified errors

**Common fixes:**
- Verify `RESEND_API_KEY` is correct (starts with `re_`)
- Ensure `MAIL_FROM_ADDRESS=onboarding@resend.dev` for testing
- Check Resend dashboard for API errors

## 💡 Why Railway Blocks SMTP

Cloud platforms often block SMTP ports to prevent spam/abuse. Using HTTP API is the recommended approach for Railway, Heroku, and similar platforms.

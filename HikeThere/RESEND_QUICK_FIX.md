# Resend Connection Timeout Fix

## ❌ Problem
```
Connection could not be established with host "ssl://smtp.resend.com:465": 
stream_socket_client(): Unable to connect to ssl://smtp.resend.com:465 (Connection timed out)
```

## ✅ Solution - Update Railway Variables

The SSL port 465 is being blocked. Use TLS port 587 instead:

### Go to Railway → Variables and UPDATE these:

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.resend.com
MAIL_PORT=587                    ← Change from 465 to 587
MAIL_USERNAME=resend
MAIL_PASSWORD=re_xxxxxxxxxxxxx   ← Your Resend API key
MAIL_ENCRYPTION=tls              ← Keep as tls (not ssl)
MAIL_FROM_ADDRESS=onboarding@resend.dev
MAIL_FROM_NAME=HikeThere
```

### Changes Made:
- **MAIL_PORT**: `587` (instead of 465)
- **MAIL_ENCRYPTION**: `tls` (TLS/STARTTLS protocol)

Port 587 uses STARTTLS which is more compatible with cloud hosting environments like Railway.

## 🔄 After Updating

1. Save the variable changes in Railway
2. Railway will automatically redeploy (1-2 minutes)
3. Try registering again
4. Check your email for verification

## 🧪 Alternative Ports (if 587 still times out)

Try these in order:

### Option 1: Port 2465 (Alternative SSL)
```
MAIL_PORT=2465
MAIL_ENCRYPTION=tls
```

### Option 2: Port 25 (Standard SMTP - least secure)
```
MAIL_PORT=25
MAIL_ENCRYPTION=tls
```

## 📊 Why This Happens

Railway's network may block outbound port 465 for security. Port 587 is the standard submission port and is usually open.

## ✅ Expected Result

After changing to port 587, registration should work and you'll receive the verification email within seconds.

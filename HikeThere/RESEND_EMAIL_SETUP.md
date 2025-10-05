# Resend Email Service Setup Guide

## ✅ Package Installation (DONE)
The `resend/resend-php` package has been added to your composer.json and installed.

## 📝 Railway Environment Variables

Add these variables to your Railway project:

### Required Variables:

1. **MAIL_MAILER**
   ```
   MAIL_MAILER=resend
   ```

2. **RESEND_API_KEY**
   ```
   RESEND_API_KEY=re_xxxxxxxxxxxxxxxxxxxxxxxxxxxxx
   ```
   Use the API key you got from Resend dashboard.

3. **MAIL_FROM_ADDRESS**
   ```
   MAIL_FROM_ADDRESS=noreply@yourdomain.com
   ```
   **Important**: Resend requires a verified domain. You need to either:
   - Use your own domain (hikethere.com) and verify it in Resend
   - OR use Resend's onboarding domain: `onboarding@resend.dev` (for testing only)

4. **MAIL_FROM_NAME**
   ```
   MAIL_FROM_NAME=HikeThere
   ```

### How to Add Variables to Railway:
1. Go to: https://railway.app/project/your-project
2. Click on your service (hikethere)
3. Go to "Variables" tab
4. Click "+ New Variable"
5. Add each variable above
6. Railway will automatically redeploy

## 🔧 Configuration File

Create a new mail transport configuration. Create this file:

**config/services.php** (add to existing file or create if missing):

```php
<?php

return [
    // ... other services ...

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],
];
```

## 📧 Create Custom Mail Driver

Since we're using the PHP SDK directly (not the Laravel package), we need to configure it manually.

**Step 1**: Create a custom mail transport service provider:

```php
// app/Providers/ResendMailServiceProvider.php
<?php

namespace App\Providers;

use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;
use Resend;

class ResendMailServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->extend(MailManager::class, function ($manager) {
            $manager->extend('resend', function () {
                return new \Illuminate\Mail\Transport\SesTransport(
                    new class (config('services.resend.key')) implements \Aws\Ses\SesClientInterface {
                        private $resend;

                        public function __construct($apiKey)
                        {
                            $this->resend = Resend::client($apiKey);
                        }

                        public function sendEmail($params)
                        {
                            $this->resend->emails->send([
                                'from' => $params['Source'],
                                'to' => $params['Destination']['ToAddresses'],
                                'subject' => $params['Message']['Subject']['Data'],
                                'html' => $params['Message']['Body']['Html']['Data'] ?? null,
                                'text' => $params['Message']['Body']['Text']['Data'] ?? null,
                            ]);

                            return new \Aws\Result(['MessageId' => uniqid()]);
                        }

                        // Other required interface methods...
                    }
                );
            });
            return $manager;
        });
    }
}
```

**Actually, there's a simpler approach**: Use Resend's API transport directly.

## ✅ SIMPLER APPROACH - Use SMTP

Resend provides SMTP relay that works with Laravel's built-in mail system:

### Railway Variables (SIMPLER):

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.resend.com
MAIL_PORT=465
MAIL_USERNAME=resend
MAIL_PASSWORD=re_xxxxxxxxxxxxxxxxxxxxxxxxxxxxx
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=onboarding@resend.dev
MAIL_FROM_NAME=HikeThere
```

**Note**: Use your Resend API key as the `MAIL_PASSWORD`.

This will work out of the box with Laravel 12 without any code changes!

## 🧪 Testing Email

After Railway redeploys, test email sending:

### Test 1: Password Reset
1. Go to: https://hikethere-production.up.railway.app/forgot-password
2. Enter your email
3. Click "Email Password Reset Link"
4. Check your email inbox

### Test 2: Artisan Command (optional)
```bash
# SSH into Railway or use local .env with Resend credentials
php artisan tinker

# Send test email
Mail::raw('This is a test email from HikeThere', function ($message) {
    $message->to('your-email@example.com')
            ->subject('Test Email');
});
```

### Test 3: Check Resend Dashboard
1. Go to: https://resend.com/emails
2. You should see sent emails in the list
3. Check delivery status, opens, clicks

## 🔍 Troubleshooting

### Error: "Domain not verified"
- You must verify your domain in Resend dashboard
- OR use `onboarding@resend.dev` for testing (limited to 100 emails)

### Error: "Authentication failed"
- Check that `MAIL_PASSWORD` is your Resend API key (starts with `re_`)
- Verify `MAIL_USERNAME=resend` (exactly as written)

### Error: "Connection timeout"
- Try changing `MAIL_PORT=587` and `MAIL_ENCRYPTION=tls`
- Or use `MAIL_PORT=465` and `MAIL_ENCRYPTION=ssl`

### Emails not being sent
1. Check Railway logs: `railway logs`
2. Enable debug mode temporarily: `APP_DEBUG=true`
3. Check Resend dashboard for blocked/failed sends
4. Verify environment variables are set correctly

## 📊 Production Checklist

- [ ] Add all environment variables to Railway
- [ ] Verify domain in Resend (or use onboarding domain)
- [ ] Test password reset email
- [ ] Test notification emails
- [ ] Check Resend dashboard for delivery
- [ ] Monitor email deliverability rates
- [ ] Set up email templates (optional)

## 💰 Pricing

**Resend Free Tier:**
- 3,000 emails/month
- 100 emails/day
- 1 verified domain

This should be enough for initial launch. Upgrade if you exceed limits.

## 📚 Where Emails Are Used in HikeThere

Your app likely sends emails for:
- Password resets (`/forgot-password`)
- Email verification (Jetstream)
- Booking confirmations
- Event notifications
- Emergency alerts
- User notifications

All of these will automatically use Resend once configured!

## 🚀 Next Steps

1. **Add variables to Railway** (SMTP approach above)
2. **Wait for redeploy** (automatic)
3. **Test password reset email**
4. **Verify domain** (if using custom domain)
5. **Monitor Resend dashboard**

---

**Need Help?**
- Resend Docs: https://resend.com/docs/send-with-smtp
- Resend API Keys: https://resend.com/api-keys
- Domain Verification: https://resend.com/domains

@component('mail::message')
# Welcome to HikeThere! 🏔️

## Thank You for Registering!

Dear {{ $organizationProfile->contact_person }},

Welcome to **HikeThere**! Thank you for registering **{{ $organizationProfile->organization_name }}** with our hiking community platform.

### Registration Received ✅

Your organization registration has been successfully submitted and is currently under review. Here are the details we received:

**Organization Name:** {{ $organizationProfile->organization_name }}

**Registration Number:** {{ $organizationProfile->registration_number }}

**Contact Email:** {{ $organizationProfile->contact_email }}

**Founded:** {{ $organizationProfile->founded_year }}

**Type:** {{ ucfirst($organizationProfile->organization_type) }}

### What Happens Next?

1. **Review Process**: Our team will review your application within 2-3 business days
2. **Verification**: We may contact you if additional information is needed
3. **Approval Notification**: You'll receive an email once your organization is approved
4. **Access Granted**: Upon approval, you'll gain full access to create and manage hiking events

### While You Wait

@component('mail::button', ['url' => route('home')])
Explore HikeThere
@endcomponent

Feel free to browse our community and see what other hiking organizations are up to!

@component('mail::panel')
**Need Help?**

If you have any questions about your registration or our platform, don't hesitate to reach out to our support team at {{ config('mail.from.address') }}.

**Registration ID:** {{ $organizationProfile->id }}
**Submitted:** {{ $organizationProfile->created_at->format('F j, Y \a\t g:i A') }}
@endcomponent

We're excited to have {{ $organizationProfile->organization_name }} join our growing community of outdoor enthusiasts!

Happy Hiking! 🥾<br>
The {{ config('app.name') }} Team
@endcomponent
@component('mail::message')
@if($approved)
# 🎉 Congratulations! Your Organization has been Approved!

Dear {{ $organizationProfile->name }},

We're excited to inform you that **{{ $organizationProfile->organization_name }}** has been approved to join the HikeThere community!

## What's Next?

Your organization can now:
- ✅ Log in to your account
- 🏔️ Create and manage hiking events
- 👥 Connect with hiking enthusiasts
- 📈 Build your community presence
- 🚀 Access all HikeThere features

@component('mail::button', ['url' => $loginUrl])
Login to Your Account
@endcomponent

## Account Details
**Organization:** {{ $organizationProfile->organization_name }}
**Email:** {{ $user->email }}
**Approved:** {{ now()->format('F j, Y \a\t g:i A') }}

@component('mail::panel')
🌟 **Welcome to the HikeThere Community!**

We're thrilled to have {{ $organizationProfile->organization_name }} as part of our growing network of outdoor enthusiasts and organizations.
@endcomponent

@else
# Organization Registration Update

Dear {{ $organizationProfile->name }},

Thank you for your interest in joining HikeThere. After careful review of your application for **{{ $organizationProfile->organization_name }}**, we regret to inform you that we cannot approve your registration at this time.

## Next Steps

If you believe this decision was made in error or if you'd like to address any concerns, please don't hesitate to contact our support team.

@component('mail::button', ['url' => 'mailto:' . config('mail.from.address')])
Contact Support
@endcomponent

@component('mail::panel')
**Organization:** {{ $organizationProfile->organization_name }}
**Decision Date:** {{ now()->format('F j, Y \a\t g:i A') }}
**Status:** Rejected
@endcomponent

@endif

@if($approved)
We look forward to seeing the amazing hiking experiences you'll create for the community!

Happy Hiking! 🥾
@else
Thank you for your understanding and interest in HikeThere.
@endif

Best regards,<br>
The {{ config('app.name') }} Team
@endcomponent
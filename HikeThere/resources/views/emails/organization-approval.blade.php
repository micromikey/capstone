@component('mail::message')
# Organization {{ $status === 'approved' ? 'Approved' : 'Rejected' }} - HikeThere

@if($status === 'approved')
## Congratulations! Your organization has been approved! 🎉

Dear {{ $organizationProfile->contact_person }},

We're excited to inform you that **{{ $organizationProfile->organization_name }}** has been approved to join the HikeThere community!

### What's Next?

Your organization can now:
- Create and manage hiking events
- Connect with hiking enthusiasts
- Build your community presence
- Access all HikeThere features

@component('mail::button', ['url' => route('organization.dashboard')])
Access Your Dashboard
@endcomponent

@else
## Organization Registration Update

Dear {{ $organizationProfile->contact_person }},

Thank you for your interest in joining HikeThere. After careful review, we regret to inform you that we cannot approve **{{ $organizationProfile->organization_name }}** at this time.

@if($remarks)
### Feedback:
{{ $remarks }}
@endif

### Next Steps
If you believe this decision was made in error or if you'd like to address any concerns, please don't hesitate to contact us.

@component('mail::button', ['url' => 'mailto:' . config('mail.from.address')])
Contact Support
@endcomponent

@endif

@component('mail::panel')
**Organization:** {{ $organizationProfile->organization_name }}

**Decision Date:** {{ now()->format('F j, Y \a\t g:i A') }}

**Status:** {{ ucfirst($status) }}

@if($remarks && $status === 'approved')
**Admin Notes:** {{ $remarks }}
@endif
@endcomponent

@if($status === 'approved')
We look forward to seeing the amazing hiking experiences you'll create for the community!
@else
Thank you for your understanding.
@endif

Best regards,<br>
The {{ config('app.name') }} Team
@endcomponent
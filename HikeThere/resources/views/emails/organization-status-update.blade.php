@component('mail::message')
# Organization Status Update

Dear {{ $organizationProfile->name }},

Your organization **{{ $organizationProfile->organization_name }}** status has been updated.

## Status Details

**Current Status:** {{ ucfirst($user->approval_status) }}

**Updated At:** {{ now()->format('F j, Y \a\t g:i A') }}

**Updated By:** {{ $updatedBy ?? 'System Administrator' }}

@if($user->approval_status === 'approved')
## Congratulations! 🎉

Your organization has been approved and is now active on HikeThere.

### What's Next?

You can now:
- ✅ Log in to your account
- 🏔️ Access the organization dashboard
- 👥 Create and manage hiking events
- 📈 Build your community presence

@component('mail::button', ['url' => route('login')])
Login to Your Account
@endcomponent

@elseif($user->approval_status === 'rejected')
## Status Update

Unfortunately, your organization registration could not be approved at this time.

If you believe this decision was made in error or if you'd like to address any concerns, please contact our support team.

@component('mail::button', ['url' => 'mailto:' . config('mail.from.address')])
Contact Support
@endcomponent
@endif

@if($user->approval_status === 'approved')
We look forward to seeing the amazing hiking experiences you'll create for the community!

Happy Hiking! 🥾
@else
Thank you for your understanding and interest in HikeThere.
@endif

Best regards,<br>
{{ config('app.name') }} Team
@endcomponent
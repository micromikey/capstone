@component('mail::message')
# Organization Status Update - HikeThere

## Organization Status Changed

The status for **{{ $organizationProfile->organization_name }}** has been updated.

### Organization Information
**Name:** {{ $organizationProfile->organization_name }}

**Contact:** {{ $organizationProfile->contact_person }} ({{ $organizationProfile->contact_email }})

**Registration #:** {{ $organizationProfile->registration_number }}

### Status Change
**Previous Status:** {{ ucfirst($previousStatus) }}

**New Status:** {{ ucfirst($currentStatus) }}

**Updated By:** {{ $updatedBy ?? 'System Administrator' }}

**Date:** {{ now()->format('F j, Y \a\t g:i A') }}

@if($remarks)
### Additional Notes:
{{ $remarks }}
@endif

@component('mail::button', ['url' => route('admin.organizations.show', $organizationProfile->id)])
View Organization Details
@endcomponent

@component('mail::panel')
This is an automated notification from the HikeThere organization management system.
@endcomponent

Best regards,<br>
{{ config('app.name') }} System
@endcomponent
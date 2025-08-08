@component('mail::message')
# New Organization Registration - HikeThere

A new hiking organization has registered and is awaiting approval.

## Organization Details

**Organization Name:** {{ $organizationProfile->organization_name }}

**Registration Number:** {{ $organizationProfile->registration_number }}

**Contact Person:** {{ $organizationProfile->contact_person }}

**Email:** {{ $organizationProfile->contact_email }}

**Phone:** {{ $organizationProfile->contact_phone }}

**Address:**
{{ $organizationProfile->address }}
{{ $organizationProfile->city }}, {{ $organizationProfile->state }} {{ $organizationProfile->postal_code }}
{{ $organizationProfile->country }}

@if($organizationProfile->website)
**Website:** {{ $organizationProfile->website }}
@endif

**Founded Year:** {{ $organizationProfile->founded_year }}

**Organization Type:** {{ ucfirst($organizationProfile->organization_type) }}

**Description:**
{{ $organizationProfile->description }}

## Action Required

Please review this organization registration and take appropriate action.

@component('mail::button', ['url' => route('admin.organizations.show', $organizationProfile->id)])
Review Organization
@endcomponent

@component('mail::panel')
**Registration Date:** {{ $organizationProfile->created_at->format('F j, Y \a\t g:i A') }}

**Status:** {{ ucfirst($organizationProfile->approval_status) }}
@endcomponent

Thanks,<br>
{{ config('app.name') }} System
@endcomponent
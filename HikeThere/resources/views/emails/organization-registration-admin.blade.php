@component('mail::message')
# New Organization Registration - HikeThere

A new hiking organization has registered and requires your approval.

## Organization Details

**Organization Name:** {{ $profile->organization_name }}

**Representative:** {{ $profile->name }}

**Email:** {{ $profile->email }}

**Phone:** {{ $profile->phone ?? 'Not provided' }}

**Description:**
{{ $profile->organization_description }}

## Documents Submitted

- Business Permit: Uploaded
- Government ID: Uploaded
@if($profile->additional_docs && count(json_decode($profile->additional_docs, true)) > 0)
- Additional Documents: {{ count(json_decode($profile->additional_docs, true)) }} file(s)
@endif

## Quick Actions

@component('mail::button', ['url' => route('organizations.approve.email', $user->id), 'color' => 'success'])
Approve Organization
@endcomponent

@component('mail::button', ['url' => route('organizations.reject.email', $user->id), 'color' => 'error'])
Reject Organization
@endcomponent

@component('mail::panel')
**Registration Details:**

Date: {{ $user->created_at->format('F j, Y \a\t g:i A') }}

User ID: {{ $user->id }}

Status: {{ ucfirst($user->approval_status) }}

**Important:** These approval links will work immediately when clicked.
@endcomponent

**Document Files:**
- Business Permit: {{ $profile->business_permit_path }}
- Government ID: {{ $profile->government_id_path }}

Thanks,<br>
{{ config('app.name') }} System
@endcomponent
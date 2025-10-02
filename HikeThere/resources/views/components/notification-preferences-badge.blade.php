{{-- 
    Notification Preferences Badge Component
    
    Shows a badge indicating which notification preferences are disabled
    Usage: @include('components.notification-preferences-badge', ['user' => Auth::user()])
--}}

@if($user && $user->preferences)
    @php
        $prefs = $user->preferences;
        $disabled = [];
        
        if (!$prefs->email_notifications) $disabled[] = 'Email';
        if (!$prefs->push_notifications) $disabled[] = 'Push';
        if (!$prefs->trail_updates) $disabled[] = 'Trail Updates';
        if (!$prefs->security_alerts) $disabled[] = 'Security';
        if ($prefs->newsletter) $enabled[] = 'Newsletter';
    @endphp
    
    @if(count($disabled) > 0)
        <div class="inline-flex items-center px-3 py-1 bg-yellow-100 border border-yellow-300 rounded-full text-xs font-medium text-yellow-800">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016zM12 9v2m0 4h.01"></path>
            </svg>
            <span>{{ count($disabled) }} notification type{{ count($disabled) > 1 ? 's' : '' }} disabled</span>
            <a href="{{ route('preferences.index') }}" class="ml-2 underline hover:text-yellow-900">Manage</a>
        </div>
    @endif
@endif

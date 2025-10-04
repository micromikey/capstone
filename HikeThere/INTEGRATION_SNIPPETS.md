# Integration Code Snippets

## 1. Community Dashboard (`resources/views/components/community-dashboard.blade.php`)

Add this BEFORE the final `@endpush` at the end of the file:

```php
{{-- Real-time Event Polling for Community Dashboard --}}
@if(Auth::check() && Auth::user()->user_type === 'hiker')
@vite(['resources/js/event-poller.js'])
<script type="module">
document.addEventListener('DOMContentLoaded', function() {
    const eventsTabButton = document.getElementById('tab-events');
    const eventsContainer = document.querySelector('#events .grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-3.gap-6');
    let pollingInitialized = false;
    
    function initializeEventPolling() {
        if (pollingInitialized || !eventsContainer) return;
        EventPoller.init({
            containerSelector: '#events .grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-3.gap-6',
            interval: 30000,
            notificationEnabled: true
        });
        pollingInitialized = true;
        EventPoller.requestNotificationPermission();
    }
    
    eventsTabButton?.addEventListener('click', () => setTimeout(initializeEventPolling, 100));
    if (eventsTabButton?.getAttribute('aria-selected') === 'true') initializeEventPolling();
});
</script>
@endif
```

## 2. Dashboard (`resources/views/components/dashboard.blade.php`)

Add this BEFORE the final closing `</script>` tag or at the end of the file:

```php
{{-- Real-time Event Polling for Dashboard --}}
@if(isset($user) && $user && $user->user_type === 'hiker')
@vite(['resources/js/event-poller.js'])
<script type="module">
document.addEventListener('DOMContentLoaded', function() {
    const eventsContainer = document.querySelector('.grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-3.gap-6.mb-8');
    if (eventsContainer) {
        EventPoller.init({
            containerSelector: '.grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-3.gap-6.mb-8',
            interval: 30000,
            notificationEnabled: true
        });
        EventPoller.requestNotificationPermission();
    }
});
</script>
@endif
```

## 3. Trail Show Page (`resources/views/trails/show.blade.php`)

Add this at the END of the file (before `</x-app-layout>` or `</body>`):

```php
{{-- Real-time Event Polling for Trail-Specific Events --}}
@if(Auth::check() && Auth::user()->user_type === 'hiker')
@vite(['resources/js/event-poller.js'])
<script type="module">
document.addEventListener('DOMContentLoaded', function() {
    const eventsContainer = document.querySelector('#trail-events-container, .events-container');
    if (eventsContainer) {
        EventPoller.init({
            containerSelector: '#trail-events-container, .events-container',
            trailId: {{ $trail->id ?? 'null' }},
            interval: 30000,
            notificationEnabled: true
        });
        EventPoller.requestNotificationPermission();
    }
});
</script>
@endif
```

## 4. Organization Event Create (`resources/views/org/events/create.blade.php`)

Already updated with AJAX! No changes needed.

---

## After Adding These Snippets:

1. Run: `npm run build`
2. Clear cache: `php artisan view:clear`
3. Test by creating an event as org user
4. Check hiker pages after 30 seconds

## Notes:

- The `@vite(['resources/js/event-poller.js'])` directive tells Laravel to compile the JS file
- The `type="module"` enables ES6 module syntax
- Each page has custom selectors matching their HTML structure
- Trail show page filters events by trail ID
- All polling is automatic and stops when page is hidden

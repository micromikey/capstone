@php
    // Get current route name
    $routeName = Route::currentRouteName();
    
    // Define breadcrumb structure based on route
    $breadcrumbs = [
        [
            'label' => 'Dashboard',
            'route' => 'org.dashboard',
            'icon' => '<svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>'
        ]
    ];
    
    // Add second level based on route
    if (str_starts_with($routeName, 'org.trails')) {
        $breadcrumbs[] = [
            'label' => 'Trails',
            'route' => 'org.trails.index',
            'active' => $routeName === 'org.trails.index'
        ];
        
        // Add third level for specific trail pages
        if ($routeName === 'org.trails.create') {
            $breadcrumbs[] = ['label' => 'Create New Trail', 'active' => true];
        } elseif ($routeName === 'org.trails.edit') {
            $breadcrumbs[] = ['label' => 'Edit Trail', 'active' => true];
        } elseif ($routeName === 'org.trails.show') {
            $breadcrumbs[] = ['label' => isset($currentPage) ? $currentPage : 'Trail Details', 'active' => true];
        }
    } elseif (str_starts_with($routeName, 'org.bookings')) {
        $breadcrumbs[] = [
            'label' => 'Bookings',
            'route' => 'org.bookings.index',
            'active' => $routeName === 'org.bookings.index'
        ];
        
        if ($routeName === 'org.bookings.show') {
            $breadcrumbs[] = ['label' => 'Booking Details', 'active' => true];
        }
    } elseif (str_starts_with($routeName, 'org.events')) {
        $breadcrumbs[] = [
            'label' => 'Events',
            'route' => 'org.events.index',
            'active' => $routeName === 'org.events.index'
        ];
        
        if ($routeName === 'org.events.create') {
            $breadcrumbs[] = ['label' => 'Create New Event', 'active' => true];
        } elseif ($routeName === 'org.events.edit') {
            $breadcrumbs[] = ['label' => 'Edit Event', 'active' => true];
        } elseif ($routeName === 'org.events.show') {
            $breadcrumbs[] = ['label' => 'Event Details', 'active' => true];
        }
    } elseif (str_starts_with($routeName, 'org.payment')) {
        $breadcrumbs[] = [
            'label' => 'Payment Setup',
            'route' => 'org.payment.index',
            'active' => true
        ];
    }
    
    // Override with custom current page if provided
    if (isset($currentPage)) {
        $breadcrumbs[count($breadcrumbs) - 1]['label'] = $currentPage;
    }
@endphp

<nav class="flex" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        @foreach($breadcrumbs as $index => $crumb)
            <li class="inline-flex items-center">
                @if($index === 0)
                    {{-- First item (Dashboard with icon) --}}
                    @if(isset($crumb['active']) && $crumb['active'])
                        <span class="inline-flex items-center text-sm font-medium text-gray-500">
                            {!! $crumb['icon'] !!}
                            {{ $crumb['label'] }}
                        </span>
                    @else
                        <a href="{{ route($crumb['route']) }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-[#336d66]">
                            {!! $crumb['icon'] !!}
                            {{ $crumb['label'] }}
                        </a>
                    @endif
                @else
                    {{-- Subsequent items --}}
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        @if(isset($crumb['active']) && $crumb['active'])
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2" aria-current="page">{{ $crumb['label'] }}</span>
                        @else
                            <a href="{{ route($crumb['route']) }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-[#336d66] md:ml-2">{{ $crumb['label'] }}</a>
                        @endif
                    </div>
                @endif
            </li>
        @endforeach
    </ol>
</nav>

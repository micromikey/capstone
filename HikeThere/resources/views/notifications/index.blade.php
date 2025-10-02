<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Notifications') }}
            </h2>
            <div class="flex space-x-2">
                @if($notifications->total() > 0)
                    <form method="POST" action="{{ route('notifications.read-all') }}" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Mark All as Read') }}
                        </button>
                    </form>
                    
                    <form method="POST" action="{{ route('notifications.destroy-read') }}" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete all read notifications?') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            {{ __('Clear Read') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success message will be shown via toast -->

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6 p-4">
                <div class="flex flex-wrap gap-4 items-center">
                    <div class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-700">{{ __('Filter:') }}</label>
                        <a href="{{ route('notifications.index', ['filter' => 'all']) }}" 
                           class="px-3 py-1 rounded-md text-sm font-medium {{ $filter === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ __('All') }}
                        </a>
                        <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" 
                           class="px-3 py-1 rounded-md text-sm font-medium {{ $filter === 'unread' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ __('Unread') }}
                        </a>
                        <a href="{{ route('notifications.index', ['filter' => 'read']) }}" 
                           class="px-3 py-1 rounded-md text-sm font-medium {{ $filter === 'read' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ __('Read') }}
                        </a>
                    </div>

                    <div class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-700">{{ __('Type:') }}</label>
                        <a href="{{ route('notifications.index', array_merge(request()->except('type'), ['filter' => $filter])) }}" 
                           class="px-3 py-1 rounded-md text-sm font-medium {{ !$type ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ __('All Types') }}
                        </a>
                        <a href="{{ route('notifications.index', ['filter' => $filter, 'type' => 'trail_update']) }}" 
                           class="px-3 py-1 rounded-md text-sm font-medium {{ $type === 'trail_update' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ __('Trail Updates') }}
                        </a>
                        <a href="{{ route('notifications.index', ['filter' => $filter, 'type' => 'security_alert']) }}" 
                           class="px-3 py-1 rounded-md text-sm font-medium {{ $type === 'security_alert' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ __('Security') }}
                        </a>
                        <a href="{{ route('notifications.index', ['filter' => $filter, 'type' => 'booking']) }}" 
                           class="px-3 py-1 rounded-md text-sm font-medium {{ $type === 'booking' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ __('Bookings') }}
                        </a>
                        <a href="{{ route('notifications.index', ['filter' => $filter, 'type' => 'weather']) }}" 
                           class="px-3 py-1 rounded-md text-sm font-medium {{ $type === 'weather' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ __('Weather') }}
                        </a>
                        <a href="{{ route('notifications.index', ['filter' => $filter, 'type' => 'new_event']) }}" 
                           class="px-3 py-1 rounded-md text-sm font-medium {{ $type === 'new_event' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ __('Events') }}
                        </a>
                        <a href="{{ route('notifications.index', ['filter' => $filter, 'type' => 'system']) }}" 
                           class="px-3 py-1 rounded-md text-sm font-medium {{ $type === 'system' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ __('System') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                @forelse($notifications as $notification)
                    <div class="border-b border-gray-200 last:border-b-0 {{ $notification->isUnread() ? 'bg-blue-50' : 'bg-white' }} hover:bg-gray-50 transition-colors">
                        <div class="p-6">
                            <div class="flex items-start">
                                <!-- Icon -->
                                <div class="flex-shrink-0 mr-4">
                                    @if($notification->type === 'trail_update')
                                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                            </svg>
                                        </div>
                                    @elseif($notification->type === 'security_alert')
                                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                            </svg>
                                        </div>
                                    @elseif($notification->type === 'booking')
                                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                        </div>
                                    @elseif($notification->type === 'weather')
                                        <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                                            </svg>
                                        </div>
                                    @elseif($notification->type === 'new_event')
                                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="text-base font-semibold text-gray-900">{{ $notification->title }}</h3>
                                            
                                            @if($notification->type === 'weather')
                                                <!-- Weather notification special display -->
                                                <div class="mt-2 space-y-2">
                                                    @if(isset($notification->data['current_temp']))
                                                        <div class="flex items-baseline">
                                                            <span class="text-2xl font-bold text-amber-700">{{ $notification->data['current_temp'] }}°</span>
                                                            <span class="ml-2 text-sm text-gray-600">in</span>
                                                            <span class="ml-1 text-sm font-medium text-gray-900">{{ $notification->data['current_location'] ?? 'Current Location' }}</span>
                                                        </div>
                                                    @endif
                                                    
                                                    @if(isset($notification->data['trail_temp']) && isset($notification->data['trail_name']))
                                                        <div class="flex items-baseline">
                                                            <span class="text-2xl font-bold text-green-700">{{ $notification->data['trail_temp'] }}°</span>
                                                            <span class="ml-2 text-sm text-gray-600">in</span>
                                                            <span class="ml-1 text-sm font-medium text-gray-900">{{ $notification->data['trail_name'] }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @elseif($notification->type === 'new_event')
                                                <!-- Event notification special display -->
                                                <p class="mt-1 text-sm text-gray-600">{{ $notification->message }}</p>
                                                
                                                <div class="mt-3 space-y-2">
                                                    <!-- Trail badge -->
                                                    @if(isset($notification->data['trail_name']))
                                                        <div class="inline-flex items-center px-2.5 py-1 rounded-md bg-green-100 text-green-800 text-xs font-medium">
                                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            </svg>
                                                            {{ $notification->data['trail_name'] }}
                                                        </div>
                                                    @endif
                                                    
                                                    <!-- Date and Price -->
                                                    <div class="flex items-center gap-3 text-sm">
                                                        @if(isset($notification->data['start_at']))
                                                            <span class="text-gray-600">
                                                                {{ \Carbon\Carbon::parse($notification->data['start_at'])->format('M d, Y') }}
                                                            </span>
                                                        @endif
                                                        
                                                        @if(isset($notification->data['is_free']) && $notification->data['is_free'])
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                                Free Event
                                                            </span>
                                                        @elseif(isset($notification->data['price']))
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                                ₱{{ number_format($notification->data['price'], 2) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    
                                                    <!-- Organization -->
                                                    @if(isset($notification->data['organization_name']))
                                                        <div class="text-xs text-gray-500">
                                                            by <span class="font-medium text-gray-700">{{ $notification->data['organization_name'] }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                <!-- View Event Button -->
                                                @if(isset($notification->data['event_slug']))
                                                    <a href="{{ route('events.show', $notification->data['event_slug']) }}" 
                                                       class="mt-3 inline-flex items-center text-sm font-medium text-purple-600 hover:text-purple-800 transition-colors">
                                                        View Event Details
                                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                        </svg>
                                                    </a>
                                                @endif
                                            @else
                                                <!-- Regular notification display -->
                                                <p class="mt-1 text-sm text-gray-600">{{ $notification->message }}</p>
                                            @endif
                                            
                                            <div class="mt-2 flex items-center text-xs text-gray-500">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                        
                                        @if($notification->isUnread())
                                            <div class="ml-4 flex-shrink-0">
                                                <div class="w-2.5 h-2.5 bg-blue-600 rounded-full"></div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Actions -->
                                    <div class="mt-4 flex items-center space-x-3">
                                        @if($notification->type === 'weather' && $notification->data && isset($notification->data['itinerary_id']))
                                            <a href="{{ route('itinerary.show', $notification->data['itinerary_id']) }}" 
                                               class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                                {{ __('View Itinerary') }} →
                                            </a>
                                        @endif

                                        @if($notification->data && isset($notification->data['trail_slug']))
                                            <a href="{{ route('trails.show', $notification->data['trail_slug']) }}" 
                                               class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                                {{ __('View Trail') }}
                                            </a>
                                        @endif

                                        @if($notification->isUnread())
                                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-sm font-medium text-gray-600 hover:text-gray-800">
                                                    {{ __('Mark as read') }}
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('notifications.unread', $notification->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-sm font-medium text-gray-600 hover:text-gray-800">
                                                    {{ __('Mark as unread') }}
                                                </button>
                                            </form>
                                        @endif

                                        <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this notification?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800">
                                                {{ __('Delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">{{ __('No notifications found') }}</h3>
                        <p class="text-gray-500">
                            @if($filter === 'unread')
                                {{ __('You\'re all caught up! No unread notifications.') }}
                            @elseif($filter === 'read')
                                {{ __('No read notifications available.') }}
                            @else
                                {{ __('You don\'t have any notifications yet.') }}
                            @endif
                        </p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-[9999] flex flex-col gap-3 pointer-events-none">
        <!-- Toasts will be dynamically inserted here -->
    </div>

    <!-- Toast Template -->
    <template id="toast-template">
        <div class="toast-item bg-white rounded-xl shadow-2xl overflow-hidden min-w-[320px] max-w-[420px] pointer-events-auto transform translate-x-[500px] opacity-0 transition-all duration-300 ease-out border-l-4">
            <div class="relative">
                <!-- Progress Bar -->
                <div class="toast-progress absolute top-0 left-0 h-1 bg-current opacity-30 transition-all ease-linear" style="width: 100%;"></div>
                
                <div class="p-4 pr-12">
                    <div class="flex items-start gap-3">
                        <!-- Icon -->
                        <div class="toast-icon flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path class="toast-icon-path" fill-rule="evenodd" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1 min-w-0 pt-0.5">
                            <div class="toast-title font-semibold text-gray-900 mb-1"></div>
                            <div class="toast-message text-sm text-gray-600"></div>
                            <div class="toast-details text-xs text-gray-500 mt-1 hidden"></div>
                            <a class="toast-link text-xs font-medium mt-2 gap-1 hover:gap-2 transition-all" style="display: none;">
                                <span class="link-text"></span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Close Button -->
                <button class="toast-close absolute top-3 right-3 text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </template>

    @push('styles')
    <style>
        /* Toast Animations */
        @keyframes slideInRight {
            from {
                transform: translateX(500px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(500px);
                opacity: 0;
            }
        }

        .toast-item {
            animation: slideInRight 0.3s ease-out forwards;
            backdrop-filter: blur(10px);
        }

        .toast-item.hiding {
            animation: slideOutRight 0.3s ease-in forwards;
        }

        /* Toast hover effects */
        .toast-item:hover {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .toast-close:hover {
            transform: scale(1.1);
        }

        /* Mobile responsiveness */
        @media (max-width: 640px) {
            #toast-container {
                left: 1rem;
                right: 1rem;
            }

            .toast-item {
                min-width: auto;
                max-width: 100%;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        // Enhanced Toast System
        function showToast(type, message, opts = {}) {
            try {
                const container = document.getElementById('toast-container');
                const template = document.getElementById('toast-template');
                
                if (!container || !template) {
                    console.error('Toast container or template not found');
                    return;
                }

                // Clone template
                const toast = template.content.cloneNode(true).querySelector('.toast-item');
                
                // Toast configuration based on type
                const config = {
                    success: {
                        title: opts.title || 'Success!',
                        borderColor: 'border-emerald-500',
                        iconBg: 'bg-emerald-100 text-emerald-600',
                        iconPath: 'M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z',
                        progressColor: 'text-emerald-500'
                    },
                    error: {
                        title: opts.title || 'Error',
                        borderColor: 'border-red-500',
                        iconBg: 'bg-red-100 text-red-600',
                        iconPath: 'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z',
                        progressColor: 'text-red-500'
                    },
                    warning: {
                        title: opts.title || 'Warning',
                        borderColor: 'border-amber-500',
                        iconBg: 'bg-amber-100 text-amber-600',
                        iconPath: 'M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z',
                        progressColor: 'text-amber-500'
                    },
                    info: {
                        title: opts.title || 'Info',
                        borderColor: 'border-blue-500',
                        iconBg: 'bg-blue-100 text-blue-600',
                        iconPath: 'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z',
                        progressColor: 'text-blue-500'
                    }
                };

                const typeConfig = config[type] || config.info;
                
                // Apply styling
                toast.classList.add(typeConfig.borderColor);
                const iconContainer = toast.querySelector('.toast-icon');
                iconContainer.className += ' ' + typeConfig.iconBg;
                const iconPath = toast.querySelector('.toast-icon-path');
                iconPath.setAttribute('d', typeConfig.iconPath);
                
                // Set content
                toast.querySelector('.toast-title').textContent = typeConfig.title;
                toast.querySelector('.toast-message').textContent = message;
                
                // Optional details
                if (opts.details) {
                    const detailsEl = toast.querySelector('.toast-details');
                    detailsEl.textContent = opts.details;
                    detailsEl.classList.remove('hidden');
                }
                
                // Optional link
                const linkHref = opts.link || opts.viewLink;
                const linkText = opts.linkText || (opts.viewLink ? 'View More' : null);
                if (linkHref && linkText) {
                    const linkEl = toast.querySelector('.toast-link');
                    linkEl.href = linkHref;
                    linkEl.querySelector('.link-text').textContent = linkText;
                    linkEl.style.display = 'inline-flex';
                    linkEl.classList.add('items-center');
                    linkEl.classList.add(typeConfig.progressColor);
                }
                
                // Progress bar
                const progressBar = toast.querySelector('.toast-progress');
                progressBar.classList.add(typeConfig.progressColor);
                
                // Close button
                const closeBtn = toast.querySelector('.toast-close');
                closeBtn.addEventListener('click', () => hideToast(toast));
                
                // Add to container
                container.appendChild(toast);
                
                // Animate in
                requestAnimationFrame(() => {
                    toast.style.transform = 'translateX(0)';
                    toast.style.opacity = '1';
                });
                
                // Auto-hide with progress bar animation
                const duration = opts.duration || 5000;
                progressBar.style.transition = `width ${duration}ms linear`;
                
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        progressBar.style.width = '0%';
                    });
                });
                
                // Auto-hide timer
                const hideTimer = setTimeout(() => {
                    hideToast(toast);
                }, duration);
                
                // Store timer for manual close
                toast._hideTimer = hideTimer;
                
                // Pause on hover
                toast.addEventListener('mouseenter', () => {
                    clearTimeout(toast._hideTimer);
                    progressBar.style.transition = 'none';
                    const currentWidth = progressBar.offsetWidth;
                    progressBar.style.width = currentWidth + 'px';
                });
                
                toast.addEventListener('mouseleave', () => {
                    const remainingWidth = parseFloat(progressBar.style.width);
                    const remainingTime = (remainingWidth / toast.offsetWidth) * duration;
                    
                    progressBar.style.transition = `width ${remainingTime}ms linear`;
                    progressBar.style.width = '0%';
                    
                    toast._hideTimer = setTimeout(() => {
                        hideToast(toast);
                    }, remainingTime);
                });
                
            } catch (err) {
                console.error('showToast error', err);
            }
        }

        function hideToast(toast) {
            if (toast._hideTimer) {
                clearTimeout(toast._hideTimer);
            }
            
            toast.style.transform = 'translateX(500px)';
            toast.style.opacity = '0';
            
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }

        // Show toast for session success messages
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                showToast('success', '{{ session('success') }}');
            @endif

            @if(session('error'))
                showToast('error', '{{ session('error') }}');
            @endif

            @if(session('warning'))
                showToast('warning', '{{ session('warning') }}');
            @endif

            @if(session('info'))
                showToast('info', '{{ session('info') }}');
            @endif
        });
    </script>
    @endpush
</x-app-layout>

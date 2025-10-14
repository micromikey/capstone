@props(['userType' => 'hiker'])
<div x-data="notificationDropdown('{{ $userType }}')" @click.away="open = false" class="relative">
    <!-- Notification Bell Button -->
    <button @click="open = !open; if(open) loadNotifications();" 
            class="relative p-2 text-gray-600 hover:text-gray-800 focus:outline-none rounded-lg transition-colors">
        <svg class="w-6 h-6" :class="{'fill-current': open, 'fill-none': !open}" :fill="open ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        <!-- Unread Badge - Lowered position -->
        <span x-show="unreadCount > 0" 
              x-text="unreadCount > 99 ? '99+' : unreadCount"
              class="absolute top-1 right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full min-w-[1.25rem]">
        </span>
    </button>

    <!-- Dropdown Menu -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 md:w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50"
         style="display: none;">
        
        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
            <button @click="markAllAsRead()" 
                    x-show="unreadCount > 0"
                    class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                Mark all as read
            </button>
        </div>

        <!-- Notifications List -->
        <div class="max-h-96 overflow-y-auto">
            <template x-if="loading">
                <div class="flex items-center justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                </div>
            </template>

            <template x-if="!loading && notifications.length === 0">
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="text-sm">No notifications yet</p>
                </div>
            </template>

            <template x-if="!loading && notifications.length > 0">
                <div>
                    <template x-for="notification in notifications" :key="notification.id">
                        <div @click="handleNotificationClick(notification)"
                             :class="{'bg-blue-50': !notification.read_at, 'bg-white': notification.read_at}"
                             class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors">
                            <div class="flex items-start">
                                <!-- Icon based on type -->
                                <div class="flex-shrink-0 mr-3">
                                    <template x-if="notification.type === 'trail_update'">
                                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                            </svg>
                                        </div>
                                    </template>
                                    <template x-if="notification.type === 'security_alert'">
                                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                            </svg>
                                        </div>
                                    </template>
                                    <template x-if="notification.type === 'booking' || notification.type === 'booking_created' || notification.type === 'booking_updated' || notification.type === 'booking_status_updated'">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                        </div>
                                    </template>
                                    <template x-if="notification.type === 'weather'">
                                        <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                                            </svg>
                                        </div>
                                    </template>
                                    <template x-if="notification.type === 'new_event'">
                                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    </template>
                                    <template x-if="notification.type === 'system'">
                                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    </template>
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <!-- Weather notification special display -->
                                    <template x-if="notification.type === 'weather'">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900" x-text="notification.title"></p>
                                            <div class="mt-2 space-y-1">
                                                <p class="text-lg font-bold text-amber-700" x-show="notification.data?.current_temp">
                                                    <span x-text="notification.data?.current_temp + '°'"></span>
                                                    <span class="text-xs font-normal text-gray-600 ml-1">in</span>
                                                    <span class="text-sm font-medium" x-text="notification.data?.current_location"></span>
                                                </p>
                                                <p class="text-lg font-bold text-green-700" x-show="notification.data?.trail_temp">
                                                    <span x-text="notification.data?.trail_temp + '°'"></span>
                                                    <span class="text-xs font-normal text-gray-600 ml-1">in</span>
                                                    <span class="text-sm font-medium" x-text="notification.data?.trail_name"></span>
                                                </p>
                                            </div>
                                            <a x-show="notification.data?.itinerary_id" 
                                               :href="`/itinerary/${notification.data?.itinerary_id}`"
                                               class="inline-block mt-2 text-xs font-medium text-blue-600 hover:text-blue-800">
                                                View Itinerary →
                                            </a>
                                            <p class="text-xs text-gray-500 mt-1" x-text="formatTime(notification.created_at)"></p>
                                        </div>
                                    </template>
                                    
                                    <!-- New Event notification special display -->
                                    <template x-if="notification.type === 'new_event'">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900" x-text="notification.title"></p>
                                            <p class="text-sm text-gray-600 mt-1" x-text="notification.message"></p>
                                            <div class="mt-2 flex flex-wrap gap-2 items-center text-xs">
                                                <span x-show="notification.data?.trail_name" class="inline-flex items-center px-2 py-1 bg-green-50 text-green-700 rounded">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                    <span x-text="notification.data?.trail_name"></span>
                                                </span>
                                                <span x-show="notification.data?.is_free" class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-700 rounded font-medium">
                                                    FREE
                                                </span>
                                                <span x-show="!notification.data?.is_free && notification.data?.price" class="inline-flex items-center px-2 py-1 bg-purple-50 text-purple-700 rounded">
                                                    ₱<span x-text="notification.data?.price"></span>
                                                </span>
                                            </div>
                                            <a x-show="notification.data?.event_slug" 
                                               :href="`/hiker/events/${notification.data?.event_slug}`"
                                               class="inline-block mt-2 text-xs font-medium text-blue-600 hover:text-blue-800">
                                                View Event Details →
                                            </a>
                                            <p class="text-xs text-gray-500 mt-1" x-text="formatTime(notification.created_at)"></p>
                                        </div>
                                    </template>
                                    
                                    <!-- Regular notification display -->
                                    <template x-if="notification.type !== 'weather' && notification.type !== 'new_event'">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900" x-text="notification.title"></p>
                                            <p class="text-sm text-gray-600 mt-1" x-text="notification.message"></p>
                                            <p class="text-xs text-gray-500 mt-1" x-text="formatTime(notification.created_at)"></p>
                                        </div>
                                    </template>
                                </div>

                                <!-- Unread indicator -->
                                <div x-show="!notification.read_at" class="flex-shrink-0 ml-2">
                                    <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <!-- Footer -->
        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
            <a href="{{ route('notifications.index') }}" 
               class="block text-center text-sm font-medium text-blue-600 hover:text-blue-800">
                View all notifications
            </a>
        </div>
    </div>
</div>

<script>
function notificationDropdown(userType = 'hiker') {
    return {
        open: false,
        loading: false,
        notifications: [],
        unreadCount: 0,
        userType: userType,

        init() {
            // Load initial unread count
            this.loadNotifications();
            
            // Listen for new notifications from toast system
            window.addEventListener('notification-received', () => {
                this.loadNotifications();
            });
            
            // Optional: Auto-refresh every 60 seconds
            setInterval(() => {
                if (!this.open) {
                    this.loadNotifications();
                }
            }, 60000);
        },

        async loadNotifications() {
            this.loading = true;
            try {
                const response = await fetch('{{ route("notifications.get") }}?limit=10');
                const data = await response.json();
                this.notifications = data.notifications;
                this.unreadCount = data.unread_count;
            } catch (error) {
                console.error('Error loading notifications:', error);
            } finally {
                this.loading = false;
            }
        },

        async markAllAsRead() {
            try {
                const response = await fetch('{{ route("notifications.read-all") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    this.notifications = this.notifications.map(n => ({...n, read_at: new Date().toISOString()}));
                    this.unreadCount = 0;
                }
            } catch (error) {
                console.error('Error marking all as read:', error);
            }
        },

        async handleNotificationClick(notification) {
            // Mark as read if unread
            if (!notification.read_at) {
                try {
                    await fetch(`/notifications/${notification.id}/read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    
                    notification.read_at = new Date().toISOString();
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                } catch (error) {
                    console.error('Error marking notification as read:', error);
                }
            }

            // Handle navigation based on notification data
            if (notification.data?.trail_slug) {
                window.location.href = `/trails/${notification.data.trail_slug}`;
            } else if (notification.data?.booking_id) {
                // Route based on user type
                if (this.userType === 'organization') {
                    window.location.href = `/org/bookings/${notification.data.booking_id}`;
                } else {
                    window.location.href = `/hiker/booking`;
                }
            }
        },

        formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diffInSeconds = Math.floor((now - date) / 1000);

            if (diffInSeconds < 60) return 'Just now';
            if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} minutes ago`;
            if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`;
            if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)} days ago`;
            
            return date.toLocaleDateString();
        }
    }
}
</script>

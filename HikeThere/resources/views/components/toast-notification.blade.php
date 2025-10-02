<!-- Enhanced Toast Notification System for Incoming Notifications -->
<style>
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
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
            transform: translateX(100%);
            opacity: 0;
        }
    }

    @keyframes progressBar {
        from {
            width: 100%;
        }
        to {
            width: 0%;
        }
    }

    .toast-slide-in {
        animation: slideInRight 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .toast-slide-out {
        animation: slideOutRight 0.3s cubic-bezier(0.4, 0, 1, 1);
    }

    .toast-progress-bar {
        animation: progressBar 6s linear forwards;
    }

    .toast-progress-bar.paused {
        animation-play-state: paused;
    }
</style>

<!-- Toast Container -->
<div x-data="{
    toasts: [],
    nextId: 0,
    
    init() {
        // Listen for toast events
        window.addEventListener('show-toast', (event) => {
            this.addToast(event.detail);
        });
        
        // Poll for new notifications every 30 seconds
        this.checkForNewNotifications();
        setInterval(() => this.checkForNewNotifications(), 30000);
    },
    
    addToast(data) {
        const id = this.nextId++;
        const toast = {
            id: id,
            type: data.type || 'system',
            title: data.title || 'Notification',
            message: data.message || '',
            data: data.data || {},
            show: true,
            isPaused: false,
            timeoutId: null
        };
        
        this.toasts.push(toast);
        
        // Auto remove after 6 seconds (matching progress bar)
        const timeoutId = setTimeout(() => this.removeToast(id), 6000);
        const index = this.toasts.findIndex(t => t.id === id);
        if (index !== -1) {
            this.toasts[index].timeoutId = timeoutId;
        }
    },
    
    removeToast(id) {
        const index = this.toasts.findIndex(t => t.id === id);
        if (index !== -1) {
            // Clear timeout if exists
            if (this.toasts[index].timeoutId) {
                clearTimeout(this.toasts[index].timeoutId);
            }
            this.toasts[index].show = false;
            setTimeout(() => {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }, 300);
        }
    },
    
    pauseToast(id) {
        const index = this.toasts.findIndex(t => t.id === id);
        if (index !== -1) {
            this.toasts[index].isPaused = true;
            if (this.toasts[index].timeoutId) {
                clearTimeout(this.toasts[index].timeoutId);
            }
        }
    },
    
    resumeToast(id) {
        const index = this.toasts.findIndex(t => t.id === id);
        if (index !== -1) {
            this.toasts[index].isPaused = false;
            // Resume with remaining time
            const timeoutId = setTimeout(() => this.removeToast(id), 6000);
            this.toasts[index].timeoutId = timeoutId;
        }
    },
    
    async checkForNewNotifications() {
        try {
            const response = await fetch('{{ route('notifications.latest') }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.notification) {
                    this.addToast(data.notification);
                    
                    // Update notification count in bell
                    window.dispatchEvent(new CustomEvent('notification-received'));
                }
            }
        } catch (error) {
            console.log('Toast: Could not check for notifications');
        }
    },
    
    getIcon(type) {
        const icons = {
            'weather': `<svg class='w-6 h-6' fill='none' stroke='currentColor' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'>
                <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z'></path>
            </svg>`,
            'new_event': `<svg class='w-6 h-6' fill='none' stroke='currentColor' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'>
                <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'></path>
            </svg>`,
            'booking': `<svg class='w-6 h-6' fill='none' stroke='currentColor' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'>
                <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'></path>
            </svg>`,
            'trail_update': `<svg class='w-6 h-6' fill='none' stroke='currentColor' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'>
                <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'></path>
            </svg>`,
            'security_alert': `<svg class='w-6 h-6' fill='none' stroke='currentColor' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'>
                <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'></path>
            </svg>`,
            'system': `<svg class='w-6 h-6' fill='none' stroke='currentColor' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'>
                <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'></path>
            </svg>`
        };
        return icons[type] || icons['system'];
    },
    
    getStyles(type) {
        const styles = {
            'weather': {
                bg: 'bg-gradient-to-r from-amber-50 to-yellow-50',
                border: 'border-amber-300',
                iconBg: 'bg-amber-100',
                iconColor: 'text-amber-600',
                progress: 'bg-amber-500',
                text: 'text-amber-900'
            },
            'new_event': {
                bg: 'bg-gradient-to-r from-purple-50 to-pink-50',
                border: 'border-purple-300',
                iconBg: 'bg-purple-100',
                iconColor: 'text-purple-600',
                progress: 'bg-purple-500',
                text: 'text-purple-900'
            },
            'booking': {
                bg: 'bg-gradient-to-r from-blue-50 to-cyan-50',
                border: 'border-blue-300',
                iconBg: 'bg-blue-100',
                iconColor: 'text-blue-600',
                progress: 'bg-blue-500',
                text: 'text-blue-900'
            },
            'trail_update': {
                bg: 'bg-gradient-to-r from-green-50 to-emerald-50',
                border: 'border-green-300',
                iconBg: 'bg-green-100',
                iconColor: 'text-green-600',
                progress: 'bg-green-500',
                text: 'text-green-900'
            },
            'security_alert': {
                bg: 'bg-gradient-to-r from-red-50 to-orange-50',
                border: 'border-red-300',
                iconBg: 'bg-red-100',
                iconColor: 'text-red-600',
                progress: 'bg-red-500',
                text: 'text-red-900'
            },
            'system': {
                bg: 'bg-gradient-to-r from-gray-50 to-slate-50',
                border: 'border-gray-300',
                iconBg: 'bg-gray-100',
                iconColor: 'text-gray-600',
                progress: 'bg-gray-500',
                text: 'text-gray-900'
            }
        };
        return styles[type] || styles['system'];
    }
}" 
class="fixed top-4 right-4 z-50 space-y-3 pointer-events-none" 
style="max-width: 420px; width: 100%;">
    <!-- Toast Template -->
    <template x-for="toast in toasts" :key="toast.id">
        <div 
            x-show="toast.show"
            :class="[
                getStyles(toast.type).bg, 
                getStyles(toast.type).border,
                getStyles(toast.type).text,
                toast.show ? 'toast-slide-in' : 'toast-slide-out'
            ]"
            class="pointer-events-auto rounded-xl shadow-2xl border-2 overflow-hidden cursor-pointer transform transition-all duration-200 hover:scale-105 hover:shadow-3xl"
            @mouseenter="pauseToast(toast.id)"
            @mouseleave="resumeToast(toast.id)"
            @click="window.location.href = '{{ route('notifications.index') }}'"
        >
            <!-- Main Content -->
            <div class="p-4 flex items-start gap-3">
                <!-- Icon with circular background -->
                <div 
                    :class="[getStyles(toast.type).iconBg, getStyles(toast.type).iconColor]"
                    class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center shadow-md"
                    x-html="getIcon(toast.type)"
                ></div>
                
                <!-- Content -->
                <div class="flex-1 min-w-0 pt-1">
                    <p class="text-sm font-bold mb-1 leading-tight" x-text="toast.title"></p>
                    <p class="text-xs opacity-90 leading-relaxed" x-text="toast.message"></p>
                    
                    <!-- Weather specific display -->
                    <template x-if="toast.type === 'weather' && toast.data.current_temp">
                        <div class="mt-3 space-y-2">
                            <div class="flex items-baseline gap-2">
                                <span class="font-bold text-2xl" x-text="toast.data.current_temp + '°'"></span>
                                <span class="text-xs opacity-75">in</span>
                                <span class="text-sm font-semibold" x-text="toast.data.current_location"></span>
                            </div>
                            <template x-if="toast.data.trail_temp && toast.data.trail_name">
                                <div class="flex items-baseline gap-2 text-green-700">
                                    <span class="font-bold text-lg" x-text="toast.data.trail_temp + '°'"></span>
                                    <span class="text-xs opacity-75">at</span>
                                    <span class="text-sm font-medium" x-text="toast.data.trail_name"></span>
                                </div>
                            </template>
                        </div>
                    </template>
                    
                    <!-- Event specific display -->
                    <template x-if="toast.type === 'new_event' && toast.data.trail_name">
                        <div class="mt-3 flex flex-wrap gap-2">
                            <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 shadow-sm">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                </svg>
                                <span x-text="toast.data.trail_name"></span>
                            </div>
                            <template x-if="toast.data.is_free">
                                <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 shadow-sm">
                                    FREE EVENT
                                </div>
                            </template>
                            <template x-if="!toast.data.is_free && toast.data.price">
                                <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 shadow-sm">
                                    ₱<span x-text="toast.data.price"></span>
                                </div>
                            </template>
                        </div>
                    </template>
                    
                    <!-- Booking specific display -->
                    <template x-if="toast.type === 'booking' && (toast.data.trail_name || toast.data.event_name)">
                        <div class="mt-2 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 shadow-sm">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span x-text="toast.data.trail_name || toast.data.event_name"></span>
                        </div>
                    </template>
                </div>
                
                <!-- Close Button -->
                <button 
                    @click.stop="removeToast(toast.id)"
                    class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center hover:bg-black/10 transition-colors"
                    title="Close"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Progress Bar -->
            <div class="h-1 bg-black/10">
                <div 
                    :class="[getStyles(toast.type).progress, toast.isPaused ? 'paused' : '']"
                    class="h-full toast-progress-bar"
                ></div>
            </div>
        </div>
    </template>
</div>

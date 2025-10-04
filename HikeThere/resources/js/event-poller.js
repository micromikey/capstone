/**
 * Event Polling System
 * Automatically checks for new events and updates the page without refresh
 * 
 * Usage:
 * EventPoller.init({
 *     containerSelector: '#events-container',
 *     trailId: null, // Optional: filter by specific trail
 *     interval: 30000, // Poll every 30 seconds
 *     onNewEvents: function(events) {
 *         // Custom handler for new events
 *     }
 * });
 */

const EventPoller = (function() {
    let config = {
        containerSelector: null,
        trailId: null,
        interval: 30000, // 30 seconds
        enabled: true,
        lastCheckTime: null,
        pollTimer: null,
        notificationEnabled: true,
        onNewEvents: null
    };

    /**
     * Initialize the event poller
     */
    function init(options) {
        config = { ...config, ...options };
        config.lastCheckTime = new Date().toISOString();
        
        // Start polling
        if (config.enabled) {
            startPolling();
        }

        // Stop polling when user leaves page
        document.addEventListener('visibilitychange', handleVisibilityChange);
        
        // Stop polling before page unload
        window.addEventListener('beforeunload', stop);
    }

    /**
     * Start the polling interval
     */
    function startPolling() {
        // Clear any existing timer
        if (config.pollTimer) {
            clearInterval(config.pollTimer);
        }

        // Initial check
        checkForNewEvents();

        // Set up interval
        config.pollTimer = setInterval(checkForNewEvents, config.interval);
        console.log('[EventPoller] Started polling every', config.interval / 1000, 'seconds');
    }

    /**
     * Stop polling
     */
    function stop() {
        if (config.pollTimer) {
            clearInterval(config.pollTimer);
            config.pollTimer = null;
            console.log('[EventPoller] Stopped polling');
        }
    }

    /**
     * Pause/resume polling based on page visibility
     */
    function handleVisibilityChange() {
        if (document.hidden) {
            stop();
        } else {
            if (config.enabled) {
                // Check immediately when user returns
                config.lastCheckTime = new Date(Date.now() - config.interval).toISOString();
                startPolling();
            }
        }
    }

    /**
     * Check for new events via API
     */
    async function checkForNewEvents() {
        try {
            const url = new URL('/api/events/latest', window.location.origin);
            url.searchParams.append('since', config.lastCheckTime);
            
            if (config.trailId) {
                url.searchParams.append('trail_id', config.trailId);
            }

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                console.error('[EventPoller] API error:', response.status);
                return;
            }

            const data = await response.json();
            
            // Update last check time
            if (data.timestamp) {
                config.lastCheckTime = data.timestamp;
            }

            // Handle new events
            if (data.success && data.count > 0) {
                console.log('[EventPoller] Found', data.count, 'new event(s)');
                handleNewEvents(data.events);
            }

        } catch (error) {
            console.error('[EventPoller] Error checking for new events:', error);
        }
    }

    /**
     * Handle new events
     */
    function handleNewEvents(events) {
        // Show notification
        if (config.notificationEnabled) {
            showNotification(events.length);
        }

        // Call custom handler if provided
        if (typeof config.onNewEvents === 'function') {
            config.onNewEvents(events);
        } else {
            // Default behavior: insert events into container
            insertEvents(events);
        }
    }

    /**
     * Insert new events into the DOM
     */
    function insertEvents(events) {
        if (!config.containerSelector) {
            console.warn('[EventPoller] No container selector provided');
            return;
        }

        const container = document.querySelector(config.containerSelector);
        if (!container) {
            console.warn('[EventPoller] Container not found:', config.containerSelector);
            return;
        }

        events.forEach(event => {
            const eventCard = createEventCard(event);
            
            // Add animation class
            eventCard.classList.add('event-new-animation');
            
            // Insert at the beginning of container
            container.insertBefore(eventCard, container.firstChild);
            
            // Remove animation class after animation completes
            setTimeout(() => {
                eventCard.classList.remove('event-new-animation');
            }, 1000);
        });
    }

    /**
     * Create event card HTML
     */
    function createEventCard(event) {
        const card = document.createElement('div');
        card.className = 'event-card bg-white rounded-lg shadow-md p-6 mb-4 border-l-4 border-[#336d66]';
        card.dataset.eventId = event.id;
        
        const startDate = event.start_at ? new Date(event.start_at).toLocaleDateString() : 'Always Available';
        const trailName = event.trail?.name || 'Unknown Trail';
        const mountain = event.trail?.mountain || '';
        
        card.innerHTML = `
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <span class="inline-block px-2 py-1 text-xs font-semibold text-white bg-green-500 rounded-full mb-2">
                        NEW
                    </span>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">
                        <a href="${event.url}" class="hover:text-[#336d66] transition-colors">
                            ${escapeHtml(event.title)}
                        </a>
                    </h3>
                    <div class="text-sm text-gray-600 space-y-1">
                        <p class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="font-medium">${escapeHtml(trailName)}</span>
                            ${mountain ? `<span class="text-gray-400 mx-1">•</span><span>${escapeHtml(mountain)}</span>` : ''}
                        </p>
                        <p class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            ${escapeHtml(startDate)}
                        </p>
                        ${event.description ? `<p class="mt-2 text-gray-700">${escapeHtml(event.description).substring(0, 100)}${event.description.length > 100 ? '...' : ''}</p>` : ''}
                    </div>
                </div>
                <a href="${event.url}" class="ml-4 inline-flex items-center px-4 py-2 bg-[#336d66] text-white text-sm font-medium rounded-lg hover:bg-[#2a5a54] transition-colors">
                    View Details
                </a>
            </div>
        `;
        
        return card;
    }

    /**
     * Show notification for new events
     */
    function showNotification(count) {
        // Check if browser supports notifications
        if (!('Notification' in window)) {
            showInPageNotification(count);
            return;
        }

        // Check notification permission
        if (Notification.permission === 'granted') {
            new Notification('New Events Available!', {
                body: `${count} new hiking event${count > 1 ? 's' : ''} added`,
                icon: '/images/logo.png', // Update with your logo path
                badge: '/images/badge.png' // Update with your badge path
            });
        } else {
            showInPageNotification(count);
        }
    }

    /**
     * Show in-page notification banner
     */
    function showInPageNotification(count) {
        const existing = document.getElementById('event-poller-notification');
        if (existing) {
            existing.remove();
        }

        const notification = document.createElement('div');
        notification.id = 'event-poller-notification';
        notification.className = 'fixed top-20 right-4 bg-[#336d66] text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center space-x-3 animate-slide-in-right';
        notification.innerHTML = `
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>${count} new event${count > 1 ? 's' : ''} added!</span>
            <button onclick="this.parentElement.remove()" class="ml-2 hover:text-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }

    /**
     * Escape HTML to prevent XSS
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Request notification permission
     */
    function requestNotificationPermission() {
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    }

    // Public API
    return {
        init,
        stop,
        requestNotificationPermission,
        checkForNewEvents
    };
})();

// Make it globally available
window.EventPoller = EventPoller;

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
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
    
    @keyframes fadeInUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    .animate-slide-in-right {
        animation: slideInRight 0.3s ease-out;
    }
    
    .event-new-animation {
        animation: fadeInUp 0.5s ease-out;
    }
    
    .event-card.event-new-animation {
        border-left-color: #10b981 !important;
        background: linear-gradient(to right, #ecfdf5 0%, #ffffff 5%);
    }
`;
document.head.appendChild(style);

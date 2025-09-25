<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3l6-3"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Interactive Trail Map') }}
                    </h2>
                    <p class="text-sm text-gray-600">Discover and explore hiking trails across the Philippines</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('explore') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Explore Trails
                </a>
                <a href="{{ route('hiking-tools') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    Hiking Tools
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Clean Search and Controls -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 mb-6">
                <div class="p-4">
                    <!-- Clean Search Bar -->
                    <div class="relative mb-4">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" id="map-search" 
                               placeholder="Search trails by name, mountain, or location..." 
                               class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>

                    <!-- Organized Controls in Sections -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <!-- Quick Actions moved into the map UI as compact circular buttons -->

                        <!-- Filters -->
                        <div class="space-y-2">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Filters</h4>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <select id="difficulty-filter" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                        <option value="">All Difficulties</option>
                                        <option value="beginner">🟢 Beginner</option>
                                        <option value="intermediate">🟡 Intermediate</option>
                                        <option value="advanced">🔴 Advanced</option>
                                    </select>
                                </div>
                                <div>
                                    <select id="radius-filter" class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                        <option value="5">5 km radius</option>
                                        <option value="10">10 km radius</option>
                                        <option value="25" selected>25 km radius</option>
                                        <option value="50">50 km radius</option>
                                        <option value="100">100 km radius</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    <!-- Compact Statistics -->
                    <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center space-x-6">
                                <div class="flex items-center space-x-2">
                                    <span class="text-gray-600">Showing:</span>
                                    <span class="font-semibold text-green-600" id="filtered-trails-count">0</span>
                                    <span class="text-gray-400">of</span>
                                    <span class="font-semibold text-gray-700" id="total-trails-count">0</span>
                                    <span class="text-gray-600">trails</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-gray-600">Zoom:</span>
                                    <span class="font-semibold text-purple-600" id="map-zoom-level">6</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-gray-500" id="last-updated">Just now</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clean Map Container -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                <div class="relative overflow-hidden rounded-xl">
                    <div id="map" class="w-full h-[500px] md:h-[600px] lg:h-[700px] bg-gray-100 flex items-center justify-center">
                        <!-- Fallback map content -->
                        <div id="fallback-map" class="text-center p-8">
                            <div class="text-gray-400 text-6xl mb-4">🗺️</div>
                            <h3 class="text-xl font-semibold text-gray-600 mb-2">Interactive Hiking Map</h3>
                            <p class="text-gray-500 mb-4">Loading your hiking adventure...</p>
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-green-200 border-t-green-600"></div>
                        </div>
                    </div>
                    
                    <!-- Map Loading Overlay -->
                    <div id="map-loading" class="absolute inset-0 bg-gray-50/90 backdrop-blur-sm flex items-center justify-center">
                        <div class="text-center">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-green-200 border-t-green-600 mb-4"></div>
                            <p class="text-gray-600 font-medium">Loading interactive map...</p>
                            <p class="text-sm text-gray-500">Preparing your hiking adventure</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Compact Trail Info Panel -->
            <div id="trail-info-panel" class="fixed bottom-0 left-4 right-4 md:left-auto md:right-4 md:w-96 bg-white rounded-t-2xl border border-gray-200 shadow-2xl transform translate-y-full transition-transform duration-300 ease-in-out z-50 max-h-[80vh] overflow-y-auto auto-hide-scrollbar">
                <div class="flex items-center justify-between p-3 border-b border-gray-200 bg-gradient-to-r from-green-50 to-blue-50">
                    <h3 id="trail-info-title" class="text-base font-semibold text-gray-900">Trail Information</h3>
                    <button id="close-trail-info" class="text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600 transition-colors p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="trail-info-content" class="p-3">
                    <!-- Trail information will be populated here -->
                </div>
            </div>

            <!-- Simplified Quick Actions -->
            <div class="fixed bottom-6 right-6 z-40 space-y-3">
                <button id="fullscreen-toggle" class="w-12 h-12 bg-white rounded-full shadow-lg border border-gray-200 flex items-center justify-center text-gray-600 hover:text-green-600 hover:bg-green-50 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                    </svg>
                </button>
                <button id="help-toggle" class="w-12 h-12 bg-white rounded-full shadow-lg border border-gray-200 flex items-center justify-center text-gray-600 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Clean Loading Spinner -->
    <div id="loading-spinner" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl p-6 shadow-2xl border border-gray-200">
            <div class="flex items-center space-x-4">
                <div class="animate-spin rounded-full h-8 w-8 border-4 border-green-200 border-t-green-600"></div>
                <span class="text-gray-700 font-medium">Loading trails...</span>
            </div>
        </div>
    </div>


    @push('scripts')
    <!-- Add CSRF token meta tag -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Load the HikeThere Map JavaScript first -->
    @vite(['resources/js/map.js'])
    
    <!-- Google Maps API with enhanced libraries -->
    <script>
        // Wait for Vite to load the HikeThereMap class before initializing
        function waitForHikeThereMap() {
            return new Promise((resolve) => {
                if (typeof HikeThereMap !== 'undefined') {
                    resolve();
                } else {
                    // Check every 100ms until HikeThereMap is available
                    const checkInterval = setInterval(() => {
                        if (typeof HikeThereMap !== 'undefined') {
                            clearInterval(checkInterval);
                            resolve();
                        }
                    }, 100);
                    
                    // Timeout after 10 seconds
                    setTimeout(() => {
                        clearInterval(checkInterval);
                        console.error('HikeThereMap class not loaded within timeout');
                        resolve(); // Resolve anyway to continue with error handling
                    }, 10000);
                }
            });
        }

        // Enhanced Google Maps API loading with comprehensive libraries
        async function loadGoogleMapsAPI() {
            return new Promise((resolve, reject) => {
                const apiKey = '{{ config('services.google.maps_api_key') }}';
                if (!apiKey) {
                    reject(new Error('Google Maps API key not configured.'));
                    return;
                }
                const script = document.createElement('script');
                script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&libraries=geometry,places,visualization,drawing&callback=initMap`;
                script.onerror = () => reject(new Error('Failed to load Google Maps API'));
                const timeout = setTimeout(() => reject(new Error('Google Maps API loading timeout')), 15000);
                window.initMap = async function() {
                    clearTimeout(timeout);
                    try {
                        await waitForHikeThereMap();
                        document.getElementById('map-loading').style.display = 'none';
                        document.getElementById('fallback-map').style.display = 'none';
                        if (typeof HikeThereMap !== 'undefined') {
                            window.hikeThereMap = new HikeThereMap();
                            updateMapStatistics();
                            setupEnhancedEventListeners();
                            console.log('Map initialized successfully');
                        } else {
                            showMapError('Map System Error', 'The hiking map system failed to initialize.');
                        }
                        resolve();
                    } catch (e) {
                        console.error('Error during map initialization:', e);
                        showMapError('Map Loading Error', 'Failed to load map components.');
                        reject(e);
                    }
                };
                document.head.appendChild(script);
            });
        }
        
        // Helper function to show map errors
        function showMapError(title, message) {
            const fallbackMap = document.getElementById('fallback-map');
            if (fallbackMap) {
                fallbackMap.innerHTML = `
                    <div class="text-center p-8">
                        <div class="text-red-600 text-4xl mb-4">⚠️</div>
                        <p class="text-red-600 text-lg font-semibold mb-2">${title}</p>
                        <p class="text-gray-600 mb-4">${message}</p>
                        <div class="mt-4 space-y-2">
                            <button onclick="location.reload()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                🔄 Refresh Page
                            </button>
                            <button onclick="showOfflineMap()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors ml-2">
                                📱 Show Offline Map
                            </button>
                        </div>
                    </div>
                `;
            }
        }

        // Enhanced error handling and user feedback
        loadGoogleMapsAPI().catch(error => {
            console.error('Error loading Google Maps API:', error);
            document.getElementById('map-loading').style.display = 'none';
            showMapError('Failed to load Google Maps', error.message + ' - Try refreshing or use offline mode.');
        });
        
        // Offline map fallback
        function showOfflineMap() {
            document.getElementById('fallback-map').innerHTML = `
                <div class="text-center p-8">
                    <div class="text-blue-600 text-4xl mb-4">📱</div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Offline Map Mode</h3>
                    <p class="text-gray-600 mb-4">Basic trail information is available offline</p>
                    <div class="bg-gray-50 rounded-lg p-4 text-left">
                        <h4 class="font-medium text-gray-900 mb-2">Available Features:</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Trail search and filtering</li>
                            <li>• Trail information display</li>
                            <li>• Safety information</li>
                            <li>• Basic navigation tools</li>
                        </ul>
                    </div>
                    <button onclick="location.reload()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors mt-4">
                        🔄 Try Online Mode Again
                    </button>
                </div>
            `;
        }

        // Initialize the loading process - don't check immediately as Vite might still be loading
        // The waitForHikeThereMap function will handle the timing properly

        // Enhanced map statistics and UI updates
        function updateMapStatistics() {
            if (window.hikeThereMap) {
                document.getElementById('filtered-trails-count').textContent = window.hikeThereMap.filteredTrails.length;
                document.getElementById('total-trails-count').textContent = window.hikeThereMap.trails.length;
                
                if (window.hikeThereMap.map) {
                    document.getElementById('map-zoom-level').textContent = window.hikeThereMap.map.getZoom();
                }
                
                document.getElementById('last-updated').textContent = new Date().toLocaleTimeString();
            }
        }

        // Enhanced event listeners setup
        function setupEnhancedEventListeners() {
            console.log('Setting up enhanced event listeners...');
            
            // Map type filter
            const mapTypeFilter = document.getElementById('map-type-filter');
            if (mapTypeFilter) {
                mapTypeFilter.addEventListener('change', (e) => {
                    const mapType = e.target.value;
                    const mapTypeId = {
                        'terrain': google.maps.MapTypeId.TERRAIN,
                        'satellite': google.maps.MapTypeId.SATELLITE,
                        'roadmap': google.maps.MapTypeId.ROADMAP,
                        'hybrid': google.maps.MapTypeId.HYBRID
                    };
                    
                    if (window.hikeThereMap && window.hikeThereMap.map) {
                        window.hikeThereMap.map.setMapTypeId(mapTypeId[mapType]);
                    }
                });
            }

            // Fullscreen toggle
            const fullscreenToggle = document.getElementById('fullscreen-toggle');
            if (fullscreenToggle) {
                fullscreenToggle.addEventListener('click', () => {
                    const mapContainer = document.querySelector('.map-container');
                    if (document.fullscreenElement) {
                        document.exitFullscreen();
                    } else {
                        mapContainer.requestFullscreen();
                    }
                });
            }

            // Help modal
            const helpToggle = document.getElementById('help-toggle');
            const helpModal = document.getElementById('help-modal');
            const closeHelp = document.getElementById('close-help');
            const closeHelpBottom = document.getElementById('close-help-bottom');
            
            if (helpToggle && helpModal) {
                helpToggle.addEventListener('click', () => {
                    helpModal.classList.remove('hidden');
                });
            }
            
            function closeHelpModal() {
                if (helpModal) {
                    helpModal.classList.add('hidden');
                }
            }
            
            if (closeHelp) {
                closeHelp.addEventListener('click', closeHelpModal);
            }
            
            if (closeHelpBottom) {
                closeHelpBottom.addEventListener('click', closeHelpModal);
            }
            
            if (helpModal) {
                helpModal.addEventListener('click', (e) => {
                    if (e.target === helpModal) {
                        closeHelpModal();
                    }
                });
            }

            // Enhanced hiking buttons setup
            setupEnhancedHikingButtons();
            
            // Periodic updates
            setInterval(updateMapStatistics, 5000);
        }

        // Enhanced hiking buttons functionality
        function setupEnhancedHikingButtons() {
            // Safety Info Button
            const safetyBtn = document.getElementById('show-safety-info-btn');
            const hikingInfoPanel = document.getElementById('hiking-info-panel');
            const closeHikingInfo = document.getElementById('close-hiking-info');
            
            if (safetyBtn && hikingInfoPanel) {
                safetyBtn.addEventListener('click', function() {
                    hikingInfoPanel.classList.toggle('hidden');
                    loadSafetyInfo();
                });
            }
            
            if (closeHikingInfo && hikingInfoPanel) {
                closeHikingInfo.addEventListener('click', function() {
                    hikingInfoPanel.classList.add('hidden');
                });
            }
            
            // Trail Conditions Button
            const conditionsBtn = document.getElementById('show-trail-conditions-btn');
            if (conditionsBtn) {
                conditionsBtn.addEventListener('click', function() {
                    loadTrailConditions();
                });
            }
        }

        // Enhanced safety information loading
        async function loadSafetyInfo() {
            try {
                const response = await fetch('/api/hiking/safety-info');
                const safetyData = await response.json();
                
                // Update safety info display with enhanced data
                document.getElementById('local-police').textContent = safetyData.emergency_contacts?.local_police || '117';
                document.getElementById('mountain-rescue').textContent = safetyData.emergency_contacts?.mountain_rescue || '143';
                document.getElementById('forest-service').textContent = safetyData.emergency_contacts?.forest_service || 'Contact local DENR';
                document.getElementById('emergency').textContent = safetyData.emergency_contacts?.emergency || '911';
                
                if (safetyData.safety_tips) {
                    const tipsList = document.getElementById('safety-tips');
                    tipsList.innerHTML = safetyData.safety_tips.map(tip => `<li>• ${tip}</li>`).join('');
                }

                // Update current conditions
                if (safetyData.current_conditions) {
                    document.getElementById('weather-info').textContent = safetyData.current_conditions.weather || 'Good';
                    document.getElementById('trail-status').textContent = safetyData.current_conditions.trail_status || 'Open';
                    document.getElementById('hazards').textContent = safetyData.current_conditions.hazards || 'None reported';
                    document.getElementById('visibility').textContent = safetyData.current_conditions.visibility || 'Good';
                }
            } catch (error) {
                console.error('Error loading safety info:', error);
            }
        }

        // Enhanced trail conditions loading
        async function loadTrailConditions() {
            try {
                const response = await fetch('/api/hiking/trail-conditions');
                const conditionsData = await response.json();
                
                // Create and show enhanced modal with trail conditions
                showTrailConditionsModal(conditionsData);
            } catch (error) {
                console.error('Error loading trail conditions:', error);
            }
        }

        // Enhanced trail conditions modal
        function showTrailConditionsModal(conditionsData) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50';
            
            modal.innerHTML = `
                <div class="bg-white rounded-2xl p-6 max-w-2xl w-full mx-4 shadow-2xl border border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-green-100 rounded-lg">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">Trail Conditions Report</h3>
                        </div>
                        <button class="text-gray-400 hover:text-gray-600 transition-colors" onclick="this.closest('.fixed').remove()">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="space-y-4 max-h-96 overflow-y-auto">
                        ${conditionsData.trails?.map(trail => `
                            <div class="p-4 bg-gradient-to-r from-green-50 to-blue-50 rounded-xl border border-green-200/50">
                                <div class="flex items-start justify-between mb-2">
                                    <h4 class="font-semibold text-gray-900">${trail.name}</h4>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full ${trail.status === 'open' ? 'bg-green-100 text-green-800' : trail.status === 'caution' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'}">
                                        ${trail.status?.toUpperCase() || 'UNKNOWN'}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-700 mb-2">${trail.conditions}</p>
                                <p class="text-xs text-gray-500 mb-3">Last updated: ${new Date(trail.last_updated).toLocaleString()}</p>
                                ${trail.hazards?.length > 0 ? `
                                    <div class="mb-3">
                                        <span class="text-xs font-medium text-red-600 flex items-center gap-1 mb-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                            Hazards:
                                        </span>
                                        <ul class="text-xs text-red-600 ml-4">
                                            ${trail.hazards.map(hazard => `<li>• ${hazard}</li>`).join('')}
                                        </ul>
                                    </div>
                                ` : ''}
                                ${trail.recommendations?.length > 0 ? `
                                    <div>
                                        <span class="text-xs font-medium text-blue-600 flex items-center gap-1 mb-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Recommendations:
                                        </span>
                                        <ul class="text-xs text-blue-600 ml-4">
                                            ${trail.recommendations.map(rec => `<li>• ${rec}</li>`).join('')}
                                        </ul>
                                    </div>
                                ` : ''}
                            </div>
                        `).join('') || '<div class="text-center text-gray-500 py-8"><p>No trail conditions available at the moment.</p><p class="text-sm mt-1">Please check back later or contact local authorities.</p></div>'}
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            // Close modal when clicking outside
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.remove();
                }
            });
        }

        // Add debugging information
        console.log('Enhanced Google Maps API Key configured:', '{{ config('services.google.maps_api_key') ? 'Yes' : 'No' }}');
        console.log('API Key value:', '{{ config('services.google.maps_api_key') }}');
        console.log('Current domain:', window.location.hostname);
        console.log('HikeThere Enhanced Map System loading...');
        
        // Debug map.js loading
        setTimeout(() => {
            console.log('HikeThereMap class available:', typeof HikeThereMap !== 'undefined');
            console.log('HikeThereMapLoaded variable:', window.HikeThereMapLoaded);
            if (typeof HikeThereMap !== 'undefined') {
                console.log('HikeThereMap class loaded successfully');
            } else {
                console.error('HikeThereMap class not loaded - check if map.js is accessible');
                console.log('Available global objects:', Object.keys(window).filter(key => key.includes('Map') || key.includes('Hike')));
            }
        }, 1000);
        
        // Additional debugging
        console.log('Vite assets loaded:', typeof Vite !== 'undefined');
        console.log('Google Maps API status:', typeof google !== 'undefined' ? 'Available' : 'Not loaded');
        
        // Test if the page is ready
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, checking map elements...');
            console.log('Map container:', document.getElementById('map'));
            console.log('Map loading overlay:', document.getElementById('map-loading'));
            console.log('Fallback map:', document.getElementById('fallback-map'));
            
            // Check if map.js loaded
            setTimeout(() => {
                console.log('HikeThereMapLoaded variable:', window.HikeThereMapLoaded);
                console.log('HikeThereMap class:', typeof HikeThereMap);
                console.log('All window properties containing "Hike":', Object.keys(window).filter(key => key.includes('Hike')));
            }, 2000);
        });
    </script>
    @endpush
</x-app-layout>
// HikeThere Enhanced Map System - Professional Hiking Experience
// Modern, feature-rich Google Maps integration for hiking enthusiasts

class HikeThereMap {
    constructor(options = {}) {
        // Core map properties
        this.map = null;
        this.markers = new Map();
        this.infoWindow = null;
        this.markerClusterer = null;
        this.currentLocation = null;
        this.trails = [];
        this.filteredTrails = [];
        
        // Configuration
        this.config = {
            isClusteringEnabled: true,
            isEmbedded: options.isEmbedded || false,
            mapElementId: options.mapElementId || 'map',
            defaultCenter: { lat: 12.8797, lng: 121.7740 }, // Philippines
            defaultZoom: 6,
            maxZoom: 18,
            minZoom: 4,
            ...options.config
        };
        
        // Enhanced hiking features
        this.trailPaths = new Map();
        this.elevationCharts = new Map();
        this.weatherData = new Map();
        this.weatherRefreshIntervals = {};
        this.activeTrail = null;
        this.hikingLayers = {};
        this.searchAutocomplete = null;
        this.heatmap = null;
        this.directionsService = null;
        this.directionsRenderer = null;
        
        // Performance and state management
        this.markerUpdateQueue = [];
        this.isUpdatingMarkers = false;
        this.debounceTimer = null;
        this.isLoading = false;
        
        // UI state
        this.currentFilters = {
            difficulty: '',
            radius: 25,
            search: '',
            showTrailPaths: false,
            showElevation: false
        };
        
        this.init();
    }

    async init() {
        try {
            await this.initializeMap();
            this.setupEventListeners();
            await this.loadTrails();
            this.setupMarkerClusterer();
            this.initializeHikingLayers();
            this.setupSearchAutocomplete();
            this.initializeHeatmap();
            this.setupDirections();
            this.setupPerformanceMonitoring();
            
            // Initialize weather system and bounds restrictions
            this.initializeWeatherAndBounds();
            
            // Setup auto-hiding scrollbars for the entire page
            this.setupAutoHideScrollbars();
            
            console.log('HikeThere Map initialized successfully');
        } catch (error) {
            console.error('Failed to initialize HikeThere Map:', error);
            this.showError('Failed to initialize map. Please refresh the page.');
        }
    }

    async initializeMap() {
        const mapOptions = {
            center: this.config.defaultCenter,
            zoom: this.config.defaultZoom,
            mapTypeId: google.maps.MapTypeId.TERRAIN,
            styles: this.getEnhancedMapStyles(),
            mapTypeControl: true,
            mapTypeControlOptions: {
                style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                position: google.maps.ControlPosition.TOP_RIGHT
            },
            streetViewControl: false,
            fullscreenControl: true,
            zoomControl: true,
            zoomControlOptions: {
                position: google.maps.ControlPosition.RIGHT_BOTTOM
            },
            tilt: 0,
            heading: 0,
            gestureHandling: 'cooperative',
            maxZoom: this.config.maxZoom,
            minZoom: this.config.minZoom,
            backgroundColor: '#f8fafc',
            scaleControl: true,
            rotateControl: true,
            rotateControlOptions: {
                position: google.maps.ControlPosition.RIGHT_TOP
            },
            // Enhanced performance options
            disableDefaultUI: false,
            clickableIcons: true,
            keyboardShortcuts: true
        };

        this.map = new google.maps.Map(document.getElementById(this.config.mapElementId), mapOptions);
        
        // Initialize enhanced components
        this.infoWindow = new google.maps.InfoWindow({
            maxWidth: 400,
            pixelOffset: new google.maps.Size(0, -10),
            className: 'hikethere-info-window'
        });
        
        this.directionsService = new google.maps.DirectionsService();
        this.directionsRenderer = new google.maps.DirectionsRenderer({
            suppressMarkers: true,
            polylineOptions: {
                strokeColor: '#10B981',
                strokeWeight: 4,
                strokeOpacity: 0.8
            }
        });
        
        this.directionsRenderer.setMap(this.map);
        
        this.addEnhancedControls();
        this.setupMapEvents();
    }

    getEnhancedMapStyles() {
        return [
            // Remove POI labels for cleaner look
            {
                featureType: 'poi',
                elementType: 'labels',
                stylers: [{ visibility: 'off' }]
            },
            // Remove transit labels
            {
                featureType: 'transit',
                elementType: 'labels',
                stylers: [{ visibility: 'off' }]
            },
            // Enhance natural terrain
            {
                featureType: 'natural.terrain',
                elementType: 'geometry',
                stylers: [
                    { visibility: 'on' },
                    { weight: 2 },
                    { color: '#8B7355' }
                ]
            },
            // Enhance water features
            {
                featureType: 'natural.water',
                elementType: 'geometry',
                stylers: [
                    { visibility: 'on' },
                    { color: '#4A90E2' },
                    { weight: 1.5 }
                ]
            },
            // Enhance parks
            {
                featureType: 'poi.park',
                elementType: 'geometry',
                stylers: [
                    { visibility: 'on' },
                    { color: '#90EE90' },
                    { weight: 1.2 }
                ]
            },
            // Simplify road labels
            {
                featureType: 'road',
                elementType: 'labels',
                stylers: [{ visibility: 'simplified' }]
            },
            // Hide local roads
            {
                featureType: 'road.local',
                elementType: 'geometry',
                stylers: [{ visibility: 'off' }]
            },
            // Enhance hiking trails
            {
                featureType: 'poi.park',
                elementType: 'labels.text',
                stylers: [
                    { visibility: 'on' },
                    { color: '#2D5A27' },
                    { weight: 1.5 }
                ]
            }
        ];
    }

    addEnhancedControls() {
        // Add custom map controls with proper spacing
        const controlDiv = document.createElement('div');
        controlDiv.className = 'hikethere-map-controls flex flex-col gap-3 p-3';
        
        // Trail count control
        const trailCountControl = this.createTrailCountControl();
        controlDiv.appendChild(trailCountControl);
        
        // Performance control
        const performanceControl = this.createPerformanceControl();
        controlDiv.appendChild(performanceControl);
        
        // Hiking tools control
        const hikingControl = this.createHikingControl();
        controlDiv.appendChild(hikingControl);
        
        this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(controlDiv);
    }

    createTrailCountControl() {
        const control = document.createElement('div');
        control.className = 'bg-white rounded-lg shadow-lg border border-gray-200 p-4';
        control.innerHTML = `
            <div class="flex items-center gap-2 mb-2 text-sm font-medium text-gray-700">
                <span>🗺️</span>
                <span>Trail Count</span>
            </div>
            <div class="flex items-baseline gap-1">
                <span class="text-2xl font-bold text-green-600" id="filtered-count">0</span>
                <span class="text-sm text-gray-500">/ <span id="total-count">0</span></span>
            </div>
        `;
        return control;
    }

    createPerformanceControl() {
        const control = document.createElement('div');
        control.className = 'bg-white rounded-lg shadow-lg border border-gray-200 p-4';
        control.innerHTML = `
            <div class="flex items-center gap-2 mb-3 text-sm font-medium text-gray-700">
                <span>⚡</span>
                <span>Performance</span>
            </div>
            <div class="space-y-2">
                <label class="flex items-center gap-2 text-sm cursor-pointer text-gray-600 hover:text-gray-800">
                    <input type="checkbox" id="clustering-toggle" checked class="w-4 h-4 text-green-600 bg-white border-gray-300 rounded focus:ring-green-500">
                    <span>Clustering</span>
                </label>
                <label class="flex items-center gap-2 text-sm cursor-pointer text-gray-600 hover:text-gray-800">
                    <input type="checkbox" id="heatmap-toggle" class="w-4 h-4 text-green-600 bg-white border-gray-300 rounded focus:ring-green-500">
                    <span>Heatmap</span>
                </label>
            </div>
        `;
        
        // Add event listeners
        control.querySelector('#clustering-toggle').addEventListener('change', (e) => {
            this.toggleClustering(e.target.checked);
        });
        
        control.querySelector('#heatmap-toggle').addEventListener('change', (e) => {
            this.toggleHeatmap(e.target.checked);
        });
        
        return control;
    }

    createHikingControl() {
        const control = document.createElement('div');
        control.className = 'bg-white rounded-lg shadow-lg border border-gray-200 p-4';
        control.innerHTML = `
            <div class="flex items-center gap-2 mb-3 text-sm font-medium text-gray-700">
                <span>🏔️</span>
                <span>Hiking Tools</span>
            </div>
            <div class="space-y-2">
                <label class="flex items-center gap-2 text-sm cursor-pointer text-gray-600 hover:text-gray-800">
                    <input type="checkbox" id="trail-paths-toggle" class="w-4 h-4 text-green-600 bg-white border-gray-300 rounded focus:ring-green-500">
                    <span>Trail Paths</span>
                </label>
                <label class="flex items-center gap-2 text-sm cursor-pointer text-gray-600 hover:text-gray-800">
                    <input type="checkbox" id="elevation-toggle" class="w-4 h-4 text-green-600 bg-white border-gray-300 rounded focus:ring-green-500">
                    <span>Elevation</span>
                </label>
            </div>
        `;
        
        // Add event listeners
        control.querySelector('#trail-paths-toggle').addEventListener('change', (e) => {
            this.toggleTrailPaths(e.target.checked);
        });
        
        control.querySelector('#elevation-toggle').addEventListener('change', (e) => {
            this.toggleElevation(e.target.checked);
        });
        
        return control;
    }







    setupMapEvents() {
        // Map click events
        this.map.addListener('click', (event) => {
            this.handleMapClick(event);
        });
        
        // Map drag events for performance optimization
        this.map.addListener('dragstart', () => {
            this.pauseMarkerUpdates();
        });
        
        this.map.addListener('dragend', () => {
            this.resumeMarkerUpdates();
        });
        
        // Zoom events
        this.map.addListener('zoom_changed', () => {
            this.handleZoomChange();
        });
        
        // Bounds change events
        this.map.addListener('bounds_changed', () => {
            this.debounce(this.updateVisibleTrails.bind(this), 300);
        });
    }

    async loadTrails() {
        try {
            this.showLoading(true);
            
            const response = await fetch('/map/trails');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            this.trails = await response.json();
            this.filteredTrails = [...this.trails];
            
            this.updateTrailCounts();
            this.createTrailMarkers();
            this.setupMarkerClusterer();
            
            console.log(`Loaded ${this.trails.length} trails successfully`);
            
        } catch (error) {
            console.error('Error loading trails:', error);
            this.showError('Failed to load trails. Please try again.');
        } finally {
            this.showLoading(false);
        }
    }

    getValidImageUrl(imageUrl) {
        // If no image URL provided, use default
        if (!imageUrl) {
            return this.getAbsoluteUrl('/img/default-trail.jpg');
        }
        
        // If it's already an absolute URL (starts with http/https), return as is
        if (imageUrl.startsWith('http')) {
            return imageUrl;
        }
        
        // If it's a relative URL, make it absolute
        return this.getAbsoluteUrl(imageUrl);
    }

    getAbsoluteUrl(path) {
        // Create absolute URL from relative path
        const baseUrl = window.location.origin;
        const cleanPath = path.startsWith('/') ? path : '/' + path;
        return baseUrl + cleanPath;
    }

    createTrailMarkers() {
        this.clearMarkers();
        
        this.filteredTrails.forEach(trail => {
            if (trail.coordinates && trail.coordinates.lat && trail.coordinates.lng) {
                const marker = this.createTrailMarker(trail);
                this.markers.set(trail.id, marker);
            }
        });
    }

    createTrailMarker(trail) {
        const position = new google.maps.LatLng(trail.coordinates.lat, trail.coordinates.lng);
        
        const marker = new google.maps.Marker({
            position: position,
            map: this.map,
            title: trail.name,
            icon: this.getDifficultyIcon(trail.difficulty),
            animation: google.maps.Animation.DROP
        });

        // Add click event listener with zoom functionality
        marker.addListener('click', () => {
            // Zoom to the trail with appropriate zoom level
            this.zoomToTrail(trail, marker);
            this.showTrailInfo(trail, marker);
        });

        return marker;
    }

    zoomToTrail(trail, marker) {
        // Center the map on the trail
        const position = new google.maps.LatLng(trail.coordinates.lat, trail.coordinates.lng);
        this.map.panTo(position);
        
        // Set appropriate zoom level (closer for better trail viewing)
        const currentZoom = this.map.getZoom();
        const targetZoom = Math.max(currentZoom, 14); // Ensure minimum zoom level of 14
        
        // Smooth zoom animation
        this.map.setZoom(targetZoom);
        
        // Add a subtle bounce animation to the marker
        marker.setAnimation(google.maps.Animation.BOUNCE);
        setTimeout(() => {
            marker.setAnimation(null);
        }, 1500);
    }

    getDifficultyIcon(difficulty) {
        const colors = {
            'beginner': '#10B981',
            'intermediate': '#F59E0B',
            'advanced': '#EF4444'
        };
        
        const color = colors[difficulty] || colors.beginner;
        
        return {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 12,
            fillColor: color,
            fillOpacity: 0.8,
            strokeColor: '#FFFFFF',
            strokeWeight: 2
        };
    }

    showTrailInfo(trail, marker) {
        const content = this.createTrailInfoContent(trail);
        
        this.infoWindow.setContent(content);
        this.infoWindow.open(this.map, marker);
        
        // Update active trail
        this.activeTrail = trail;
        this.showTrailInfoPanel(trail);
    }

    createTrailInfoContent(trail) {
        // Get difficulty badge styling
        const difficultyStyles = {
            'beginner': 'bg-green-100 text-green-800 border-green-200',
            'intermediate': 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'advanced': 'bg-red-100 text-red-800 border-red-200'
        };
        
        const difficultyIcons = {
            'beginner': '🟢',
            'intermediate': '🟡', 
            'advanced': '🔴'
        };

        return `
            <div class="enhanced-trail-info-window max-w-sm bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden">
                <div class="relative h-40 overflow-hidden">
                    <img src="${trail.image_url || '/img/default-trail.jpg'}" 
                         alt="${trail.name}" 
                         class="w-full h-full object-cover"
                         onerror="this.src='/img/default-trail.jpg';">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                </div>
                
                <div class="p-3">
                    <!-- Trail Header -->
                    <div class="mb-3">
                        <h3 class="text-base font-bold text-gray-900 mb-1 leading-tight">${trail.name}</h3>
                        <div class="flex items-center text-xs text-gray-600">
                            <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 0 1111.314 0z"></path>
                        </svg>
                            <span class="truncate">${trail.location_name || 'Location not specified'}</span>
                    </div>
                    </div>

                    <!-- Trail Metadata -->
                    <div class="flex flex-wrap gap-1.5 mb-3">
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium border ${difficultyStyles[trail.difficulty] || difficultyStyles['intermediate']}">
                            <span class="mr-1">${difficultyIcons[trail.difficulty] || '🟡'}</span>
                            ${trail.difficulty ? trail.difficulty.charAt(0).toUpperCase() + trail.difficulty.slice(1) : 'Intermediate'}
                        </span>
                        ${trail.length ? `
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3l6-3"></path>
                                </svg>
                                ${trail.length} km
                            </span>
                        ` : ''}
                        ${trail.elevation_gain ? `
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                </svg>
                                +${trail.elevation_gain}m
                            </span>
                        ` : ''}
                    </div>

                    <!-- Trail Description (if available) -->
                    ${trail.description ? `
                        <div class="mb-3">
                            <p class="text-xs text-gray-700 line-clamp-2 leading-relaxed">${trail.description}</p>
                        </div>
                    ` : ''}

                    <!-- Action Buttons -->
                                            <div class="space-y-2">
                            <button onclick="window.hikeThereMap.getDirections('${trail.coordinates.lat},${trail.coordinates.lng}')" 
                                    class="w-full inline-flex items-center justify-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-1 transition-colors shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3l6-3"></path>
                                </svg>
                            Get Directions
                        </button>
                            <div class="grid grid-cols-2 gap-2">
                                <button onclick="window.hikeThereMap.showElevationProfile(${trail.id})" 
                                        class="inline-flex items-center justify-center px-3 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-1 transition-colors shadow-sm">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                    </svg>
                                    Elevation
                                </button>
                                <button onclick="window.hikeThereMap.showTrailDetails(${trail.id})" 
                                        class="inline-flex items-center justify-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition-colors shadow-sm">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Details
                        </button>
                            </div>
                    </div>
                </div>
            </div>
        `;
    }

    setupMarkerClusterer() {
        if (!this.config.isClusteringEnabled) return;
        
        const markers = Array.from(this.markers.values());
        
        if (typeof MarkerClusterer !== 'undefined') {
            this.markerClusterer = new MarkerClusterer(this.map, markers, {
                imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m',
                gridSize: 50,
                minimumClusterSize: 3,
                styles: this.getClusterStyles()
            });
        }
    }

    getClusterStyles() {
        return [
            {
                textColor: 'white',
                url: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiByeD0iMjAiIGZpbGw9IiMxMEI5ODEiLz4KPHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTEwIDVMMTUgMTBIMTVMMTAgMTVMMCA1SDEwWiIgZmlsbD0id2hpdGUiLz4KPC9zdmc+Cjwvc3ZnPgo=',
                height: 40,
                width: 40
            }
        ];
    }

    setupEventListeners() {
        // Search functionality
        const searchInput = document.getElementById('map-search');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.debounce(() => this.filterTrails(), 300);
            });
        }

        // Difficulty filter
        const difficultyFilter = document.getElementById('difficulty-filter');
        if (difficultyFilter) {
            difficultyFilter.addEventListener('change', (e) => {
                this.currentFilters.difficulty = e.target.value;
                this.filterTrails();
            });
        }

        // Radius filter
        const radiusFilter = document.getElementById('radius-filter');
        if (radiusFilter) {
            radiusFilter.addEventListener('change', (e) => {
                this.currentFilters.radius = parseInt(e.target.value);
                this.filterTrails();
            });
        }

        // Action buttons
        const useLocationBtn = document.getElementById('use-location-btn');
        if (useLocationBtn) {
            useLocationBtn.addEventListener('click', () => this.useCurrentLocation());
        }

        const resetMapBtn = document.getElementById('reset-map-btn');
        if (resetMapBtn) {
            resetMapBtn.addEventListener('click', () => this.resetMap());
        }

        const clusterToggleBtn = document.getElementById('cluster-toggle-btn');
        if (clusterToggleBtn) {
            clusterToggleBtn.addEventListener('click', () => this.toggleClustering());
        }
    }

    filterTrails() {
        const searchTerm = document.getElementById('map-search')?.value.toLowerCase() || '';
        const difficulty = document.getElementById('difficulty-filter')?.value || '';
        
        this.filteredTrails = this.trails.filter(trail => {
            const matchesSearch = !searchTerm || 
                trail.name.toLowerCase().includes(searchTerm) ||
                trail.mountain_name?.toLowerCase().includes(searchTerm) ||
                trail.location_name.toLowerCase().includes(searchTerm);
            
            const matchesDifficulty = !difficulty || trail.difficulty === difficulty;
            
            return matchesSearch && matchesDifficulty;
        });

        this.updateTrailCounts();
        this.createTrailMarkers();
        this.setupMarkerClusterer();
    }

    updateTrailCounts() {
        const filteredCount = document.getElementById('filtered-count');
        const totalCount = document.getElementById('total-count');
        
        if (filteredCount) filteredCount.textContent = this.filteredTrails.length;
        if (totalCount) totalCount.textContent = this.trails.length;
    }

    async useCurrentLocation() {
        try {
            this.showLoading(true);
            
            if (!navigator.geolocation) {
                throw new Error('Geolocation is not supported by this browser.');
            }

            const position = await new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(resolve, reject, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 60000
                });
            });

            const { latitude: lat, longitude: lng } = position.coords;
            
            this.currentLocation = { lat, lng };
            
            // Update map center
            this.map.setCenter(this.currentLocation);
            this.map.setZoom(12);
            
            // Add current location marker
            this.addCurrentLocationMarker();
            
            // Search for nearby trails
            await this.searchNearbyTrails(lat, lng, this.currentFilters.radius);
            
            this.showSuccess('Location found! Showing nearby trails.');
            
        } catch (error) {
            console.error('Error getting location:', error);
            this.showError('Unable to get your location. Please ensure location services are enabled.');
        } finally {
            this.showLoading(false);
        }
    }

    addCurrentLocationMarker() {
        if (this.currentLocationMarker) {
            this.currentLocationMarker.setMap(null);
        }
        
        this.currentLocationMarker = new google.maps.Marker({
            position: this.currentLocation,
            map: this.map,
            title: 'Your Location',
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 8,
                fillColor: '#4285F4',
                fillOpacity: 1,
                strokeColor: '#FFFFFF',
                strokeWeight: 2
            },
            animation: google.maps.Animation.BOUNCE
        });
        
        // Stop animation after 3 seconds
        setTimeout(() => {
            this.currentLocationMarker.setAnimation(null);
        }, 3000);
    }

    async searchNearbyTrails(lat, lng, radius) {
        try {
            const response = await fetch('/map/search-nearby', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({ lat, lng, radius })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const nearbyTrails = await response.json();
            this.filteredTrails = nearbyTrails;
            this.updateTrailCounts();
            this.createTrailMarkers();
            this.setupMarkerClusterer();
            
        } catch (error) {
            console.error('Error searching nearby trails:', error);
            this.showError('Failed to search nearby trails.');
        }
    }

    resetMap() {
        // Reset filters
        this.currentFilters = {
            difficulty: '',
            radius: 25,
            search: '',
            showTrailPaths: false,
            showElevation: false
        };
        
        // Reset UI elements
        const searchInput = document.getElementById('map-search');
        if (searchInput) searchInput.value = '';
        
        const difficultyFilter = document.getElementById('difficulty-filter');
        if (difficultyFilter) difficultyFilter.value = '';
        
        const radiusFilter = document.getElementById('radius-filter');
        if (radiusFilter) radiusFilter.value = '25';
        
        // Reset map view
        this.map.setCenter(this.config.defaultCenter);
        this.map.setZoom(this.config.defaultZoom);
        
        // Reset trails
        this.filteredTrails = [...this.trails];
        this.updateTrailCounts();
        this.createTrailMarkers();
        this.setupMarkerClusterer();
        
        // Remove current location marker
        if (this.currentLocationMarker) {
            this.currentLocationMarker.setMap(null);
            this.currentLocationMarker = null;
        }
        
        // Close info windows
        this.infoWindow.close();
        this.hideTrailInfoPanel();
        
        this.showSuccess('Map has been reset to default view.');
    }

    toggleClustering(enabled) {
        if (enabled === undefined) {
            this.config.isClusteringEnabled = !this.config.isClusteringEnabled;
        } else {
            this.config.isClusteringEnabled = enabled;
        }
        
        if (this.markerClusterer) {
            this.markerClusterer.clearMarkers();
            this.markerClusterer = null;
        }
        
        if (this.config.isClusteringEnabled) {
            this.setupMarkerClusterer();
        }
    }

    toggleHeatmap(enabled) {
        if (!enabled && this.heatmap) {
            this.heatmap.setMap(null);
            this.heatmap = null;
            return;
        }
        
        if (enabled && !this.heatmap && google.maps.visualization) {
            const heatmapData = this.trails.map(trail => {
                if (trail.coordinates && trail.coordinates.lat && trail.coordinates.lng) {
                    return new google.maps.LatLng(trail.coordinates.lat, trail.coordinates.lng);
                }
            }).filter(Boolean);
            
            this.heatmap = new google.maps.visualization.HeatmapLayer({
                data: heatmapData,
                radius: 30,
                opacity: 0.6
            });
            
            this.heatmap.setMap(this.map);
        }
    }

    toggleTrailPaths(enabled) {
        this.currentFilters.showTrailPaths = enabled;
        
        if (enabled) {
            this.loadTrailPaths();
        } else {
            this.clearTrailPaths();
        }
    }

    async loadTrailPaths() {
        try {
            const response = await fetch('/api/trails/paths');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const trailPaths = await response.json();
            
            trailPaths.forEach(trailPath => {
                if (trailPath.path_coordinates && trailPath.path_coordinates.length > 0) {
                    const path = new google.maps.Polyline({
                        path: trailPath.path_coordinates,
                        geodesic: true,
                        strokeColor: this.getTrailPathColor(trailPath.difficulty),
                        strokeOpacity: 0.8,
                        strokeWeight: 3
                    });
                    
                    path.setMap(this.map);
                    this.trailPaths.set(trailPath.id, path);
                }
            });
            
        } catch (error) {
            console.error('Error loading trail paths:', error);
            this.showError('Failed to load trail paths.');
        }
    }

    getTrailPathColor(difficulty) {
        const colors = {
            'beginner': '#10B981',
            'intermediate': '#F59E0B',
            'advanced': '#EF4444'
        };
        return colors[difficulty] || colors.beginner;
    }

    clearTrailPaths() {
        this.trailPaths.forEach(path => {
            path.setMap(null);
        });
        this.trailPaths.clear();
    }

    toggleElevation(enabled) {
        this.currentFilters.showElevation = enabled;
        
        if (enabled && this.activeTrail) {
            this.showElevationChart(this.activeTrail.id);
        } else {
            this.hideElevationChart();
        }
    }



    showTrailInfoPanel(trail) {
        const panel = document.getElementById('trail-info-panel');
        const content = document.getElementById('trail-info-content');
        const title = document.getElementById('trail-info-title');
        
        if (!panel || !content || !title) return;
        
        // Set active trail for weather management
        this.activeTrail = trail;
        
        title.textContent = trail.name;
        content.innerHTML = this.createDetailedTrailContent(trail);
        
        panel.classList.add('show');
        panel.style.transform = 'translateY(0)';
        
        // Setup close button
        const closeBtn = document.getElementById('close-trail-info');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.hideTrailInfoPanel());
        }
        
        // Auto-load weather, trail conditions, and enhanced images
        setTimeout(() => {
            this.autoLoadWeatherForTrail(trail.id, trail.coordinates.lat, trail.coordinates.lng);
            this.autoLoadTrailConditionsForTrail(trail.id);
            this.loadTrailGallery(trail.id);
        }, 500); // Small delay to let the panel appear first
    }

    hideTrailInfoPanel() {
        const panel = document.getElementById('trail-info-panel');
        if (panel) {
            panel.classList.remove('show');
            panel.style.transform = 'translateY(100%)';
            
            // Clear weather auto-refresh for the current trail
            if (this.activeTrail) {
                this.clearWeatherAutoRefresh(this.activeTrail.id);
            }
        }
    }

    createDetailedTrailContent(trail) {
        // Get difficulty badge styling
        const difficultyStyles = {
            'beginner': 'bg-green-100 text-green-800 border-green-200',
            'intermediate': 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'advanced': 'bg-red-100 text-red-800 border-red-200'
        };
        
        const difficultyIcons = {
            'beginner': '🟢',
            'intermediate': '🟡', 
            'advanced': '🔴'
        };

        return `
            <!-- Trail Header with Image -->
            <div class="relative h-32 mb-4 rounded-lg overflow-hidden">
                <img src="${trail.image_url || '/img/default-trail.jpg'}" 
                     alt="${trail.name}" 
                     class="w-full h-full object-cover"
                     onerror="this.src='/img/default-trail.jpg';">
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/10 to-transparent"></div>
                <div class="absolute bottom-2 left-2 right-2">
                    <h3 class="text-base font-bold text-white mb-1">${trail.name}</h3>
                    <div class="flex items-center text-white/90 text-xs">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 0 1111.314 0z"></path>
                        </svg>
                        <span>${trail.location_name || 'Location not specified'}</span>
                </div>
                </div>
            </div>

            <!-- Compact Trail Metadata -->
            <div class="grid grid-cols-2 gap-3 mb-4">
                <!-- Difficulty Badge -->
                <div class="p-3 bg-white rounded-lg border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-500">Difficulty</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium border ${difficultyStyles[trail.difficulty] || difficultyStyles['intermediate']}">
                            <span class="mr-1">${difficultyIcons[trail.difficulty] || '🟡'}</span>
                            ${trail.difficulty ? trail.difficulty.charAt(0).toUpperCase() + trail.difficulty.slice(1) : 'Intermediate'}
                        </span>
                    </div>
                </div>
                
                ${trail.length ? `
                <!-- Distance -->
                <div class="p-3 bg-white rounded-lg border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-500">Distance</span>
                        <span class="text-sm font-bold text-gray-900">${trail.length} km</span>
                    </div>
                </div>
                ` : ''}
                
                ${trail.elevation_gain ? `
                <!-- Elevation Gain -->
                <div class="p-3 bg-white rounded-lg border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-500">Elevation</span>
                        <span class="text-sm font-bold text-gray-900">+${trail.elevation_gain}m</span>
                </div>
                </div>
                ` : ''}
                
                ${trail.estimated_time ? `
                <!-- Estimated Time -->
                <div class="p-3 bg-white rounded-lg border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-500">Est. Time</span>
                        <span class="text-sm font-bold text-gray-900">${trail.estimated_time}</span>
                    </div>
                </div>
                ` : ''}
            </div>
            
            ${trail.description ? `
            <!-- Trail Description -->
            <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                <h5 class="text-sm font-semibold text-gray-800 mb-2">Description</h5>
                <p class="text-sm text-gray-700 leading-relaxed">${trail.description}</p>
            </div>
            ` : ''}
            
            <!-- Trail Image Gallery -->
            <div class="mb-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="p-3 bg-gradient-to-r from-purple-500 to-pink-500 rounded-t-lg">
                    <div class="flex items-center gap-2 text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4a2 2 0 012-2z"></path>
                        </svg>
                        <span class="text-sm font-semibold">Image Gallery</span>
                    </div>
                </div>
                <div class="p-3">
                    <div id="trail-gallery-${trail.id}" class="trail-gallery-placeholder">
                        <div class="text-center text-gray-600 text-sm">
                            <div class="inline-block animate-spin rounded-full h-4 w-4 border-2 border-purple-200 border-t-purple-600 mr-2"></div>
                            Loading images...
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Compact Information Cards -->
            <div class="grid grid-cols-1 gap-3 mb-4">
                <!-- Weather Information Card -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                    <div class="p-3 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-t-lg">
                        <div class="flex items-center gap-2 text-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                            </svg>
                            <h5 class="text-sm font-semibold">Current Weather</h5>
                        </div>
                    </div>
                    <div class="p-3">
                        <div id="weather-data-${trail.id}" class="weather-data-placeholder">
                            <div class="text-center text-gray-600 text-sm">
                                <div class="inline-block animate-spin rounded-full h-4 w-4 border-2 border-blue-200 border-t-blue-600 mr-2"></div>
                                Loading weather data...
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trail Conditions Card -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                    <div class="p-3 bg-gradient-to-r from-green-500 to-emerald-500 rounded-t-lg">
                        <div class="flex items-center gap-2 text-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h5 class="text-sm font-semibold">Trail Status</h5>
                        </div>
                    </div>
                    <div class="p-3">
                <div id="trail-conditions-${trail.id}" class="trail-conditions-placeholder">
                            <div class="text-center text-gray-600 text-sm">
                                <div class="inline-block animate-spin rounded-full h-4 w-4 border-2 border-green-200 border-t-green-600 mr-2"></div>
                                Loading trail conditions...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Compact Action Buttons -->
            <div class="space-y-2">
                <div class="space-y-2">
                    <button onclick="window.hikeThereMap.getDirections('${trail.coordinates.lat},${trail.coordinates.lng}')" 
                            class="w-full inline-flex items-center justify-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-1 transition-colors shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3l6-3"></path>
                        </svg>
                        Get Directions
                    </button>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="window.hikeThereMap.showElevationProfile(${trail.id})" 
                                class="inline-flex items-center justify-center px-3 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-1 transition-colors shadow-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                            </svg>
                            Elevation
                        </button>
                        <button onclick="window.hikeThereMap.showTrailDetails(${trail.id})" 
                                class="inline-flex items-center justify-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition-colors shadow-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Details
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    // Auto-loading methods for weather and trail conditions
    async autoLoadWeatherForTrail(trailId, lat, lng) {
        const weatherElement = document.getElementById(`weather-data-${trailId}`);
        if (!weatherElement) return;
        
        try {
            // Simulate weather API call - replace with actual API
            const weatherData = await this.fetchWeatherData(lat, lng);
            
            weatherElement.innerHTML = `
                <div class="space-y-3">
                    <!-- Weather Header with Refresh Button -->
                    <div class="flex items-center justify-between">
                        <h6 class="text-sm font-semibold text-gray-700">Current Weather</h6>
                        <button onclick="window.hikeThereMap.refreshWeatherForTrail('${trailId}', ${lat}, ${lng})" 
                                class="p-1 text-gray-400 hover:text-blue-600 transition-colors" 
                                title="Refresh weather data">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Main Weather Info -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="text-center p-2 bg-blue-50 rounded-lg">
                            <div class="text-lg font-bold text-blue-600">${weatherData.temperature}</div>
                            <div class="text-xs text-blue-500">Temperature</div>
                        </div>
                        <div class="text-center p-2 bg-cyan-50 rounded-lg">
                            <div class="text-lg font-bold text-cyan-600">${weatherData.conditions}</div>
                            <div class="text-xs text-cyan-500">Conditions</div>
                        </div>
                    </div>
                    
                    <!-- Detailed Weather Info -->
                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Humidity:</span>
                            <span class="font-medium text-gray-900">${weatherData.humidity}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Wind Speed:</span>
                            <span class="font-medium text-gray-900">${weatherData.wind}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">UV Index:</span>
                            <span class="font-medium text-gray-900">${weatherData.uvIndex}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Pressure:</span>
                            <span class="font-medium text-gray-900">${weatherData.pressure} hPa</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Visibility:</span>
                            <span class="font-medium text-gray-900">${weatherData.visibility} km</span>
                        </div>
                    </div>
                    
                    <!-- Weather Description -->
                    ${weatherData.description ? `
                        <div class="pt-2 border-t border-gray-200">
                            <p class="text-xs text-gray-600 italic">${weatherData.description}</p>
                        </div>
                    ` : ''}
                    
                    <!-- Last Updated with Auto-refresh Indicator -->
                    <div class="text-center text-xs text-gray-400">
                        <div class="flex items-center justify-center gap-2">
                            <span>Last updated: ${new Date(weatherData.timestamp).toLocaleTimeString()}</span>
                            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse" title="Auto-refreshing"></div>
                        </div>
                    </div>
                </div>
            `;
        } catch (error) {
            console.error('Error loading weather data:', error);
            weatherElement.innerHTML = `
                <div class="text-center text-red-600 text-sm">
                    <svg class="w-4 h-4 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Weather data unavailable
                </div>
            `;
        }
    }

    // Manual refresh function for weather data
    async refreshWeatherForTrail(trailId, lat, lng) {
        const weatherElement = document.getElementById(`weather-data-${trailId}`);
        if (!weatherElement) return;
        
        // Show refreshing state
        const refreshButton = weatherElement.querySelector('button');
        if (refreshButton) {
            const originalContent = refreshButton.innerHTML;
            refreshButton.innerHTML = `
                <div class="animate-spin rounded-full h-4 w-4 border-2 border-blue-200 border-t-blue-600"></div>
            `;
            refreshButton.disabled = true;
            
            try {
                await this.autoLoadWeatherForTrail(trailId, lat, lng);
            } finally {
                refreshButton.innerHTML = originalContent;
                refreshButton.disabled = false;
            }
        }
    }

    // Set up auto-refresh for weather data every 5 minutes
    setupWeatherAutoRefresh(trailId, lat, lng) {
        // Clear any existing interval for this trail
        if (this.weatherRefreshIntervals && this.weatherRefreshIntervals[trailId]) {
            clearInterval(this.weatherRefreshIntervals[trailId]);
        }
        
        // Initialize intervals object if it doesn't exist
        if (!this.weatherRefreshIntervals) {
            this.weatherRefreshIntervals = {};
        }
        
        // Set up new interval (5 minutes = 300000 ms)
        this.weatherRefreshIntervals[trailId] = setInterval(async () => {
            try {
                await this.autoLoadWeatherForTrail(trailId, lat, lng);
                console.log(`Auto-refreshed weather for trail ${trailId}`);
            } catch (error) {
                console.error(`Error auto-refreshing weather for trail ${trailId}:`, error);
            }
        }, 300000); // 5 minutes
    }

    // Clear weather auto-refresh for a specific trail
    clearWeatherAutoRefresh(trailId) {
        if (this.weatherRefreshIntervals && this.weatherRefreshIntervals[trailId]) {
            clearInterval(this.weatherRefreshIntervals[trailId]);
            delete this.weatherRefreshIntervals[trailId];
        }
    }

    async autoLoadTrailConditionsForTrail(trailId) {
        const conditionsElement = document.getElementById(`trail-conditions-${trailId}`);
        if (!conditionsElement) return;
        
        try {
            // Simulate trail conditions API call - replace with actual API
            const conditionsData = await this.fetchTrailConditions(trailId);
            
            conditionsElement.innerHTML = `
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Status:</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${conditionsData.status === 'open' ? 'bg-green-100 text-green-800' : conditionsData.status === 'caution' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'}">
                            ${conditionsData.status?.toUpperCase() || 'OPEN'}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Trail Quality:</span>
                        <span class="text-sm font-medium text-gray-900">${conditionsData.quality || 'Good'}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Last Updated:</span>
                        <span class="text-sm font-medium text-gray-900">${conditionsData.lastUpdated || 'Today'}</span>
                    </div>
                    ${conditionsData.notes ? `
                        <div class="pt-2 border-t border-gray-200">
                            <p class="text-xs text-gray-600">${conditionsData.notes}</p>
                        </div>
                    ` : ''}
                </div>
            `;
        } catch (error) {
            console.error('Error loading trail conditions:', error);
            conditionsElement.innerHTML = `
                <div class="text-center text-red-600 text-sm">
                    <svg class="w-4 h-4 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Trail conditions unavailable
                </div>
            `;
        }
    }

    // Real API methods for weather and trail conditions
    async fetchWeatherData(lat, lng) {
        try {
            const response = await fetch(`/api/weather?lat=${lat}&lng=${lng}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                }
            });

            if (!response.ok) {
                throw new Error(`Weather API error: ${response.status}`);
            }

            const weatherData = await response.json();
            
            return {
                temperature: `${weatherData.temperature}°C`,
                conditions: weatherData.condition || 'Unknown',
                humidity: `${weatherData.humidity}%`,
                wind: `${weatherData.windSpeed} km/h`,
                uvIndex: weatherData.uvIndex || 'N/A',
                pressure: weatherData.pressure || 'N/A',
                visibility: weatherData.visibility || 'N/A',
                description: weatherData.description || '',
                timestamp: weatherData.timestamp || new Date().toISOString()
            };
        } catch (error) {
            console.error('Error fetching weather data:', error);
            // Return fallback data if API fails
            return {
                temperature: 'N/A',
                conditions: 'Unavailable',
                humidity: 'N/A',
                wind: 'N/A',
                uvIndex: 'N/A',
                pressure: 'N/A',
                visibility: 'N/A',
                description: 'Weather data temporarily unavailable',
                timestamp: new Date().toISOString()
            };
        }
    }

    async fetchTrailConditions(trailId) {
        // Simulate API delay
        await new Promise(resolve => setTimeout(resolve, 1200));
        
        // Return mock trail conditions - replace with actual trail conditions API
        const statuses = ['open', 'caution', 'closed'];
        const qualities = ['Excellent', 'Good', 'Fair', 'Poor'];
        const status = statuses[Math.floor(Math.random() * 3)];
        
        return {
            status: status,
            quality: qualities[Math.floor(Math.random() * 4)],
            lastUpdated: 'Today',
            notes: status === 'caution' ? 'Recent rainfall may cause muddy conditions' : status === 'closed' ? 'Trail maintenance in progress' : null
        };
    }

    // Utility methods
    clearMarkers() {
        this.markers.forEach(marker => {
            marker.setMap(null);
        });
        this.markers.clear();
    }

    debounce(func, wait) {
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(func, wait);
    }

    showLoading(show) {
        const spinner = document.getElementById('loading-spinner');
        if (spinner) {
            spinner.style.display = show ? 'flex' : 'none';
        }
        this.isLoading = show;
    }

    showError(message) {
        this.showNotification(message, 'error');
    }

    showSuccess(message) {
        this.showNotification(message, 'success');
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `enhanced-notification ${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-icon">${type === 'error' ? '❌' : type === 'success' ? '✅' : 'ℹ️'}</span>
                <span class="notification-message">${message}</span>
                <button class="notification-close" onclick="this.parentElement.parentElement.remove()">×</button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }

    // Initialize additional features
    initializeHikingLayers() {
        // Initialize hiking-specific layers
        console.log('Hiking layers initialized');
    }

    setupSearchAutocomplete() {
        const searchInput = document.getElementById('map-search');
        if (searchInput && google.maps.places) {
            this.searchAutocomplete = new google.maps.places.Autocomplete(searchInput, {
                types: ['establishment', 'geocode', 'natural_feature'], // Include businesses, addresses, and natural features
                componentRestrictions: { country: 'ph' }, // Philippines
                fields: ['formatted_address', 'name', 'place_id', 'geometry', 'types']
            });

            // Add place_changed event listener
            this.searchAutocomplete.addListener('place_changed', () => {
                const place = this.searchAutocomplete.getPlace();
                if (place.geometry) {
                    // Center map on selected location
                    const position = new google.maps.LatLng(place.geometry.location.lat(), place.geometry.location.lng());
                    this.map.panTo(position);
                    this.map.setZoom(14);
                    
                    // Add a marker for the searched location
                    this.addSearchResultMarker(place);
                    
                    // Update search input placeholder to show what was found
                    searchInput.placeholder = `Found: ${place.name || place.formatted_address}`;
                    
                    // Clear search input after a delay
                    setTimeout(() => {
                        searchInput.value = '';
                        searchInput.placeholder = 'Search trails by name, mountain, or location...';
                    }, 2000);
                }
            });
        }
    }

    addSearchResultMarker(place) {
        // Remove previous search marker if exists
        if (this.searchResultMarker) {
            this.searchResultMarker.setMap(null);
        }

        const position = new google.maps.LatLng(place.geometry.location.lat(), place.geometry.location.lng());
        
        // Create a special marker for search results
        this.searchResultMarker = new google.maps.Marker({
            position: position,
            map: this.map,
            title: place.name || place.formatted_address,
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 14,
                fillColor: '#3B82F6', // Blue color for search results
                fillOpacity: 0.8,
                strokeColor: '#FFFFFF',
                strokeWeight: 2
            },
            animation: google.maps.Animation.BOUNCE
        });

        // Add info window for search result
        const infoWindow = new google.maps.InfoWindow({
            content: `
                <div class="p-3 max-w-[250px]">
                    <h3 class="font-semibold text-gray-900 text-sm mb-2">${place.name || 'Location'}</h3>
                    <p class="text-sm text-gray-600 mb-2">${place.formatted_address}</p>
                    <div class="text-xs text-gray-500">
                        <span class="font-medium">Type:</span> ${place.types ? place.types[0].replace(/_/g, ' ') : 'Location'}
                    </div>
                </div>
            `
        });

        this.searchResultMarker.addListener('click', () => {
            infoWindow.open(this.map, this.searchResultMarker);
        });

        // Auto-open info window
        infoWindow.open(this.map, this.searchResultMarker);

        // Stop bouncing after 2 seconds
        setTimeout(() => {
            if (this.searchResultMarker) {
                this.searchResultMarker.setAnimation(null);
            }
        }, 2000);
    }

    initializeHeatmap() {
        // Heatmap will be initialized when toggled
        console.log('Heatmap ready for initialization');
    }

    setupDirections() {
        // Directions service already initialized
        console.log('Directions service ready');
    }

    setupPerformanceMonitoring() {
        // Monitor map performance
        this.map.addListener('idle', () => {
            const zoom = this.map.getZoom();
            const center = this.map.getCenter();
            console.log(`Map idle at zoom ${zoom}, center: ${center.lat()}, ${center.lng()}`);
        });
    }

    // Additional hiking features
    handleMapClick(event) {
        console.log('Map clicked at:', event.latLng.lat(), event.latLng.lng());
    }

    pauseMarkerUpdates() {
        this.isUpdatingMarkers = true;
    }

    resumeMarkerUpdates() {
        this.isUpdatingMarkers = false;
    }

    handleZoomChange() {
        const zoom = this.map.getZoom();
        console.log('Zoom changed to:', zoom);
    }

    updateVisibleTrails() {
        // Update visible trails based on map bounds
        const bounds = this.map.getBounds();
        if (!bounds) return;
        
        // Implementation for updating visible trails
        console.log('Updating visible trails');
    }

    // External API methods
    getDirections(destination) {
        if (!this.currentLocation) {
            this.showError('Please enable location services to get directions.');
            return;
        }
        
        const destination_coords = destination.split(',');
        const dest = new google.maps.LatLng(parseFloat(destination_coords[0]), parseFloat(destination_coords[1]));
        
        const request = {
            origin: this.currentLocation,
            destination: dest,
            travelMode: google.maps.TravelMode.DRIVING
        };
        
        this.directionsService.route(request, (result, status) => {
            if (status === 'OK') {
                this.directionsRenderer.setDirections(result);
            } else {
                this.showError('Directions request failed: ' + status);
            }
        });
    }

    showTrailDetails(trailId) {
        // Navigate to trail details page
        window.open(`/trails/${trailId}`, '_blank');
    }

    shareTrail(trailId) {
        if (navigator.share) {
            navigator.share({
                title: 'Check out this hiking trail!',
                url: `${window.location.origin}/trails/${trailId}`
            });
        } else {
            // Fallback: copy to clipboard
            const url = `${window.location.origin}/trails/${trailId}`;
            navigator.clipboard.writeText(url).then(() => {
                this.showSuccess('Trail link copied to clipboard!');
            });
        }
    }

    async loadWeatherForTrail(trailId, lat, lng) {
        try {
            const weatherElement = document.getElementById(`weather-data-${trailId}`);
            if (!weatherElement) return;

            weatherElement.innerHTML = `
                <div class="flex items-center justify-center py-4">
                    <div class="animate-spin rounded-full h-6 w-6 border-2 border-blue-500 border-t-transparent"></div>
                    <span class="ml-2 text-gray-600">Loading weather...</span>
                </div>
            `;

            const response = await fetch(`/api/weather?lat=${lat}&lng=${lng}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const weatherData = await response.json();
            
            weatherElement.innerHTML = `
                <div class="grid grid-cols-2 gap-4">
                    <div class="weather-item">
                        <span class="text-sm font-medium text-gray-600">Temperature:</span>
                        <span class="text-lg font-bold text-blue-600">${weatherData.temperature}°C</span>
                    </div>
                    <div class="weather-item">
                        <span class="text-sm font-medium text-gray-600">Conditions:</span>
                        <span class="text-sm font-medium text-gray-800">${weatherData.conditions}</span>
                    </div>
                    <div class="weather-item">
                        <span class="text-sm font-medium text-gray-600">Wind:</span>
                        <span class="text-sm font-medium text-gray-800">${weatherData.wind_speed} km/h</span>
                    </div>
                    <div class="weather-item">
                        <span class="text-sm font-medium text-gray-600">Humidity:</span>
                        <span class="text-sm font-medium text-gray-800">${weatherData.humidity}%</span>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-blue-200">
                    <button onclick="hikeThereMap.loadWeatherForTrail(${trailId}, ${lat}, ${lng})" 
                            class="px-3 py-1 bg-blue-500 text-white text-xs rounded-lg hover:bg-blue-600 transition-colors">
                        🔄 Refresh
                    </button>
                </div>
            `;

        } catch (error) {
            console.error('Error loading weather for trail:', error);
            const weatherElement = document.getElementById(`weather-data-${trailId}`);
            if (weatherElement) {
                weatherElement.innerHTML = `
                    <div class="text-center py-4">
                        <div class="text-red-500 text-sm mb-2">Failed to load weather data</div>
                        <button onclick="hikeThereMap.loadWeatherForTrail(${trailId}, ${lat}, ${lng})" 
                                class="px-3 py-1 bg-blue-500 text-white text-xs rounded-lg hover:bg-blue-600 transition-colors">
                            🔄 Retry
                        </button>
                    </div>
                `;
            }
        }
    }

    async loadTrailConditionsForTrail(trailId) {
        try {
            const conditionsElement = document.getElementById(`trail-conditions-${trailId}`);
            if (!conditionsElement) return;

            conditionsElement.innerHTML = `
                <div class="flex items-center justify-center py-4">
                    <div class="animate-spin rounded-full h-6 w-6 border-2 border-green-500 border-t-transparent"></div>
                    <span class="ml-2 text-gray-600">Loading conditions...</span>
                </div>
            `;

            const response = await fetch('/api/hiking/trail-conditions');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const conditionsData = await response.json();
            const trailConditions = conditionsData.trails?.find(t => t.id == trailId);
            
            if (trailConditions) {
                conditionsElement.innerHTML = `
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">Status:</span>
                            <span class="px-2 py-1 text-xs font-medium rounded-full ${trailConditions.status === 'open' ? 'bg-green-100 text-green-800' : trailConditions.status === 'caution' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'}">
                                ${trailConditions.status?.toUpperCase() || 'UNKNOWN'}
                            </span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-600">Conditions:</span>
                            <p class="text-sm text-gray-800 mt-1">${trailConditions.conditions || 'No information available'}</p>
                        </div>
                        ${trailConditions.hazards?.length > 0 ? `
                            <div>
                                <span class="text-sm font-medium text-red-600">⚠️ Hazards:</span>
                                <ul class="text-sm text-red-600 mt-1 ml-4">
                                    ${trailConditions.hazards.map(hazard => `<li>• ${hazard}</li>`).join('')}
                                </ul>
                            </div>
                        ` : ''}
                        <div class="pt-2 border-t border-green-200">
                            <button onclick="hikeThereMap.loadTrailConditionsForTrail(${trailId})" 
                                    class="px-3 py-1 bg-green-500 text-white text-xs rounded-lg hover:bg-green-600 transition-colors">
                                🔄 Refresh
                            </button>
                        </div>
                    </div>
                `;
            } else {
                conditionsElement.innerHTML = `
                    <div class="text-center py-4">
                        <div class="text-gray-500 text-sm mb-2">No conditions data available</div>
                        <button onclick="hikeThereMap.loadTrailConditionsForTrail(${trailId})" 
                                class="px-3 py-1 bg-green-500 text-white text-xs rounded-lg hover:bg-green-600 transition-colors">
                            🔄 Retry
                        </button>
                    </div>
                `;
            }

        } catch (error) {
            console.error('Error loading trail conditions for trail:', error);
            const conditionsElement = document.getElementById(`trail-conditions-${trailId}`);
            if (conditionsElement) {
                conditionsElement.innerHTML = `
                    <div class="text-center py-4">
                        <div class="text-red-500 text-sm mb-2">Failed to load conditions</div>
                        <button onclick="hikeThereMap.loadTrailConditionsForTrail(${trailId})" 
                                class="px-3 py-1 bg-green-500 text-white text-xs rounded-lg hover:bg-green-600 transition-colors">
                            🔄 Retry
                        </button>
                    </div>
                `;
            }
        }
    }

    async showElevationProfile(trailId) {
        try {
            const response = await fetch(`/map/trails/${trailId}/elevation`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const elevationData = await response.json();
            
            // Create elevation profile modal
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50';
            
            modal.innerHTML = `
                <div class="bg-white rounded-2xl max-w-5xl w-full mx-4 shadow-2xl border border-gray-200 max-h-[90vh] overflow-hidden">
                    <!-- Header -->
                    <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gradient-to-r from-purple-500 to-indigo-600">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white">Elevation Profile</h3>
                                <p class="text-purple-100 text-sm">Trail elevation analysis</p>
                        </div>
                        </div>
                        <button class="text-white/80 hover:text-white transition-colors p-2 hover:bg-white/10 rounded-lg" onclick="this.closest('.fixed').remove()">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Content -->
                    <div class="p-6 overflow-y-auto">
                        <!-- Stats Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-xl p-4 text-center">
                                <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                    </svg>
                            </div>
                                <div class="text-2xl font-bold text-purple-700">${elevationData.total_gain}m</div>
                                <div class="text-sm text-purple-600 font-medium">Total Gain</div>
                            </div>
                            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 border border-indigo-200 rounded-xl p-4 text-center">
                                <div class="w-12 h-12 bg-indigo-500 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z"></path>
                                    </svg>
                                </div>
                                <div class="text-2xl font-bold text-indigo-700">${elevationData.max_elevation}m</div>
                                <div class="text-sm text-indigo-600 font-medium">Max Elevation</div>
                            </div>
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-4 text-center">
                                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                    </svg>
                                </div>
                                <div class="text-2xl font-bold text-blue-700">${elevationData.min_elevation}m</div>
                                <div class="text-sm text-blue-600 font-medium">Min Elevation</div>
                            </div>
                        </div>
                        
                        <!-- Elevation Points Table -->
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                            <div class="p-4 border-b border-gray-200 bg-gray-50 rounded-t-xl">
                                <h4 class="font-semibold text-gray-800 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    Elevation Points
                                </h4>
                                    </div>
                            <div class="auto-hide-scrollbar max-h-80 overflow-y-auto">
                                <div class="p-4">
                                    <!-- Table Header -->
                                    <div class="grid grid-cols-3 gap-4 mb-3 text-sm font-medium text-gray-600 border-b border-gray-200 pb-2">
                                        <div>Distance</div>
                                        <div>Elevation</div>
                                        <div>Grade</div>
                            </div>
                                    <!-- Table Rows -->
                                    <div class="space-y-2">
                                        ${elevationData.points?.map((point, index) => `
                                            <div class="grid grid-cols-3 gap-4 p-3 hover:bg-gray-50 rounded-lg transition-colors border border-transparent hover:border-gray-200">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-2 h-2 bg-purple-400 rounded-full"></div>
                                                    <span class="text-sm font-medium text-gray-800">${point.distance.toFixed(1)} km</span>
                                                </div>
                                                <div class="text-sm text-gray-700 font-medium">${point.elevation}m</div>
                                                <div class="flex items-center gap-1">
                                                    <span class="text-xs px-2 py-1 rounded-full ${point.grade > 5 ? 'bg-red-100 text-red-700' : point.grade > 0 ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700'}">
                                                        ${point.grade > 0 ? '↗' : point.grade < 0 ? '↘' : '→'} ${point.grade.toFixed(1)}%
                                                    </span>
                                                </div>
                                            </div>
                                        `).join('') || '<div class="text-gray-500 text-center py-8">No elevation data available</div>'}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            // Setup auto-hiding scrollbars
            this.setupAutoHideScrollbars(modal);
            
            // Close modal when clicking outside
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.remove();
                }
            });

        } catch (error) {
            console.error('Error loading elevation profile:', error);
            this.showError('Failed to load elevation profile');
        }
    }

    showDirectionsDialog() {
        // Show directions dialog
        this.showNotification('Click on a trail marker to get directions');
    }

    toggleDistanceMeasurement() {
        // Toggle distance measurement tool
        this.showNotification('Distance measurement tool activated');
    }

    saveCurrentLocation() {
        if (this.currentLocation) {
            localStorage.setItem('hikethere_saved_location', JSON.stringify(this.currentLocation));
            this.showSuccess('Location saved!');
        } else {
            this.showError('No location to save');
        }
    }

    /**
     * Load trail gallery with organization + API images
     */
    async loadTrailGallery(trailId) {
        try {
            // Check if we have a gallery container in the trail panel
            const galleryContainer = document.getElementById(`trail-gallery-${trailId}`);
            if (!galleryContainer) return;

            // Show loading state
            galleryContainer.innerHTML = `
                <div class="text-center text-gray-600 text-sm py-4">
                    <div class="inline-block animate-spin rounded-full h-4 w-4 border-2 border-blue-200 border-t-blue-600 mr-2"></div>
                    Loading image gallery...
                </div>
            `;

            const response = await fetch(`/map/trails/${trailId}/images`);
            if (!response.ok) throw new Error('Failed to fetch trail images');

            const data = await response.json();
            const images = data.images || [];

            if (images.length === 0) {
                galleryContainer.innerHTML = `
                    <p class="text-gray-500 text-center text-sm py-4">No images available</p>
                `;
                return;
            }

            // Create image gallery
            galleryContainer.innerHTML = `
                <div class="grid grid-cols-2 gap-2">
                    ${images.slice(0, 4).map((image, index) => `
                        <div class="relative group cursor-pointer rounded-lg overflow-hidden ${index === 0 ? 'col-span-2' : ''}" 
                             onclick="window.hikeThereMap.showImageModal(${trailId}, ${index})">
                            <img src="${image.thumb_url || image.url}" 
                                 alt="${image.caption || 'Trail image'}"
                                 class="w-full ${index === 0 ? 'h-32' : 'h-20'} object-cover transition-transform duration-200 group-hover:scale-105"
                                 onerror="this.src='/img/default-trail.jpg';">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors">
                                <div class="absolute bottom-1 left-1">
                                    <span class="text-xs px-1.5 py-0.5 rounded ${this.getImageSourceBadgeStyle(image.source)}">
                                        ${this.getImageSourceLabel(image.source)}
                                    </span>
                                </div>
                                ${index === 3 && images.length > 4 ? `
                                    <div class="absolute inset-0 bg-black/60 flex items-center justify-center">
                                        <span class="text-white text-sm font-medium">+${images.length - 4} more</span>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    `).join('')}
                </div>
                ${images.length > 4 ? `
                    <button onclick="window.hikeThereMap.showImageModal(${trailId}, 0)"
                            class="w-full mt-2 text-xs text-blue-600 hover:text-blue-700 font-medium">
                        View all ${images.length} images
                    </button>
                ` : ''}
            `;

            // Store images data for modal
            this.trailImages = this.trailImages || {};
            this.trailImages[trailId] = images;

        } catch (error) {
            console.error('Error loading trail gallery:', error);
            const galleryContainer = document.getElementById(`trail-gallery-${trailId}`);
            if (galleryContainer) {
                galleryContainer.innerHTML = `
                    <p class="text-red-500 text-center text-sm py-4">Failed to load images</p>
                `;
            }
        }
    }

    /**
     * Get badge style for image source
     */
    getImageSourceBadgeStyle(source) {
        const styles = {
            'organization': 'bg-green-600 text-white',
            'unsplash': 'bg-purple-600 text-white',
            'pexels': 'bg-blue-600 text-white',
            'pixabay': 'bg-yellow-600 text-white',
            'google_places': 'bg-red-600 text-white',
            'default': 'bg-gray-600 text-white'
        };
        return styles[source] || styles.default;
    }

    /**
     * Get label for image source
     */
    getImageSourceLabel(source) {
        const labels = {
            'organization': 'ORG',
            'unsplash': 'UNS',
            'pexels': 'PEX',
            'pixabay': 'PIX',
            'google_places': 'MAP',
            'default': 'DEF'
        };
        return labels[source] || labels.default;
    }

    /**
     * Show image modal with gallery
     */
    showImageModal(trailId, startIndex = 0) {
        const images = this.trailImages?.[trailId];
        if (!images || images.length === 0) return;

        let currentIndex = startIndex;

        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black/90 flex items-center justify-center z-[60]';
        
        const updateModalContent = () => {
            const image = images[currentIndex];
            modal.innerHTML = `
                <div class="relative max-w-4xl max-h-full mx-4 bg-white rounded-xl overflow-hidden">
                    <!-- Header -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 border-b">
                        <div>
                            <h3 class="font-semibold text-gray-900">${image.caption || 'Trail Image'}</h3>
                            <p class="text-sm text-gray-600">
                                ${currentIndex + 1} of ${images.length} • 
                                <span class="font-medium">${this.getImageSourceLabel(image.source)}</span>
                                ${image.photographer ? ` by ${image.photographer}` : ''}
                            </p>
                        </div>
                        <button onclick="this.closest('.fixed').remove()" 
                                class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Image -->
                    <div class="relative">
                        <img src="${image.url}" alt="${image.caption || 'Trail image'}"
                             class="w-full max-h-[60vh] object-contain bg-gray-100"
                             onerror="this.src='/img/default-trail.jpg';">
                        
                        <!-- Navigation -->
                        ${images.length > 1 ? `
                            <button onclick="window.hikeThereMap.previousImage()" 
                                    class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white rounded-full p-2 shadow-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                            <button onclick="window.hikeThereMap.nextImage()" 
                                    class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white rounded-full p-2 shadow-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        ` : ''}
                    </div>
                    
                    <!-- Attribution -->
                    ${image.photographer_url ? `
                        <div class="p-4 bg-gray-50 text-sm text-gray-600">
                            Photo by <a href="${image.photographer_url}" target="_blank" class="text-blue-600 hover:underline">${image.photographer}</a>
                            on ${image.source.charAt(0).toUpperCase() + image.source.slice(1)}
                        </div>
                    ` : ''}
                </div>
            `;
        };

        updateModalContent();
        document.body.appendChild(modal);

        // Store references for navigation
        this.currentImageModal = {
            modal,
            updateContent: updateModalContent,
            currentIndex: () => currentIndex,
            setIndex: (index) => { currentIndex = index; },
            images,
            maxIndex: images.length - 1
        };

        // Close modal when clicking outside
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
                this.currentImageModal = null;
            }
        });

        // Keyboard navigation
        const handleKeyPress = (e) => {
            if (!this.currentImageModal) return;
            
            if (e.key === 'ArrowLeft') {
                this.previousImage();
            } else if (e.key === 'ArrowRight') {
                this.nextImage();
            } else if (e.key === 'Escape') {
                modal.remove();
                this.currentImageModal = null;
                document.removeEventListener('keydown', handleKeyPress);
            }
        };
        
        document.addEventListener('keydown', handleKeyPress);
    }

    /**
     * Navigate to previous image in modal
     */
    previousImage() {
        if (!this.currentImageModal) return;
        
        const currentIndex = this.currentImageModal.currentIndex();
        const newIndex = currentIndex > 0 ? currentIndex - 1 : this.currentImageModal.maxIndex;
        this.currentImageModal.setIndex(newIndex);
        this.currentImageModal.updateContent();
    }

    /**
     * Navigate to next image in modal
     */
    nextImage() {
        if (!this.currentImageModal) return;
        
        const currentIndex = this.currentImageModal.currentIndex();
        const newIndex = currentIndex < this.currentImageModal.maxIndex ? currentIndex + 1 : 0;
        this.currentImageModal.setIndex(newIndex);
        this.currentImageModal.updateContent();
    }

    /**
     * Setup auto-hiding scrollbars for elements
     */
    setupAutoHideScrollbars(container = document) {
        const scrollableElements = container.querySelectorAll('.auto-hide-scrollbar');
        
        scrollableElements.forEach(element => {
            let scrollTimeout;
            
            // Add scrolling class when scrolling starts
            element.addEventListener('scroll', () => {
                element.classList.add('scrolling');
                
                // Clear existing timeout
                clearTimeout(scrollTimeout);
                
                // Remove scrolling class after scrolling stops
                scrollTimeout = setTimeout(() => {
                    element.classList.remove('scrolling');
                }, 1000); // Hide after 1 second of no scrolling
            });
            
            // Show scrollbar on mouseenter
            element.addEventListener('mouseenter', () => {
                element.classList.add('scrolling');
            });
            
            // Hide scrollbar on mouseleave (with delay)
            element.addEventListener('mouseleave', () => {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(() => {
                    element.classList.remove('scrolling');
                }, 500); // Hide after 0.5 seconds
            });
        });
    }



    /**
     * Map bounds restrictions to prevent scrolling to extreme areas
     */
    setupMapBoundsRestrictions() {
        // Define safe bounds (prevent scrolling to Antarctica, extreme poles, etc.)
        const safeBounds = {
            north: 85, // Prevent going too close to North Pole
            south: -60, // Prevent going to Antarctica
            east: 180, // Full longitude range
            west: -180
        };

        // Add bounds change listener to enforce restrictions
        this.map.addListener('bounds_changed', () => {
            const bounds = this.map.getBounds();
            if (bounds) {
                const ne = bounds.getNorthEast();
                const sw = bounds.getSouthWest();
                
                let needsAdjustment = false;
                let newCenter = this.map.getCenter();
                
                // Check if map has moved outside safe bounds
                if (ne.lat() > safeBounds.north) {
                    newCenter = new google.maps.LatLng(
                        Math.min(ne.lat() - (ne.lat() - safeBounds.north), this.map.getCenter().lat()),
                        this.map.getCenter().lng()
                    );
                    needsAdjustment = true;
                }
                
                if (sw.lat() < safeBounds.south) {
                    newCenter = new google.maps.LatLng(
                        Math.max(sw.lat() + (safeBounds.south - sw.lat()), this.map.getCenter().lat()),
                        this.map.getCenter().lng()
                    );
                    needsAdjustment = true;
                }
                
                // Apply adjustment if needed
                if (needsAdjustment) {
                    this.map.setCenter(newCenter);
                    console.log('Map bounds restricted to safe area');
                }
            }
        });

        // Add drag listener to prevent dragging to restricted areas
        this.map.addListener('drag', () => {
            const center = this.map.getCenter();
            let newLat = center.lat();
            let newLng = center.lng();
            
            // Restrict latitude
            if (newLat > safeBounds.north) {
                newLat = safeBounds.north;
            } else if (newLat < safeBounds.south) {
                newLat = safeBounds.south;
            }
            
            // Restrict longitude (handle wrapping)
            if (newLng > safeBounds.east) {
                newLng = safeBounds.west;
            } else if (newLng < safeBounds.west) {
                newLng = safeBounds.east;
            }
            
            // Apply restrictions if needed
            if (newLat !== center.lat() || newLng !== center.lng()) {
                this.map.setCenter(new google.maps.LatLng(newLat, newLng));
            }
        });
    }

    /**
     * Initialize weather system and bounds restrictions
     */
    initializeWeatherAndBounds() {
        // Set up bounds restrictions
        this.setupMapBoundsRestrictions();
        
        // Initialize weather system
        this.initializeWeatherSystem();
    }

    /**
     * Initialize weather system for hiking locations
     */
    initializeWeatherSystem() {
        // Create weather control panel
        this.createWeatherControl();
        
        // Set up weather data fetching
        this.setupWeatherData();
        
        // Add weather layer to map
        this.addWeatherLayer();
        
        // Load weather for map center automatically
        this.loadInitialWeather();
        
        // Set up automatic refresh
        this.setupAutoRefresh();
    }

    /**
     * Create weather control panel
     */
    createWeatherControl() {
        const weatherControl = document.createElement('div');
        weatherControl.className = 'bg-white rounded-lg shadow-lg border border-gray-200 p-4';
        weatherControl.innerHTML = `
            <div class="text-center mb-4">
                <div class="flex items-center justify-center gap-2 text-sm font-medium text-gray-700 mb-2">
                    <span>🌤️</span>
                    <span>Weather</span>
                </div>
            </div>
            <div class="space-y-3">
                <div class="text-center">
                    <div id="current-weather" class="text-lg font-bold text-blue-600 mb-2">--</div>
                    <div id="weather-location" class="text-xs text-gray-500 mb-3">Click on map</div>
                </div>
                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div class="text-center">
                        <div class="text-gray-500 mb-1">Temp:</div>
                        <div id="weather-temp" class="font-medium">--</div>
                    </div>
                    <div class="text-center">
                        <div class="text-gray-500 mb-1">Humidity:</div>
                        <div id="weather-humidity" class="font-medium">--</div>
                    </div>
                    <div class="text-center">
                        <div class="text-gray-500 mb-1">Wind:</div>
                        <div id="weather-wind" class="font-medium">--</div>
                    </div>
                    <div class="text-center">
                        <div class="text-gray-500 mb-1">UV:</div>
                        <div id="weather-uv" class="font-medium">--</div>
                    </div>
                </div>
            </div>
        `;
        
        // Weather will load automatically when map is ready
        
        // Create container with same spacing as left panels
        const weatherContainer = document.createElement('div');
        weatherContainer.className = 'p-3';
        weatherContainer.appendChild(weatherControl);
        
        // Add to map controls - positioned on the right side below full-screen button
        this.map.controls[google.maps.ControlPosition.RIGHT_TOP].push(weatherContainer);
    }

    /**
     * Set up weather data fetching
     */
    setupWeatherData() {
        // Add click listener to map for weather data
        this.map.addListener('click', (event) => {
            this.getWeatherForLocation(event.latLng);
        });
    }

    /**
     * Get weather data for a specific location
     */
    async getWeatherForLocation(latLng) {
        try {
            // Store the last weather location for auto-refresh
            this.lastWeatherLocation = latLng;
            
            const response = await fetch(`/api/weather?lat=${latLng.lat()}&lng=${latLng.lng()}`);
            if (!response.ok) {
                throw new Error('Weather data unavailable');
            }
            
            const weatherData = await response.json();
            
            // Check if there's an error in the response
            if (weatherData.error) {
                throw new Error(weatherData.message || 'Weather data unavailable');
            }
            
            this.updateWeatherDisplay(weatherData, latLng);
            
        } catch (error) {
            console.error('Error fetching weather:', error);
            this.showWeatherError();
        }
    }

    /**
     * Update weather display
     */
    updateWeatherDisplay(weatherData, latLng) {
        const currentWeather = document.getElementById('current-weather');
        const weatherLocation = document.getElementById('weather-location');
        const weatherTemp = document.getElementById('weather-temp');
        const weatherHumidity = document.getElementById('weather-humidity');
        const weatherWind = document.getElementById('weather-wind');
        const weatherUV = document.getElementById('weather-uv');
        
        if (currentWeather) currentWeather.textContent = weatherData.condition || '--';
        if (weatherLocation) weatherLocation.textContent = `${latLng.lat().toFixed(2)}, ${latLng.lng().toFixed(2)}`;
        if (weatherTemp) weatherTemp.textContent = `${weatherData.temperature}°C`;
        if (weatherHumidity) weatherHumidity.textContent = `${weatherData.humidity}%`;
        if (weatherWind) weatherWind.textContent = `${weatherData.windSpeed} km/h`;
        if (weatherUV) weatherUV.textContent = weatherData.uvIndex || '--';
        
        // Add last updated timestamp
        const timestamp = new Date(weatherData.timestamp).toLocaleTimeString();
        console.log(`Weather updated at ${timestamp}`);
    }

    /**
     * Show weather error
     */
    showWeatherError() {
        const currentWeather = document.getElementById('current-weather');
        if (currentWeather) currentWeather.textContent = 'Error';
    }



    /**
     * Add weather layer to map
     */
    addWeatherLayer() {
        // This would integrate with a weather service API
        // For now, we'll just show the control panel
        console.log('Weather system initialized');
    }

    /**
     * Load initial weather for map center
     */
    loadInitialWeather() {
        // Wait a bit for the map to be fully loaded
        setTimeout(() => {
            const center = this.map.getCenter();
            if (center) {
                console.log('Loading initial weather for map center:', center.lat(), center.lng());
                this.getWeatherForLocation(center);
            }
        }, 1000);
    }

    /**
     * Set up automatic weather refresh
     */
    setupAutoRefresh() {
        this.autoRefreshInterval = null;
        
        // Start auto-refresh
        this.startAutoRefresh();
    }

    /**
     * Start automatic refresh
     */
    startAutoRefresh() {
        if (this.autoRefreshInterval) {
            clearInterval(this.autoRefreshInterval);
        }
        
        // Refresh every 2 minutes (120,000 ms)
        this.autoRefreshInterval = setInterval(() => {
            if (this.lastWeatherLocation) {
                this.getWeatherForLocation(this.lastWeatherLocation);
            }
        }, 120000); // 2 minutes
        
        console.log('Auto-refresh started (every 2 minutes)');
    }



    /**
     * Stop auto-refresh
     */
    stopAutoRefresh() {
        if (this.autoRefreshInterval) {
            clearInterval(this.autoRefreshInterval);
            this.autoRefreshInterval = null;
        }
    }

    /**
     * Cleanup weather system
     */
    cleanupWeatherSystem() {
        this.stopAutoRefresh();
        console.log('Weather system cleaned up');
    }


}

// Initialize map when script loads
let hikeThereMap;

// Export for global use
window.HikeThereMap = HikeThereMap;

// Add a simple test variable to verify loading
window.HikeThereMapLoaded = true;
console.log('HikeThereMap class exported successfully');
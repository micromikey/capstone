// Simplified Itinerary Map - Fast Loading Version
class ItineraryMap {
    constructor(options = {}) {
        this.map = null;
        this.markers = new Map();
        this.infoWindow = null;
        this.searchResultMarker = null;
        this.selectedTrailMarker = null;
        this.trails = [];
        this.stopoverAutocomplete = null;
        this.sideTripAutocomplete = null;
        
        this.config = {
            mapElementId: options.mapElementId || 'itinerary-map',
            defaultCenter: { lat: 12.8797, lng: 121.7740 },
            defaultZoom: 6,
            ...options.config
        };
        
        this.isInitialized = false;
        this.init();
    }

    async init() {
        try {
            console.log('Initializing Itinerary Map...');
            
            // Check if Google Maps is already available
            if (this.isGoogleMapsReady()) {
                await this.initializeMap();
                this.setupFeatures();
                return;
            }

            // Wait for Google Maps with timeout
            await this.waitForGoogleMaps(5000); // 5 second timeout
            await this.initializeMap();
            this.setupFeatures();
            
        } catch (error) {
            console.error('Map initialization failed:', error);
            this.showMapError(error.message);
        }
    }

    isGoogleMapsReady() {
        return typeof google !== 'undefined' && 
               google.maps && 
               google.maps.Map && 
               google.maps.places;
    }

    async waitForGoogleMaps(timeout = 5000) {
        return new Promise((resolve, reject) => {
            const startTime = Date.now();
            
            const checkInterval = setInterval(() => {
                if (this.isGoogleMapsReady()) {
                    clearInterval(checkInterval);
                    console.log('Google Maps ready');
                    resolve();
                } else if (Date.now() - startTime > timeout) {
                    clearInterval(checkInterval);
                    reject(new Error('Google Maps loading timeout'));
                }
            }, 100); // Check every 100ms instead of 500ms
        });
    }

    async initializeMap() {
        const mapElement = document.getElementById(this.config.mapElementId);
        if (!mapElement) {
            throw new Error('Map container not found');
        }

        // Simple map options - no complex styling initially
        const mapOptions = {
            center: this.config.defaultCenter,
            zoom: this.config.defaultZoom,
            mapTypeId: google.maps.MapTypeId.HYBRID,
            mapTypeControl: false, // Disable built-in controls
            streetViewControl: false,
            fullscreenControl: false,
            zoomControl: false
        };

        this.map = new google.maps.Map(mapElement, mapOptions);
        
        // Hide loading state
        this.hideLoadingState();
        
        console.log('Map created successfully');
        this.isInitialized = true;
    }

    setupFeatures() {
        // Setup in sequence to avoid overwhelming the browser
        setTimeout(() => this.setupMapControls(), 100);
        setTimeout(() => this.setupSearchAutocomplete(), 200);
        setTimeout(() => this.setupStopoverAndSideTripAutocomplete(), 300);
        setTimeout(() => this.loadTrails(), 400);
        setTimeout(() => this.setupMapEvents(), 500);
    }

    hideLoadingState() {
        const loadingState = document.getElementById('map-loading-state');
        if (loadingState) {
            loadingState.style.display = 'none';
        }
    }

    setupMapControls() {
        // Map type toggle
        const mapTypeToggle = document.getElementById('map-type-toggle');
        if (mapTypeToggle && this.map) {
            mapTypeToggle.addEventListener('click', () => {
                const currentType = this.map.getMapTypeId();
                const newType = currentType === google.maps.MapTypeId.HYBRID 
                    ? google.maps.MapTypeId.TERRAIN 
                    : google.maps.MapTypeId.HYBRID;
                
                this.map.setMapTypeId(newType);
                this.updateMapTypeIndicator(newType);
            });
        }

        // Zoom controls
        this.setupZoomControls();
        
        // Reset button
        const resetBtn = document.getElementById('map-reset');
        if (resetBtn && this.map) {
            resetBtn.addEventListener('click', () => {
                this.map.setCenter(this.config.defaultCenter);
                this.map.setZoom(this.config.defaultZoom);
                this.map.setMapTypeId(google.maps.MapTypeId.HYBRID);
                this.updateMapTypeIndicator(google.maps.MapTypeId.HYBRID);
            });
        }
    }

    setupZoomControls() {
        const zoomIn = document.getElementById('map-zoom-in');
        const zoomOut = document.getElementById('map-zoom-out');
        
        if (zoomIn && this.map) {
            zoomIn.addEventListener('click', () => {
                this.map.setZoom(this.map.getZoom() + 1);
            });
        }
        
        if (zoomOut && this.map) {
            zoomOut.addEventListener('click', () => {
                this.map.setZoom(this.map.getZoom() - 1);
            });
        }
    }

    updateMapTypeIndicator(mapType) {
        const indicator = document.getElementById('map-type-indicator');
        if (indicator) {
            indicator.textContent = mapType === google.maps.MapTypeId.HYBRID ? 'Satellite' : 'Terrain';
        }
    }

    setupSearchAutocomplete() {
        const searchInput = document.getElementById('itinerary-search-input');
        if (!searchInput || !this.map || !google.maps.places) {
            console.log('Search setup skipped - missing requirements');
            return;
        }

        try {
            const autocomplete = new google.maps.places.Autocomplete(searchInput, {
                componentRestrictions: { country: 'ph' },
                fields: ['name', 'formatted_address', 'geometry'],
                types: ['establishment', 'geocode']
            });

            autocomplete.addListener('place_changed', () => {
                const place = autocomplete.getPlace();
                if (place.geometry) {
                    this.handleSearchResult(place);
                }
            });

            console.log('Search autocomplete setup complete');
        } catch (error) {
            console.error('Search setup failed:', error);
        }
    }

    setupStopoverAndSideTripAutocomplete() {
        if (!google.maps.places) {
            console.log('Google Places not available for stopover/side trip autocomplete');
            return;
        }

        try {
            // Setup stopover autocomplete
            const stopoverInput = document.getElementById('add-stopover-input');
            if (stopoverInput) {
                this.stopoverAutocomplete = new google.maps.places.Autocomplete(stopoverInput, {
                    componentRestrictions: { country: 'ph' },
                    types: ['establishment', 'geocode'],
                    fields: ['formatted_address', 'name', 'place_id', 'geometry']
                });

                this.stopoverAutocomplete.addListener('place_changed', () => {
                    const place = this.stopoverAutocomplete.getPlace();
                    if (place.geometry) {
                        this.addStopover(place.formatted_address || place.name);
                        stopoverInput.value = '';
                    }
                });
            }

            // Setup side trip autocomplete
            const sideTripInput = document.getElementById('add-sidetrip-input');
            if (sideTripInput) {
                this.sideTripAutocomplete = new google.maps.places.Autocomplete(sideTripInput, {
                    componentRestrictions: { country: 'ph' },
                    types: ['establishment', 'geocode'],
                    fields: ['formatted_address', 'name', 'place_id', 'geometry']
                });

                this.sideTripAutocomplete.addListener('place_changed', () => {
                    const place = this.sideTripAutocomplete.getPlace();
                    if (place.geometry) {
                        this.addSideTrip(place.formatted_address || place.name);
                        sideTripInput.value = '';
                    }
                });
            }

            console.log('Stopover and side trip autocomplete setup complete');
        } catch (error) {
            console.error('Stopover/side trip autocomplete setup failed:', error);
        }
    }

    handleSearchResult(place) {
        const position = place.geometry.location;
        this.map.panTo(position);
        this.map.setZoom(14);
        
        this.addSearchMarker(place, position);
        this.updateMapStatus(`Found: ${place.name || place.formatted_address}`);
    }

    addSearchMarker(place, position) {
        // Remove previous search marker
        if (this.searchResultMarker) {
            this.searchResultMarker.setMap(null);
        }

        this.searchResultMarker = new google.maps.Marker({
            position: position,
            map: this.map,
            title: place.name || place.formatted_address,
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 10,
                fillColor: '#3B82F6',
                fillOpacity: 0.8,
                strokeColor: '#FFFFFF',
                strokeWeight: 2
            }
        });

        const infoWindow = new google.maps.InfoWindow({
            content: `
                <div class="p-2">
                    <h3 class="font-semibold text-sm">${place.name || 'Location'}</h3>
                    <p class="text-xs text-gray-600">${place.formatted_address}</p>
                    <div class="mt-2 space-y-1">
                        <button type="button" onclick="event.preventDefault(); event.stopPropagation(); window.itineraryMap.addStopover('${(place.name || place.formatted_address).replace(/'/g, "\\'")}')" 
                                class="w-full px-2 py-1 bg-emerald-600 text-white text-xs rounded hover:bg-emerald-700">
                            + Add to Stopovers
                        </button>
                        <button type="button" onclick="event.preventDefault(); event.stopPropagation(); window.itineraryMap.addSideTrip('${(place.name || place.formatted_address).replace(/'/g, "\\'")}')" 
                                class="w-full px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                            + Add to Side Trips
                        </button>
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

    async loadTrails() {
        if (!this.map) return;
        
        try {
            console.log('Loading trails...');
            const response = await fetch('/map/trails');
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            
            this.trails = await response.json();
            console.log(`Loaded ${this.trails.length} trails`);
            
            // Add trails to map with delay to prevent browser freeze
            this.displayTrailsAsync();
            
        } catch (error) {
            console.error('Trail loading failed:', error);
            this.updateMapStatus('Failed to load trails');
        }
    }

    displayTrailsAsync() {
        if (!this.trails.length) return;
        
        let index = 0;
        const batchSize = 10; // Process 10 trails at a time
        
        const processBatch = () => {
            const endIndex = Math.min(index + batchSize, this.trails.length);
            
            for (let i = index; i < endIndex; i++) {
                this.addTrailMarker(this.trails[i]);
            }
            
            index = endIndex;
            
            if (index < this.trails.length) {
                setTimeout(processBatch, 50); // Small delay between batches
            } else {
                this.updateTrailCount();
                this.updateMapStatus('Ready to plan your hike');
            }
        };
        
        processBatch();
    }

    addTrailMarker(trail) {
        // Check if trail has coordinates (using the same structure as main map)
        if (!trail.coordinates || !trail.coordinates.lat || !trail.coordinates.lng) {
            // Fallback to old structure if needed
            if (!trail.latitude || !trail.longitude) return;
            
            // Convert old structure to new structure
            trail.coordinates = {
                lat: parseFloat(trail.latitude),
                lng: parseFloat(trail.longitude)
            };
        }

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
            this.zoomToTrail(trail, marker);
            this.showTrailInfo(trail, marker);
        });

        this.markers.set(trail.id, marker);
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
        if (this.infoWindow) {
            this.infoWindow.close();
        }

        this.infoWindow = new google.maps.InfoWindow({
            content: this.createTrailInfoContent(trail),
            maxWidth: 400,
            pixelOffset: new google.maps.Size(0, -10)
        });

        this.infoWindow.open(this.map, marker);
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
                            <span class="truncate">${trail.location_name || trail.location || 'Location not specified'}</span>
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
                        <button type="button" onclick="event.preventDefault(); event.stopPropagation(); window.itineraryMap.selectTrail(${trail.id})" 
                                class="w-full inline-flex items-center justify-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-1 transition-colors shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3l6-3"></path>
                            </svg>
                            Select for Itinerary
                        </button>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" onclick="event.preventDefault(); event.stopPropagation(); window.itineraryMap.getDirections('${trail.coordinates.lat},${trail.coordinates.lng}')" 
                                    class="inline-flex items-center justify-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition-colors shadow-sm">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3l6-3"></path>
                                </svg>
                                Directions
                            </button>
                            <button type="button" onclick="event.preventDefault(); event.stopPropagation(); window.itineraryMap.showTrailDetails(${trail.id})" 
                                    class="inline-flex items-center justify-center px-3 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-1 transition-colors shadow-sm">
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

    selectTrail(trailId) {
        const trail = this.trails.find(t => t.id == trailId);
        if (!trail) return;

        // Update form fields
        this.updateTrailForm(trail);
        
        // Highlight selected trail
        this.highlightTrail(trail);
        
        // Close info window
        if (this.infoWindow) {
            this.infoWindow.close();
        }
        
        this.updateMapStatus(`Selected: ${trail.name}`);
    }

    updateTrailForm(trail) {
        const displayElement = document.getElementById('selectedTrailDisplay');
        const inputElement = document.getElementById('trailNameInput');
        const destinationElement = document.getElementById('destinationDisplay');
        
        if (displayElement) displayElement.textContent = trail.name;
        if (inputElement) inputElement.value = trail.name;
        if (destinationElement) destinationElement.textContent = trail.name;
        
        // Also update the dropdown
        const selectElement = document.getElementById('trailSelect');
        if (selectElement) {
            selectElement.value = trail.name;
        }
    }

    highlightTrail(trail) {
        // Remove previous highlight
        if (this.selectedTrailMarker) {
            this.selectedTrailMarker.setMap(null);
        }

        // Ensure trail has coordinates
        if (!trail.coordinates) {
            if (trail.latitude && trail.longitude) {
                trail.coordinates = {
                    lat: parseFloat(trail.latitude),
                    lng: parseFloat(trail.longitude)
                };
            } else {
                console.error('Trail has no coordinates');
                return;
            }
        }

        // Add highlight marker
        this.selectedTrailMarker = new google.maps.Marker({
            position: { 
                lat: parseFloat(trail.coordinates.lat), 
                lng: parseFloat(trail.coordinates.lng) 
            },
            map: this.map,
            title: `Selected: ${trail.name}`,
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 12,
                fillColor: '#10B981',
                fillOpacity: 0.9,
                strokeColor: '#FFFFFF',
                strokeWeight: 3
            },
            animation: google.maps.Animation.BOUNCE
        });

        // Stop bouncing after 2 seconds
        setTimeout(() => {
            if (this.selectedTrailMarker) {
                this.selectedTrailMarker.setAnimation(null);
            }
        }, 2000);

        // Center map on selected trail
        this.map.panTo({
            lat: parseFloat(trail.coordinates.lat), 
            lng: parseFloat(trail.coordinates.lng)
        });
        this.map.setZoom(12);
    }

    updateTrailCount() {
        const countElement = document.getElementById('trail-count');
        if (countElement) {
            countElement.textContent = `${this.trails.length} trails`;
        }
    }

    updateMapStatus(status) {
        const statusElement = document.getElementById('map-status');
        if (statusElement) {
            statusElement.textContent = status;
        }
    }

    setupMapEvents() {
        if (!this.map) return;
        
        this.map.addListener('click', () => {
            if (this.infoWindow) {
                this.infoWindow.close();
            }
        });
    }

    // Stopover and Side Trip Management
    addStopover(location) {
        const container = document.getElementById('stopovers-container');
        if (!container) return;
        
        const stopoverDiv = document.createElement('div');
        stopoverDiv.className = 'flex items-center gap-2 p-2 bg-white rounded-lg border border-gray-200 shadow-sm';
        stopoverDiv.innerHTML = `
            <div class="flex-shrink-0 w-2 h-2 bg-emerald-500 rounded-full"></div>
            <span class="flex-grow text-sm text-gray-800">${location}</span>
            <input type="hidden" name="stopovers[]" value="${location}">
            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 text-sm font-bold">
                ×
            </button>
        `;
        
        container.appendChild(stopoverDiv);
        this.updateMapStatus(`Added "${location}" to stopovers`);
    }

    addSideTrip(location) {
        const container = document.getElementById('sidetrips-container');
        if (!container) return;
        
        const sideTripDiv = document.createElement('div');
        sideTripDiv.className = 'flex items-center gap-2 p-2 bg-white rounded-lg border border-gray-200 shadow-sm';
        sideTripDiv.innerHTML = `
            <div class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full"></div>
            <span class="flex-grow text-sm text-gray-800">${location}</span>
            <input type="hidden" name="sidetrips[]" value="${location}">
            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 text-sm font-bold">
                ×
            </button>
        `;
        
        container.appendChild(sideTripDiv);
        this.updateMapStatus(`Added "${location}" to side trips`);
    }

    showMapError(message) {
        console.error('Map error:', message);
        
        const loadingState = document.getElementById('map-loading-state');
        const fallback = document.getElementById('map-fallback');
        
        if (loadingState) loadingState.style.display = 'none';
        
        if (fallback) {
            fallback.classList.remove('hidden');
            fallback.innerHTML = `
                <div class="text-center p-4">
                    <div class="text-red-500 text-xl mb-2">⚠️</div>
                    <p class="text-sm text-gray-600 font-medium mb-2">Map Loading Failed</p>
                    <p class="text-xs text-gray-500 mb-3">${message}</p>
                    <button onclick="location.reload()" 
                            class="px-3 py-1.5 bg-emerald-600 text-white text-xs rounded hover:bg-emerald-700">
                        Refresh Page
                    </button>
                </div>
            `;
        }
        
        this.updateMapStatus('Map failed to load');
    }

    // Public methods
    getMap() { return this.map; }
    getTrails() { return this.trails; }

    // Additional methods for trail info window
    getDirections(destination) {
        if (!this.currentLocation) {
            this.showNotification('Please enable location services to get directions.', 'info');
            return;
        }
        
        const destination_coords = destination.split(',');
        const dest = new google.maps.LatLng(parseFloat(destination_coords[0]), parseFloat(destination_coords[1]));
        
        // For now, just show a notification - you can implement full directions later
        this.showNotification('Directions feature coming soon!', 'info');
    }

    showTrailDetails(trailId) {
        // Navigate to trail details page
        window.open(`/trails/${trailId}`, '_blank');
    }

    showNotification(message, type = 'info') {
        // Create a simple notification
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg border-l-4 ${
            type === 'error' ? 'bg-red-100 border-red-500 text-red-700' :
            type === 'success' ? 'bg-green-100 border-green-500 text-green-700' :
            'bg-blue-100 border-blue-500 text-blue-700'
        }`;
        
        notification.innerHTML = `
            <div class="flex items-center">
                <span class="mr-2">${type === 'error' ? '❌' : type === 'success' ? '✅' : 'ℹ️'}</span>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 3000);
    }
}

// Global functions for the blade template
window.addLocationToStopovers = function(location, lat, lng) {
    if (window.itineraryMap) {
        window.itineraryMap.addStopover(location);
    }
};

window.addLocationToSideTrips = function(location, lat, lng) {
    if (window.itineraryMap) {
        window.itineraryMap.addSideTrip(location);
    }
};

// Export for global use
window.ItineraryMap = ItineraryMap;

// Add a simple test variable to verify loading
window.ItineraryMapLoaded = true;
console.log('ItineraryMap class exported successfully');
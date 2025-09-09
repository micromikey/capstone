/**
 * Advanced Trail Map - Interactive Google Maps for HikeThere
 * Features: Directions, Dynamic Trail Visualization, Real-time Updates, GPX Integration
 * Author: HikeThere Development Team
 */

// Make sure the class is available globally
window.AdvancedTrailMap = class AdvancedTrailMap {
    constructor(options = {}) {
        // Core properties
        this.map = null;
        this.directionsService = null;
        this.directionsRenderer = null;
        this.elevationService = null;
        this.placesService = null;
        this.geocoder = null;
        this.streetViewService = null;
        this.streetViewPanorama = null;
        
        // Trail data
        this.trails = new Map();
        this.currentTrail = null;
        this.trailPaths = new Map();
        this.trailMarkers = new Map();
        
        // User location
        this.userLocation = null;
        this.userMarker = null;
        this.watchId = null;
        
        // UI State
        this.mapElementId = options.mapElementId || 'advanced-trail-map';
        this.isFullscreen = false;
        this.isSidebarVisible = true;
        this.activeOverlays = {
            weather: false,
            elevation: false,
            facilities: false
        };
        
        // Weather data
        this.weatherData = null;
        this.weatherMarkers = new Map();
        
        // Configuration
        this.config = {
            defaultCenter: { lat: 12.8797, lng: 121.7740 }, // Philippines
            defaultZoom: 8,
            maxZoom: 20,
            minZoom: 4,
            animationDuration: 300,
            ...options
        };
        
        // Initial trail data (if provided)
        this.initialTrail = options.initialTrail || null;
        
        this.init();
    }

    async init() {
        try {
            this.showLoadingStatus('Initializing map system...');
            
            // Initialize map
            await this.initializeMap();
            
            // Initialize services
            this.initializeServices();
            
            // Setup event listeners
            this.setupEventListeners();
            
            // Load trail data
            await this.loadTrailData();
            
            // Initialize initial trail if provided
            if (this.initialTrail) {
                await this.selectTrail(this.initialTrail);
            }
            
            // Get user location
            this.getUserLocation();
            
            this.showLoadingStatus('Map ready!');
            this.hideLoading();
            this.updateStatus('Ready', 'green');
            
            console.log('Advanced Trail Map initialized successfully');
        } catch (error) {
            console.error('Failed to initialize Advanced Trail Map:', error);
            this.showError('Failed to initialize map: ' + error.message);
        }
    }

    async initializeMap() {
        const mapOptions = {
            center: this.config.defaultCenter,
            zoom: this.config.defaultZoom,
            mapTypeId: google.maps.MapTypeId.TERRAIN,
            styles: this.getMapStyles(),
            
            // Controls
            zoomControl: true,
            zoomControlOptions: {
                position: google.maps.ControlPosition.RIGHT_BOTTOM
            },
            mapTypeControl: true,
            mapTypeControlOptions: {
                style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
                position: google.maps.ControlPosition.TOP_RIGHT
            },
            streetViewControl: false, // We handle this ourselves
            fullscreenControl: false, // We handle this ourselves
            
            // Restrictions
            restriction: {
                latLngBounds: {
                    north: 21.12,
                    south: 4.65,
                    west: 116.93,
                    east: 126.60,
                },
                strictBounds: false,
            },
            
            // Gestures
            gestureHandling: 'greedy',
            clickableIcons: false
        };

        const mapElement = document.getElementById(this.mapElementId);
        if (!mapElement) {
            throw new Error(`Map element with ID '${this.mapElementId}' not found`);
        }

        this.map = new google.maps.Map(mapElement, mapOptions);

        // Add click listener for trail selection
        this.map.addListener('click', (event) => {
            this.handleMapClick(event);
        });

        // Add zoom change listener
        this.map.addListener('zoom_changed', () => {
            this.updateZoomLevel();
        });

        // Add bounds change listener for coordinates display
        this.map.addListener('center_changed', () => {
            this.updateCoordinatesDisplay();
        });
    }

    initializeServices() {
        this.directionsService = new google.maps.DirectionsService();
        this.directionsRenderer = new google.maps.DirectionsRenderer({
            suppressMarkers: false,
            draggable: true,
            panel: document.getElementById('directions-content')
        });
        
        this.elevationService = new google.maps.ElevationService();
        this.geocoder = new google.maps.Geocoder();
        this.streetViewService = new google.maps.StreetViewService();
        
        // Initialize places service after map is ready
        this.placesService = new google.maps.places.PlacesService(this.map);
    }

    setupEventListeners() {
        // Trail search
        const searchInput = document.getElementById('trail-search');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.handleTrailSearch(e.target.value);
            });
            
            // Clear search button
            const clearButton = document.getElementById('clear-search');
            if (clearButton) {
                clearButton.addEventListener('click', () => {
                    searchInput.value = '';
                    this.hideSearchResults();
                    clearButton.classList.add('hidden');
                });
            }
            
            // Hide results when clicking outside
            document.addEventListener('click', (e) => {
                if (!searchInput.contains(e.target)) {
                    this.hideSearchResults();
                }
            });
        }

        // Navigation buttons
        this.setupButton('get-directions-btn', () => this.getDirectionsToTrail());
        this.setupButton('show-trail-street-view', () => this.showTrailStreetView());
        this.setupButton('current-location-btn', () => this.centerOnUserLocation());
        this.setupButton('close-directions', () => this.hideDirectionsPanel());

        // Map control buttons
        this.setupButton('toggle-sidebar', () => this.toggleSidebar());
        this.setupButton('toggle-fullscreen', () => this.toggleFullscreen());
        this.setupButton('share-trail-btn', () => this.shareTrail());
        this.setupButton('save-trail-btn', () => this.saveTrail());
        this.setupButton('nearby-btn', () => this.showNearbyTrails());
        this.setupButton('send-to-phone-btn', () => this.sendToPhone());

        // Street View controls
        this.setupButton('close-street-view', () => this.hideStreetView());
        this.setupButton('share-street-view', () => this.shareStreetView());

        // Trail action buttons
        this.setupButton('suggest-edit-btn', () => this.suggestEdit());
    }

    setupButton(id, handler) {
        const button = document.getElementById(id);
        if (button && handler) {
            button.addEventListener('click', handler);
        }
    }

    async loadTrailData() {
        this.showLoadingStatus('Loading trail data...');
        
        try {
            const response = await fetch('/api/trails/map-data', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            
            if (data.trails) {
                data.trails.forEach(trail => {
                    this.trails.set(trail.id, trail);
                    this.addTrailToMap(trail);
                });
                
                console.log(`Loaded ${data.trails.length} trails`);
            }
        } catch (error) {
            console.error('Error loading trail data:', error);
            this.showNotification('Failed to load trail data', 'error');
        }
    }

    addTrailToMap(trail) {
        // Create trail marker
        if (trail.coordinates && trail.coordinates.length >= 2) {
            const position = {
                lat: parseFloat(trail.coordinates[0]),
                lng: parseFloat(trail.coordinates[1])
            };

            const marker = new google.maps.Marker({
                position: position,
                map: this.map,
                title: trail.trail_name,
                icon: this.getTrailMarkerIcon(trail.difficulty),
                animation: google.maps.Animation.DROP
            });

            marker.addListener('click', () => {
                this.selectTrailFromMarker(trail);
            });

            this.trailMarkers.set(trail.id, marker);

            // Add trail path if GPX data exists
            if (trail.gpx_file || (trail.coordinates && trail.coordinates.length > 2)) {
                this.createTrailPath(trail);
            }
        }
    }

    createTrailPath(trail) {
        // This would ideally parse GPX data, but for now use coordinates
        if (trail.coordinates && trail.coordinates.length >= 4) {
            const path = [];
            for (let i = 0; i < trail.coordinates.length; i += 2) {
                if (trail.coordinates[i + 1]) {
                    path.push({
                        lat: parseFloat(trail.coordinates[i]),
                        lng: parseFloat(trail.coordinates[i + 1])
                    });
                }
            }

            if (path.length > 1) {
                const trailPath = new google.maps.Polyline({
                    path: path,
                    geodesic: true,
                    strokeColor: this.getTrailPathColor(trail.difficulty),
                    strokeOpacity: 0.8,
                    strokeWeight: 4,
                    map: null // Hidden by default
                });

                trailPath.addListener('click', () => {
                    this.selectTrailFromPath(trail);
                });

                this.trailPaths.set(trail.id, trailPath);
            }
        }
    }

    async selectTrail(trail) {
        this.currentTrail = trail;
        this.updateTrailDisplay(trail);
        this.enableTrailButtons();
        
        // Center map on trail
        if (trail.coordinates && trail.coordinates.length >= 2) {
            const position = {
                lat: parseFloat(trail.coordinates[0]),
                lng: parseFloat(trail.coordinates[1])
            };
            
            this.map.setCenter(position);
            this.map.setZoom(14);
        }

        // Load additional trail data if needed
        await this.loadTrailDetails(trail.id);
    }

    async selectTrailFromMarker(trail) {
        await this.selectTrail(trail);
        this.showTrailInfoWindow(trail);
    }

    async selectTrailFromPath(trail) {
        await this.selectTrail(trail);
    }

    updateTrailDisplay(trail) {
        // Hide prompt and show details
        const prompt = document.getElementById('trail-selection-prompt');
        const display = document.getElementById('trail-details-display');
        
        if (prompt) prompt.classList.add('hidden');
        if (display) display.classList.remove('hidden');

        // Update trail information
        this.updateElement('current-trail-name', trail.trail_name);
        this.updateElement('current-trail-address', trail.location?.name || 'Unknown Location');
        
        // Update rating
        const ratingElement = document.getElementById('current-trail-rating');
        const starsElement = document.getElementById('current-trail-stars');
        const reviewCountElement = document.getElementById('current-trail-review-count');
        
        if (ratingElement && trail.average_rating) {
            const rating = parseFloat(trail.average_rating).toFixed(1);
            ratingElement.textContent = rating;
            
            // Update stars
            if (starsElement) {
                const fullStars = Math.floor(rating);
                const hasHalfStar = rating % 1 >= 0.5;
                let stars = '★'.repeat(fullStars);
                if (hasHalfStar) stars += '☆';
                stars += '☆'.repeat(5 - fullStars - (hasHalfStar ? 1 : 0));
                starsElement.textContent = stars;
            }
            
            if (reviewCountElement) {
                reviewCountElement.textContent = `(${trail.total_reviews || 0})`;
            }
        } else {
            if (ratingElement) ratingElement.textContent = '-';
            if (starsElement) starsElement.textContent = '☆☆☆☆☆';
            if (reviewCountElement) reviewCountElement.textContent = '(0)';
        }

        // Update trail image if available
        if (trail.primary_image) {
            const imageContainer = document.getElementById('trail-image-container');
            const imageElement = document.getElementById('trail-image');
            if (imageContainer && imageElement) {
                imageElement.src = trail.primary_image;
                imageElement.alt = trail.trail_name;
                imageContainer.classList.remove('hidden');
            }
        }
    }

    enableTrailButtons() {
        const buttons = ['get-directions-btn', 'show-trail-street-view'];
        buttons.forEach(buttonId => {
            const button = document.getElementById(buttonId);
            if (button) {
                button.disabled = false;
                button.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        });
    }

    async getDirectionsToTrail() {
        if (!this.currentTrail) {
            this.showNotification('Please select a trail first', 'warning');
            return;
        }

        this.showLoadingStatus('Calculating directions...');
        
        try {
            // Get user location if not available
            if (!this.userLocation) {
                await this.getUserLocation();
                if (!this.userLocation) {
                    this.showNotification('Could not get your location. Please enable location access.', 'error');
                    this.hideLoading();
                    return;
                }
            }

            const destination = {
                lat: parseFloat(this.currentTrail.coordinates[0]),
                lng: parseFloat(this.currentTrail.coordinates[1])
            };

            // Create directions request
            const request = {
                origin: this.userLocation,
                destination: destination,
                travelMode: google.maps.TravelMode.DRIVING,
                avoidHighways: false,
                avoidTolls: false,
                provideRouteAlternatives: true
            };

            // Use Promise-based approach for better error handling
            const result = await new Promise((resolve, reject) => {
                this.directionsService.route(request, (result, status) => {
                    if (status === 'OK') {
                        resolve(result);
                    } else {
                        reject(new Error(`Directions request failed: ${status}`));
                    }
                });
            });

            // Set directions on map
            this.directionsRenderer.setMap(this.map);
            this.directionsRenderer.setDirections(result);
            
            // Show directions panel
            this.showDirectionsPanel();
            this.updateDirectionsPanel(result);
            
            // Fit map to show entire route
            const bounds = new google.maps.LatLngBounds();
            bounds.extend(this.userLocation);
            bounds.extend(destination);
            this.map.fitBounds(bounds);
            
            this.showNotification('Directions loaded successfully', 'success');
            
        } catch (error) {
            console.error('Error getting directions:', error);
            this.showNotification(`Error calculating directions: ${error.message}`, 'error');
        } finally {
            this.hideLoading();
        }
    }

    showTrailStreetView() {
        if (!this.currentTrail) {
            this.showNotification('Please select a trail first', 'warning');
            return;
        }

        const trailLocation = {
            lat: parseFloat(this.currentTrail.coordinates[0]),
            lng: parseFloat(this.currentTrail.coordinates[1])
        };

        this.showStreetViewAtLocation(trailLocation, this.currentTrail.trail_name);
    }

    showStreetViewAtLocation(location, title = 'Street View') {
        this.showLoadingStatus('Loading Street View...');

        // Create or get the Street View container
        let streetViewContainer = document.getElementById('street-view-panorama');
        if (!streetViewContainer) {
            // Create the container if it doesn't exist
            streetViewContainer = document.createElement('div');
            streetViewContainer.id = 'street-view-panorama';
            streetViewContainer.style.height = '400px';
            streetViewContainer.style.width = '100%';
            streetViewContainer.style.borderRadius = '8px';
            
            // Find the street view container and append the panorama
            const container = document.getElementById('street-view-container');
            if (container) {
                container.appendChild(streetViewContainer);
            }
        }

        // Check if Street View is available at this location
        this.streetViewService.getPanorama({
            location: location,
            radius: 50
        }, (data, status) => {
            if (status === 'OK') {
                // Create or update Street View panorama
                if (!this.streetViewPanorama) {
                    this.streetViewPanorama = new google.maps.StreetViewPanorama(
                        streetViewContainer,
                        {
                            position: location,
                            pov: {
                                heading: 34,
                                pitch: 10
                            },
                            zoom: 1,
                            addressControl: false,
                            fullscreenControl: false,
                            motionTracking: false,
                            motionTrackingControl: false,
                            panControl: true,
                            zoomControl: true
                        }
                    );
                } else {
                    this.streetViewPanorama.setPosition(location);
                }

                // Update Street View info
                this.updateElement('street-view-location', title);
                this.updateElement('street-view-date', 'Apr 2015'); // Mock date
                
                this.showStreetViewPanel();
                this.showNotification('Street View loaded successfully', 'success');
            } else {
                console.error('Street View not available:', status);
                this.showNotification('Street View not available at this location', 'warning');
                
                // Show a placeholder or alternative content
                streetViewContainer.innerHTML = `
                    <div class="flex items-center justify-center h-full bg-gray-100 rounded-lg">
                        <div class="text-center text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-sm font-medium">Street View not available</p>
                            <p class="text-xs text-gray-400 mt-1">This location doesn't have Street View coverage</p>
                        </div>
                    </div>
                `;
            }
            this.hideLoading();
        });
    }

    showStreetViewPanel() {
        const container = document.getElementById('street-view-container');
        if (container) {
            container.classList.remove('hidden');
        }
    }

    hideStreetView() {
        const container = document.getElementById('street-view-container');
        if (container) {
            container.classList.add('hidden');
        }
    }

    showDirectionsPanel() {
        const panel = document.getElementById('directions-panel');
        if (panel) {
            panel.style.transform = 'translateY(0)';
        }
    }

    hideDirectionsPanel() {
        const panel = document.getElementById('directions-panel');
        if (panel) {
            panel.style.transform = 'translateY(100%)';
        }
    }

    toggleSidebar() {
        const sidebar = document.getElementById('trail-sidebar');
        if (sidebar) {
            this.isSidebarVisible = !this.isSidebarVisible;
            if (this.isSidebarVisible) {
                sidebar.style.transform = 'translateX(0)';
            } else {
                sidebar.style.transform = 'translateX(-100%)';
            }
        }
    }

    centerOnUserLocation() {
        if (this.userLocation) {
            this.map.panTo(this.userLocation);
            this.map.setZoom(16);
        } else {
            this.getUserLocation();
        }
    }

    async getUserLocation() {
        return new Promise((resolve, reject) => {
            if (navigator.geolocation) {
                this.showLoadingStatus('Getting your location...');
                
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        this.userLocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        
                        this.updateUserMarker();
                        this.hideLoading();
                        this.showNotification('Location found', 'success');
                        resolve(this.userLocation);
                    },
                    (error) => {
                        console.error('Geolocation error:', error);
                        let errorMessage = 'Could not get your location';
                        switch (error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage = 'Location access denied. Please enable location services.';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage = 'Location information unavailable.';
                                break;
                            case error.TIMEOUT:
                                errorMessage = 'Location request timed out.';
                                break;
                        }
                        this.showNotification(errorMessage, 'error');
                        this.hideLoading();
                        reject(error);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 15000,
                        maximumAge: 600000 // 10 minutes
                    }
                );
            } else {
                const error = new Error('Geolocation not supported');
                this.showNotification('Geolocation not supported by your browser', 'error');
                reject(error);
            }
        });
    }

    updateUserMarker() {
        if (this.userMarker) {
            this.userMarker.setMap(null);
        }

        if (this.userLocation) {
            this.userMarker = new google.maps.Marker({
                position: this.userLocation,
                map: this.map,
                title: 'Your Location',
                icon: {
                    url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="10" cy="10" r="8" fill="#4285F4" stroke="white" stroke-width="2"/>
                            <circle cx="10" cy="10" r="3" fill="white"/>
                        </svg>
                    `),
                    scaledSize: new google.maps.Size(20, 20)
                },
                zIndex: 1000
            });
        }
    }

    async handleTrailSearch(query) {
        if (!query || query.length < 2) {
            this.hideSearchResults();
            return;
        }

        const results = Array.from(this.trails.values())
            .filter(trail => 
                trail.trail_name.toLowerCase().includes(query.toLowerCase()) ||
                trail.mountain_name?.toLowerCase().includes(query.toLowerCase()) ||
                trail.location?.name?.toLowerCase().includes(query.toLowerCase())
            )
            .slice(0, 5); // Limit to 5 results

        this.showSearchResults(results);
    }

    showSearchResults(results) {
        const resultsContainer = document.getElementById('trail-search-results');
        const clearButton = document.getElementById('clear-search');
        
        if (!resultsContainer) return;

        if (results.length === 0) {
            resultsContainer.innerHTML = '<div class="p-3 text-sm text-gray-500">No trails found</div>';
        } else {
            resultsContainer.innerHTML = results.map(trail => `
                <div class="search-result-item p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0" data-trail-id="${trail.id}">
                    <div class="font-medium text-gray-900">${trail.trail_name}</div>
                    <div class="text-sm text-gray-600">${trail.location?.name || 'Unknown Location'}</div>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="px-2 py-1 text-xs rounded-full ${this.getDifficultyClasses(trail.difficulty)}">${trail.difficulty || 'Unknown'}</span>
                        ${trail.length ? `<span class="text-xs text-gray-500">${trail.length} km</span>` : ''}
                    </div>
                </div>
            `).join('');

            // Add click handlers
            resultsContainer.querySelectorAll('.search-result-item').forEach(item => {
                item.addEventListener('click', async () => {
                    const trailId = parseInt(item.dataset.trailId);
                    const trail = this.trails.get(trailId);
                    if (trail) {
                        await this.selectTrail(trail);
                        this.hideSearchResults();
                        document.getElementById('trail-search').value = trail.trail_name;
                        document.getElementById('clear-search').classList.remove('hidden');
                    }
                });
            });
        }

        resultsContainer.classList.remove('hidden');
    }

    hideSearchResults() {
        const resultsContainer = document.getElementById('trail-search-results');
        if (resultsContainer) {
            resultsContainer.classList.add('hidden');
        }
    }

    // Utility methods
    getMapStyles() {
        return [
            {
                featureType: 'poi',
                elementType: 'labels',
                stylers: [{ visibility: 'off' }]
            },
            {
                featureType: 'transit',
                elementType: 'labels',
                stylers: [{ visibility: 'off' }]
            }
        ];
    }

    getTrailMarkerIcon(difficulty) {
        const colors = {
            beginner: '#10b981',    // green
            intermediate: '#f59e0b', // yellow
            advanced: '#ef4444'      // red
        };

        const color = colors[difficulty] || '#6b7280';

        return {
            url: `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(`
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L13.09 8.26L22 9L13.09 15.74L12 22L10.91 15.74L2 9L10.91 8.26L12 2Z" fill="${color}" stroke="white" stroke-width="2"/>
                </svg>
            `)}`,
            scaledSize: new google.maps.Size(24, 24),
            anchor: new google.maps.Point(12, 12)
        };
    }

    getTrailPathColor(difficulty) {
        return {
            beginner: '#10b981',
            intermediate: '#f59e0b',
            advanced: '#ef4444'
        }[difficulty] || '#6b7280';
    }

    getDifficultyClasses(difficulty) {
        return {
            beginner: 'bg-green-100 text-green-800',
            intermediate: 'bg-yellow-100 text-yellow-800',
            advanced: 'bg-red-100 text-red-800'
        }[difficulty] || 'bg-gray-100 text-gray-800';
    }

    // UI Helper Methods
    updateElement(id, content) {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = content;
        }
    }

    updateStatus(status, color = 'gray') {
        const statusElement = document.getElementById('map-status');
        if (statusElement) {
            statusElement.textContent = status;
            statusElement.className = `font-medium text-${color}-600`;
        }
    }

    updateZoomLevel() {
        const zoomElement = document.getElementById('current-zoom-level');
        if (zoomElement && this.map) {
            zoomElement.textContent = this.map.getZoom();
        }
    }

    updateCoordinatesDisplay() {
        const coordsElement = document.getElementById('current-coordinates');
        if (coordsElement && this.map) {
            const center = this.map.getCenter();
            coordsElement.textContent = `${center.lat().toFixed(4)}, ${center.lng().toFixed(4)}`;
        }
    }

    showLoading() {
        const loading = document.getElementById('advanced-trail-loading');
        if (loading) {
            loading.classList.remove('hidden');
        }
    }

    hideLoading() {
        const loading = document.getElementById('advanced-trail-loading');
        if (loading) {
            loading.classList.add('hidden');
        }
    }

    showLoadingStatus(status) {
        const statusElement = document.getElementById('loading-status');
        if (statusElement) {
            statusElement.textContent = status;
        }
        this.showLoading();
    }

    showNotification(message, type = 'info') {
        const container = document.getElementById('advanced-trail-notifications');
        if (!container) return;

        const notification = document.createElement('div');
        notification.className = `advanced-trail-notification advanced-trail-notification-${type}`;
        notification.innerHTML = `
            <div class="flex items-center gap-3 p-4 rounded-lg shadow-lg">
                <div class="flex-shrink-0">
                    ${this.getNotificationIcon(type)}
                </div>
                <div class="flex-1 text-sm font-medium">${message}</div>
                <button class="flex-shrink-0 text-current opacity-70 hover:opacity-100" onclick="this.closest('.advanced-trail-notification').remove()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        `;

        container.appendChild(notification);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    getNotificationIcon(type) {
        const icons = {
            success: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>',
            error: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>',
            warning: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>',
            info: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>'
        };
        return icons[type] || icons.info;
    }

    showError(message) {
        this.showNotification(message, 'error');
        this.updateStatus('Error', 'red');
    }

    // Event handlers for map interactions
    handleMapClick(event) {
        console.log('Map clicked at:', event.latLng.lat(), event.latLng.lng());
    }

    // Additional methods for future features
    toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
        } else {
            document.exitFullscreen();
        }
    }

    shareTrail() {
        if (this.currentTrail) {
            const url = `${window.location.origin}/advanced-trail-map/${this.currentTrail.slug || this.currentTrail.id}`;
            if (navigator.share) {
                navigator.share({
                    title: this.currentTrail.trail_name,
                    text: `Check out this trail: ${this.currentTrail.trail_name}`,
                    url: url
                });
            } else {
                navigator.clipboard.writeText(url);
                this.showNotification('Trail URL copied to clipboard', 'success');
            }
        }
    }

    saveTrail() {
        if (this.currentTrail) {
            // Implement save functionality
            this.showNotification('Trail saved to your list', 'success');
        }
    }

    showNearbyTrails() {
        if (this.currentTrail) {
            // Implement nearby trails functionality
            this.showNotification('Showing nearby trails', 'info');
        }
    }

    sendToPhone() {
        if (this.currentTrail) {
            // Implement send to phone functionality
            this.showNotification('Trail sent to your phone', 'success');
        }
    }

    shareStreetView() {
        // Implement street view sharing
        this.showNotification('Street View shared', 'success');
    }

    suggestEdit() {
        if (this.currentTrail) {
            // Implement suggest edit functionality
            this.showNotification('Edit suggestion sent', 'success');
        }
    }

    async loadTrailDetails(trailId) {
        // Load additional trail details from API
        try {
            const response = await fetch(`/api/trails/${trailId}/details`);
            const details = await response.json();
            // Process additional details
        } catch (error) {
            console.error('Error loading trail details:', error);
        }
    }

    // Enhanced Directions Panel Update
    updateDirectionsPanel(result) {
        const directionsContent = document.getElementById('directions-content');
        if (!directionsContent) return;

        if (result && result.routes && result.routes.length > 0) {
            const route = result.routes[0];
            const leg = route.legs[0];
            
            // Create custom directions display
            let directionsHtml = `
                <div class="space-y-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-blue-900">Route Summary</h4>
                            <span class="text-sm text-blue-700">${route.fare ? `$${route.fare.value}` : 'Free'}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-blue-600">Distance:</span>
                                <span class="font-medium text-blue-900">${leg.distance.text}</span>
                            </div>
                            <div>
                                <span class="text-blue-600">Duration:</span>
                                <span class="font-medium text-blue-900">${leg.duration.text}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <h5 class="font-medium text-gray-900">Step-by-step directions:</h5>
            `;

            leg.steps.forEach((step, index) => {
                directionsHtml += `
                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                        <div class="flex-shrink-0 w-6 h-6 bg-green-600 text-white rounded-full flex items-center justify-center text-xs font-medium">
                            ${index + 1}
                        </div>
                        <div class="flex-1">
                            <div class="text-sm text-gray-900">${step.instructions}</div>
                            <div class="text-xs text-gray-500 mt-1">${step.distance.text} • ${step.duration.text}</div>
                        </div>
                    </div>
                `;
            });

            directionsHtml += `
                    </div>
                    
                    <div class="flex gap-2 mt-4">
                        <button onclick="window.open('${this.generateGoogleMapsUrl()}', '_blank')" 
                                class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                            Open in Google Maps
                        </button>
                        <button onclick="window.advancedTrailMap.showTrailStreetView()" 
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                            View Trail Street View
                        </button>
                    </div>
                </div>
            `;

            directionsContent.innerHTML = directionsHtml;
        } else {
            directionsContent.innerHTML = `
                <div class="text-center text-gray-500 py-8">
                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3l6-3"></path>
                     </svg>
                     <p class="text-sm">Select a trail and click "Get Directions"</p>
                 </div>
             `;
         }
     }

     // Missing method: Generate Google Maps URL
     generateGoogleMapsUrl() {
         if (!this.currentTrail) return 'https://maps.google.com';
         
         const destination = {
             lat: parseFloat(this.currentTrail.coordinates[0]),
             lng: parseFloat(this.currentTrail.coordinates[1])
         };
         
         const origin = this.userLocation ? 
             `${this.userLocation.lat},${this.userLocation.lng}` : 
             'My Location';
             
         return `https://www.google.com/maps/dir/${encodeURIComponent(origin)}/${encodeURIComponent(destination.lat + ',' + destination.lng)}`;
     }

     // Missing method: Show trail info window
     showTrailInfoWindow(trail) {
         if (!this.map || !trail) return;

         const position = {
             lat: parseFloat(trail.coordinates[0]),
             lng: parseFloat(trail.coordinates[1])
         };

         // Create info window content
         const content = `
             <div class="advanced-trail-info-window">
                 <div class="trail-info-header">
                     <div class="trail-info-name">${trail.trail_name}</div>
                     <div class="trail-info-location">${trail.location?.name || 'Unknown Location'}</div>
                 </div>
                 <div class="trail-info-body">
                     <div class="trail-info-stats">
                         <div class="trail-info-stat">
                             <div class="trail-info-stat-label">Difficulty</div>
                             <div class="trail-info-stat-value">${trail.difficulty || 'Unknown'}</div>
                         </div>
                         <div class="trail-info-stat">
                             <div class="trail-info-stat-label">Length</div>
                             <div class="trail-info-stat-value">${trail.length ? trail.length + ' km' : 'Unknown'}</div>
                         </div>
                     </div>
                     <div class="trail-info-actions">
                         <button onclick="window.advancedTrailMap.selectTrailFromInfoWindow(${trail.id})" 
                                 class="trail-info-btn">
                             View Details
                         </button>
                     </div>
                 </div>
             </div>
         `;

         // Create info window
         const infoWindow = new google.maps.InfoWindow({
             content: content,
             maxWidth: 320
         });

         // Show info window at trail position
         infoWindow.setPosition(position);
         infoWindow.open(this.map);
     }

     // Helper method for info window selection
     async selectTrailFromInfoWindow(trailId) {
         const trail = this.trails.get(trailId);
         if (trail) {
             await this.selectTrail(trail);
         }
     }

     // Cleanup method for proper resource management
     destroy() {
         // Clear intervals and timeouts
         if (this.watchId) {
             navigator.geolocation.clearWatch(this.watchId);
         }
         
         // Clear map
         if (this.map) {
             google.maps.event.clearInstanceListeners(this.map);
         }
         
         // Clear collections
         this.trails.clear();
         this.trailPaths.clear();
         this.trailMarkers.clear();
         this.weatherMarkers.clear();
         
         console.log('Advanced Trail Map destroyed');
     }
 }
// HikeThere Map Functionality
class HikeThereMap {
    constructor(options = {}) {
        this.map = null;
        this.markers = [];
        this.infoWindow = null;
        this.markerClusterer = null;
        this.currentLocation = null;
        this.trails = [];
        this.filteredTrails = [];
        this.isClusteringEnabled = true;
        this.isEmbedded = options.isEmbedded || false;
        this.mapElementId = options.mapElementId || 'map';
        this.config = options.config || {};
        
        // Default center (Philippines)
        this.defaultCenter = { lat: 12.8797, lng: 121.7740 };
        this.defaultZoom = 6;
        
        // Override defaults with config if provided
        if (this.config.centerLat && this.config.centerLng) {
            this.defaultCenter = { 
                lat: parseFloat(this.config.centerLat), 
                lng: parseFloat(this.config.centerLng) 
            };
        }
        if (this.config.zoom) {
            this.defaultZoom = parseInt(this.config.zoom);
        }
        
        this.init();
    }

    init() {
        this.initializeMap();
        this.setupEventListeners();
        this.loadTrails();
        this.setupMarkerClusterer();
    }

    initializeMap() {
        const mapOptions = {
            center: this.defaultCenter,
            zoom: this.defaultZoom,
            mapTypeId: google.maps.MapTypeId.TERRAIN,
            styles: this.getMapStyles(),
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
            }
        };

        this.map = new google.maps.Map(document.getElementById(this.mapElementId), mapOptions);
        this.infoWindow = new google.maps.InfoWindow();
        
        // Add custom controls
        this.addCustomControls();
    }

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

    addCustomControls() {
        if (!this.isEmbedded) {
            // Add a custom control for trail count
            const trailCountControl = document.createElement('div');
            trailCountControl.className = 'trail-count-control';
            trailCountControl.style.cssText = `
                background: white;
                border: 2px solid #ccc;
                border-radius: 3px;
                box-shadow: 0 2px 6px rgba(0,0,0,.3);
                cursor: pointer;
                margin-bottom: 22px;
                text-align: center;
                padding: 8px;
                font-family: Arial, sans-serif;
                font-size: 14px;
                font-weight: bold;
                color: #333;
            `;
            
            this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(trailCountControl);
            this.updateTrailCount();
        }
    }

    setupEventListeners() {
        const prefix = this.isEmbedded ? 'embedded-' : '';
        
        // Search functionality
        const searchInput = document.getElementById(`${prefix}map-search`);
        if (searchInput) {
            searchInput.addEventListener('input', this.debounce(() => {
                this.filterTrails();
            }, 300));
        }

        // Difficulty filter
        const difficultyFilter = document.getElementById(`${prefix}difficulty-filter`);
        if (difficultyFilter) {
            difficultyFilter.addEventListener('change', () => {
                this.filterTrails();
            });
        }

        // Radius filter
        const radiusFilter = document.getElementById(`${prefix}radius-filter`);
        if (radiusFilter) {
            radiusFilter.addEventListener('change', () => {
                this.filterTrails();
            });
        }

        // Use location button
        const useLocationBtn = document.getElementById(`${prefix}use-location-btn`);
        if (useLocationBtn) {
            useLocationBtn.addEventListener('click', () => {
                this.getCurrentLocation();
            });
        }

        // Reset map button
        const resetMapBtn = document.getElementById(`${prefix}reset-map-btn`);
        if (resetMapBtn) {
            resetMapBtn.addEventListener('click', () => {
                this.resetMap();
            });
        }

        // Toggle clustering button
        const clusterToggleBtn = document.getElementById(`${prefix}cluster-toggle-btn`);
        if (clusterToggleBtn) {
            clusterToggleBtn.addEventListener('click', () => {
                this.toggleClustering();
            });
        }

        // Close trail info panel
        const closeTrailInfoBtn = document.getElementById(`${prefix}close-trail-info`);
        if (closeTrailInfoBtn) {
            closeTrailInfoBtn.addEventListener('click', () => {
                this.hideTrailInfo();
            });
        }

        // Map click to close info window
        this.map.addListener('click', () => {
            this.infoWindow.close();
        });
    }

    async loadTrails() {
        try {
            this.showLoading(true);
            const response = await fetch('/map/trails');
            const trails = await response.json();
            
            this.trails = trails;
            this.filteredTrails = [...trails];
            this.addTrailMarkers();
            this.updateTrailCount();
        } catch (error) {
            console.error('Error loading trails:', error);
            this.showError('Failed to load trails. Please try again.');
        } finally {
            this.showLoading(false);
        }
    }

    addTrailMarkers() {
        // Clear existing markers
        this.clearMarkers();
        
        this.filteredTrails.forEach(trail => {
            if (trail.coordinates && trail.coordinates.lat && trail.coordinates.lng) {
                const marker = this.createTrailMarker(trail);
                this.markers.push(marker);
            }
        });

        // Update clustering
        if (this.isClusteringEnabled && this.markerClusterer) {
            this.markerClusterer.clearMarkers();
            this.markerClusterer.addMarkers(this.markers);
        }
    }

    createTrailMarker(trail) {
        const position = new google.maps.LatLng(trail.coordinates.lat, trail.coordinates.lng);
        
        // Create custom marker icon based on difficulty
        const icon = this.getDifficultyIcon(trail.difficulty);
        
        const marker = new google.maps.Marker({
            position: position,
            map: this.map,
            icon: icon,
            title: trail.name,
            animation: google.maps.Animation.DROP
        });

        // Add click listener
        marker.addListener('click', () => {
            this.showTrailInfo(trail, marker);
        });

        // Add hover effects
        marker.addListener('mouseover', () => {
            marker.setAnimation(google.maps.Animation.BOUNCE);
        });

        marker.addListener('mouseout', () => {
            marker.setAnimation(null);
        });

        return marker;
    }

    getDifficultyIcon(difficulty) {
        const baseUrl = 'https://maps.google.com/mapfiles/ms/icons/';
        const colors = {
            'beginner': 'green',
            'intermediate': 'yellow',
            'advanced': 'red'
        };
        
        return {
            url: `${baseUrl}${colors[difficulty] || 'blue'}-dot.png`,
            scaledSize: new google.maps.Size(32, 32),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(16, 16)
        };
    }

    showTrailInfo(trail, marker) {
        // Close existing info window
        this.infoWindow.close();
        
        // Create info window content
        const content = this.createInfoWindowContent(trail);
        
        // Show info window
        this.infoWindow.setContent(content);
        this.infoWindow.open(this.map, marker);
        
        // Show detailed trail info panel
        this.showTrailInfoPanel(trail);
        
        // Center map on marker
        this.map.panTo(marker.getPosition());
    }

    createInfoWindowContent(trail) {
        const difficultyColor = {
            'beginner': 'text-green-600',
            'intermediate': 'text-yellow-600',
            'advanced': 'text-red-600'
        };

        return `
            <div class="trail-info-window p-3 max-w-sm">
                <div class="flex items-start space-x-3">
                    <img src="${trail.image_url || '/img/default-trail.jpg'}" 
                         alt="${trail.name}" 
                         class="w-16 h-16 object-cover rounded-lg">
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 text-sm">${trail.name}</h3>
                        <p class="text-gray-600 text-xs">${trail.location_name}</p>
                        <div class="flex items-center space-x-2 mt-1">
                            <span class="px-2 py-1 text-xs font-medium rounded-full ${difficultyColor[trail.difficulty]} bg-${difficultyColor[trail.difficulty].split('-')[1]}-100">
                                ${trail.difficulty}
                            </span>
                            <span class="text-xs text-gray-500">${trail.length} km</span>
                        </div>
                    </div>
                </div>
                <div class="mt-2">
                    <button onclick="hikeThereMap.showTrailDetails('${trail.id}')" 
                            class="w-full px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition-colors">
                        View Details
                    </button>
                </div>
            </div>
        `;
    }

    showTrailInfoPanel(trail) {
        const prefix = this.isEmbedded ? 'embedded-' : '';
        const panel = document.getElementById(`${prefix}trail-info-panel`);
        const title = document.getElementById(`${prefix}trail-info-title`);
        const content = document.getElementById(`${prefix}trail-info-content`);
        
        if (panel && title && content) {
            title.textContent = trail.name;
            content.innerHTML = this.createTrailInfoContent(trail);
            panel.classList.remove('translate-y-full');
        }
    }

    createTrailInfoContent(trail) {
        const difficultyColor = {
            'beginner': 'text-green-600',
            'intermediate': 'text-yellow-600',
            'advanced': 'text-red-600'
        };

        return `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <img src="${trail.image_url || '/img/default-trail.jpg'}" 
                         alt="${trail.name}" 
                         class="w-full h-48 object-cover rounded-lg">
                </div>
                <div class="space-y-3">
                    <div>
                        <h4 class="font-medium text-gray-900">Location</h4>
                        <p class="text-gray-600">${trail.location_name}</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900">Difficulty</h4>
                        <span class="px-3 py-1 text-sm font-medium rounded-full ${difficultyColor[trail.difficulty]} bg-${difficultyColor[trail.difficulty].split('-')[1]}-100">
                            ${trail.difficulty}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <h4 class="font-medium text-gray-900">Length</h4>
                            <p class="text-gray-600">${trail.length} km</p>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Elevation Gain</h4>
                            <p class="text-gray-600">${trail.elevation_gain} m</p>
                        </div>
                    </div>
                    ${trail.estimated_time ? `
                        <div>
                            <h4 class="font-medium text-gray-900">Estimated Time</h4>
                            <p class="text-gray-600">${trail.estimated_time}</p>
                        </div>
                    ` : ''}
                </div>
            </div>
            ${trail.description ? `
                <div>
                    <h4 class="font-medium text-gray-900">Description</h4>
                    <p class="text-gray-600">${trail.description}</p>
                </div>
            ` : ''}
            <div class="flex space-x-3">
                <button onclick="hikeThereMap.showTrailDetails('${trail.id}')" 
                        class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    View Full Details
                </button>
                <button onclick="hikeThereMap.getDirections('${trail.coordinates.lat}', '${trail.coordinates.lng}')" 
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Get Directions
                </button>
            </div>
        `;
    }

    hideTrailInfo() {
        const prefix = this.isEmbedded ? 'embedded-' : '';
        const panel = document.getElementById(`${prefix}trail-info-panel`);
        if (panel) {
            panel.classList.add('translate-y-full');
        }
    }

    async showTrailDetails(trailId) {
        try {
            const response = await fetch(`/map/trails/${trailId}`);
            const trail = await response.json();
            
            // Redirect to trail detail page
            window.location.href = `/trails/${trail.slug}`;
        } catch (error) {
            console.error('Error loading trail details:', error);
            this.showError('Failed to load trail details.');
        }
    }

    getDirections(lat, lng) {
        const url = `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`;
        window.open(url, '_blank');
    }

    filterTrails() {
        const prefix = this.isEmbedded ? 'embedded-' : '';
        const searchInput = document.getElementById(`${prefix}map-search`);
        const difficultyFilter = document.getElementById(`${prefix}difficulty-filter`);
        
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const difficulty = difficultyFilter ? difficultyFilter.value : '';
        
        this.filteredTrails = this.trails.filter(trail => {
            const matchesSearch = !searchTerm || 
                trail.name.toLowerCase().includes(searchTerm) ||
                trail.location_name.toLowerCase().includes(searchTerm);
            
            const matchesDifficulty = !difficulty || trail.difficulty === difficulty;
            
            return matchesSearch && matchesDifficulty;
        });
        
        this.addTrailMarkers();
        this.updateTrailCount();
    }

    async getCurrentLocation() {
        if (navigator.geolocation) {
            try {
                this.showLoading(true);
                const position = await this.getCurrentPosition();
                const { latitude, longitude } = position.coords;
                
                this.currentLocation = { lat: latitude, lng: longitude };
                
                // Center map on user location
                this.map.setCenter(this.currentLocation);
                this.map.setZoom(12);
                
                // Add user location marker
                this.addUserLocationMarker();
                
                // Search for nearby trails
                await this.searchNearbyTrails(latitude, longitude);
                
            } catch (error) {
                console.error('Error getting location:', error);
                this.showError('Unable to get your location. Please check your browser settings.');
            } finally {
                this.showLoading(false);
            }
        } else {
            this.showError('Geolocation is not supported by this browser.');
        }
    }

    getCurrentPosition() {
        return new Promise((resolve, reject) => {
            navigator.geolocation.getCurrentPosition(resolve, reject, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 60000
            });
        });
    }

    addUserLocationMarker() {
        // Remove existing user location marker
        if (this.userLocationMarker) {
            this.userLocationMarker.setMap(null);
        }
        
        this.userLocationMarker = new google.maps.Marker({
            position: this.currentLocation,
            map: this.map,
            icon: {
                url: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png',
                scaledSize: new google.maps.Size(32, 32)
            },
            title: 'Your Location',
            animation: google.maps.Animation.BOUNCE
        });
    }

    async searchNearbyTrails(lat, lng) {
        try {
            const prefix = this.isEmbedded ? 'embedded-' : '';
            const radiusFilter = document.getElementById(`${prefix}radius-filter`);
            const radius = radiusFilter ? radiusFilter.value : '25';
            
            const response = await fetch('/map/search-nearby', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ lat, lng, radius })
            });
            
            const nearbyTrails = await response.json();
            
            // Filter to show only nearby trails
            this.filteredTrails = nearbyTrails;
            this.addTrailMarkers();
            this.updateTrailCount();
            
        } catch (error) {
            console.error('Error searching nearby trails:', error);
        }
    }

    resetMap() {
        const prefix = this.isEmbedded ? 'embedded-' : '';
        
        // Clear filters
        const searchInput = document.getElementById(`${prefix}map-search`);
        const difficultyFilter = document.getElementById(`${prefix}difficulty-filter`);
        const radiusFilter = document.getElementById(`${prefix}radius-filter`);
        
        if (searchInput) searchInput.value = '';
        if (difficultyFilter) difficultyFilter.value = '';
        if (radiusFilter) radiusFilter.value = '25';
        
        // Reset map view
        this.map.setCenter(this.defaultCenter);
        this.map.setZoom(this.defaultZoom);
        
        // Show all trails
        this.filteredTrails = [...this.trails];
        this.addTrailMarkers();
        this.updateTrailCount();
        
        // Remove user location marker
        if (this.userLocationMarker) {
            this.userLocationMarker.setMap(null);
            this.userLocationMarker = null;
        }
        
        // Hide trail info panel
        this.hideTrailInfo();
        
        // Close info window
        this.infoWindow.close();
    }

    toggleClustering() {
        this.isClusteringEnabled = !this.isClusteringEnabled;
        
        if (this.isClusteringEnabled) {
            this.setupMarkerClusterer();
        } else {
            if (this.markerClusterer) {
                this.markerClusterer.clearMarkers();
                this.markerClusterer = null;
            }
        }
        
        // Update button text
        const prefix = this.isEmbedded ? 'embedded-' : '';
        const button = document.getElementById(`${prefix}cluster-toggle-btn`);
        if (button) {
            button.textContent = this.isClusteringEnabled ? 'Disable Clustering' : 'Enable Clustering';
        }
    }

    setupMarkerClusterer() {
        if (!this.isClusteringEnabled) return;
        
        // Simple clustering implementation
        this.markerClusterer = new MarkerClusterer(this.map, this.markers, {
            imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'
        });
    }

    clearMarkers() {
        this.markers.forEach(marker => {
            marker.setMap(null);
        });
        this.markers = [];
    }

    updateTrailCount() {
        if (!this.isEmbedded) {
            const control = document.querySelector('.trail-count-control');
            if (control) {
                control.innerHTML = `
                    <div>Trails: ${this.filteredTrails.length}</div>
                    <div class="text-xs text-gray-500">Total: ${this.trails.length}</div>
                `;
            }
        }
    }

    showLoading(show) {
        const prefix = this.isEmbedded ? 'embedded-' : '';
        const spinner = document.getElementById(`${prefix}loading-spinner`);
        if (spinner) {
            if (show) {
                spinner.classList.remove('hidden');
            } else {
                spinner.classList.add('hidden');
            }
        }
    }

    showError(message) {
        // Create a simple error notification
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg z-50';
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
}

// Simple MarkerClusterer implementation
class MarkerClusterer {
    constructor(map, markers, options) {
        this.map = map;
        this.markers = markers;
        this.options = options;
        this.clusters = [];
        this.clusterMarkers = [];
        
        this.init();
    }
    
    init() {
        // Simple grid-based clustering
        this.createClusters();
    }
    
    createClusters() {
        const gridSize = 50;
        const bounds = this.map.getBounds();
        
        if (!bounds) return;
        
        const ne = bounds.getNorthEast();
        const sw = bounds.getSouthWest();
        
        const latStep = (ne.lat() - sw.lat()) / gridSize;
        const lngStep = (ne.lng() - sw.lng()) / gridSize;
        
        // Group markers by grid
        const grid = {};
        
        this.markers.forEach(marker => {
            const pos = marker.getPosition();
            const latIndex = Math.floor((pos.lat() - sw.lat()) / latStep);
            const lngIndex = Math.floor((pos.lng() - sw.lng()) / lngStep);
            const key = `${latIndex}-${lngIndex}`;
            
            if (!grid[key]) {
                grid[key] = [];
            }
            grid[key].push(marker);
        });
        
        // Create cluster markers
        Object.keys(grid).forEach(key => {
            const markers = grid[key];
            if (markers.length > 1) {
                this.createClusterMarker(markers);
            }
        });
    }
    
    createClusterMarker(markers) {
        const center = this.getCenter(markers);
        const count = markers.length;
        
        const clusterMarker = new google.maps.Marker({
            position: center,
            map: this.map,
            icon: this.createClusterIcon(count),
            title: `${count} trails`
        });
        
        clusterMarker.addListener('click', () => {
            this.zoomToCluster(markers);
        });
        
        this.clusterMarkers.push(clusterMarker);
        
        // Hide individual markers
        markers.forEach(marker => {
            marker.setMap(null);
        });
    }
    
    getCenter(markers) {
        let lat = 0, lng = 0;
        markers.forEach(marker => {
            const pos = marker.getPosition();
            lat += pos.lat();
            lng += pos.lng();
        });
        return new google.maps.LatLng(lat / markers.length, lng / markers.length);
    }
    
    createClusterIcon(count) {
        return {
            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                <svg width="40" height="40" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="20" cy="20" r="18" fill="#4F46E5" stroke="#312E81" stroke-width="2"/>
                    <text x="20" y="25" font-family="Arial" font-size="14" font-weight="bold" text-anchor="middle" fill="white">${count}</text>
                </svg>
            `),
            scaledSize: new google.maps.Size(40, 40),
            anchor: new google.maps.Point(20, 20)
        };
    }
    
    zoomToCluster(markers) {
        const bounds = new google.maps.LatLngBounds();
        markers.forEach(marker => {
            bounds.extend(marker.getPosition());
        });
        this.map.fitBounds(bounds);
    }
    
    clearMarkers() {
        this.clusterMarkers.forEach(marker => {
            marker.setMap(null);
        });
        this.clusterMarkers = [];
        
        // Show individual markers again
        this.markers.forEach(marker => {
            marker.setMap(this.map);
        });
    }
    
    addMarkers(markers) {
        this.markers = markers;
        this.createClusters();
    }
}

// Initialize map when DOM is loaded
let hikeThereMap;

function initMap() {
    hikeThereMap = new HikeThereMap();
}

// Export for global access
window.hikeThereMap = hikeThereMap;
window.initMap = initMap;

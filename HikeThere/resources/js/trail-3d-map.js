/**
 * Trail 3D Map - Google Map Tiles API Integration
 * Photorealistic 3D terrain visualization for hiking trails
 * Author: HikeThere Development Team
 * Date: October 14, 2025
 */

class Trail3DMap {
    constructor(options = {}) {
        // Core properties
        this.map = null;
        this.mapElement = null;
        this.elementId = options.elementId || 'trail-3d-map';
        
        // Trail data
        this.trailData = options.trailData || null;
        this.trailPath = null;
        this.markers = [];
        
        // Camera settings
        this.camera = {
            center: options.center || { lat: 14.5995, lng: 120.9842 }, // Default: Philippines
            zoom: options.zoom || 15,
            tilt: options.tilt || 65, // 0-85.051111 degrees
            heading: options.heading || 0, // 0-360 degrees
            altitude: options.altitude || 0
        };
        
        // 3D Configuration
        this.config = {
            enable3D: options.enable3D !== false,
            enableStreetView: options.enableStreetView !== false,
            autoTour: options.autoTour || false,
            animationSpeed: options.animationSpeed || 50, // ms per degree
            mapId: options.mapId || null // Required for 3D
        };
        
        // State
        this.isInitialized = false;
        this.is3DEnabled = false;
        this.isAnimating = false;
        this.animationFrame = null;
        
        // WebGL detection
        this.webGLSupported = this.detectWebGL();
        
        console.log('Trail3DMap initialized with options:', options);
    }
    
    /**
     * Detect WebGL 2.0 support
     */
    detectWebGL() {
        try {
            const canvas = document.createElement('canvas');
            const gl = canvas.getContext('webgl2') || canvas.getContext('webgl');
            return !!gl;
        } catch (e) {
            console.warn('WebGL detection failed:', e);
            return false;
        }
    }
    
    /**
     * Initialize the 3D map
     */
    async init() {
        try {
            console.log('Initializing Trail3DMap...');
            
            // Get map element
            this.mapElement = document.getElementById(this.elementId);
            if (!this.mapElement) {
                throw new Error(`Map element with ID '${this.elementId}' not found`);
            }
            
            // Check WebGL support
            if (!this.webGLSupported) {
                this.showWebGLFallback();
                return;
            }
            
            // Wait for Google Maps API
            await this.waitForGoogleMaps();
            
            // Initialize map with 3D
            await this.initializeMap();
            
            // Load trail data if provided
            if (this.trailData) {
                await this.loadTrailData(this.trailData);
            }
            
            this.isInitialized = true;
            console.log('Trail3DMap initialized successfully');
            
            // Start auto-tour if enabled
            if (this.config.autoTour) {
                this.startAutoTour();
            }
            
        } catch (error) {
            console.error('Failed to initialize Trail3DMap:', error);
            this.showErrorFallback(error);
        }
    }
    
    /**
     * Wait for Google Maps API to load
     */
    waitForGoogleMaps() {
        return new Promise((resolve, reject) => {
            if (typeof google !== 'undefined' && google.maps) {
                resolve();
                return;
            }
            
            let attempts = 0;
            const maxAttempts = 50;
            
            const checkInterval = setInterval(() => {
                attempts++;
                
                if (typeof google !== 'undefined' && google.maps) {
                    clearInterval(checkInterval);
                    resolve();
                } else if (attempts >= maxAttempts) {
                    clearInterval(checkInterval);
                    reject(new Error('Google Maps API failed to load'));
                }
            }, 100);
        });
    }
    
    /**
     * Initialize the Google Map with 3D tiles
     */
    async initializeMap() {
        console.log('Creating 3D map instance...');
        
        const mapOptions = {
            center: this.camera.center,
            zoom: this.camera.zoom,
            tilt: this.camera.tilt,
            heading: this.camera.heading,
            
            // Map ID is REQUIRED for 3D tiles
            mapId: this.config.mapId || 'DEMO_MAP_ID',
            
            // Map type (use satellite for better 3D)
            mapTypeId: google.maps.MapTypeId.SATELLITE,
            
            // Controls
            mapTypeControl: true,
            mapTypeControlOptions: {
                style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                position: google.maps.ControlPosition.TOP_RIGHT
            },
            
            streetViewControl: this.config.enableStreetView,
            fullscreenControl: true,
            zoomControl: true,
            
            // 3D specific options
            rotateControl: true,
            tiltControl: true,
            
            // Gestural controls
            gestureHandling: 'greedy',
            
            // Disable 45° imagery (we want photorealistic 3D)
            rotateControlOptions: {
                position: google.maps.ControlPosition.RIGHT_BOTTOM
            }
        };
        
        // Create map
        this.map = new google.maps.Map(this.mapElement, mapOptions);
        
        // Add custom 3D controls
        this.add3DControls();
        
        // Setup map event listeners
        this.setupMapListeners();
        
        console.log('3D map created successfully');
    }
    
    /**
     * Add custom 3D control buttons
     */
    add3DControls() {
        // Create control container
        const controlDiv = document.createElement('div');
        controlDiv.className = 'trail-3d-controls';
        controlDiv.style.cssText = `
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
            margin: 10px;
            padding: 8px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        `;
        
        // Toggle 3D button
        const toggle3DBtn = this.createControlButton(
            '3D View',
            'Toggle 3D terrain',
            () => this.toggle3D()
        );
        
        // Auto-tour button
        const autoTourBtn = this.createControlButton(
            '🎬 Tour',
            'Auto-rotate around trail',
            () => this.toggleAutoTour()
        );
        
        // Reset view button
        const resetBtn = this.createControlButton(
            '🔄 Reset',
            'Reset camera view',
            () => this.resetCamera()
        );
        
        controlDiv.appendChild(toggle3DBtn);
        controlDiv.appendChild(autoTourBtn);
        controlDiv.appendChild(resetBtn);
        
        // Add to map
        this.map.controls[google.maps.ControlPosition.LEFT_TOP].push(controlDiv);
    }
    
    /**
     * Create a control button
     */
    createControlButton(text, title, onClick) {
        const button = document.createElement('button');
        button.textContent = text;
        button.title = title;
        button.style.cssText = `
            background: #336d66;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s;
            white-space: nowrap;
        `;
        
        button.addEventListener('mouseenter', () => {
            button.style.background = '#2a5a54';
        });
        
        button.addEventListener('mouseleave', () => {
            button.style.background = '#336d66';
        });
        
        button.addEventListener('click', onClick);
        
        return button;
    }
    
    /**
     * Setup map event listeners
     */
    setupMapListeners() {
        // Camera change listener
        this.map.addListener('center_changed', () => {
            if (this.map) {
                this.camera.center = this.map.getCenter().toJSON();
            }
        });
        
        this.map.addListener('zoom_changed', () => {
            if (this.map) {
                this.camera.zoom = this.map.getZoom();
            }
        });
        
        this.map.addListener('tilt_changed', () => {
            if (this.map) {
                this.camera.tilt = this.map.getTilt();
            }
        });
        
        this.map.addListener('heading_changed', () => {
            if (this.map) {
                this.camera.heading = this.map.getHeading();
            }
        });
    }
    
    /**
     * Load trail data and render on map
     */
    async loadTrailData(trailData) {
        console.log('Loading trail data:', trailData);
        
        this.trailData = trailData;
        
        // Parse coordinates
        const coordinates = this.parseCoordinates(trailData.coordinates || trailData.path_coordinates);
        
        if (!coordinates || coordinates.length === 0) {
            console.warn('No valid coordinates found in trail data');
            return;
        }
        
        // Create trail path
        this.renderTrailPath(coordinates);
        
        // Add markers
        this.addTrailMarkers(coordinates);
        
        // Center map on trail
        this.centerOnTrail(coordinates);
    }
    
    /**
     * Parse coordinates from various formats
     */
    parseCoordinates(coords) {
        if (!coords) return [];
        
        // If already array of {lat, lng} objects
        if (Array.isArray(coords) && coords.length > 0) {
            if (typeof coords[0] === 'object' && 'lat' in coords[0]) {
                return coords;
            }
        }
        
        // If JSON string
        if (typeof coords === 'string') {
            try {
                const parsed = JSON.parse(coords);
                return this.parseCoordinates(parsed);
            } catch (e) {
                console.error('Failed to parse coordinates:', e);
                return [];
            }
        }
        
        return [];
    }
    
    /**
     * Render trail path on 3D map
     */
    renderTrailPath(coordinates) {
        // Remove existing path
        if (this.trailPath) {
            this.trailPath.setMap(null);
        }
        
        // Create polyline
        this.trailPath = new google.maps.Polyline({
            path: coordinates,
            geodesic: true,
            strokeColor: '#FF4444',
            strokeOpacity: 1.0,
            strokeWeight: 4,
            map: this.map,
            zIndex: 1000
        });
        
        console.log(`Trail path rendered with ${coordinates.length} points`);
    }
    
    /**
     * Add trail markers (start, end, waypoints)
     */
    addTrailMarkers(coordinates) {
        // Clear existing markers
        this.clearMarkers();
        
        if (coordinates.length === 0) return;
        
        // Start marker (green)
        const startMarker = new google.maps.Marker({
            position: coordinates[0],
            map: this.map,
            title: 'Trail Start',
            icon: {
                url: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png',
                scaledSize: new google.maps.Size(32, 32)
            },
            zIndex: 1001
        });
        
        this.markers.push(startMarker);
        
        // End marker (red)
        if (coordinates.length > 1) {
            const endMarker = new google.maps.Marker({
                position: coordinates[coordinates.length - 1],
                map: this.map,
                title: 'Trail End',
                icon: {
                    url: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png',
                    scaledSize: new google.maps.Size(32, 32)
                },
                zIndex: 1001
            });
            
            this.markers.push(endMarker);
        }
        
        // Waypoint markers (every 10th point)
        if (coordinates.length > 20) {
            const step = Math.floor(coordinates.length / 10);
            
            for (let i = step; i < coordinates.length - step; i += step) {
                const waypoint = new google.maps.Marker({
                    position: coordinates[i],
                    map: this.map,
                    title: `Waypoint ${Math.floor(i / step)}`,
                    icon: {
                        url: 'http://maps.google.com/mapfiles/ms/icons/yellow-dot.png',
                        scaledSize: new google.maps.Size(24, 24)
                    },
                    zIndex: 1000
                });
                
                this.markers.push(waypoint);
            }
        }
        
        console.log(`Added ${this.markers.length} markers to map`);
    }
    
    /**
     * Center map on trail with appropriate zoom
     */
    centerOnTrail(coordinates) {
        if (!coordinates || coordinates.length === 0) return;
        
        const bounds = new google.maps.LatLngBounds();
        coordinates.forEach(coord => {
            bounds.extend(coord);
        });
        
        this.map.fitBounds(bounds);
        
        // Adjust zoom for 3D view
        setTimeout(() => {
            const currentZoom = this.map.getZoom();
            this.map.setZoom(Math.max(currentZoom - 1, 14));
        }, 100);
    }
    
    /**
     * Toggle 3D view
     */
    toggle3D() {
        this.is3DEnabled = !this.is3DEnabled;
        
        if (this.is3DEnabled) {
            // Enable 3D
            this.map.setTilt(65);
            this.map.setMapTypeId(google.maps.MapTypeId.SATELLITE);
            console.log('3D view enabled');
        } else {
            // Disable 3D
            this.map.setTilt(0);
            console.log('3D view disabled');
        }
    }
    
    /**
     * Toggle auto-tour animation
     */
    toggleAutoTour() {
        if (this.isAnimating) {
            this.stopAutoTour();
        } else {
            this.startAutoTour();
        }
    }
    
    /**
     * Start auto-tour around trail
     */
    startAutoTour() {
        if (this.isAnimating) return;
        
        this.isAnimating = true;
        console.log('Starting auto-tour...');
        
        // Enable 3D first
        if (!this.is3DEnabled) {
            this.toggle3D();
        }
        
        let currentHeading = this.map.getHeading() || 0;
        
        const animate = () => {
            if (!this.isAnimating) return;
            
            currentHeading = (currentHeading + 1) % 360;
            this.map.setHeading(currentHeading);
            
            this.animationFrame = setTimeout(animate, this.config.animationSpeed);
        };
        
        animate();
    }
    
    /**
     * Stop auto-tour animation
     */
    stopAutoTour() {
        this.isAnimating = false;
        
        if (this.animationFrame) {
            clearTimeout(this.animationFrame);
            this.animationFrame = null;
        }
        
        console.log('Auto-tour stopped');
    }
    
    /**
     * Reset camera to initial view
     */
    resetCamera() {
        if (this.trailData && this.trailPath) {
            const coordinates = this.parseCoordinates(this.trailData.coordinates || this.trailData.path_coordinates);
            this.centerOnTrail(coordinates);
        } else {
            this.map.setCenter(this.camera.center);
            this.map.setZoom(15);
        }
        
        this.map.setTilt(65);
        this.map.setHeading(0);
        
        console.log('Camera reset');
    }
    
    /**
     * Clear all markers
     */
    clearMarkers() {
        this.markers.forEach(marker => marker.setMap(null));
        this.markers = [];
    }
    
    /**
     * Show WebGL fallback
     */
    showWebGLFallback() {
        this.mapElement.innerHTML = `
            <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: #f3f4f6; padding: 20px; text-align: center;">
                <div>
                    <svg style="width: 64px; height: 64px; color: #9ca3af; margin-bottom: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h3 style="font-size: 18px; font-weight: 600; color: #374151; margin-bottom: 8px;">
                        3D View Not Supported
                    </h3>
                    <p style="color: #6b7280; font-size: 14px; margin-bottom: 16px;">
                        Your browser doesn't support WebGL 2.0 required for 3D terrain visualization.
                    </p>
                    <button onclick="location.reload()" style="background: #336d66; color: white; padding: 8px 16px; border-radius: 6px; border: none; cursor: pointer; font-weight: 600;">
                        Try Standard Map View
                    </button>
                </div>
            </div>
        `;
    }
    
    /**
     * Show error fallback
     */
    showErrorFallback(error) {
        this.mapElement.innerHTML = `
            <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: #fef2f2; padding: 20px; text-align: center;">
                <div>
                    <svg style="width: 64px; height: 64px; color: #ef4444; margin-bottom: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <h3 style="font-size: 18px; font-weight: 600; color: #991b1b; margin-bottom: 8px;">
                        Failed to Load 3D Map
                    </h3>
                    <p style="color: #7f1d1d; font-size: 14px; margin-bottom: 4px;">
                        ${error.message || 'Unknown error occurred'}
                    </p>
                    <button onclick="location.reload()" style="background: #336d66; color: white; padding: 8px 16px; border-radius: 6px; border: none; cursor: pointer; font-weight: 600; margin-top: 16px;">
                        Reload Page
                    </button>
                </div>
            </div>
        `;
    }
    
    /**
     * Cleanup and destroy map instance
     */
    destroy() {
        this.stopAutoTour();
        this.clearMarkers();
        
        if (this.trailPath) {
            this.trailPath.setMap(null);
        }
        
        if (this.map) {
            this.map = null;
        }
        
        console.log('Trail3DMap destroyed');
    }
}

// Export for global use
window.Trail3DMap = Trail3DMap;

// Auto-initialize if data attribute present
document.addEventListener('DOMContentLoaded', () => {
    const maps3D = document.querySelectorAll('[data-trail-3d-map]');
    
    maps3D.forEach(element => {
        const options = {
            elementId: element.id,
            enable3D: element.dataset.enable3d !== 'false',
            autoTour: element.dataset.autoTour === 'true',
            mapId: element.dataset.mapId || null
        };
        
        // Load trail data if provided
        if (element.dataset.trailId) {
            fetch(`/api/trails/${element.dataset.trailId}/3d-data`)
                .then(response => response.json())
                .then(data => {
                    options.trailData = data;
                    const map = new Trail3DMap(options);
                    map.init();
                })
                .catch(error => {
                    console.error('Failed to load trail data:', error);
                    const map = new Trail3DMap(options);
                    map.init();
                });
        } else {
            const map = new Trail3DMap(options);
            map.init();
        }
    });
});

console.log('Trail3DMap class loaded successfully');

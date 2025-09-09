/**
 * Modern Google Places Service following new Places API patterns
 * Based on official Google Maps JavaScript API documentation
 */
class ModernPlacesService {
    constructor(map) {
        this.map = map;
        this.service = new google.maps.places.PlacesService(map);
        this.infoWindow = new google.maps.InfoWindow();
    }

    /**
     * Find places using text query (new pattern)
     * @param {string} query - Search query
     * @param {Array} fields - Fields to retrieve
     * @param {Function} callback - Callback function
     */
    findPlaceFromQuery(query, fields = ['name', 'geometry', 'place_id'], callback) {
        console.log('🔍 Finding place from query:', query);
        
        const request = {
            query: query,
            fields: fields,
        };

        this.service.findPlaceFromQuery(request, (results, status) => {
            if (status === google.maps.places.PlacesServiceStatus.OK) {
                console.log('✅ Place found:', results);
                callback(results, status);
            } else {
                console.error('❌ Place search failed:', status);
                this.handlePlacesError(status, () => {
                    // Retry logic
                    this.findPlaceFromQuery(query, fields, callback);
                });
            }
        });
    }

    /**
     * Get place details by place ID (enhanced with retry logic)
     * @param {string} placeId - Place ID
     * @param {Array} fields - Fields to retrieve
     * @param {Function} callback - Callback function
     */
    getPlaceDetails(placeId, fields = ['name', 'geometry', 'formatted_address'], callback) {
        console.log('📍 Getting place details for:', placeId);
        
        const request = {
            placeId: placeId,
            fields: fields,
        };

        let retryCount = 0;
        const maxRetries = 3;

        const executeRequest = () => {
            this.service.getDetails(request, (place, status) => {
                if (status === google.maps.places.PlacesServiceStatus.OK) {
                    console.log('✅ Place details retrieved:', place);
                    callback(place, status);
                } else if (status === google.maps.places.PlacesServiceStatus.OVER_QUERY_LIMIT && retryCount < maxRetries) {
                    // Exponential backoff retry
                    const delay = (Math.pow(2, retryCount) + Math.random()) * 500;
                    console.warn(`⚠️ Rate limit hit, retrying in ${delay}ms (attempt ${retryCount + 1}/${maxRetries})`);
                    
                    setTimeout(() => {
                        retryCount++;
                        executeRequest();
                    }, delay);
                } else {
                    console.error('❌ Place details failed:', status);
                    callback(null, status);
                }
            });
        };

        executeRequest();
    }

    /**
     * Search for nearby places (modern pattern)
     * @param {google.maps.LatLng} location - Center location
     * @param {number} radius - Search radius in meters
     * @param {Array} types - Place types to search for
     * @param {Function} callback - Callback function
     */
    searchNearby(location, radius = 1000, types = ['tourist_attraction'], callback) {
        console.log('🗺️ Searching nearby places:', { location, radius, types });
        
        const request = {
            location: location,
            radius: radius,
            type: types
        };

        this.service.nearbySearch(request, (results, status) => {
            if (status === google.maps.places.PlacesServiceStatus.OK) {
                console.log(`✅ Found ${results.length} nearby places`);
                callback(results, status);
            } else {
                console.error('❌ Nearby search failed:', status);
                this.handlePlacesError(status, () => {
                    this.searchNearby(location, radius, types, callback);
                });
            }
        });
    }

    /**
     * Text search for places (modern pattern)
     * @param {string} query - Search query
     * @param {google.maps.LatLng} location - Center location (optional)
     * @param {number} radius - Search radius (optional)
     * @param {Function} callback - Callback function
     */
    textSearch(query, location = null, radius = 5000, callback) {
        console.log('📝 Text search for places:', query);
        
        const request = {
            query: query,
        };

        if (location) {
            request.location = location;
            request.radius = radius;
        }

        this.service.textSearch(request, (results, status) => {
            if (status === google.maps.places.PlacesServiceStatus.OK) {
                console.log(`✅ Text search found ${results.length} places`);
                callback(results, status);
            } else {
                console.error('❌ Text search failed:', status);
                this.handlePlacesError(status, () => {
                    this.textSearch(query, location, radius, callback);
                });
            }
        });
    }

    /**
     * Create marker for place (following new pattern)
     * @param {Object} place - Place object
     * @param {google.maps.Map} map - Map instance
     * @return {google.maps.Marker} - Created marker
     */
    createPlaceMarker(place, map = null) {
        const targetMap = map || this.map;
        
        if (!place.geometry || !place.geometry.location) {
            console.error('❌ Cannot create marker: no geometry data');
            return null;
        }

        const marker = new google.maps.Marker({
            position: place.geometry.location,
            map: targetMap,
            title: place.name,
            animation: google.maps.Animation.DROP
        });

        // Add info window
        marker.addListener('click', () => {
            const content = `
                <div style="padding: 10px;">
                    <h3 style="margin: 0 0 10px 0; color: #333;">${place.name}</h3>
                    ${place.formatted_address ? `<p style="margin: 0; color: #666;">${place.formatted_address}</p>` : ''}
                    ${place.rating ? `<p style="margin: 5px 0 0 0; color: #666;">⭐ ${place.rating}/5</p>` : ''}
                </div>
            `;
            
            this.infoWindow.setContent(content);
            this.infoWindow.open(targetMap, marker);
        });

        console.log('✅ Marker created for:', place.name);
        return marker;
    }

    /**
     * Handle Places API errors with retry logic
     * @param {string} status - Error status
     * @param {Function} retryCallback - Function to retry
     */
    handlePlacesError(status, retryCallback = null) {
        switch (status) {
            case google.maps.places.PlacesServiceStatus.OVER_QUERY_LIMIT:
                console.warn('⚠️ Places API query limit exceeded');
                if (retryCallback) {
                    setTimeout(retryCallback, 1000); // Retry after 1 second
                }
                break;
            case google.maps.places.PlacesServiceStatus.REQUEST_DENIED:
                console.error('❌ Places API request denied - check API key permissions');
                break;
            case google.maps.places.PlacesServiceStatus.INVALID_REQUEST:
                console.error('❌ Invalid Places API request');
                break;
            case google.maps.places.PlacesServiceStatus.ZERO_RESULTS:
                console.info('ℹ️ No places found for the request');
                break;
            default:
                console.error('❌ Places API error:', status);
        }
    }

    /**
     * Batch get details for multiple places
     * @param {Array} placeIds - Array of place IDs
     * @param {Array} fields - Fields to retrieve
     * @param {Function} callback - Callback function
     */
    batchGetDetails(placeIds, fields, callback) {
        console.log(`📦 Batch getting details for ${placeIds.length} places`);
        
        const results = [];
        let completed = 0;
        
        placeIds.forEach((placeId, index) => {
            // Add delay between requests to avoid rate limiting
            setTimeout(() => {
                this.getPlaceDetails(placeId, fields, (place, status) => {
                    results[index] = { place, status };
                    completed++;
                    
                    if (completed === placeIds.length) {
                        console.log('✅ Batch details completed');
                        callback(results);
                    }
                });
            }, index * 100); // 100ms delay between requests
        });
    }
}

// Example usage following the pattern you provided:
/*
function initMap() {
    const manila = new google.maps.LatLng(14.5995, 120.9842);
    
    const map = new google.maps.Map(document.getElementById('map'), {
        center: manila,
        zoom: 15
    });
    
    const placesService = new ModernPlacesService(map);
    
    // Find a specific place
    placesService.findPlaceFromQuery(
        'Rizal Park Manila',
        ['name', 'geometry', 'formatted_address', 'rating'],
        (results, status) => {
            if (status === google.maps.places.PlacesServiceStatus.OK) {
                for (let i = 0; i < results.length; i++) {
                    placesService.createPlaceMarker(results[i]);
                }
                map.setCenter(results[0].geometry.location);
            }
        }
    );
}
*/

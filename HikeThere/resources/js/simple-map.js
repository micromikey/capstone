// Simple Google Maps test for HikeThere
// This is a basic map to test if Google Maps loads properly

console.log('Simple map script loaded');

// Simple map initialization function
function initSimpleMap() {
    console.log('Initializing simple map...');
    
    try {
        // Check if Google Maps is available
        if (typeof google === 'undefined' || !google.maps) {
            console.error('Google Maps API not loaded');
            document.getElementById('simple-map').innerHTML = 
                '<div class="text-center p-8 text-red-600">Google Maps API not loaded</div>';
            return;
        }
        
        console.log('Google Maps API available');
        
        const mapElement = document.getElementById('simple-map');
        if (!mapElement) {
            console.error('Map element not found');
            return;
        }
        
        // Simple map options
        const mapOptions = {
            center: { lat: 14.5995, lng: 120.9842 }, // Manila, Philippines
            zoom: 8,
            mapTypeId: google.maps.MapTypeId.TERRAIN
        };
        
        // Create the map
        const map = new google.maps.Map(mapElement, mapOptions);
        console.log('Map created successfully');
        
        // Add a simple marker
        const marker = new google.maps.Marker({
            position: { lat: 14.5995, lng: 120.9842 },
            map: map,
            title: 'Manila'
        });
        
        console.log('Marker added successfully');
        
        // Test trails loading
        console.log('Testing trails API...');
        fetch('/map/trails')
            .then(response => {
                console.log('Trails API response status:', response.status);
                console.log('Trails API response ok:', response.ok);
                return response.text();
            })
            .then(data => {
                console.log('Trails API response data:', data);
                try {
                    const jsonData = JSON.parse(data);
                    console.log('Parsed trails data:', jsonData);
                    console.log('Number of trails:', jsonData.length);
                } catch (e) {
                    console.error('Failed to parse trails JSON:', e);
                    console.log('Raw response:', data);
                }
            })
            .catch(error => {
                console.error('Error loading trails:', error);
            });
        
    } catch (error) {
        console.error('Error initializing simple map:', error);
        document.getElementById('simple-map').innerHTML = 
            '<div class="text-center p-8 text-red-600">Map initialization error: ' + error.message + '</div>';
    }
}

// Make it available globally
window.initSimpleMap = initSimpleMap;

console.log('Simple map script ready');

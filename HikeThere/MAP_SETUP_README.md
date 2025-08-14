# HikeThere Map Functionality Setup Guide

## Overview
HikeThere now includes comprehensive map functionality powered by Google Maps API, featuring interactive trail markers, search, filtering, and clustering capabilities.

## Features

### 🗺️ Interactive Map
- **Google Maps Integration**: Terrain view with custom styling
- **Trail Markers**: Color-coded by difficulty level
- **Info Windows**: Quick trail information on marker click
- **Trail Info Panel**: Detailed trail information at bottom of screen

### 🔍 Search & Filtering
- **Real-time Search**: Search trails by name, mountain, or location
- **Difficulty Filter**: Filter by beginner, intermediate, or advanced
- **Radius Search**: Find trails within 10, 25, 50, or 100 km
- **Location-based Search**: Use your current location to find nearby trails

### 📍 Advanced Features
- **Marker Clustering**: Group nearby markers for better performance
- **User Location**: Get your current location and find nearby trails
- **Responsive Design**: Works on desktop, tablet, and mobile devices
- **Trail Count Display**: Shows filtered and total trail counts

## Setup Instructions

### 1. Google Maps API Key

First, you need to obtain a Google Maps API key:

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable the following APIs:
   - Maps JavaScript API
   - Geocoding API
   - Places API
4. Create credentials (API Key)
5. Restrict the API key to your domain for security

### 2. Environment Configuration

Add your Google Maps API key to your `.env` file:

```env
GOOGLE_MAPS_API_KEY=your_google_maps_api_key_here
```

### 3. Build Assets

Run the following commands to build the assets:

```bash
npm install
npm run build
```

Or for development:

```bash
npm run dev
```

### 4. Database Requirements

Ensure your database has the following tables with proper data:

- `trails` table with coordinates (JSON field)
- `locations` table with latitude/longitude fields
- `trail_images` table for trail images

### 5. Routes

The following routes are automatically available:

- `/map` - Main map view
- `/map/trails` - API endpoint for trail data
- `/map/trails/{id}` - API endpoint for individual trail details
- `/map/search-nearby` - API endpoint for nearby trail search

## Usage

### For Users

1. **Navigate to Map**: Click "View Map" from the explore page or go to `/map`
2. **Search Trails**: Use the search box to find specific trails
3. **Filter by Difficulty**: Select difficulty level from the dropdown
4. **Find Nearby Trails**: Click "Use My Location" to find trails near you
5. **View Trail Details**: Click on any marker to see trail information
6. **Get Directions**: Click "Get Directions" to open Google Maps directions

### For Developers

The map functionality is built with vanilla JavaScript and includes:

- **HikeThereMap Class**: Main map controller
- **MarkerClusterer Class**: Custom clustering implementation
- **Event Handling**: Comprehensive event management
- **API Integration**: RESTful API calls to Laravel backend

## Customization

### Map Styling

Modify the `getMapStyles()` method in `resources/js/map.js` to customize map appearance:

```javascript
getMapStyles() {
    return [
        {
            featureType: 'poi',
            elementType: 'labels',
            stylers: [{ visibility: 'off' }]
        },
        // Add more custom styles here
    ];
}
```

### Marker Icons

Customize trail difficulty icons by modifying the `getDifficultyIcon()` method:

```javascript
getDifficultyIcon(difficulty) {
    const colors = {
        'beginner': 'green',
        'intermediate': 'yellow',
        'advanced': 'red'
    };
    
    return {
        url: `path/to/your/icons/${colors[difficulty]}.png`,
        scaledSize: new google.maps.Size(32, 32)
    };
}
```

### Clustering

Adjust clustering behavior by modifying the `MarkerClusterer` class:

```javascript
createClusters() {
    const gridSize = 50; // Adjust grid size for clustering
    // ... clustering logic
}
```

## Troubleshooting

### Common Issues

1. **Map Not Loading**
   - Check if Google Maps API key is valid
   - Ensure API key has proper restrictions
   - Check browser console for JavaScript errors

2. **Trails Not Showing**
   - Verify database has trail data with coordinates
   - Check API endpoints are working
   - Ensure CSRF token is properly set

3. **Geolocation Not Working**
   - Check if HTTPS is enabled (required for geolocation)
   - Ensure user has granted location permissions
   - Check browser console for geolocation errors

### Debug Mode

Enable debug logging by checking the browser console. The map includes comprehensive error handling and logging.

## Performance Optimization

### For Large Numbers of Trails

1. **Enable Clustering**: Use the clustering feature for better performance
2. **Implement Pagination**: Modify the API to return paginated results
3. **Lazy Loading**: Load trails as the user pans/zooms the map
4. **Caching**: Implement Redis caching for trail data

### Database Optimization

1. **Index Coordinates**: Add spatial indexes to location coordinates
2. **Query Optimization**: Use efficient distance calculations
3. **Data Denormalization**: Consider denormalizing frequently accessed data

## Security Considerations

1. **API Key Restrictions**: Restrict Google Maps API key to your domain
2. **CSRF Protection**: All POST requests include CSRF tokens
3. **Input Validation**: All user inputs are validated server-side
4. **Rate Limiting**: Consider implementing rate limiting for API endpoints

## Browser Support

- **Chrome**: 60+
- **Firefox**: 55+
- **Safari**: 12+
- **Edge**: 79+

## Mobile Optimization

The map is fully responsive and includes:

- Touch-friendly controls
- Swipe gestures for map navigation
- Optimized marker sizes for mobile
- Responsive info panels

## Future Enhancements

Potential improvements for future versions:

1. **Offline Support**: Cache map tiles and trail data
2. **Real-time Updates**: WebSocket integration for live trail updates
3. **Advanced Filtering**: More sophisticated search and filter options
4. **Trail Routing**: Show actual trail paths on the map
5. **Weather Integration**: Display weather conditions on the map
6. **Social Features**: User reviews and ratings on map markers

## Support

For technical support or questions about the map functionality:

1. Check the browser console for error messages
2. Verify all setup steps are completed
3. Ensure database contains proper trail data
4. Check API endpoints are accessible

## License

This map functionality is part of the HikeThere application and follows the same licensing terms.

# Google Maps API Integration for Trail Images

## Overview

The HikeThere application integrates Google Maps API to automatically fetch relevant images for organization-created trails. This enhancement provides high-quality, location-specific images when organizations haven't uploaded their own photos.

## Features

### 🎯 **Smart Image Priority System**
1. **Organization Images First** - User-uploaded images always take priority
2. **Google Places API** - Location-specific images from Google Maps
3. **Default Fallback** - Generic hiking image if no other images available

### 🗺️ **Google Maps Integration**
- **Places API** - Searches for natural features, tourist attractions, and establishments
- **Location-specific** - Uses trail location, mountain name, and region for targeted searches
- **Philippines-focused** - Optimized for Philippine hiking trails and mountains
- **Caching** - 2-hour cache to reduce API calls and improve performance

### 🖼️ **Image Sources**
- **Organization Uploads** - Primary source (drag & drop, multiple formats)
- **Google Places** - Location-specific photos from Google Maps
- **Default Image** - Generic hiking image as fallback

## Technical Implementation

### Enhanced TrailImageService

```php
// Get all available images for a trail
$imageService = new TrailImageService();
$images = $imageService->getTrailImages($trail, 5);

// Get primary image (organization first, then Google Places)
$primaryImage = $imageService->getPrimaryTrailImage($trail);
```

### Google Places API Search Queries

The service generates optimized search queries:

1. **Location-based**: `"Mount Arayat hiking trail"`
2. **Mountain-specific**: `"Mount Arayat philippines"`
3. **Trail-specific**: `"Arayat Trail hiking"`
4. **Combined**: `"Mount Arayat Pampanga"`
5. **Regional**: `"Central Luzon hiking trails"`

### Caching Strategy

- **Google Places**: 2-hour cache (7200 seconds)
- **Cache keys**: Include trail ID, name, and limit for uniqueness

## Configuration

### Required Environment Variables

```env
GOOGLE_MAPS_API_KEY=your_google_maps_api_key_here
```

### Google Maps API Setup

1. **Enable APIs** in Google Cloud Console:
   - Places API
   - Maps JavaScript API
   - Geocoding API

2. **API Key Permissions**:
   - Restrict to Places API
   - Set appropriate quotas

## Usage Examples

### In Blade Views

```php
@php
    $imageService = app(App\Services\TrailImageService::class);
    $primaryImage = $imageService->getPrimaryTrailImage($trail);
@endphp

<img src="{{ $primaryImage['url'] }}" 
     alt="{{ $primaryImage['caption'] }}"
     class="trail-image">
```

### In Controllers

```php
public function show(Trail $trail)
{
    $imageService = new TrailImageService();
    $images = $imageService->getTrailImages($trail, 10);
    
    return view('trails.show', compact('trail', 'images'));
}
```

### Testing

```bash
# Test Google Maps integration
php artisan test:google-maps-images

# Test specific trail
php artisan test:google-maps-images --trail-id=1
```

## Image Display Features

### Source Attribution
- **Organization images**: No badge (primary source)
- **Google Places images**: Small badge showing "Google" source
- **Hover effects**: Show source on image hover

### Gallery Integration
- **Unified gallery**: Organization + Google Places images in single gallery
- **Smooth transitions**: Alpine.js powered image switching
- **Thumbnail navigation**: Click thumbnails to switch images

## Benefits

### For Organizations
- **Professional appearance**: High-quality images even without uploads
- **Location accuracy**: Images match actual trail locations
- **Reduced workload**: Automatic image sourcing

### For Users
- **Rich visual experience**: Multiple images per trail
- **Location context**: Images show actual trail features
- **Consistent quality**: Professional photography from Google Places

### For Platform
- **Scalability**: Automatic image sourcing for new trails
- **Performance**: Smart caching reduces API calls
- **Reliability**: Google Places provides reliable, location-specific images

## Error Handling

- **API failures**: Graceful fallback to default image
- **Missing keys**: Service continues with organization images only
- **Rate limits**: Caching prevents excessive API calls
- **Invalid images**: Automatic filtering of broken links

## Future Enhancements

- **AI-powered selection**: Better image relevance scoring
- **Seasonal images**: Match images to current season
- **User preferences**: Allow users to choose image sources
- **Batch processing**: Pre-fetch images for popular trails

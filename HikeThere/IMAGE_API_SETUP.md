# Image API Setup Guide

This guide explains how to set up the image APIs for automatic trail image fetching in HikeThere.

## Available Image APIs

### 1. Unsplash API (Recommended)
- **Website**: https://unsplash.com/developers
- **Free Tier**: 50 requests per hour
- **Quality**: High-quality, curated nature/hiking photos
- **Setup**:
  1. Create an account at https://unsplash.com/developers
  2. Create a new application
  3. Copy your Access Key
  4. Add to `.env`: `UNSPLASH_ACCESS_KEY=your_key_here`

### 2. Pexels API
- **Website**: https://www.pexels.com/api/
- **Free Tier**: 200 requests per hour
- **Quality**: Good outdoor and nature photography
- **Setup**:
  1. Go to https://www.pexels.com/api/
  2. Sign up for an account
  3. Get your API key
  4. Add to `.env`: `PEXELS_API_KEY=your_key_here`

### 3. Pixabay API
- **Website**: https://pixabay.com/api/docs/
- **Free Tier**: 5000 requests per hour
- **Quality**: Good variety, user-generated content
- **Setup**:
  1. Visit https://pixabay.com/api/docs/
  2. Get your API key
  3. Add to `.env`: `PIXABAY_API_KEY=your_key_here`

## Environment Configuration

Add these lines to your `.env` file:

```env
# Image APIs
UNSPLASH_ACCESS_KEY=your_unsplash_access_key
PEXELS_API_KEY=your_pexels_api_key
PIXABAY_API_KEY=your_pixabay_api_key
```

## How It Works

1. **Priority System**: The system tries APIs in order: Unsplash → Pexels → Pixabay → Fallback
2. **Smart Keywords**: Automatically generates search terms based on trail information
3. **Caching**: Images are cached for 1 hour to reduce API calls
4. **Fallback**: Uses default images if all APIs fail

## Image Types

- **Primary Images**: Main trail photos (hiking, nature, mountains)
- **Map Images**: Trail maps and topographic views
- **Secondary Images**: Additional trail photos

## Usage Examples

```php
// In your controller
use App\Services\TrailImageService;

public function show(Trail $trail)
{
    $imageService = app(TrailImageService::class);
    
    $primaryImage = $imageService->getTrailImage($trail, 'primary', 'large');
    $mapImage = $imageService->getTrailImage($trail, 'map', 'medium');
    $allImages = $imageService->getTrailImages($trail, 5);
}
```

## Rate Limiting

- **Unsplash**: 50 requests/hour (free)
- **Pexels**: 200 requests/hour (free)
- **Pixabay**: 5000 requests/hour (free)

## Troubleshooting

### Common Issues

1. **No Images Loading**
   - Check API keys are correct
   - Verify API quotas haven't been exceeded
   - Check network connectivity

2. **Poor Image Quality**
   - Images are fetched based on trail keywords
   - Try adjusting trail names/locations for better results

3. **API Errors**
   - Check Laravel logs for specific error messages
   - Verify API endpoints are accessible

### Debug Mode

Enable debug logging by adding to your `.env`:

```env
LOG_LEVEL=debug
```

## Best Practices

1. **Start with Unsplash**: Best quality for nature photos
2. **Use Multiple APIs**: Provides fallback options
3. **Monitor Usage**: Keep track of API quotas
4. **Cache Wisely**: Images are cached for 1 hour by default
5. **Fallback Images**: Always have default images ready

## Cost Considerations

- **Free Tiers**: Sufficient for development and small projects
- **Paid Plans**: Available for higher usage needs
- **Caching**: Reduces API calls and costs

## Support

For issues with specific APIs:
- **Unsplash**: https://help.unsplash.com/
- **Pexels**: https://www.pexels.com/support/
- **Pixabay**: https://pixabay.com/service/contact/

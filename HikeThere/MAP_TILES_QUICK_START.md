# 🚀 Quick Start: Google Map Tiles API 3D Integration

## Step 1: Enable APIs in Google Cloud Console

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Select your project (or create a new one)
3. Navigate to **APIs & Services** > **Library**
4. Enable these APIs:
   - ✅ Maps JavaScript API (already enabled)
   - 🆕 **Map Tiles API**
   - 🆕 **Photorealistic 3D Tiles**
   - 🆕 **Street View Static API** (optional)

## Step 2: Create a Map ID

1. Go to [Google Maps Platform](https://console.cloud.google.com/google/maps-apis/studio/maps)
2. Click **Create Map ID**
3. Configuration:
   - **Map Name:** HikeThere 3D Trails
   - **Map Type:** JavaScript
   - **Map Style:** Enable 3D
   - **Restrict to these referrers:** (add your domain)
4. Copy the **Map ID** (looks like: `a1b2c3d4e5f6g7h8`)

## Step 3: Update .env File

Add these lines to your `.env` file:

```env
# Google Maps 3D Configuration
GOOGLE_MAPS_API_KEY=AIzaSyAARIjCa3K7Q7a0ruls5HfXB4_pX6hEAgA
GOOGLE_MAPS_3D_ENABLED=true
GOOGLE_MAPS_3D_MAP_ID=YOUR_MAP_ID_HERE  # Replace with actual Map ID
```

## Step 4: Update config/services.php

Add this to `config/services.php`:

```php
'google' => [
    'maps_api_key' => env('GOOGLE_MAPS_API_KEY'),
    'maps_3d_enabled' => env('GOOGLE_MAPS_3D_ENABLED', false),
    'maps_3d_id' => env('GOOGLE_MAPS_3D_MAP_ID'),
],
```

## Step 5: Load 3D Map Script

Update your layout file to load Google Maps with Map ID:

```html
<!-- In your layout file (e.g., app.blade.php) -->
<script async defer 
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&map_ids={{ config('services.google.maps_3d_id') }}&libraries=places,geometry&callback=initGoogleMaps">
</script>

<!-- Load our 3D map class -->
@vite(['resources/js/trail-3d-map.js'])
```

## Step 6: Use the 3D Preview Component

### In Trail Listing (Thumbnail Mode):
```blade
@foreach($trails as $trail)
    <x-trail-3d-preview 
        :trail="$trail" 
        mode="thumbnail"
    />
@endforeach
```

### In Trail Detail Page (Full Viewer):
```blade
<x-trail-3d-preview 
    :trail="$trail" 
    mode="full"
    :autoTour="true"
    :showControls="true"
/>
```

## Step 7: Test the Integration

1. **Start your dev server:**
   ```bash
   php artisan serve
   ```

2. **Navigate to trails page**
   ```
   http://127.0.0.1:8000/org/trails
   ```

3. **Check browser console** for:
   - "Trail3DMap class loaded successfully"
   - "Trail3DMap initialized"
   - "3D map created successfully"

4. **Test 3D features:**
   - ✅ Click "3D View" button
   - ✅ Use "Tour" for auto-rotation
   - ✅ Drag to rotate camera
   - ✅ Scroll to zoom
   - ✅ Tilt controls work

## Troubleshooting

### ❌ "Map ID is invalid"
**Solution:** Make sure you created a Map ID in Google Cloud Console and added it to `.env`

### ❌ "This API project is not authorized"
**Solution:** Enable Map Tiles API in Google Cloud Console

### ❌ "WebGL not supported"
**Solution:** The component will show a fallback message. User needs a modern browser.

### ❌ Map shows but no 3D
**Solution:** 
1. Check that map_ids parameter is in the script URL
2. Verify Map ID is correct
3. Make sure "3D" is enabled in Map ID settings

### ❌ "Google Maps API failed to load"
**Solution:** Check that your API key is valid and has no restrictions blocking localhost

## Performance Tips

1. **Lazy Load 3D Views:**
   - Only initialize 3D maps when they come into viewport
   - Use Intersection Observer API

2. **Cache Tiles:**
   - Browser automatically caches 3D tiles
   - Consider service worker for offline support

3. **Optimize for Mobile:**
   - Reduce initial tilt on mobile
   - Disable auto-tour on mobile by default
   - Use lower quality on slow connections

## Cost Monitoring

Monitor your usage in Google Cloud Console:
- **Dashboard** > **APIs & Services** > **Metrics**
- Set up billing alerts at $10, $20, $50
- Typical cost: ~$15-25/month for 1000 trail views

## Next Steps

- [ ] Enable APIs in Google Cloud
- [ ] Create Map ID
- [ ] Update .env file
- [ ] Test on localhost
- [ ] Deploy to staging
- [ ] Monitor costs
- [ ] Optimize performance

## Support Resources

- [Map Tiles API Docs](https://developers.google.com/maps/documentation/tile)
- [3D Tiles Guide](https://developers.google.com/maps/documentation/tile/3d-tiles)
- [Map IDs Documentation](https://developers.google.com/maps/documentation/get-map-id)

---

**Ready to go!** Once you complete these steps, your trails will have stunning 3D visualizations! 🎉

# OpenStreetMap Quick Test

## Test the Integration

1. **Open your HikeThere application**: http://127.0.0.1:8001

2. **Go to Trail Creation** (as Organization user)

3. **Fill in these test values:**
   - **Mountain Name**: Mount Pulag
   - **Trail Name**: Ambangeg Trail
   - **Location**: Benguet, Philippines

4. **Click the green "OpenStreetMap Route" button**

5. **Expected Results:**
   - ✅ Distance: ~14.6km (not 0.61km)
   - ✅ Source: "OpenStreetMap enhanced" or "Verified trail database"
   - ✅ Coordinates: Should have hundreds of points
   - ✅ Accuracy: "Verified" or "High"

## What You Should See

### ✅ **Success Indicators:**
- Green success message with coordinate count
- Distance field shows accurate measurement
- Source indicator shows OSM or enhanced data
- Coordinate status shows "success"

### 🔄 **Fallback Behavior:**
If OpenStreetMap doesn't have data:
1. "OpenStreetMap data not found, using alternative method..."
2. Falls back to known trail database
3. Ultimate fallback to Google Maps
4. Never fails completely

### 📊 **Console Output:**
Check browser developer tools console for:
```
OpenStreetMap coordinates: {
  coordinates: [...],
  source: "openstreetmap_enhanced",
  accuracy: "verified",
  distance: "14.6 km"
}
```

## Troubleshooting

### **If OSM Button Does Nothing:**
- Check browser console for JavaScript errors
- Verify route exists: `/org/trails/generate-osm-coordinates`
- Check Laravel logs for API errors

### **If Slow Response:**
- Normal for first request (API wakeup)
- Built-in 1-second delay is working
- Subsequent requests should be faster

### **If Always Falls Back to Google:**
- OSM might not have data for that specific trail
- System is working correctly with fallbacks
- Try different trail names or popular mountains

## API Status Check

### **Test Nominatim (Geocoding):**
Open in browser: https://nominatim.openstreetmap.org/search?q=Mount+Pulag+Benguet+Philippines&format=json

Should return location data with bounding box.

### **Test Overpass (Trail Data):**
Open: https://overpass-api.de/api/interpreter

Query example:
```
[out:json][timeout:30];
way["highway"~"^(path|track|footway)$"](16.4,120.7,16.8,121.1);
out geom;
```

Should return trail data in the Mount Pulag area.

## Configuration Verification

Your `.env` should have:
```
OSM_ENABLED=true
OSM_REQUEST_DELAY=1
OSM_USER_AGENT="HikeThere/1.0 (Trail Mapping Application; your-email@example.com)"
```

## Success Criteria

✅ **Working Correctly If:**
- Ambangeg Trail shows ~14.6km distance
- System uses OpenStreetMap data when available
- Fallbacks work smoothly when OSM data unavailable
- No API errors in logs
- User gets feedback about data source

You're all set! The OpenStreetMap integration is working if you see accurate trail distances and source indicators. 🎉

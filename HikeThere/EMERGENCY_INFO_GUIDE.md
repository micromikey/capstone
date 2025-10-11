# Emergency Information Configuration Guide

This guide explains where the Emergency Information data comes from and how to modify it.

## Data Sources Overview

The Emergency Information section displays 4 types of data:

### 1. **Emergency Numbers** (Static/Hardcoded)
**Location:** `app/Services/EmergencyInfoService.php` → `getPhilippinesEmergencyNumbers()` method (line 220-227)

```php
protected function getPhilippinesEmergencyNumbers()
{
    return [
        ['service' => 'National Emergency Hotline', 'number' => '911'],
        ['service' => 'Philippine Red Cross', 'number' => '143'],
        ['service' => 'NDRRMC Hotline', 'number' => '(02) 8911-1406'],
        ['service' => 'Coast Guard', 'number' => '(02) 8527-8481'],
    ];
}
```

**How to modify:**
- Add more emergency services to the array
- Update phone numbers
- Change service names
- Example: Add `['service' => 'Fire Department', 'number' => '117']`

---

### 2. **Nearest Hospitals** (Dynamic - Google Places API)
**Location:** `app/Services/EmergencyInfoService.php` → `findNearbyHospitals()` method (line 67-99)

**How it works:**
- Uses **Google Places API** with `type=hospital`
- Searches within **50km radius** of trail coordinates
- Returns top **3 closest hospitals**
- Includes: name, address, coordinates, distance

**Why it might be empty:**
- No hospitals found within 50km (remote trails)
- Google Places API error/quota exceeded
- Trail coordinates missing

**How to modify:**
```php
// Change search radius (default: 50000 meters = 50km)
protected function findNearbyHospitals($lat, $lng, $radius = 100000) // 100km

// Change number of results (default: 3)
foreach (array_slice($data['results'] ?? [], 0, 5) as $place) // Show 5 hospitals
```

**Fallback Data:** If API fails, uses `getDefaultHospitals()` (line 258-266)

---

### 3. **Ranger Stations** (Dynamic - Google Places API)
**Location:** `app/Services/EmergencyInfoService.php` → `findRangerStations()` method (line 101-133)

**How it works:**
- Searches Google Places for: `park`, `tourist_attraction`, `point_of_interest`
- Filters by keywords: "ranger", "station", "office", "headquarters", "visitor center"
- Returns top **2 matches**

**Why you see "Trail Management Office":**
- This is the **fallback/default** when no ranger stations found
- Located in `ranger_stations` array from `getDefaultEmergencyInfo()`

**How to modify:**
```php
// Add more search keywords
if (stripos($name, 'ranger') !== false || 
    stripos($name, 'station') !== false ||
    stripos($name, 'park office') !== false ||  // ADD THIS
    stripos($name, 'trail office') !== false) { // ADD THIS

// Change number of results
foreach ($candidates as $station) {
    if (count($stations) >= 3) break; // Show 3 instead of 2
```

---

### 4. **Evacuation Points** (Auto-Generated Based on Trail)
**Location:** `app/Services/EmergencyInfoService.php` → `generateEvacuationPoints()` method (line 175-212)

**How it works:**
- **Automatically generates** based on trail characteristics
- Uses trail name and description to determine points

**Default evacuation points:**
1. **Trailhead / Base Camp** - "Primary evacuation point"
2. **Camp 1** - "Mid-trail evacuation point" (if multi-day)
3. **Summit Area** - "Emergency shelter if descent not possible"

**How to modify:**
```php
protected function generateEvacuationPoints($trail)
{
    $points = [];
    
    // Always add trailhead
    $points[] = ['name' => 'Trailhead / Base Camp', 'description' => 'Primary evacuation point'];
    
    // Add your custom points
    $points[] = ['name' => 'Emergency Shelter A', 'description' => 'Located at kilometer 5'];
    $points[] = ['name' => 'Helipad Zone', 'description' => 'Emergency helicopter landing area'];
    
    // Add camp if multi-day
    if ($this->isMultiDayTrail($trail)) {
        $points[] = ['name' => 'Camp 1', 'description' => 'Mid-trail evacuation point'];
        $points[] = ['name' => 'Camp 2', 'description' => 'High-altitude evacuation point'];
    }
    
    return $points;
}
```

---

## How to Add Custom Emergency Data Per Trail

### Option 1: Store in Database (Recommended)
Add custom emergency info to the `trails` table's `emergency_info` JSON column:

```php
// In your trail seeder or admin panel
Trail::create([
    'name' => 'Mt. Pulag',
    'emergency_info' => [
        'hospitals' => [
            ['name' => 'Benguet General Hospital', 'address' => 'La Trinidad, Benguet'],
        ],
        'ranger_stations' => [
            ['name' => 'Mt. Pulag Ranger Station', 'address' => 'Babadak, Kabayan'],
        ],
        'evacuation_points' => [
            ['name' => 'Camp 1', 'description' => 'First evacuation point at 2,500m'],
            ['name' => 'Camp 2', 'description' => 'Second evacuation point at 2,800m'],
        ],
        'emergency_numbers' => [
            ['service' => 'Local Ranger', 'number' => '0917-XXX-XXXX'],
        ],
    ],
]);
```

When `emergency_info` exists in the trail record, it will be used instead of fetching from Google API.

### Option 2: Modify Default Fallback
Edit the fallback data used when API fails:

```php
// In EmergencyInfoService.php → getDefaultEmergencyInfo() (line 256)
protected function getDefaultEmergencyInfo()
{
    return [
        'hospitals' => [
            ['name' => 'Provincial Hospital', 'address' => 'Contact local authorities'],
            ['name' => 'Rural Health Unit', 'address' => 'Nearest town center'],
        ],
        'ranger_stations' => [
            ['name' => 'Trail Management Office', 'address' => 'Contact your tour organizer'],
            ['name' => 'DENR Office', 'address' => 'Provincial office'],
        ],
        'evacuation_points' => $this->generateEvacuationPoints(null),
        'emergency_numbers' => $this->getPhilippinesEmergencyNumbers(),
    ];
}
```

---

## Configuration Files

### Google Maps API Key
**Location:** `.env` file
```env
GOOGLE_MAPS_API_KEY=your_api_key_here
```

**Services config:** `config/services.php`
```php
'google_maps' => [
    'key' => env('GOOGLE_MAPS_API_KEY'),
],
```

**Required APIs:**
- Google Places API (for hospitals, ranger stations)
- Must be enabled in Google Cloud Console

---

## Display Component

**Location:** `resources/views/components/itinerary/emergency-info.blade.php`

This Blade component receives the emergency data and displays it in the 4-column layout you see.

**How to modify the display:**
```blade
{{-- Add a new section --}}
<div class="bg-white/60 backdrop-blur-sm p-6 rounded-xl border border-yellow-200/50">
    <div class="flex items-center gap-3 mb-4">
        <div class="bg-purple-600 p-2.5 rounded-lg">
            <svg class="w-5 h-5 text-white">...</svg>
        </div>
        <h4 class="text-sm font-bold text-purple-900 uppercase tracking-wider">Weather Stations</h4>
    </div>
    
    @if(!empty($emergencyInfo['weather_stations']))
        @foreach($emergencyInfo['weather_stations'] as $station)
            <div class="text-sm text-slate-700 mb-2">
                <div class="font-semibold">{{ $station['name'] }}</div>
                <div class="text-xs text-slate-600">{{ $station['contact'] }}</div>
            </div>
        @endforeach
    @endif
</div>
```

---

## Testing

To test emergency info generation:
```bash
# In tinker
php artisan tinker

$trail = Trail::first();
$service = app(\App\Services\EmergencyInfoService::class);
$info = $service->getEmergencyInfo($trail);
dd($info);
```

---

## Summary

| Data Type | Source | Modifiable In |
|-----------|--------|---------------|
| Emergency Numbers | Hardcoded | `EmergencyInfoService.php` line 220 |
| Hospitals | Google Places API | `EmergencyInfoService.php` line 67 |
| Ranger Stations | Google Places API | `EmergencyInfoService.php` line 101 |
| Evacuation Points | Auto-generated | `EmergencyInfoService.php` line 175 |
| Per-Trail Custom | Database | `trails.emergency_info` JSON column |
| Fallback Data | Hardcoded | `EmergencyInfoService.php` line 256 |

**Best Practice:** Store custom emergency info in the `emergency_info` JSON column of each trail for accurate, trail-specific data.

# OpenStreetMap Setup Guide for HikeThere

## 🎉 **No API Key Required!**

**Great news:** OpenStreetMap does NOT require any API keys or registration! The integration is completely free to use.

## 📋 **Setup Steps**

### Step 1: ✅ **Already Done**
The OpenStreetMap integration is already implemented and configured in your HikeThere application. No additional setup required!

### Step 2: 🔧 **Configuration (Optional)**
You can customize the settings in your `.env` file:

```bash
# OpenStreetMap Configuration (No API key required!)
OSM_ENABLED=true
OSM_REQUEST_DELAY=1
OSM_USER_AGENT="HikeThere/1.0 (Trail Mapping Application; your-email@example.com)"
OSM_RATE_LIMIT_REQUESTS_PER_MINUTE=60

# Philippines Bounding Box (for default searches)
OSM_PHILIPPINES_BBOX_SOUTH=4.5
OSM_PHILIPPINES_BBOX_NORTH=21.0
OSM_PHILIPPINES_BBOX_WEST=116.0
OSM_PHILIPPINES_BBOX_EAST=127.0
```

### Step 3: 🚀 **Ready to Use**
Just use the green "OpenStreetMap Route" button in the trail creation form!

## 📍 **What is BBOX (Bounding Box)?**

A **bounding box (bbox)** is a rectangular area defined by coordinates that limits where to search for trails. Think of it as drawing a rectangle on a map to say "only search for trails inside this area."

### 🗺️ **BBOX Format:**
```
[south, west, north, east]
```

### 🇵🇭 **Philippines Bounding Box:**
```
South: 4.5°  (Southernmost point - Tawi-Tawi)
North: 21.0° (Northernmost point - Batanes)
West: 116.0° (Westernmost point - Palawan)  
East: 127.0° (Easternmost point - Davao Oriental)
```

### 🏔️ **Example - Mount Pulag Area:**
```
South: 16.5° (South of Benguet)
North: 17.0° (North of Benguet)
West: 120.8° (West of Mount Pulag)
East: 121.0° (East of Mount Pulag)
```

### 🎯 **How BBOX is Used:**
1. **Location Search**: System finds the mountain/location coordinates
2. **BBOX Generation**: Creates a search area around that location
3. **Trail Search**: Looks for hiking trails only within that bbox
4. **Result Filtering**: Returns trails from the specific area

## 🔄 **How the System Works**

### 1. **User Input**
```
Trail Name: "Ambangeg Trail"
Mountain: "Mount Pulag"  
Location: "Benguet, Philippines"
```

### 2. **Bbox Generation**
```
System finds Mount Pulag coordinates: (16.5926°N, 120.8907°E)
Creates bbox around it: [16.4, 120.7, 16.8, 121.1]
```

### 3. **Overpass Query**
```
[out:json][timeout:60];
(
  way["highway"~"^(path|track|footway)$"](16.4,120.7,16.8,121.1);
  way["sac_scale"](16.4,120.7,16.8,121.1);
  way["name"~"Ambangeg",i](16.4,120.7,16.8,121.1);
);
out geom;
```

### 4. **Result Processing**
- Extracts trail coordinates
- Calculates accurate distances
- Optimizes path geometry
- Returns enhanced trail data

## 🌐 **API Endpoints Used**

### **Nominatim API (Geocoding)**
- **URL**: `https://nominatim.openstreetmap.org/search`
- **Purpose**: Convert place names to coordinates and bounding boxes
- **Cost**: FREE, no API key required
- **Rate Limit**: ~1 request per second (be respectful)

### **Overpass API (Trail Data)**
- **URL**: `https://overpass-api.de/api/interpreter`
- **Purpose**: Query OpenStreetMap database for trail data
- **Cost**: FREE, no API key required  
- **Rate Limit**: Reasonable usage (we add 1-second delays)

## ⚡ **Rate Limiting & Best Practices**

### **Current Implementation:**
- ✅ **1-second delay** between requests
- ✅ **Proper User-Agent** header identifying HikeThere
- ✅ **Timeout protection** (30-60 seconds)
- ✅ **Error handling** with fallbacks
- ✅ **Respectful usage** patterns

### **Rate Limits:**
- **Nominatim**: ~1 request per second recommended
- **Overpass**: No strict limit, but don't abuse
- **Our Setting**: 60 requests per minute maximum

## 🆚 **OpenStreetMap vs Google Maps**

| Feature | OpenStreetMap | Google Maps |
|---------|---------------|-------------|
| **API Key** | ❌ None needed | ✅ Required |
| **Cost** | 🆓 Completely free | 💰 Pay per request |
| **Trail Data** | 🥾 Hiking-specific paths | 🚗 Road-focused routing |
| **Accuracy** | 🎯 Community-verified trails | 📍 Good for roads |
| **Rate Limits** | 😊 Generous free usage | 💳 Based on billing |
| **Coverage** | 🌍 Global community data | 🌍 Google's data |

## 🛠️ **Troubleshooting**

### **No Trail Data Found**
- Check if the trail exists in OpenStreetMap
- Try broader location searches
- System will fallback to Google Maps automatically

### **Slow Response**
- Normal for first requests (API wakeup)
- Subsequent requests are faster
- Fallback systems ensure no complete failures

### **Rate Limiting**
- Built-in delays prevent issues
- System respects API guidelines
- No action needed from you

## 🎯 **Usage Examples**

### **For Popular Trails:**
1. Enter: "Ambangeg Trail", "Mount Pulag", "Benguet"
2. Click "OpenStreetMap Route"
3. Get: Real hiking path with 14.6km distance ✅

### **For New Trails:**
1. If OSM has data: Get accurate trail geometry
2. If no OSM data: System uses known trail database
3. Ultimate fallback: Enhanced Google Maps routing

## 🔍 **Testing Your Setup**

1. **Go to**: Trail creation page in HikeThere
2. **Fill in**: Mount Pulag, Ambangeg Trail, Benguet
3. **Click**: Green "OpenStreetMap Route" button  
4. **Expect**: ~14.6km distance with "OpenStreetMap enhanced" indicator

## 🚀 **You're All Set!**

Your OpenStreetMap integration is ready to use immediately. No API keys, no registration, no additional setup required. Just click the green button and get more accurate trail data! 🏔️✨

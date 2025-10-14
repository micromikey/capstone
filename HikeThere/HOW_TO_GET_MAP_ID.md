# 🗺️ How to Get Your Google Maps 3D Map ID

## Step-by-Step Guide with Screenshots Instructions

### Method 1: Google Maps Platform (Recommended)

1. **Go to Google Cloud Console**
   - Visit: https://console.cloud.google.com/
   - Sign in with your Google account
   - Select your project (or create one if you don't have one)

2. **Navigate to Google Maps Platform**
   - In the left sidebar, find "Google Maps Platform"
   - OR search for "Maps Platform" in the top search bar

3. **Go to Map Management**
   - Click on **"Map Management"** in the left menu
   - OR directly visit: https://console.cloud.google.com/google/maps-apis/studio/maps

4. **Create a New Map ID**
   - Click the **"+ CREATE MAP ID"** button (blue button at top)
   
5. **Configure Your Map**
   Fill in these details:
   ```
   Map Name: HikeThere 3D Trails
   Map Type: JavaScript
   Description: 3D trail visualization for HikeThere hiking platform
   ```

6. **Enable 3D (IMPORTANT!)**
   - Scroll down to **"3D"** section
   - Toggle **"Enable 3D"** to ON (it should be blue/green)
   - This is CRITICAL for photorealistic 3D tiles to work!

7. **Save and Copy Map ID**
   - Click **"Save"** button
   - Your Map ID will be displayed (format: `a1b2c3d4e5f6g7h8`)
   - Click the **copy icon** next to it
   - It looks like: `4a5b6c7d8e9f0g1h` (16 characters, alphanumeric)

8. **Add to .env File**
   ```env
   GOOGLE_MAPS_3D_MAP_ID=4a5b6c7d8e9f0g1h
   ```

---

## Method 2: Direct Link

**Fastest way:**

1. Go directly to: https://console.cloud.google.com/google/maps-apis/studio/maps

2. Make sure you're in the correct project (check dropdown at top)

3. Follow steps 4-8 from Method 1 above

---

## Visual Guide - What to Look For

### **Navigation Path:**
```
Google Cloud Console
  └── Navigation Menu (☰)
      └── Google Maps Platform
          └── Map Management
              └── [+ CREATE MAP ID button]
```

### **Page Title You Should See:**
```
Map Management
Manage map IDs for your Maps JavaScript API implementations
```

### **Map ID Format:**
- Length: Usually 16 characters
- Format: Alphanumeric (letters and numbers)
- Example: `4a5b6c7d8e9f0g1h`
- Example: `DEMO_MAP_ID` (for testing, but use your own!)

---

## Important Settings When Creating Map ID

| Setting | Value | Why |
|---------|-------|-----|
| **Map Name** | HikeThere 3D Trails | Easy to identify |
| **Map Type** | JavaScript | We're using JS API |
| **Enable 3D** | ✅ ON | REQUIRED for 3D tiles! |
| **Platform** | Web | For web application |

---

## Verification Steps

After creating your Map ID:

1. **Check if 3D is enabled:**
   - Go back to Map Management
   - Click on your map name
   - Verify "3D" toggle is ON (green/blue)

2. **Test the Map ID:**
   - Add it to your .env file
   - Load your application
   - Check browser console for errors

---

## Troubleshooting

### ❌ "I don't see Map Management"

**Solution:**
1. Make sure you're in the correct Google Cloud project
2. Enable Maps JavaScript API first:
   - Go to APIs & Services → Library
   - Search for "Maps JavaScript API"
   - Click Enable

### ❌ "I can't find the + CREATE MAP ID button"

**Solution:**
- You might be on the old console
- Use this direct link: https://console.cloud.google.com/google/maps-apis/studio/maps
- Click the dropdown at top to switch to the new console

### ❌ "3D option is grayed out"

**Solution:**
- Enable "Map Tiles API" first:
  1. Go to APIs & Services → Library
  2. Search for "Map Tiles API"
  3. Click Enable
  4. Return to Map Management and try again

### ❌ "Map ID doesn't work in my app"

**Checklist:**
- [ ] 3D is enabled on the Map ID
- [ ] Map Tiles API is enabled
- [ ] Photorealistic 3D Tiles API is enabled
- [ ] API key has no domain restrictions (for testing)
- [ ] Map ID is correctly copied to .env

---

## Quick Reference Card

```
┌─────────────────────────────────────────────┐
│  HOW TO GET YOUR GOOGLE MAPS 3D MAP ID     │
├─────────────────────────────────────────────┤
│                                             │
│  1. https://console.cloud.google.com        │
│                                             │
│  2. Google Maps Platform → Map Management   │
│                                             │
│  3. Click "+ CREATE MAP ID"                 │
│                                             │
│  4. Name: HikeThere 3D Trails              │
│     Type: JavaScript                        │
│     Enable 3D: ✅ ON                        │
│                                             │
│  5. Click Save                              │
│                                             │
│  6. Copy the Map ID (16 chars)             │
│                                             │
│  7. Paste in .env:                         │
│     GOOGLE_MAPS_3D_MAP_ID=your_id_here     │
│                                             │
└─────────────────────────────────────────────┘
```

---

## Alternative: Use Existing Map ID

If you already have a Map ID but need to enable 3D:

1. Go to Map Management
2. Click on your existing map name
3. Scroll to "3D" section
4. Toggle **"Enable 3D"** to ON
5. Click **"Save"**
6. Use that Map ID in your .env

---

## Required APIs to Enable

Before creating Map ID, make sure these are enabled:

1. **Maps JavaScript API** ✅
2. **Map Tiles API** 🆕
3. **Photorealistic 3D Tiles API** 🆕

**Enable them at:**
https://console.cloud.google.com/apis/library

Search for each API name and click "Enable"

---

## Video Tutorial Alternative

If you prefer a video walkthrough:

1. Go to YouTube
2. Search: "Google Maps Platform create Map ID"
3. Or: "Google Maps 3D tiles Map ID"

Google also has official docs at:
https://developers.google.com/maps/documentation/get-map-id

---

## After Getting Your Map ID

1. **Add to .env:**
   ```env
   GOOGLE_MAPS_3D_MAP_ID=your_actual_map_id_here
   ```

2. **Restart your server:**
   ```bash
   php artisan serve
   ```

3. **Test it:**
   - Visit your trail pages
   - Check browser console
   - Verify 3D tiles load

---

## Need Help?

If you're still stuck:

1. **Check your current project:**
   - Look at the project name in the top bar
   - Make sure you're in the right project

2. **Verify billing is enabled:**
   - Go to Billing in left menu
   - Some APIs require billing (but free tier is generous)

3. **Check API restrictions:**
   - Go to Credentials
   - Click your API key
   - Temporarily remove restrictions for testing

---

## Your Current API Key

You already have:
```
GOOGLE_MAPS_API_KEY=AIzaSyAARIjCa3K7Q7a0ruls5HfXB4_pX6hEAgA
```

You just need to add the Map ID below it:
```
GOOGLE_MAPS_3D_MAP_ID=your_map_id_from_console
```

Both work together! 🎉

---

**Pro Tip:** You can create multiple Map IDs for different purposes (dev, staging, production) with different styles and settings!

**Still having issues?** Let me know and I'll help troubleshoot! 🚀

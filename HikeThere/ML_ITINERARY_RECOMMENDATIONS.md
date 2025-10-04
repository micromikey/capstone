# ML-Powered Itinerary Builder Recommendations

**Date:** October 5, 2025  
**Feature:** Personalized Trail Suggestions in Itinerary Builder

---

## Overview

The itinerary builder now uses **machine learning recommendations** to suggest trails based on the user's complete activity history, not just their assessment score.

### What Changed

Previously, trail suggestions were **only** based on:
- ✅ Assessment score (fitness level)
- ✅ Difficulty level matching

Now, trail suggestions are based on:
- ✅ **Booking history** - Trails user has booked before
- ✅ **Itinerary history** - Trails in saved/generated itineraries
- ✅ **Review history** - Trails user has reviewed/rated
- ✅ **Assessment data** - Fitness level and experience
- ✅ **Viewed organizations** - Organizations user has interacted with
- ✅ **User preferences** - Hiking preferences and tags

---

## Implementation Details

### Backend Changes

#### 1. **ItineraryController.php** - New ML Recommendation Method

```php
protected function getMLRecommendations($userId, $k = 10)
```

**What it does:**
- Calls the ML recommender API (`/api/recommender/user/{userId}`)
- Requests top 10 recommendations, returns top 5 for display
- Handles both ML-powered and database fallback responses
- Returns trail data with scores and explanations

**Error Handling:**
- 5-second timeout to prevent blocking
- Graceful fallback to empty collection on failure
- Logs warnings and errors for debugging

#### 2. **Updated Controller Methods**

Both `build()` and `buildWithTrail()` now:
1. Fetch ML recommendations for the authenticated user
2. Pass `$recommendedTrails` to the view
3. Include recommendation scores and explanations

### Frontend Changes

#### 1. **Enhanced "Suggested Trails" Section**

**Location:** `resources/views/hiker/itinerary/build.blade.php`

**New Features:**
- **Title changed** from "Suggested Trail" to "Suggested Trails" (plural)
- **Subtitle** emphasizes personalization: "Personalized for you based on your activity"
- **Description** mentions multiple factors: "booking history, reviews, saved itineraries, and fitness level"

#### 2. **ML Recommendations Display**

**Visual Elements:**
- ✨ **Gradient background** (emerald to cyan) for recommended trails
- ⭐ **Match score badge** showing recommendation confidence (0-100%)
- 🏔️ **Trail information** including mountain name, rating, location
- 💡 **"Why this trail?" explanation** (expandable details)
- 🎯 **"ML Powered" label** to indicate intelligent recommendations

**Interactive Features:**
- **Click to select** - Clicking a recommendation automatically selects it in the dropdown
- **Visual feedback** - Selected trail gets a green ring highlight
- **Smooth scrolling** - Page scrolls to the trail selector
- **Success notification** - Toast notification confirms selection

#### 3. **JavaScript Functions**

```javascript
function selectRecommendedTrail(trailId, trailName)
```
- Finds matching trail in dropdown by ID or name
- Selects the trail and triggers change events
- Adds visual highlight and success notification

```javascript
function showNotification(message, type)
```
- Shows toast notifications for user feedback
- Auto-dismisses after 3 seconds
- Color-coded by type (success, error, info)

---

## User Experience Flow

### Step-by-Step Usage

1. **User navigates to Itinerary Builder**
   ```
   /hiker/itinerary/build
   ```
   (Note: The route is `/hiker/itinerary/build`, not `/itinerary/build`)

2. **System loads ML recommendations**
   - Backend calls `/api/recommender/user/{userId}?k=10`
   - ML service (or DB fallback) returns personalized trails
   - Top 5 recommendations displayed in "Suggested Trails" section

3. **User sees personalized suggestions**
   - Each trail shows:
     - Trail name and location
     - Match score (e.g., "87%")
     - Rating and reviews count
     - "Why this trail?" explanation
   - Trails ordered by recommendation score (highest first)

4. **User clicks a recommended trail**
   - Trail automatically selected in dropdown
   - Visual highlight appears
   - "Trail selected: [name]" notification shown
   - Trail overview and map updates

5. **User continues building itinerary**
   - Selected trail pre-filled in form
   - User adds dates, activities, etc.
   - Generates personalized itinerary

---

## ML Recommendation Algorithm

### How It Works

The recommender system uses **content-based filtering** with multiple data sources:

#### 1. **User Profile Building**
```php
$userProfile = [
    'preferred_difficulty' => $difficulty,
    'preferred_tags' => $tags,
    'location' => $userLocation,
    'liked_trail_ids' => $reviewedTrailIds,
    'hiking_preferences' => $hikingPrefs,
    'user_preferences' => $dbPrefs,
];
```

#### 2. **Data Sources**
- **Booking History**: Trails from `bookings` table → liked trails
- **Review History**: Trails from `trail_reviews` table → liked trails
- **Itinerary History**: Trails from saved `itineraries` → preferences
- **Assessment Data**: `assessment_results` → difficulty preference
- **User Preferences**: `user_preferences` → tags, features, location
- **Hiking Preferences**: `hiking_preferences` → difficulty, distance, etc.

#### 3. **Scoring**
- **ML Service** (when available):
  - TF-IDF vectorization of trail features
  - Cosine similarity between user profile and trails
  - Returns scores (0.0 - 1.0) and explanations
  
- **Database Fallback** (when ML unavailable):
  - Orders by reviews count and elevation gain
  - Filters by user's preferred difficulty
  - Returns popular matching trails

#### 4. **Ranking**
- Trails sorted by recommendation score (highest first)
- Top 5 displayed in UI
- Scores converted to percentages for display

---

## Visual Design

### Recommended Trail Card Layout

```
┌─────────────────────────────────────────────┐
│  [Trail Name]                    [★ 87%]   │
│  🏔️ Mountain Name   ⭐ 4.5               │
│  📍 Location, Province                      │
│  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━   │
│  Why this trail? ▼                          │
│  Based on your previous visits to           │
│  similar trails and high rating history.    │
└─────────────────────────────────────────────┘
```

### Color Scheme

- **Background**: Gradient from `emerald-50` to `cyan-50`
- **Border**: `emerald-200` 
- **Badge**: `emerald-600` (white text)
- **Hover**: Elevated shadow (`shadow-md`)
- **Text**: Gray scale for hierarchy

---

## Benefits

### For Users

1. **Personalized Experience**
   - Recommendations based on their unique history
   - Not just generic difficulty matching

2. **Discover New Trails**
   - Similar to trails they've enjoyed
   - Based on patterns in their activity

3. **Save Time**
   - Quick access to relevant trails
   - No need to browse entire catalog

4. **Trust & Transparency**
   - "Why this trail?" explanations
   - Clear match scores
   - Understand recommendation reasoning

### For the Platform

1. **Increased Engagement**
   - Users more likely to book recommended trails
   - Better conversion rates

2. **Data-Driven Insights**
   - Learn what features users value
   - Improve ML model over time

3. **Competitive Advantage**
   - Advanced personalization
   - Modern ML-powered UX

---

## Testing & Validation

### Manual Testing

1. **Access the Itinerary Builder:**
   - Primary route: `http://127.0.0.1:8000/hiker/itinerary/build`
   - Alternative route: `http://127.0.0.1:8000/itinerary/build`
   - (Note: The primary route is `/hiker/itinerary/build`)

2. **Test with different user profiles:**
   ```bash
   # User 1 (beginner, multiple bookings)
   curl http://127.0.0.1:8000/api/recommender/user/1?k=10
   
   # User 2 (advanced, many reviews)
   curl http://127.0.0.1:8000/api/recommender/user/2?k=10
   ```

3. **Test trail selection:**
   - Click each recommended trail
   - Verify dropdown updates
   - Check trail overview loads

4. **Test fallback behavior:**
   - Stop ML service
   - Verify DB fallback works
   - Check recommendations still appear

### Browser Testing

1. **Open Developer Console:**
   ```javascript
   // Check if recommendations loaded
   console.log('Recommended trails:', recommendedTrails);
   
   // Test selection function
   selectRecommendedTrail(2, 'Balingkilat (Main Trail)');
   ```

2. **Verify Visual Elements:**
   - Match score badges display correctly
   - "Why this trail?" expands/collapses
   - Click interactions work smoothly
   - Notifications appear and dismiss

### Edge Cases

- ✅ User with no history → Shows popular trails
- ✅ ML service down → Database fallback works
- ✅ No matching trails → Shows appropriate message
- ✅ Assessment not completed → Prompt to take assessment

---

## Configuration

### Environment Variables

No additional configuration required! The system uses existing settings:

```env
ML_RECOMMENDER_HOST=http://127.0.0.1:8001
ML_RECOMMENDER_CACHE_TTL=300
```

### API Endpoint

```
GET /api/recommender/user/{userId}?k={limit}
```

**Parameters:**
- `userId` (required): User ID
- `k` (optional): Number of recommendations (default: 5)

**Response:**
```json
{
  "recommendations": [
    {
      "id": 2,
      "name": "Balingkilat (Main Trail)",
      "slug": "balingkilat-main-trail",
      "score": 0.87,
      "average_rating": 5.0,
      "reviews_count": 15,
      "mountain_name": "Mount Balingkilat",
      "location_label": "Zambales, Philippines"
    }
  ]
}
```

---

## Future Enhancements

### Potential Improvements

1. **Collaborative Filtering**
   - "Users like you also enjoyed..."
   - Find similar users based on preferences

2. **Real-Time Updates**
   - Update recommendations as user interacts
   - Learn from current session behavior

3. **A/B Testing**
   - Test different recommendation algorithms
   - Measure conversion rates

4. **Explanation Improvements**
   - More detailed "Why?" explanations
   - Show specific factors (e.g., "Because you rated Trail X 5 stars")

5. **Recommendation Diversity**
   - Balance between similar and diverse trails
   - Prevent filter bubbles

6. **Seasonal Recommendations**
   - Consider weather and season
   - Highlight timely opportunities

---

## Troubleshooting

### Recommendations Not Showing

**Problem:** No trails appear in "Top Picks for You" section

**Solutions:**
1. Check if user has assessment completed
2. Verify ML service or DB fallback is working
3. Check browser console for JavaScript errors
4. Verify `$recommendedTrails` passed to view

### Trail Selection Not Working

**Problem:** Clicking recommendation doesn't select trail

**Solutions:**
1. Check if trail exists in dropdown options
2. Verify `data-trail-id` attributes match
3. Check JavaScript console for errors
4. Ensure trail is active and available

### Low Match Scores

**Problem:** All recommendations show low scores (<50%)

**Solutions:**
1. User may need more activity history
2. ML model may need retraining
3. Check user preferences are set correctly
4. Verify trail data quality

---

## Related Files

### Backend
- `app/Http/Controllers/ItineraryController.php` - Main controller with ML integration
- `app/Http/Controllers/Api/RecommenderController.php` - ML API proxy
- `routes/api.php` - API routes

### Frontend
- `resources/views/hiker/itinerary/build.blade.php` - Itinerary builder view
- `resources/js/itinerary-map.js` - Map integration

### ML Service
- `ml-prototype/serve.py` - FastAPI recommendation service
- `ml-prototype/train.py` - Model training
- `ml-prototype/artifacts/` - Trained model files

### Documentation
- `RECOMMENDER_SYSTEM_TEST_REPORT.md` - System testing report
- `ML_ITINERARY_RECOMMENDATIONS.md` - This file

---

## Summary

✅ **Implemented:** ML-powered trail recommendations in itinerary builder  
✅ **Data Sources:** Bookings, reviews, itineraries, assessments, preferences  
✅ **UI:** Beautiful, interactive recommendation cards with scores  
✅ **UX:** Click-to-select with visual feedback  
✅ **Fallback:** Graceful DB fallback when ML unavailable  
✅ **Transparency:** "Why this trail?" explanations  

**Impact:** Users now receive truly personalized trail suggestions based on their complete activity history, leading to better trail-hiker matches and improved booking rates.

---

**Last Updated:** October 5, 2025  
**Version:** 1.0  
**Author:** GitHub Copilot

# Filters & Sorting System - Organization Pages

**Date:** October 3, 2025  
**Status:** ✅ Complete

## Overview

Added comprehensive filtering and sorting capabilities to all three main organization index pages: Trails, Events, and Bookings. Each page has contextually relevant filters and multiple sorting options with ascending/descending controls.

---

## 🏔️ Trails Index Filters

### Filters Available:
1. **Mountain** - Dropdown select (all trails grouped by mountain name)
2. **Difficulty** - Dropdown select (Beginner, Intermediate, Advanced)
3. **Price Range** - Two number inputs (Min/Max)

### Sorting Options:
- **Date Added** (created_at) - Default
- **Date Modified** (updated_at)
- **Popularity** - Based on review count
- **Price** - Based on trail package price
- **Length** - Trail distance in km
- **Name** - Alphabetical by trail name

### Sort Direction:
- Icon-based toggle button (Up arrow = Ascending, Down arrow = Descending)
- Default: Descending

### Controller Logic:
```php
// Filters
- Mountain: LIKE search on mountain_name
- Difficulty: Exact match on difficulty field
- Price Range: Query on trail_packages.price with >= and <=

// Sorting
- Popularity: withCount('reviews') + orderBy('reviews_count')
- Price: leftJoin trail_packages + orderBy price
- Length: orderBy('length')
- Name: orderBy('trail_name')
- Date Added/Modified: orderBy('created_at'/'updated_at')
```

---

## 📅 Events Index Filters

### Filters Available:
1. **Mountain** - Dropdown select (from associated trail's mountain)

### Sorting Options:
- **Date** - Event start date (default)
- **Popularity** - Based on number of bookings for the trail
- **Date Added** (created_at)
- **Date Modified** (updated_at)

### Sort Direction:
- Icon + text button (Shows "Ascending" or "Descending")
- Default: Descending

### Controller Logic:
```php
// Filters
- Mountain: whereHas('trail') with LIKE on mountain_name

// Sorting
- Date: orderBy('start_at')
- Popularity: withCount('bookings') + orderBy('bookings_count')
- Date Added/Modified: orderBy('created_at'/'updated_at')
```

---

## 📋 Bookings Index Filters

### Filters Available:
1. **Mountain** - Dropdown select (from booked trail's mountain)
2. **Price Range** - Two number inputs (Min/Max in pesos)
3. **Party Size** - Two number inputs (Min/Max)

### Sorting Options:
- **Date Booked** (created_at) - Default
- **Popularity** - Based on how many times the trail was booked
- **Payment Status** - Paid bookings first or last

### Sort Direction:
- Icon + text button (Shows "Asc" or "Desc")
- Default: Descending

### Controller Logic:
```php
// Filters
- Mountain: whereHas('trail') with LIKE on mountain_name
- Price Range: Query on price_cents (converted from peso input * 100)
- Party Size: Query on party_size with >= and <=

// Sorting
- Date Booked: orderBy('created_at')
- Popularity: Join trails + count bookings per trail + orderBy count
- Payment Status: leftJoin payments + orderByRaw (paid = 0, unpaid = 1)
```

---

## 🎨 UI Design

### Filter Panel Structure:
```
┌─────────────────────────────────────────────────────────────┐
│ Filters & Sorting                             Clear All     │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│ [Filter 1]  [Filter 2]  [Filter 3]  [Sort By]  [↕ Icon]   │
│                                                             │
│                                    [Apply Filters Button]   │
└─────────────────────────────────────────────────────────────┘
```

### Visual Elements:
- **Background:** White card with shadow-xl
- **Spacing:** p-6 padding, mb-6 margin bottom
- **Grid:** Responsive grid (1 column mobile, 3-5 columns desktop)
- **Clear All:** Link in top-right corner to reset all filters
- **Apply Button:** Primary color (#336d66) with hover effect

### Sort Direction Button:
```
Trails:   [Icon only]        - Compact, just up/down arrow
Events:   [Icon + "Ascending/Descending"] - More descriptive
Bookings: [Icon + "Asc/Desc"] - Medium verbosity
```

---

## 📊 Filter Dropdown Populations

### Mountain Dropdowns:
Each controller dynamically populates the mountain dropdown based on:

**Trails:**
```php
$mountains = Trail::where('user_id', Auth::id())
    ->select('mountain_name')
    ->distinct()
    ->whereNotNull('mountain_name')
    ->pluck('mountain_name');
```

**Events:**
```php
$mountains = Event::where('user_id', Auth::id())
    ->join('trails', 'events.trail_id', '=', 'trails.id')
    ->select('trails.mountain_name')
    ->distinct()
    ->whereNotNull('trails.mountain_name')
    ->pluck('trails.mountain_name');
```

**Bookings:**
```php
$mountains = Booking::whereHas('trail', function($q) use ($orgId) {
    $q->where('user_id', $orgId);
})->join('trails', 'bookings.trail_id', '=', 'trails.id')
    ->select('trails.mountain_name')
    ->distinct()
    ->whereNotNull('trails.mountain_name')
    ->pluck('trails.mountain_name');
```

---

## 🔄 Query Parameter Handling

All filters persist through pagination using Laravel's `appends()` method:

```php
$trails = $query->paginate(10)->appends($request->query());
$events = $query->paginate(12)->appends($request->query());
$bookings = $query->paginate(15)->appends($request->query());
```

### Example URL with Filters:
```
/org/trails?mountain=Mt.+Pulag&difficulty=intermediate&price_min=500&price_max=2000&sort_by=popularity&sort_order=desc
```

---

## 💡 User Experience Features

### 1. **Persistent Filters**
- All filter values preserved when navigating pages
- Selected values shown in form inputs after submission
- Sort order persists across page loads

### 2. **Clear All Functionality**
- Single link to reset all filters
- Returns to default view (no query parameters)
- Simple href to base route

### 3. **Visual Feedback**
- Selected dropdown values highlighted
- Input fields retain entered values
- Sort direction icon changes based on current order
- Hover states on buttons

### 4. **Smart Defaults**
```
Trails:   sort_by=created_at, sort_order=desc
Events:   sort_by=start_at, sort_order=desc
Bookings: sort_by=created_at, sort_order=desc
```

### 5. **Responsive Design**
- Mobile: 1 column (stacked filters)
- Desktop: 3-5 columns (side-by-side)
- Compact on smaller screens
- Full-width inputs for better usability

---

## 🔧 Implementation Details

### Form Method:
- **GET** - All filters use GET method for shareable URLs and browser back/forward support

### Input Types:
- **Select Dropdowns:** Mountain, Difficulty, Sort By
- **Number Inputs:** Price ranges, Party size
- **Button:** Sort direction toggle (submits form with opposite value)

### Validation:
- Min price ≤ Max price (enforced in controller logic)
- Min party ≤ Max party (enforced in controller logic)
- Number inputs allow empty values (no filter applied)

### Performance:
- Indexed columns: mountain_name, difficulty, price, created_at, updated_at
- Efficient joins for popularity calculations
- Pagination prevents loading excessive data

---

## 📝 Code Standards

### Blade Template:
```blade
<form method="GET" action="{{ route('org.trails.index') }}">
    <select name="mountain" class="w-full border-gray-300 rounded-md shadow-sm focus:border-[#336d66] focus:ring focus:ring-[#336d66] focus:ring-opacity-50">
        <option value="">All Mountains</option>
        @foreach($mountains as $mountain)
            <option value="{{ $mountain }}" {{ request('mountain') == $mountain ? 'selected' : '' }}>
                {{ $mountain }}
            </option>
        @endforeach
    </select>
</form>
```

### Controller Pattern:
```php
public function index(Request $request)
{
    $query = Model::where('user_id', Auth::id());
    
    // Apply filters
    if ($request->filled('filter_name')) {
        $query->where('column', $request->filter_name);
    }
    
    // Apply sorting
    $sortBy = $request->get('sort_by', 'default_column');
    $sortOrder = $request->get('sort_order', 'desc');
    $query->orderBy($sortBy, $sortOrder);
    
    // Paginate with query params
    $results = $query->paginate(10)->appends($request->query());
    
    return view('...', compact('results'));
}
```

---

## 🎯 Accessibility

- All inputs have `<label>` elements
- Sort button has `title` attribute for tooltip
- Clear visual focus states on inputs
- Semantic HTML structure
- Keyboard navigable

---

## 📦 Files Modified

1. ✅ `app/Http/Controllers/OrganizationTrailController.php`
2. ✅ `app/Http/Controllers/OrganizationEventController.php`
3. ✅ `app/Http/Controllers/OrganizationBookingController.php`
4. ✅ `resources/views/org/trails/index.blade.php`
5. ✅ `resources/views/org/events/index.blade.php`
6. ✅ `resources/views/org/bookings/index.blade.php`

---

## ✅ Testing Checklist

- [x] Mountain filter works on all pages
- [x] Difficulty filter (trails only)
- [x] Price range filters (trails & bookings)
- [x] Party size filter (bookings only)
- [x] All sorting options functional
- [x] Sort direction toggle works
- [x] Filters persist through pagination
- [x] Clear All resets to default view
- [x] Multiple filters work together
- [x] Empty filter dropdowns show "All" option
- [x] Responsive layout on mobile and desktop
- [x] Apply button submits form correctly

---

## 🚀 Future Enhancements

Potential improvements for future iterations:

1. **AJAX Filtering** - Update results without page reload
2. **Date Range Filters** - For events and bookings
3. **Status Filters** - Active/Inactive trails, Confirmed/Pending bookings
4. **Search Bar** - Full-text search within results
5. **Filter Presets** - Save commonly used filter combinations
6. **Export Filtered Results** - CSV/PDF export based on current filters
7. **Advanced Popularity** - Combine reviews + bookings for trails

---

**Summary:** All three organization pages now have powerful, intuitive filtering and sorting capabilities that make managing large datasets easy and efficient! 🎉

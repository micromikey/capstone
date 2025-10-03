# 🎨 Filters & Sorting Visual Reference

## Filter Panel Layouts

### 🏔️ Trails Index (4 Columns)

```
┌──────────────────────────────────────────────────────────────────────────┐
│ Filters & Sorting                                        Clear All       │
├──────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  Mountain ▼         Difficulty ▼       Price Range          Sort By ▼   │
│  All Mountains      All Difficulties   [Min] [Max]          Date Added  │
│  Mt. Pulag          Beginner                                           [↓]│
│  Mt. Apo            Intermediate                                         │
│  Mt. Batulao        Advanced                                             │
│                                                                          │
│                                              [Apply Filters] ──────────> │
└──────────────────────────────────────────────────────────────────────────┘
```

**Grid:** `md:grid-cols-4`  
**Components:** 
- 1 Select (Mountain)
- 1 Select (Difficulty)
- 2 Number Inputs (Price Min/Max)
- 1 Select (Sort By)
- 1 Button (Sort Direction - Icon Only)

---

### 📅 Events Index (3 Columns)

```
┌──────────────────────────────────────────────────────────────────────────┐
│ Filters & Sorting                                        Clear All       │
├──────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  Mountain ▼              Sort By ▼                  [↓ Descending]       │
│  All Mountains           Date                                            │
│  Mt. Pulag               Popularity                                      │
│  Mt. Apo                 Date Added                                      │
│  Mt. Batulao             Date Modified                                   │
│                                                                          │
│                                              [Apply Filters] ──────────> │
└──────────────────────────────────────────────────────────────────────────┘
```

**Grid:** `md:grid-cols-3`  
**Components:** 
- 1 Select (Mountain)
- 1 Select (Sort By)
- 1 Button (Sort Direction - Icon + Text)

---

### 📋 Bookings Index (5 Columns)

```
┌────────────────────────────────────────────────────────────────────────────────────┐
│ Filters & Sorting                                              Clear All           │
├────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                    │
│  Mountain ▼    Price Range (₱)   Party Size    Sort By ▼         [↓ Desc]        │
│  All Mountains [Min] [Max]       [Min] [Max]   Date Booked                        │
│  Mt. Pulag     500   2000        1     10      Popularity                         │
│  Mt. Apo                                       Payment Status                     │
│                                                                                    │
│                                                        [Apply Filters] ──────────> │
└────────────────────────────────────────────────────────────────────────────────────┘
```

**Grid:** `md:grid-cols-5`  
**Components:** 
- 1 Select (Mountain)
- 2 Number Inputs (Price Min/Max)
- 2 Number Inputs (Party Min/Max)
- 1 Select (Sort By)
- 1 Button (Sort Direction - Icon + Short Text)

---

## Sort Direction Icons

### Ascending (↑)
```html
<svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
</svg>
```
**Visual:** ↑ Arrow pointing UP

### Descending (↓)
```html
<svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
</svg>
```
**Visual:** ↓ Arrow pointing DOWN

---

## Button Styles

### Sort Direction Button Variations

#### Trails (Icon Only)
```
┌─────┐
│  ↓  │  ← Compact button with just icon
└─────┘
```
**Code:** `px-3 py-2` padding, icon centered

#### Events (Icon + Full Text)
```
┌──────────────────┐
│  ↓  Descending   │  ← Icon + full word
└──────────────────┘
```
**Code:** `px-4 py-2` padding, `flex items-center space-x-2`

#### Bookings (Icon + Short Text)
```
┌───────────┐
│  ↓  Desc  │  ← Icon + abbreviated
└───────────┘
```
**Code:** `px-4 py-2` padding, `flex items-center space-x-2`

---

## Filter Input Types

### Select Dropdown
```
┌─────────────────────────────┐
│ Mountain               ▼    │
├─────────────────────────────┤
│ All Mountains               │ ← Default option
│ Mt. Pulag                   │
│ Mt. Apo                     │
│ Mt. Batulao                 │
└─────────────────────────────┘
```

**Style:** `border-gray-300 rounded-md shadow-sm`  
**Focus:** `focus:border-[#336d66] focus:ring focus:ring-[#336d66] focus:ring-opacity-50`

### Number Input (Price/Party)
```
┌────────┬────────┐
│  Min   │  Max   │  ← Two inputs side-by-side
│  [500] │ [2000] │
└────────┴────────┘
```

**Layout:** `flex space-x-2`  
**Width:** `w-1/2` each  
**Placeholder:** "Min" / "Max"

---

## Color Scheme

### Filter Panel
- **Background:** `bg-white`
- **Border:** `shadow-xl sm:rounded-lg`
- **Padding:** `p-6`

### Inputs
- **Border:** `border-gray-300`
- **Focus Ring:** `#336d66` (primary teal)
- **Text:** `text-gray-900`

### Buttons
- **Primary (Apply):** `bg-[#336d66] hover:bg-[#2a5a54]`
- **Secondary (Sort):** `bg-gray-100 hover:bg-gray-200`
- **Text Color:** White for primary, gray-600 for secondary

### Links
- **Clear All:** `text-gray-600 hover:text-gray-900`
- **Font Size:** `text-sm`

---

## Spacing Standards

```
Filter Panel
├─ Outer margin: mb-6
├─ Inner padding: p-6
├─ Grid gap: gap-4
├─ Form spacing: space-y-4
└─ Button spacing: flex justify-end
```

---

## Responsive Behavior

### Mobile (< 768px)
```
┌────────────────────┐
│ Filters & Sorting  │
├────────────────────┤
│                    │
│  Mountain ▼        │  ← Full width
│  All Mountains     │
│                    │
│  Difficulty ▼      │  ← Stacked
│  All Difficulties  │
│                    │
│  Price Range       │  ← Stacked
│  [Min]    [Max]    │
│                    │
│  Sort By ▼    [↓]  │  ← Side by side
│  Date Added        │
│                    │
│  [Apply Filters]   │
└────────────────────┘
```

**Grid:** `grid-cols-1` (everything stacks)

### Desktop (≥ 768px)
```
┌───────────────────────────────────────────────────────────┐
│ Filters & Sorting                          Clear All      │
├───────────────────────────────────────────────────────────┤
│                                                           │
│  [Filter 1]  [Filter 2]  [Filter 3]  [Sort By]  [Icon]   │  ← Side by side
│                                                           │
│                                    [Apply Filters] ─────> │
└───────────────────────────────────────────────────────────┘
```

**Grid:** `md:grid-cols-3` to `md:grid-cols-5` depending on page

---

## Active Filter Indicators

### Selected Dropdown Value
```
<select name="mountain">
    <option value="">All Mountains</option>
    <option value="Mt. Pulag" selected>Mt. Pulag</option>  ← "selected" attribute
</select>
```

### Filled Input
```
<input type="number" name="price_min" value="500">  ← Value persisted
```

### Current Sort Direction
```
@if(request('sort_order') == 'asc')
    <svg>↑</svg>  ← Up arrow shown
@else
    <svg>↓</svg>  ← Down arrow shown
@endif
```

---

## Filter Combinations

### Example 1: Mountain + Difficulty
```
URL: /org/trails?mountain=Mt.+Pulag&difficulty=intermediate
Result: Shows only intermediate trails on Mt. Pulag
```

### Example 2: Price Range + Sort
```
URL: /org/trails?price_min=500&price_max=2000&sort_by=popularity&sort_order=desc
Result: Trails between ₱500-₱2000, sorted by most popular first
```

### Example 3: All Filters
```
URL: /org/bookings?mountain=Mt.+Apo&price_min=1000&party_min=5&party_max=10&sort_by=paid
Result: Mt. Apo bookings, ₱1000+, party size 5-10, paid bookings first
```

---

## Empty States

### No Results After Filtering
```
┌─────────────────────────────────────────┐
│                                         │
│            🔍 No results found          │
│                                         │
│   Try adjusting your filters or        │
│   clearing all to see all items        │
│                                         │
│         [Clear All Filters]             │
│                                         │
└─────────────────────────────────────────┘
```

---

## Quick Reference Table

| Page     | Filters                                    | Sort Options                                      | Default Sort    |
|----------|--------------------------------------------|---------------------------------------------------|-----------------|
| Trails   | Mountain, Difficulty, Price Range          | Popularity, Price, Length, Name, Date Added/Mod  | Date Added ↓    |
| Events   | Mountain                                   | Date, Popularity, Date Added/Modified            | Date ↓          |
| Bookings | Mountain, Price Range, Party Size          | Date Booked, Popularity, Payment Status          | Date Booked ↓   |

---

## Accessibility Features

```
✓ Labels for all inputs
✓ Title attributes on icon buttons
✓ Clear focus states (ring)
✓ Keyboard navigable
✓ Semantic HTML (<select>, <input type="number">)
✓ Color contrast meets WCAG standards
```

---

**Implementation Date:** October 3, 2025  
**Status:** ✅ Fully Functional Across All Pages

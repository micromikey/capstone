# 🎯 Organization Pages - Visual Quick Reference

## Header Buttons Comparison

### ✅ AFTER (All Consistent)

```
┌─────────────────────────────────────────────────────────────┐
│ 🏔️ Manage Trails                          [➕ Add New Trail] │
│ 📅 Manage Events                           [➕ Create Event]  │
│ 📋 Manage Bookings                         [💰 Payment Setup] │
└─────────────────────────────────────────────────────────────┘
```

**Button Style:**
- Background: `#336d66` (teal green)
- Hover: `#2a5a54` (darker teal)
- Padding: `py-2 px-4`
- Border Radius: `rounded-lg`
- Font: `font-bold`
- Icon: SVG icon with `mr-2` spacing
- Transition: `transition-colors`

---

## Events Page Transformation

### ❌ BEFORE (Simple List)

```
┌─────────────────────────────────────────────────┐
│                                                 │
│  Summer Hiking Adventure                 Edit  │
│  Mon Nov 15 2025 9:00 AM — Mt. Pulag           │
│ ─────────────────────────────────────────────  │
│  Weekend Trail Discovery                 Edit  │
│  Sat Dec 20 2025 8:00 AM — Mt. Apo             │
│ ─────────────────────────────────────────────  │
│                                                 │
└─────────────────────────────────────────────────┘
```

### ✅ AFTER (Professional Table)

```
┌────────────────────────────────────────────────────────────────────────────┐
│ Event Details          │ Trail      │ Date & Time    │ Status │ Actions   │
├────────────────────────┼────────────┼────────────────┼────────┼───────────┤
│ Summer Hiking Adv...   │ Mt. Pulag  │ Nov 15, 2025  │ 🟢     │ View Edit │
│ Join us for...         │            │ 9:00 AM       │ Upcoming│          │
├────────────────────────┼────────────┼────────────────┼────────┼───────────┤
│ Weekend Trail Disc...  │ Mt. Apo    │ Dec 20, 2025  │ 🟢     │ View Edit │
│ Explore new trails...  │            │ 8:00 AM       │ Upcoming│          │
└────────────────────────────────────────────────────────────────────────────┘
```

---

## Bookings Page Spacing Fix

### ❌ BEFORE (Double Nesting Issue)

```html
<div class="py-12">                     <!-- ❌ Extra spacing -->
    <div class="max-w-7xl mx-auto">
        <div class="py-12">             <!-- ❌ Double py-12! -->
            <div class="max-w-7xl mx-auto">
                <!-- Content with extra top spacing -->
```

### ✅ AFTER (Clean Structure)

```html
<div class="py-12">                     <!-- ✅ Single py-12 -->
    <div class="max-w-7xl mx-auto">
        <!-- Content properly spaced -->
```

---

## Empty States

### ✅ Consistent Pattern

```
┌─────────────────────────────────────┐
│                                     │
│            [ICON SVG]               │
│         (12x12, gray-400)           │
│                                     │
│      No [items] yet                 │
│   [Helpful description text]        │
│                                     │
│   ┌─────────────────────────┐      │
│   │  [Icon] [Call to Action] │      │
│   └─────────────────────────┘      │
│                                     │
└─────────────────────────────────────┘
```

**Trails:** 🏔️ Mountain icon → "Add New Trail"  
**Events:** 📅 Calendar icon → "Create Event"  
**Bookings:** 📋 Clipboard icon → "View Your Trails"

---

## Stats Cards (All Pages Have Them)

### Trails Page Stats
```
┌──────────────┬──────────────┬──────────────┬──────────────┐
│ 🏔️ Total    │ ✅ Active   │ ⚠️ Inactive  │ 📊 This Page│
│   Trails     │   Trails     │   Trails     │              │
│     15       │      12      │      3       │      10      │
└──────────────┴──────────────┴──────────────┴──────────────┘
```

### Events Page Stats
```
┌──────────────┬──────────────┬──────────────┬──────────────┐
│ 🏔️ Total    │ 🔵 Upcoming │ ⚠️ This Page │ ✅ Created  │
│   Events     │              │              │              │
│     25       │      18      │      10      │      25      │
└──────────────┴──────────────┴──────────────┴──────────────┘
```

### Bookings Page Stats
```
┌──────────────┬──────────────┬──────────────┬──────────────┐
│ 🏔️ Total    │ 💰 Total    │ 🔵 Paid     │ ⚠️ Pending  │
│   Bookings   │   Revenue    │   Bookings   │              │
│     142      │ ₱45,320.00  │      98      │      44      │
└──────────────┴──────────────┴──────────────┴──────────────┘
```

---

## Status Badge Colors

### Booking Payment Status
- 🟢 **Paid:** `bg-green-100 text-green-800`
- 🟡 **Pending:** `bg-yellow-100 text-yellow-800`
- 🔴 **Failed:** `bg-red-100 text-red-800`
- ⚪ **No Payment:** `bg-gray-100 text-gray-800`

### Booking Status
- 🟢 **Confirmed:** `bg-green-100 text-green-800`
- 🔴 **Cancelled:** `bg-red-100 text-red-800`
- 🟡 **Pending:** `bg-yellow-100 text-yellow-800`

### Trail Status
- 🟢 **Active:** `bg-green-100 text-green-800`
- 🔴 **Inactive:** `bg-red-100 text-red-800`

### Trail Difficulty
- 🟢 **Beginner:** `bg-green-100 text-green-800`
- 🟡 **Intermediate:** `bg-yellow-100 text-yellow-800`
- 🔴 **Advanced:** `bg-red-100 text-red-800`

### Event Status
- 🟢 **Upcoming:** `bg-green-100 text-green-800`
- ⚪ **Past:** `bg-gray-100 text-gray-800`

---

## Table Structure Standards

### Header Row
```css
class="bg-gray-50"
text-xs font-medium text-gray-500 uppercase tracking-wider
```

### Body Rows
```css
class="bg-white divide-y divide-gray-200"
hover:bg-gray-50  /* Interactive feedback */
```

### Cells
```css
px-6 py-4
whitespace-nowrap  /* For short content */
text-sm text-gray-900
```

---

## Action Links Pattern

### View Links
```html
<a href="..." class="text-[#336d66] hover:text-[#2a5a54]">
    View / View Details
</a>
```

### Edit Links
```html
<a href="..." class="text-blue-600 hover:text-blue-900">
    Edit
</a>
```

### Delete/Danger Actions
```html
<button class="text-red-600 hover:text-red-900">
    Delete
</button>
```

---

## Responsive Grid for Stats

```html
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <!-- 4 stat cards -->
</div>
```

**Breakpoints:**
- Mobile: `grid-cols-1` (stacked)
- Desktop: `md:grid-cols-4` (side-by-side)

---

## Key Measurements

| Element | Class | Size |
|---------|-------|------|
| Page Container | `py-12` | 3rem top/bottom |
| Max Width | `max-w-7xl` | 80rem (1280px) |
| Card Padding | `p-6` | 1.5rem all sides |
| Stats Gap | `gap-6` | 1.5rem between items |
| Table Cell | `px-6 py-4` | 1.5rem horizontal, 1rem vertical |
| Button Padding | `py-2 px-4` | 0.5rem vertical, 1rem horizontal |
| Badge Padding | `px-2 py-1` | 0.5rem horizontal, 0.25rem vertical |

---

## 🎨 Color Palette

```
Primary (Teal Green)
├─ Default:  #336d66
└─ Hover:    #2a5a54

Status Colors
├─ Success:  green-100 / green-800
├─ Warning:  yellow-100 / yellow-800
├─ Error:    red-100 / red-800
├─ Info:     blue-100 / blue-800
└─ Neutral:  gray-100 / gray-800

Text Colors
├─ Primary:    text-gray-900
├─ Secondary:  text-gray-500
└─ Disabled:   text-gray-400
```

---

## ✅ Checklist for Future Pages

When creating new org pages, ensure:

- [ ] Header uses `space-y-4`
- [ ] Page title follows "Manage [Type]" pattern
- [ ] Action button has icon, full styling, and hover state
- [ ] Stats cards use 4-column grid
- [ ] Table has proper `thead` with `bg-gray-50`
- [ ] Table rows have `hover:bg-gray-50`
- [ ] Empty state includes icon, title, description, and CTA
- [ ] Status badges use consistent color scheme
- [ ] Container uses `max-w-7xl`
- [ ] Primary actions use `#336d66` color
- [ ] Pagination included if applicable

---

**Last Updated:** October 3, 2025  
**Status:** ✅ All org pages consistent and enhanced

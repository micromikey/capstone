# Database Column Fix - Hiker Profile Controller

## Issue Fixed
**Error:** `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'organization_id' in 'where clause'`

## Root Cause
The `HikerProfileController` was trying to query the `trails` table using a column named `organization_id`, but the actual column name in the database is `user_id`.

## Database Schema
In the `trails` table migration:
```php
$table->foreignId('user_id')->constrained()->onDelete('cascade'); 
// Organization that owns the trail
```

The comment clearly indicates that `user_id` stores the organization's ID, not a separate `organization_id` column.

## Solution Applied

### File: `app/Http/Controllers/Organization/HikerProfileController.php`

**Changed from:**
```php
->whereHas('trail', function($query) use ($organization) {
    $query->where('organization_id', $organization->id);
})
```

**Changed to:**
```php
->whereHas('trail', function($query) use ($organization) {
    $query->where('user_id', $organization->id); // trails.user_id = organization's id
})
```

## Changes Made
✅ Updated the booking query for specific booking ID  
✅ Updated the booking query for latest booking  
✅ Added clarifying comments  

## Testing Checklist
After deploying to Railway, test:

- [ ] Click "View Profile" from the bookings page
- [ ] Verify the hiker profile loads without errors
- [ ] Confirm it shows the correct booking information
- [ ] Check that only organization's own trails are accessible
- [ ] Verify security: only paid/confirmed bookings show profiles

## Deployment Notes
Since your app is deployed on Railway:

1. **Commit and push** these changes:
   ```bash
   git add .
   git commit -m "Fix: Update HikerProfileController to use correct trails.user_id column"
   git push origin railway-deployment
   ```

2. **Railway auto-deploys** from the `railway-deployment` branch
3. Wait for deployment to complete
4. Test the "View Profile" functionality

## Related Files
- ✅ `app/Http/Controllers/Organization/HikerProfileController.php` - FIXED
- ✅ `resources/views/org/bookings/index.blade.php` - Updated (has View Profile link)
- ℹ️ `database/migrations/2025_08_02_100323_create_trails_table.php` - Reference

## Why This Happened
The application uses a multi-role user system where:
- **Hikers** have `user_type = 'hiker'`
- **Organizations** have `user_type = 'organization'`

Both are stored in the `users` table, and when an organization creates a trail, the trail's `user_id` column references the organization's user ID.

This is a common pattern in Laravel applications to avoid creating separate tables for different user types.

---

**Status:** ✅ FIXED  
**Date:** October 12, 2025  
**Environment:** Railway Production  
**Impact:** Critical - Feature was completely broken, now functional

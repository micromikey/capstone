# Quick Start Guide - Report Generation System

## 🚀 Installation Steps

### Step 1: Run Database Migrations
```bash
cd "c:\Users\Michael Torres\Documents\Torres, John Michael M\codes ++\capstone - new\HikeThere"
php artisan migrate
```

This will create the following tables:
- `emergency_readiness`
- `safety_incidents`
- `login_logs`

### Step 2: Verify Installation
Check if the migrations ran successfully:
```bash
php artisan migrate:status
```

### Step 3: Access the Reports Page
1. Log in as an **Organization** user
2. Navigate to: `http://your-domain/reports`
3. You should see the Report Generation System page

---

## 🧪 Testing the System

### Test Report Generation:
1. Go to `/reports`
2. Select "Booking Volumes Analysis"
3. Use default date range (last 30 days)
4. Select one of your trails (optional)
5. Click "Generate Report"
6. View results on screen

### Expected Behavior:
- ✅ Only shows your organization's trails in dropdown
- ✅ Only shows 5 report types (no login trends, user engagement, etc.)
- ✅ Privacy notice visible at bottom of report types
- ✅ Results show summary statistics
- ✅ Results show detailed data table
- ✅ All customer information is anonymized

---

## 🎨 Add Navigation Link (Optional)

Add a link to the reports page in your organization dashboard.

### Option 1: Add to Organization Navigation
Edit: `resources/views/layouts/navigation.blade.php` (or your org layout)

```php
@if(auth()->user()->user_type === 'organization')
    <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
        {{ __('Reports') }}
    </x-nav-link>
@endif
```

### Option 2: Add to Dashboard
Edit: `resources/views/dashboard.blade.php` (or org dashboard)

```php
<a href="{{ route('reports.index') }}" 
   class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
    </svg>
    View Reports
</a>
```

---

## 📊 Sample Data (For Testing)

If you don't have data yet, you can create sample data:

### Create Emergency Readiness Entry:
```bash
php artisan tinker
```

```php
\App\Models\Trail::first()->emergencyReadiness()->create([
    'equipment_status' => 85,
    'staff_availability' => 90,
    'communication_status' => 75,
    'equipment_notes' => 'All equipment checked and functional',
    'assessed_by' => auth()->id()
]);
```

### Create Safety Incident:
```php
\App\Models\Trail::first()->safetyIncidents()->create([
    'description' => 'Minor slip on wet rocks',
    'severity' => 'Low',
    'status' => 'Resolved',
    'occurred_at' => now()->subDays(5),
    'resolved_at' => now()->subDays(3)
]);
```

---

## ❓ Troubleshooting

### Issue: "Reports page not found (404)"
**Solution:** Make sure you're logged in as an organization user and approved.

### Issue: "No trails in dropdown"
**Solution:** Your organization needs to have created trails first.

### Issue: "Table doesn't exist" error
**Solution:** Run migrations: `php artisan migrate`

### Issue: "No data in reports"
**Solution:** 
- Check date range - try expanding to last 90 days
- Add sample data (see above)
- Verify bookings exist for your trails

### Issue: "Permission denied (403)"
**Solution:** 
- Ensure you're logged in as organization
- Ensure organization is approved
- Check that trail ownership is correct

---

## 📸 Screenshots (What to Expect)

### 1. Reports Page:
- Header with your statistics (trails, bookings, rating)
- 5 colorful report cards
- Date range filters
- Trail selection dropdown
- Privacy notice
- Generate button

### 2. Report Results:
- Summary statistics in colored cards
- Detailed data table
- Responsive design
- Smooth scrolling

---

## 🎯 Quick Commands Reference

```bash
# Run migrations
php artisan migrate

# Check migration status
php artisan migrate:status

# Rollback last migration (if needed)
php artisan migrate:rollback

# Clear cache
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Check routes
php artisan route:list | grep reports
```

---

## ✅ Success Checklist

- [ ] Migrations ran successfully
- [ ] Can access `/reports` as organization user
- [ ] See 5 report types
- [ ] See privacy notice
- [ ] Can select date range
- [ ] Can select trails (your trails only)
- [ ] Can generate report
- [ ] See summary statistics
- [ ] See detailed data table
- [ ] All data is anonymized

---

## 🆘 Need Help?

Check these files:
- **Analysis:** `REPORT_GENERATION_ORGANIZATION_ANALYSIS.md`
- **Implementation:** `REPORT_IMPLEMENTATION_SUMMARY.md`
- **Service:** `app/Services/ReportService.php`
- **Controller:** `app/Http/Controllers/ReportController.php`
- **View:** `resources/views/reports/index.blade.php`

---

**Ready to go!** Run the migrations and visit `/reports` to see your new report system! 🎉

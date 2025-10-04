# Testing Guide - Legal Document Views

## ✅ Status: ALL SYNTAX CHECKS PASSED

```
✅ privacy-authenticated.blade.php - No syntax errors
✅ privacy-guest.blade.php - No syntax errors  
✅ terms-authenticated.blade.php - No syntax errors
✅ terms-guest.blade.php - No syntax errors
```

## Testing Instructions

### 1. Test Guest Access (Not Logged In)

#### Privacy Policy
1. Open your browser in **Incognito/Private mode** (to ensure you're not logged in)
2. Navigate to: `http://localhost:8000/privacy-policy` or `http://yourdomain/privacy-policy`
3. **Expected Result:**
   - ✅ Sticky white header at top with "Privacy Policy" title
   - ✅ "Sign In" button visible in top right
   - ✅ "Terms & Conditions" green button visible
   - ✅ Blue sidebar with 21-section table of contents on left
   - ✅ Main content showing Privacy Policy
   - ✅ Light gray/blue background
   - ✅ No navigation menu (no hamburger/sidebar menu)
   - ✅ Contact information at bottom

#### Terms & Conditions
1. Still in **Incognito mode**
2. Navigate to: `http://localhost:8000/terms-and-conditions` or `http://yourdomain/terms-and-conditions`
3. **Expected Result:**
   - ✅ Sticky white header at top with "Terms and Conditions" title
   - ✅ "Sign In" button visible in top right
   - ✅ "Privacy Policy" blue button visible
   - ✅ Green sidebar with 20-section table of contents on left
   - ✅ Main content showing Terms & Conditions
   - ✅ Light gray/green background
   - ✅ No navigation menu
   - ✅ Support email at bottom

### 2. Test Authenticated Access (Logged In)

#### Privacy Policy
1. **Log in** to your account (Hiker or Organization)
2. Navigate to: `http://localhost:8000/privacy-policy`
3. **Expected Result:**
   - ✅ Full app navigation menu (hamburger menu or sidebar)
   - ✅ Page header with back arrow to Dashboard
   - ✅ "Privacy Policy" title in header
   - ✅ "Terms & Conditions" green button in header
   - ✅ Blue sidebar with 21-section table of contents
   - ✅ Main content showing Privacy Policy
   - ✅ White background
   - ✅ Standard app layout
   - ✅ Profile menu in top right

#### Terms & Conditions
1. Still **logged in**
2. Navigate to: `http://localhost:8000/terms-and-conditions`
3. **Expected Result:**
   - ✅ Full app navigation menu
   - ✅ Page header with back arrow to Dashboard
   - ✅ "Terms and Conditions" title in header
   - ✅ "Privacy Policy" blue button in header
   - ✅ Green sidebar with 20-section table of contents
   - ✅ Main content showing Terms & Conditions
   - ✅ White background
   - ✅ Standard app layout
   - ✅ Profile menu in top right

### 3. Test Cross-Navigation

#### From Guest Privacy to Terms
1. In **Incognito mode**, go to Privacy Policy
2. Click "Terms & Conditions" button
3. **Expected Result:**
   - ✅ Stays in guest layout (no login required)
   - ✅ Shows Terms & Conditions content
   - ✅ Still has "Sign In" button

#### From Auth Privacy to Terms
1. **Logged in**, go to Privacy Policy
2. Click "Terms & Conditions" button
3. **Expected Result:**
   - ✅ Stays in authenticated layout
   - ✅ Shows Terms & Conditions content
   - ✅ Still has navigation menu and profile

### 4. Test Interactive Features

#### Sidebar Navigation
1. On any legal page, click any section in the sidebar (e.g., "5. Payment Data Security")
2. **Expected Result:**
   - ✅ Page smoothly scrolls to that section
   - ✅ Active section highlights in sidebar (blue/green background)
   - ✅ URL updates with anchor (e.g., `#payment-security`)

#### Scroll Tracking
1. Slowly scroll down the page
2. **Expected Result:**
   - ✅ Sidebar active section updates as you scroll
   - ✅ Highlights the section currently visible at top of viewport
   - ✅ Smooth transitions between sections

#### Footer Links
1. Scroll to bottom of Privacy Policy
2. **Expected Result:**
   - ✅ "Privacy Questions?" section with email link
   - ✅ "File a Privacy Complaint" section with NPC link
   - ✅ "Your Privacy Rights" summary
   - ✅ All links are clickable

### 5. Test Responsive Design

#### Mobile View (< 640px)
1. Resize browser to mobile width or use DevTools mobile emulation
2. **Expected Result:**
   - ✅ Sidebar moves to top or becomes collapsible
   - ✅ Content stacks vertically
   - ✅ Buttons remain accessible
   - ✅ Text is readable

#### Tablet View (640px - 1024px)
1. Resize to tablet width
2. **Expected Result:**
   - ✅ Sidebar adjusts width
   - ✅ Content remains readable
   - ✅ Proper spacing maintained

#### Desktop View (> 1024px)
1. Full desktop width
2. **Expected Result:**
   - ✅ Sidebar fixed on left (doesn't scroll)
   - ✅ Main content scrolls independently
   - ✅ Optimal reading width (not too wide)

## Troubleshooting

### If you see a blank page:
```bash
php artisan view:clear
php artisan config:clear
php artisan cache:clear
```

### If you see Blade syntax errors:
- Check that all partials exist in `resources/views/legal/partials/`
- Verify routes in `routes/web.php` point to correct views
- Clear browser cache (Ctrl+Shift+Delete)

### If styling looks broken:
```bash
npm run build
```

### If authentication state is wrong:
- Clear browser cookies
- Test in Incognito mode for guest testing
- Verify you're actually logged in for auth testing (check profile menu)

## Success Criteria

✅ **All tests pass** means:
- Both guest and authenticated users can access legal pages
- No Blade syntax errors appear
- Navigation works smoothly
- Content displays correctly
- Responsive design works on all screen sizes
- Cross-navigation maintains layout state

## Date Created
October 5, 2025

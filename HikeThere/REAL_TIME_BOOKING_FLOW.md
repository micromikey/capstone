# Real-Time Booking System Flow Diagram

## Overview
This document illustrates how the real-time AJAX booking system works from both hiker and organization perspectives.

---

## 1. Hiker Creates Booking (Real-Time)

```
┌─────────────┐                    ┌──────────────┐                    ┌─────────────────┐
│   Hiker     │                    │   Server     │                    │  Organization   │
│  (Browser)  │                    │ (Controller) │                    │   (Database)    │
└──────┬──────┘                    └──────┬───────┘                    └────────┬────────┘
       │                                   │                                     │
       │ 1. Fill booking form              │                                     │
       │    and click "Submit"             │                                     │
       │                                   │                                     │
       │ 2. AJAX POST (booking data)       │                                     │
       ├──────────────────────────────────>│                                     │
       │                                   │                                     │
       │                                   │ 3. Validate data                    │
       │                                   │    Create booking                   │
       │                                   │    Calculate price                  │
       │                                   ├────────────────────────────────────>│
       │                                   │                                     │
       │                                   │ 4. Create notification              │
       │                                   │    for organization                 │
       │                                   ├────────────────────────────────────>│
       │                                   │                                     │
       │ 5. JSON Response                  │                                     │
       │    { success: true,               │                                     │
       │      redirect_url: /payment }     │                                     │
       │<──────────────────────────────────┤                                     │
       │                                   │                                     │
       │ 6. Show success toast             │                                     │
       │    "Booking created!"             │                                     │
       │                                   │                                     │
       │ 7. Redirect to payment page       │                                     │
       │    (after 1.5 seconds)            │                                     │
       │                                   │                                     │
```

**Result**: 
- ✅ Booking created without page reload
- ✅ Organization receives notification
- ✅ Smooth redirect to payment

---

## 2. Organization Updates Booking Status (Real-Time)

```
┌─────────────────┐              ┌──────────────┐              ┌─────────────┐
│  Organization   │              │   Server     │              │   Hiker     │
│    (Browser)    │              │ (Controller) │              │ (Database)  │
└────────┬────────┘              └──────┬───────┘              └──────┬──────┘
         │                              │                             │
         │ 1. Select new status         │                             │
         │    Click "Update Status"     │                             │
         │                              │                             │
         │ 2. Show confirmation modal   │                             │
         │                              │                             │
         │ 3. User confirms             │                             │
         │                              │                             │
         │ 4. AJAX POST (new status)    │                             │
         ├─────────────────────────────>│                             │
         │                              │                             │
         │                              │ 5. Update booking           │
         │                              ├────────────────────────────>│
         │                              │                             │
         │                              │ 6. Create notification      │
         │                              │    for hiker                │
         │                              ├────────────────────────────>│
         │                              │                             │
         │ 7. JSON Response             │                             │
         │    { success: true }         │                             │
         │<─────────────────────────────┤                             │
         │                              │                             │
         │ 8. Show success toast        │                             │
         │    "Status updated!"         │                             │
         │                              │                             │
         │ 9. Page reloads              │                             │
         │    (after 1.5 seconds)       │                             │
         │                              │                             │
```

**Result**: 
- ✅ Status updated without full page reload
- ✅ Hiker receives notification
- ✅ Changes reflected immediately

---

## 3. Real-Time Polling System (Hiker Booking List)

```
┌─────────────┐                    ┌──────────────┐                    ┌─────────────────┐
│   Hiker     │                    │   Server     │                    │  Organization   │
│  (Browser)  │                    │ (Controller) │                    │   (Updates)     │
└──────┬──────┘                    └──────┬───────┘                    └────────┬────────┘
       │                                   │                                     │
       │ 1. Load booking index page        │                                     │
       │                                   │                                     │
       │ 2. Store initial booking states   │                                     │
       │    {booking_id: {status, payment}}│                                     │
       │                                   │                                     │
       │ 3. Start polling timer (30s)      │                                     │
       │                                   │                                     │
       │ ... 30 seconds pass ...           │                                     │
       │                                   │                                     │
       │                                   │          (Meanwhile)                │
       │                                   │     Organization updates            │
       │                                   │     booking status                  │
       │                                   │<────────────────────────────────────┤
       │                                   │                                     │
       │ 4. AJAX GET (check updates)       │                                     │
       ├──────────────────────────────────>│                                     │
       │                                   │                                     │
       │                                   │ 5. Query bookings                   │
       │                                   │    Return JSON with                 │
       │                                   │    current states                   │
       │                                   │                                     │
       │ 6. JSON Response                  │                                     │
       │    { bookings: [...] }            │                                     │
       │<──────────────────────────────────┤                                     │
       │                                   │                                     │
       │ 7. Compare with stored states     │                                     │
       │    Detect changes                 │                                     │
       │                                   │                                     │
       │ 8. If changes found:              │                                     │
       │    - Show toast notification      │                                     │
       │    - Update stored states         │                                     │
       │    - Reload page (after 3s)       │                                     │
       │                                   │                                     │
       │ ... Wait 30 seconds ...           │                                     │
       │                                   │                                     │
       │ 9. Repeat from step 4             │                                     │
       │                                   │                                     │
```

**Result**: 
- ✅ Hiker sees updates within 30 seconds
- ✅ No manual refresh needed
- ✅ Visual notification of changes
- ✅ Battery-efficient (stops when page hidden)

---

## 4. Complete Booking Lifecycle with Real-Time Updates

```
TIME    HIKER SIDE                          ORGANIZATION SIDE
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
00:00   Create booking (AJAX)
        ├─> No page reload
        └─> Redirect to payment                
                                                
00:05   Submit payment proof (AJAX)          → Receives notification
        └─> "Under Verification" badge          "New booking received"
                                                
00:10   Viewing booking list                    Reviews payment proof
        └─> Polling every 30s                  Clicks "Verify Payment"
                                                └─> AJAX verification
                                                
00:15   📱 Toast notification!              → Payment verified
        "Payment verified"                      └─> No page reload
        └─> Auto refresh
        
00:16   Status shows: "Verified" ✅             Views booking details
        
01:00   Still on booking list                   Changes status to "Confirmed"
        └─> Polling detects change              └─> AJAX status update
        
01:00   📱 Toast notification!              → Status updated
        "Booking confirmed"                     └─> Hiker notified
        └─> Auto refresh
        
01:01   Download reservation slip
        └─> Ready for hike! 🎉
```

---

## 5. Error Handling Flow

```
┌─────────────┐                    ┌──────────────┐
│   User      │                    │   Server     │
└──────┬──────┘                    └──────┬───────┘
       │                                   │
       │ 1. Submit form (AJAX)             │
       ├──────────────────────────────────>│
       │                                   │
       │                                   │ 2. Validation fails
       │                                   │    OR server error
       │                                   │
       │ 3. JSON Error Response            │
       │    { success: false,              │
       │      message: "Error...",         │
       │      errors: {...} }              │
       │<──────────────────────────────────┤
       │                                   │
       │ 4. Show error toast (red)         │
       │    Keep form state                │
       │    Re-enable submit button        │
       │                                   │
       │ 5. User can retry                 │
       │                                   │
```

---

## Key Features Summary

### 1. **No Page Reloads**
- All forms use AJAX
- Instant feedback
- Better UX

### 2. **Real-Time Notifications**
- Database notifications
- Toast messages
- Auto-dismiss

### 3. **Live Updates**
- 30-second polling
- Smart change detection
- Automatic refresh

### 4. **Resource Efficient**
- Stops when page hidden
- Clean up on unload
- JSON responses only

### 5. **Error Recovery**
- Clear error messages
- Form state preserved
- Retry capability

---

## Technical Specifications

### Polling Configuration
```javascript
// Default: 30 seconds
const POLLING_INTERVAL = 30000; // milliseconds

// Auto-refresh delay after change detected
const REFRESH_DELAY = 3000; // 3 seconds

// Notification auto-dismiss
const NOTIFICATION_TIMEOUT = 8000; // 8 seconds
```

### API Endpoints Used
- `POST /hiker/booking` - Create booking (AJAX)
- `PATCH /hiker/booking/{id}` - Update booking (AJAX)
- `GET /hiker/booking` - Get bookings (AJAX polling)
- `PATCH /org/bookings/{id}/status` - Update status (AJAX)
- `POST /org/bookings/{id}/verify-payment` - Verify payment (AJAX)
- `POST /org/bookings/{id}/reject-payment` - Reject payment (AJAX)

### Headers Required
```
X-Requested-With: XMLHttpRequest
Accept: application/json
```

---

## Browser Compatibility

✅ Chrome 90+
✅ Firefox 88+
✅ Safari 14+
✅ Edge 90+
✅ Mobile browsers (iOS Safari, Chrome Mobile)

**Fallback**: If JavaScript disabled, forms work with traditional POST (page reloads)

---

## Performance Metrics

- **Initial page load**: No change (same as before)
- **Polling overhead**: ~5KB per request every 30s
- **AJAX submission**: ~2KB request, ~1KB response
- **Memory usage**: Minimal (cleanup on unload)
- **Battery impact**: Low (polling stops when hidden)

---

## Security Considerations

1. **CSRF Protection**: Laravel CSRF tokens in all forms
2. **Authentication**: All routes protected by auth middleware
3. **Authorization**: Policy checks on booking ownership
4. **SQL Injection**: Eloquent ORM prevents injection
5. **XSS Protection**: JSON responses, escaped output

---

**Last Updated**: October 4, 2025
**Status**: ✅ Production Ready

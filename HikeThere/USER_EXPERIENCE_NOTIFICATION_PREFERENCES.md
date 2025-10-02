# 👤 User Experience - Notification Preferences

## What Users See

### 1. Preferences Page (`/account/preferences`)

```
┌─────────────────────────────────────────────────────────────────┐
│  Account Preferences                    [Back to Settings]       │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│  🔔 Notification Preferences                                     │
│  Choose how and when you want to be notified. Unchecking        │
│  these will stop notifications of that type.                     │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ☑ Email Notifications              ☑ Push Notifications        │
│    Receive important updates          Get real-time alerts      │
│    via email                          in-app                    │
│                                                                  │
│  ☑ Trail Updates                    ☑ Security Alerts           │
│    Alerts about trail conditions      Important account         │
│    & closures                         security notices          │
│                                                                  │
│  ☐ Newsletter                                                    │
│    Tips, news, and featured trails                              │
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ ℹ️ Note: Push notifications include events, weather      │  │
│  │   updates, bookings, and system messages. Disabling      │  │
│  │   this will stop all in-app notifications except those   │  │
│  │   you specifically enable above.                         │  │
│  └──────────────────────────────────────────────────────────┘  │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│  🔒 Privacy Settings                                             │
│  Control who can see your profile information.                   │
├─────────────────────────────────────────────────────────────────┤
│  Profile Visibility: [Public ▼]                                 │
│  ☐ Show Email Address      ☐ Show Phone Number                  │
│  ☑ Show Location           ☐ Show Birth Date                    │
│  ☑ Show Hiking Preferences                                       │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│  ⚙️ Account Settings                                             │
│  Additional account configuration options.                       │
├─────────────────────────────────────────────────────────────────┤
│  Timezone: [UTC ▼]         Language: [English ▼]                │
│  ☐ Require Two-Factor Authentication for all logins             │
└─────────────────────────────────────────────────────────────────┘

  [💾 Save Preferences]  [📥 Export Data]         [🔄 Reset to Defaults]
```

## User Journey Examples

### Example 1: User Wants Fewer Notifications

**Scenario**: User is receiving too many notifications and wants only critical ones.

**Steps**:
1. Goes to `/account/preferences`
2. Unchecks "Newsletter" ✅
3. Unchecks "Trail Updates" ✅
4. Keeps "Security Alerts" checked ✅
5. Keeps "Push Notifications" checked ✅
6. Clicks "Save Preferences"
7. Sees success message: ✅ "Preferences updated successfully"

**Result**: 
- ❌ No more trail update notifications
- ❌ No more newsletter emails
- ✅ Still gets security alerts
- ✅ Still gets event/weather/booking notifications

---

### Example 2: User Wants Complete Silence

**Scenario**: User is going on vacation and doesn't want ANY notifications.

**Steps**:
1. Goes to `/account/preferences`
2. Unchecks "Push Notifications" ✅
3. Unchecks "Email Notifications" ✅
4. Clicks "Save Preferences"

**Result**: 
- ❌ No push notifications (events, weather, bookings, system)
- ❌ No email notifications
- 🔕 Complete notification silence

---

### Example 3: User Only Wants Trail-Specific Notifications

**Scenario**: User only cares about trail updates and security.

**Steps**:
1. Goes to `/account/preferences`
2. Unchecks "Push Notifications" ✅ (disables general notifications)
3. Checks "Trail Updates" ✅ (specific override)
4. Checks "Security Alerts" ✅ (specific override)
5. Unchecks "Newsletter" ✅
6. Clicks "Save Preferences"

**Result**: 
- ✅ Receives trail updates (specific preference overrides general)
- ✅ Receives security alerts (specific preference overrides general)
- ❌ No event notifications
- ❌ No weather notifications
- ❌ No booking notifications
- ❌ No newsletter

---

## What Happens Behind the Scenes

### When User Saves Preferences

```
User clicks "Save Preferences"
        ↓
Controller validates input
        ↓
Handles checkbox states
  • Checked = true
  • Unchecked = false (not sent by browser, so must check $request->has())
        ↓
Saves to database (user_preferences table)
        ↓
Shows success message
        ↓
User redirected back to preferences page
```

### When System Tries to Send Notification

```
Event occurs (e.g., new trail update)
        ↓
Code calls: $service->sendTrailUpdate($user, $trail, $message)
        ↓
NotificationService checks user preferences
        ↓
If trail_updates = false → Return null (no notification)
If trail_updates = true → Create notification in database
        ↓
Notification appears in user's notification center (if created)
```

---

## Visual Feedback Examples

### ✅ Success State (After Saving)

```
┌─────────────────────────────────────────────────────────────┐
│  ✅ Preferences updated successfully.                        │
└─────────────────────────────────────────────────────────────┘
```

### ⚠️ Warning State (Some Notifications Disabled)

If user has disabled notifications, they might see a badge:

```
┌──────────────────────────────────────────────────────────────┐
│  ⚠️ 2 notification types disabled  [Manage]                  │
└──────────────────────────────────────────────────────────────┘
```

### 📧 Email Notification Example (If Enabled)

```
From: HikeThere <notifications@hikethere.com>
To: user@example.com
Subject: Trail Update: Mt. Batulao

Hi John,

The trail "Mt. Batulao" has been updated:
"Trail temporarily closed due to weather conditions"

View Details: https://hikethere.com/trails/mt-batulao

---
You're receiving this because you have email notifications enabled.
Manage preferences: https://hikethere.com/account/preferences
```

### 🔔 In-App Notification Example (If Enabled)

```
┌─────────────────────────────────────────────┐
│  🔔 Notifications (3)                        │
├─────────────────────────────────────────────┤
│  🏔️ Trail Update: Mt. Batulao               │
│  Trail temporarily closed due to...  [•]    │
│  2 hours ago                                │
├─────────────────────────────────────────────┤
│  🎉 New Event: Sunset Hike                   │
│  Join us for an amazing sunset...   [•]     │
│  5 hours ago                                │
├─────────────────────────────────────────────┤
│  ☀️ Weather Update                           │
│  25° in Manila • 18° in Mt. Pulag   [•]     │
│  1 day ago                                  │
└─────────────────────────────────────────────┘
```

---

## Mobile Experience

### On Mobile Devices

The preferences page is responsive:

```
┌─────────────────────────┐
│  Account Preferences    │
│  [← Back to Settings]   │
├─────────────────────────┤
│                         │
│  🔔 Notification        │
│     Preferences         │
│  ───────────────────    │
│                         │
│  ☑ Email Notifications  │
│    Get updates via      │
│    email                │
│                         │
│  ☑ Push Notifications   │
│    Real-time alerts     │
│                         │
│  ☑ Trail Updates        │
│    Trail conditions     │
│                         │
│  ☑ Security Alerts      │
│    Account security     │
│                         │
│  ☐ Newsletter           │
│    Tips and news        │
│                         │
│  ℹ️ Push notifications  │
│     include events,     │
│     weather, bookings   │
│                         │
├─────────────────────────┤
│  [💾 Save Preferences]  │
└─────────────────────────┘
```

---

## User Benefits

✅ **Control**: Users decide what notifications they want
✅ **Privacy**: Users control their notification experience
✅ **Clarity**: Clear descriptions explain each option
✅ **Flexibility**: Can enable/disable specific types
✅ **Reversible**: Can reset to defaults anytime
✅ **Exportable**: Can export all their preferences (GDPR)

---

## Common User Questions (FAQ)

**Q: What happens to my existing notifications if I disable a type?**
A: Existing notifications remain visible. Only new notifications of that type will be blocked.

**Q: Can I temporarily disable all notifications?**
A: Yes! Uncheck "Push Notifications" and "Email Notifications" to silence all alerts.

**Q: Will I still get security alerts if I disable push notifications?**
A: Yes! Security alerts have their own setting and will still come through if enabled.

**Q: What's the difference between Push and Email notifications?**
A: Push notifications appear in-app (bell icon), while email notifications are sent to your inbox.

**Q: How do I reset to default settings?**
A: Click the "Reset to Defaults" button at the bottom of the preferences page.

**Q: Can I export my preferences?**
A: Yes! Click "Export Data" to download all your account data including preferences.

---

This represents the complete user experience for the notification preferences system! 🎉

# Notification Preferences Flow Diagram

```
┌──────────────────────────────────────────────────────────────────┐
│                         USER INTERFACE                            │
│                    /account/preferences                           │
└───────────────────────────┬──────────────────────────────────────┘
                            │
                            │ User toggles checkboxes
                            │
                            ▼
┌──────────────────────────────────────────────────────────────────┐
│                  PreferencesController                            │
│                     update() method                               │
│                                                                   │
│  • Validates input                                                │
│  • Handles checkbox states (checked/unchecked)                    │
│  • Saves to database                                              │
└───────────────────────────┬──────────────────────────────────────┘
                            │
                            │ Saves preferences
                            ▼
┌──────────────────────────────────────────────────────────────────┐
│                    user_preferences table                         │
│                                                                   │
│  ├── email_notifications: true/false                              │
│  ├── push_notifications: true/false                               │
│  ├── trail_updates: true/false                                    │
│  ├── security_alerts: true/false                                  │
│  └── newsletter: true/false                                       │
└───────────────────────────┬──────────────────────────────────────┘
                            │
                            │ Preferences stored
                            │
┌───────────────────────────┴──────────────────────────────────────┐
│                                                                   │
│              NOTIFICATION SENDING FLOW                            │
│                                                                   │
└───────────────────────────────────────────────────────────────────┘

Event Occurs (e.g., Trail Update)
         │
         ▼
┌──────────────────────────────────────────────────────────────────┐
│                   NotificationService                             │
│                    create() method                                │
│                                                                   │
│  1. Load user preferences                                         │
│  2. Call shouldNotify($preferences, $type)                        │
└───────────────────────────┬──────────────────────────────────────┘
                            │
                            │ Check preferences
                            ▼
┌──────────────────────────────────────────────────────────────────┐
│                   shouldNotify() method                           │
│                                                                   │
│  Map notification type to preference:                             │
│    • trail_update    → trail_updates                              │
│    • security_alert  → security_alerts                            │
│    • newsletter      → newsletter                                 │
│    • new_event       → push_notifications                         │
│    • weather         → push_notifications                         │
│    • booking         → push_notifications                         │
│    • system          → push_notifications                         │
└───────────────────────────┬──────────────────────────────────────┘
                            │
              ┌─────────────┴─────────────┐
              │                           │
              ▼                           ▼
      Preference = TRUE          Preference = FALSE
              │                           │
              ▼                           ▼
┌─────────────────────────┐   ┌─────────────────────────┐
│  Create Notification    │   │  Return null            │
│  Save to database       │   │  (No notification)      │
└─────────────┬───────────┘   └─────────────────────────┘
              │
              ▼
┌──────────────────────────────────────────────────────────────────┐
│                   notifications table                             │
│                                                                   │
│  • user_id                                                        │
│  • type (trail_update, security_alert, etc.)                      │
│  • title                                                          │
│  • message                                                        │
│  • data (JSON)                                                    │
│  • read_at (null = unread)                                        │
└───────────────────────────┬──────────────────────────────────────┘
                            │
                            │ Notification created
                            ▼
┌──────────────────────────────────────────────────────────────────┐
│                      USER INTERFACE                               │
│                   Notification appears                            │
│                   (Bell icon, toast, etc.)                        │
└──────────────────────────────────────────────────────────────────┘


════════════════════════════════════════════════════════════════════

EXAMPLE SCENARIOS:

1. User Disables Trail Updates
   ────────────────────────────
   User: Unchecks "Trail Updates" → Saves
   DB: trail_updates = false
   
   When trail condition changes:
   System: Calls sendTrailUpdate($user, ...)
   NotificationService: Checks trail_updates → false
   Result: ❌ No notification created

2. User Disables Push Notifications
   ─────────────────────────────────
   User: Unchecks "Push Notifications" → Saves
   DB: push_notifications = false
   
   When new event is posted:
   System: Calls sendNewEventNotification($event)
   NotificationService: Checks push_notifications → false
   Result: ❌ No notification created
   
   When trail is updated:
   System: Calls sendTrailUpdate($user, ...)
   NotificationService: Checks trail_updates → true
   Result: ✅ Notification IS created (specific override)

3. User Enables Everything (Default)
   ──────────────────────────────────
   User: Keeps all checkboxes checked → Saves
   DB: All = true
   
   Any notification:
   System: Calls any send...() method
   NotificationService: Checks preference → true
   Result: ✅ Notification created

════════════════════════════════════════════════════════════════════

PREFERENCE HIERARCHY:

1. Specific preference (e.g., trail_updates)
   ↓ Takes priority over ↓
2. General preference (e.g., push_notifications)
   ↓ Takes priority over ↓
3. Default value (true/false)

Example:
- push_notifications = false
- trail_updates = true
→ Trail updates WILL be sent (specific overrides general)

- push_notifications = true
- trail_updates = false
→ Trail updates will NOT be sent (specific overrides general)
```

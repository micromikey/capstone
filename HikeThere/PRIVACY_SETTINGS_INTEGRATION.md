# Privacy Settings Integration

## Overview
The privacy settings system allows users to control who can see their profile and what information is displayed. This system is fully integrated with the user preferences and provides granular control over profile visibility.

## Features

### Profile Visibility Levels

1. **Public** (Default)
   - Profile is visible to all users (logged in or guests)
   - Individual field settings still apply
   - Best for users who want to be discoverable

2. **Private**
   - Profile is only visible to the profile owner
   - All fields are hidden from other users
   - Best for users who want maximum privacy

**Note:** The "Friends Only" option has been removed to simplify privacy management.

### Field-Level Privacy Controls

Users can control visibility of specific profile fields:

1. **Email Address** (`show_email`)
   - Default: Hidden (false)
   - Shows/hides email on profile

2. **Phone Number** (`show_phone`)
   - Default: Hidden (false)
   - Shows/hides phone number on profile

3. **Location** (`show_location`)
   - Default: Visible (true)
   - Shows/hides city/region on profile

4. **Birth Date** (`show_birth_date`)
   - Default: Hidden (false)
   - Shows/hides birthday on profile

5. **Hiking Preferences** (`show_hiking_preferences`)
   - Default: Visible (true)
   - Shows/hides difficulty levels and terrain preferences

## How It Works

### Privacy Hierarchy

```
Profile Visibility (Master Control)
    ↓
    ├─ Public → Check individual field settings
    └─ Private → Hide everything from others
```

### Checking Profile Visibility

```php
use Illuminate\Support\Facades\Auth;

$user = User::find($userId);
$viewer = Auth::user(); // Can be null for guests

// Check if profile is visible
if ($user->isProfileVisibleTo($viewer)) {
    // Show profile
} else {
    // Hide profile or show limited info
}
```

### Checking Field Visibility

```php
// Check if a specific field should be shown
if ($user->shouldShowField('email', $viewer)) {
    echo $user->email;
}

if ($user->shouldShowField('phone', $viewer)) {
    echo $user->phone;
}

if ($user->shouldShowField('location', $viewer)) {
    echo $user->location;
}

if ($user->shouldShowField('birth_date', $viewer)) {
    echo $user->birth_date;
}

if ($user->shouldShowField('hiking_preferences', $viewer)) {
    // Show hiking preferences
}
```

## User Model Methods

### `isProfileVisibleTo($viewer = null)`

Checks if the user's profile should be visible to a viewer.

**Parameters:**
- `$viewer` (User|null): The user viewing the profile, or null for guests

**Returns:** `bool`

**Logic:**
- Public profiles → Always visible
- Private profiles → Only visible to owner
- No preferences → Defaults to public

**Example:**
```php
$user = User::find(1);
$currentUser = Auth::user();

if ($user->isProfileVisibleTo($currentUser)) {
    // Display full profile
}
```

### `shouldShowField($field, $viewer = null)`

Checks if a specific profile field should be shown.

**Parameters:**
- `$field` (string): Field name (email, phone, location, birth_date, hiking_preferences)
- `$viewer` (User|null): The user viewing the profile, or null for guests

**Returns:** `bool`

**Logic:**
1. Owner always sees their own fields
2. If profile is private, hide all fields from others
3. If profile is public, check individual field setting
4. If no preferences, use defaults

**Example:**
```php
$user = User::find(1);
$viewer = Auth::user();

if ($user->shouldShowField('email', $viewer)) {
    echo $user->email; // Show email
}
```

## View Implementation

### Profile View Example

```blade.php
@php
    $viewer = Auth::user();
    $isOwnProfile = $viewer && $viewer->id === $user->id;
@endphp

@if($user->isProfileVisibleTo($viewer))
    <div class="profile">
        <h1>{{ $user->name }}</h1>
        
        @if($user->shouldShowField('email', $viewer))
            <p>Email: {{ $user->email }}</p>
        @endif
        
        @if($user->shouldShowField('phone', $viewer))
            <p>Phone: {{ $user->phone }}</p>
        @endif
        
        @if($user->shouldShowField('location', $viewer))
            <p>Location: {{ $user->location }}</p>
        @endif
        
        @if($user->shouldShowField('birth_date', $viewer))
            <p>Birthday: {{ $user->birth_date->format('F j, Y') }}</p>
        @endif
        
        @if($user->shouldShowField('hiking_preferences', $viewer))
            <div class="hiking-preferences">
                <h3>Hiking Preferences</h3>
                @foreach($user->hiking_preferences ?? [] as $preference)
                    <span class="badge">{{ $preference }}</span>
                @endforeach
            </div>
        @endif
    </div>
@else
    <div class="alert alert-warning">
        This profile is private.
    </div>
@endif
```

## API Implementation

### For API Responses

```php
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function show($id)
    {
        $user = User::findOrFail($id);
        $viewer = Auth::user();
        
        // Check if profile is visible
        if (!$user->isProfileVisibleTo($viewer)) {
            return response()->json([
                'message' => 'This profile is private.'
            ], 403);
        }
        
        // Build response with privacy-aware fields
        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'user_type' => $user->user_type,
        ];
        
        if ($user->shouldShowField('email', $viewer)) {
            $data['email'] = $user->email;
        }
        
        if ($user->shouldShowField('phone', $viewer)) {
            $data['phone'] = $user->phone;
        }
        
        if ($user->shouldShowField('location', $viewer)) {
            $data['location'] = $user->location;
        }
        
        if ($user->shouldShowField('birth_date', $viewer)) {
            $data['birth_date'] = $user->birth_date;
        }
        
        if ($user->shouldShowField('hiking_preferences', $viewer)) {
            $data['hiking_preferences'] = $user->hiking_preferences;
        }
        
        return response()->json($data);
    }
}
```

## Database Schema

```sql
CREATE TABLE user_preferences (
    id BIGINT PRIMARY KEY,
    user_id BIGINT UNIQUE,
    
    -- Profile visibility (master control)
    profile_visibility ENUM('public', 'private') DEFAULT 'public',
    
    -- Field-level controls
    show_email BOOLEAN DEFAULT FALSE,
    show_phone BOOLEAN DEFAULT FALSE,
    show_location BOOLEAN DEFAULT TRUE,
    show_birth_date BOOLEAN DEFAULT FALSE,
    show_hiking_preferences BOOLEAN DEFAULT TRUE,
    
    -- Other preferences...
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## Default Privacy Settings

For new users or users without preferences:

```php
[
    'profile_visibility' => 'public',
    'show_email' => false,
    'show_phone' => false,
    'show_location' => true,
    'show_birth_date' => false,
    'show_hiking_preferences' => true,
]
```

## Privacy Scenarios

### Scenario 1: Public Profile with Selective Fields

**Settings:**
- Profile Visibility: Public
- Show Email: No
- Show Phone: No
- Show Location: Yes
- Show Birth Date: No
- Show Hiking Preferences: Yes

**Result:**
- Profile visible to everyone
- Location and hiking preferences shown
- Email, phone, and birth date hidden

### Scenario 2: Private Profile

**Settings:**
- Profile Visibility: Private
- (All field settings ignored)

**Result:**
- Profile only visible to owner
- All fields hidden from other users
- Owner can still see everything

### Scenario 3: Maximum Visibility

**Settings:**
- Profile Visibility: Public
- All field settings: Yes

**Result:**
- Profile visible to everyone
- All information displayed

### Scenario 4: Owner Viewing Own Profile

**Context:**
- User viewing their own profile

**Result:**
- All fields always visible regardless of settings
- Allows users to see what they've set

## Testing

### Manual Testing

1. **Test Public Profile:**
   - Set profile to Public
   - Uncheck "Show Email"
   - Log out and view profile as guest
   - Email should be hidden

2. **Test Private Profile:**
   - Set profile to Private
   - Log out or use different account
   - Profile should show "This profile is private"

3. **Test Owner View:**
   - Set profile to Private
   - View your own profile
   - Should see all fields

4. **Test Field Controls:**
   - Set profile to Public
   - Toggle individual field settings
   - Verify fields show/hide correctly

### Programmatic Testing

```php
// Test profile visibility
$user = User::factory()->create();
UserPreference::updatePreferences($user->id, [
    'profile_visibility' => 'private',
]);

$otherUser = User::factory()->create();

assert($user->isProfileVisibleTo($user) === true);  // Owner can see
assert($user->isProfileVisibleTo($otherUser) === false);  // Others cannot
assert($user->isProfileVisibleTo(null) === false);  // Guests cannot

// Test field visibility
UserPreference::updatePreferences($user->id, [
    'profile_visibility' => 'public',
    'show_email' => false,
    'show_location' => true,
]);

assert($user->shouldShowField('email', $otherUser) === false);
assert($user->shouldShowField('location', $otherUser) === true);
assert($user->shouldShowField('email', $user) === true);  // Owner always sees
```

## Best Practices

1. **Always check visibility before displaying profiles:**
   ```php
   if ($user->isProfileVisibleTo(Auth::user())) {
       // Show profile
   }
   ```

2. **Use shouldShowField for all sensitive fields:**
   ```php
   if ($user->shouldShowField('email', Auth::user())) {
       echo $user->email;
   }
   ```

3. **Provide clear privacy descriptions to users:**
   - Explain what "Public" and "Private" mean
   - Show examples of what others will see

4. **Respect privacy in all contexts:**
   - Views
   - API responses
   - Search results
   - Notifications

5. **Owner always has full access:**
   - Users should always be able to see their own data
   - Privacy settings don't apply to the owner

## Migration Note

If you have existing data with `profile_visibility = 'friends'`, you'll need to migrate it:

```php
// Migration script
DB::table('user_preferences')
    ->where('profile_visibility', 'friends')
    ->update(['profile_visibility' => 'public']);
```

## Future Enhancements

Potential privacy features to add:

- [ ] Blocklist (hide profile from specific users)
- [ ] Activity privacy (hide recent activity)
- [ ] Search visibility (appear/don't appear in search)
- [ ] Stats privacy (hide trail stats, achievements)
- [ ] Location privacy levels (exact vs approximate)

---

**The privacy settings are now fully functional and integrated!** 🔒

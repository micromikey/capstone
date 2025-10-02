# Event Creation Prompt Implementation

## Overview
After an organization user successfully creates a trail, a modal now prompts them to create an event for that newly added trail.

## Changes Made

### 1. Backend Controller Updates

#### `OrganizationTrailController.php` (store method)
- Modified the redirect after successful trail creation to include additional session data:
  - `new_trail_id`: ID of the newly created trail
  - `new_trail_name`: Name of the newly created trail
  - `show_event_prompt`: Flag to trigger the modal display

```php
return redirect()->route('org.trails.index')
    ->with('success', 'Trail created successfully!')
    ->with('new_trail_id', $trail->id)
    ->with('new_trail_name', $trail->trail_name)
    ->with('show_event_prompt', true);
```

#### `OrganizationEventController.php` (create method)
- Modified to accept a `trail_id` query parameter
- Passes `preselectedTrailId` to the view for pre-selection

```php
public function create(Request $request)
{
    $trails = \App\Models\Trail::where('user_id', Auth::id())->orderBy('trail_name')->get();
    $preselectedTrailId = $request->query('trail_id');
    return view('org.events.create', compact('trails', 'preselectedTrailId'));
}
```

### 2. Frontend View Updates

#### `resources/views/org/trails/index.blade.php`
Added a modal that displays when `show_event_prompt` session flag is present:

**Features:**
- Success checkmark icon with green theme
- Displays the newly created trail name
- Two action buttons:
  - **Create Event**: Links to event creation page with pre-selected trail
  - **Maybe Later**: Closes the modal
- Modal can be closed by:
  - Clicking "Maybe Later" button
  - Pressing ESC key
  - Clicking outside the modal (on overlay)

**Modal Structure:**
```html
@if(session('show_event_prompt'))
<div id="createEventModal" class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Modal content with trail name and action buttons -->
</div>
@endif
```

#### `resources/views/org/events/create.blade.php`
1. **Trail Selection Pre-population:**
   - Modified the trail select dropdown to check for `$preselectedTrailId`
   - Trail is automatically selected if coming from the prompt

```blade
<option value="{{ $trail->id }}" 
    @if(old('trail_id') == $trail->id || (isset($preselectedTrailId) && $preselectedTrailId == $trail->id)) 
        selected 
    @endif>
    {{ $trail->trail_name }}
</option>
```

2. **JavaScript Trigger for Pre-selected Trail:**
   - Added script that triggers when a trail is pre-selected
   - Automatically fires change events to update:
     - Duration preview
     - Trail package preview
     - Event title placeholder
     - Other dependent fields

```javascript
@if(isset($preselectedTrailId))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const trailSelect = document.getElementById('trail_select');
        if (trailSelect && trailSelect.value) {
            trailSelect.dispatchEvent(new Event('change', { bubbles: true }));
            // Additional initialization...
        }
    });
</script>
@endif
```

## User Flow

1. **Organization user creates a new trail** via `org/trails/create`
2. **After successful creation**, they are redirected to `org/trails/index`
3. **Modal automatically appears** with:
   - Success message
   - Trail name confirmation
   - Prompt to create an event
4. **User chooses:**
   - **Create Event**: Taken to event creation page with:
     - Trail already selected
     - Duration and package details auto-populated
     - Event title placeholder auto-generated
   - **Maybe Later**: Modal closes, they remain on trails index

## Benefits

- **Streamlined workflow**: Encourages event creation immediately after trail creation
- **Better UX**: Pre-populates event form, reducing manual data entry
- **Flexible**: Users can opt to create event later without blocking trail creation
- **Maintains context**: Trail information carries over automatically

## Technical Details

### Session Data Flow
```
Trail Creation → Controller Store Method → Session Flash Data → 
Trails Index View → Modal Display → Event Creation Route (with query param) → 
Event Create View → Pre-selected Trail
```

### URL Pattern
When clicking "Create Event" from modal:
```
/org/events/create?trail_id=123
```

### Modal Behavior
- Z-index: 50 (ensures it appears above other content)
- Background overlay with opacity
- Centered on screen
- Responsive design for mobile/desktop
- Keyboard accessible (ESC to close)

## Testing Checklist

- [ ] Create a new trail and verify modal appears
- [ ] Click "Create Event" and verify trail is pre-selected
- [ ] Verify duration and package preview updates automatically
- [ ] Click "Maybe Later" and verify modal closes
- [ ] Press ESC key to close modal
- [ ] Verify old() values still work when form validation fails
- [ ] Test on mobile devices for responsive behavior

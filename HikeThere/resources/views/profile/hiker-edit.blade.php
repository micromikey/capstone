<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Hiker Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                        @if(empty($user->hiking_preferences) || count($user->hiking_preferences ?? []) === 0)
                            <div class="mb-4 p-4 rounded-md bg-yellow-50 border-l-4 border-yellow-300">
                                <p class="text-yellow-800 font-medium">We noticed you haven't set your hiking preferences yet.</p>
                                <p class="text-sm text-yellow-700">Setting these helps us surface better trail recommendations. Please select the activities you enjoy below and save your profile.</p>
                            </div>
                        @endif
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <!-- Profile Picture Section -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Profile Picture</h3>
                            <div class="flex items-center space-x-6">
                                <div class="relative">
                                    @if($user->profile_picture)
                                        <img id="profile-picture-img" src="{{ $user->profile_picture_url }}" 
                                             alt="{{ $user->name }}" 
                                             class="w-24 h-24 rounded-full object-cover border-2 border-gray-200">
                                    @else
                                        <div id="profile-picture-placeholder" role="img" aria-label="{{ $user->name }}'s avatar" class="w-24 h-24 rounded-full object-cover border-2 border-gray-200 bg-gray-200 flex items-center justify-center text-2xl font-bold text-gray-700">
                                            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <input type="file" id="profile_picture_input" name="profile_picture" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#336d66] file:text-white hover:file:bg-[#2a5a54]">
                                    <p class="text-xs text-gray-500 mt-1">JPG, PNG, GIF up to 2MB</p>
                                    <div class="mt-2">
                                        <button type="button" id="clear-new-picture" class="text-sm text-red-500 hover:underline hidden">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Basic Information -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#336d66] focus:border-[#336d66]">
                                    @error('name')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#336d66] focus:border-[#336d66]">
                                    @error('email')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                    <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#336d66] focus:border-[#336d66]">
                                    @error('phone')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                                    <input type="text" name="location" id="location" value="{{ old('location', $user->location) }}" 
                                           placeholder="City, State/Province" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#336d66] focus:border-[#336d66]">
                                    @error('location')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">Birth Date</label>
                                    <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', $user->birth_date ? $user->birth_date->format('Y-m-d') : '') }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#336d66] focus:border-[#336d66]">
                                    @error('birth_date')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                                    <select name="gender" id="gender" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#336d66] focus:border-[#336d66]">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender', $user->gender) === 'other' ? 'selected' : '' }}>Other</option>
                                        <option value="prefer_not_to_say" {{ old('gender', $user->gender) === 'prefer_not_to_say' ? 'selected' : '' }}>Prefer not to say</option>
                                    </select>
                                    @error('gender')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Bio Section -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">About You</h3>
                            <div>
                                <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                                <textarea name="bio" id="bio" rows="4" 
                                          placeholder="Tell us about yourself, your hiking experience, and what you love about the outdoors..." 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#336d66] focus:border-[#336d66]">{{ old('bio', $user->bio) }}</textarea>
                                @error('bio')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Hiking Preferences -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Hiking Preferences</h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                @php
                                    $preferences = [
                                        'Day Hiking', 'Backpacking', 'Trail Running', 'Mountain Biking',
                                        'Rock Climbing', 'Camping', 'Photography', 'Wildlife Watching',
                                        'Solo Hiking', 'Group Hiking', 'Family Hiking', 'Adventure Racing'
                                    ];
                                    $userPreferences = old('hiking_preferences', $user->hiking_preferences ?? []);
                                @endphp
                                
                                @foreach($preferences as $preference)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="hiking_preferences[]" value="{{ $preference }}" 
                                               {{ in_array($preference, $userPreferences) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-[#336d66] focus:ring-[#336d66]">
                                        <span class="ml-2 text-sm text-gray-700">{{ $preference }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('hiking_preferences')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Difficulty Preferences -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Difficulty Preferences</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                @php
                                    $difficultyOptions = ['Easy', 'Moderate', 'Challenging'];
                                    $userDifficulties = old('difficulty_preferences', $user->difficulty_preferences ?? []);
                                @endphp

                                @foreach($difficultyOptions as $opt)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="difficulty_preferences[]" value="{{ $opt }}" 
                                               {{ in_array($opt, $userDifficulties) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-[#336d66] focus:ring-[#336d66]">
                                        <span class="ml-2 text-sm text-gray-700">{{ $opt }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('difficulty_preferences')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Emergency Contact -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Emergency Contact</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-2">Contact Name</label>
                                    <input type="text" name="emergency_contact_name" id="emergency_contact_name" 
                                           value="{{ old('emergency_contact_name', $user->emergency_contact_name) }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#336d66] focus:border-[#336d66]">
                                    @error('emergency_contact_name')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                                    <input type="tel" name="emergency_contact_phone" id="emergency_contact_phone" 
                                           value="{{ old('emergency_contact_phone', $user->emergency_contact_phone) }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#336d66] focus:border-[#336d66]">
                                    @error('emergency_contact_phone')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="emergency_contact_relationship" class="block text-sm font-medium text-gray-700 mb-2">Relationship</label>
                                    <input type="text" name="emergency_contact_relationship" id="emergency_contact_relationship" 
                                           value="{{ old('emergency_contact_relationship', $user->emergency_contact_relationship) }}" 
                                           placeholder="e.g., Spouse, Parent, Friend" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#336d66] focus:border-[#336d66]">
                                    @error('emergency_contact_relationship')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('custom.profile.show') }}" 
                               class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#336d66]">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#336d66] hover:bg-[#2a5a54] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#336d66]">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Adapted from hiker-show preview handlers to ensure consistent behavior
    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) return meta.getAttribute('content');
        const input = document.querySelector('input[name="_token"]');
        return input ? input.value : '';
    }

    // Support both id styles used across show/edit templates
    const input = document.getElementById('profile_picture_input') || document.getElementById('profile-picture-input');
    // no separate preview panel; we preview directly into the main image
    const clearBtn = document.getElementById('clear-new-picture');
    let mainImg = document.getElementById('profile-picture-preview') || document.getElementById('profile-picture-img');
    const deleteBtn = document.getElementById('delete-profile-picture');

    let pendingObjectUrl = null;
    let originalSrc = mainImg ? mainImg.src : null;

    // Track navigation avatars and replaced placeholders so we can restore on clear
    const navOriginals = []; // { el: HTMLImageElement, src: string }
    const navReplacedPlaceholders = []; // { placeholderEl: Element, replacementEl: Element }

    // Helper to update nav avatars similar to hiker-show (stores originals first)
    function updateNavAvatars(tempUrl) {
        try {
            // If this is the first time, capture originals and replace placeholders
            if (navOriginals.length === 0) {
                document.querySelectorAll('.js-profile-avatar').forEach(navImg => {
                    if (navImg && navImg.tagName === 'IMG') {
                        navOriginals.push({ el: navImg, src: navImg.src });
                        navImg.src = tempUrl;
                    }
                });

                document.querySelectorAll('.js-profile-avatar-placeholder').forEach(placeholderEl => {
                    const clone = placeholderEl.cloneNode(true);
                    const navImg = document.createElement('img');
                    navImg.className = (placeholderEl.className || '') + ' js-profile-avatar dynamic-temp-avatar rounded-full object-cover';
                    navImg.src = tempUrl;
                    navImg.alt = document.title || '';
                    placeholderEl.replaceWith(navImg);
                    navReplacedPlaceholders.push({ placeholderEl: clone, replacementEl: navImg });
                });
            } else {
                // Already have originals, just update temporary srcs
                document.querySelectorAll('.js-profile-avatar').forEach(navImg => {
                    if (navImg && navImg.tagName === 'IMG') navImg.src = tempUrl;
                });
            }
        } catch (e) { console.error('Nav preview update failed', e); }
    }

    // Also capture placeholder node (if present) so we can replace it when previewing
    const placeholder = document.getElementById('profile-picture-placeholder');
    const originalPlaceholderClone = placeholder ? placeholder.cloneNode(true) : null;

    if (input) {
        // Prevent double-binding the handler if script runs twice
        if (input.dataset.hikerPreviewBound !== '1') {
            input.dataset.hikerPreviewBound = '1';

            input.addEventListener('change', function() {
            const file = input.files && input.files[0];
            if (!file) return;

            if (!file.type.startsWith('image/')) {
                alert('Please choose an image file.');
                input.value = '';
                return;
            }
            const MAX_BYTES = 2 * 1024 * 1024; // 2MB
            if (file.size > MAX_BYTES) {
                alert('Please choose an image smaller than 2 MB.');
                input.value = '';
                return;
            }

            if (pendingObjectUrl) URL.revokeObjectURL(pendingObjectUrl);
            pendingObjectUrl = URL.createObjectURL(file);

            // Use FileReader to set data URL for main preview (more reliable), but
            // if there's no <img> yet (placeholder present) create one and replace
            try {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    if (mainImg) {
                        mainImg.src = ev.target.result;
                    } else if (placeholder) {
                        // create img element mirroring the styling used in the template
                        const newImg = document.createElement('img');
                        newImg.id = 'profile-picture-img';
                        newImg.className = 'w-24 h-24 rounded-full object-cover border-2 border-gray-200';
                        newImg.alt = document.title || '';
                        newImg.src = ev.target.result;
                        placeholder.replaceWith(newImg);
                        // update reference so subsequent handlers can use it
                        mainImg = newImg;
                    }
                };
                reader.readAsDataURL(file);
            } catch (e) {
                // if FileReader fails, fall back to object URL and ensure an <img> exists
                if (mainImg) {
                    mainImg.src = pendingObjectUrl;
                } else if (placeholder) {
                    const newImg = document.createElement('img');
                    newImg.id = 'profile-picture-img';
                    newImg.className = 'w-24 h-24 rounded-full object-cover border-2 border-gray-200';
                    newImg.alt = document.title || '';
                    newImg.src = pendingObjectUrl;
                    placeholder.replaceWith(newImg);
                    mainImg = newImg;
                }
            }

            // Reveal Clear button so user can restore original image
            if (clearBtn) clearBtn.classList.remove('hidden');

            // Update nav avatars (best-effort) and track originals for restore
            updateNavAvatars(pendingObjectUrl);
        });
        }
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            if (input) input.value = '';
            if (pendingObjectUrl) {
                try { URL.revokeObjectURL(pendingObjectUrl); } catch (e) {}
                pendingObjectUrl = null;
            }

            // Restore main image or placeholder
            if (mainImg && originalSrc) {
                mainImg.src = originalSrc;
            } else if (mainImg && !originalSrc && originalPlaceholderClone) {
                // We created an <img> from a placeholder earlier — remove it and restore placeholder
                const parent = mainImg.parentNode;
                try { mainImg.remove(); } catch (e) {}
                if (parent) parent.prepend(originalPlaceholderClone.cloneNode(true));
                // reset references
                mainImg = document.getElementById('profile-picture-img');
            }

            // Restore navigation avatars/placeholders if we modified them
            try {
                if (navOriginals.length > 0) {
                    navOriginals.forEach(entry => {
                        try { if (entry.el && entry.el.tagName === 'IMG') entry.el.src = entry.src; } catch(e){}
                    });
                    navOriginals.length = 0;
                }

                if (navReplacedPlaceholders.length > 0) {
                    navReplacedPlaceholders.forEach(pair => {
                        try {
                            const { placeholderEl, replacementEl } = pair;
                            if (replacementEl && replacementEl.parentNode) {
                                replacementEl.parentNode.replaceChild(placeholderEl, replacementEl);
                            }
                        } catch(e){}
                    });
                    navReplacedPlaceholders.length = 0;
                }
            } catch (e) {
                console.error('Nav restore failed', e);
            }

            // hide clear button
            clearBtn.classList.add('hidden');
        });
    }

    if (deleteBtn) {
        deleteBtn.addEventListener('click', function() {
            if (!confirm('Are you sure you want to delete your profile picture?')) return;

            const url = "{{ route('profile.picture.delete') }}";
            const token = getCsrfToken();

            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            }).then(response => {
                if (response.ok) return response.json().catch(() => ({}));
                // if non-json 200, allow reload
                if (response.status === 200) return {};
                throw new Error('Delete failed: ' + response.status);
            }).then(() => {
                // reload to let server return updated state/flash
                window.location.reload();
            }).catch(err => {
                console.error(err);
                alert('There was an error deleting your profile picture.');
            });
        });
    }
});
</script>

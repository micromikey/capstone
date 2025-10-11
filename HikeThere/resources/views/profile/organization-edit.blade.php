<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Organization Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('org.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <!-- Profile Picture Section -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Organization Logo</h3>
                            <div class="flex items-center space-x-6">
                                <div class="relative">
                                    @if($user->profile_picture)
                                        <img id="profile-picture-img" src="{{ $user->profile_picture_url }}" 
                                             alt="{{ $user->organization_name }}" 
                                             class="w-24 h-24 rounded-full object-cover border-2 border-gray-200 js-profile-avatar">
                                    @else
                                        <div id="profile-picture-placeholder" role="img" aria-label="{{ $user->organization_name }}'s logo" 
                                             class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center text-2xl font-semibold text-gray-700 border-2 border-gray-200 js-profile-avatar-placeholder">
                                            {{ strtoupper(substr($user->organization_name ?? 'O', 0, 1)) }}
                                        </div>
                                    @endif
                                    
                                    @if($user->profile_picture)
                                        <button type="button" id="delete-profile-picture" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <input type="file" id="profile_picture_input" name="profile_picture" accept="image/*" 
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#336d66] file:text-white hover:file:bg-[#2a5a54]">
                                    <p class="text-xs text-gray-500 mt-1">JPG, PNG, GIF up to 2MB</p>
                                    <button type="button" id="clear-new-picture" class="hidden mt-2 text-xs text-blue-600 hover:text-blue-800 underline">
                                        Clear new picture
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Basic Organization Information -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="organization_name" class="block text-sm font-medium text-gray-700 mb-2">Organization Name</label>
                                    <input type="text" name="organization_name" id="organization_name" 
                                           value="{{ old('organization_name', $user->organization_name) }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#336d66] focus:border-[#336d66]">
                                    @error('organization_name')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                    <input type="email" name="email" id="email" 
                                           value="{{ old('email', $user->email) }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#336d66] focus:border-[#336d66]">
                                    @error('email')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                    <input type="tel" name="phone" id="phone" 
                                           value="{{ old('phone', $organizationProfile->phone ?? '') }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#336d66] focus:border-[#336d66]">
                                    @error('phone')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="website" class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                                    <input type="url" name="website" id="website" 
                                           value="{{ old('website', $organizationProfile->website ?? '') }}" 
                                           placeholder="https://www.example.com" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#336d66] focus:border-[#336d66]">
                                    @error('website')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-6">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                                <textarea name="address" id="address" rows="3" 
                                          placeholder="Enter your complete address" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#336d66] focus:border-[#336d66]">{{ old('address', $organizationProfile->address ?? '') }}</textarea>
                                @error('address')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Organization Description -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">About Your Organization</h3>
                            <div>
                                <label for="organization_description" class="block text-sm font-medium text-gray-700 mb-2">Organization Description</label>
                                <textarea name="organization_description" id="organization_description" rows="4" 
                                          placeholder="Describe your organization, its mission, and what makes it unique..." 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#336d66] focus:border-[#336d66]">{{ old('organization_description', $organizationProfile->organization_description ?? '') }}</textarea>
                                @error('organization_description')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Mission Statement -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Mission & Values</h3>
                            <div>
                                <label for="mission_statement" class="block text-sm font-medium text-gray-700 mb-2">Mission Statement</label>
                                <textarea name="mission_statement" id="mission_statement" rows="3" 
                                          placeholder="What is your organization's mission and core values?" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#336d66] focus:border-[#336d66]">{{ old('mission_statement', $organizationProfile->mission_statement ?? '') }}</textarea>
                                @error('mission_statement')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="operating_hours" class="block text-sm font-medium text-gray-700 mb-2">Operating Hours</label>
                                    <input type="text" name="operating_hours" id="operating_hours" 
                                           value="{{ old('operating_hours', $organizationProfile->operating_hours ?? '') }}" 
                                           placeholder="e.g., Mon-Fri 9AM-5PM" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#336d66] focus:border-[#336d66]">
                                    @error('operating_hours')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="founded_year" class="block text-sm font-medium text-gray-700 mb-2">Founded Year</label>
                                    <input type="text" name="founded_year" id="founded_year" 
                                           value="{{ old('founded_year', $organizationProfile->founded_year ?? '') }}" 
                                           placeholder="e.g., 2020" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#336d66] focus:border-[#336d66]">
                                    @error('founded_year')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="team_size" class="block text-sm font-medium text-gray-700 mb-2">Team Size</label>
                                    <input type="text" name="team_size" id="team_size" 
                                           value="{{ old('team_size', $organizationProfile->team_size ?? '') }}" 
                                           placeholder="e.g., 10-20 employees" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#336d66] focus:border-[#336d66]">
                                    @error('team_size')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-2">Primary Contact Person</label>
                                    <input type="text" name="contact_person" id="contact_person" 
                                           value="{{ old('contact_person', $organizationProfile->contact_person ?? '') }}" 
                                           placeholder="Full name" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#336d66] focus:border-[#336d66]">
                                    @error('contact_person')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="contact_position" class="block text-sm font-medium text-gray-700 mb-2">Contact Position</label>
                                    <input type="text" name="contact_position" id="contact_position" 
                                           value="{{ old('contact_position', $organizationProfile->contact_position ?? '') }}" 
                                           placeholder="e.g., Manager, Director" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#336d66] focus:border-[#336d66]">
                                    @error('contact_position')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Services & Specializations -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Services & Specializations</h3>
                            <div>
                                <label for="services_offered" class="block text-sm font-medium text-gray-700 mb-2">Services Offered</label>
                                <textarea name="services_offered" id="services_offered" rows="3" 
                                          placeholder="What services does your organization provide to hikers?" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#336d66] focus:border-[#336d66]">{{ old('services_offered', $organizationProfile->services_offered ?? '') }}</textarea>
                                @error('services_offered')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('org.profile.show') }}" 
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
    // Adapted from hiker-edit preview handlers to ensure consistent behavior for organization profiles
    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) return meta.getAttribute('content');
        const input = document.querySelector('input[name="_token"]');
        return input ? input.value : '';
    }

    const input = document.getElementById('profile_picture_input');
    const clearBtn = document.getElementById('clear-new-picture');
    let mainImg = document.getElementById('profile-picture-img');
    const deleteBtn = document.getElementById('delete-profile-picture');

    let pendingObjectUrl = null;
    let originalSrc = mainImg ? mainImg.src : null;

    // Track navigation avatars and replaced placeholders so we can restore on clear
    const navOriginals = [];
    const navReplacedPlaceholders = [];

    // Helper to update nav avatars
    function updateNavAvatars(tempUrl) {
        try {
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
                document.querySelectorAll('.js-profile-avatar').forEach(navImg => {
                    if (navImg && navImg.tagName === 'IMG') navImg.src = tempUrl;
                });
            }
        } catch (e) { console.error('Nav preview update failed', e); }
    }

    const placeholder = document.getElementById('profile-picture-placeholder');
    const originalPlaceholderClone = placeholder ? placeholder.cloneNode(true) : null;

    if (input) {
        if (input.dataset.orgPreviewBound !== '1') {
            input.dataset.orgPreviewBound = '1';

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

                try {
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        if (mainImg) {
                            mainImg.src = ev.target.result;
                        } else if (placeholder) {
                            const newImg = document.createElement('img');
                            newImg.id = 'profile-picture-img';
                            newImg.className = 'w-24 h-24 rounded-full object-cover border-2 border-gray-200 js-profile-avatar';
                            newImg.alt = document.title || '';
                            newImg.src = ev.target.result;
                            placeholder.replaceWith(newImg);
                            mainImg = newImg;
                        }
                    };
                    reader.readAsDataURL(file);
                } catch (e) {
                    if (mainImg) {
                        mainImg.src = pendingObjectUrl;
                    } else if (placeholder) {
                        const newImg = document.createElement('img');
                        newImg.id = 'profile-picture-img';
                        newImg.className = 'w-24 h-24 rounded-full object-cover border-2 border-gray-200 js-profile-avatar';
                        newImg.alt = document.title || '';
                        newImg.src = pendingObjectUrl;
                        placeholder.replaceWith(newImg);
                        mainImg = newImg;
                    }
                }

                if (clearBtn) clearBtn.classList.remove('hidden');
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
                const parent = mainImg.parentNode;
                try { mainImg.remove(); } catch (e) {}
                if (parent) parent.prepend(originalPlaceholderClone.cloneNode(true));
                mainImg = document.getElementById('profile-picture-img');
            }

            // Restore navigation avatars/placeholders
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

            clearBtn.classList.add('hidden');
        });
    }

    if (deleteBtn) {
        deleteBtn.addEventListener('click', function() {
            if (!confirm('Are you sure you want to delete your organization logo?')) return;

            const url = "{{ route('org.profile.picture.delete') }}";
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
                if (response.status === 200) return {};
                throw new Error('Delete failed: ' + response.status);
            }).then(() => {
                window.location.reload();
            }).catch(err => {
                console.error(err);
                alert('There was an error deleting your organization logo.');
            });
        });
    }
});
</script>

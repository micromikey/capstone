@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Locations</h1>
    <div class="mb-4 flex justify-end">
        <a href="{{ route('org.trails.create') }}" class="text-sm text-[#336d66] hover:underline">Back to Create Trail</a>
    </div>
    <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Province</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Region</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lat</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lng</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-sm">
                @forelse($locations as $loc)
                    <tr>
                        <td class="px-4 py-2">{{ $loc->name }}</td>
                        <td class="px-4 py-2">{{ $loc->province }}</td>
                        <td class="px-4 py-2">{{ $loc->region }}</td>
                        <td class="px-4 py-2">{{ $loc->latitude }}</td>
                        <td class="px-4 py-2">{{ $loc->longitude }}</td>
                        <td class="px-4 py-2 text-right space-x-2">
                            <button data-edit='@json($loc)' class="editLocation inline-flex items-center px-2 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-500">Edit</button>
                            <form action="{{ route('org.locations.destroy', $loc->slug) }}" method="POST" class="inline" onsubmit="return confirm('Delete this location?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-500">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">No locations yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $locations->links() }}</div>
</div>

<!-- Edit Location Modal -->
<div id="editLocationModal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white w-full max-w-md rounded-lg shadow-xl p-6 relative">
        <h3 class="text-lg font-semibold mb-4">Edit Location</h3>
        <form id="editLocationForm" class="space-y-4">
            @csrf
            @method('PATCH')
            <input type="hidden" name="_slug" id="edit_slug" />
            <div>
                <label class="block text-sm font-medium text-gray-700">Name *</label>
                <input type="text" name="name" id="edit_name" required class="mt-1 w-full border-gray-300 rounded-md"/>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Province *</label>
                    <input type="text" name="province" id="edit_province" required class="mt-1 w-full border-gray-300 rounded-md"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Region *</label>
                    <input type="text" name="region" id="edit_region" required class="mt-1 w-full border-gray-300 rounded-md"/>
                </div>
            </div>
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Search / Pick on Map</label>
                <input type="text" id="edit_location_search" placeholder="Search place, mountain, barangay..." class="w-full border-gray-300 rounded-md" autocomplete="off" />
                <div id="edit_map" class="w-full h-64 rounded-md border border-gray-300"></div>
                <p class="text-[11px] text-gray-500">Drag the marker or click the map. Coordinates update automatically. You can still fine‑tune below.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Latitude *</label>
                    <input type="number" step="0.000001" name="latitude" id="edit_latitude" required class="mt-1 w-full border-gray-300 rounded-md"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Longitude *</label>
                    <input type="number" step="0.000001" name="longitude" id="edit_longitude" required class="mt-1 w-full border-gray-300 rounded-md"/>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="edit_description" rows="2" class="mt-1 w-full border-gray-300 rounded-md"></textarea>
            </div>
            <p id="editLocationError" class="hidden text-xs text-red-600"></p>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" id="cancelEditLocation" class="px-3 py-1.5 text-sm bg-gray-200 rounded hover:bg-gray-300">Cancel</button>
                <button type="submit" class="px-4 py-1.5 text-sm bg-blue-600 text-white rounded hover:bg-blue-500">Update</button>
            </div>
        </form>
        <button type="button" id="closeEditLocation" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">&times;</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('editLocationModal');
    const form = document.getElementById('editLocationForm');
    const errorEl = document.getElementById('editLocationError');
    const closeButtons = [document.getElementById('closeEditLocation'), document.getElementById('cancelEditLocation')];
    let editMap, editMarker, editAutocomplete;
    let mapInitialized = false;
    let scriptLoaded = typeof google !== 'undefined' && google.maps;

    function initEditMap(forceRecenter = false) {
        if (!scriptLoaded) return; // wait until script is ready
        const mapEl = document.getElementById('edit_map');
        if (!mapEl) return;
        // If already initialized just trigger resize and optionally recenter
        if (mapInitialized && editMap) {
            google.maps.event.trigger(editMap, 'resize');
            if (forceRecenter && editMarker) {
                editMap.setCenter(editMarker.getPosition());
            }
            return;
        }
        const latInput = document.getElementById('edit_latitude');
        const lngInput = document.getElementById('edit_longitude');
        const searchInput = document.getElementById('edit_location_search');
        const startLat = parseFloat(latInput.value) || 14.5995; // Manila default
        const startLng = parseFloat(lngInput.value) || 120.9842;
        editMap = new google.maps.Map(mapEl, {
            center: { lat: startLat, lng: startLng },
            zoom: 8,
            mapTypeId: 'terrain'
        });
        editMarker = new google.maps.Marker({
            position: { lat: startLat, lng: startLng },
            map: editMap,
            draggable: true
        });
        // Update inputs on marker drag
        editMarker.addListener('dragend', () => {
            const pos = editMarker.getPosition();
            latInput.value = pos.lat().toFixed(6);
            lngInput.value = pos.lng().toFixed(6);
        });
        // Click to move marker
        editMap.addListener('click', (e) => {
            editMarker.setPosition(e.latLng);
            latInput.value = e.latLng.lat().toFixed(6);
            lngInput.value = e.latLng.lng().toFixed(6);
        });
        // Places Autocomplete
        editAutocomplete = new google.maps.places.Autocomplete(searchInput, {
            fields: ['geometry','address_components','formatted_address'],
            componentRestrictions: { country: 'ph' }
        });
        editAutocomplete.addListener('place_changed', () => {
            const place = editAutocomplete.getPlace();
            if (!place.geometry) return;
            const loc = place.geometry.location;
            editMap.panTo(loc);
            editMap.setZoom(12);
            editMarker.setPosition(loc);
            latInput.value = loc.lat().toFixed(6);
            lngInput.value = loc.lng().toFixed(6);
            // Attempt auto-fill province / region
            if (place.address_components) {
                let province = null, region = null;
                place.address_components.forEach(c => {
                    if (c.types.includes('administrative_area_level_2') && !province) province = c.long_name;
                    if (c.types.includes('administrative_area_level_1') && !region) region = c.long_name;
                });
                if (province && !document.getElementById('edit_province').value) document.getElementById('edit_province').value = province;
                if (region && !document.getElementById('edit_region').value) document.getElementById('edit_region').value = region;
            }
        });
        mapInitialized = true;
    }
    function ensureScriptLoaded(callback) {
        if (scriptLoaded) { callback(); return; }
        if (window.__gmapsLoading) { // queue callback
            window.__gmapsCallbacks = window.__gmapsCallbacks || []; window.__gmapsCallbacks.push(callback); return;
        }
        window.__gmapsLoading = true;
        window.__gmapsCallbacks = [callback];
        const apiKey = document.querySelector('meta[name="google-maps-api-key"]')?.content;
        if (!apiKey) {
            console.warn('Google Maps API key missing. Set GOOGLE_MAPS_API_KEY in .env');
            errorEl.textContent = 'Map unavailable: missing API key.';
            errorEl.classList.remove('hidden');
            return;
        }
        const s = document.createElement('script');
        s.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&libraries=places&v=weekly`;
        s.async = true; s.defer = true;
        s.onload = () => {
            scriptLoaded = true;
            (window.__gmapsCallbacks || []).forEach(cb => { try { cb(); } catch(e){ console.error(e);} });
            window.__gmapsCallbacks = [];
        };
        s.onerror = () => {
            console.error('Failed to load Google Maps API');
            errorEl.textContent = 'Failed to load map. Please retry or check API key.';
            errorEl.classList.remove('hidden');
        };
        document.head.appendChild(s);
    }
    function openModal() { modal.classList.remove('hidden'); modal.classList.add('flex'); }
    function closeModal() { modal.classList.add('hidden'); modal.classList.remove('flex'); form.reset(); errorEl.classList.add('hidden'); }
    closeButtons.forEach(btn => btn?.addEventListener('click', closeModal));
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

    document.querySelectorAll('.editLocation').forEach(btn => {
        btn.addEventListener('click', () => {
            const loc = JSON.parse(btn.getAttribute('data-edit'));
            document.getElementById('edit_slug').value = loc.slug;
            document.getElementById('edit_name').value = loc.name;
            document.getElementById('edit_province').value = loc.province;
            document.getElementById('edit_region').value = loc.region;
            document.getElementById('edit_latitude').value = loc.latitude;
            document.getElementById('edit_longitude').value = loc.longitude;
            document.getElementById('edit_description').value = loc.description ?? '';
            openModal();
            // Ensure script then init (after a short delay so container has dimensions)
            ensureScriptLoaded(() => {
                setTimeout(() => {
                    initEditMap();
                    if (editMarker) {
                        const newPos = { lat: parseFloat(loc.latitude), lng: parseFloat(loc.longitude) };
                        if (!isNaN(newPos.lat) && !isNaN(newPos.lng)) {
                            editMarker.setPosition(newPos);
                            editMap.setCenter(newPos);
                            google.maps.event.trigger(editMap, 'resize');
                        }
                    }
                }, 250);
            });
        });
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        errorEl.classList.add('hidden');
        const slug = document.getElementById('edit_slug').value;
        const url = `{{ url('/org/locations') }}/${slug}`;
        const formData = new FormData(form);
        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            });
            const data = await res.json();
            if (!res.ok || !data.success) throw new Error(data.message || 'Update failed');
            location.reload();
        } catch (err) {
            errorEl.textContent = err.message;
            errorEl.classList.remove('hidden');
        }
    });
});
</script>
@endsection

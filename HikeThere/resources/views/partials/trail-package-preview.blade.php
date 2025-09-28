<div id="trail_package_preview" class="mt-4 p-4 bg-gray-50 border rounded text-sm hidden" aria-live="polite">
    <div class="flex items-start gap-4">
        <img id="preview_image" src="{{ asset('img/default-trail.jpg') }}" alt="Trail image" class="w-28 h-20 object-cover rounded" />
        <div class="flex-1">
            <h3 id="preview_title" class="text-lg font-semibold">Trail Package Preview</h3>
            <p id="preview_summary" class="text-gray-700 mt-2 text-sm"></p>
            <div class="mt-1 text-sm text-gray-600">
                <span id="preview_times" class="block"><strong>Opening Hours:</strong> <span id="preview_opening">—</span> &ndash; <span id="preview_closing">—</span></span>
                <span id="preview_estimated" class="block mt-1"><strong>Estimated time:</strong> <span id="preview_estimated_time">—</span></span>
            </div>
        </div>
        <div id="preview_spinner" class="ml-3" style="display:none;">
            <svg class="animate-spin h-6 w-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
        </div>
    </div>
    <div id="preview_error" class="mt-3 text-sm text-red-600 hidden"></div>
    <div id="preview_details" class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-3">
        <div><strong>Duration:</strong> <span id="preview_duration">—</span></div>
        <div><strong>Price:</strong> <span id="preview_price">—</span></div>
        <div class="md:col-span-2"><strong>Includes:</strong>
            <ul id="preview_inclusions" class="list-disc list-inside text-gray-600 mt-1"></ul>
        </div>
        <div class="md:col-span-2"><strong>Side trips:</strong>
            <ul id="preview_side_trips" class="list-disc list-inside text-gray-600 mt-1"></ul>
        </div>
    </div>
</div>

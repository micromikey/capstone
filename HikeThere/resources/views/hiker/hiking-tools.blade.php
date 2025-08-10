<x-app-layout>
    <div class="bg-gradient-to-br from-slate-50 via-blue-50 to-emerald-50 min-h-screen py-10">
        <div class="max-w-4xl mx-auto px-4 md:px-8">
            <div class="bg-white rounded-2xl shadow-xl border border-emerald-100 p-8">
                <h2 class="text-lg font-semibold text-gray-700 tracking-wide mb-6">HIKING TOOLS</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Build Itineraries Card -->
                    <div class="bg-white border rounded-xl shadow-sm flex flex-col items-center p-4 hover:shadow-lg transition">
                        <div class="relative w-full mb-2">
                            <img src="{{ asset('img/itinerary-map.jpg') }}" alt="Build Itineraries" class="rounded-lg w-full h-32 object-cover">
                            <div class="absolute inset-0 bg-black bg-opacity-40 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xl font-bold drop-shadow-lg">BUILD ITINERARIES</span>
                            </div>
                        </div>
                        <p class="text-gray-700 text-center text-sm mb-2">Build and organize custom hiking itineraries for your adventures.</p>
                        <a href="{{ url('/itineraries') }}" class="text-blue-700 font-semibold underline text-sm">Saved Itineraries</a>
                    </div>
                    <!-- Self Assessment Card -->
                    <div class="bg-white border rounded-xl shadow-sm flex flex-col items-center p-4 hover:shadow-lg transition">
                        <div class="relative w-full mb-2">
                            <img src="{{ asset('img/self-assessment.jpg') }}" alt="Self Assessment" class="rounded-lg w-full h-32 object-cover">
                            <div class="absolute inset-0 bg-black bg-opacity-40 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xl font-bold drop-shadow-lg">SELF ASSESSMENT</span>
                            </div>
                        </div>
                        <p class="text-gray-700 text-center text-sm mb-2">Complete a quick self-assessment to ensure you're ready for your hike.</p>
                        <a href="{{ url('/assessment') }}" class="text-blue-700 font-semibold underline text-sm">View Results</a>
                    </div>
                    <!-- Bookings Card -->
                    <div class="bg-white border rounded-xl shadow-sm flex flex-col items-center p-4 hover:shadow-lg transition">
                        <div class="relative w-full mb-2">
                            <img src="{{ asset('img/bookings-calendar.jpg') }}" alt="Bookings" class="rounded-lg w-full h-32 object-cover">
                            <div class="absolute inset-0 bg-black bg-opacity-40 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xl font-bold drop-shadow-lg">BOOKINGS</span>
                            </div>
                        </div>
                        <p class="text-gray-700 text-center text-sm mb-2">Manage and confirm campsite or guided hike bookings in one place.</p>
                        <a href="{{ url('/bookings') }}" class="text-blue-700 font-semibold underline text-sm">View Schedules</a>
                    </div>
                </div>
                <div class="tools-title text-lg font-semibold text-gray-700 mt-8">More Tools</div>
                <!-- Add more tools or features here if needed -->
            </div>
        </div>
    </div>
</x-app-layout>
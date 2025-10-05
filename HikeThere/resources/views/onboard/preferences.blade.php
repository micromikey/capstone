<x-app-layout>

    <div class="py-12">
        <div class="max-w-7x                    <div class="grid grid-cols-1 gap-6 mb-6 lg:grid-cols-2">
                        <!-- Left: Simple mountain illustration -->
                        <div class="relative bg-gradient-to-br from-green-50 to-white rounded-2xl p-6 flex items-center justify-center overflow-hidden">
                            <div class="w-full h-full min-h-[260px] flex items-center justify-center">
                                {{-- Lightweight CSS-only mountain scene --}}
                                <svg viewBox="0 0 400 300" class="w-full h-full opacity-20" style="max-width: 320px;">
                                    {{-- Mountains --}}
                                    <polygon points="0,250 100,100 200,250" fill="#10b981" opacity="0.3">
                                        <animate attributeName="opacity" values="0.3;0.4;0.3" dur="4s" repeatCount="indefinite"/>
                                    </polygon>
                                    <polygon points="120,250 220,80 320,250" fill="#059669" opacity="0.4">
                                        <animate attributeName="opacity" values="0.4;0.5;0.4" dur="5s" repeatCount="indefinite"/>
                                    </polygon>
                                    <polygon points="200,250 300,120 400,250" fill="#047857" opacity="0.3">
                                        <animate attributeName="opacity" values="0.3;0.4;0.3" dur="6s" repeatCount="indefinite"/>
                                    </polygon>
                                    {{-- Trees --}}
                                    <g opacity="0.3">
                                        <polygon points="80,240 90,220 100,240" fill="#065f46"/>
                                        <polygon points="280,240 290,215 300,240" fill="#065f46"/>
                                        <polygon points="340,240 350,225 360,240" fill="#065f46"/>
                                    </g>
                                    {{-- Hiking trail path --}}
                                    <path d="M 0,260 Q 100,240 200,245 T 400,250" stroke="#10b981" stroke-width="2" fill="none" opacity="0.2" stroke-dasharray="5,5">
                                        <animate attributeName="stroke-dashoffset" from="0" to="10" dur="2s" repeatCount="indefinite"/>
                                    </path>
                                </svg>
                            </div>
                        </div>

                        <!-- Right: stacked preference panels --}}sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
            @if($errors->any())
                <div class="mb-4 text-red-600">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('onboard.preferences.save') }}">
                @csrf

                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Set your hiking preferences</h1>
                        <p class="mt-1 text-sm text-gray-500">Pick activities you enjoy and the difficulty levels you prefer.</p>
                    </div>
                    <div class="text-sm text-gray-500">Select any — your results will be personalized.</div>
                </div>

                <div class="grid grid-cols-1 gap-6 mb-6">
                    @php
                        $options = [
                            'Day Hiking',
                            'Backpacking',
                            'Trail Running',
                            'Mountain Biking',
                            'Rock Climbing',
                            'Camping',
                            'Photography',
                            'Wildlife Watching',
                            'Solo Hiking',
                            'Group Hiking',
                            'Family Hiking',
                            'Adventure Racing',
                        ];
                        // simple emoji/icon mapping for activities
                        $optionIcons = [
                            'Day Hiking' => '🥾',
                            'Backpacking' => '🎒',
                            'Trail Running' => '🏃‍♂️',
                            'Mountain Biking' => '🚵‍♀️',
                            'Rock Climbing' => '🧗‍♀️',
                            'Camping' => '🏕️',
                            'Photography' => '📷',
                            'Wildlife Watching' => '🦅',
                            'Solo Hiking' => '🧭',
                            'Group Hiking' => '👥',
                            'Family Hiking' => '👨‍👩‍👧‍👦',
                            'Adventure Racing' => '🏁',
                        ];
                        $selected = old('hiking_preferences', $user->hiking_preferences ?? []);
                    @endphp

                    @php
                        $difficultySelected = old('difficulty_preferences', $user->difficulty_preferences ?? []);
                        $levels = ['easy', 'moderate', 'challenging'];
                        $levelIcons = [
                            'easy' => '🙂',
                            'moderate' => '😅',
                            'challenging' => '😮‍💨',
                        ];
                    @endphp

                    <div class="grid grid-cols-1 gap-6 mb-6 lg:grid-cols-2">
                        <!-- Left: Visualization / decorative column -->
                        <div class="relative bg-gradient-to-br from-green-50 to-white rounded-2xl p-6 flex items-center justify-center overflow-hidden">
                            <div class="w-full h-full min-h-[260px]">
                                {{-- Inline poly-mountain partial (extracts the SVG and loads its CSS/JS) --}}
                                @include('partials.poly-mountain')
                            </div>
                        </div>

                        <!-- Right: stacked preference panels -->
                        <div class="space-y-4">
                            <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
                                <h3 class="text-sm font-semibold text-gray-700 mb-4">Hiking Activities</h3>
                                <div class="flex flex-wrap gap-3">
                                    @foreach($options as $opt)
                                        @php $isChecked = in_array($opt, (array)$selected); @endphp
                                        <label class="preference-chip inline-flex items-center px-3 py-2 rounded-full border border-gray-100 transition-all duration-150 cursor-pointer" data-value="{{ $opt }}" aria-pressed="{{ $isChecked ? 'true' : 'false' }}" role="button">
                                            <input type="checkbox" name="hiking_preferences[]" value="{{ $opt }}" class="preference-checkbox sr-only" {{ $isChecked ? 'checked' : '' }}>
                                            <svg class="check-icon h-4 w-4 mr-2 opacity-0 transition-opacity duration-150" viewBox="0 0 20 20" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l3 3L15 6"/></svg>
                                            <span class="icon-emoji mr-2 w-5 text-lg flex items-center justify-center" aria-hidden="true">{{ $optionIcons[$opt] ?? '·' }}</span>
                                            <span class="text-sm font-medium">{{ $opt }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
                                <h3 class="text-sm font-semibold text-gray-700 mb-4">Difficulty Preferences</h3>
                                <div class="flex flex-wrap gap-3">
                                    @foreach($levels as $level)
                                        @php $dChecked = in_array($level, (array)$difficultySelected); @endphp
                                        <label class="preference-chip difficulty-chip inline-flex items-center px-3 py-2 rounded-full border border-gray-100 transition-all duration-150 cursor-pointer" data-value="{{ $level }}" aria-pressed="{{ $dChecked ? 'true' : 'false' }}" role="button">
                                            <input type="checkbox" name="difficulty_preferences[]" value="{{ $level }}" class="difficulty-checkbox sr-only" {{ $dChecked ? 'checked' : '' }}>
                                            <svg class="check-icon h-4 w-4 mr-2 opacity-0 transition-opacity duration-150" viewBox="0 0 20 20" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l3 3L15 6"/></svg>
                                            <span class="icon-emoji mr-2 w-5 text-lg flex items-center justify-center" aria-hidden="true">{{ $levelIcons[$level] ?? '·' }}</span>
                                            <span class="text-sm font-medium capitalize">{{ $level }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>

                <div class="flex items-center justify-end">
                    <a href="{{ route('dashboard') }}" class="mr-4 text-sm text-gray-500 hover:underline">Skip for now</a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all duration-300">Save Preferences</button>
                </div>
                </form>
                </div>
            </div>
        </div>
    </div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Activity chips (exclude difficulty chips)
    document.querySelectorAll('.preference-chip:not(.difficulty-chip)').forEach(function (chip) {
        const input = chip.querySelector('.preference-checkbox');
        const icon = chip.querySelector('.check-icon');

        function refresh() {
            // Keep border and size stable; only toggle background and icon opacity to avoid layout shift
            if (input.checked) {
                chip.classList.add('bg-green-50');
                chip.classList.remove('bg-white');
                chip.setAttribute('aria-pressed', 'true');
                if (icon) {
                    icon.classList.remove('opacity-0');
                    icon.classList.add('opacity-100');
                }
            } else {
                chip.classList.remove('bg-green-50');
                chip.classList.add('bg-white');
                chip.setAttribute('aria-pressed', 'false');
                if (icon) {
                    icon.classList.remove('opacity-100');
                    icon.classList.add('opacity-0');
                }
            }
        }

        chip.addEventListener('click', function (e) {
            const targetTag = (e.target && e.target.tagName) ? e.target.tagName.toLowerCase() : '';
            if (targetTag === 'input') {
                // native input click already toggled the checkbox; just refresh UI
                refresh();
                return;
            }
            // prevent native toggling and toggle programmatically so we have full control
            e.preventDefault();
            input.checked = !input.checked;
            refresh();
        });

        chip.addEventListener('keydown', function (e) {
            if (e.key === ' ' || e.key === 'Enter') {
                e.preventDefault();
                input.checked = !input.checked;
                refresh();
            }
        });

        refresh();
    });

    // Difficulty chips (now same design as activities)
    document.querySelectorAll('.difficulty-chip').forEach(function (chip) {
        const input = chip.querySelector('.difficulty-checkbox');
        const icon = chip.querySelector('.check-icon');
        const label = chip.querySelector('span');

        function refresh() {
            // Make difficulty chips match activity chips visually (green selection, no layout shift)
            if (input.checked) {
                chip.classList.add('bg-green-50');
                chip.classList.remove('bg-white');
                // ensure border remains consistent
                chip.setAttribute('aria-pressed', 'true');
                if (icon) {
                    icon.classList.remove('opacity-0');
                    icon.classList.add('opacity-100');
                }
            } else {
                chip.classList.remove('bg-green-50');
                chip.classList.add('bg-white');
                chip.setAttribute('aria-pressed', 'false');
                if (icon) {
                    icon.classList.remove('opacity-100');
                    icon.classList.add('opacity-0');
                }
            }
        }

        chip.addEventListener('click', function (e) {
            const targetTag = (e.target && e.target.tagName) ? e.target.tagName.toLowerCase() : '';
            if (targetTag === 'input') {
                // native input click already toggled the checkbox; mirror UI
                refresh();
                return;
            }
            e.preventDefault();
            input.checked = !input.checked;
            refresh();
        });

        chip.addEventListener('keydown', function (e) {
            if (e.key === ' ' || e.key === 'Enter') {
                e.preventDefault();
                input.checked = !input.checked;
                refresh();
            }
        });

        refresh();
    });
});
</script>
@endpush
</x-app-layout>

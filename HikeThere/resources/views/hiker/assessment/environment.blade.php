<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="space-y-2">
                <h2 class="font-bold text-2xl text-gray-900 leading-tight tracking-tight">
                    PRE-HIKE SELF-ASSESSMENT
                </h2>
                <h1 class="text-gray-600 text-2xl font-medium flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-10 h-10 bg-green-100 text-green-800 text-xl font-bold rounded-full">6</span>
                    Environment
                </h1>
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">
        <form action="{{ route('assessment.environment.store') }}" method="POST" class="space-y-4">
            @csrf

            @php
                $radioClasses = "env-radio w-6 h-6 border-2 border-yellow-400 text-yellow-500 rounded-full focus:ring-2 focus:ring-yellow-400 transition-all duration-150";
                $labelClasses = "flex items-center space-x-3 cursor-pointer hover:bg-yellow-50 rounded px-3 py-2 transition-all duration-150";
            @endphp

            <!-- Header Card -->
            <div class="bg-gradient-to-r from-green-50 to-yellow-50 rounded-xl p-8 border border-green-100">
                <div class="flex items-start gap-4">
                    <div class="p-4 bg-green-100 rounded-lg flex-shrink-0">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 0v10m0 0l4 4m-4-4l-4 4"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-gray-800 mb-3">Environmental Care Assessment</h3>
                        <p class="text-green-700 text-lg mb-4">Protect nature and minimize your impact—responsible hiking keeps trails beautiful for everyone.</p>
                        
                        <!-- Info Section -->
                        <div class="bg-white/60 rounded-lg p-5 border border-green-100">
                            <div class="flex items-start gap-3">
                                <div class="p-2 bg-green-500 rounded-full flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="text-base text-gray-700 leading-relaxed">
                                    <p class="font-medium text-gray-800 mb-2 text-base">Leave No Trace Principles</p>
                                    <p class="text-base">Following these principles helps preserve the environment and ensures a safe, enjoyable experience for all hikers.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Questions -->
            <div class="space-y-4">

                <!-- 7 Leave No Trace Principles Checkbox -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-6">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-3 bg-green-50 rounded-lg">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold text-gray-700 mb-3">Leave No Trace Principles</h3>
                                <p class="text-base text-gray-600 mb-4">Select all items that are part of the 7 Leave No Trace principles:</p>
                            </div>
                        </div>

                        <div class="pl-14">
                            @php
                                $principles = [
                                    'Plan ahead and prepare',
                                    'Travel and camp on durable surfaces',
                                    'Dispose of waste properly',
                                    'Leave what you find',
                                    'Minimize campfire impacts',
                                    'Respect wildlife',
                                    'Be considerate of other visitors',
                                    // Distractors
                                    'Always hike alone',
                                    'Feed wild animals',
                                    'Pick flowers for souvenirs',
                                ];
                            @endphp
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-3">
                                @foreach($principles as $i => $principle)
                                    <label class="inline-flex items-center text-base">
                                        <input type="checkbox" name="principles[]" value="{{ $principle }}" class="mr-3 w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500"
                                            {{ isset($environmentData['principles']) && in_array($principle, $environmentData['principles']) ? 'checked' : '' }}>
                                        {{ $principle }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Environmental Care Practices Questions -->
                @php
                    $questions = [
                        ['label' => 'I pack out all trash, including food scraps, after every hike.', 'name' => 'question_0'],
                        ['label' => 'I stay on marked trails to avoid damaging vegetation.', 'name' => 'question_1'],
                        ['label' => 'I avoid picking plants, rocks, or artifacts from nature.', 'name' => 'question_2'],
                        ['label' => 'I use a portable toilet or dig a proper cathole for human waste.', 'name' => 'question_3'],
                        ['label' => 'I avoid making campfires unless absolutely necessary.', 'name' => 'question_4'],
                        ['label' => 'I keep noise levels low to respect wildlife and other hikers.', 'name' => 'question_5'],
                        ['label' => 'I avoid feeding or approaching wild animals.', 'name' => 'question_6'],
                        ['label' => 'I clean my boots and gear to prevent spreading invasive species.', 'name' => 'question_7'],
                        ['label' => 'I camp at least 200 feet away from lakes and streams.', 'name' => 'question_8'],
                        ['label' => 'I yield the trail to others and maintain courteous behavior.', 'name' => 'question_9'],
                    ];
                @endphp

                @foreach($questions as $q)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-6">
                        <div class="flex items-start gap-4 mb-2">
                            <div class="p-3 bg-green-50 rounded-lg">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <label class="block font-semibold text-gray-800 text-lg leading-relaxed flex-1">
                                {{ $q['label'] }}
                            </label>
                        </div>
                        <div class="pl-14">
                            <x-likert-scale name="{{ $q['name'] }}" type="frequency" :value="$environmentData[$q['name']] ?? null" />
                        </div>
                    </div>
                </div>
                @endforeach

            </div>

            <!-- Action Section -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-8 mt-8">
                <div class="flex justify-between items-center gap-4 flex-wrap">
                    <!-- Back Button -->
                    <div class="flex-shrink-0">
                        <a href="{{ route('assessment.emergency') }}"
                            class="env-btn inline-flex items-center px-10 py-5 font-semibold text-lg rounded-xl shadow-lg transition-all duration-200">
                            <svg class="mr-3 w-6 h-6 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            <span>Back: Emergency Assessment</span>
                        </a>
                    </div>
                    <!-- Message -->
                    <div class="flex-1 text-center">
                        <p class="text-base text-gray-600">
                            Complete your environment assessment to finish your pre-hike safety check and save results to your profile.
                        </p>
                    </div>
                    <!-- Generate Result Button -->
                    <div class="flex-shrink-0">
                        <button type="submit"
                            class="env-btn inline-flex items-center px-10 py-5 font-semibold text-lg rounded-xl shadow-lg transition-all duration-200"
                            style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                            <span>Complete & Save Assessment</span>
                            <svg class="ml-3 w-6 h-6 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <style>
        /* Button styling matches other assessment pages */
        .env-btn {
            color: #fff !important;
            background: linear-gradient(135deg, #e3a746 0%, #f4c430 100%);
            box-shadow: 0 4px 16px 0 rgba(244,196,48,0.10);
            font-size: 1.05rem;
            letter-spacing: 0.01em;
            border: none;
        }
        .env-btn:hover, .env-btn:focus {
            filter: brightness(1.08);
            box-shadow: 0 8px 32px 0 rgba(244,196,48,0.18);
            outline: none;
        }
        .env-btn:active {
            filter: brightness(0.98);
        }
        /* Radio button styling matches other assessment pages */
        .env-radio {
            accent-color: #f4c430;
            border-color: #e3a746;
            background-color: #fff;
            transition: box-shadow 0.2s;
        }
        .env-radio:focus {
            box-shadow: 0 0 0 2px #f4c430;
            outline: none;
        }
        /* Card refinement */
        .bg-white\/60 {
            background: rgba(255,255,255,0.85);
            border-radius: 0.75rem;
            border: 1px solid #bbf7d0;
        }
        .bg-gradient-to-r {
            background: linear-gradient(90deg, #dcfce7 0%, #fef3c7 100%);
        }
        .rounded-xl {
            border-radius: 1rem;
        }
        .shadow-lg {
            box-shadow: 0 8px 32px 0 rgba(34,197,94,0.10);
        }
        /* Question card hover */
        .hover\:shadow-md:hover {
            box-shadow: 0 6px 24px 0 rgba(34,197,94,0.12);
        }
        /* Checkbox refinement */
        input[type="checkbox"]:checked {
            background-color: #10b981;
            border-color: #10b981;
        }
    </style>
</x-app-layout>
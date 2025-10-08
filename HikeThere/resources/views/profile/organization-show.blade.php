<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Organization Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Profile Header -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="relative">
                    <!-- Cover Image Placeholder -->
                    <div class="h-48 bg-gradient-to-r from-[#336d66] to-[#20b6d2]"></div>
                    
                    <!-- Profile Picture and Basic Info -->
                    <div class="absolute bottom-0 left-0 right-0 p-6">
                        <div class="flex items-end space-x-6">
                            <div class="relative">
                                @if($user->profile_picture)
                                    <img src="{{ $user->profile_picture_url }}" 
                                         alt="{{ $user->organization_name }}" 
                                         class="w-32 h-32 rounded-full border-4 border-white shadow-lg object-cover">
                                @else
                                    <div role="img" aria-label="{{ $user->organization_name }}'s logo" 
                                         class="w-32 h-32 rounded-full border-4 border-white shadow-lg bg-gray-200 flex items-center justify-center text-4xl font-semibold text-gray-700">
                                        {{ strtoupper(substr($user->organization_name ?? 'O', 0, 1)) }}
                                    </div>
                                @endif
                                <div class="absolute -bottom-2 -right-2 bg-[#336d66] rounded-full p-2">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 text-white">
                                <h1 class="text-3xl font-bold">{{ $user->organization_name }}</h1>
                                <p class="text-white/90">{{ $user->email }}</p>
                                @if($organizationProfile && $organizationProfile->phone)
                                    <p class="text-white/90 flex items-center mt-2">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        {{ $organizationProfile->phone }}
                                    </p>
                                @endif
                            </div>
                            <div class="text-right text-white">
                                <a href="{{ route('profile.edit') }}" 
                                   class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors">
                                    Edit Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Completion Bar -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Profile Completion</h3>
                        <span class="text-sm text-gray-500">{{ $organizationProfile ? $organizationProfile->profile_completion_percentage : 0 }}% Complete</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-[#336d66] to-[#20b6d2] h-2 rounded-full transition-all duration-300" 
                             style="width: {{ $organizationProfile ? $organizationProfile->profile_completion_percentage : 0 }}%"></div>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">Complete your organization profile to build trust with hikers!</p>
                </div>
            </div>

            <!-- Profile Information Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Organization Information -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-[#336d66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Organization Information
                        </h3>
                        
                        <div class="space-y-4">
                            @if($organizationProfile && $organizationProfile->organization_description)
                                <div>
                                    <h4 class="font-medium text-gray-900 mb-2">Description</h4>
                                    <p class="text-gray-600">{{ $organizationProfile->organization_description }}</p>
                                </div>
                            @endif

                            @if($organizationProfile && $organizationProfile->website)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                    </svg>
                                    <a href="{{ $organizationProfile->website }}" target="_blank" class="text-[#336d66] hover:underline">
                                        {{ $organizationProfile->website }}
                                    </a>
                                </div>
                            @endif

                            @if($organizationProfile && $organizationProfile->mission_statement)
                                <div>
                                    <h4 class="font-medium text-gray-900 mb-2">Mission Statement</h4>
                                    <p class="text-gray-600">{{ $organizationProfile->mission_statement }}</p>
                                </div>
                            @endif

                            @if($organizationProfile && $organizationProfile->services_offered)
                                <div>
                                    <h4 class="font-medium text-gray-900 mb-2">Services Offered</h4>
                                    <p class="text-gray-600">{{ $organizationProfile->services_offered }}</p>
                                </div>
                            @endif
                        </div>

                        @if(!$organizationProfile || (!$organizationProfile->website && !$organizationProfile->mission_statement))
                            <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                                <p class="text-sm text-blue-700">Complete your organization profile by adding missing information.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Contact & Business Information -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-[#20b6d2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Contact & Business Information
                        </h3>
                        
                        <div class="space-y-4">
                            @if($organizationProfile && $organizationProfile->email)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-gray-900">{{ $organizationProfile->email }}</span>
                                </div>
                            @endif

                            @if($organizationProfile && $organizationProfile->phone)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <span class="text-gray-900">{{ $organizationProfile->phone }}</span>
                                </div>
                            @endif

                            @if($organizationProfile && $organizationProfile->address)
                                <div class="flex items-start">
                                    <svg class="w-4 h-4 text-gray-400 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="text-gray-900">{{ $organizationProfile->address }}</span>
                                </div>
                            @endif

                            @if($organizationProfile && $organizationProfile->operating_hours)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-gray-900">{{ $organizationProfile->operating_hours }}</span>
                                </div>
                            @endif

                            @if($organizationProfile && $organizationProfile->contact_person)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span class="text-gray-900">{{ $organizationProfile->contact_person }}</span>
                                    @if($organizationProfile->contact_position)
                                        <span class="text-gray-500 ml-2">({{ $organizationProfile->contact_position }})</span>
                                    @endif
                                </div>
                            @endif

                            @if($organizationProfile && $organizationProfile->founded_year)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-gray-900">Founded in {{ $organizationProfile->founded_year }}</span>
                                </div>
                            @endif

                            @if($organizationProfile && $organizationProfile->team_size)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span class="text-gray-900">{{ $organizationProfile->team_size }} team members</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Specializations -->
                @if($organizationProfile && $organizationProfile->specializations && count($organizationProfile->specializations) > 0)
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Specializations
                        </h3>
                        
                        <div class="flex flex-wrap gap-2">
                            @foreach($organizationProfile->specializations as $specialization)
                                <span class="px-3 py-1 bg-[#336d66]/10 text-[#336d66] rounded-full text-sm">
                                    {{ $specialization }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Account Status -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Account Status
                        </h3>
                        
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Approval Status</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $user->approval_status_text }}
                                </span>
                            </div>
                            
                            @if($user->approved_at)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Approved On</span>
                                <span class="text-gray-900">{{ $user->approved_at->format('F j, Y') }}</span>
                            </div>
                            @endif
                            
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Member Since</span>
                                <span class="text-gray-900">{{ $user->created_at->format('F Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

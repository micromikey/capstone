<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Events') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($events->count())
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <ul class="space-y-4">
                            @foreach($events as $event)
                                <li class="border-b pb-4">
                                    <a href="{{ route('events.show', $event->slug) }}" class="text-lg font-semibold text-[#336d66]">{{ $event->title }}</a>
                                    <div class="text-sm text-gray-500">{{ optional($event->start_at)->toDayDateTimeString() }} @if($event->trail) — {{ $event->trail->trail_name }} @endif</div>
                                </li>
                            @endforeach
                        </ul>

                        <div class="mt-6">{{ $events->links() }}</div>
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-12 text-center">
                    <h3 class="text-lg font-semibold">No upcoming events</h3>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
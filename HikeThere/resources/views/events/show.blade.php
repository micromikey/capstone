<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $event->title }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <p class="text-sm text-gray-500">{{ optional($event->start_at)->toDayDateTimeString() }} @if($event->trail) — {{ $event->trail->trail_name }} @endif</p>
                <div class="mt-4">{!! nl2br(e($event->description)) !!}</div>
            </div>
            <div class="mt-6">
                <a href="{{ route('org.events.index') }}" class="text-sm text-gray-600">&larr; Back to events</a>
            </div>
        </div>
    </div>
</x-app-layout>
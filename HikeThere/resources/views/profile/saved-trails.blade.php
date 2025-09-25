<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Saved Trails</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                @if(isset($favorites) && $favorites->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($favorites as $trail)
                            <div class="border rounded-lg p-4 flex flex-col">
                                <div class="h-40 w-full overflow-hidden rounded-md mb-3">
                                    @if($trail->primaryImage)
                                        <img src="{{ $trail->primaryImage->url }}" alt="{{ $trail->trail_name }}" class="w-full h-full object-cover">
                                    @else
                                        <img src="/img/default-trail.jpg" alt="{{ $trail->trail_name }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <a href="{{ route('trails.show', $trail->slug) }}" class="text-lg font-semibold text-gray-900">{{ $trail->trail_name }}</a>
                                    <div class="text-sm text-gray-600 mt-1">{{ $trail->location?->name ?? '' }} {{ $trail->length ? '· ' . $trail->length . ' km' : '' }}</div>
                                </div>
                                <div class="mt-4 flex items-center justify-between">
                                    <a href="{{ route('trails.show', $trail->slug) }}" class="inline-flex items-center px-3 py-2 bg-emerald-600 text-white rounded">View</a>
                                    <button data-trail-id="{{ $trail->id }}" class="unsave-btn px-3 py-2 bg-red-100 text-red-700 rounded">Remove</button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $favorites->links() }}
                    </div>
                @else
                    <div class="text-center text-gray-600">You have not saved any trails yet.</div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function(){
            document.querySelectorAll('.unsave-btn').forEach(btn => {
                btn.addEventListener('click', async function(){
                    const trailId = this.dataset.trailId;
                    const card = this.closest('.border');
                    this.disabled = true;
                    try {
                        const res = await fetch('/trails/favorite/toggle', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                            body: new URLSearchParams({ trail_id: trailId })
                        });
                        const json = await res.json();
                        if (json.success) {
                            // Remove card and optionally reload page if we need fresh pagination
                            card.remove();
                        } else {
                            alert(json.message || 'Unable to remove saved trail');
                        }
                    } catch (err) {
                        console.error(err);
                        alert('Unable to remove saved trail');
                    } finally {
                        this.disabled = false;
                    }
                });
            });
        });
    </script>
</x-app-layout>

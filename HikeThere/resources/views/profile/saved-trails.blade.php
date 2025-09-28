<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Saved Trails</h2>
    </x-slot>

    <div class="py-8">
        <!-- Clean responsive hero (constrained to main content width) -->
        <section class="mb-6">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-gradient-to-br from-emerald-50 via-emerald-100 to-white py-10 px-6 md:px-8 rounded-lg">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-center">
                        <div>
                            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-emerald-900">Saved Trails</h1>
                            <p class="mt-3 text-base text-emerald-700 max-w-xl">Keep track of trails you want to try or revisit. Manage your saved list from here and explore new routes recommended by the community.</p>

                            <div class="mt-6 flex items-center gap-3">
                                <a href="{{ route('trails.index') }}" class="inline-flex items-center px-5 py-3 bg-emerald-600 text-white rounded-lg shadow hover:bg-emerald-700">Explore trails</a>
                                <a href="{{ route('community.index') }}" class="inline-flex items-center px-4 py-2 border border-emerald-600 text-emerald-700 rounded-lg hover:bg-emerald-50">Community</a>
                            </div>
                        </div>

                        <div class="mt-6 lg:mt-0">
                            <div class="bg-white border border-gray-100 rounded-lg p-4 shadow-sm">
                                <h3 class="text-sm font-semibold text-gray-700">Recently saved</h3>
                                <ul class="mt-3 space-y-2 text-sm text-gray-600">
                                    @if(isset($favorites) && $favorites->count() > 0)
                                        @foreach($favorites->take(3) as $f)
                                            <li class="flex items-start gap-3">
                                                <div class="w-10 h-10 bg-gray-100 rounded overflow-hidden flex-shrink-0">
                                                    @if($f->primaryImage)
                                                        <img src="{{ $f->primaryImage->url }}" alt="{{ $f->trail_name }}" class="w-full h-full object-cover">
                                                    @else
                                                        <img src="/img/default-trail.jpg" alt="" class="w-full h-full object-cover">
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="text-gray-800">{{ $f->trail_name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $f->location?->name ?? 'Unknown' }} {{ $f->length ? '· ' . $f->length . ' km' : '' }}</div>
                                                </div>
                                            </li>
                                        @endforeach
                                    @else
                                        <li class="text-gray-500">You haven't saved any trails yet.</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                @if(isset($favorites) && $favorites->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($favorites as $trail)
                            <article class="trail-card group bg-gray-50 border border-gray-100 rounded-lg overflow-hidden flex flex-col shadow-sm" data-trail-id="{{ $trail->id }}">
                                <a href="{{ route('trails.show', $trail->slug) }}" class="block h-48 w-full overflow-hidden bg-gray-200">
                                    @if($trail->primaryImage)
                                        <img
                                            loading="lazy"
                                            src="{{ $trail->primaryImage->url }}"
                                            alt="{{ $trail->trail_name }}"
                                            class="w-full h-full object-cover transform transition-transform duration-300 group-hover:scale-105"
                                        >
                                    @else
                                        <img loading="lazy" src="/img/default-trail.jpg" alt="{{ $trail->trail_name }}" class="w-full h-full object-cover">
                                    @endif
                                </a>

                                <div class="p-4 flex-1 flex flex-col">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <a href="{{ route('trails.show', $trail->slug) }}" class="text-lg font-semibold text-gray-900 hover:underline">{{ $trail->trail_name }}</a>
                                            <p class="text-sm text-gray-600 mt-1">{{ $trail->location?->name ?? 'Unknown location' }} {{ $trail->length ? '· ' . $trail->length . ' km' : '' }}</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('trails.show', $trail->slug) }}" class="inline-flex items-center px-3 py-2 bg-emerald-600 text-white rounded-md text-sm">View</a>
                                            <button
                                                aria-label="Remove {{ $trail->trail_name }} from saved trails"
                                                data-trail-id="{{ $trail->id }}"
                                                class="unsave-btn inline-flex items-center px-3 py-2 bg-red-50 text-red-700 rounded-md text-sm border border-red-100 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-300"
                                            >
                                                Remove
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mt-3 text-xs text-gray-500">Added on: {{ $trail->pivot->created_at ?? '' }}</div>
                                </div>
                            </article>
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

    <!-- Confirmation modal -->
    <div id="confirmModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40" aria-hidden="true">
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-4" role="dialog" aria-modal="true" aria-labelledby="confirmTitle">
            <h3 id="confirmTitle" class="text-lg font-semibold text-gray-900">Remove saved trail?</h3>
            <p id="confirmBody" class="text-sm text-gray-600 mt-2">Are you sure you want to remove this trail from your saved list? This action can be undone by saving it again.</p>
            <div class="mt-4 flex justify-end gap-2">
                <button id="confirmCancel" class="px-3 py-2 rounded-md bg-gray-100">Cancel</button>
                <button id="confirmRemove" class="px-3 py-2 rounded-md bg-red-600 text-white">Remove</button>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div id="toast" class="fixed bottom-6 right-6 z-50 hidden items-center space-x-3 bg-gray-900 text-white px-4 py-2 rounded-md shadow"> 
        <span id="toastMessage" class="text-sm"></span>
        <button id="toastClose" class="ml-2 text-gray-300 hover:text-white">✕</button>
    </div>

    <script>
        // Small helper: show toast
        function showToast(message, duration = 3000) {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toastMessage');
            toastMessage.textContent = message;
            toast.classList.remove('hidden');
            clearTimeout(window._toastTimeout);
            window._toastTimeout = setTimeout(() => { toast.classList.add('hidden'); }, duration);
        }

        document.addEventListener('DOMContentLoaded', function(){
            let pendingTrailId = null;
            const modal = document.getElementById('confirmModal');
            const confirmRemove = document.getElementById('confirmRemove');
            const confirmCancel = document.getElementById('confirmCancel');

            // open confirmation modal
            document.querySelectorAll('.unsave-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    pendingTrailId = btn.dataset.trailId;
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    modal.setAttribute('aria-hidden', 'false');
                    confirmRemove.focus();
                });
            });

            // Close modal helper
            function closeModal() {
                pendingTrailId = null;
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                modal.setAttribute('aria-hidden', 'true');
            }

            confirmCancel.addEventListener('click', closeModal);

            // allow Escape to close
            document.addEventListener('keydown', (ev) => {
                if (ev.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });

            confirmRemove.addEventListener('click', async () => {
                if (!pendingTrailId) return;
                // optimistic UI: remove card immediately
                const card = document.querySelector(`article[data-trail-id="${pendingTrailId}"]`);
                // disable to avoid double-submit
                confirmRemove.disabled = true;
                try {
                    const res = await fetch('/trails/favorite/toggle', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: new URLSearchParams({ trail_id: pendingTrailId })
                    });
                    const json = await res.json();
                    if (json.success) {
                        if (card) card.remove();
                        showToast('Removed from saved trails');

                        // if there are no more cards on the page, reload so pagination updates
                        if (!document.querySelectorAll('article.trail-card').length) {
                            window.location.reload();
                        }
                    } else {
                        showToast(json.message || 'Unable to remove saved trail');
                    }
                } catch (err) {
                    console.error(err);
                    showToast('Unable to remove saved trail');
                } finally {
                    confirmRemove.disabled = false;
                    closeModal();
                }
            });

            // toast close
            document.getElementById('toastClose').addEventListener('click', () => {
                document.getElementById('toast').classList.add('hidden');
            });
        });
    </script>
</x-app-layout>

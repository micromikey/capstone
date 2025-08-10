<x-app-layout>

  <div class="min-h-screen relative overflow-hidden bg-gradient-to-br from-emerald-50 via-white to-teal-50">
    <div aria-hidden="true" class="pointer-events-none absolute inset-0">
      <div class="absolute -top-24 -left-24 h-80 w-80 rounded-full bg-gradient-to-br from-emerald-300/50 via-teal-300/40 to-cyan-300/40 blur-3xl"></div>
      <div class="absolute top-32 -right-16 h-72 w-72 rounded-full bg-gradient-to-br from-indigo-300/40 via-purple-300/40 to-fuchsia-300/40 blur-3xl"></div>
      <div class="absolute bottom-0 left-1/3 h-64 w-64 rounded-full bg-gradient-to-br from-amber-300/40 via-rose-300/40 to-emerald-300/40 blur-3xl"></div>
    </div>

    <div class="relative z-10 mx-auto max-w-6xl px-4 sm:px-6 py-10">

      <div class="rounded-2xl p-[1px] bg-gradient-to-r from-emerald-300/60 via-cyan-300/60 to-indigo-300/60 shadow-xl">
        <div class="rounded-2xl bg-white/80 px-8 py-10 text-center ring-1 ring-black/5 backdrop-blur-xl">
          <!-- Animated star + halo -->
          <div class="relative mx-auto mb-6 h-[160px] w-[160px]">
            <button id="starBtn" type="button" aria-label="Celebrate" class="absolute inset-0 grid place-items-center group">
              <span id="star" class="text-[140px] leading-none select-none" style="filter: drop-shadow(0 10px 24px rgba(234,179,8,.35));">⭐</span>
              <span class="pointer-events-none absolute inset-0 rounded-full bg-yellow-300/30 blur-2xl group-hover:bg-yellow-300/40 transition"></span>
            </button>
          </div>

          <h1 class="mb-2 text-2xl font-extrabold tracking-tight">
            <span class="bg-gradient-to-r from-emerald-700 via-teal-700 to-cyan-700 bg-clip-text text-transparent">
              Itinerary Generated Successfully!
            </span>
          </h1>
          <p class="mx-auto max-w-2xl text-base text-slate-600">
            Your Mt. Pulag trip plan is ready. Review the details and prepare for your hike!
          </p>

          <!-- Actions-->
          <div class="mt-7 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('itinerary.pdf') }}"
               class="group relative inline-flex items-center justify-center gap-2 overflow-hidden rounded-full bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg ring-1 ring-emerald-400/40 transition active:scale-[.98]">
              <span class="absolute inset-0 translate-x-[-120%] bg-white/20 transition-all duration-500 group-hover:translate-x-[120%]"></span>
              <svg class="h-4 w-4 transition group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
              <span>View PDF</span>
            </a>

            <button id="shareBtn"
              class="inline-flex items-center gap-2 rounded-full bg-blue-500 px-6 py-2.5 text-sm font-semibold text-white shadow-lg transition hover:bg-blue-600 active:scale-[.98]">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 12v0m8-8v0m8 8v0m-8 8v0m0-16v16"/></svg>
              Share
            </button>

            <button id="favBtn"
              class="inline-flex items-center gap-2 rounded-full bg-amber-400 px-6 py-2.5 text-sm font-semibold text-white shadow-lg transition hover:bg-amber-500 active:scale-[.98]">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 0 1 6.364 0L12 7.636l1.318-1.318a4.5 4.5 0 1 1 6.364 6.364L12 21.364l-7.682-7.682a4.5 4.5 0 0 1 0-6.364z"/></svg>
              Favorite
            </button>

            <a href="{{ route('itinerary.generate') }}"
               class="inline-flex items-center gap-2 rounded-full bg-white/80 px-6 py-2.5 text-sm font-semibold text-emerald-700 shadow ring-1 ring-emerald-300/50 backdrop-blur transition hover:bg-white">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
              Back to Planner
            </a>
          </div>
        </div>
      </div>

      <!-- Favorites History -->
      <div class="mt-8 rounded-2xl border border-white/70 bg-white/80 p-5 shadow-xl ring-1 ring-black/5 backdrop-blur">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
          <h2 class="text-base font-semibold text-slate-900">
            <span class="bg-gradient-to-r from-emerald-700 via-teal-700 to-cyan-700 bg-clip-text text-transparent">Favorite History</span>
          </h2>
          <div class="relative">
            <div class="rounded-full bg-gradient-to-r from-emerald-300/60 to-cyan-300/60 p-[1px]">
              <input id="favSearch" type="text" placeholder="Search favorites..."
                class="w-56 rounded-full bg-white px-4 py-2 text-sm placeholder-gray-500 outline-none ring-1 ring-gray-200 focus:ring-2 focus:ring-transparent">
            </div>
            <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-3.5-3.5"/></svg>
          </div>
        </div>

        <div id="favList" class="space-y-3">
          <!-- Entry -->
          <div class="favorite-entry flex items-center justify-between rounded-xl border border-gray-100 bg-white px-5 py-3 shadow-sm transition hover:shadow-md">
            <div class="flex flex-wrap items-center gap-2">
              <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-medium text-emerald-700 ring-1 ring-emerald-200">Mt. Pulag</span>
              <span class="text-sm text-slate-800">Itinerary — July 2025</span>
            </div>
            <div class="flex items-center gap-4 text-xs font-semibold">
              <a href="#" class="text-emerald-600 hover:underline">View</a>
              <button onclick="removeFavorite(this)" class="text-red-600 hover:underline">Remove</button>
            </div>
          </div>

          <div class="favorite-entry flex items-center justify-between rounded-xl border border-gray-100 bg-white px-5 py-3 shadow-sm transition hover:shadow-md">
            <div class="flex flex-wrap items-center gap-2">
              <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-0.5 text-[11px] font-medium text-sky-700 ring-1 ring-sky-200">Mt. Apo</span>
              <span class="text-sm text-slate-800">Expedition — May 2025</span>
            </div>
            <div class="flex items-center gap-4 text-xs font-semibold">
              <a href="#" class="text-emerald-600 hover:underline">View</a>
              <button onclick="removeFavorite(this)" class="text-red-600 hover:underline">Remove</button>
            </div>
          </div>

          <div class="favorite-entry flex items-center justify-between rounded-xl border border-gray-100 bg-white px-5 py-3 shadow-sm transition hover:shadow-md">
            <div class="flex flex-wrap items-center gap-2">
              <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-[11px] font-medium text-amber-700 ring-1 ring-amber-200">Mt. Ugo</span>
              <span class="text-sm text-slate-800">Trail Plan — March 2025</span>
            </div>
            <div class="flex items-center gap-4 text-xs font-semibold">
              <a href="#" class="text-emerald-600 hover:underline">View</a>
              <button onclick="removeFavorite(this)" class="text-red-600 hover:underline">Remove</button>
            </div>
          </div>
        </div>

        <!-- Empty state -->
        <div id="emptyState" class="hidden mt-3 rounded-xl border border-dashed border-gray-200 bg-white p-8 text-center text-gray-500">
          No favorites yet.
        </div>
      </div>

      <!-- Footer -->
      <footer class="mt-8 text-center text-xs text-gray-600">
        <p class="inline-flex items-center gap-1 rounded-full bg-white/70 px-3 py-1 ring-1 ring-gray-200 backdrop-blur">
          &copy; {{ date('Y') }} Hiking Planner • All rights reserved
        </p>
      </footer>
    </div>
  </div>

  <!-- Animations -->
  <style>
    @keyframes floaty { 0%,100% { transform: translateY(0) } 50% { transform: translateY(-10px) } }
    @keyframes pop { 0% { transform: scale(.85); opacity: 0 } 100% { transform: scale(1); opacity: 1 } }
    #star { animation: pop .5s ease-out, floaty 2.6s ease-in-out infinite }
  </style>

  <script>
    // Delete favorite with fade + empty toggle
    function removeFavorite(button) {
      const list = document.getElementById('favList');
      const empty = document.getElementById('emptyState');
      const entry = button.closest('.favorite-entry');
      entry.classList.add('opacity-0','translate-y-1','transition');
      setTimeout(() => {
        entry.remove();
        const anyLeft = Array.from(list.children).some(c => c.classList.contains('favorite-entry'));
        empty.classList.toggle('hidden', anyLeft);
      }, 180);
    }

    // Filter favorites
    const favSearch = document.getElementById('favSearch');
    if (favSearch) {
      favSearch.addEventListener('input', () => {
        const q = favSearch.value.toLowerCase().trim();
        const entries = document.querySelectorAll('#favList .favorite-entry');
        let any = false;
        entries.forEach(e => {
          const show = e.innerText.toLowerCase().includes(q);
          e.style.display = show ? '' : 'none';
          if (show) any = true;
        });
        document.getElementById('emptyState').classList.toggle('hidden', any);
      });
    }

    // Share
    const shareBtn = document.getElementById('shareBtn');
    if (shareBtn) {
      shareBtn.addEventListener('click', () => {
        if (navigator.share) {
          navigator.share({
            title: 'Mt. Pulag Itinerary',
            text: 'Check out my hiking plan!',
            url: window.location.href
          }).catch(()=>{});
        } else {
          alert('Sharing is not supported on your browser.');
        }
      });
    }

    // Favorite visual feedback
    const favBtn = document.getElementById('favBtn');
    if (favBtn) {
      favBtn.addEventListener('click', () => {
        favBtn.classList.add('ring-2','ring-yellow-300');
        setTimeout(()=>favBtn.classList.remove('ring-2','ring-yellow-300'), 400);
      });
    }
  </script>
</x-app-layout>

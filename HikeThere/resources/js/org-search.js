// Organization search functionality
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('org-search-input');
    if (!input) return;

    const dropdown = document.getElementById('org-search-dropdown');
    let timeout = null;
    let focusedIndex = -1;
    let currentResults = [];

    // Accessible roles
    input.setAttribute('role', 'combobox');
    input.setAttribute('aria-autocomplete', 'list');
    input.setAttribute('aria-expanded', 'false');
    dropdown.setAttribute('role', 'listbox');

    function iconHtmlForType(type) {
        // Using Font Awesome class names for organization features
        if (type === 'trail') {
            return `<i class="fa-fw text-base fas fa-hiking text-green-600" aria-hidden="true"></i>`;
        }
        if (type === 'event') {
            return `<i class="fa-fw text-base fas fa-calendar text-blue-600" aria-hidden="true"></i>`;
        }
        if (type === 'booking') {
            return `<i class="fa-fw text-base fas fa-clipboard-list text-indigo-600" aria-hidden="true"></i>`;
        }
        // default
        return `<i class="fa-fw text-base fas fa-search text-gray-600" aria-hidden="true"></i>`;
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>"'`]/g, function (s) {
            return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;","`":"&#96;"})[s];
        });
    }

    function highlightMatch(text, query) {
        if (!text || !query) return escapeHtml(text || '');
        const q = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); // escape regex
        const re = new RegExp('(' + q + ')', 'ig');
        return escapeHtml(text).replace(re, '<span class="bg-yellow-100 text-yellow-800 rounded px-0.5">$1</span>');
    }

    function renderResults(results) {
        if (!dropdown) return;
        dropdown.innerHTML = '';
        focusedIndex = -1;
        currentResults = results || [];

        if (!results || results.length === 0) {
            dropdown.classList.add('hidden');
            input.setAttribute('aria-expanded', 'false');
            return;
        }

        const ul = document.getElementById('org-search-results') || dropdown;
        ul.innerHTML = '';
        results.forEach((item, idx) => {
            const li = document.createElement('li');
            const a = document.createElement('a');
            a.href = item.url || '#';
            a.className = 'flex items-center gap-3 px-3 py-2 hover:bg-gray-50 focus:bg-gray-100';
            a.setAttribute('role', 'option');
            a.setAttribute('data-index', idx);
            a.setAttribute('tabindex', '-1');

            const iconWrap = document.createElement('div');
            iconWrap.className = 'w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 text-center flex-shrink-0';
            iconWrap.innerHTML = iconHtmlForType(item.type);

            const txt = document.createElement('div');
            const title = document.createElement('div');
            title.className = 'text-sm font-medium text-gray-900';
            title.innerHTML = highlightMatch(item.title, input.value.trim());
            const subtitle = document.createElement('div');
            subtitle.className = 'text-xs text-gray-500';
            subtitle.innerHTML = highlightMatch(item.subtitle || '', input.value.trim());

            txt.appendChild(title);
            txt.appendChild(subtitle);

            a.appendChild(iconWrap);
            a.appendChild(txt);
            li.appendChild(a);

            ul.appendChild(li);
        });

        dropdown.classList.remove('hidden');
        input.setAttribute('aria-expanded', 'true');
    }

    input.addEventListener('input', function(e) {
        const q = e.target.value.trim();
        if (timeout) clearTimeout(timeout);
        if (!q) {
            if (dropdown) dropdown.classList.add('hidden');
            return;
        }

        timeout = setTimeout(() => {
            fetch(`/api/org-search?q=${encodeURIComponent(q)}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
                .then(r => {
                    if (!r.ok) {
                        throw new Error(`HTTP error! status: ${r.status}`);
                    }
                    return r.json();
                })
                .then(data => renderResults(data.results || []))
                .catch(err => {
                    console.error('Organization search error', err);
                    // Hide dropdown on error
                    if (dropdown) dropdown.classList.add('hidden');
                });
        }, 300);
    });

    // keyboard navigation
    input.addEventListener('keydown', function(e) {
        if (!currentResults.length) return;
        const items = dropdown.querySelectorAll('[role="option"]');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            focusedIndex = Math.min(focusedIndex + 1, items.length - 1);
            updateFocus(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            focusedIndex = Math.max(focusedIndex - 1, 0);
            updateFocus(items);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (focusedIndex >= 0 && items[focusedIndex]) {
                window.location.href = items[focusedIndex].href;
            } else if (currentResults[0]) {
                // fallback: go to first result
                window.location.href = currentResults[0].url;
            }
        } else if (e.key === 'Escape') {
            dropdown.classList.add('hidden');
            input.setAttribute('aria-expanded', 'false');
        }
    });

    function updateFocus(items) {
        items.forEach((it, i) => {
            if (i === focusedIndex) {
                it.classList.add('bg-gray-100');
                it.scrollIntoView({ block: 'nearest' });
            } else {
                it.classList.remove('bg-gray-100');
            }
        });
    }

    // close on outside click
    document.addEventListener('click', function(e) {
        if (!dropdown) return;
        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
});

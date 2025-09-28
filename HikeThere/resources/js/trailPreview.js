// Lightweight trail package preview initializer
// Formats times for Philippines timezone (Asia/Manila)
function formatTimeForPH(raw) {
    if (!raw && raw !== 0) return '—';
    // If only a time string like HH:MM or HH:MM:SS, parse as local time (avoid Date() treating it as UTC)
    const m = String(raw).match(/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/);
    if (m) {
        let hh = parseInt(m[1], 10);
        const mm = m[2];
        const ampm = hh >= 12 ? 'PM' : 'AM';
        hh = hh % 12; if (hh === 0) hh = 12;
        return `${hh}:${mm} ${ampm}`;
    }

    // If ISO datetime or timestamp-like
    try {
        const maybeDate = new Date(raw);
        if (!isNaN(maybeDate.getTime())) {
            return maybeDate.toLocaleTimeString('en-PH', { hour: 'numeric', minute: '2-digit', hour12: true, timeZone: 'Asia/Manila' });
        }
    } catch (e) {
        // fall through
    }

    // otherwise, return as-is
    return String(raw);
}

export function initializeTrailPreview(trailSelectId = 'trail_select') {
    const defaultTrailImage = document.querySelector('meta[name="app-url"]')?.getAttribute('content') ? '' : '';
    const trailSelect = document.getElementById(trailSelectId);
    const packagePreview = document.getElementById('trail_package_preview');
    const previewTitle = document.getElementById('preview_title');
    const previewSummary = document.getElementById('preview_summary');
    const previewDuration = document.getElementById('preview_duration');
    const previewPrice = document.getElementById('preview_price');
    const previewInclusions = document.getElementById('preview_inclusions');
    const previewSideTrips = document.getElementById('preview_side_trips');
    const previewSpinner = document.getElementById('preview_spinner');
    const previewError = document.getElementById('preview_error');
    const previewImage = document.getElementById('preview_image');

    async function fetchAndPopulate(trailId) {
        if (!trailId) {
            packagePreview?.classList?.add('hidden');
            return;
        }
        try {
            if (previewSpinner) previewSpinner.style.display = '';
            if (previewError) previewError.classList.add('hidden');

            const res = await fetch(`${window.location.origin}/hiker/api/trail/${trailId}/package`, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });

            if (previewSpinner) previewSpinner.style.display = 'none';
            if (!res.ok) {
                if (previewError) {
                    previewError.textContent = res.status === 403 ? 'You must follow this organization to view package details.' : 'Unable to load package details. (Error ' + res.status + ')';
                    previewError.classList.remove('hidden');
                }
                return;
            }

            const pkg = await res.json();
            packagePreview?.classList?.remove('hidden');
            if (previewTitle) previewTitle.textContent = pkg.trail_name || 'Trail Package Preview';
            if (previewSummary) previewSummary.textContent = pkg.summary || pkg.description || '';
            if (previewDuration) previewDuration.textContent = pkg.duration ?? '—';
            if (previewPrice) previewPrice.textContent = pkg.price ? (pkg.price + ' PHP') : 'Free / N/A';

            if (previewImage) previewImage.src = pkg.image || previewImage.src || '/img/default-trail.jpg';

            // opening/closing/estimated
            const opening = pkg.opening_time_formatted ?? pkg.opening_time ?? null;
            const closing = pkg.closing_time_formatted ?? pkg.closing_time ?? null;
            const estFormatted = pkg.estimated_time_formatted ?? null;
            const estRaw = pkg.estimated_time ?? null;
            const openingEl = document.getElementById('preview_opening');
            const closingEl = document.getElementById('preview_closing');
            const estEl = document.getElementById('preview_estimated_time');
            if (openingEl) openingEl.textContent = formatTimeForPH(opening);
            if (closingEl) closingEl.textContent = formatTimeForPH(closing);
            if (estEl) estEl.textContent = estFormatted || (estRaw ? (estRaw + ' m') : '—');

            // inclusions
            if (previewInclusions) {
                previewInclusions.innerHTML = '';
                if (pkg.package_inclusions && Array.isArray(pkg.package_inclusions) && pkg.package_inclusions.length) {
                    pkg.package_inclusions.forEach(i => { const li = document.createElement('li'); li.textContent = i; previewInclusions.appendChild(li); });
                } else if (pkg.package_inclusions && typeof pkg.package_inclusions === 'string') {
                    const li = document.createElement('li'); li.textContent = pkg.package_inclusions; previewInclusions.appendChild(li);
                } else {
                    const li = document.createElement('li'); li.textContent = '—'; previewInclusions.appendChild(li);
                }
            }

            if (previewSideTrips) {
                previewSideTrips.innerHTML = '';
                if (pkg.side_trips && Array.isArray(pkg.side_trips) && pkg.side_trips.length) {
                    pkg.side_trips.forEach(i => { const li = document.createElement('li'); li.textContent = i; previewSideTrips.appendChild(li); });
                } else if (pkg.side_trips && typeof pkg.side_trips === 'string') {
                    const li = document.createElement('li'); li.textContent = pkg.side_trips; previewSideTrips.appendChild(li);
                } else {
                    const li = document.createElement('li'); li.textContent = '—'; previewSideTrips.appendChild(li);
                }
            }
        } catch (err) {
            if (previewSpinner) previewSpinner.style.display = 'none';
            if (previewError) {
                previewError.textContent = 'Unable to load package details.';
                previewError.classList.remove('hidden');
            }
            console.error(err);
        }
    }

    // attach listeners
    trailSelect?.addEventListener('change', function() {
        fetchAndPopulate(this.value);
    });

    // If there's a selected value on load, populate
    if (trailSelect?.value) fetchAndPopulate(trailSelect.value);

    return { fetchAndPopulate };
}

// default export for convenience
export default initializeTrailPreview;

// expose helper for other inline scripts
if (typeof window !== 'undefined') {
    window.formatTimeForPH = formatTimeForPH;
}

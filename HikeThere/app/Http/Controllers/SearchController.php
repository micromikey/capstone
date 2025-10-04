<?php

namespace App\Http\Controllers;

use App\Models\Trail;
use App\Models\Location;
use App\Models\OrganizationProfile;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Search header endpoint that returns mixed results for trails, organizations, and locations.
     * Returns JSON array of results with 'type' => 'trail'|'organization'|'location',
     * 'title', 'subtitle', 'icon' (frontend chooses icon), and 'url'.
     */
    public function headerSearch(Request $request)
    {
        $q = trim($request->get('q', ''));

        if ($q === '') {
            return response()->json([ 'results' => [] ]);
        }

        $results = collect();

        $queryLower = mb_strtolower($q);

        $addResult = function($type, $title, $subtitle, $url, $id, $weight) use (&$results, $queryLower) {
            $titleLower = mb_strtolower($title);
            $score = 0;

            // exact substring match
            if (strpos($titleLower, $queryLower) !== false) {
                $score += 50;
                // prefix matches get bonus
                if (strpos($titleLower, $queryLower) === 0) {
                    $score += 20;
                }
            }

            // subtitle match
            if ($subtitle && mb_stripos($subtitle, $queryLower) !== false) {
                $score += 15;
            }

            // Fuzzy: levenshtein distance normalized
            $lev = levenshtein($queryLower, $titleLower);
            if ($lev > 0 && $lev <= 5) {
                $score += max(0, 10 - $lev * 2);
            }

            // length normalization (short titles are easier to match)
            $score = $score / (1 + (mb_strlen($titleLower) / 100));

            $results->push([
                'type' => $type,
                'title' => $title,
                'subtitle' => $subtitle,
                'url' => $url,
                'id' => $id,
                'raw_score' => $score,
                'weight' => $weight,
                'score' => $score * $weight,
            ]);
        };

        // Fetch candidates with broad where clauses (not too many rows)
        // NOTE: not all legacy columns exist on the `trails` table. Avoid
        // referencing an unexistent `name` column which causes SQL errors.
        $trailCandidates = Trail::query()
            ->active()
            ->with('location')
            ->where(function($w) use ($q) {
                $w->where('trail_name', 'like', "%{$q}%")
                  ->orWhere('mountain_name', 'like', "%{$q}%")
                  // fallback to matching via related location name
                  ->orWhereHas('location', function($l) use ($q) {
                      $l->where('name', 'like', "%{$q}%");
                  });
            })
            ->limit(12)
            ->get();

        foreach ($trailCandidates as $trail) {
            $title = $trail->trail_name ?? $trail->mountain_name ?? $trail->name ?? 'Trail';
            $subtitle = optional($trail->location)->name;
            // route generation can sometimes fail if route parameters differ
            // (robustify by catching exceptions and falling back to null)
            try {
                $url = route('trails.show', $trail);
            } catch (\Exception $e) {
                $url = null;
            }
            $addResult('trail', $title, $subtitle, $url, $trail->id, 3);
        }

        if (class_exists(OrganizationProfile::class)) {
            // Prefer the explicit organization fields when available. Some profiles
            // store the business/display name in `organization_name` while `name`
            // may refer to a contact/handler. Search and display using the org fields.
            $orgCandidates = OrganizationProfile::query()
                ->where(function($qBuilder) use ($q) {
                    $qBuilder->where('organization_name', 'like', "%{$q}%")
                             ->orWhere('organization_description', 'like', "%{$q}%")
                             ->orWhere('name', 'like', "%{$q}%");
                })
                ->limit(8)
                ->get();

            foreach ($orgCandidates as $org) {
                // Show the organization/display name first, fall back to handler name
                $title = $org->organization_name ?: $org->name ?: 'Organization';
                $subtitle = $org->organization_description ?? $org->description ?? '';
                // try to route to community organization show (be defensive)
                try {
                    $url = route('community.organization.show', $org->organization ?? $org);
                } catch (\Exception $e) {
                    $url = null;
                }
                $addResult('organization', $title, $subtitle, $url, $org->id, 2);
            }
        }

        $locCandidates = Location::query()
            ->where('name', 'like', "%{$q}%")
            ->limit(8)
            ->get();

        foreach ($locCandidates as $loc) {
            $title = $loc->name;
            $subtitle = $loc->province ?? $loc->region;
            try {
                $url = route('explore', ['location' => $loc->slug]);
            } catch (\Exception $e) {
                $url = null;
            }
            $addResult('location', $title, $subtitle, $url, $loc->id, 1);
        }

        // Sort by weighted score desc, then raw_score desc
        $sorted = $results->sortByDesc(function($item) {
            return [$item['score'], $item['raw_score']];
        })->values();

        // Take top 10 and drop scoring metadata
        $final = $sorted->slice(0, 10)->map(function($r) {
            return [
                'type' => $r['type'],
                'title' => $r['title'],
                'subtitle' => $r['subtitle'],
                'url' => $r['url'],
                'id' => $r['id'],
            ];
        });

        return response()->json([ 'results' => $final ]);
    }

    /**
     * Search endpoint for organization users to search their own trails, events, and bookings
     * Returns JSON array of results with 'type' => 'trail'|'event'|'booking',
     * 'title', 'subtitle', and 'url'.
     */
    public function orgSearch(Request $request)
    {
        $q = trim($request->get('q', ''));

        if ($q === '' || !auth()->check()) {
            return response()->json([ 'results' => [] ]);
        }

        $user = auth()->user();
        $results = collect();
        $queryLower = mb_strtolower($q);

        $addResult = function($type, $title, $subtitle, $url, $id, $weight) use (&$results, $queryLower) {
            $titleLower = mb_strtolower($title);
            $score = 0;

            // exact substring match
            if (strpos($titleLower, $queryLower) !== false) {
                $score += 50;
                // prefix matches get bonus
                if (strpos($titleLower, $queryLower) === 0) {
                    $score += 20;
                }
            }

            // subtitle match
            if ($subtitle && mb_stripos($subtitle, $queryLower) !== false) {
                $score += 15;
            }

            // Fuzzy: levenshtein distance normalized
            $lev = levenshtein($queryLower, $titleLower);
            if ($lev > 0 && $lev <= 5) {
                $score += max(0, 10 - $lev * 2);
            }

            // length normalization
            $score = $score / (1 + (mb_strlen($titleLower) / 100));

            $results->push([
                'type' => $type,
                'title' => $title,
                'subtitle' => $subtitle,
                'url' => $url,
                'id' => $id,
                'raw_score' => $score,
                'weight' => $weight,
                'score' => $score * $weight,
            ]);
        };

        // Search trails belonging to this organization
        $trailCandidates = \App\Models\Trail::query()
            ->where('user_id', $user->id)
            ->where(function($w) use ($q) {
                $w->where('trail_name', 'like', "%{$q}%")
                  ->orWhere('mountain_name', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get();

        foreach ($trailCandidates as $trail) {
            $title = $trail->trail_name ?? $trail->mountain_name ?? 'Trail';
            $subtitle = $trail->difficulty ?? '';
            try {
                $url = route('org.trails.edit', $trail);
            } catch (\Exception $e) {
                $url = null;
            }
            $addResult('trail', $title, $subtitle, $url, $trail->id, 3);
        }

        // Search events belonging to this organization
        $eventCandidates = \App\Models\Event::query()
            ->where('user_id', $user->id)
            ->where(function($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                  ->orWhere('description', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get();

        foreach ($eventCandidates as $event) {
            $title = $event->title ?? 'Event';
            $subtitle = $event->start_at ? $event->start_at->format('M d, Y') : '';
            try {
                $url = route('org.events.edit', $event);
            } catch (\Exception $e) {
                $url = null;
            }
            $addResult('event', $title, $subtitle, $url, $event->id, 3);
        }

        // Search bookings for this organization's trails
        $bookingCandidates = \App\Models\Booking::query()
            ->whereHas('event.trail', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where(function($w) use ($q) {
                // Search by booking ID (numeric) or user info
                if (is_numeric($q)) {
                    $w->where('id', '=', $q);
                }
                $w->orWhereHas('user', function($u) use ($q) {
                    $u->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->with(['user', 'event.trail'])
            ->limit(10)
            ->get();

        foreach ($bookingCandidates as $booking) {
            $title = 'Booking #' . $booking->id;
            $subtitle = ($booking->user->name ?? 'Guest') . ' - ' . ($booking->event->trail->trail_name ?? '');
            try {
                $url = route('org.bookings.show', $booking);
            } catch (\Exception $e) {
                $url = null;
            }
            $addResult('booking', $title, $subtitle, $url, $booking->id, 2);
        }

        // Sort by weighted score desc
        $sorted = $results->sortByDesc(function($item) {
            return [$item['score'], $item['raw_score']];
        })->values();

        // Take top 10 and drop scoring metadata
        $final = $sorted->slice(0, 10)->map(function($r) {
            return [
                'type' => $r['type'],
                'title' => $r['title'],
                'subtitle' => $r['subtitle'],
                'url' => $r['url'],
                'id' => $r['id'],
            ];
        });

        return response()->json([ 'results' => $final ]);
    }

    /**
     * Display search results page for organization users
     */
    public function orgSearchPage(Request $request)
    {
        $query = $request->get('q', '');
        // This would render a search results page
        // For now, just redirect to dashboard if no query
        if (empty($query)) {
            return redirect()->route('org.dashboard');
        }
        
        return view('org.search', ['query' => $query]);
    }
}

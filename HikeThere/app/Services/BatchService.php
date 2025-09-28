<?php

namespace App\Services;

use App\Models\TrailPackage;
use App\Models\Batch;
use Carbon\Carbon;
use Illuminate\Support\Str;

class BatchService
{
    /**
     * Sync batches for the given TrailPackage for the next $days days.
     * If opening_time/closing_time are available, create multiple slots per day.
     * Otherwise create a single undated batch per day.
     *
     * @param TrailPackage $package
     * @param int $days
     * @return void
     */
    public function syncForPackage(TrailPackage $package, int $days = 30)
    {
        // Defensive: ensure package is associated with a trail
        if (! $package->trail_id) {
            return;
        }

        // Remove existing future batches for this package (keep historical past batches)
        Batch::where('trail_package_id', $package->id)
            ->where('starts_at', '>=', now()->startOfDay())
            ->delete();

        // Parse duration: support numeric (hours) or H:MM
        $durationHours = $this->parseDurationToHours($package->duration);

        // capacity: prefer package->capacity if available else default
        $capacity = $package->capacity ?? 10;

        // If times provided, use them as daily window; otherwise create 1 undated batch per day.
        $hasWindow = !empty($package->opening_time) && !empty($package->closing_time);

        for ($i = 0; $i < $days; $i++) {
            $date = Carbon::today()->addDays($i);

            if ($hasWindow && $durationHours > 0) {
                try {
                    $start = Carbon::parse($package->opening_time)->setDate($date->year, $date->month, $date->day);
                    $endWindow = Carbon::parse($package->closing_time)->setDate($date->year, $date->month, $date->day);

                    $slotLength = intval(round($durationHours * 60)); // minutes

                    // create slots until we can't fit another full slot
                    while ($start->copy()->addMinutes($slotLength) <= $endWindow) {
                        $slotEnd = $start->copy()->addMinutes($slotLength);

                        Batch::create([
                            'trail_id' => $package->trail_id,
                            'trail_package_id' => $package->id,
                            'name' => Str::slug($package->duration) . ' ' . $start->toDateTimeString(),
                            'capacity' => $capacity,
                            'starts_at' => $start,
                            'ends_at' => $slotEnd,
                        ]);

                        // advance start to next slot
                        $start = $slotEnd->copy();
                    }
                } catch (\Exception $e) {
                    // fallback to a single undated batch for this date
                    Batch::create([
                        'trail_id' => $package->trail_id,
                        'trail_package_id' => $package->id,
                        'name' => $date->toDateString() . ' batch',
                        'capacity' => $capacity,
                        'starts_at' => null,
                        'ends_at' => null,
                    ]);
                }
            } else {
                // single batch without times
                Batch::create([
                    'trail_id' => $package->trail_id,
                    'trail_package_id' => $package->id,
                    'name' => $date->toDateString() . ' batch',
                    'capacity' => $capacity,
                    'starts_at' => null,
                    'ends_at' => null,
                ]);
            }
        }
    }

    /**
     * Very small heuristic parser: accepts a numeric string (hours), or strings like '4:30' (hours:minutes),
     * or '1d' for days. Returns hours as float. Returns 0 on failure.
     */
    protected function parseDurationToHours($duration): float
    {
        if (!$duration) return 0;

        $duration = trim((string)$duration);

        // numeric (hours)
        if (is_numeric($duration)) {
            return floatval($duration);
        }

        // HH:MM
        if (preg_match('/^(\d+):(\d{1,2})$/', $duration, $m)) {
            $h = intval($m[1]);
            $min = intval($m[2]);
            return $h + ($min / 60);
        }

        // Xd (days)
        if (preg_match('/^(\d+)\s*d$/i', $duration, $m)) {
            return intval($m[1]) * 24;
        }

        return 0;
    }
}

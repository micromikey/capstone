<?php

namespace App\Services;

use App\Models\Itinerary;
use Carbon\Carbon;
use Illuminate\Support\Str;

class IcalService
{
    /**
     * Generate iCal (.ics) content for an itinerary
     *
     * @param Itinerary $itinerary
     * @return string
     */
    public function generate(Itinerary $itinerary): string
    {
        $events = $this->buildEvents($itinerary);
        
        return $this->buildIcalContent($events, $itinerary);
    }

    /**
     * Build individual events from itinerary days and activities
     *
     * @param Itinerary $itinerary
     * @return array
     */
    protected function buildEvents(Itinerary $itinerary): array
    {
        $events = [];
        
        // Load itinerary with days and activities
        $itinerary->load(['days.activities', 'trail.location']);
        
        $startDate = Carbon::parse($itinerary->start_date ?? now());
        
        // If we have structured days and activities, use those
        if ($itinerary->days->isNotEmpty()) {
            foreach ($itinerary->days as $day) {
                $dayDate = $startDate->copy()->addDays($day->day_index);
                
                foreach ($day->activities as $activity) {
                    $events[] = $this->buildActivityEvent($activity, $dayDate, $itinerary);
                }
            }
        } 
        // Otherwise, create events from daily_schedule JSON
        elseif (!empty($itinerary->daily_schedule)) {
            foreach ($itinerary->daily_schedule as $dayIndex => $dayData) {
                $dayDate = $startDate->copy()->addDays($dayIndex);
                
                if (isset($dayData['activities']) && is_array($dayData['activities'])) {
                    foreach ($dayData['activities'] as $activity) {
                        $events[] = $this->buildScheduleEvent($activity, $dayDate, $itinerary, $dayIndex + 1);
                    }
                }
            }
        }
        // Fallback: create a single all-day event for the hike
        else {
            $events[] = $this->buildFallbackEvent($itinerary, $startDate);
        }
        
        return $events;
    }

    /**
     * Build an event from an ItineraryActivity model
     *
     * @param mixed $activity
     * @param Carbon $dayDate
     * @param Itinerary $itinerary
     * @return array
     */
    protected function buildActivityEvent($activity, Carbon $dayDate, Itinerary $itinerary): array
    {
        $startTime = $this->parseTime($activity->start_time ?? '08:00');
        $endTime = $this->parseTime($activity->end_time ?? '09:00');
        
        $startDateTime = $dayDate->copy()
            ->setHour($startTime['hour'])
            ->setMinute($startTime['minute'])
            ->setSecond(0);
            
        $endDateTime = $dayDate->copy()
            ->setHour($endTime['hour'])
            ->setMinute($endTime['minute'])
            ->setSecond(0);
        
        // If end time is before start time, assume it's the next day
        if ($endDateTime->lt($startDateTime)) {
            $endDateTime->addDay();
        }
        
        return [
            'summary' => $this->sanitizeText($activity->activity_name ?? $activity->name ?? 'Hiking Activity'),
            'description' => $this->buildActivityDescription($activity, $itinerary),
            'location' => $this->buildLocation($itinerary),
            'start' => $startDateTime,
            'end' => $endDateTime,
            'uid' => $this->generateUid($itinerary->id, 'activity', $activity->id ?? uniqid()),
        ];
    }

    /**
     * Build an event from daily_schedule JSON data
     *
     * @param array $activity
     * @param Carbon $dayDate
     * @param Itinerary $itinerary
     * @param int $dayNumber
     * @return array
     */
    protected function buildScheduleEvent(array $activity, Carbon $dayDate, Itinerary $itinerary, int $dayNumber): array
    {
        $startTime = $this->parseTime($activity['time'] ?? $activity['start_time'] ?? '08:00');
        
        // Calculate end time based on duration or default to 1 hour
        $duration = $activity['duration'] ?? 60; // default 60 minutes
        if (is_string($duration)) {
            // Parse duration strings like "2 hours", "30 mins", etc.
            preg_match('/(\d+)/', $duration, $matches);
            $durationMinutes = isset($matches[1]) ? (int)$matches[1] : 60;
            if (stripos($duration, 'hour') !== false) {
                $durationMinutes *= 60;
            }
        } else {
            $durationMinutes = (int)$duration;
        }
        
        $startDateTime = $dayDate->copy()
            ->setHour($startTime['hour'])
            ->setMinute($startTime['minute'])
            ->setSecond(0);
            
        $endDateTime = $startDateTime->copy()->addMinutes($durationMinutes);
        
        return [
            'summary' => $this->sanitizeText($activity['name'] ?? $activity['activity'] ?? "Day {$dayNumber} Activity"),
            'description' => $this->buildScheduleDescription($activity, $itinerary, $dayNumber),
            'location' => $this->buildLocation($itinerary),
            'start' => $startDateTime,
            'end' => $endDateTime,
            'uid' => $this->generateUid($itinerary->id, 'schedule', md5(json_encode($activity))),
        ];
    }

    /**
     * Build a fallback all-day event when no detailed schedule exists
     *
     * @param Itinerary $itinerary
     * @param Carbon $startDate
     * @return array
     */
    protected function buildFallbackEvent(Itinerary $itinerary, Carbon $startDate): array
    {
        $duration = $itinerary->duration_days ?? 1;
        $endDate = $startDate->copy()->addDays($duration);
        
        return [
            'summary' => $this->sanitizeText($itinerary->trail_name ?? $itinerary->title ?? 'Hiking Trip'),
            'description' => $this->buildFallbackDescription($itinerary),
            'location' => $this->buildLocation($itinerary),
            'start' => $startDate->copy()->setHour(0)->setMinute(0)->setSecond(0),
            'end' => $endDate->copy()->setHour(23)->setMinute(59)->setSecond(59),
            'uid' => $this->generateUid($itinerary->id, 'fallback', 'main'),
            'allDay' => true,
        ];
    }

    /**
     * Build the complete iCal content with all events
     *
     * @param array $events
     * @param Itinerary $itinerary
     * @return string
     */
    protected function buildIcalContent(array $events, Itinerary $itinerary): string
    {
        $now = Carbon::now('UTC');
        
        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "PRODID:-//HikeThere//Itinerary Export//EN\r\n";
        $ical .= "CALSCALE:GREGORIAN\r\n";
        $ical .= "METHOD:PUBLISH\r\n";
        $ical .= "X-WR-CALNAME:" . $this->sanitizeText($itinerary->trail_name ?? 'Hiking Itinerary') . "\r\n";
        $ical .= "X-WR-TIMEZONE:Asia/Manila\r\n";
        $ical .= "X-WR-CALDESC:Hiking itinerary generated by HikeThere\r\n";
        
        foreach ($events as $event) {
            $ical .= $this->buildEventBlock($event, $now);
        }
        
        $ical .= "END:VCALENDAR\r\n";
        
        return $ical;
    }

    /**
     * Build a single VEVENT block
     *
     * @param array $event
     * @param Carbon $now
     * @return string
     */
    protected function buildEventBlock(array $event, Carbon $now): string
    {
        $block = "BEGIN:VEVENT\r\n";
        $block .= "UID:" . $event['uid'] . "\r\n";
        $block .= "DTSTAMP:" . $now->format('Ymd\THis\Z') . "\r\n";
        
        // Handle all-day events differently
        if (isset($event['allDay']) && $event['allDay']) {
            $block .= "DTSTART;VALUE=DATE:" . $event['start']->format('Ymd') . "\r\n";
            $block .= "DTEND;VALUE=DATE:" . $event['end']->format('Ymd') . "\r\n";
        } else {
            $block .= "DTSTART:" . $event['start']->copy()->timezone('UTC')->format('Ymd\THis\Z') . "\r\n";
            $block .= "DTEND:" . $event['end']->copy()->timezone('UTC')->format('Ymd\THis\Z') . "\r\n";
        }
        
        $block .= "SUMMARY:" . $this->foldLine($event['summary']) . "\r\n";
        
        if (!empty($event['description'])) {
            $block .= "DESCRIPTION:" . $this->foldLine($event['description']) . "\r\n";
        }
        
        if (!empty($event['location'])) {
            $block .= "LOCATION:" . $this->foldLine($event['location']) . "\r\n";
        }
        
        // Add alarm/reminder 1 day before
        $block .= "BEGIN:VALARM\r\n";
        $block .= "TRIGGER:-P1D\r\n";
        $block .= "ACTION:DISPLAY\r\n";
        $block .= "DESCRIPTION:Reminder: " . $event['summary'] . " tomorrow\r\n";
        $block .= "END:VALARM\r\n";
        
        // Add alarm/reminder 1 hour before (for timed events)
        if (!isset($event['allDay']) || !$event['allDay']) {
            $block .= "BEGIN:VALARM\r\n";
            $block .= "TRIGGER:-PT1H\r\n";
            $block .= "ACTION:DISPLAY\r\n";
            $block .= "DESCRIPTION:Reminder: " . $event['summary'] . " in 1 hour\r\n";
            $block .= "END:VALARM\r\n";
        }
        
        $block .= "STATUS:CONFIRMED\r\n";
        $block .= "SEQUENCE:0\r\n";
        $block .= "END:VEVENT\r\n";
        
        return $block;
    }

    /**
     * Build description for an activity event
     *
     * @param mixed $activity
     * @param Itinerary $itinerary
     * @return string
     */
    protected function buildActivityDescription($activity, Itinerary $itinerary): string
    {
        $parts = [];
        
        if (!empty($activity->description)) {
            $parts[] = $activity->description;
        }
        
        if (!empty($activity->duration)) {
            $parts[] = "Duration: " . $activity->duration;
        }
        
        if (!empty($activity->notes)) {
            $parts[] = "Notes: " . $activity->notes;
        }
        
        $parts[] = "\n--- Itinerary Details ---";
        
        if (!empty($itinerary->trail_name)) {
            $parts[] = "Trail: " . $itinerary->trail_name;
        }
        
        if (!empty($itinerary->difficulty_level)) {
            $parts[] = "Difficulty: " . ucfirst($itinerary->difficulty_level);
        }
        
        return $this->sanitizeText(implode("\n", $parts));
    }

    /**
     * Build description for a schedule event
     *
     * @param array $activity
     * @param Itinerary $itinerary
     * @param int $dayNumber
     * @return string
     */
    protected function buildScheduleDescription(array $activity, Itinerary $itinerary, int $dayNumber): string
    {
        $parts = [];
        
        $parts[] = "Day {$dayNumber} of your hiking itinerary";
        
        if (!empty($activity['description'])) {
            $parts[] = $activity['description'];
        }
        
        if (!empty($activity['duration'])) {
            $parts[] = "Duration: " . $activity['duration'];
        }
        
        if (!empty($activity['notes'])) {
            $parts[] = "Notes: " . $activity['notes'];
        }
        
        $parts[] = "\n--- Itinerary Details ---";
        
        if (!empty($itinerary->trail_name)) {
            $parts[] = "Trail: " . $itinerary->trail_name;
        }
        
        if (!empty($itinerary->difficulty_level)) {
            $parts[] = "Difficulty: " . ucfirst($itinerary->difficulty_level);
        }
        
        if (!empty($itinerary->distance)) {
            $parts[] = "Distance: " . $itinerary->distance;
        }
        
        return $this->sanitizeText(implode("\n", $parts));
    }

    /**
     * Build description for fallback event
     *
     * @param Itinerary $itinerary
     * @return string
     */
    protected function buildFallbackDescription(Itinerary $itinerary): string
    {
        $parts = [];
        
        if (!empty($itinerary->route_description)) {
            $parts[] = $itinerary->route_description;
        }
        
        if (!empty($itinerary->difficulty_level)) {
            $parts[] = "Difficulty: " . ucfirst($itinerary->difficulty_level);
        }
        
        if (!empty($itinerary->distance)) {
            $parts[] = "Distance: " . $itinerary->distance;
        }
        
        if (!empty($itinerary->elevation_gain)) {
            $parts[] = "Elevation Gain: " . $itinerary->elevation_gain;
        }
        
        if (!empty($itinerary->estimated_duration)) {
            $parts[] = "Duration: " . $itinerary->estimated_duration;
        }
        
        $parts[] = "\nGenerated by HikeThere - Your AI Hiking Companion";
        
        return $this->sanitizeText(implode("\n", $parts));
    }

    /**
     * Build location string for an event
     *
     * @param Itinerary $itinerary
     * @return string
     */
    protected function buildLocation(Itinerary $itinerary): string
    {
        $location = [];
        
        if ($itinerary->trail && $itinerary->trail->location) {
            $loc = $itinerary->trail->location;
            
            if (!empty($loc->name)) {
                $location[] = $loc->name;
            }
            if (!empty($loc->municipality)) {
                $location[] = $loc->municipality;
            }
            if (!empty($loc->province)) {
                $location[] = $loc->province;
            }
        } elseif (!empty($itinerary->trail_name)) {
            $location[] = $itinerary->trail_name;
        }
        
        return $this->sanitizeText(implode(', ', $location));
    }

    /**
     * Parse time string to hour and minute
     *
     * @param string $time
     * @return array
     */
    protected function parseTime(string $time): array
    {
        // Handle formats like "08:00", "8:00 AM", "14:30", etc.
        $time = trim($time);
        
        // Try to parse with Carbon first
        try {
            $carbon = Carbon::parse($time);
            return [
                'hour' => $carbon->hour,
                'minute' => $carbon->minute,
            ];
        } catch (\Exception $e) {
            // Fallback to regex parsing
            if (preg_match('/(\d{1,2}):(\d{2})/', $time, $matches)) {
                return [
                    'hour' => (int)$matches[1],
                    'minute' => (int)$matches[2],
                ];
            }
        }
        
        // Default to 8:00 AM if parsing fails
        return ['hour' => 8, 'minute' => 0];
    }

    /**
     * Generate a unique UID for an event
     *
     * @param int $itineraryId
     * @param string $type
     * @param mixed $identifier
     * @return string
     */
    protected function generateUid(int $itineraryId, string $type, $identifier): string
    {
        return "itinerary-{$itineraryId}-{$type}-{$identifier}@hikethere.app";
    }

    /**
     * Sanitize text for iCal format (escape special characters)
     *
     * @param string $text
     * @return string
     */
    protected function sanitizeText(string $text): string
    {
        // Remove or replace problematic characters
        $text = str_replace(["\r\n", "\n", "\r"], "\\n", $text);
        $text = str_replace([",", ";", "\\"], ["\\,", "\\;", "\\\\"], $text);
        
        return $text;
    }

    /**
     * Fold long lines according to iCal specification (max 75 characters per line)
     *
     * @param string $text
     * @return string
     */
    protected function foldLine(string $text): string
    {
        $text = $this->sanitizeText($text);
        
        // iCal spec requires lines to be max 75 characters
        // Continuation lines start with a space
        if (strlen($text) <= 75) {
            return $text;
        }
        
        $folded = '';
        $chunks = str_split($text, 75);
        
        foreach ($chunks as $index => $chunk) {
            if ($index > 0) {
                $folded .= "\r\n " . $chunk; // Continuation line starts with space
            } else {
                $folded .= $chunk;
            }
        }
        
        return $folded;
    }
}

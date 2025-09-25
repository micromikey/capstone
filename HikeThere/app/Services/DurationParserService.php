<?php

namespace App\Services;

/**
 * Duration Parser Service
 * 
 * Parses duration strings in various formats (hours, days, nights)
 * and provides consistent formatting for display.
 * 
 * Based on the JavaScript parsing logic from create.blade.php
 */
class DurationParserService
{
    /**
     * Parse duration input string and return structured data
     * 
     * Supports formats like:
     * - "36 hours", "36h", "36"
     * - "2 days", "2d" 
     * - "1 night", "1n"
     * - "2 days 1 night", "2d1n"
     */
    public function parseDurationInput($raw)
    {
        if (empty($raw)) return null;
        
        $s = strtolower(trim($raw));

        // Try explicit hours first
        if (preg_match('/(\d+(?:\.\d+)?)\s*(hours?|hrs?|h)\b/i', $s, $matches)) {
            return ['hours' => floatval($matches[1])];
        }

        // Try pure number (assume hours)
        if (preg_match('/^(\d+(?:\.\d+)?)$/', $s, $matches)) {
            return ['hours' => floatval($matches[1])];
        }

        // Try explicit days
        if (preg_match('/(\d+(?:\.\d+)?)\s*(days?|d)\b/i', $s, $matches)) {
            return ['days' => floatval($matches[1])];
        }

        // Try explicit nights
        if (preg_match('/(\d+(?:\.\d+)?)\s*(nights?|n)\b/i', $s, $matches)) {
            return ['nights' => floatval($matches[1])];
        }

        // Try combined format "2 days 1 night" or "2d1n"
        $days = null;
        $nights = null;
        
        if (preg_match('/(\d+)\s*d(?:ays?)?/i', $s, $daysMatch)) {
            $days = intval($daysMatch[1]);
        }
        
        if (preg_match('/(\d+)\s*n(?:ights?)?/i', $s, $nightsMatch)) {
            $nights = intval($nightsMatch[1]);
        }
        
        if ($days !== null || $nights !== null) {
            return [
                'days' => $days ?: 0,
                'nights' => $nights ?: 0
            ];
        }

        return null;
    }

    /**
     * Normalize parsed duration to include days, nights, and hours
     */
    public function normalizeDuration($value)
    {
        $parsed = $this->parseDurationInput($value);
        if (!$parsed) return null;

        // If hours provided, convert to days/nights heuristically
        if (isset($parsed['hours'])) {
            $hours = $parsed['hours'];
            $days = 0;
            $nights = 0;
            
            if ($hours >= 24) {
                // Round up days for partial days
                $days = ceil($hours / 24);
                
                // If hours is an exact multiple of 24 (e.g. 48 -> 2 days),
                // infer nights equal to days (organizer likely means whole days with nights per day).
                // Otherwise (partial-day rounding) infer nights as days - 1 (typical travel logic).
                if (abs($hours % 24) < 0.01) { // exact multiples
                    $nights = $days; // nights match days
                } else {
                    $nights = max(0, $days - 1);
                }
            }
            
            return ['hours' => $hours, 'days' => $days, 'nights' => $nights];
        }

        // If days provided, nights default to days - 1 (typical travel logic: 2 days = 1 night)
        if (isset($parsed['days'])) {
            $days = $parsed['days'];
            $nights = max(0, floor($days) - 1);
            return ['days' => $days, 'nights' => $nights, 'hours' => $days * 24];
        }

        // If nights provided, infer days
        if (isset($parsed['nights'])) {
            $nights = $parsed['nights'];
            $days = $nights + 1; // infer days
            return ['nights' => $nights, 'days' => $days, 'hours' => $days * 24];
        }

        return null;
    }

    /**
     * Format duration for display
     */
    public function formatDuration($durationString, $format = 'full')
    {
        $normalized = $this->normalizeDuration($durationString);
        
        if (!$normalized) {
            return $durationString; // Return original if can't parse
        }

        switch ($format) {
            case 'days_nights':
                return $this->formatDaysNights($normalized);
            case 'hours':
                return $this->formatHours($normalized);
            case 'short':
                return $this->formatShort($normalized);
            case 'full':
            default:
                return $this->formatFull($normalized);
        }
    }

    /**
     * Format as "X day(s) • Y night(s)"
     */
    protected function formatDaysNights($normalized)
    {
        $days = $normalized['days'] ?? 0;
        $nights = $normalized['nights'] ?? 0;
        
        $dayText = $days . ' day' . ($days != 1 ? 's' : '');
        $nightText = $nights . ' night' . ($nights != 1 ? 's' : '');
        
        return $dayText . ' • ' . $nightText;
    }

    /**
     * Format as hours only
     */
    protected function formatHours($normalized)
    {
        $hours = $normalized['hours'] ?? 0;
        return $hours . ' hour' . ($hours != 1 ? 's' : '');
    }

    /**
     * Format in shortest appropriate form
     */
    protected function formatShort($normalized)
    {
        $days = $normalized['days'] ?? 0;
        $nights = $normalized['nights'] ?? 0;
        $hours = $normalized['hours'] ?? 0;
        
        if ($days > 0) {
            if ($nights > 0) {
                return $days . 'd' . $nights . 'n';
            } else {
                return $days . ' day' . ($days != 1 ? 's' : '');
            }
        } else {
            return $hours . 'h';
        }
    }

    /**
     * Format with full description
     */
    protected function formatFull($normalized)
    {
        $days = $normalized['days'] ?? 0;
        $nights = $normalized['nights'] ?? 0;
        $hours = $normalized['hours'] ?? 0;
        
        $parts = [];
        
        if ($days > 0) {
            $parts[] = $days . ' day' . ($days != 1 ? 's' : '');
        }
        
        if ($nights > 0) {
            $parts[] = $nights . ' night' . ($nights != 1 ? 's' : '');
        }
        
        if (empty($parts) && $hours > 0) {
            $parts[] = $hours . ' hour' . ($hours != 1 ? 's' : '');
        }
        
        return implode(', ', $parts) ?: 'N/A';
    }

    /**
     * Get duration information for a trail
     * Checks trail package first, then falls back to trail data
     */
    public function getTrailDuration($trail)
    {
        // Check trail package duration first
        if (isset($trail['package']['duration'])) {
            return $this->normalizeDuration($trail['package']['duration']);
        }
        
        // Check if trail has package relationship loaded
        if (is_object($trail) && $trail->package && $trail->package->duration) {
            return $this->normalizeDuration($trail->package->duration);
        }
        
        // Fall back to trail duration field
        if (isset($trail['duration'])) {
            return $this->normalizeDuration($trail['duration']);
        }
        
        if (is_object($trail) && $trail->duration) {
            return $this->normalizeDuration($trail->duration);
        }
        
        // Fall back to estimated_time in minutes
        if (isset($trail['estimated_time'])) {
            $minutes = intval($trail['estimated_time']);
            $hours = $minutes / 60;
            return $this->normalizeDuration($hours . ' hours');
        }
        
        if (is_object($trail) && $trail->estimated_time) {
            $minutes = intval($trail->estimated_time);
            $hours = $minutes / 60;
            return $this->normalizeDuration($hours . ' hours');
        }
        
        return null;
    }

    /**
     * Test the parsing with various examples
     */
    public function testParsing()
    {
        $testCases = [
            '36 hours',
            '2 days',
            '1 night',
            '2 days 1 night',
            '2d1n',
            '48',
            '24 hours',
            '3 days 2 nights'
        ];
        
        $results = [];
        foreach ($testCases as $test) {
            $results[$test] = [
                'parsed' => $this->parseDurationInput($test),
                'normalized' => $this->normalizeDuration($test),
                'formatted' => $this->formatDuration($test, 'days_nights'),
                'full' => $this->formatDuration($test, 'full')
            ];
        }
        
        return $results;
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TrailPackage;

class ConvertTrailPackageTextToJson extends Command
{
    protected $signature = 'convert:trail-package-text-to-json {--batch=100}';
    protected $description = 'Convert existing package_inclusions and side_trips text fields to JSON arrays in trail_packages';

    public function handle()
    {
        $batch = (int) $this->option('batch');
        $this->info("Starting conversion (batch={$batch})...");

        TrailPackage::chunk($batch, function ($packages) {
            foreach ($packages as $p) {
                $changed = false;

                if (empty($p->package_inclusions_json) && !empty($p->package_inclusions)) {
                    $p->package_inclusions_json = $this->toArray($p->package_inclusions);
                    $changed = true;
                }

                if (empty($p->side_trips_json) && !empty($p->side_trips)) {
                    $p->side_trips_json = $this->toArray($p->side_trips);
                    $changed = true;
                }

                if ($changed) {
                    $p->saveQuietly();
                    $this->line("Converted package id={$p->id}");
                }
            }
        });

        $this->info('Conversion completed.');
        return 0;
    }

    protected function toArray($text)
    {
        if (empty($text)) return null;

        // Try JSON first
        $decoded = json_decode($text, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_values(array_filter(array_map('trim', $decoded)));
        }

        // Split by newlines
        $items = preg_split('/\r\n|\r|\n/', trim($text));
        $items = array_map('trim', $items);
        $items = array_filter($items, fn($i) => $i !== '');
        if (count($items) > 1) return array_values($items);

        // Single-line fallback: split on bullets/semicolons/commas
        $items = preg_split('/•|\u2022|-\s|;|,/', $text);
        $items = array_map('trim', $items);
        $items = array_filter($items, fn($i) => $i !== '');
        return array_values($items);
    }
}

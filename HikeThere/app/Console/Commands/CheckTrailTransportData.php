<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Trail;

class CheckTrailTransportData extends Command
{
    protected $signature = 'check:trail-transport';
    protected $description = 'Check trail transport_included data in database';

    public function handle()
    {
        $this->info('=== Checking Trail Transport Data ===');

        // Find trails with Pulag in the trail_name
        $trails = Trail::where('trail_name', 'LIKE', '%Pulag%')->get();
        
        if ($trails->isEmpty()) {
            $this->warn('No trails found with "Pulag" in trail_name');
            
            // Show first few trails with their columns
            $allTrails = Trail::take(3)->get();
            $this->line('First 3 trails in database:');
            foreach ($allTrails as $trail) {
                $this->line("- ID: {$trail->id}");
                $this->line("  mountain_name: " . ($trail->mountain_name ?? 'NOT SET'));
                $this->line("  trail_name: " . ($trail->trail_name ?? 'NOT SET'));
                $this->line("  Package: " . ($trail->package ? 'EXISTS' : 'NOT EXISTS'));
                if ($trail->package) {
                    $this->line("    transport_included: " . ($trail->package->transport_included ?? 'NOT SET'));
                    $this->line("    departure_point: " . ($trail->package->departure_point ?? 'NOT SET'));
                }
                $this->line('');
            }
        } else {
            foreach ($trails as $trail) {
                $this->line("Trail: {$trail->mountain_name} - {$trail->trail_name}");
                $this->line("  ID: {$trail->id}");
                if ($trail->package) {
                    $this->line("  transport_included: " . ($trail->package->transport_included ?? 'NOT SET'));
                    $this->line("  departure_point: " . ($trail->package->departure_point ?? 'NOT SET'));
                    $this->line("  transport_details: " . ($trail->package->transport_details ?? 'NOT SET'));
                } else {
                    $this->warn("  No package data found for this trail");
                }
                $this->line('');
            }
        }

        return 0;
    }
}
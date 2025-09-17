<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Trail;

class ExtractTrailCoordinates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trails:extract-coordinates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract latitude and longitude from existing trail coordinates JSON data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Extracting latitude and longitude from trail coordinates...');

        $trails = Trail::whereNotNull('coordinates')
            ->where(function($query) {
                $query->whereNull('latitude')->orWhereNull('longitude');
            })
            ->get();

        $this->info("Found {$trails->count()} trails with coordinates but missing lat/lng");

        $updated = 0;
        foreach ($trails as $trail) {
            $coordinates = $trail->coordinates;
            
            if (is_array($coordinates) && count($coordinates) > 0) {
                // Use the first coordinate as the starting point (trailhead)
                $startPoint = $coordinates[0];
                
                if (isset($startPoint['lat']) && isset($startPoint['lng'])) {
                    $trail->latitude = $startPoint['lat'];
                    $trail->longitude = $startPoint['lng'];
                    $trail->save();
                    
                    $this->line("Updated trail '{$trail->trail_name}' with lat: {$startPoint['lat']}, lng: {$startPoint['lng']}");
                    $updated++;
                }
            }
        }

        $this->info("Successfully updated {$updated} trails with extracted coordinates");
        
        return 0;
    }
}

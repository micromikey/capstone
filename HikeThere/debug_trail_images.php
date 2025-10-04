<?php

/**
 * Debug Trail Images - Check for duplicates
 * 
 * Run this file to see what images are being fetched for a trail
 * Usage: php debug_trail_images.php [trail_id]
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get trail ID from command line or use default
$trailId = $argv[1] ?? 1;

echo "=== Debugging Trail Images ===\n";
echo "Trail ID: $trailId\n\n";

try {
    $trail = App\Models\Trail::with(['images', 'location', 'user'])->findOrFail($trailId);
    
    echo "Trail: {$trail->trail_name}\n";
    echo "Mountain: {$trail->mountain_name}\n";
    echo "Location: " . ($trail->location ? $trail->location->name : 'N/A') . "\n\n";
    
    // Check organization images
    echo "--- Organization Images ---\n";
    if ($trail->images && $trail->images->count() > 0) {
        echo "Total: {$trail->images->count()} images\n\n";
        
        $urls = [];
        foreach ($trail->images as $index => $image) {
            $isDuplicate = in_array($image->url, $urls) ? '❌ DUPLICATE' : '✅ Unique';
            $urls[] = $image->url;
            
            echo ($index + 1) . ". {$isDuplicate}\n";
            echo "   URL: {$image->url}\n";
            echo "   Caption: " . ($image->caption ?? 'N/A') . "\n";
            echo "   Primary: " . ($image->is_primary ? 'Yes' : 'No') . "\n\n";
        }
        
        // Check for duplicates
        $duplicates = array_filter(array_count_values($urls), function($count) {
            return $count > 1;
        });
        
        if (count($duplicates) > 0) {
            echo "⚠️  FOUND DUPLICATES IN DATABASE:\n";
            foreach ($duplicates as $url => $count) {
                echo "   • $url (appears $count times)\n";
            }
            echo "\n";
        } else {
            echo "✅ No duplicate URLs in database\n\n";
        }
    } else {
        echo "No organization images found\n\n";
    }
    
    // Test the service
    echo "--- TrailImageService Output ---\n";
    $imageService = new App\Services\TrailImageService();
    $images = $imageService->getTrailImages($trail, 10);
    
    echo "Total images returned: " . count($images) . "\n\n";
    
    $serviceUrls = [];
    foreach ($images as $index => $image) {
        $isDuplicate = in_array($image['url'], $serviceUrls) ? '❌ DUPLICATE' : '✅ Unique';
        $serviceUrls[] = $image['url'];
        
        echo ($index + 1) . ". {$isDuplicate}\n";
        echo "   Source: {$image['source']}\n";
        echo "   Caption: {$image['caption']}\n";
        echo "   URL: " . substr($image['url'], 0, 80) . "...\n\n";
    }
    
    // Check for duplicates in service output
    $serviceDuplicates = array_filter(array_count_values($serviceUrls), function($count) {
        return $count > 1;
    });
    
    if (count($serviceDuplicates) > 0) {
        echo "⚠️  FOUND DUPLICATES IN SERVICE OUTPUT:\n";
        foreach ($serviceDuplicates as $url => $count) {
            echo "   • " . substr($url, 0, 80) . "... (appears $count times)\n";
        }
        echo "\n";
    } else {
        echo "✅ No duplicate URLs in service output\n\n";
    }
    
    echo "=== Debug Complete ===\n";
    
} catch (\Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
    echo "Stack trace:\n{$e->getTraceAsString()}\n";
}

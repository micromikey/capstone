<?php

/**
 * Upload OG Image to Google Cloud Storage
 * 
 * This script uploads your og-image.png to Google Cloud Storage
 * so it can be used in your deployed app's meta tags.
 * 
 * Usage:
 * 1. Make sure you have generated og-image.png from generate-og-image.html
 * 2. Save it as public/img/og-image.png
 * 3. Run: php artisan upload:og-image
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class UploadOgImage extends Command
{
    protected $signature = 'upload:og-image {file=public/img/og-image.png}';
    protected $description = 'Upload Open Graph image to Google Cloud Storage';

    public function handle()
    {
        $localPath = $this->argument('file');
        
        // Check if file exists
        if (!File::exists(base_path($localPath))) {
            $this->error("File not found: {$localPath}");
            $this->info("Please generate the OG image first using: http://localhost/generate-og-image.html");
            return 1;
        }

        $this->info("Reading file: {$localPath}");
        $fileContents = File::get(base_path($localPath));
        
        // Upload to GCS
        $gcsPath = 'assets/og-image.png';
        
        try {
            $this->info("Uploading to Google Cloud Storage...");
            
            Storage::disk('gcs')->put($gcsPath, $fileContents, [
                'visibility' => 'public',
                'CacheControl' => 'public, max-age=86400', // Cache for 1 day
                'ContentType' => 'image/png',
            ]);
            
            $url = Storage::disk('gcs')->url($gcsPath);
            
            $this->info("✓ Successfully uploaded!");
            $this->line("");
            $this->info("Public URL: {$url}");
            $this->line("");
            $this->info("Update your .env file with:");
            $this->line("OG_IMAGE_URL={$url}");
            $this->line("");
            $this->info("Or use in blade files:");
            $this->line('{{ Storage::disk(\'gcs\')->url(\'assets/og-image.png\') }}');
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Failed to upload: " . $e->getMessage());
            $this->line("");
            $this->info("Make sure your GCS credentials are configured correctly in .env");
            return 1;
        }
    }
}

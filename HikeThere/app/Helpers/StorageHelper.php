<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class StorageHelper
{
    /**
     * Get the public URL for a stored file, handling both local and GCS storage
     * 
     * @param string|null $path The storage path
     * @param string|null $disk The disk to use (defaults to configured default)
     * @return string|null The public URL or null if path is empty
     */
    public static function url(?string $path, ?string $disk = null): ?string
    {
        if (empty($path)) {
            return null;
        }
        
        // If already a full URL, return as-is
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }
        
        // Use specified disk or default
        $disk = $disk ?? config('filesystems.default', 'public');
        
        // For GCS, use Storage facade which handles URL generation properly
        if ($disk === 'gcs') {
            try {
                // Storage::url() will use the configured GCS URL
                return Storage::disk('gcs')->url($path);
            } catch (\Exception $e) {
                \Log::warning('Failed to generate GCS URL', [
                    'path' => $path,
                    'disk' => $disk,
                    'error' => $e->getMessage()
                ]);
                
                // Fallback to manual construction if Storage::url() fails
                $bucket = config('filesystems.disks.gcs.bucket');
                if ($bucket) {
                    return "https://storage.googleapis.com/{$bucket}/{$path}";
                }
                
                return null;
            }
        }
        
        // For local/public disk, use Storage facade
        try {
            return Storage::disk($disk)->url($path);
        } catch (\Exception $e) {
            \Log::warning('Failed to generate storage URL', [
                'path' => $path,
                'disk' => $disk,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}

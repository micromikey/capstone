<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class StorageHelper
{
    /**
     * Get the public URL for a stored file, handling both local and GCS storage
     * Automatically detects where the file is stored (local or GCS) for backwards compatibility
     * 
     * @param string|null $path The storage path
     * @param string|null $disk The disk to use (null = auto-detect)
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
        
        // If disk is specified, use it directly
        if ($disk !== null) {
            return self::generateUrl($path, $disk);
        }
        
        // Auto-detect: Check which disk has the file
        // This ensures backwards compatibility for files stored before GCS migration
        $detectedDisk = self::detectFileDisk($path);
        
        return self::generateUrl($path, $detectedDisk);
    }
    
    /**
     * Detect which disk contains the file
     * Checks in order: GCS -> public -> local
     * 
     * @param string $path The file path
     * @return string The disk name where file exists, or default disk
     */
    protected static function detectFileDisk(string $path): string
    {
        // Get the default disk from config
        $defaultDisk = config('filesystems.default', 'public');
        
        // If GCS is configured, check if file exists there first
        if (config('filesystems.disks.gcs.bucket')) {
            try {
                if (Storage::disk('gcs')->exists($path)) {
                    return 'gcs';
                }
            } catch (\Exception $e) {
                Log::debug('GCS check failed for path', [
                    'path' => $path,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Check public disk
        try {
            if (Storage::disk('public')->exists($path)) {
                return 'public';
            }
        } catch (\Exception $e) {
            Log::debug('Public disk check failed for path', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
        }
        
        // Check local disk
        try {
            if (Storage::disk('local')->exists($path)) {
                return 'local';
            }
        } catch (\Exception $e) {
            Log::debug('Local disk check failed for path', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
        }
        
        // If file not found anywhere, use default disk
        // This allows new uploads to go to the configured default
        return $defaultDisk;
    }
    
    /**
     * Generate URL for a specific disk
     * 
     * @param string $path The file path
     * @param string $disk The disk name
     * @return string|null The generated URL
     */
    protected static function generateUrl(string $path, string $disk): ?string
    {
        // For GCS, use Storage facade which handles URL generation properly
        if ($disk === 'gcs') {
            try {
                // Storage::url() will use the configured GCS URL
                return Storage::disk('gcs')->url($path);
            } catch (\Exception $e) {
                Log::warning('Failed to generate GCS URL', [
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
            Log::warning('Failed to generate storage URL', [
                'path' => $path,
                'disk' => $disk,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Migrate a file from local/public storage to GCS
     * Useful for migrating existing files to cloud storage
     * 
     * @param string $path The file path
     * @param string $sourceDisk The source disk (local or public)
     * @param bool $deleteOriginal Whether to delete the original file after migration
     * @return bool True if migration successful
     */
    public static function migrateToGcs(string $path, string $sourceDisk = 'public', bool $deleteOriginal = false): bool
    {
        try {
            // Check if source file exists
            if (!Storage::disk($sourceDisk)->exists($path)) {
                Log::warning('Source file not found for GCS migration', [
                    'path' => $path,
                    'source_disk' => $sourceDisk
                ]);
                return false;
            }
            
            // Check if already exists in GCS
            if (Storage::disk('gcs')->exists($path)) {
                Log::info('File already exists in GCS', ['path' => $path]);
                return true;
            }
            
            // Get file contents
            $contents = Storage::disk($sourceDisk)->get($path);
            
            // Upload to GCS
            $uploaded = Storage::disk('gcs')->put($path, $contents);
            
            if ($uploaded) {
                Log::info('File migrated to GCS successfully', [
                    'path' => $path,
                    'source_disk' => $sourceDisk
                ]);
                
                // Optionally delete original
                if ($deleteOriginal) {
                    Storage::disk($sourceDisk)->delete($path);
                    Log::info('Original file deleted after migration', ['path' => $path]);
                }
                
                return true;
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::error('Failed to migrate file to GCS', [
                'path' => $path,
                'source_disk' => $sourceDisk,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}

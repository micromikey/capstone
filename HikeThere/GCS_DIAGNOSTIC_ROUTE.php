<?php

// Temporary diagnostic route - add to routes/web.php to test GCS
Route::get('/test-gcs-setup', function () {
    $results = [
        'timestamp' => now()->toDateTimeString(),
        'environment' => [
            'FILESYSTEM_DISK' => config('filesystems.default'),
            'GCS_PROJECT_ID' => config('filesystems.disks.gcs.project_id'),
            'GCS_BUCKET' => config('filesystems.disks.gcs.bucket'),
            'GCS_KEY_FILE_CONTENT' => env('GCS_KEY_FILE_CONTENT') ? 'SET (length: ' . strlen(env('GCS_KEY_FILE_CONTENT')) . ')' : 'NOT SET',
        ],
        'composer_packages' => [
            'google/cloud-storage' => class_exists('Google\Cloud\Storage\StorageClient') ? 'INSTALLED' : 'NOT FOUND',
            'league/flysystem-google-cloud-storage' => class_exists('League\Flysystem\GoogleCloudStorage\GoogleCloudStorageAdapter') ? 'INSTALLED' : 'NOT FOUND',
        ],
        'laravel_storage' => [
            'available_disks' => array_keys(config('filesystems.disks')),
            'default_disk' => config('filesystems.default'),
        ],
        'gcs_test' => null,
    ];

    // Try to test GCS
    try {
        if (config('filesystems.default') === 'gcs') {
            $disk = Storage::disk('gcs');
            $results['gcs_test'] = 'Disk created successfully';
            
            // Try to list files
            try {
                $files = $disk->files();
                $results['gcs_test'] .= ' - Can list files: ' . count($files) . ' files found';
            } catch (\Exception $e) {
                $results['gcs_test'] .= ' - List files error: ' . $e->getMessage();
            }
        } else {
            $results['gcs_test'] = 'Not testing - default disk is: ' . config('filesystems.default');
        }
    } catch (\Exception $e) {
        $results['gcs_test'] = 'ERROR: ' . $e->getMessage();
        $results['gcs_test_trace'] = $e->getTraceAsString();
    }

    return response()->json($results, 200, [], JSON_PRETTY_PRINT);
})->name('test.gcs');

<?php

namespace App\Observers;

use App\Models\TrailPackage;
use App\Services\BatchService;
use Illuminate\Support\Facades\Log;

class TrailPackageObserver
{
    protected $batches;

    public function __construct()
    {
        $this->batches = new BatchService();
    }

    public function saved(TrailPackage $package)
    {
        try {
            // generate batches for the next 30 days by default
            $this->batches->syncForPackage($package, 30);
        } catch (\Exception $e) {
            Log::error('Failed to sync batches for package '.$package->id.': '.$e->getMessage());
        }
    }

    public function deleted(TrailPackage $package)
    {
        // remove batches tied to this package
        try {
            \App\Models\Batch::where('trail_package_id', $package->id)->delete();
        } catch (\Exception $e) {
            Log::warning('Failed to remove batches for deleted package '.$package->id.': '.$e->getMessage());
        }
    }
}

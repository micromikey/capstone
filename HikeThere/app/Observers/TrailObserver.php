<?php

namespace App\Observers;

use App\Models\Trail;
use App\Services\TrailMetricsService;

class TrailObserver
{
    public function creating(Trail $trail): void
    {
        $this->derive($trail);
    }

    public function updating(Trail $trail): void
    {
        $this->derive($trail, false);
    }

    private function derive(Trail $trail, bool $isCreate = true): void
    {
        // Only derive if coordinates available
        if (!is_array($trail->coordinates) || count($trail->coordinates) < 2) {
            return;
        }
        $service = new TrailMetricsService();
        $service->fillMissingMetrics($trail);
    }
}

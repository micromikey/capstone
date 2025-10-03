<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use Illuminate\Http\Request;

class SlotAvailabilityController extends Controller
{
    /**
     * Check slot availability for a specific batch
     */
    public function checkBatch(Request $request, $batchId)
    {
        $batch = Batch::with('trail')->find($batchId);
        
        if (!$batch) {
            return response()->json([
                'error' => 'Batch not found'
            ], 404);
        }
        
        $requestedSlots = (int) $request->query('party_size', 1);
        $availableSlots = $batch->getAvailableSlots();
        $hasEnoughSlots = $batch->hasAvailableSlots($requestedSlots);
        
        return response()->json([
            'batch_id' => $batch->id,
            'date' => $batch->starts_at->format('Y-m-d'),
            'capacity' => $batch->capacity,
            'slots_taken' => $batch->slots_taken,
            'available_slots' => $availableSlots,
            'requested_slots' => $requestedSlots,
            'has_enough_slots' => $hasEnoughSlots,
            'is_full' => $batch->isFull(),
            'occupancy_percentage' => $batch->getOccupancyPercentage(),
            'message' => $hasEnoughSlots 
                ? "{$availableSlots} slot(s) available"
                : "Only {$availableSlots} slot(s) available. You need {$requestedSlots}."
        ]);
    }
    
    /**
     * Get alternative dates with available slots for a trail
     */
    public function getAlternatives(Request $request, $trailId)
    {
        $requestedSlots = (int) $request->query('party_size', 1);
        $excludeBatchId = $request->query('exclude_batch_id');
        
        $query = Batch::where('trail_id', $trailId)
            ->where('starts_at', '>', now())
            ->orderBy('starts_at', 'asc');
        
        if ($excludeBatchId) {
            $query->where('id', '!=', $excludeBatchId);
        }
        
        $batches = $query->get()->filter(function($batch) use ($requestedSlots) {
            return $batch->hasAvailableSlots($requestedSlots);
        })->take(5);
        
        return response()->json([
            'trail_id' => $trailId,
            'requested_slots' => $requestedSlots,
            'alternatives' => $batches->map(function($batch) {
                return [
                    'batch_id' => $batch->id,
                    'date' => $batch->starts_at->format('M d, Y'),
                    'date_time' => $batch->starts_at->format('Y-m-d H:i:s'),
                    'available_slots' => $batch->getAvailableSlots(),
                    'capacity' => $batch->capacity,
                    'occupancy_percentage' => $batch->getOccupancyPercentage()
                ];
            })->values()
        ]);
    }
}

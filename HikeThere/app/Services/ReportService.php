<?php

namespace App\Services;

use App\Models\User;
use App\Models\Trail;
use App\Models\Booking;
use App\Models\Event;
use App\Models\TrailReview;
use App\Models\EmergencyReadiness;
use App\Models\SafetyIncident;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
use Exception;

class ReportService
{
    private $maxRetries = 3;
    private $retryDelay = 1; // seconds
    
    // Reports allowed for organization users
    const ORGANIZATION_ALLOWED_REPORTS = [
        'overall_transactions',
        'booking_volumes',
        'trail_popularity',
        'emergency_readiness',
        'safety_incidents',
        'feedback_summary'
    ];

    /**
     * Check if report type is allowed for organizations
     */
    public function isReportAllowedForOrganization($reportType)
    {
        return in_array($reportType, self::ORGANIZATION_ALLOWED_REPORTS);
    }

    /**
     * Generate report with organization scoping
     */
    public function generateReport($reportType, $dateFrom, $dateTo, $filters = [], $organizationId = null)
    {
        try {
            $dateFrom = Carbon::parse($dateFrom)->startOfDay();
            $dateTo = Carbon::parse($dateTo)->endOfDay();

            // Validate date range
            if ($dateFrom->gt($dateTo)) {
                throw new Exception('Start date cannot be after end date');
            }

            if ($dateFrom->gt(Carbon::now())) {
                throw new Exception('Start date cannot be in the future');
            }

            // If organization ID is provided, scope to their trails only
            if ($organizationId) {
                $filters['organization_id'] = $organizationId;
            }

            switch ($reportType) {
                case 'overall_transactions':
                    return $this->generateOverallTransactionsReport($dateFrom, $dateTo, $filters);
                case 'booking_volumes':
                    return $this->generateBookingVolumesReport($dateFrom, $dateTo, $filters);
                case 'trail_popularity':
                    return $this->generateTrailPopularityReport($dateFrom, $dateTo, $filters);
                case 'emergency_readiness':
                    return $this->generateEmergencyReadinessReport($dateFrom, $dateTo, $filters);
                case 'safety_incidents':
                    return $this->generateSafetyIncidentsReport($dateFrom, $dateTo, $filters);
                case 'feedback_summary':
                    return $this->generateFeedbackSummaryReport($dateFrom, $dateTo, $filters);
                default:
                    throw new Exception('Invalid report type: ' . $reportType);
            }
        } catch (Exception $e) {
            Log::error('Report generation error: ' . $e->getMessage());
            return [
                'error' => true,
                'message' => $e->getMessage(),
                'title' => $this->getReportTitle($reportType),
                'period' => ($dateFrom ?? Carbon::now())->format('Y-m-d') . ' to ' . ($dateTo ?? Carbon::now())->format('Y-m-d'),
                'summary' => ['error' => 'Report generation failed: ' . $e->getMessage()],
                'data' => []
            ];
        }
    }

    /**
     * Get trail IDs for organization
     */
    private function getOrganizationTrailIds($organizationId)
    {
        return Trail::where('user_id', $organizationId)->pluck('id')->toArray();
    }

    /**
     * Scope query to organization's trails
     */
    private function scopeToOrganizationTrails($query, $organizationId)
    {
        if ($organizationId) {
            $trailIds = $this->getOrganizationTrailIds($organizationId);
            $query->whereIn('trail_id', $trailIds);
        }
        return $query;
    }

    /**
     * Remove PII from data for organization reports
     */
    private function sanitizeForOrganization($data)
    {
        if (isset($data['user_name'])) {
            unset($data['user_name']);
        }
        if (isset($data['email'])) {
            unset($data['email']);
        }
        if (isset($data['user_id'])) {
            $data['user_id'] = 'User_' . $data['user_id']; // Anonymize
        }
        if (isset($data['reported_by'])) {
            $data['reported_by'] = 'Anonymous';
        }
        return $data;
    }

    /**
     * Execute database query with retry logic
     */
    private function executeWithRetry($callback)
    {
        $attempts = 0;
        
        while ($attempts < $this->maxRetries) {
            try {
                return $callback();
            } catch (QueryException $e) {
                $attempts++;
                Log::warning("Database query failed (attempt {$attempts}): " . $e->getMessage());
                
                if ($attempts >= $this->maxRetries) {
                    throw new Exception('Database unavailable after ' . $this->maxRetries . ' attempts: ' . $e->getMessage());
                }
                
                sleep($this->retryDelay);
            }
        }
    }

    /**
     * Generate Overall Transactions Report
     */
    protected function generateOverallTransactionsReport($dateFrom, $dateTo, $filters)
    {
        try {
            return $this->executeWithRetry(function() use ($dateFrom, $dateTo, $filters) {
                $query = Booking::whereBetween('created_at', [$dateFrom, $dateTo]);
                
                // Scope to organization's trails if provided
                if (!empty($filters['organization_id'])) {
                    $trailIds = $this->getOrganizationTrailIds($filters['organization_id']);
                    $query->whereHas('event', function($q) use ($trailIds) {
                        $q->whereIn('trail_id', $trailIds);
                    });
                }
                
                if (!empty($filters['trail_id'])) {
                    $query->whereHas('event', function($q) use ($filters) {
                        $q->where('trail_id', $filters['trail_id']);
                    });
                }

                $bookings = $query->with(['event.trail', 'user'])->get();

                // Calculate revenue by status
                $confirmedRevenue = 0;
                $pendingRevenue = 0;
                $cancelledRevenue = 0;
                
                foreach ($bookings as $booking) {
                    $amount = $booking->getAmountInPesos();
                    
                    switch ($booking->status) {
                        case 'confirmed':
                            $confirmedRevenue += $amount;
                            break;
                        case 'pending':
                            $pendingRevenue += $amount;
                            break;
                        case 'cancelled':
                            $cancelledRevenue += $amount;
                            break;
                    }
                }
                
                $totalRevenue = $confirmedRevenue + $pendingRevenue;
                
                // Group by payment status
                $paidCount = $bookings->where('payment_status', 'paid')->count();
                $pendingPaymentCount = $bookings->where('payment_status', 'pending')->count();
                $failedPaymentCount = $bookings->where('payment_status', 'failed')->count();
                
                // Group by trail
                $trailBreakdown = $bookings->groupBy(function($booking) {
                    return $booking->event->trail->trail_name ?? 'Unknown Trail';
                })->map(function($trailBookings, $trailName) {
                    $revenue = $trailBookings->sum(function($booking) {
                        return $booking->getAmountInPesos();
                    });
                    
                    return [
                        'trail_name' => $trailName,
                        'booking_count' => $trailBookings->count(),
                        'total_hikers' => $trailBookings->sum('party_size'),
                        'revenue' => '₱' . number_format($revenue, 2),
                        'confirmed' => $trailBookings->where('status', 'confirmed')->count(),
                        'pending' => $trailBookings->where('status', 'pending')->count(),
                        'cancelled' => $trailBookings->where('status', 'cancelled')->count()
                    ];
                });

                return [
                    'title' => 'Overall Transactions Report',
                    'period' => $dateFrom->format('Y-m-d') . ' to ' . $dateTo->format('Y-m-d'),
                    'summary' => [
                        'total_transactions' => $bookings->count(),
                        'total_revenue' => '₱' . number_format($totalRevenue, 2),
                        'confirmed_revenue' => '₱' . number_format($confirmedRevenue, 2),
                        'pending_revenue' => '₱' . number_format($pendingRevenue, 2),
                        'confirmed_bookings' => $bookings->where('status', 'confirmed')->count(),
                        'pending_bookings' => $bookings->where('status', 'pending')->count(),
                        'cancelled_bookings' => $bookings->where('status', 'cancelled')->count(),
                        'total_hikers' => $bookings->sum('party_size'),
                        'paid_transactions' => $paidCount,
                        'pending_payments' => $pendingPaymentCount,
                        'avg_transaction_value' => $bookings->count() > 0 ? '₱' . number_format($totalRevenue / $bookings->count(), 2) : '₱0.00'
                    ],
                    'data' => $trailBreakdown->values()->toArray()
                ];
            });
        } catch (Exception $e) {
            Log::error('Overall transactions report error: ' . $e->getMessage());
            return $this->getEmptyReport('Overall Transactions Report', $dateFrom, $dateTo, $e->getMessage());
        }
    }

    /**
     * Generate Booking Volumes Report
     */
    protected function generateBookingVolumesReport($dateFrom, $dateTo, $filters)
    {
        try {
            return $this->executeWithRetry(function() use ($dateFrom, $dateTo, $filters) {
                $query = Booking::whereBetween('created_at', [$dateFrom, $dateTo]);
                
                // Scope to organization's trails if provided
                if (!empty($filters['organization_id'])) {
                    $trailIds = $this->getOrganizationTrailIds($filters['organization_id']);
                    $query->whereHas('event', function($q) use ($trailIds) {
                        $q->whereIn('trail_id', $trailIds);
                    });
                }
                
                if (!empty($filters['trail_id'])) {
                    $query->whereHas('event', function($q) use ($filters) {
                        $q->where('trail_id', $filters['trail_id']);
                    });
                }
                
                if (!empty($filters['status'])) {
                    $query->where('status', $filters['status']);
                }

                $bookings = $query->with(['event.trail'])->get();

                $bookingData = $bookings->map(function($booking) use ($filters) {
                    $data = [
                        'booking_id' => $booking->id,
                        'trail_name' => $booking->event->trail->trail_name ?? 'N/A',
                        'event_date' => $booking->event->start_at ? Carbon::parse($booking->event->start_at)->format('Y-m-d') : 'N/A',
                        'party_size' => $booking->party_size ?? 1,
                        'status' => ucfirst($booking->status ?? 'pending'),
                        'amount' => '₱' . number_format($booking->getAmountInPesos(), 2),
                        'booking_date' => $booking->created_at->format('Y-m-d'),
                        'payment_status' => ucfirst($booking->payment_status ?? 'pending')
                    ];
                    
                    // Sanitize PII for organizations
                    if (!empty($filters['organization_id'])) {
                        return $this->sanitizeForOrganization($data);
                    }
                    
                    return $data;
                });

                // Calculate total revenue using getAmountInPesos()
                $totalRevenue = $bookings->sum(function($booking) {
                    return $booking->getAmountInPesos();
                });
                
                $confirmedBookings = $bookings->where('status', 'confirmed')->count();
                $pendingBookings = $bookings->where('status', 'pending')->count();
                $cancelledBookings = $bookings->where('status', 'cancelled')->count();

                return [
                    'title' => 'Booking Volumes Report',
                    'period' => $dateFrom->format('Y-m-d') . ' to ' . $dateTo->format('Y-m-d'),
                    'summary' => [
                        'total_bookings' => $bookings->count(),
                        'confirmed_bookings' => $confirmedBookings,
                        'pending_bookings' => $pendingBookings,
                        'cancelled_bookings' => $cancelledBookings,
                        'total_revenue' => '₱' . number_format($totalRevenue, 2),
                        'avg_party_size' => $bookings->count() > 0 ? round($bookings->avg('party_size'), 2) : 0,
                        'avg_booking_value' => $bookings->count() > 0 ? '₱' . number_format($totalRevenue / $bookings->count(), 2) : '₱0.00'
                    ],
                    'data' => $bookingData->toArray()
                ];
            });
        } catch (Exception $e) {
            Log::error('Booking volumes report error: ' . $e->getMessage());
            return $this->getEmptyReport('Booking Volumes Report', $dateFrom, $dateTo, $e->getMessage());
        }
    }

    /**
     * Generate Trail Popularity Report
     */
    protected function generateTrailPopularityReport($dateFrom, $dateTo, $filters)
    {
        try {
            return $this->executeWithRetry(function() use ($dateFrom, $dateTo, $filters) {
                $query = Trail::query();
                
                // Scope to organization's trails
                if (!empty($filters['organization_id'])) {
                    $query->where('user_id', $filters['organization_id']);
                }
                
                if (!empty($filters['trail_id'])) {
                    $query->where('id', $filters['trail_id']);
                }

                $trails = $query->with(['reviews', 'events.bookings'])->get();

                $trailData = $trails->map(function($trail) use ($dateFrom, $dateTo) {
                    // Count bookings in date range
                    $bookingCount = 0;
                    $uniqueHikers = collect();
                    
                    foreach ($trail->events as $event) {
                        foreach ($event->bookings as $booking) {
                            if ($booking->created_at->between($dateFrom, $dateTo) && $booking->status !== 'cancelled') {
                                $bookingCount++;
                                $uniqueHikers->push($booking->user_id);
                            }
                        }
                    }

                    // Reviews in date range
                    $reviews = $trail->reviews->whereBetween('created_at', [$dateFrom, $dateTo]);
                    
                    return [
                        'trail_id' => $trail->id,
                        'trail_name' => $trail->trail_name ?? $trail->name,
                        'difficulty' => $trail->difficulty ?? 'N/A',
                        'length' => $trail->length ?? 'N/A',
                        'booking_count' => $bookingCount,
                        'unique_hikers' => $uniqueHikers->unique()->count(),
                        'avg_rating' => $reviews->count() > 0 ? round($reviews->avg('rating'), 2) : 0,
                        'review_count' => $reviews->count()
                    ];
                });

                $totalBookings = $trailData->sum('booking_count');
                $mostPopular = $trailData->sortByDesc('booking_count')->first();

                return [
                    'title' => 'Trail Popularity Report',
                    'period' => $dateFrom->format('Y-m-d') . ' to ' . $dateTo->format('Y-m-d'),
                    'summary' => [
                        'total_trails' => $trailData->count(),
                        'total_bookings' => $totalBookings,
                        'total_unique_hikers' => $trailData->sum('unique_hikers'),
                        'most_popular_trail' => $mostPopular['trail_name'] ?? 'N/A',
                        'most_popular_bookings' => $mostPopular['booking_count'] ?? 0,
                        'avg_rating_overall' => $trailData->count() > 0 ? round($trailData->avg('avg_rating'), 2) : 0,
                        'trails_with_reviews' => $trailData->where('review_count', '>', 0)->count()
                    ],
                    'data' => $trailData->toArray()
                ];
            });
        } catch (Exception $e) {
            Log::error('Trail popularity report error: ' . $e->getMessage());
            return $this->getEmptyReport('Trail Popularity Report', $dateFrom, $dateTo, $e->getMessage());
        }
    }

    /**
     * Generate Emergency Readiness Report
     */
    protected function generateEmergencyReadinessReport($dateFrom, $dateTo, $filters)
    {
        try {
            return $this->executeWithRetry(function() use ($dateFrom, $dateTo, $filters) {
                // Check if emergency_readiness table exists
                if (!$this->tableExists('emergency_readiness')) {
                    throw new Exception('Emergency readiness data is not available. Please create the emergency_readiness table first.');
                }

                $query = DB::table('emergency_readiness')
                    ->whereBetween('created_at', [$dateFrom, $dateTo]);
                
                // Scope to organization's trails
                if (!empty($filters['organization_id'])) {
                    $trailIds = $this->getOrganizationTrailIds($filters['organization_id']);
                    $query->whereIn('trail_id', $trailIds);
                }
                
                if (!empty($filters['trail_id'])) {
                    $query->where('trail_id', $filters['trail_id']);
                }

                $records = $query->get()->map(function($item) {
                    $trail = Trail::find($item->trail_id);
                    $equipmentStatus = $item->equipment_status ?? 0;
                    $staffAvailability = $item->staff_availability ?? 0;
                    $communicationStatus = $item->communication_status ?? 0;
                    $overallScore = $this->calculateReadinessScore($item);
                    
                    return [
                        'readiness_id' => $item->id,
                        'trail_name' => $trail ? ($trail->trail_name ?? $trail->name) : 'Unknown Trail',
                        'equipment_status' => $equipmentStatus,
                        'staff_availability' => $staffAvailability,
                        'communication_status' => $communicationStatus,
                        'overall_score' => $overallScore,
                        'readiness_level' => $this->getReadinessLevel($overallScore),
                        'assessment_date' => isset($item->created_at) ? Carbon::parse($item->created_at)->format('Y-m-d') : 'N/A',
                    ];
                });

                return [
                    'title' => 'Emergency Readiness Report',
                    'period' => $dateFrom->format('Y-m-d') . ' to ' . $dateTo->format('Y-m-d'),
                    'summary' => [
                        'total_assessments' => $records->count(),
                        'average_readiness_score' => $records->count() > 0 ? round($records->avg('overall_score'), 2) : 0,
                        'excellent_readiness' => $records->where('readiness_level', 'Excellent')->count(),
                        'good_readiness' => $records->where('readiness_level', 'Good')->count(),
                        'fair_readiness' => $records->where('readiness_level', 'Fair')->count(),
                        'needs_improvement' => $records->where('readiness_level', 'Needs Improvement')->count(),
                        'avg_equipment_status' => $records->count() > 0 ? round($records->avg('equipment_status'), 2) : 0,
                        'avg_staff_availability' => $records->count() > 0 ? round($records->avg('staff_availability'), 2) : 0,
                        'avg_communication_status' => $records->count() > 0 ? round($records->avg('communication_status'), 2) : 0
                    ],
                    'data' => $records->toArray()
                ];
            });
        } catch (Exception $e) {
            Log::error('Emergency readiness report error: ' . $e->getMessage());
            return $this->getEmptyReport('Emergency Readiness Report', $dateFrom, $dateTo, $e->getMessage());
        }
    }

    /**
     * Generate Safety Incidents Report
     */
    protected function generateSafetyIncidentsReport($dateFrom, $dateTo, $filters)
    {
        try {
            return $this->executeWithRetry(function() use ($dateFrom, $dateTo, $filters) {
                // Check if safety_incidents table exists
                if (!$this->tableExists('safety_incidents')) {
                    throw new Exception('Safety incidents data is not available. Please create the safety_incidents table first.');
                }

                $columns = $this->getTableColumns('safety_incidents');
                
                // Use appropriate date column
                $dateColumn = 'created_at';
                if (in_array('occurred_at', $columns)) {
                    $dateColumn = 'occurred_at';
                } elseif (in_array('incident_date', $columns)) {
                    $dateColumn = 'incident_date';
                }

                $query = DB::table('safety_incidents')
                    ->whereBetween($dateColumn, [$dateFrom, $dateTo]);
                
                // Scope to organization's trails
                if (!empty($filters['organization_id']) && in_array('trail_id', $columns)) {
                    $trailIds = $this->getOrganizationTrailIds($filters['organization_id']);
                    $query->whereIn('trail_id', $trailIds);
                }
                
                if (!empty($filters['trail_id']) && in_array('trail_id', $columns)) {
                    $query->where('trail_id', $filters['trail_id']);
                }
                if (!empty($filters['severity']) && in_array('severity', $columns)) {
                    $query->where('severity', $filters['severity']);
                }

                $records = $query->get()->map(function($item) use ($dateColumn, $filters) {
                    $trail = isset($item->trail_id) ? Trail::find($item->trail_id) : null;
                    $incidentDate = isset($item->$dateColumn) ? Carbon::parse($item->$dateColumn) : null;
                    $daysSinceOccurred = $incidentDate ? $incidentDate->diffInDays(Carbon::now()) : null;
                    
                    $data = [
                        'incident_id' => $item->id,
                        'trail_name' => $trail ? ($trail->trail_name ?? $trail->name) : 'Unknown Trail',
                        'description' => isset($item->description) ? 
                            (substr($item->description, 0, 200) . (strlen($item->description) > 200 ? '...' : '')) : 'N/A',
                        'severity' => $item->severity ?? 'Unknown',
                        'status' => $item->status ?? 'Open',
                        'occurred_at' => $incidentDate ? $incidentDate->format('Y-m-d H:i:s') : 'N/A',
                        'days_since_occurred' => $daysSinceOccurred ?? 'N/A',
                    ];
                    
                    // Sanitize for organizations
                    if (!empty($filters['organization_id'])) {
                        return $this->sanitizeForOrganization($data);
                    }
                    
                    return $data;
                });

                $severityDistribution = [
                    'Critical' => $records->where('severity', 'Critical')->count(),
                    'High' => $records->where('severity', 'High')->count(),
                    'Medium' => $records->where('severity', 'Medium')->count(),
                    'Low' => $records->where('severity', 'Low')->count(),
                ];

                return [
                    'title' => 'Safety Incidents Report',
                    'period' => $dateFrom->format('Y-m-d') . ' to ' . $dateTo->format('Y-m-d'),
                    'summary' => [
                        'total_incidents' => $records->count(),
                        'critical_incidents' => $severityDistribution['Critical'],
                        'high_severity' => $severityDistribution['High'],
                        'medium_severity' => $severityDistribution['Medium'],
                        'low_severity' => $severityDistribution['Low'],
                        'resolved_incidents' => $records->where('status', 'Resolved')->count(),
                        'open_incidents' => $records->where('status', 'Open')->count(),
                    ],
                    'data' => $records->toArray()
                ];
            });
        } catch (Exception $e) {
            Log::error('Safety incidents report error: ' . $e->getMessage());
            return $this->getEmptyReport('Safety Incidents Report', $dateFrom, $dateTo, $e->getMessage());
        }
    }

    /**
     * Generate Feedback Summary Report
     */
    protected function generateFeedbackSummaryReport($dateFrom, $dateTo, $filters)
    {
        try {
            return $this->executeWithRetry(function() use ($dateFrom, $dateTo, $filters) {
                $query = TrailReview::whereBetween('created_at', [$dateFrom, $dateTo]);
                
                // Scope to organization's trails
                if (!empty($filters['organization_id'])) {
                    $trailIds = $this->getOrganizationTrailIds($filters['organization_id']);
                    $query->whereIn('trail_id', $trailIds);
                }
                
                if (!empty($filters['trail_id'])) {
                    $query->where('trail_id', $filters['trail_id']);
                }
                if (!empty($filters['rating'])) {
                    $query->where('rating', $filters['rating']);
                }

                $feedbacks = $query->with('trail')->get()->map(function($item) use ($filters) {
                    $comment = $item->comment ?? $item->review ?? '';
                    $sentiment = $this->analyzeSentiment($comment);
                    
                    $data = [
                        'feedback_id' => $item->id,
                        'trail_name' => $item->trail ? ($item->trail->trail_name ?? $item->trail->name) : 'N/A',
                        'rating' => $item->rating ?? 0,
                        'comment_preview' => substr($comment, 0, 100) . (strlen($comment) > 100 ? '...' : ''),
                        'sentiment' => $sentiment,
                        'has_comment' => !empty($comment),
                        'created_at' => $item->created_at ? $item->created_at->format('Y-m-d') : 'N/A'
                    ];
                    
                    // Sanitize for organizations - remove any user identifiers
                    if (!empty($filters['organization_id'])) {
                        return $this->sanitizeForOrganization($data);
                    }
                    
                    return $data;
                });

                $ratingDistribution = [
                    '5_star' => $feedbacks->where('rating', 5)->count(),
                    '4_star' => $feedbacks->where('rating', 4)->count(),
                    '3_star' => $feedbacks->where('rating', 3)->count(),
                    '2_star' => $feedbacks->where('rating', 2)->count(),
                    '1_star' => $feedbacks->where('rating', 1)->count(),
                ];

                return [
                    'title' => 'Feedback Summary Report',
                    'period' => $dateFrom->format('Y-m-d') . ' to ' . $dateTo->format('Y-m-d'),
                    'summary' => [
                        'total_feedbacks' => $feedbacks->count(),
                        'average_rating' => $feedbacks->count() > 0 ? round($feedbacks->avg('rating'), 2) : 0,
                        'positive_sentiment' => $feedbacks->where('sentiment', 'positive')->count(),
                        'neutral_sentiment' => $feedbacks->where('sentiment', 'neutral')->count(),
                        'negative_sentiment' => $feedbacks->where('sentiment', 'negative')->count(),
                        'feedbacks_with_comments' => $feedbacks->where('has_comment', true)->count(),
                        'rating_distribution' => $ratingDistribution,
                    ],
                    'data' => $feedbacks->toArray()
                ];
            });
        } catch (Exception $e) {
            Log::error('Feedback summary report error: ' . $e->getMessage());
            return $this->getEmptyReport('Feedback Summary Report', $dateFrom, $dateTo, $e->getMessage());
        }
    }

    // Helper methods

    private function calculateReadinessScore($item)
    {
        $equipment = $item->equipment_status ?? 0;
        $staff = $item->staff_availability ?? 0;
        $communication = $item->communication_status ?? 0;
        
        return round(($equipment + $staff + $communication) / 3, 2);
    }

    private function getReadinessLevel($score)
    {
        if ($score >= 90) return 'Excellent';
        if ($score >= 75) return 'Good';
        if ($score >= 60) return 'Fair';
        return 'Needs Improvement';
    }

    private function analyzeSentiment($text)
    {
        if (empty($text)) return 'neutral';
        
        $positiveWords = ['great', 'excellent', 'amazing', 'wonderful', 'good', 'beautiful', 'love', 'enjoyed', 'recommend'];
        $negativeWords = ['bad', 'poor', 'terrible', 'horrible', 'worst', 'disappointing', 'dangerous', 'difficult', 'hate'];
        
        $text = strtolower($text);
        $positiveCount = 0;
        $negativeCount = 0;
        
        foreach ($positiveWords as $word) {
            if (strpos($text, $word) !== false) $positiveCount++;
        }
        
        foreach ($negativeWords as $word) {
            if (strpos($text, $word) !== false) $negativeCount++;
        }
        
        if ($positiveCount > $negativeCount) return 'positive';
        if ($negativeCount > $positiveCount) return 'negative';
        return 'neutral';
    }

    private function tableExists($tableName)
    {
        try {
            return DB::getSchemaBuilder()->hasTable($tableName);
        } catch (Exception $e) {
            Log::warning("Could not check if table {$tableName} exists: " . $e->getMessage());
            return false;
        }
    }

    private function getTableColumns($tableName)
    {
        try {
            return DB::getSchemaBuilder()->getColumnListing($tableName);
        } catch (Exception $e) {
            Log::warning("Could not get columns for table {$tableName}: " . $e->getMessage());
            return [];
        }
    }

    private function getReportTitle($reportType)
    {
        $titles = [
            'overall_transactions' => 'Overall Transactions Report',
            'booking_volumes' => 'Booking Volumes Report',
            'trail_popularity' => 'Trail Popularity Report',
            'emergency_readiness' => 'Emergency Readiness Report',
            'safety_incidents' => 'Safety Incidents Report',
            'feedback_summary' => 'Feedback Summary Report',
        ];
        
        return $titles[$reportType] ?? 'Report';
    }

    private function getEmptyReport($title, $dateFrom, $dateTo, $errorMessage)
    {
        return [
            'error' => true,
            'message' => $errorMessage,
            'title' => $title,
            'period' => $dateFrom->format('Y-m-d') . ' to ' . $dateTo->format('Y-m-d'),
            'summary' => ['error' => $errorMessage],
            'data' => []
        ];
    }

    /**
     * Get dashboard statistics for organizations
     */
    public function getDashboardStats($organizationId = null)
    {
        try {
            $trailQuery = Trail::query();
            if ($organizationId) {
                $trailQuery->where('user_id', $organizationId);
            }
            $totalTrails = $trailQuery->count();

            $bookingQuery = Booking::whereMonth('created_at', Carbon::now()->month);
            if ($organizationId) {
                $trailIds = $this->getOrganizationTrailIds($organizationId);
                $bookingQuery->whereHas('event', function($q) use ($trailIds) {
                    $q->whereIn('trail_id', $trailIds);
                });
            }
            $totalBookings = $bookingQuery->count();

            $reviewQuery = TrailReview::query();
            if ($organizationId) {
                $trailIds = $this->getOrganizationTrailIds($organizationId);
                $reviewQuery->whereIn('trail_id', $trailIds);
            }
            $avgRating = round($reviewQuery->avg('rating') ?? 0, 1);

            return [
                'total_users' => 0, // Not relevant for organizations
                'total_trails' => $totalTrails,
                'total_bookings' => $totalBookings,
                'avg_rating' => $avgRating,
            ];
        } catch (Exception $e) {
            Log::error('Dashboard stats error: ' . $e->getMessage());
            return [
                'total_users' => 0,
                'total_trails' => 0,
                'total_bookings' => 0,
                'avg_rating' => 0,
            ];
        }
    }
}

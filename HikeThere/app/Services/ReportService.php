<?php

namespace App\Services;

use App\Models\User;
use App\Models\Trail;
use App\Models\Booking;
use App\Models\LoginLog;
use App\Models\Feedback;
use App\Models\SafetyIncident;
use App\Models\CommunityPost;
use App\Models\EmergencyReadiness;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Carbon\Carbon;
use Exception;

class ReportService
{
    private $maxRetries = 3;
    private $retryDelay = 1; // seconds

    public function generateReport($reportType, $dateFrom, $dateTo, $filters = [])
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

            switch ($reportType) {
                case 'login_trends':
                    return $this->generateLoginTrendsReport($dateFrom, $dateTo, $filters);
                case 'user_engagement':
                    return $this->generateUserEngagementReport($dateFrom, $dateTo, $filters);
                case 'trail_popularity':
                    return $this->generateTrailPopularityReport($dateFrom, $dateTo, $filters);
                case 'booking_volumes':
                    return $this->generateBookingVolumesReport($dateFrom, $dateTo, $filters);
                case 'emergency_readiness':
                    return $this->generateEmergencyReadinessReport($dateFrom, $dateTo, $filters);
                case 'feedback_summary':
                    return $this->generateFeedbackSummaryReport($dateFrom, $dateTo, $filters);
                case 'safety_incidents':
                    return $this->generateSafetyIncidentsReport($dateFrom, $dateTo, $filters);
                case 'community_posts':
                    return $this->generateCommunityPostsReport($dateFrom, $dateTo, $filters);
                case 'account_moderation':
                    return $this->generateAccountModerationReport($dateFrom, $dateTo, $filters);
                case 'content_trends':
                    return $this->generateContentTrendsReport($dateFrom, $dateTo, $filters);
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

    protected function generateLoginTrendsReport($dateFrom, $dateTo, $filters)
    {
        try {
            return $this->executeWithRetry(function() use ($dateFrom, $dateTo, $filters) {
                // Check if login_logs table exists
                if (!$this->tableExists('login_logs')) {
                    // Return mock data if table doesn't exist
                    return [
                        'title' => 'Login Trends Report',
                        'period' => $dateFrom->format('Y-m-d') . ' to ' . $dateTo->format('Y-m-d'),
                        'summary' => [
                            'total_logins' => 0,
                            'unique_users' => 0,
                            'avg_daily_logins' => 0,
                            'reporting_days' => 0,
                            'peak_day' => 'N/A',
                            'lowest_day' => 'N/A',
                            'note' => 'login_logs table does not exist'
                        ],
                        'data' => []
                    ];
                }

                // Simple query without joins first
                $dailyLoginData = DB::select("
                    SELECT 
                        DATE(created_at) as login_date,
                        COUNT(*) as login_count,
                        COUNT(DISTINCT user_id) as unique_users
                    FROM login_logs 
                    WHERE created_at BETWEEN ? AND ?
                    GROUP BY DATE(created_at)
                    ORDER BY DATE(created_at) ASC
                ", [$dateFrom, $dateTo]);

                $dailyLogins = collect($dailyLoginData);
                $totalLogins = $dailyLogins->sum('login_count');

                // Separate query for total unique users
                $totalUniqueUsers = DB::select("
                    SELECT COUNT(DISTINCT user_id) as count
                    FROM login_logs 
                    WHERE created_at BETWEEN ? AND ?
                ", [$dateFrom, $dateTo]);
                
                $totalUniqueUsers = $totalUniqueUsers[0]->count ?? 0;
                $avgDailyLogins = $dailyLogins->count() > 0 ? round($totalLogins / $dailyLogins->count(), 2) : 0;

                return [
                    'title' => 'Login Trends Report',
                    'period' => $dateFrom->format('Y-m-d') . ' to ' . $dateTo->format('Y-m-d'),
                    'summary' => [
                        'total_logins' => $totalLogins,
                        'unique_users' => $totalUniqueUsers,
                        'avg_daily_logins' => $avgDailyLogins,
                        'reporting_days' => $dailyLogins->count(),
                        'peak_day' => $dailyLogins->sortByDesc('login_count')->first()->login_date ?? 'N/A',
                        'lowest_day' => $dailyLogins->sortBy('login_count')->first()->login_date ?? 'N/A'
                    ],
                    'data' => $dailyLogins->map(function($item) {
                        return [
                            'date' => $item->login_date,
                            'login_count' => $item->login_count,
                            'unique_users' => $item->unique_users
                        ];
                    })->toArray()
                ];
            });
        } catch (Exception $e) {
            Log::error('Login trends report error: ' . $e->getMessage());
            return $this->getEmptyReport('Login Trends Report', $dateFrom, $dateTo, $e->getMessage());
        }
    }




    protected function generateTrailPopularityReport($dateFrom, $dateTo, $filters)
    {
        try {
            return $this->executeWithRetry(function() use ($dateFrom, $dateTo, $filters) {
                // Check if trails table exists
                if (!$this->tableExists('trails')) {
                    return [
                        'title' => 'Trail Popularity Report',
                        'period' => $dateFrom->format('Y-m-d') . ' to ' . $dateTo->format('Y-m-d'),
                        'summary' => [
                            'total_trails' => 0,
                            'total_bookings' => 0,
                            'total_unique_hikers' => 0,
                            'most_popular_trail' => 'N/A',
                            'note' => 'trails table does not exist'
                        ],
                        'data' => []
                    ];
                }

                // Start with just trails data
                $trailsQuery = "SELECT id, name";
                
                // Check if optional columns exist
                $trailColumns = $this->getTableColumns('trails');
                if (in_array('difficulty_level', $trailColumns)) {
                    $trailsQuery .= ", difficulty_level";
                } else {
                    $trailsQuery .= ", 'N/A' as difficulty_level";
                }
                
                if (in_array('length_km', $trailColumns)) {
                    $trailsQuery .= ", length_km";
                } else {
                    $trailsQuery .= ", 0 as length_km";
                }
                
                $trailsQuery .= " FROM trails";
                
                // Add filters
                $params = [];
                $whereConditions = [];
                
                if (!empty($filters['region_id']) && in_array('region_id', $trailColumns)) {
                    $whereConditions[] = "region_id = ?";
                    $params[] = $filters['region_id'];
                }
                
                if (!empty($filters['trail_id'])) {
                    $whereConditions[] = "id = ?";
                    $params[] = $filters['trail_id'];
                }
                
                if (!empty($whereConditions)) {
                    $trailsQuery .= " WHERE " . implode(' AND ', $whereConditions);
                }

                $trails = DB::select($trailsQuery, $params);
                
                // For now, return trails with zero bookings/ratings
                // We'll add booking data once we know your table structure
                $trailData = collect($trails)->map(function($trail) {
                    return [
                        'id' => $trail->id,
                        'name' => $trail->name,
                        'difficulty_level' => $trail->difficulty_level ?? 'N/A',
                        'length_km' => $trail->length_km ?? 0,
                        'booking_count' => 0,
                        'unique_hikers' => 0,
                        'avg_rating' => 0,
                        'review_count' => 0
                    ];
                });

                return [
                    'title' => 'Trail Popularity Report',
                    'period' => $dateFrom->format('Y-m-d') . ' to ' . $dateTo->format('Y-m-d'),
                    'summary' => [
                        'total_trails' => $trailData->count(),
                        'total_bookings' => 0,
                        'total_unique_hikers' => 0,
                        'most_popular_trail' => $trailData->first()['name'] ?? 'N/A',
                        'most_popular_bookings' => 0,
                        'avg_rating_overall' => 0,
                        'trails_with_reviews' => 0,
                        'note' => 'Basic trail data only - booking/rating data requires table structure analysis'
                    ],
                    'data' => $trailData->toArray()
                ];
            });
        } catch (Exception $e) {
            Log::error('Trail popularity report error: ' . $e->getMessage());
            return $this->getEmptyReport('Trail Popularity Report', $dateFrom, $dateTo, $e->getMessage());
        }
    }


    protected function generateEmergencyReadinessReport($dateFrom, $dateTo, $filters)
    {
        try {
            return $this->executeWithRetry(function() use ($dateFrom, $dateTo, $filters) {
                // Check if emergency_readiness table exists
                if (!$this->tableExists('emergency_readiness')) {
                    throw new Exception('Emergency readiness data is not available. The emergency_readiness table does not exist.');
                }

                $query = DB::table('emergency_readiness')
                    ->whereBetween('created_at', [$dateFrom, $dateTo]);
                
                if (!empty($filters['trail_id'])) {
                    $query->where('trail_id', $filters['trail_id']);
                }

                $records = $query->get()->map(function($item) {
                    $equipmentStatus = $item->equipment_status ?? 0;
                    $staffAvailability = $item->staff_availability ?? 0;
                    $communicationStatus = $item->communication_status ?? 0;
                    $overallScore = $this->calculateReadinessScore($item);
                    
                    return [
                        'readiness_id' => $item->id,
                        'trail_id' => $item->trail_id ?? null,
                        'trail_name' => 'N/A', // Would need to join with trails table
                        'equipment_status' => $equipmentStatus,
                        'staff_availability' => $staffAvailability,
                        'communication_status' => $communicationStatus,
                        'overall_score' => $overallScore,
                        'readiness_level' => $this->getReadinessLevel($overallScore),
                        'assessment_date' => isset($item->created_at) ? Carbon::parse($item->created_at)->format('Y-m-d') : 'N/A',
                        'created_at' => isset($item->created_at) ? Carbon::parse($item->created_at)->format('Y-m-d H:i:s') : 'N/A'
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

    protected function generateSafetyIncidentsReport($dateFrom, $dateTo, $filters)
    {
        try {
            return $this->executeWithRetry(function() use ($dateFrom, $dateTo, $filters) {
                // Check if safety_incidents table exists
                if (!$this->tableExists('safety_incidents')) {
                    throw new Exception('Safety incidents data is not available. The safety_incidents table does not exist.');
                }

                $columns = $this->getTableColumns('safety_incidents');
                
                // Use appropriate date column
                $dateColumn = 'created_at'; // default
                if (in_array('occurred_at', $columns)) {
                    $dateColumn = 'occurred_at';
                } elseif (in_array('incident_date', $columns)) {
                    $dateColumn = 'incident_date';
                }

                $query = DB::table('safety_incidents')
                    ->whereBetween($dateColumn, [$dateFrom, $dateTo]);
                
                if (!empty($filters['trail_id']) && in_array('trail_id', $columns)) {
                    $query->where('trail_id', $filters['trail_id']);
                }
                if (!empty($filters['severity']) && in_array('severity', $columns)) {
                    $query->where('severity', $filters['severity']);
                }
                if (!empty($filters['status']) && in_array('status', $columns)) {
                    $query->where('status', $filters['status']);
                }

                $records = $query->get()->map(function($item) use ($dateColumn) {
                    $incidentDate = null;
                    if (isset($item->$dateColumn)) {
                        $incidentDate = Carbon::parse($item->$dateColumn);
                    }
                    
                    $daysSinceOccurred = $incidentDate ? $incidentDate->diffInDays(Carbon::now()) : null;
                    
                    return [
                        'incident_id' => $item->id,
                        'trail_id' => $item->trail_id ?? null,
                        'trail_name' => 'N/A', // Would need to join with trails
                        'description' => isset($item->description) ? 
                            (substr($item->description, 0, 200) . (strlen($item->description) > 200 ? '...' : '')) : 'N/A',
                        'severity' => $item->severity ?? 'Unknown',
                        'status' => $item->status ?? 'Open',
                        'reported_by' => 'N/A', // Would need to join with users
                        'reported_by_id' => $item->reported_by ?? null,
                        'occurred_at' => $incidentDate ? $incidentDate->format('Y-m-d H:i:s') : null,
                        'days_since_occurred' => $daysSinceOccurred,
                        'created_at' => isset($item->created_at) ? Carbon::parse($item->created_at)->format('Y-m-d H:i:s') : null
                    ];
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
                        'severity_distribution' => $severityDistribution,
                        'resolved_incidents' => $records->where('status', 'Resolved')->count(),
                        'pending_incidents' => $records->where('status', 'Pending')->count(),
                        'open_incidents' => $records->where('status', 'Open')->count(),
                        'avg_resolution_time' => 'N/A', // Would need resolution_date field
                        'incidents_per_day' => $dateFrom->diffInDays($dateTo) > 0 ? 
                            round($records->count() / $dateFrom->diffInDays($dateTo), 2) : $records->count()
                    ],
                    'data' => $records->toArray()
                ];
            });
        } catch (Exception $e) {
            Log::error('Safety incidents report error: ' . $e->getMessage());
            return $this->getEmptyReport('Safety Incidents Report', $dateFrom, $dateTo, $e->getMessage());
        }
    }

        protected function generateAccountModerationReport($dateFrom, $dateTo, $filters)
        {
            try {
                return $this->executeWithRetry(function() use ($dateFrom, $dateTo, $filters) {
                    $userColumns = $this->getTableColumns('users');
                    
                    // Build the WHERE clause based on available columns
                    $query = User::whereBetween('updated_at', [$dateFrom, $dateTo]);

                    // Only filter by moderation fields if they exist
                    $query->where(function($q) use ($userColumns) {
                        if (in_array('status', $userColumns)) {
                            $q->where('status', '!=', 'active');
                        }
                        if (in_array('suspended_at', $userColumns)) {
                            $q->orWhereNotNull('suspended_at');
                        }
                        if (in_array('banned_at', $userColumns)) {
                            $q->orWhereNotNull('banned_at');
                        }
                        if (in_array('warning_count', $userColumns)) {
                            $q->orWhere('warning_count', '>', 0);
                        }
                        // If none of the moderation columns exist, just get all users updated in period
                        if (!in_array('status', $userColumns) && 
                            !in_array('suspended_at', $userColumns) && 
                            !in_array('banned_at', $userColumns) && 
                            !in_array('warning_count', $userColumns)) {
                            $q->whereRaw('1=1'); // Include all users
                        }
                    });

                    if (!empty($filters['user_type']) && in_array('user_type', $userColumns)) {
                        $query->where('user_type', $filters['user_type']);
                    }
                    if (!empty($filters['status']) && in_array('status', $userColumns)) {
                        $query->where('status', $filters['status']);
                    }

                    $records = $query->get()->map(function($user) use ($userColumns) {
                        $daysSinceSuspended = null;
                        $daysSinceBanned = null;
                        
                        if (in_array('suspended_at', $userColumns) && $user->suspended_at) {
                            $daysSinceSuspended = Carbon::parse($user->suspended_at)->diffInDays(Carbon::now());
                        }
                        
                        if (in_array('banned_at', $userColumns) && $user->banned_at) {
                            $daysSinceBanned = Carbon::parse($user->banned_at)->diffInDays(Carbon::now());
                        }
                        
                        return [
                            'user_id' => $user->id,
                            'name' => $user->name ?? 'N/A',
                            'email' => $user->email ? substr($user->email, 0, 20) . '...' : 'N/A',
                            'user_type' => (in_array('user_type', $userColumns) ? $user->user_type : null) ?? 'N/A',
                            'status' => (in_array('status', $userColumns) ? $user->status : null) ?? 'active',
                            'warning_count' => (in_array('warning_count', $userColumns) ? $user->warning_count : null) ?? 0,
                            'suspended_at' => (in_array('suspended_at', $userColumns) && $user->suspended_at) ? 
                                $user->suspended_at->format('Y-m-d H:i:s') : null,
                            'banned_at' => (in_array('banned_at', $userColumns) && $user->banned_at) ? 
                                $user->banned_at->format('Y-m-d H:i:s') : null,
                            'days_since_suspended' => $daysSinceSuspended,
                            'days_since_banned' => $daysSinceBanned,
                            'last_violation' => (in_array('last_violation', $userColumns) ? $user->last_violation : null) ?? 'N/A',
                            'updated_at' => $user->updated_at ? $user->updated_at->format('Y-m-d H:i:s') : null
                        ];
                    });

                    return [
                        'title' => 'Account Moderation Report',
                        'period' => $dateFrom->format('Y-m-d') . ' to ' . $dateTo->format('Y-m-d'),
                        'summary' => [
                            'total_moderated_accounts' => $records->count(),
                            'suspended_accounts' => $records->whereNotNull('suspended_at')->count(),
                            'banned_accounts' => $records->whereNotNull('banned_at')->count(),
                            'accounts_with_warnings' => $records->where('warning_count', '>', 0)->count(),
                            'inactive_accounts' => $records->where('status', 'inactive')->count(),
                            'avg_warnings_per_account' => $records->count() > 0 ? round($records->avg('warning_count'), 2) : 0,
                            'total_warnings_issued' => $records->sum('warning_count')
                        ],
                        'data' => $records->toArray()
                    ];
                });
            } catch (Exception $e) {
                Log::error('Account moderation report error: ' . $e->getMessage());
                return $this->getEmptyReport('Account Moderation Report', $dateFrom, $dateTo, $e->getMessage());
            }
        }

        // Helper method to check if table exists
        private function tableExists($tableName)
        {
            try {
                return DB::getSchemaBuilder()->hasTable($tableName);
            } catch (Exception $e) {
                Log::warning("Could not check if table {$tableName} exists: " . $e->getMessage());
                return false;
            }
        }

        // Helper method to get table columns
        private function getTableColumns($tableName)
        {
            try {
                return DB::getSchemaBuilder()->getColumnListing($tableName);
            } catch (Exception $e) {
                Log::warning("Could not get columns for table {$tableName}: " . $e->getMessage());
                return [];
            }
        }

    protected function generateUserEngagementReport($dateFrom, $dateTo, $filters)
    {
        try {
            return $this->executeWithRetry(function() use ($dateFrom, $dateTo, $filters) {
                $query = User::query();
                if (!empty($filters['user_type'])) {
                    $query->where('user_type', $filters['user_type']);
                }

                $users = $query->with(['loginLogs' => function($q) use ($dateFrom, $dateTo) {
                    $q->whereBetween('created_at', [$dateFrom, $dateTo]);
                }])->get();

                $engagementData = $users->map(function($user) {
                    $loginCount = $user->loginLogs ? $user->loginLogs->count() : 0;
                    $engagementLevel = $this->calculateEngagementLevel($loginCount);
                    $lastLogin = $user->loginLogs && $user->loginLogs->isNotEmpty() 
                        ? $user->loginLogs->max('created_at')
                        : null;

                    return [
                        'user_id' => $user->id,
                        'name' => $user->name ?? 'Unknown',
                        'email' => substr($user->email ?? '', 0, 20) . '...',
                        'user_type' => $user->user_type ?? 'N/A',
                        'login_count' => $loginCount,
                        'last_login' => $lastLogin ? Carbon::parse($lastLogin)->format('Y-m-d H:i:s') : 'Never',
                        'engagement_level' => $engagementLevel,
                        'days_since_last_login' => $lastLogin ? Carbon::parse($lastLogin)->diffInDays(Carbon::now()) : 'N/A'
                    ];
                });

                $summary = [
                    'total_users' => $engagementData->count(),
                    'active_users' => $engagementData->where('login_count', '>', 0)->count(),
                    'high_engagement' => $engagementData->where('engagement_level', 'High')->count(),
                    'medium_engagement' => $engagementData->where('engagement_level', 'Medium')->count(),
                    'low_engagement' => $engagementData->where('engagement_level', 'Low')->count(),
                    'inactive' => $engagementData->where('engagement_level', 'Inactive')->count(),
                    'avg_logins_per_user' => $engagementData->count() > 0 ? round($engagementData->avg('login_count'), 2) : 0
                ];

                return [
                    'title' => 'User Engagement Report',
                    'period' => $dateFrom->format('Y-m-d') . ' to ' . $dateTo->format('Y-m-d'),
                    'summary' => $summary,
                    'data' => $engagementData->map(fn($item) => $this->safeValue($item))->toArray()
                ];
            });
        } catch (Exception $e) {
            Log::error('User engagement report error: ' . $e->getMessage());
            return $this->getEmptyReport('User Engagement Report', $dateFrom, $dateTo, $e->getMessage());
        }
    }

    protected function generateBookingVolumesReport($dateFrom, $dateTo, $filters)
    {
        try {
            return $this->executeWithRetry(function() use ($dateFrom, $dateTo, $filters) {
                $query = Booking::whereBetween('created_at', [$dateFrom, $dateTo]);
                if (!empty($filters['trail_id'])) {
                    $query->where('trail_id', $filters['trail_id']);
                }
                if (!empty($filters['status'])) {
                    $query->where('status', $filters['status']);
                }

                $dailyBookings = $query->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as total_bookings'),
                    DB::raw('SUM(CASE WHEN status = "confirmed" THEN 1 ELSE 0 END) as confirmed_bookings'),
                    DB::raw('SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled_bookings'),
                    DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_bookings'),
                    DB::raw('COUNT(DISTINCT user_id) as unique_users')
                )
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('date')
                ->get()
                ->map(fn($item) => $this->safeValue($item));

                $totalBookings = $dailyBookings->sum('total_bookings');
                $totalCancelled = $dailyBookings->sum('cancelled_bookings');
                $totalConfirmed = $dailyBookings->sum('confirmed_bookings');
                $cancellationRate = $totalBookings > 0 ? round(($totalCancelled / $totalBookings) * 100, 2) : 0;
                $confirmationRate = $totalBookings > 0 ? round(($totalConfirmed / $totalBookings) * 100, 2) : 0;

                return [
                    'title' => 'Booking Volumes Report',
                    'period' => $dateFrom->format('Y-m-d') . ' to ' . $dateTo->format('Y-m-d'),
                    'summary' => [
                        'total_bookings' => $totalBookings,
                        'confirmed_bookings' => $totalConfirmed,
                        'cancelled_bookings' => $totalCancelled,
                        'pending_bookings' => $dailyBookings->sum('pending_bookings'),
                        'cancellation_rate' => $cancellationRate . '%',
                        'confirmation_rate' => $confirmationRate . '%',
                        'avg_daily_bookings' => $dailyBookings->count() > 0 ? round($dailyBookings->avg('total_bookings'), 2) : 0,
                        'peak_booking_day' => $dailyBookings->sortByDesc('total_bookings')->first()['date'] ?? 'N/A',
                        'unique_customers' => $dailyBookings->sum('unique_users')
                    ],
                    'data' => $dailyBookings->toArray()
                ];
            });
        } catch (Exception $e) {
            Log::error('Booking volumes report error: ' . $e->getMessage());
            return $this->getEmptyReport('Booking Volumes Report', $dateFrom, $dateTo, $e->getMessage());
        }
    }

    protected function generateFeedbackSummaryReport($dateFrom, $dateTo, $filters)
    {
        try {
            return $this->executeWithRetry(function() use ($dateFrom, $dateTo, $filters) {
                $query = Feedback::whereBetween('created_at', [$dateFrom, $dateTo]);
                if (!empty($filters['trail_id'])) {
                    $query->where('trail_id', $filters['trail_id']);
                }
                if (!empty($filters['rating'])) {
                    $query->where('rating', $filters['rating']);
                }

                $feedbacks = $query->with(['trail', 'user'])->get()->map(function($item) {
                    $comment = $item->comment ?? '';
                    $sentiment = $this->analyzeSentiment($comment);
                    
                    return [
                        'feedback_id' => $item->id,
                        'user_id' => $item->user_id ?? null,
                        'user_name' => $item->user ? $item->user->name : 'Anonymous',
                        'trail_id' => $item->trail_id ?? null,
                        'trail_name' => $item->trail ? $item->trail->name : 'N/A',
                        'rating' => $item->rating ?? 0,
                        'comment_preview' => substr($comment, 0, 100) . (strlen($comment) > 100 ? '...' : ''),
                        'comment_length' => strlen($comment),
                        'sentiment' => $sentiment,
                        'has_comment' => !empty($comment),
                        'created_at' => $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : 'N/A'
                    ];
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
                        'avg_comment_length' => $feedbacks->where('has_comment', true)->count() > 0 ? 
                            round($feedbacks->where('has_comment', true)->avg('comment_length'), 0) : 0
                    ],
                    'data' => $feedbacks->toArray()
                ];
            });
        } catch (Exception $e) {
            Log::error('Feedback summary report error: ' . $e->getMessage());
            return $this->getEmptyReport('Feedback Summary Report', $dateFrom, $dateTo, $e->getMessage());
        }
    }

    protected function generateCommunityPostsReport($dateFrom, $dateTo, $filters)
    {
        try {
            return $this->executeWithRetry(function() use ($dateFrom, $dateTo, $filters) {
                // Check if community_posts table exists
                if (!$this->tableExists('community_posts')) {
                    throw new Exception('Community posts data is not available. The community_posts table does not exist.');
                }

                $query = DB::table('community_posts')->whereBetween('created_at', [$dateFrom, $dateTo]);
                if (!empty($filters['trail_id'])) {
                    $query->where('trail_id', $filters['trail_id']);
                }
                if (!empty($filters['status'])) {
                    $query->where('status', $filters['status']);
                }

                $posts = $query->get()->map(function($item) {
                    $content = $item->content ?? '';
                    $engagementScore = ($item->likes_count ?? 0) + (($item->comments_count ?? 0) * 2);
                    
                    return [
                        'post_id' => $item->id,
                        'user_id' => $item->user_id ?? null,
                        'user_name' => 'N/A', // Would need to join with users
                        'trail_id' => $item->trail_id ?? null,
                        'trail_name' => 'N/A', // Would need to join with trails
                        'title' => $item->title ?? 'No Title',
                        'content_preview' => substr($content, 0, 100) . (strlen($content) > 100 ? '...' : ''),
                        'content_length' => strlen($content),
                        'likes_count' => $item->likes_count ?? 0,
                        'comments_count' => $item->comments_count ?? 0,
                        'engagement_score' => $engagementScore,
                        'status' => $item->status ?? 'Active',
                        'hashtags' => $this->extractPostHashtags($content),
                        'category' => $this->categorizePost($content),
                        'created_at' => isset($item->created_at) ? Carbon::parse($item->created_at)->format('Y-m-d H:i:s') : null,
                    ];
                });

                return [
                    'title' => 'Community Posts Report',
                    'period' => $dateFrom->format('Y-m-d') . ' to ' . $dateTo->format('Y-m-d'),
                    'summary' => [
                        'total_posts' => $posts->count(),
                        'active_posts' => $posts->where('status', 'Active')->count(),
                        'inactive_posts' => $posts->where('status', '!=', 'Active')->count(),
                        'total_likes' => $posts->sum('likes_count'),
                        'total_comments' => $posts->sum('comments_count'),
                        'total_engagement' => $posts->sum('engagement_score'),
                        'avg_engagement_per_post' => $posts->count() > 0 ? round($posts->avg('engagement_score'), 2) : 0,
                        'most_engaged_post_id' => $posts->sortByDesc('engagement_score')->first()['post_id'] ?? 'N/A',
                        'posts_per_day' => $dateFrom->diffInDays($dateTo) > 0 ? 
                            round($posts->count() / $dateFrom->diffInDays($dateTo), 2) : $posts->count()
                    ],
                    'data' => $posts->toArray()
                ];
            });
        } catch (Exception $e) {
            Log::error('Community posts report error: ' . $e->getMessage());
            return $this->getEmptyReport('Community Posts Report', $dateFrom, $dateTo, $e->getMessage());
        }
    }

    protected function generateContentTrendsReport($dateFrom, $dateTo, $filters)
    {
        try {
            return $this->executeWithRetry(function() use ($dateFrom, $dateTo, $filters) {
                // Check if community_posts table exists
                if (!$this->tableExists('community_posts')) {
                    throw new Exception('Content trends data is not available. The community_posts table does not exist.');
                }

                $query = DB::table('community_posts')->whereBetween('created_at', [$dateFrom, $dateTo]);
                if (!empty($filters['trail_id'])) {
                    $query->where('trail_id', $filters['trail_id']);
                }

                $posts = $query->get();

                $trendingHashtags = $this->extractHashtags($posts);
                $categoryCounts = $this->categorizeContent($posts);
                $dailyPostCounts = $this->getDailyPostCounts($posts);

                $postsData = $posts->map(function($item) {
                    $content = $item->content ?? '';
                    $hashtags = $this->extractPostHashtags($content);
                    $category = $this->categorizePost($content);
                    $engagementScore = ($item->likes_count ?? 0) + (($item->comments_count ?? 0) * 2);
                    
                    return [
                        'post_id' => $item->id,
                        'user_name' => 'N/A', // Would need to join with users
                        'trail_name' => 'N/A', // Would need to join with trails
                        'content_preview' => substr($content, 0, 100) . (strlen($content) > 100 ? '...' : ''),
                        'content_length' => strlen($content),
                        'hashtags' => $hashtags,
                        'hashtag_count' => substr_count($hashtags, ',') + (empty($hashtags) ? 0 : 1),
                        'category' => $category,
                        'engagement_score' => $engagementScore,
                        'likes_count' => $item->likes_count ?? 0,
                        'comments_count' => $item->comments_count ?? 0,
                        'created_at' => isset($item->created_at) ? Carbon::parse($item->created_at)->format('Y-m-d H:i:s') : null
                    ];
                });

                return [
                    'title' => 'Content Trends Report',
                    'period' => $dateFrom->format('Y-m-d') . ' to ' . $dateTo->format('Y-m-d'),
                    'summary' => [
                        'total_posts' => $posts->count(),
                        'top_10_hashtags' => $trendingHashtags->take(10)->toArray(),
                        'content_categories' => $categoryCounts->toArray(),
                        'avg_daily_posts' => $dailyPostCounts->count() > 0 ? round($dailyPostCounts->avg(), 2) : 0,
                        'peak_posting_day' => $dailyPostCounts->keys()->first() ?? 'N/A',
                        'total_hashtags_used' => $trendingHashtags->count(),
                        'avg_engagement_per_post' => $posts->count() > 0 ? 
                            round($postsData->avg('engagement_score'), 2) : 0,
                        'most_engaging_category' => $categoryCounts->keys()->first() ?? 'N/A'
                    ],
                    'data' => $postsData->toArray()
                ];
            });
        } catch (Exception $e) {
            Log::error('Content trends report error: ' . $e->getMessage());
            return $this->getEmptyReport('Content Trends Report', $dateFrom, $dateTo, $e->getMessage());
        }
    }

    /**
     * Generate PDF from report data
     */
    public function generatePDF($reportData, $reportType)
    {
        try {
            $fileName = 'report_' . $reportType . '_' . time() . '.pdf';
            $filePath = storage_path('app/reports/' . $fileName);
            
            // Ensure directory exists
            if (!file_exists(storage_path('app/reports'))) {
                mkdir(storage_path('app/reports'), 0755, true);
            }

            $html = $this->generateReportHTML($reportData);
            $pdf = PDF::loadHTML($html)->setPaper('a4', 'landscape');
            $pdf->save($filePath);

            return $filePath;
        } catch (Exception $e) {
            Log::error('PDF generation error: ' . $e->getMessage());
            throw new Exception('Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Generate Excel from report data
     */
    public function generateExcel($reportData, $reportType)
    {
        try {
            $fileName = 'report_' . $reportType . '_' . time() . '.xlsx';
            $filePath = storage_path('app/reports/' . $fileName);
            
            // Ensure directory exists
            if (!file_exists(storage_path('app/reports'))) {
                mkdir(storage_path('app/reports'), 0755, true);
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set title and metadata
            $sheet->setCellValue('A1', $reportData['title'] ?? 'Report');
            $sheet->setCellValue('A2', 'Period: ' . ($reportData['period'] ?? 'N/A'));
            $sheet->setCellValue('A3', 'Generated: ' . Carbon::now()->format('Y-m-d H:i:s'));
            
            // Style the headers
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A2:A3')->getFont()->setBold(true)->setSize(12);
            
            $currentRow = 5;
            
            // Handle error case
            if (isset($reportData['error']) && $reportData['error']) {
                $sheet->setCellValue('A' . $currentRow, 'Error: ' . ($reportData['message'] ?? 'Unknown error'));
                $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->getColor()->setRGB('FF0000');
                $currentRow += 2;
            }
            
            // Add summary if exists
            if (!empty($reportData['summary']) && is_array($reportData['summary'])) {
                $sheet->setCellValue('A' . $currentRow, 'Summary Statistics');
                $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(14);
                $currentRow += 2;
                
                foreach ($reportData['summary'] as $key => $value) {
                    $displayKey = ucwords(str_replace('_', ' ', $key));
                    $displayValue = $this->formatValueForExcel($value);
                    
                    $sheet->setCellValue('A' . $currentRow, $displayKey);
                    $sheet->setCellValue('B' . $currentRow, $displayValue);
                    $currentRow++;
                }
                $currentRow += 2;
            }
            
            // Add detailed data if exists
            if (!empty($reportData['data']) && is_array($reportData['data']) && count($reportData['data']) > 0) {
                $sheet->setCellValue('A' . $currentRow, 'Detailed Data');
                $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(14);
                $currentRow += 2;
                
                $firstRow = current($reportData['data']);
                if ($firstRow && is_array($firstRow)) {
                    // Headers
                    $col = 'A';
                    foreach (array_keys($firstRow) as $header) {
                        $displayHeader = ucwords(str_replace('_', ' ', $header));
                        $sheet->setCellValue($col . $currentRow, $displayHeader);
                        $sheet->getStyle($col . $currentRow)->getFont()->setBold(true);
                        $sheet->getStyle($col . $currentRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E0E0E0');
                        $col++;
                    }
                    $currentRow++;
                    
                    // Data rows
                    foreach ($reportData['data'] as $row) {
                        $col = 'A';
                        foreach ($row as $value) {
                            $displayValue = $this->formatValueForExcel($value);
                            $sheet->setCellValue($col . $currentRow, $displayValue);
                            $col++;
                        }
                        $currentRow++;
                    }
                }
            } else {
                $sheet->setCellValue('A' . $currentRow, 'No data available for the selected period');
                $sheet->getStyle('A' . $currentRow)->getFont()->setItalic(true);
            }
            
            // Auto-size columns
            foreach (range('A', 'Z') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            $writer = new Xlsx($spreadsheet);
            $writer->save($filePath);
            
            return $filePath;
        } catch (Exception $e) {
            Log::error('Excel generation error: ' . $e->getMessage());
            throw new Exception('Failed to generate Excel: ' . $e->getMessage());
        }
    }

    /**
     * Generate CSV from report data
     */
    public function generateCSV($reportData, $reportType)
    {
        try {
            $fileName = 'report_' . $reportType . '_' . time() . '.csv';
            $filePath = storage_path('app/reports/' . $fileName);
            
            // Ensure directory exists
            if (!file_exists(storage_path('app/reports'))) {
                mkdir(storage_path('app/reports'), 0755, true);
            }

            $handle = fopen($filePath, 'w');
            
            if (!$handle) {
                throw new Exception('Cannot create CSV file');
            }
            
            // Write BOM for UTF-8
            fwrite($handle, "\xEF\xBB\xBF");
            
            // Write title and metadata
            fputcsv($handle, [$reportData['title'] ?? 'Report']);
            fputcsv($handle, ['Period: ' . ($reportData['period'] ?? 'N/A')]);
            fputcsv($handle, ['Generated: ' . Carbon::now()->format('Y-m-d H:i:s')]);
            fputcsv($handle, []); // Empty line
            
            // Handle error case
            if (isset($reportData['error']) && $reportData['error']) {
                fputcsv($handle, ['Error: ' . ($reportData['message'] ?? 'Unknown error')]);
                fputcsv($handle, []);
            }
            
            // Write summary
            if (!empty($reportData['summary']) && is_array($reportData['summary'])) {
                fputcsv($handle, ['Summary Statistics']);
                foreach ($reportData['summary'] as $key => $value) {
                    $displayKey = ucwords(str_replace('_', ' ', $key));
                    $displayValue = $this->formatValueForCSV($value);
                    fputcsv($handle, [$displayKey, $displayValue]);
                }
                fputcsv($handle, []); // Empty line
            }
            
            // Write detailed data
            if (!empty($reportData['data']) && is_array($reportData['data']) && count($reportData['data']) > 0) {
                fputcsv($handle, ['Detailed Data']);
                
                $firstRow = current($reportData['data']);
                if ($firstRow && is_array($firstRow)) {
                    // Headers
                    $headers = array_map(function($header) {
                        return ucwords(str_replace('_', ' ', $header));
                    }, array_keys($firstRow));
                    fputcsv($handle, $headers);
                    
                    // Data rows
                    foreach ($reportData['data'] as $row) {
                        $csvRow = array_map(function($value) {
                            return $this->formatValueForCSV($value);
                        }, array_values($row));
                        fputcsv($handle, $csvRow);
                    }
                }
            } else {
                fputcsv($handle, ['No data available for the selected period']);
            }
            
            fclose($handle);
            return $filePath;
        } catch (Exception $e) {
            Log::error('CSV generation error: ' . $e->getMessage());
            throw new Exception('Failed to generate CSV: ' . $e->getMessage());
        }
    }

    /**
     * Process offline log files
     */
    public function processOfflineLog($filePath)
    {
        try {
            $fullPath = storage_path('app/' . $filePath);
            if (!file_exists($fullPath)) {
                throw new Exception('File not found: ' . $filePath);
            }

            $content = file_get_contents($fullPath);
            if ($content === false) {
                throw new Exception('Cannot read file content');
            }

            $data = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Try to parse as CSV if JSON fails
                $data = $this->parseCSVContent($content);
            }

            if (!is_array($data)) {
                throw new Exception('Invalid file format - expected JSON or CSV');
            }

            $processedRecords = 0;
            $errors = [];

            foreach ($data as $index => $record) {
                try {
                    if (!is_array($record)) {
                        $errors[] = "Record {$index}: Invalid record format";
                        continue;
                    }
                    
                    $this->processLogRecord($record);
                    $processedRecords++;
                } catch (Exception $e) {
                    $errors[] = "Record {$index}: " . $e->getMessage();
                }
            }

            // Clean up uploaded file
            unlink($fullPath);

            return [
                'processed_records' => $processedRecords,
                'total_records' => count($data),
                'errors' => $errors,
                'success_rate' => count($data) > 0 ? round(($processedRecords / count($data)) * 100, 2) : 0
            ];
        } catch (Exception $e) {
            Log::error('Offline log processing error: ' . $e->getMessage());
            throw new Exception('Failed to process offline log: ' . $e->getMessage());
        }
    }

    protected function parseCSVContent($content)
    {
        $lines = explode("\n", $content);
        $data = [];
        $headers = null;

        foreach ($lines as $lineNum => $line) {
            $line = trim($line);
            if (empty($line)) continue;

            try {
                $row = str_getcsv($line);
                if ($headers === null) {
                    $headers = $row;
                } else {
                    if (count($row) === count($headers)) {
                        $data[] = array_combine($headers, $row);
                    } else {
                        Log::warning("CSV line {$lineNum}: Column count mismatch");
                    }
                }
            } catch (Exception $e) {
                Log::warning("CSV line {$lineNum}: Parse error - " . $e->getMessage());
            }
        }

        return $data;
    }

    protected function processLogRecord($record)
    {
        // Validate required fields
        if (!isset($record['type'])) {
            throw new Exception('Missing record type');
        }

        // Process based on record type
        switch ($record['type']) {
            case 'login':
                $this->processLoginRecord($record);
                break;
            case 'booking':
                $this->processBookingRecord($record);
                break;
            case 'safety_incident':
                $this->processSafetyIncidentRecord($record);
                break;
            case 'feedback':
                $this->processFeedbackRecord($record);
                break;
            default:
                Log::info('Unknown record type: ' . $record['type']);
        }
    }

    protected function processLoginRecord($record)
    {
        // Implementation for processing login records
        // This would depend on your specific requirements
        Log::info('Processing login record', $record);
    }

    protected function processBookingRecord($record)
    {
        // Implementation for processing booking records
        Log::info('Processing booking record', $record);
    }

    protected function processSafetyIncidentRecord($record)
    {
        // Implementation for processing safety incident records
        Log::info('Processing safety incident record', $record);
    }

    protected function processFeedbackRecord($record)
    {
        // Implementation for processing feedback records
        Log::info('Processing feedback record', $record);
    }

    protected function generateReportHTML($reportData)
    {
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>' . htmlspecialchars($reportData['title'] ?? 'Report') . '</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
                .header { background-color: #10b981; color: white; padding: 20px; margin-bottom: 20px; border-radius: 5px; }
                .header h1 { margin: 0 0 10px 0; }
                .header p { margin: 0; opacity: 0.9; }
                .summary { background-color: #f0fdf4; padding: 20px; margin-bottom: 20px; border-left: 4px solid #10b981; border-radius: 5px; }
                .error { background-color: #fef2f2; padding: 20px; margin-bottom: 20px; border-left: 4px solid #ef4444; border-radius: 5px; color: #dc2626; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
                th, td { border: 1px solid #e5e7eb; padding: 12px 8px; text-align: left; }
                th { background-color: #f9fafb; font-weight: bold; color: #374151; }
                tr:nth-child(even) { background-color: #f9fafb; }
                tr:hover { background-color: #f3f4f6; }
                .no-data { text-align: center; padding: 40px; color: #6b7280; font-style: italic; }
                .metadata { font-size: 0.9em; color: #6b7280; margin-bottom: 20px; }
                .rating-bar { background: #e5e7eb; border-radius: 4px; overflow: hidden; height: 18px; width: 120px; display: inline-block; vertical-align: middle; margin-right: 8px; }
                .rating-bar-fill { background: #10b981; height: 100%; display: block; }
                .rating-row { white-space: nowrap; }
            </style>
        </head>
        <body>';

        $html .= '<div class="header">';
        $html .= '<h1>' . htmlspecialchars($reportData['title'] ?? 'Report') . '</h1>';
        if (isset($reportData['period'])) {
            $html .= '<p>Period: ' . htmlspecialchars($reportData['period']) . '</p>';
        }
        $html .= '</div>';

        $html .= '<div class="metadata">Generated: ' . \Carbon\Carbon::now()->format('Y-m-d H:i:s') . '</div>';

        // Handle error case
        if (isset($reportData['error']) && $reportData['error']) {
            $html .= '<div class="error">';
            $html .= '<h2>Error</h2>';
            $html .= '<p>' . htmlspecialchars($reportData['message'] ?? 'Unknown error occurred') . '</p>';
            $html .= '</div>';
        }

        // Summary section
        if (!empty($reportData['summary']) && is_array($reportData['summary'])) {
            $html .= '<div class="summary"><h2>Summary Statistics</h2><table>';
            foreach ($reportData['summary'] as $key => $value) {
                // Special display for rating_distribution
                if ($key === 'rating_distribution' && is_array($value)) {
                    $total = array_sum($value);
                    $html .= '<tr><td colspan="2"><strong>Rating Distribution</strong></td></tr>';
                    foreach ($value as $star => $count) {
                        $percent = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                        $html .= '<tr class="rating-row"><td>' . htmlspecialchars(str_replace('_', ' ', ucfirst($star))) . '</td>';
                        $html .= '<td><div class="rating-bar"><div class="rating-bar-fill" style="width:' . $percent . '%"></div></div> ';
                        $html .= $count . ' (' . $percent . '%)</td></tr>';
                    }
                } else {
                    $displayKey = ucwords(str_replace('_', ' ', $key));
                    $displayValue = $this->formatValueForHTML($value);
                    $html .= '<tr><td><strong>' . htmlspecialchars($displayKey) . '</strong></td><td>' . $displayValue . '</td></tr>';
                }
            }
            $html .= '</table></div>';
        }

        // Detailed data section
        if (!empty($reportData['data']) && is_array($reportData['data']) && count($reportData['data']) > 0) {
            $html .= '<h2>Detailed Data</h2><table>';
            
            $firstRow = current($reportData['data']);
            if ($firstRow && is_array($firstRow)) {
                $html .= '<thead><tr>';
                foreach (array_keys($firstRow) as $header) {
                    $displayHeader = ucwords(str_replace('_', ' ', $header));
                    $html .= '<th>' . htmlspecialchars($displayHeader) . '</th>';
                }
                $html .= '</tr></thead><tbody>';

                foreach ($reportData['data'] as $row) {
                    $html .= '<tr>';
                    foreach ($row as $value) {
                        $displayValue = $this->formatValueForHTML($value);
                        $html .= '<td>' . $displayValue . '</td>';
                    }
                    $html .= '</tr>';
                }
                $html .= '</tbody>';
            }
            $html .= '</table>';
        } else {
            $html .= '<div class="no-data">No data available for the selected period</div>';
        }

        $html .= '</body></html>';
        return $html;
    }

    // Format value helpers
    protected function formatValueForExcel($value)
    {
        if ($value === null || $value === '') return '';
        if (is_bool($value)) return $value ? 'Yes' : 'No';
        if (is_array($value) || is_object($value)) return json_encode($value, JSON_UNESCAPED_UNICODE);
        return (string)$value;
    }

    protected function formatValueForCSV($value)
    {
        if ($value === null || $value === '') return '';
        if (is_bool($value)) return $value ? 'Yes' : 'No';
        if (is_array($value) || is_object($value)) return json_encode($value, JSON_UNESCAPED_UNICODE);
        return (string)$value;
    }

    protected function formatValueForHTML($value)
    {
        if ($value === null || $value === '') return '<em>N/A</em>';
        if (is_bool($value)) return $value ? '<span style="color: #10b981;">Yes</span>' : '<span style="color: #ef4444;">No</span>';
        if (is_array($value) || is_object($value)) return '<code>' . htmlspecialchars(json_encode($value, JSON_UNESCAPED_UNICODE)) . '</code>';
        return htmlspecialchars((string)$value);
    }

    protected function safeValue($value)
    {
        if ($value instanceof \Illuminate\Support\Collection) return $value->toArray();
        if (is_object($value)) return (array) $value;
        return $value ?? '';
    }

    protected function getReportTitle($reportType)
    {
        $titles = [
            'login_trends' => 'Login Trends Report',
            'user_engagement' => 'User Engagement Report',
            'trail_popularity' => 'Trail Popularity Report',
            'booking_volumes' => 'Booking Volumes Report',
            'emergency_readiness' => 'Emergency Readiness Report',
            'feedback_summary' => 'Feedback Summary Report',
            'safety_incidents' => 'Safety Incidents Report',
            'community_posts' => 'Community Posts Report',
            'account_moderation' => 'Account Moderation Report',
            'content_trends' => 'Content Trends Report',
        ];

        return $titles[$reportType] ?? 'Unknown Report';
    }

    // Helper methods
    protected function getEmptyReport($title, $dateFrom, $dateTo, $errorMessage = null)
    {
        return [
            'title' => $title,
            'period' => $dateFrom->format('Y-m-d') . ' to ' . $dateTo->format('Y-m-d'),
            'error' => true,
            'message' => $errorMessage ?? 'No data available',
            'summary' => [
                'error_details' => $errorMessage ?? 'No data available for the selected period',
                'total_records' => 0,
                'data_source_status' => 'unavailable'
            ],
            'data' => []
        ];
    }

    protected function calculateEngagementLevel($loginCount)
    {
        if ($loginCount >= 20) return 'High';
        if ($loginCount >= 8) return 'Medium';
        if ($loginCount >= 1) return 'Low';
        return 'Inactive';
    }

    protected function calculateReadinessScore($item)
    {
        $scores = array_filter([
            $item->equipment_status ?? 0,
            $item->staff_availability ?? 0,
            $item->communication_status ?? 0
        ], function($score) { return $score > 0; });
        
        return count($scores) > 0 ? round(array_sum($scores) / count($scores), 2) : 0;
    }

    protected function getReadinessLevel($score)
    {
        if ($score >= 9) return 'Excellent';
        if ($score >= 7) return 'Good';
        if ($score >= 5) return 'Fair';
        return 'Needs Improvement';
    }

    protected function analyzeSentiment($comment)
    {
        if (empty($comment)) return 'neutral';
        
        $positiveWords = ['good', 'great', 'excellent', 'amazing', 'fantastic', 'wonderful', 'awesome', 
                         'love', 'perfect', 'beautiful', 'happy', 'satisfied', 'enjoyed', 'recommend',
                         'outstanding', 'brilliant', 'superb', 'magnificent', 'spectacular'];
        $negativeWords = ['bad', 'terrible', 'awful', 'horrible', 'worst', 'hate', 'disappointing', 
                         'poor', 'sad', 'angry', 'frustrated', 'dangerous', 'difficult', 'disgusting',
                         'nightmare', 'disaster', 'pathetic', 'useless', 'waste'];

        $text = strtolower($comment);
        $positiveCount = 0;
        $negativeCount = 0;
        
        foreach ($positiveWords as $word) {
            $positiveCount += substr_count($text, $word);
        }
        
        foreach ($negativeWords as $word) {
            $negativeCount += substr_count($text, $word);
        }
        
        if ($positiveCount > $negativeCount) return 'positive';
        if ($negativeCount > $positiveCount) return 'negative';
        return 'neutral';
    }

    protected function extractHashtags($posts)
    {
        $hashtags = collect();
        
        foreach ($posts as $post) {
            $content = $post->content ?? '';
            preg_match_all('/#([A-Za-z0-9_]+)/', $content, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $hashtag) {
                    $hashtags->push(strtolower($hashtag));
                }
            }
        }
        
        return $hashtags->countBy()->sortDesc();
    }

    protected function extractPostHashtags($content)
    {
        preg_match_all('/#([A-Za-z0-9_]+)/', $content, $matches);
        return !empty($matches[1]) ? implode(', ', $matches[1]) : '';
    }

    protected function categorizeContent($posts)
    {
        $categories = [
            'safety' => ['safety', 'accident', 'incident', 'rescue', 'emergency', 'danger', 'warning', 'alert'],
            'trail_conditions' => ['trail', 'path', 'condition', 'weather', 'mud', 'snow', 'ice', 'rocks', 'erosion'],
            'equipment' => ['gear', 'equipment', 'backpack', 'boots', 'tent', 'camping', 'hiking', 'supplies'],
            'wildlife' => ['animal', 'bear', 'snake', 'bird', 'wildlife', 'nature', 'deer', 'fox', 'wolf'],
            'scenic' => ['view', 'beautiful', 'scenic', 'photo', 'sunset', 'sunrise', 'landscape', 'vista', 'mountains'],
            'community' => ['meetup', 'group', 'event', 'gathering', 'club', 'friend', 'together', 'join'],
            'fitness' => ['workout', 'training', 'fitness', 'exercise', 'challenge', 'marathon', 'run', 'cardio'],
            'tips' => ['tip', 'advice', 'guide', 'help', 'tutorial', 'recommendation', 'suggestion', 'how to']
        ];

        $counts = collect();
        
        foreach ($posts as $post) {
            $content = strtolower($post->content ?? '' . ' ' . $post->title ?? '');
            $categorized = false;
            
            foreach ($categories as $category => $keywords) {
                foreach ($keywords as $keyword) {
                    if (strpos($content, $keyword) !== false && !$categorized) {
                        $counts[$category] = ($counts[$category] ?? 0) + 1;
                        $categorized = true;
                        break 2;
                    }
                }
            }
            
            if (!$categorized) {
                $counts['general'] = ($counts['general'] ?? 0) + 1;
            }
        }
        
        return $counts->sortDesc();
    }

    protected function categorizePost($content)
    {
        $categories = [
            'safety' => ['safety', 'accident', 'incident', 'rescue', 'emergency', 'danger', 'warning', 'alert'],
            'trail_conditions' => ['trail', 'path', 'condition', 'weather', 'mud', 'snow', 'ice', 'rocks', 'erosion'],
            'equipment' => ['gear', 'equipment', 'backpack', 'boots', 'tent', 'camping', 'hiking', 'supplies'],
            'wildlife' => ['animal', 'bear', 'snake', 'bird', 'wildlife', 'nature', 'deer', 'fox', 'wolf'],
            'scenic' => ['view', 'beautiful', 'scenic', 'photo', 'sunset', 'sunrise', 'landscape', 'vista', 'mountains'],
            'community' => ['meetup', 'group', 'event', 'gathering', 'club', 'friend', 'together', 'join'],
            'fitness' => ['workout', 'training', 'fitness', 'exercise', 'challenge', 'marathon', 'run', 'cardio'],
            'tips' => ['tip', 'advice', 'guide', 'help', 'tutorial', 'recommendation', 'suggestion', 'how to']
        ];

        $text = strtolower($content);
        
        foreach ($categories as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($text, $keyword) !== false) {
                    return $category;
                }
            }
        }
        
        return 'general';
    }

    protected function getDailyPostCounts($posts)
    {
        $dailyCounts = collect();
        
        foreach ($posts as $post) {
            if (isset($post->created_at)) {
                $date = Carbon::parse($post->created_at)->format('Y-m-d');
                $dailyCounts[$date] = ($dailyCounts[$date] ?? 0) + 1;
            }
        }
        
        return $dailyCounts->sortDesc();
    }

    /**
     * Clean up old report files
     */
    public function cleanupOldReports($daysOld = 30)
    {
        try {
            $reportPath = storage_path('app/reports');
            if (!file_exists($reportPath)) {
                return ['cleaned' => 0, 'errors' => []];
            }

            $files = glob($reportPath . '/report_*');
            $cleaned = 0;
            $errors = [];
            $cutoffTime = Carbon::now()->subDays($daysOld)->timestamp;

            foreach ($files as $file) {
                if (filemtime($file) < $cutoffTime) {
                    if (unlink($file)) {
                        $cleaned++;
                    } else {
                        $errors[] = 'Failed to delete: ' . basename($file);
                    }
                }
            }

            return [
                'cleaned' => $cleaned,
                'errors' => $errors,
                'cutoff_date' => Carbon::createFromTimestamp($cutoffTime)->format('Y-m-d H:i:s')
            ];
        } catch (Exception $e) {
            Log::error('Report cleanup error: ' . $e->getMessage());
            return ['cleaned' => 0, 'errors' => [$e->getMessage()]];
        }
    }

    /**
     * Get available report types with their descriptions
     */
    public function getAvailableReportTypes()
    {
        return [
            'login_trends' => [
                'name' => 'Login Trends Report',
                'description' => 'Analyze user login patterns and trends over time',
                'filters' => ['user_type']
            ],
            'user_engagement' => [
                'name' => 'User Engagement Report',
                'description' => 'Measure user activity and engagement levels',
                'filters' => ['user_type']
            ],
            'trail_popularity' => [
                'name' => 'Trail Popularity Report',
                'description' => 'Track trail booking patterns and ratings',
                'filters' => ['region_id', 'trail_id']
            ],
            'booking_volumes' => [
                'name' => 'Booking Volumes Report',
                'description' => 'Monitor booking trends and cancellation rates',
                'filters' => ['trail_id', 'status']
            ],
            'emergency_readiness' => [
                'name' => 'Emergency Readiness Report',
                'description' => 'Assess emergency preparedness across trails',
                'filters' => ['trail_id']
            ],
            'feedback_summary' => [
                'name' => 'Feedback Summary Report',
                'description' => 'Analyze customer feedback and ratings',
                'filters' => ['trail_id', 'rating']
            ],
            'safety_incidents' => [
                'name' => 'Safety Incidents Report',
                'description' => 'Track and analyze safety incidents',
                'filters' => ['trail_id', 'severity', 'status']
            ],
            'community_posts' => [
                'name' => 'Community Posts Report',
                'description' => 'Monitor community engagement and posts',
                'filters' => ['trail_id', 'status']
            ],
            'account_moderation' => [
                'name' => 'Account Moderation Report',
                'description' => 'Track user account moderation actions',
                'filters' => ['user_type', 'status']
            ],
            'content_trends' => [
                'name' => 'Content Trends Report',
                'description' => 'Analyze content trends and hashtags',
                'filters' => ['trail_id']
            ]
        ];
    }
}
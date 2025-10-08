<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Models\Trail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display the report generation form
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get trails based on user type
        if ($user->user_type === 'organization') {
            // Organizations only see their own trails
            $trails = Trail::where('user_id', $user->id)
                ->select('id', 'trail_name as name')
                ->orderBy('trail_name')
                ->get();
        } else {
            // Admin users see all trails
            $trails = Trail::select('id', 'trail_name as name')
                ->orderBy('trail_name')
                ->get();
        }
        
        $userTypes = [
            'organization' => 'Organization',
            'hiker' => 'Hiker'
        ];
        
        // Get dashboard statistics
        $organizationId = $user->user_type === 'organization' ? $user->id : null;
        $stats = $this->reportService->getDashboardStats($organizationId);

        return view('reports.index', compact('trails', 'userTypes', 'stats'));
    }

    /**
     * Generate report based on request
     */
    public function generate(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Check if user is organization and validate allowed report types
            if ($user->user_type === 'organization') {
                // Validate report type is allowed for organizations
                if (!$this->reportService->isReportAllowedForOrganization($request->report_type)) {
                    return response()->json([
                        'error' => true,
                        'message' => 'This report type is not available for organization accounts. Organizations can only access: Booking Volumes, Trail Popularity, Emergency Readiness, Safety Incidents, and Feedback Summary reports.'
                    ], 403);
                }
                
                // Verify trail ownership if trail_id is provided
                if ($request->trail_id) {
                    $trail = Trail::where('id', $request->trail_id)
                        ->where('user_id', $user->id)
                        ->first();
                    
                    if (!$trail) {
                        return response()->json([
                            'error' => true,
                            'message' => 'You do not have permission to generate reports for this trail.'
                        ], 403);
                    }
                }
            }
            
            // Validate request
            $validator = Validator::make($request->all(), [
                'report_type' => 'required|string|in:overall_transactions,booking_volumes,trail_popularity,emergency_readiness,safety_incidents,feedback_summary',
                'output_format' => 'required|string|in:screen,pdf,csv',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
                'trail_id' => 'nullable|exists:trails,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Set default date range if not provided (last 30 days)
            $dateFrom = $request->date_from ?: now()->subDays(30)->format('Y-m-d');
            $dateTo = $request->date_to ?: now()->format('Y-m-d');

            // Add organization_id to filters if user is organization
            $filters = $request->only(['trail_id', 'status', 'rating']);
            $organizationId = $user->user_type === 'organization' ? $user->id : null;

            // Generate report data
            $reportData = $this->reportService->generateReport(
                $request->report_type,
                $dateFrom,
                $dateTo,
                $filters,
                $organizationId
            );

            // Add trail name if specific trail was selected
            if ($request->trail_id) {
                $trail = Trail::find($request->trail_id);
                $reportData['trail_name'] = $trail ? $trail->trail_name : 'Unknown Trail';
            }

            // Add organization name if organization user
            if ($organizationId) {
                $reportData['organization_name'] = $user->organization_name ?? $user->name;
            }

            // Handle different output formats
            switch ($request->output_format) {
                case 'pdf':
                    return $this->generatePDF($reportData);
                
                case 'csv':
                    return $this->generateCSV($reportData);
                
                case 'screen':
                default:
                    return response()->json($reportData);
            }

        } catch (Exception $e) {
            Log::error('Report generation failed: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Failed to generate report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate PDF from report data
     */
    protected function generatePDF($reportData)
    {
        try {
            $pdf = Pdf::loadView('reports.pdf', $reportData);
            
            // Generate filename
            $filename = $this->generateFilename($reportData['title'], 'pdf');
            
            return $pdf->download($filename);
        } catch (Exception $e) {
            Log::error('PDF generation failed: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Failed to generate PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate CSV from report data
     */
    protected function generateCSV($reportData)
    {
        try {
            $filename = $this->generateFilename($reportData['title'], 'csv');
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ];

            $callback = function() use ($reportData) {
                $file = fopen('php://output', 'w');
                
                // Add UTF-8 BOM for Excel compatibility
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // Write report header
                fputcsv($file, [$reportData['title']]);
                fputcsv($file, ['Period: ' . $reportData['period']]);
                fputcsv($file, ['Generated: ' . now()->format('Y-m-d H:i:s')]);
                fputcsv($file, []); // Empty line
                
                // Write summary section
                if (isset($reportData['summary']) && !empty($reportData['summary'])) {
                    fputcsv($file, ['=== SUMMARY ===']);
                    foreach ($reportData['summary'] as $key => $value) {
                        if (!is_array($value) && !is_object($value)) {
                            $label = str_replace('_', ' ', ucwords($key, '_'));
                            fputcsv($file, [$label, $value]);
                        }
                    }
                    fputcsv($file, []); // Empty line
                }
                
                // Write detailed data
                if (isset($reportData['data']) && count($reportData['data']) > 0) {
                    fputcsv($file, ['=== DETAILED DATA ===']);
                    
                    // Write headers
                    $headers = array_keys($reportData['data'][0]);
                    $headerLabels = array_map(function($header) {
                        return str_replace('_', ' ', ucwords($header, '_'));
                    }, $headers);
                    fputcsv($file, $headerLabels);
                    
                    // Write data rows
                    foreach ($reportData['data'] as $row) {
                        fputcsv($file, array_values($row));
                    }
                } else {
                    fputcsv($file, ['No data available for the selected period']);
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (Exception $e) {
            Log::error('CSV generation failed: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Failed to generate CSV: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate filename for exports
     */
    protected function generateFilename($title, $extension)
    {
        $slug = preg_replace('/[^A-Za-z0-9-]+/', '_', strtolower($title));
        $date = now()->format('Y-m-d');
        return "{$slug}_{$date}.{$extension}";
    }

    /**
     * Get available report types for current user
     */
    public function availableReports()
    {
        $user = auth()->user();
        
        if ($user->user_type === 'organization') {
            return response()->json([
                'reports' => ReportService::ORGANIZATION_ALLOWED_REPORTS,
                'message' => 'Organization users have access to operational reports only.'
            ]);
        }
        
        // Admin users have access to all reports
        return response()->json([
            'reports' => array_merge(
                ReportService::ORGANIZATION_ALLOWED_REPORTS,
                ['login_trends', 'user_engagement', 'community_posts', 'account_moderation', 'content_trends']
            ),
            'message' => 'Admin users have access to all reports.'
        ]);
    }
}

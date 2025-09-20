<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use App\Services\EmailService;
use App\Models\Region;
use App\Models\Trail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class ReportController extends Controller
{
    protected $reportService;
    protected $emailService;

    public function __construct(ReportService $reportService, EmailService $emailService)
    {
        $this->reportService = $reportService;
        $this->emailService = $emailService;
    }

    /**
     * Display the report generation form
     */
    public function index()
    {
        $regions = Region::select('id', 'name')->orderBy('name')->get();
        $trails = Trail::select('id', 'name')->orderBy('name')->get();
        $userTypes = [
            'admin' => 'Admin',
            'guide' => 'Guide',
            'hiker' => 'Hiker',
            'moderator' => 'Moderator'
        ];

        return view('reports.index', compact('regions', 'trails', 'userTypes'));
    }

    /**
     * Generate report based on request
     */
    public function generate(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'report_type' => 'required|string|in:login_trends,user_engagement,trail_popularity,booking_volumes,emergency_readiness,feedback_summary,safety_incidents,community_posts,account_moderation,content_trends',
                'output_format' => 'required|string|in:screen,pdf,excel,csv',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
                'region_id' => 'nullable|exists:regions,id',
                'trail_id' => 'nullable|exists:trails,id',
                'user_type' => 'nullable|string|in:admin,guide,hiker,moderator',
                'email_report' => 'nullable|email'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Set default date range if not provided
            $dateFrom = $request->date_from ?: now()->subDays(30)->format('Y-m-d');
            $dateTo = $request->date_to ?: now()->format('Y-m-d');

            // Generate report data
            $reportData = $this->reportService->generateReport(
                $request->report_type,
                $dateFrom,
                $dateTo,
                $request->only(['region_id', 'trail_id', 'user_type'])
            );

            // Handle different output formats
            switch ($request->output_format) {
                case 'pdf':
                    $filePath = $this->reportService->generatePDF($reportData, $request->report_type);
                    return response()->json([
                        'download_url' => route('reports.download', ['file' => basename($filePath)]),
                        'message' => 'PDF report generated successfully'
                    ]);

                case 'excel':
                    $filePath = $this->reportService->generateExcel($reportData, $request->report_type);
                    return response()->json([
                        'download_url' => route('reports.download', ['file' => basename($filePath)]),
                        'message' => 'Excel report generated successfully'
                    ]);

                case 'csv':
                    $filePath = $this->reportService->generateCSV($reportData, $request->report_type);
                    return response()->json([
                        'download_url' => route('reports.download', ['file' => basename($filePath)]),
                        'message' => 'CSV report generated successfully'
                    ]);

                default: // screen
                    return response()->json($reportData);
            }

        } catch (Exception $e) {
            Log::error('Report generation failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to generate report',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle offline sync file upload
     */
    public function offlineSync(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'log_file' => 'required|file|mimes:json,csv,txt|max:10240' // 10MB max
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $file = $request->file('log_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('offline_logs', $fileName, 'local');

            // Process the uploaded file
            $result = $this->reportService->processOfflineLog($filePath);

            return response()->json([
                'message' => 'Offline log processed successfully',
                'processed_records' => $result['processed_records'] ?? 0,
                'errors' => $result['errors'] ?? []
            ]);

        } catch (Exception $e) {
            Log::error('Offline sync failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to process offline sync',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send report via email
     */
    public function email(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'report_data' => 'required',
                'report_type' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $result = $this->emailService->sendReportEmail(
                $request->email,
                $request->report_data,
                $request->report_type
            );

            if ($result) {
                return response()->json(['message' => 'Report emailed successfully']);
            } else {
                return response()->json(['error' => 'Failed to send email'], 500);
            }

        } catch (Exception $e) {
            Log::error('Email sending failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to send email',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download generated report file
     */
    public function download(Request $request, $file)
    {
        try {
            $filePath = storage_path('app/reports/' . $file);
            
            if (!file_exists($filePath)) {
                return response()->json(['error' => 'File not found'], 404);
            }

            return response()->download($filePath)->deleteFileAfterSend(true);

        } catch (Exception $e) {
            Log::error('File download failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to download file',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
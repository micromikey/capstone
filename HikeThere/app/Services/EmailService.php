<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Mail\ReportMail;
use Exception;
use Illuminate\Support\Facades\Log;

class EmailService
{
    /**
     * Send report via email
     */
    public function sendReportEmail($email, $reportData, $reportType)
    {
        try {
            Mail::to($email)->send(new ReportMail($reportData, $reportType));
            
            Log::info('Report email sent successfully', [
                'email' => $email,
                'report_type' => $reportType
            ]);
            
            return true;
            
        } catch (Exception $e) {
            Log::error('Failed to send report email', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Send bulk reports to multiple recipients
     */
    public function sendBulkReportEmails(array $emails, $reportData, $reportType)
    {
        $results = [];
        
        foreach ($emails as $email) {
            $results[$email] = $this->sendReportEmail($email, $reportData, $reportType);
        }
        
        return $results;
    }

    /**
     * Schedule report emails for future delivery
     */
    public function scheduleReportEmail($email, $reportData, $reportType, $sendAt)
    {
        try {
            Mail::to($email)
                ->later($sendAt, new ReportMail($reportData, $reportType));
            
            return true;
            
        } catch (Exception $e) {
            Log::error('Failed to schedule report email', [
                'email' => $email,
                'send_at' => $sendAt,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
}
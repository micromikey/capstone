<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $reportData;
    protected $reportType;

    /**
     * Create a new message instance.
     */
    public function __construct($reportData, $reportType)
    {
        $this->reportData = $reportData;
        $this->reportType = $reportType;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $reportTitle = $this->getReportTitle();
        
        return $this->view('emails.report')
                    ->subject('Hiking Platform Report: ' . $reportTitle)
                    ->with([
                        'reportData' => $this->reportData,
                        'reportType' => $this->reportType,
                        'reportTitle' => $reportTitle
                    ]);
    }

    /**
     * Get human-readable report title
     */
    protected function getReportTitle()
    {
        $titles = [
            'login_trends' => 'Login Trends Analysis',
            'user_engagement' => 'User Engagement Report',
            'trail_popularity' => 'Trail Popularity Analysis',
            'booking_volumes' => 'Booking Volumes Report',
            'emergency_readiness' => 'Emergency Readiness Assessment',
            'feedback_summary' => 'Feedback Summary Report',
            'safety_incidents' => 'Safety Incidents Report',
            'community_posts' => 'Community Posts Analysis',
            'account_moderation' => 'Account Moderation Report',
            'content_trends' => 'Content Trends Analysis'
        ];

        return $titles[$this->reportType] ?? 'Platform Report';
    }
}
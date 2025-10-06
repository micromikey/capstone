<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Organization Approval Required</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #336d66 0%, #20b6d2 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        .logo-section {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 20px;
        }
        .logo {
            width: 48px;
            height: 48px;
        }
        .brand-name {
            font-size: 32px;
            font-weight: bold;
            margin: 0;
        }
        .header h1 {
            font-size: 24px;
            margin: 10px 0 0 0;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
            background: white;
        }
        .greeting {
            font-size: 18px;
            color: #336d66;
            margin-bottom: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .message {
            margin: 20px 0;
            font-size: 15px;
            line-height: 1.8;
        }
        .organization-details {
            background: linear-gradient(135deg, #f0fdf4 0%, #e0f2fe 100%);
            border: 2px solid #20b6d2;
            border-radius: 10px;
            padding: 25px;
            margin: 25px 0;
        }
        .organization-details h3 {
            color: #336d66;
            margin: 0 0 20px 0;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .organization-details p {
            margin: 12px 0;
            font-size: 14px;
            color: #4b5563;
            line-height: 1.6;
        }
        .organization-details strong {
            color: #336d66;
            font-weight: 600;
            display: inline-block;
            min-width: 140px;
        }
        .action-buttons {
            text-align: center;
            margin: 35px 0;
            padding: 25px;
            background: #f9fafb;
            border-radius: 10px;
        }
        .action-buttons h3 {
            color: #336d66;
            margin: 0 0 20px 0;
            font-size: 16px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 16px 40px;
            margin: 10px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }
        .btn-approve {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            color: white !important;
        }
        .btn-reject {
            background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
            color: white !important;
        }
        .document-links {
            margin: 20px 0;
            padding: 20px;
            background: #f9fafb;
            border-radius: 8px;
        }
        .document-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            margin: 8px 0;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            text-decoration: none;
            color: #336d66;
            font-weight: 500;
            transition: all 0.2s;
        }
        .document-link:hover {
            background: #f0fdf4;
            border-color: #20b6d2;
            transform: translateX(5px);
        }
        .document-icon {
            width: 24px;
            height: 24px;
        }
        .security-note {
            background: #fff7ed;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .security-note strong {
            color: #f59e0b;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }
        .security-note p {
            margin: 5px 0;
            font-size: 13px;
            color: #6b7280;
        }
        .footer {
            text-align: center;
            padding: 30px 20px;
            background: #f9fafb;
            color: #6b7280;
            font-size: 13px;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 8px 0;
        }
        .footer a {
            color: #20b6d2;
            text-decoration: none;
        }
        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #e5e7eb, transparent);
            margin: 25px 0;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            background: #fef3c7;
            color: #92400e;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo-section">
                <img src="{{ asset('img/icon1.png') }}" alt="HikeThere Logo" class="logo">
                <h2 class="brand-name">HikeThere</h2>
            </div>
            <h1>Organization Approval Required</h1>
        </div>

        <div class="content">
            <p class="greeting">
                👋 Hello Admin!
            </p>
            
            <p class="message">A new organization has registered and requires your approval. Please review the details below:</p>

            <div class="organization-details">
                <h3>
                    🏢 Organization Information
                    <span class="badge">PENDING REVIEW</span>
                </h3>
                <p><strong>Organization Name:</strong> {{ $user->organization_name ?? 'Not provided' }}</p>
                <p><strong>Contact Person:</strong> {{ $user->name ?? 'Not provided' }}</p>
                <p><strong>Email:</strong> {{ $user->email ?? 'Not provided' }}</p>
                <p><strong>Phone:</strong> {{ $organizationProfile->phone ?? 'Not provided' }}</p>
                <p><strong>Description:</strong> {{ $user->organization_description ?? 'Not provided' }}</p>
                <p><strong>Registration Date:</strong> {{ $user->created_at ? $user->created_at->format('F j, Y \a\t g:i A') : 'Not available' }}</p>
            </div>

            @if($organizationProfile)
            <div class="organization-details">
                <h3>
                    📍 Additional Details
                </h3>
                <p><strong>Address:</strong> {{ $organizationProfile->address ?? 'Not provided' }}</p>
                <p><strong>Website:</strong> {{ $organizationProfile->website ?? 'Not provided' }}</p>
            </div>

            @if($organizationProfile->business_permit_path || $organizationProfile->government_id_path || $organizationProfile->additional_docs)
            <div class="organization-details">
                <h3>
                    📄 Submitted Documents
                </h3>
                <div class="document-links">
                    @if($organizationProfile->business_permit_path)
                        <a href="{{ url('storage/' . $organizationProfile->business_permit_path) }}" class="document-link" target="_blank">
                            <svg class="document-icon" viewBox="0 0 24 24" fill="#336d66">
                                <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                            </svg>
                            <span>Business Permit</span>
                        </a>
                    @endif
                    
                    @if($organizationProfile->government_id_path)
                        <a href="{{ url('storage/' . $organizationProfile->government_id_path) }}" class="document-link" target="_blank">
                            <svg class="document-icon" viewBox="0 0 24 24" fill="#336d66">
                                <path d="M22,3H2C0.91,3.04 0.04,3.91 0,5V19C0.04,20.09 0.91,20.96 2,21H22C23.09,20.96 23.96,20.09 24,19V5C23.96,3.91 23.09,3.04 22,3M22,19H2V5H22V19M14,17V15.75C14,14.09 10.66,13.25 9,13.25C7.34,13.25 4,14.09 4,15.75V17H14M9,7A2.5,2.5 0 0,0 6.5,9.5A2.5,2.5 0 0,0 9,12A2.5,2.5 0 0,0 11.5,9.5A2.5,2.5 0 0,0 9,7M14,7V8H20V7H14M14,9V10H20V9H14M14,11V12H18V11H14"/>
                            </svg>
                            <span>Government ID</span>
                        </a>
                    @endif
                    
                    @if($organizationProfile->additional_docs && is_array($organizationProfile->additional_docs))
                        @foreach($organizationProfile->additional_docs as $index => $docPath)
                            <a href="{{ url('storage/' . $docPath) }}" class="document-link" target="_blank">
                                <svg class="document-icon" viewBox="0 0 24 24" fill="#336d66">
                                    <path d="M14,2L20,8V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V4A2,2 0 0,1 6,2H14M18,20V9H13V4H6V20H18M12,19L8,15H10.5V12H13.5V15H16L12,19Z"/>
                                </svg>
                                <span>Additional Document {{ $index + 1 }}</span>
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>
            @endif
            @endif

            <div class="action-buttons">
                <h3>⚡ Quick Actions</h3>
                <a href="{{ URL::signedRoute('organizations.approve.email', $user->id) }}" class="btn btn-approve">
                    <svg style="width: 20px; height: 20px;" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                    </svg>
                    Approve Organization
                </a>
                <a href="{{ URL::signedRoute('organizations.reject.email', $user->id) }}" class="btn btn-reject">
                    <svg style="width: 20px; height: 20px;" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z"/>
                    </svg>
                    Reject Organization
                </a>
            </div>

            <div class="security-note">
                <strong>
                    <svg style="width: 20px; height: 20px;" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M12,5A3,3 0 0,1 15,8A3,3 0 0,1 12,11A3,3 0 0,1 9,8A3,3 0 0,1 12,5M17.13,17C15.92,18.85 14.11,20.24 12,20.92C9.89,20.24 8.08,18.85 6.87,17C6.53,16.5 6.24,16 6,15.47C6,13.82 8.71,12.47 12,12.47C15.29,12.47 18,13.79 18,15.47C17.76,16 17.47,16.5 17.13,17Z"/>
                    </svg>
                    Important Notes
                </strong>
                <p>• Clicking the buttons above will immediately approve or reject this organization</p>
                <p>• Please review all documents carefully before making your decision</p>
                <p>• These approval links are signed and secure</p>
                <p>• Links will expire after 24 hours for security purposes</p>
            </div>

            <div class="divider"></div>

            <p class="message" style="text-align: center; color: #6b7280; font-size: 13px;">
                This is an automated notification from the HikeThere admin system
            </p>
        </div>

        <div class="footer">
            <p>If you have any questions, please contact the system administrator.</p>
            <p style="margin-top: 15px;">
                <a href="{{ url('/admin') }}">Admin Dashboard</a> •
                <a href="{{ url('/admin/organizations') }}">View All Organizations</a>
            </p>
        </div>
    </div>
</body>
</html>


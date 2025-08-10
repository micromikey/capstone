<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Organization Approval Required</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #4F46E5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
        }
        .organization-details {
            background: white;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #4F46E5;
        }
        .action-buttons {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            margin: 0 10px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
        }
        .btn-approve {
            background: #10B981;
            color: white;
        }
        .btn-reject {
            background: #EF4444;
            color: white;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            background: #f5f5f5;
            border-radius: 0 0 8px 8px;
        }
        .document-links {
            margin: 15px 0;
        }
        .document-link {
            display: block;
            padding: 8px;
            margin: 5px 0;
            background: #e5e7eb;
            border-radius: 3px;
            text-decoration: none;
            color: #374151;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏔️ HikeThere</h1>
        <h2>Organization Registration Approval Required</h2>
    </div>

    <div class="content">
        <p>Hello Admin,</p>
        
        <p>A new organization has registered and requires your approval. Please review the details below:</p>

        <div class="organization-details">
            <h3>Organization Information</h3>
            <p><strong>Organization Name:</strong> {{ $user->organization_name ?? 'Not provided' }}</p>
            <p><strong>Contact Person:</strong> {{ $user->name ?? 'Not provided' }}</p>
            <p><strong>Email:</strong> {{ $user->email ?? 'Not provided' }}</p>
            <p><strong>Phone:</strong> {{ $organizationProfile->phone ?? 'Not provided' }}</p>
            <p><strong>Description:</strong> {{ $user->organization_description ?? 'Not provided' }}</p>
            <p><strong>Registration Date:</strong> {{ $user->created_at ? $user->created_at->format('F j, Y \a\t g:i A') : 'Not available' }}</p>
        </div>

        @if($organizationProfile)
        <div class="organization-details">
            <h3>Additional Details</h3>
            <p><strong>Address:</strong> {{ $organizationProfile->address ?? 'Not provided' }}</p>
            <p><strong>Website:</strong> {{ $organizationProfile->website ?? 'Not provided' }}</p>
        </div>

        @if($organizationProfile->business_permit_path || $organizationProfile->government_id_path || $organizationProfile->additional_docs)
        <div class="organization-details">
            <h3>Submitted Documents</h3>
            <div class="document-links">
                @if($organizationProfile->business_permit_path)
                    <a href="{{ url('storage/' . $organizationProfile->business_permit_path) }}" class="document-link" target="_blank">
                        📄 View Business Permit
                    </a>
                @endif
                
                @if($organizationProfile->government_id_path)
                    <a href="{{ url('storage/' . $organizationProfile->government_id_path) }}" class="document-link" target="_blank">
                        🆔 View Government ID
                    </a>
                @endif
                
                @if($organizationProfile->additional_docs && is_array($organizationProfile->additional_docs))
                    @foreach($organizationProfile->additional_docs as $index => $docPath)
                        <a href="{{ url('storage/' . $docPath) }}" class="document-link" target="_blank">
                            📋 View Additional Document {{ $index + 1 }}
                        </a>
                    @endforeach
                @endif
            </div>
        </div>
        @endif
        @endif

        <div class="action-buttons">
            <a href="{{ URL::signedRoute('organizations.approve.email', $user->id) }}" class="btn btn-approve">
                ✅ Approve Organization
            </a>
            <a href="{{ URL::signedRoute('organizations.reject.email', $user->id) }}" class="btn btn-reject">
                ❌ Reject Organization
            </a>
        </div>

        <p><strong>Note:</strong> Clicking the buttons above will immediately approve or reject this organization. Please review all documents carefully before making your decision.</p>
        <p><strong>Security:</strong> These approval links are signed and secure. They will expire after 24 hours for security purposes.</p>
    </div>

    <div class="footer">
        <p>This is an automated notification from HikeThere.</p>
        <p>If you have any questions, please contact the system administrator.</p>
    </div>
</body>
</html>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Organization Registration Update</title>
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
            background: #EF4444;
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
        .notice-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            background: #f5f5f5;
            border-radius: 0 0 8px 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏔️ HikeThere</h1>
        <h2>Organization Registration Update</h2>
    </div>

    <div class="content">
        <div class="notice-box">
            <h3>📋 Registration Status Update</h3>
            <p><strong>{{ $user->organization_name }}</strong> - Registration Review Complete</p>
        </div>

        <p>Dear <strong>{{ $user->name }}</strong>,</p>
        
        <p>Thank you for your interest in joining the HikeThere community. After careful review of your organization's registration and submitted documents, we regret to inform you that we are unable to approve your registration at this time.</p>

        <h3>What This Means</h3>
        <p>Your organization registration has been reviewed and was not approved. This could be due to various factors including:</p>
        <ul>
            <li>Incomplete or unclear documentation</li>
            <li>Documents that don't meet our verification requirements</li>
            <li>Information that requires additional clarification</li>
            <li>Other compliance-related considerations</li>
        </ul>

        <h3>Next Steps</h3>
        <p>If you believe this decision was made in error or if you would like to address any concerns:</p>
        <ul>
            <li>Review the documents you submitted</li>
            <li>Ensure all required information is complete and clear</li>
            <li>Contact our support team for clarification</li>
            <li>Consider submitting a new application with updated documentation</li>
        </ul>

        <p><strong>Contact Information:</strong></p>
        <ul>
            <li><strong>Support Email:</strong> support@hikethere.com</li>
            <li><strong>Phone:</strong> +1 (555) 123-4567</li>
        </ul>

        <p>We appreciate your understanding and hope to work with you in the future.</p>

        <p>Best regards,<br>
        The HikeThere Team</p>
    </div>

    <div class="footer">
        <p>If you have any questions about this decision, please don't hesitate to contact us.</p>
        <p>Thank you for your interest in HikeThere.</p>
    </div>
</body>
</html>


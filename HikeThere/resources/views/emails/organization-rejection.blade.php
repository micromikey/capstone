<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Organization Registration Update</title>
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
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
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
            color: #dc2626;
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
        .notice-box {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 2px solid #ef4444;
            border-radius: 10px;
            padding: 25px;
            margin: 30px 0;
            text-align: center;
        }
        .notice-box h3 {
            color: #dc2626;
            margin: 0 0 15px 0;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .info-section {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .info-section h3 {
            color: #336d66;
            margin: 0 0 15px 0;
            font-size: 16px;
            font-weight: 600;
        }
        .info-section ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .info-section li {
            margin: 8px 0;
            color: #4b5563;
        }
        .contact-box {
            background: linear-gradient(135deg, #f0fdf4 0%, #e0f2fe 100%);
            border: 2px solid #20b6d2;
            border-radius: 10px;
            padding: 20px;
            margin: 25px 0;
        }
        .contact-box h3 {
            color: #336d66;
            margin: 0 0 15px 0;
            font-size: 16px;
        }
        .contact-box ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .contact-box li {
            margin: 10px 0;
            color: #4b5563;
            display: flex;
            align-items: center;
            gap: 10px;
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
            margin-bottom: 5px;
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
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo-section">
                <img src="{{ asset('img/icon1.png') }}" alt="HikeThere Logo" class="logo">
                <h2 class="brand-name">HikeThere</h2>
            </div>
            <h1>Organization Registration Update</h1>
        </div>

        <div class="content">
            <p class="greeting">
                <svg style="width: 24px; height: 24px; vertical-align: middle;" viewBox="0 0 24 24" fill="#dc2626">
                    <path d="M19,3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3M19,5V19H5V5H19M17,8.4L13.4,12L17,15.6L15.6,17L12,13.4L8.4,17L7,15.6L10.6,12L7,8.4L8.4,7L12,10.6L15.6,7L17,8.4Z"/>
                </svg>
                Registration Status Update
            </p>

            <div class="notice-box">
                <h3>
                    <svg style="width: 24px; height: 24px;" viewBox="0 0 24 24" fill="#dc2626">
                        <path d="M12,2C17.53,2 22,6.47 22,12C22,17.53 17.53,22 12,22C6.47,22 2,17.53 2,12C2,6.47 6.47,2 12,2M15.59,7L12,10.59L8.41,7L7,8.41L10.59,12L7,15.59L8.41,17L12,13.41L15.59,17L17,15.59L13.41,12L17,8.41L15.59,7Z"/>
                    </svg>
                    Registration Not Approved
                </h3>
                <p style="margin: 0; font-size: 16px; color: #4b5563;">
                    <strong>{{ $user->organization_name }}</strong>
                </p>
            </div>

            <p class="message">Dear <strong>{{ $user->name }}</strong>,</p>
            
            <p class="message">Thank you for your interest in joining the HikeThere community. After careful review of your organization's registration and submitted documents, we regret to inform you that we are unable to approve your registration at this time.</p>

            <div class="info-section">
                <h3>What This Means</h3>
                <p style="margin: 10px 0 5px 0; color: #4b5563;">Your organization registration has been reviewed and was not approved. This could be due to various factors including:</p>
                <ul>
                    <li>Incomplete or unclear documentation</li>
                    <li>Documents that don't meet our verification requirements</li>
                    <li>Information that requires additional clarification</li>
                    <li>Other compliance-related considerations</li>
                </ul>
            </div>

            <div class="info-section">
                <h3>Next Steps</h3>
                <p style="margin: 10px 0 5px 0; color: #4b5563;">If you believe this decision was made in error or if you would like to address any concerns:</p>
                <ul>
                    <li>Review the documents you submitted</li>
                    <li>Ensure all required information is complete and clear</li>
                    <li>Contact our support team for clarification</li>
                    <li>Consider submitting a new application with updated documentation</li>
                </ul>
            </div>

            <div class="contact-box">
                <h3>
                    <svg style="width: 20px; height: 20px; vertical-align: middle;" viewBox="0 0 24 24" fill="#336d66">
                        <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                    </svg>
                    Contact Information
                </h3>
                <ul>
                    <li>
                        <svg style="width: 20px; height: 20px; vertical-align: middle;" viewBox="0 0 24 24" fill="#336d66">
                            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                        </svg>
                        <strong>Support Email:</strong> support@hikethere.com
                    </li>
                    <li>
                        <svg style="width: 20px; height: 20px; vertical-align: middle;" viewBox="0 0 24 24" fill="#336d66">
                            <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                        </svg>
                        <strong>Phone:</strong> +1 (555) 123-4567
                    </li>
                </ul>
            </div>

            <p class="message">We appreciate your understanding and hope to work with you in the future.</p>

            <div class="divider"></div>

            <p class="message" style="text-align: center; color: #6b7280; font-style: italic;">
                Best regards,<br>
                <strong style="color: #336d66;">The HikeThere Team</strong>
            </p>
        </div>

        <div class="footer">
            <p>If you have any questions about this decision, please don't hesitate to contact us.</p>
            <p>Thank you for your interest in HikeThere.</p>
            <p style="margin-top: 15px;">
                <a href="{{ url('/') }}">Visit HikeThere</a> •
                <a href="{{ url('/contact') }}">Contact Support</a>
            </p>
        </div>
    </div>
</body>
</html>


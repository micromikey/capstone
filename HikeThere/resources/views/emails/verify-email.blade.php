<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Your Email Address</title>
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
        .verification-box {
            background: linear-gradient(135deg, #f0fdf4 0%, #e0f2fe 100%);
            border: 2px solid #20b6d2;
            border-radius: 10px;
            padding: 25px;
            margin: 30px 0;
            text-align: center;
        }
        .verification-box h3 {
            color: #336d66;
            margin: 0 0 15px 0;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .action-button {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 16px 50px;
            background: linear-gradient(135deg, #336d66 0%, #20b6d2 100%);
            color: white !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(51, 109, 102, 0.3);
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(51, 109, 102, 0.4);
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
        .alternative-link {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .alternative-link p {
            margin: 0 0 10px 0;
            font-size: 13px;
            color: #6b7280;
        }
        .alternative-link a {
            color: #20b6d2;
            word-break: break-all;
            font-size: 12px;
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
        .features {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin: 25px 0;
        }
        .feature-item {
            flex: 1;
            min-width: 150px;
            text-align: center;
            padding: 15px;
            background: #f9fafb;
            border-radius: 8px;
        }
        .feature-icon {
            width: 32px;
            height: 32px;
            margin: 0 auto 8px;
        }
        .feature-text {
            font-size: 13px;
            color: #6b7280;
            font-weight: 500;
        }
        .icon-inline {
            width: 20px;
            height: 20px;
            vertical-align: middle;
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
            <h1>Verify Your Email Address</h1>
        </div>

        <div class="content">
            <p class="greeting">
                <svg class="icon-inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#336d66">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
                Hello {{ $user->name }}!
            </p>

            <p class="message">
                Welcome to <strong>HikeThere</strong> – your ultimate hiking companion! We're thrilled to have you join our community of adventure seekers and nature enthusiasts.
            </p>

            <p class="message">
                To get started on your hiking journey, please verify your email address by clicking the button below:
            </p>

            <div class="verification-box">
                <h3>
                    <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#336d66">
                        <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM9 6c0-1.66 1.34-3 3-3s3 1.34 3 3v2H9V6zm9 14H6V10h12v10zm-6-3c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z"/>
                    </svg>
                    Email Verification Required
                </h3>
                <p style="margin: 0; color: #6b7280; font-size: 14px;">
                    Click the button below to confirm your email address and activate your account.
                </p>
            </div>

            <div class="action-button">
                <a href="{{ $verificationUrl }}" class="btn">
                    <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                    </svg>
                    Verify Email Address
                </a>
            </div>

            <div class="divider"></div>

            <p class="message">
                Once verified, you'll be able to:
            </p>

            <div class="features">
                <div class="feature-item">
                    <svg class="feature-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#336d66">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                    <div class="feature-text">Discover Trails</div>
                </div>
                <div class="feature-item">
                    <svg class="feature-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#20b6d2">
                        <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zM9 14H7v-2h2v2zm4 0h-2v-2h2v2zm4 0h-2v-2h2v2zm-8 4H7v-2h2v2zm4 0h-2v-2h2v2zm4 0h-2v-2h2v2z"/>
                    </svg>
                    <div class="feature-text">Join Events</div>
                </div>
                <div class="feature-item">
                    <svg class="feature-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#f59e0b">
                        <path d="M6.76 4.84l-1.8-1.79-1.41 1.41 1.79 1.79 1.42-1.41zM4 10.5H1v2h3v-2zm9-9.95h-2V3.5h2V.55zm7.45 3.91l-1.41-1.41-1.79 1.79 1.41 1.41 1.79-1.79zm-3.21 13.7l1.79 1.8 1.41-1.41-1.8-1.79-1.4 1.4zM20 10.5v2h3v-2h-3zm-8-5c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6zm-1 16.95h2V19.5h-2v2.95zm-7.45-3.91l1.41 1.41 1.79-1.8-1.41-1.41-1.79 1.8z"/>
                    </svg>
                    <div class="feature-text">Check Weather</div>
                </div>
                <div class="feature-item">
                    <svg class="feature-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#336d66">
                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                    </svg>
                    <div class="feature-text">Connect Hikers</div>
                </div>
            </div>

            <div class="security-note">
                <strong>
                    <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#f59e0b">
                        <path d="M12 2L4 5v6.09c0 5.05 3.41 9.76 8 10.91 4.59-1.15 8-5.86 8-10.91V5l-8-3zm-1 14.93h2V13h-2v3.93zm0-5.98h2V7h-2v3.95z"/>
                    </svg>
                    Security Notice
                </strong>
                If you didn't create an account with HikeThere, please disregard this email. Your email address will not be used without verification.
            </div>

            <div class="alternative-link">
                <p><strong>Having trouble clicking the button?</strong></p>
                <p>Copy and paste this URL into your browser:</p>
                <a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a>
            </div>

            <div class="divider"></div>

            <p class="message" style="margin-bottom: 0;">
                Happy hiking!
                <svg width="20" height="20" style="vertical-align: middle; margin-left: 4px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#336d66">
                    <path d="M13.5 5.5c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zM9.8 8.9L7 23h2.1l1.8-8 2.1 2v6h2v-7.5l-2.1-2 .6-3C14.8 12 16.8 13 19 13v-2c-1.9 0-3.5-1-4.3-2.4l-1-1.6c-.4-.6-1-1-1.7-1-.3 0-.5.1-.8.1L6 8.3V13h2V9.6l1.8-.7"/>
                </svg><br>
                <strong>The HikeThere Team</strong>
            </p>
        </div>

        <div class="footer">
            <p><strong>HikeThere</strong> - Your Ultimate Hiking Companion</p>
            <p>
                Questions? Contact us at 
                <a href="mailto:support@hikethere.com">support@hikethere.com</a>
            </p>
            <p style="margin-top: 15px; font-size: 12px;">
                This is an automated email. Please do not reply to this message.
            </p>
        </div>
    </div>
</body>
</html>

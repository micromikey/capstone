<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Organization Approved!</title>
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
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .content {
            background: white;
            padding: 40px 30px;
        }
        .success-message {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border: 2px solid #10B981;
            border-radius: 10px;
            padding: 25px;
            margin: 20px 0;
            text-align: center;
        }
        .success-message h3 {
            color: #065f46;
            margin: 0 0 10px 0;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .success-message p {
            color: #065f46;
            margin: 0;
            font-size: 16px;
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
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            color: white !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(16, 185, 129, 0.4);
        }
        .features-list {
            background: #f9fafb;
            border-radius: 8px;
            padding: 20px 30px;
            margin: 20px 0;
        }
        .features-list h3 {
            color: #336d66;
            margin: 0 0 15px 0;
            font-size: 18px;
        }
        .features-list ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .features-list li {
            padding: 8px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #4b5563;
        }
        .login-details {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .login-details strong {
            color: #1e40af;
        }
        .login-details ul {
            list-style: none;
            padding: 0;
            margin: 10px 0 0 0;
        }
        .login-details li {
            padding: 5px 0;
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
        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #e5e7eb, transparent);
            margin: 25px 0;
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
            <h1>
                <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
                Your Organization is Approved!
            </h1>
        </div>

        <div class="content">
            <div class="success-message">
                <h3>
                    <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#10B981">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    Welcome to HikeThere!
                </h3>
                <p><strong>{{ $user->organization_name }}</strong> has been approved!</p>
            </div>

            <p>Dear <strong>{{ $user->name }}</strong>,</p>
            
            <p>We're excited to inform you that your organization <strong>{{ $user->organization_name }}</strong> has been approved to join the HikeThere community!</p>

            <div class="features-list">
                <h3>What's Next?</h3>
                <p style="margin: 0 0 10px 0; color: #6b7280;">Your organization can now:</p>
                <ul>
                    <li>
                        <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#10B981">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                        </svg>
                        Log in to your account
                    </li>
                    <li>
                        <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#10B981">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                        </svg>
                        Access the organization dashboard
                    </li>
                    <li>
                        <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#10B981">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                        </svg>
                        Create and manage hiking events
                    </li>
                    <li>
                        <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#10B981">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                        </svg>
                        Connect with hiking enthusiasts
                    </li>
                    <li>
                        <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#10B981">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                        </svg>
                        Build your community presence
                    </li>
                    <li>
                        <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#10B981">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                        </svg>
                        Access all HikeThere features
                    </li>
                </ul>
            </div>

            <div class="action-button">
                <a href="{{ route('login') }}" class="btn">
                    <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                        <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                    </svg>
                    Access Your Dashboard
                </a>
            </div>

            <div class="login-details">
                <strong>
                    <svg class="icon-inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#3b82f6">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                    Login Details:
                </strong>
                <ul>
                    <li><strong>Email:</strong> {{ $user->email }}</li>
                    <li><strong>Password:</strong> The password you set during registration</li>
                </ul>
            </div>

            <p>We look forward to seeing the amazing hiking experiences you'll create for the community!</p>

            <div class="divider"></div>

            <p style="margin-bottom: 0;">
                Best regards,<br>
                <strong>The HikeThere Team</strong>
            </p>
        </div>

        <div class="footer">
            <p><strong>HikeThere</strong> - Your Ultimate Hiking Companion</p>
            <p>If you have any questions, please contact our support team.</p>
            <p style="margin-top: 15px;">
                Welcome to the HikeThere family!
                <svg class="icon-inline" style="margin-left: 4px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#336d66">
                    <path d="M2.05 13h6.49l-1.63-4.9A1.5 1.5 0 0 1 8.35 6.5h7.3c.66 0 1.25.42 1.44 1.1L18.72 13h2.78L13.5 2L2.05 13zm19.9 0l-8 8V15h-4v6l-8-8h20z"/>
                </svg>
            </p>
        </div>
    </div>
</body>
</html>
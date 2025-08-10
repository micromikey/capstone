<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Organization Approved!</title>
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
            background: #10B981;
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
        .success-message {
            background: #d1fae5;
            border: 1px solid #10B981;
            color: #065f46;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        .action-button {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            padding: 15px 40px;
            background: #10B981;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
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
        <h2>Congratulations! Your Organization is Approved! 🎉</h2>
    </div>

    <div class="content">
        <div class="success-message">
            <h3>🎊 Welcome to HikeThere!</h3>
            <p><strong>{{ $user->organization_name }}</strong> has been approved!</p>
        </div>

        <p>Dear <strong>{{ $user->name }}</strong>,</p>
        
        <p>We're excited to inform you that your organization <strong>{{ $user->organization_name }}</strong> has been approved to join the HikeThere community!</p>

        <h3>What's Next?</h3>
        <p>Your organization can now:</p>
        <ul>
            <li>✅ Log in to your account</li>
            <li>✅ Access the organization dashboard</li>
            <li>✅ Create and manage hiking events</li>
            <li>✅ Connect with hiking enthusiasts</li>
            <li>✅ Build your community presence</li>
            <li>✅ Access all HikeThere features</li>
        </ul>

        <div class="action-button">
            <a href="{{ route('login') }}" class="btn">
                🚀 Access Your Dashboard
            </a>
        </div>

        <p><strong>Login Details:</strong></p>
        <ul>
            <li><strong>Email:</strong> {{ $user->email }}</li>
            <li><strong>Password:</strong> The password you set during registration</li>
        </ul>

        <p>We look forward to seeing the amazing hiking experiences you'll create for the community!</p>

        <p>Best regards,<br>
        The HikeThere Team</p>
    </div>

    <div class="footer">
        <p>If you have any questions, please contact our support team.</p>
        <p>Welcome to the HikeThere family! 🏔️</p>
    </div>
</body>
</html>
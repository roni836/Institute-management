<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Antra Institute</title>
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
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e3e3e3;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        .welcome-text {
            font-size: 18px;
            color: #374151;
            margin-bottom: 20px;
        }
        .credentials-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .credential-item {
            margin-bottom: 15px;
        }
        .label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
        }
        .value {
            background-color: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            padding: 8px 12px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            color: #1f2937;
        }
        .warning {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            color: #92400e;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e3e3e3;
            color: #6b7280;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            background-color: #2563eb;
            color: #ffffff;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">Antra Institute</div>
            <div class="welcome-text">Welcome to Our Teaching Team!</div>
        </div>

        <p>Dear <strong>{{ $teacher->name }}</strong>,</p>

        <p>Welcome to Antra Institute! We're excited to have you join our teaching team. Your account has been successfully created and you can now access our management system.</p>

        <div class="credentials-box">
            <h3 style="margin-top: 0; color: #1f2937;">Your Login Credentials</h3>
            
            <div class="credential-item">
                <div class="label">Email Address:</div>
                <div class="value">{{ $teacher->email }}</div>
            </div>
            
            <div class="credential-item">
                <div class="label">Password:</div>
                <div class="value">{{ $password }}</div>
            </div>
        </div>

        <div class="warning">
            <strong>Important:</strong> Please change your password after your first login for security purposes. Keep these credentials safe and do not share them with anyone.
        </div>

        <p>You can now log in to your account using the credentials provided above. If you have any questions or need assistance, please don't hesitate to contact our support team.</p>

        <div style="text-align: center;">
            <a href="{{ url('/login') }}" class="button">Login to Your Account</a>
        </div>

        <p>Best regards,<br>
        <strong>The Antra Institute Team</strong></p>

        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} Antra Institute. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

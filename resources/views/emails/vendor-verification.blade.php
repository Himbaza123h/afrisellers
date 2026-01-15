<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 40px 30px;
        }
        .content h2 {
            color: #2563eb;
            margin-top: 0;
        }
        .verification-code {
            background: #f3f4f6;
            border-left: 4px solid #2563eb;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            color: #2563eb;
            letter-spacing: 5px;
            font-family: 'Courier New', monospace;
        }
        .button {
            display: inline-block;
            padding: 15px 40px;
            background: #2563eb;
            color: white !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 20px 0;
            transition: background 0.3s;
        }
        .button:hover {
            background: #1d4ed8;
        }
        .footer {
            background: #f9fafb;
            padding: 30px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            border-top: 1px solid #e5e7eb;
        }
        .warning {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
        }
        .link-text {
            color: #6b7280;
            font-size: 12px;
            word-break: break-all;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📧 Email Verification</h1>
            <p style="margin: 10px 0 0 0; font-size: 16px;">Welcome to Afrisellers</p>
        </div>

        <div class="content">
            <h2>Hello {{ $vendorName }}!</h2>

            <p>Thank you for registering as a vendor on <strong>Afrisellers</strong> - Africa's leading B2B marketplace.</p>

            <p>To complete your registration and submit your vendor application, please verify your email address by clicking the button below:</p>

            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="button">
                    ✓ Verify Email Address
                </a>
            </div>

            <div class="verification-code">
                <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 14px;">Or use this verification code:</p>
                <div class="code">{{ $verificationToken }}</div>
            </div>

            <p>This verification link will expire in <strong>24 hours</strong>.</p>

            <div class="warning">
                <strong>⚠️ Important:</strong> Your vendor application will only be submitted for review after you verify your email address.
            </div>

            <p>If you didn't create an account with Afrisellers, please ignore this email.</p>

            <p class="link-text">
                If the button doesn't work, copy and paste this link into your browser:<br>
                <a href="{{ $verificationUrl }}" style="color: #2563eb;">{{ $verificationUrl }}</a>
            </p>
        </div>

        <div class="footer">
            <p style="margin: 0 0 10px 0;"><strong>Afrisellers</strong></p>
            <p style="margin: 0 0 10px 0;">Africa's Premier B2B Marketplace</p>
            <p style="margin: 0;">© {{ date('Y') }} Afrisellers. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

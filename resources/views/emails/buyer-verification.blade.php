<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - AfriSellers</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .header {
            padding: 40px 40px 20px;
            text-align: center;
            border-bottom: 1px solid #e5e5e5;
        }
        .logo {
            height: 40px;
        }
        .content {
            padding: 40px;
        }
        h1 {
            color: #111827;
            font-size: 24px;
            margin: 0 0 16px;
            font-weight: 700;
        }
        p {
            margin: 0 0 16px;
            color: #4b5563;
            font-size: 16px;
        }
        .verification-code {
            background-color: #f9fafb;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 32px;
            text-align: center;
            margin: 32px 0;
        }
        .code {
            font-size: 48px;
            font-weight: 700;
            letter-spacing: 8px;
            color: #111827;
            font-family: 'Courier New', monospace;
        }
        .code-label {
            font-size: 14px;
            color: #6b7280;
            margin-top: 8px;
        }
        .button {
            display: inline-block;
            background-color: #ff0808;
            color: #ffffff;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 16px 0;
        }
        .info-box {
            background-color: #f3f4f6;
            border-left: 4px solid #6b7280;
            padding: 16px;
            margin: 24px 0;
            border-radius: 4px;
        }
        .info-box p {
            margin: 0;
            font-size: 14px;
            color: #4b5563;
        }
        .footer {
            padding: 32px 40px;
            background-color: #f9fafb;
            border-top: 1px solid #e5e5e5;
            text-align: center;
        }
        .footer p {
            margin: 8px 0;
            font-size: 14px;
            color: #6b7280;
        }
        .footer a {
            color: #111827;
            text-decoration: underline;
        }
        @media only screen and (max-width: 600px) {
            .content, .header, .footer {
                padding: 24px !important;
            }
            .code {
                font-size: 36px;
                letter-spacing: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="https://afrisellers.com/public/uploads/all/rcIW6v7SfbxlCbrTIBU6CXQNggsQbKVO1a8vXheE.png" alt="AfriSellers" class="logo">
        </div>

        <!-- Content -->
        <div class="content">
            <h1>Verify your email address</h1>

            <p>Hello {{ $userName }},</p>

            <p>Thank you for registering with AfriSellers! To complete your registration and activate your account, please verify your email address by entering the verification code below:</p>

            <!-- Verification Code Box -->
            <div class="verification-code">
                <div class="code">{{ $verificationToken }}</div>
                <div class="code-label">Your verification code</div>
            </div>

            <p>Enter this code on the verification page to activate your account and start shopping on Africa's premier B2B marketplace.</p>

            <!-- Info Box -->
            <div class="info-box">
                <p><strong>Security tip:</strong> This code will expire in 24 hours. If you didn't create an account with AfriSellers, please ignore this email or contact our support team.</p>
            </div>

            <p>If you have any questions or need assistance, please don't hesitate to reach out to our support team.</p>

            <p style="margin-top: 32px;">
                Best regards,<br>
                <strong>The AfriSellers Team</strong>
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} AfriSellers. All rights reserved.</p>
            <p>
                <a href="https://afrisellers.com">Visit our website</a> |
                <a href="mailto:support@afrisellers.com">Contact Support</a>
            </p>
            <p style="margin-top: 16px; font-size: 12px;">
                This email was sent to you as part of your AfriSellers account registration.
            </p>
        </div>
    </div>
</body>
</html>

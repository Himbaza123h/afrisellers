<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Profile Application Rejected - AfriSellers</title>
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
        .rejection-box {
            background-color: #fef2f2;
            border: 2px solid #fecaca;
            border-radius: 8px;
            padding: 24px;
            margin: 32px 0;
        }
        .rejection-box h2 {
            color: #dc2626;
            font-size: 18px;
            margin: 0 0 12px;
            font-weight: 600;
        }
        .rejection-box p {
            margin: 0;
            color: #7f1d1d;
            font-size: 14px;
        }
        .reason-box {
            background-color: #f9fafb;
            border-left: 4px solid #dc2626;
            padding: 16px;
            margin: 24px 0;
            border-radius: 4px;
        }
        .reason-box p {
            margin: 0;
            font-size: 14px;
            color: #4b5563;
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
            <h1>Business Profile Application Status</h1>

            <p>Hello {{ $userName }},</p>

            <p>Thank you for your interest in becoming a vendor on AfriSellers. We have reviewed your business profile application for <strong>{{ $businessName }}</strong>.</p>

            <!-- Rejection Notice -->
            <div class="rejection-box">
                <h2>Application Status: Rejected</h2>
                <p>Unfortunately, your business profile application has been rejected at this time.</p>
            </div>

            @if($rejectionReason)
                <!-- Rejection Reason -->
                <div class="reason-box">
                    <p><strong>Reason for Rejection:</strong></p>
                    <p style="margin-top: 8px;">{{ $rejectionReason }}</p>
                </div>
            @endif

            <p>We encourage you to review your application and the provided documents. If you believe this decision was made in error, or if you have additional information to provide, please don't hesitate to contact our support team.</p>

            <!-- Info Box -->
            <div class="info-box">
                <p><strong>Next Steps:</strong> You may submit a new application after addressing any issues identified in the rejection reason above. Our team is here to help you through the process.</p>
            </div>

            <p>If you have any questions or need assistance, please contact our support team.</p>

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
                This email was sent regarding your business profile application on AfriSellers.
            </p>
        </div>
    </div>
</body>
</html>


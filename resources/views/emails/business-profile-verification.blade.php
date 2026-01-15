<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Profile Verified - AfriSellers</title>
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
        .success-box {
            background-color: #f0fdf4;
            border: 2px solid #86efac;
            border-radius: 8px;
            padding: 24px;
            margin: 32px 0;
        }
        .success-box h2 {
            color: #16a34a;
            font-size: 18px;
            margin: 0 0 12px;
            font-weight: 600;
        }
        .success-box p {
            margin: 0;
            color: #166534;
            font-size: 14px;
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
        .features-list {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 24px;
            margin: 24px 0;
        }
        .features-list h3 {
            color: #111827;
            font-size: 16px;
            margin: 0 0 16px;
            font-weight: 600;
        }
        .features-list ul {
            margin: 0;
            padding-left: 20px;
            color: #4b5563;
            font-size: 14px;
        }
        .features-list li {
            margin: 8px 0;
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
            <h1>Congratulations! Your Business Profile Has Been Verified</h1>

            <p>Hello {{ $userName }},</p>

            <p>We're excited to inform you that your business profile application for <strong>{{ $businessName }}</strong> has been reviewed and <strong>approved</strong>!</p>

            <!-- Success Notice -->
            <div class="success-box">
                <h2>✓ Application Status: Verified</h2>
                <p>Your vendor account has been successfully created. You can now start selling on AfriSellers!</p>
            </div>

            <p>You now have access to your vendor dashboard where you can:</p>

            <!-- Features List -->
            <div class="features-list">
                <h3>What You Can Do Now:</h3>
                <ul>
                    <li>Add and manage your products</li>
                    <li>Set up your store profile</li>
                    <li>Receive and manage orders</li>
                    <li>Track your sales and analytics</li>
                    <li>Connect with buyers across Africa</li>
                </ul>
            </div>

            <div style="text-align: center; margin: 32px 0;">
                <a href="{{ route('vendor.dashboard.home') }}" class="button">Access Your Vendor Dashboard</a>
            </div>

            <!-- Info Box -->
            <div class="info-box">
                <p><strong>Getting Started:</strong> Log in to your account and visit your vendor dashboard to start adding products. Our support team is available to help you get started with your first listing.</p>
            </div>

            <p>If you have any questions or need assistance, please don't hesitate to reach out to our support team.</p>

            <p style="margin-top: 32px;">
                Welcome to AfriSellers!<br>
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
                This email was sent regarding your business profile verification on AfriSellers.
            </p>
        </div>
    </div>
</body>
</html>


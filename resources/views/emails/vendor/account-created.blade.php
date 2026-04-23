<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: #ff0808; padding: 24px 32px; text-align: center; }
        .header img { height: 36px; }
        .body { padding: 32px; color: #333; }
        .body h2 { margin-top: 0; color: #111; font-size: 20px; }
        .body p { line-height: 1.6; color: #555; }
        .credentials { background: #f9f9f9; border: 1px solid #eee; border-radius: 6px; padding: 16px 20px; margin: 20px 0; }
        .credentials p { margin: 6px 0; font-size: 14px; }
        .credentials strong { color: #111; }
        .btn { display: inline-block; margin: 20px 0; padding: 12px 28px; background: #ff0808; color: #fff !important; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 15px; }
        .footer { padding: 20px 32px; background: #f9f9f9; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <img src="{{ asset('mainlogo.png') }}" alt="AfriSellers">
        </div>
        <div class="body">
            <h2>Welcome, {{ $vendorName }}! 🎉</h2>
            <p>Your AfriSellers vendor account has been created. You can now log in and start managing your business profile.</p>

            <div class="credentials">
                <p><strong>Login Email:</strong> {{ $email }}</p>
                <p><strong>Temporary Password:</strong> {{ $password }}</p>
            </div>

            <p>For security, please change your password after your first login.</p>

            <p style="text-align:center;">
                <a href="{{ url('/auth/signin') }}" class="btn">Login to Dashboard</a>
            </p>

            <p>If you have any questions, contact your AfriSellers agent.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} AfriSellers. All rights reserved.
        </div>
    </div>
</body>
</html>

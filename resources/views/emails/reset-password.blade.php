<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: #ff0808; padding: 24px 32px; text-align: center; }
        .header img { height: 36px; }
        .body { padding: 32px; color: #333; }
        .body h2 { margin-top: 0; font-size: 20px; color: #111; }
        .body p { line-height: 1.6; color: #555; }
        .btn { display: inline-block; margin: 24px 0; padding: 12px 28px; background: #ff0808; color: #fff !important; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 15px; }
        .footer { padding: 20px 32px; background: #f9f9f9; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #eee; }
        .url-fallback { word-break: break-all; color: #888; font-size: 12px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <img src="{{ asset('mainlogo.png') }}" alt="AfriSellers">
        </div>
        <div class="body">
            <h2>Reset Your Password</h2>
            <p>Hello {{ $user->name ?? 'there' }},</p>
            <p>We received a request to reset the password for your AfriSellers account. Click the button below to proceed:</p>
            <p style="text-align:center;">
                <a href="{{ $url }}" class="btn">Reset Password</a>
            </p>
            <p>This link will expire in <strong>60 minutes</strong>. If you didn't request a password reset, you can safely ignore this email — your password will remain unchanged.</p>
            <p>If the button above doesn't work, copy and paste this URL into your browser:</p>
            <p class="url-fallback">{{ $url }}</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} AfriSellers. All rights reserved.
        </div>
    </div>
</body>
</html>

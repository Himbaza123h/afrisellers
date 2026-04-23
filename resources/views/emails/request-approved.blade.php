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
        .badge { display: inline-block; background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; border-radius: 6px; padding: 10px 20px; font-weight: bold; font-size: 15px; margin: 16px 0; }
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
            <h2>🎉 Congratulations, {{ $name }}!</h2>
            <p>We are happy to let you know that your request to become an AfriSellers Agent has been <strong>approved</strong>.</p>
            <div style="text-align:center;">
                <span class="badge">✅ Agent Request Approved</span>
            </div>
            <p>Your account has been upgraded to an Agent account. You can now log in to your dashboard and start managing vendors assigned to you.</p>
            <p style="text-align:center;">
                <a href="{{ url('/auth/signin') }}" class="btn">Go to Dashboard</a>
            </p>
            <p>If you have any questions, don't hesitate to contact our support team.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} AfriSellers. All rights reserved.
        </div>
    </div>
</body>
</html>

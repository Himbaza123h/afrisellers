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
        .reason-box { background: #fff7ed; border: 1px solid #fed7aa; border-radius: 6px; padding: 16px 20px; margin: 16px 0; }
        .reason-box p { margin: 0; color: #9a3412; font-size: 14px; }
        .footer { padding: 20px 32px; background: #f9f9f9; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <img src="{{ asset('mainlogo.png') }}" alt="AfriSellers">
        </div>
        <div class="body">
            <h2>Update on Your Agent Request</h2>
            <p>Hello {{ $name }}, thank you for your interest in becoming an AfriSellers Agent.</p>
            <p>After reviewing your application, we are unable to approve your request at this time for the following reason:</p>
            <div class="reason-box">
                <p>{{ $reason }}</p>
            </div>
            <p>You are welcome to apply again once you have addressed the above. If you believe this decision was made in error, please contact our support team.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} AfriSellers. All rights reserved.
        </div>
    </div>
</body>
</html>

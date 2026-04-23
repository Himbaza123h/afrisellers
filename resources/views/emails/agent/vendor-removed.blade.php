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
        .vendor-card { background: #fff7ed; border: 1px solid #fed7aa; border-radius: 6px; padding: 16px 20px; margin: 20px 0; }
        .vendor-card p { margin: 5px 0; font-size: 14px; color: #9a3412; }
        .footer { padding: 20px 32px; background: #f9f9f9; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <img src="{{ asset('mainlogo.png') }}" alt="AfriSellers">
        </div>
        <div class="body">
            <h2>Vendor Removed From Your Account</h2>
            <p>Hello {{ $agentName }}, the following vendor has been removed from your agent account.</p>

            <div class="vendor-card">
                <p><strong>Business:</strong> {{ $vendorBusinessName }}</p>
            </div>

            <p>If you believe this was done in error, please contact your AfriSellers admin team.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} AfriSellers. All rights reserved.
        </div>
    </div>
</body>
</html>

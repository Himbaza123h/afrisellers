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
        .info { background: #f9f9f9; border: 1px solid #eee; border-radius: 6px; padding: 16px 20px; margin: 20px 0; }
        .info p { margin: 6px 0; font-size: 14px; }
        .info strong { color: #111; }
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
            <h2>Hi {{ $agentName }}, a new vendor has been added! ✅</h2>
            <p>You have successfully added a new vendor to your account on AfriSellers.</p>

            <div class="info">
                <p><strong>Vendor Name:</strong> {{ $vendorName }}</p>
                <p><strong>Vendor Email:</strong> {{ $vendorEmail }}</p>
            </div>

            <p>Login credentials have been sent to the vendor. You can manage them from your dashboard.</p>

            <p style="text-align:center;">
                <a href="{{ url('/agent/vendors') }}" class="btn">View My Vendors</a>
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} AfriSellers. All rights reserved.
        </div>
    </div>
</body>
</html>

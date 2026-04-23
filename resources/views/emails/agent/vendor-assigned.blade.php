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
        .vendor-card { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 16px 20px; margin: 20px 0; }
        .vendor-card p { margin: 5px 0; font-size: 14px; color: #166534; }
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
            <h2>New Vendor Assigned 🏪</h2>
            <p>Hello {{ $agentName }}, a new vendor has been assigned to your agent account on AfriSellers.</p>

            <div class="vendor-card">
                <p><strong>Business:</strong> {{ $vendorBusinessName }}</p>
                <p><strong>Location:</strong> {{ $vendorCity }}, {{ $vendorCountry }}</p>
            </div>

            <p>Log in to your dashboard to view and manage this vendor's profile and products.</p>

            <p style="text-align:center;">
                <a href="{{ url('/agent/dashboard') }}" class="btn">Go to Dashboard</a>
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} AfriSellers. All rights reserved.
        </div>
    </div>
</body>
</html>

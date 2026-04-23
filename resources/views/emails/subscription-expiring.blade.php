<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .card { background: #fff; border-radius: 10px; max-width: 500px; margin: 0 auto; padding: 30px; }
        .header { background: linear-gradient(135deg, #1a2942, #ff0808); border-radius: 8px; padding: 20px; text-align: center; margin-bottom: 24px; }
        .header h1 { color: #fff; margin: 0; font-size: 20px; }
        .badge { display: inline-block; background: #fef08a; color: #854d0e; font-size: 11px; font-weight: bold; padding: 4px 12px; border-radius: 99px; margin-bottom: 10px; }
        .btn { display: inline-block; background: #ff0808; color: #fff; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: bold; margin-top: 20px; }
        p { color: #374151; font-size: 14px; line-height: 1.6; }
        .footer { text-align: center; font-size: 11px; color: #9ca3af; margin-top: 24px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <span class="badge">⚠️ Expiring Soon</span>
            <h1>Your plan is about to expire</h1>
        </div>

        <p>Hi <strong>{{ $userName }}</strong>,</p>

        <p>
            Your <strong>{{ $planName }}</strong> plan on Afrisellers will expire on
            <strong>{{ $expiryDate }}</strong> — that's only <strong>{{ $daysLeft }} day(s)</strong> away.
        </p>

        <p>Renew now to avoid any interruption to your vendor services.</p>

        <div style="text-align:center;">
            <a href="{{ url('/vendor/subscriptions') }}" class="btn">Renew My Plan →</a>
        </div>

        <div class="footer">
            <p>Afrisellers &mdot; This is an automated reminder. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>

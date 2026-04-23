<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: sans-serif; background:#f9fafb; padding: 30px;">
    <div style="max-width:560px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
        <div style="background:#ff0808;padding:24px 32px;">
            <h1 style="color:#fff;margin:0;font-size:20px;">AfriSellers</h1>
        </div>
        <div style="padding:32px;">
            <h2 style="color:#111;margin-top:0;">Service Delivered ✅</h2>
            <p style="color:#555;">Hello <strong>{{ $delivery->user->name }}</strong>,</p>
            <p style="color:#555;">We're pleased to let you know that the following service included in your <strong>{{ $delivery->plan->name }}</strong> plan has been completed:</p>

            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:16px 20px;margin:20px 0;">
                <p style="margin:0;font-size:16px;font-weight:bold;color:#15803d;">{{ $delivery->service_name }}</p>
                @if($delivery->notes)
                    <p style="margin:8px 0 0;font-size:14px;color:#374151;">{{ $delivery->notes }}</p>
                @endif
            </div>

            <p style="color:#555;">Log into your dashboard to view your updated services.</p>
            <a href="{{ url('/vendor/subscriptions') }}"
               style="display:inline-block;background:#ff0808;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:bold;margin-top:8px;">
                View My Subscription
            </a>
        </div>
        <div style="padding:16px 32px;border-top:1px solid #e5e7eb;text-align:center;">
            <p style="color:#9ca3af;font-size:12px;margin:0;">AfriSellers — Delivered with care</p>
        </div>
    </div>
</body>
</html>

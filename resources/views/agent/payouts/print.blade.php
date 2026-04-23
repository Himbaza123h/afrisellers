<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payouts Report — {{ now()->format('M d, Y') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111; padding: 24px; }
        h1 { font-size: 20px; font-weight: 700; color: #0891b2; }
        .meta { color: #6b7280; font-size: 11px; margin-top: 2px; }
        .summary { display: flex; gap: 16px; margin: 20px 0; }
        .summary-box { flex: 1; border: 1px solid #cffafe; background: #ecfeff; border-radius: 8px; padding: 12px 16px; }
        .summary-box .label { font-size: 10px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; }
        .summary-box .value { font-size: 18px; font-weight: 700; color: #0891b2; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        thead tr { background: #f9fafb; }
        th { text-align: left; padding: 8px 10px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; border-bottom: 2px solid #e5e7eb; }
        td { padding: 8px 10px; border-bottom: 1px solid #f3f4f6; font-size: 11px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 10px; font-weight: 600; }
        .badge-pending    { background: #fef3c7; color: #92400e; }
        .badge-approved   { background: #dbeafe; color: #1e40af; }
        .badge-processing { background: #e0e7ff; color: #3730a3; }
        .badge-paid       { background: #d1fae5; color: #065f46; }
        .badge-rejected   { background: #fee2e2; color: #991b1b; }
        .badge-cancelled  { background: #f3f4f6; color: #6b7280; }
        .footer { margin-top: 30px; padding-top: 12px; border-top: 1px solid #e5e7eb; color: #9ca3af; font-size: 10px; text-align: center; }
        @media print { body { padding: 0; } @page { margin: 15mm; } }
    </style>
</head>
<body>

    <h1>Payouts Report</h1>
    <p class="meta">Agent: {{ auth()->user()->name }} &nbsp;|&nbsp; Generated: {{ now()->format('M d, Y H:i') }}</p>

    <div class="summary">
        <div class="summary-box">
            <p class="label">Total Paid Out</p>
            <p class="value">${{ number_format($totals['paid'], 2) }}</p>
        </div>
        <div class="summary-box">
            <p class="label">Pending</p>
            <p class="value" style="color:#d97706">${{ number_format($totals['pending'], 2) }}</p>
        </div>
        <div class="summary-box">
            <p class="label">Total Requests</p>
            <p class="value" style="color:#374151">{{ $totals['count'] }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Payout #</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Status</th>
                <th>Requested</th>
                <th>Processed</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payouts as $i => $payout)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-family:monospace; font-weight:700">{{ $payout->payout_number }}</td>
                    <td style="font-weight:700">{{ $payout->currency }} {{ number_format($payout->amount, 2) }}</td>
                    <td>{{ ucwords(str_replace('_', ' ', $payout->payment_method)) }}</td>
                    <td><span class="badge badge-{{ $payout->status }}">{{ ucfirst($payout->status) }}</span></td>
                    <td>{{ $payout->created_at->format('M d, Y') }}</td>
                    <td>{{ $payout->processed_at ? $payout->processed_at->format('M d, Y') : '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:20px; color:#9ca3af;">No payouts found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generated automatically — {{ config('app.name') }} &nbsp;|&nbsp; {{ now()->format('Y') }}
    </div>

    <script>window.onload = () => window.print();</script>
</body>
</html>

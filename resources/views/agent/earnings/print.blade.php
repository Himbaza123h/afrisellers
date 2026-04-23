<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Earnings Report — {{ now()->format('M d, Y') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111; padding: 24px; }
        h1 { font-size: 20px; font-weight: 700; color: #059669; }
        .meta { color: #6b7280; font-size: 11px; margin-top: 2px; }
        .summary { display: flex; gap: 20px; margin: 20px 0; }
        .summary-box { flex: 1; border: 1px solid #d1fae5; background: #f0fdf4; border-radius: 8px; padding: 12px 16px; }
        .summary-box .label { font-size: 10px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; }
        .summary-box .value { font-size: 18px; font-weight: 700; color: #059669; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        thead tr { background: #f9fafb; }
        th { text-align: left; padding: 8px 10px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; border-bottom: 2px solid #e5e7eb; }
        td { padding: 8px 10px; border-bottom: 1px solid #f3f4f6; }
        tr:hover td { background: #fafafa; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 10px; font-weight: 600; }
        .badge-paid { background: #d1fae5; color: #065f46; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }
        .footer { margin-top: 30px; padding-top: 12px; border-top: 1px solid #e5e7eb; color: #9ca3af; font-size: 10px; text-align: center; }
        @media print {
            body { padding: 0; }
            @page { margin: 15mm; }
        }
    </style>
</head>
<body>
    <div>
        <h1>Earnings Report</h1>
        <p class="meta">Agent: {{ auth()->user()->name }} &nbsp;|&nbsp; Generated: {{ now()->format('M d, Y H:i') }}</p>
    </div>

    <div class="summary">
        <div class="summary-box">
            <p class="label">Total Paid</p>
            <p class="value">${{ number_format($total, 2) }}</p>
        </div>
        <div class="summary-box">
            <p class="label">Pending</p>
            <p class="value" style="color: #d97706">${{ number_format($pending, 2) }}</p>
        </div>
        <div class="summary-box">
            <p class="label">Total Records</p>
            <p class="value" style="color: #374151">{{ $earnings->count() }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Reference</th>
                <th>Vendor</th>
                <th>Order #</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($earnings as $i => $earning)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-family: monospace; font-size: 11px;">
                        {{ $earning->reference ?? 'COM-' . str_pad($earning->id, 5, '0', STR_PAD_LEFT) }}
                    </td>
                    <td>{{ $earning->vendor?->businessProfile?->business_name ?? 'N/A' }}</td>
                    <td style="font-family: monospace; font-size: 11px;">
                        {{ $earning->order?->order_number ?? '—' }}
                    </td>
                    <td style="font-weight: 700; color: #059669;">${{ number_format($earning->amount, 2) }}</td>
                    <td>
                        <span class="badge badge-{{ $earning->status }}">{{ ucfirst($earning->status) }}</span>
                    </td>
                    <td>{{ $earning->created_at->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px; color: #9ca3af;">No earnings found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        This report was generated automatically — {{ config('app.name') }} &nbsp;|&nbsp; {{ now()->format('Y') }}
    </div>

    <script>window.onload = () => window.print();</script>
</body>
</html>

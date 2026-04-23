<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        {{ ucfirst($type) }} Report — {{ now()->format('M d, Y') }}
    </title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111; padding: 24px; }

        /* Header */
        .report-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid #e5e7eb; }
        .report-title { font-size: 22px; font-weight: 700; color: #111; }
        .report-sub   { font-size: 11px; color: #6b7280; margin-top: 4px; }
        .report-meta  { text-align: right; font-size: 11px; color: #6b7280; }

        /* Summary boxes */
        .summary { display: flex; gap: 16px; margin-bottom: 24px; }
        .summary-box { flex: 1; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px 16px; background: #f9fafb; }
        .summary-box .label { font-size: 10px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; }
        .summary-box .value { font-size: 18px; font-weight: 700; color: #111; margin-top: 2px; }
        .summary-box.green  { background: #f0fdf4; border-color: #bbf7d0; }
        .summary-box.green .value { color: #059669; }
        .summary-box.amber  { background: #fffbeb; border-color: #fde68a; }
        .summary-box.amber .value { color: #d97706; }

        /* Table */
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        thead tr { background: #f3f4f6; }
        th { text-align: left; padding: 8px 10px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; border-bottom: 2px solid #e5e7eb; white-space: nowrap; }
        td { padding: 8px 10px; border-bottom: 1px solid #f3f4f6; font-size: 11px; }
        tr:nth-child(even) td { background: #fafafa; }

        .badge { display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 10px; font-weight: 600; }
        .badge-paid      { background: #d1fae5; color: #065f46; }
        .badge-pending   { background: #fef3c7; color: #92400e; }
        .badge-rejected  { background: #fee2e2; color: #991b1b; }
        .badge-active    { background: #d1fae5; color: #065f46; }
        .badge-suspended { background: #fee2e2; color: #991b1b; }
        .badge-completed { background: #d1fae5; color: #065f46; }
        .badge-failed    { background: #fee2e2; color: #991b1b; }

        .mono { font-family: monospace; }
        .amount { font-weight: 700; color: #059669; }

        .section-title { font-size: 13px; font-weight: 700; color: #374151; margin: 20px 0 8px; }

        .footer { margin-top: 32px; padding-top: 12px; border-top: 1px solid #e5e7eb; color: #9ca3af; font-size: 10px; display: flex; justify-content: space-between; }

        @media print {
            body { padding: 0; }
            @page { margin: 15mm; }
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="report-header">
        <div>
            <p class="report-title">
                @if($type === 'earnings') Earnings Report
                @elseif($type === 'vendors') Vendor Report
                @else Transaction Report
                @endif
            </p>
            <p class="report-sub">
                Agent: {{ auth()->user()->name }}
                @if($dateFrom || $dateTo)
                    &nbsp;|&nbsp; Period: {{ $dateFrom ?? 'All time' }} – {{ $dateTo ?? now()->format('Y-m-d') }}
                @endif
            </p>
        </div>
        <div class="report-meta">
            <p>Generated: {{ now()->format('M d, Y H:i') }}</p>
            <p style="margin-top:4px;">{{ config('app.name') }}</p>
        </div>
    </div>

    {{-- Summary --}}
    <div class="summary">
        <div class="summary-box green">
            <p class="label">Total Earned</p>
            <p class="value">${{ number_format($summary['total_earned'], 2) }}</p>
        </div>
        <div class="summary-box amber">
            <p class="label">Pending</p>
            <p class="value">${{ number_format($summary['total_pending'], 2) }}</p>
        </div>
        <div class="summary-box">
            <p class="label">This Month</p>
            <p class="value">${{ number_format($summary['this_month'], 2) }}</p>
        </div>
        <div class="summary-box">
            <p class="label">Vendors</p>
            <p class="value">{{ $summary['total_vendors'] }}</p>
        </div>
        <div class="summary-box">
            <p class="label">Total Records</p>
            <p class="value">{{ $records->count() }}</p>
        </div>
    </div>

    {{-- EARNINGS TABLE --}}
    @if($type === 'earnings')
        <p class="section-title">Commission Records</p>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Reference</th>
                    <th>Vendor</th>
                    <th>Order #</th>
                    <th>Amount</th>
                    <th>Rate</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $i => $r)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="mono">{{ $r->reference ?? 'COM-' . str_pad($r->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $r->vendor?->businessProfile?->business_name ?? 'N/A' }}</td>
                        <td class="mono">{{ $r->order?->order_number ?? '—' }}</td>
                        <td class="amount">${{ number_format($r->amount, 2) }}</td>
                        <td>{{ $r->rate ? $r->rate . '%' : '—' }}</td>
                        <td><span class="badge badge-{{ $r->status }}">{{ ucfirst($r->status) }}</span></td>
                        <td>{{ $r->created_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="text-align:center;padding:20px;color:#9ca3af;">No records found.</td></tr>
                @endforelse
            </tbody>
        </table>

        @php
            $paidTotal    = $records->where('status', 'paid')->sum('amount');
            $pendingTotal = $records->where('status', 'pending')->sum('amount');
        @endphp
        <div style="margin-top:16px;display:flex;gap:24px;justify-content:flex-end;">
            <span style="font-size:11px;color:#6b7280;">Paid: <strong style="color:#059669;">${{ number_format($paidTotal, 2) }}</strong></span>
            <span style="font-size:11px;color:#6b7280;">Pending: <strong style="color:#d97706;">${{ number_format($pendingTotal, 2) }}</strong></span>
            <span style="font-size:11px;color:#6b7280;">Total: <strong>${{ number_format($paidTotal + $pendingTotal, 2) }}</strong></span>
        </div>

    {{-- VENDORS TABLE --}}
    @elseif($type === 'vendors')
        <p class="section-title">Vendor Records</p>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Business Name</th>
                    <th>Contact Person</th>
                    <th>Email</th>
                    <th>Country</th>
                    <th>City</th>
                    <th>Status</th>
                    <th>Joined</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $i => $r)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td style="font-weight:600;">{{ $r->businessProfile?->business_name ?? 'N/A' }}</td>
                        <td>{{ $r->user?->name ?? 'N/A' }}</td>
                        <td>{{ $r->user?->email ?? '—' }}</td>
                        <td>{{ $r->businessProfile?->country?->name ?? '—' }}</td>
                        <td>{{ $r->businessProfile?->city ?? '—' }}</td>
                        <td><span class="badge badge-{{ $r->account_status }}">{{ ucfirst($r->account_status) }}</span></td>
                        <td>{{ $r->created_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="text-align:center;padding:20px;color:#9ca3af;">No records found.</td></tr>
                @endforelse
            </tbody>
        </table>

    {{-- TRANSACTIONS TABLE --}}
    @else
        <p class="section-title">Transaction Records</p>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Transaction #</th>
                    <th>Order #</th>
                    <th>Amount</th>
                    <th>Currency</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $i => $r)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="mono">{{ $r->transaction_number ?? 'TXN-' . str_pad($r->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="mono">{{ $r->order?->order_number ?? '—' }}</td>
                        <td class="amount">${{ number_format($r->amount, 2) }}</td>
                        <td>{{ $r->currency ?? 'USD' }}</td>
                        <td>{{ $r->payment_method ?? '—' }}</td>
                        <td><span class="badge badge-{{ $r->status }}">{{ ucfirst($r->status) }}</span></td>
                        <td>{{ $r->created_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="text-align:center;padding:20px;color:#9ca3af;">No records found.</td></tr>
                @endforelse
            </tbody>
        </table>

        @php $txTotal = $records->where('status', 'completed')->sum('amount'); @endphp
        <div style="margin-top:16px;text-align:right;">
            <span style="font-size:11px;color:#6b7280;">Completed Total: <strong style="color:#059669;">${{ number_format($txTotal, 2) }}</strong></span>
        </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <span>{{ config('app.name') }} &mdash; Agent Report</span>
        <span>Generated {{ now()->format('Y-m-d H:i:s') }}</span>
    </div>

    <script>window.onload = () => window.print();</script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Report — {{ now()->format('M d, Y') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111; padding: 24px; }

        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; padding-bottom: 14px; border-bottom: 2px solid #e5e7eb; }
        .header h1 { font-size: 20px; font-weight: 700; color: #4338ca; }
        .header .sub { font-size: 11px; color: #6b7280; margin-top: 3px; }
        .header .meta { text-align: right; font-size: 11px; color: #9ca3af; }

        .summary { display: flex; gap: 14px; margin-bottom: 20px; }
        .box { flex: 1; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px 14px; background: #f9fafb; }
        .box .lbl { font-size: 10px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; }
        .box .val { font-size: 18px; font-weight: 700; margin-top: 3px; }
        .box.indigo .val { color: #4338ca; }
        .box.blue   .val { color: #2563eb; }
        .box.purple .val { color: #7c3aed; }

        .section { font-size: 12px; font-weight: 700; color: #374151; margin: 18px 0 8px; text-transform: uppercase; letter-spacing: 0.05em; border-left: 3px solid #4338ca; padding-left: 8px; }

        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #f3f4f6; }
        th { text-align: left; padding: 7px 10px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; border-bottom: 2px solid #e5e7eb; white-space: nowrap; }
        td { padding: 7px 10px; border-bottom: 1px solid #f3f4f6; font-size: 11px; }
        tr:nth-child(even) td { background: #fafafa; }

        .ctr-good { color: #059669; font-weight: 700; }
        .ctr-avg  { color: #d97706; font-weight: 700; }
        .ctr-low  { color: #dc2626; font-weight: 700; }
        .clicks   { font-weight: 700; color: #2563eb; }

        .vendor-section { margin-top: 20px; }

        .footer { margin-top: 28px; padding-top: 10px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; font-size: 10px; color: #9ca3af; }

        @media print {
            body { padding: 0; }
            @page { margin: 15mm; }
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <div>
            <h1>Performance Report</h1>
            <p class="sub">Agent: {{ auth()->user()->name }} &nbsp;|&nbsp; Generated: {{ now()->format('M d, Y H:i') }}</p>
        </div>
        <div class="meta">
            <p>{{ config('app.name') }}</p>
            <p style="margin-top:3px;">{{ now()->format('Y') }}</p>
        </div>
    </div>

    {{-- Summary --}}
    <div class="summary">
        <div class="box indigo">
            <p class="lbl">Overall CTR</p>
            <p class="val">{{ $summary['overall_ctr'] }}%</p>
        </div>
        <div class="box blue">
            <p class="lbl">Total Clicks</p>
            <p class="val">{{ number_format($summary['total_clicks']) }}</p>
        </div>
        <div class="box purple">
            <p class="lbl">Total Impressions</p>
            <p class="val">{{ number_format($summary['total_impressions']) }}</p>
        </div>
        <div class="box">
            <p class="lbl">Product Records</p>
            <p class="val" style="color:#374151;">{{ $records->count() }}</p>
        </div>
    </div>

    {{-- Vendor Breakdown --}}
    <p class="section">Performance by Vendor</p>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Vendor</th>
                <th>Clicks</th>
                <th>Impressions</th>
                <th>CTR</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vendorBreakdown as $i => $row)
                @php
                    $ctr   = $row->ctr ?? 0;
                    $class = $ctr >= 5 ? 'ctr-good' : ($ctr >= 2 ? 'ctr-avg' : 'ctr-low');
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-weight:600;">{{ $row->vendor?->business_name ?? 'N/A' }}</td>
                    <td class="clicks">{{ number_format($row->total_clicks) }}</td>
                    <td>{{ number_format($row->total_impressions) }}</td>
                    <td class="{{ $class }}">{{ $ctr }}%</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center;padding:16px;color:#9ca3af;">No vendor data.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Product Records --}}
    <p class="section" style="margin-top:24px;">Product Performance Records</p>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Vendor</th>
                <th>Clicks</th>
                <th>Impressions</th>
                <th>CTR</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $i => $row)
                @php
                    $ctr   = $row->ctr ?? 0;
                    $class = $ctr >= 5 ? 'ctr-good' : ($ctr >= 2 ? 'ctr-avg' : 'ctr-low');
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-weight:600;">{{ $row->product?->name ?? 'N/A' }}</td>
                    <td>{{ $row->vendor?->business_name ?? 'N/A' }}</td>
                    <td class="clicks">{{ number_format($row->total_clicks) }}</td>
                    <td>{{ number_format($row->total_impressions) }}</td>
                    <td class="{{ $class }}">{{ $ctr }}%</td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center;padding:16px;color:#9ca3af;">No performance data yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <span>{{ config('app.name') }} &mdash; Agent Performance Report</span>
        <span>{{ now()->format('Y-m-d H:i:s') }}</span>
    </div>

    <script>window.onload = () => window.print();</script>
</body>
</html>

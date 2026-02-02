<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Commission Report - {{ now()->format('M d, Y') }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
        }

        .header h1 {
            font-size: 18px;
            margin: 5px 0;
            font-weight: bold;
        }

        .header p {
            margin: 3px 0;
            font-size: 10px;
            color: #666;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .stat-card {
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
            background-color: #f9f9f9;
        }

        .stat-label {
            font-weight: bold;
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .stat-value {
            font-weight: bold;
            font-size: 16px;
            color: #000;
            margin-bottom: 3px;
        }

        .stat-subtext {
            font-size: 8px;
            color: #666;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #000;
        }

        .data-table {
            margin-top: 10px;
            page-break-inside: avoid;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        .data-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-paid { background-color: #d1fae5; color: #065f46; }
        .badge-pending { background-color: #fed7aa; color: #9a3412; }
        .badge-processing { background-color: #dbeafe; color: #1e40af; }
        .badge-cancelled { background-color: #fee2e2; color: #991b1b; }

        .footer {
            text-align: center;
            font-size: 9px;
            color: #666;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }

        .info-box {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 15px;
            background-color: #f9f9f9;
        }

        .info-box-title {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 5px;
            color: #333;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>COMMISSION EARNINGS REPORT</h1>
        <p>Detailed Commission Summary & Payment History</p>
        <p>
            Generated on: {{ now()->format('d/m/Y H:i:s') }} |
            Agent: {{ auth()->user()->name }} ({{ auth()->user()->email }}) |
            Filter: {{ $filterLabels['status'] }} - {{ $filterLabels['date'] }}
        </p>
    </div>

    <!-- Filter Information -->
    @if($filterLabels['status'] !== 'All Status' || $filterLabels['date'] !== 'All Time')
        <div class="info-box">
            <div class="info-box-title">Report Filters Applied:</div>
            <p style="font-size: 10px; margin: 0;">
                <strong>Status:</strong> {{ $filterLabels['status'] }} |
                <strong>Period:</strong> {{ $filterLabels['date'] }}
            </p>
        </div>
    @endif

    <!-- Key Statistics - Amount -->
    <div class="section-title">Financial Summary</div>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Earned</div>
            <div class="stat-value">${{ number_format($stats['total_amount'], 2) }}</div>
            <div class="stat-subtext">{{ $stats['total'] }} commission(s)</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Paid Out</div>
            <div class="stat-value">${{ number_format($stats['paid_amount'], 2) }}</div>
            <div class="stat-subtext">{{ $stats['paid'] }} payment(s)</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Pending</div>
            <div class="stat-value">${{ number_format($stats['pending_amount'], 2) }}</div>
            <div class="stat-subtext">{{ $stats['pending'] }} awaiting</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Processing</div>
            <div class="stat-value">${{ number_format($stats['processing_amount'], 2) }}</div>
            <div class="stat-subtext">{{ $stats['processing'] }} in process</div>
        </div>
    </div>

    <!-- Status Distribution -->
    <div class="section-title">Status Distribution</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Status</th>
                <th class="text-right">Count</th>
                <th class="text-right">Percentage</th>
                <th class="text-right">Total Amount</th>
                <th class="text-right">Average Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><span class="badge badge-paid">Paid</span></td>
                <td class="text-right">{{ number_format($stats['paid']) }}</td>
                <td class="text-right">{{ $stats['total'] > 0 ? number_format(($stats['paid'] / $stats['total']) * 100, 1) : '0.0' }}%</td>
                <td class="text-right">${{ number_format($stats['paid_amount'], 2) }}</td>
                <td class="text-right">${{ $stats['paid'] > 0 ? number_format($stats['paid_amount'] / $stats['paid'], 2) : '0.00' }}</td>
            </tr>
            <tr>
                <td><span class="badge badge-pending">Pending</span></td>
                <td class="text-right">{{ number_format($stats['pending']) }}</td>
                <td class="text-right">{{ $stats['total'] > 0 ? number_format(($stats['pending'] / $stats['total']) * 100, 1) : '0.0' }}%</td>
                <td class="text-right">${{ number_format($stats['pending_amount'], 2) }}</td>
                <td class="text-right">${{ $stats['pending'] > 0 ? number_format($stats['pending_amount'] / $stats['pending'], 2) : '0.00' }}</td>
            </tr>
            <tr>
                <td><span class="badge badge-processing">Processing</span></td>
                <td class="text-right">{{ number_format($stats['processing']) }}</td>
                <td class="text-right">{{ $stats['total'] > 0 ? number_format(($stats['processing'] / $stats['total']) * 100, 1) : '0.0' }}%</td>
                <td class="text-right">${{ number_format($stats['processing_amount'], 2) }}</td>
                <td class="text-right">${{ $stats['processing'] > 0 ? number_format($stats['processing_amount'] / $stats['processing'], 2) : '0.00' }}</td>
            </tr>
            <tr style="font-weight: bold; background-color: #f2f2f2;">
                <td>Total</td>
                <td class="text-right">{{ number_format($stats['total']) }}</td>
                <td class="text-right">100%</td>
                <td class="text-right">${{ number_format($stats['total_amount'], 2) }}</td>
                <td class="text-right">${{ $stats['total'] > 0 ? number_format($stats['total_amount'] / $stats['total'], 2) : '0.00' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- All Commissions -->
    <div class="section-title">Complete Commission History ({{ $commissions->count() }})</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Commission ID</th>
                <th>Referral Name</th>
                <th>Ref Code</th>
                <th class="text-right">Amount</th>
                <th>Status</th>
                <th>Date Earned</th>
                <th>Payment Date</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($commissions as $commission)
                <tr>
                    <td style="font-family: monospace; font-weight: bold;">#{{ str_pad($commission->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $commission->referral->name ?? 'N/A' }}</td>
                    <td style="font-family: monospace; font-size: 9px;">{{ $commission->referral->referral_code ?? 'N/A' }}</td>
                    <td class="text-right" style="font-weight: bold;">${{ number_format($commission->amount, 2) }}</td>
                    <td><span class="badge badge-{{ strtolower($commission->status) }}">{{ ucfirst($commission->status) }}</span></td>
                    <td style="font-size: 9px;">{{ $commission->created_at->format('M d, Y') }}</td>
                    <td style="font-size: 9px;">{{ $commission->paid_at ? $commission->paid_at->format('M d, Y') : '-' }}</td>
                    <td style="font-size: 9px;">{{ $commission->notes ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #666;">
                        No commissions found for the selected filters
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($commissions->count() > 0)
            <tfoot style="background-color: #f2f2f2; font-weight: bold;">
                <tr>
                    <td colspan="3">Grand Total:</td>
                    <td class="text-right">${{ number_format($commissions->sum('amount'), 2) }}</td>
                    <td colspan="4">{{ $commissions->count() }} commission(s)</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <!-- Top Performing Referrals -->
    @php
        $topReferrals = $commissions->groupBy('referral_id')->map(function($group) {
            return [
                'referral' => $group->first()->referral,
                'total' => $group->sum('amount'),
                'count' => $group->count(),
            ];
        })->sortByDesc('total')->take(10);
    @endphp

    @if($topReferrals->count() > 0)
        <div class="section-title" style="page-break-before: always;">Top 10 Commission Earning Referrals</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Referral Name</th>
                    <th>Ref Code</th>
                    <th class="text-right">Total Commissions</th>
                    <th class="text-right">Commission Count</th>
                    <th class="text-right">Average Commission</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topReferrals as $index => $item)
                    <tr>
                        <td class="text-center" style="font-weight: bold;">{{ $index + 1 }}</td>
                        <td>{{ $item['referral']->name ?? 'N/A' }}</td>
                        <td style="font-family: monospace; font-size: 9px;">{{ $item['referral']->referral_code ?? 'N/A' }}</td>
                        <td class="text-right" style="font-weight: bold;">${{ number_format($item['total'], 2) }}</td>
                        <td class="text-right">{{ $item['count'] }}</td>
                        <td class="text-right">${{ number_format($item['total'] / $item['count'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Monthly Breakdown -->
    @php
        $monthlyBreakdown = $commissions->groupBy(function($commission) {
            return $commission->created_at->format('Y-m');
        })->map(function($group, $month) {
            return [
                'month' => \Carbon\Carbon::parse($month . '-01')->format('M Y'),
                'count' => $group->count(),
                'total' => $group->sum('amount'),
                'paid' => $group->where('status', 'paid')->sum('amount'),
                'pending' => $group->where('status', 'pending')->sum('amount'),
            ];
        })->sortKeysDesc();
    @endphp

    @if($monthlyBreakdown->count() > 0)
        <div class="section-title">Monthly Breakdown</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Month</th>
                    <th class="text-right">Commissions</th>
                    <th class="text-right">Total Amount</th>
                    <th class="text-right">Paid</th>
                    <th class="text-right">Pending</th>
                    <th class="text-right">Average</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyBreakdown as $data)
                    <tr>
                        <td style="font-weight: bold;">{{ $data['month'] }}</td>
                        <td class="text-right">{{ $data['count'] }}</td>
                        <td class="text-right" style="font-weight: bold;">${{ number_format($data['total'], 2) }}</td>
                        <td class="text-right">${{ number_format($data['paid'], 2) }}</td>
                        <td class="text-right">${{ number_format($data['pending'], 2) }}</td>
                        <td class="text-right">${{ number_format($data['total'] / $data['count'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Report Summary -->
    <div class="section-title">Report Summary</div>
    <table class="data-table">
        <tbody>
            <tr>
                <td style="width: 30%; font-weight: bold;">Agent Name</td>
                <td>{{ auth()->user()->name }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Agent Email</td>
                <td>{{ auth()->user()->email }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Report Period</td>
                <td>{{ $filterLabels['date'] }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Status Filter</td>
                <td>{{ $filterLabels['status'] }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Total Commissions</td>
                <td>{{ number_format($stats['total']) }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Total Amount Earned</td>
                <td>${{ number_format($stats['total_amount'], 2) }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Amount Paid Out</td>
                <td>${{ number_format($stats['paid_amount'], 2) }} ({{ $stats['total'] > 0 ? number_format(($stats['paid'] / $stats['total']) * 100, 1) : '0.0' }}%)</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Amount Pending</td>
                <td>${{ number_format($stats['pending_amount'], 2) }} ({{ $stats['total'] > 0 ? number_format(($stats['pending'] / $stats['total']) * 100, 1) : '0.0' }}%)</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Average Commission</td>
                <td>${{ $stats['total'] > 0 ? number_format($stats['total_amount'] / $stats['total'], 2) : '0.00' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Payment Success Rate</td>
                <td>{{ $stats['total'] > 0 ? number_format(($stats['paid'] / $stats['total']) * 100, 1) : '0.0' }}%</td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Commission Management System - Detailed Earnings Report</p>
        <p>Page 1 of 1 | Report ID: COM-{{ now()->format('Ymd-His') }} | Generated by: {{ auth()->user()->email }}</p>
        <p class="no-print">
            <button onclick="window.print()" style="padding: 5px 15px; background: #007bff; color: white; border: none; cursor: pointer; border-radius: 3px;">
                Print Report
            </button>
            <button onclick="window.close()" style="padding: 5px 15px; background: #6c757d; color: white; border: none; cursor: pointer; border-radius: 3px; margin-left: 10px;">
                Close
            </button>
        </p>
    </div>

    <script>
        window.onload = function() {
            // Uncomment to auto-print on load
            window.print();
        };
    </script>
</body>
</html>

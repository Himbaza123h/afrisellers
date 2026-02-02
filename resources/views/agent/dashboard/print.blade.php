<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Agent Dashboard Report - {{ now()->format('M d, Y') }}</title>
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

        .stats-section {
            margin-bottom: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            border: 1px solid #000;
            padding: 12px;
            text-align: center;
            background-color: #f9f9f9;
        }

        .stat-label {
            font-weight: bold;
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .stat-value {
            font-weight: bold;
            font-size: 16px;
            color: #000;
            margin-bottom: 5px;
        }

        .stat-subtext {
            font-size: 9px;
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
            padding: 8px;
            text-align: left;
        }

        .data-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 10px;
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
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-active { background-color: #d1fae5; color: #065f46; }
        .badge-pending { background-color: #fed7aa; color: #9a3412; }
        .badge-paid { background-color: #d1fae5; color: #065f46; }
        .badge-inactive { background-color: #fee2e2; color: #991b1b; }
        .badge-rejected { background-color: #fee2e2; color: #991b1b; }

        .footer {
            text-align: center;
            font-size: 9px;
            color: #666;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 15px 0;
        }

        .info-box {
            border: 1px solid #ddd;
            padding: 10px;
            background-color: #f9f9f9;
        }

        .info-box-title {
            font-weight: bold;
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .info-box-value {
            font-size: 12px;
            color: #000;
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
        <h1>AGENT DASHBOARD REPORT</h1>
        <p>Referral Performance & Commission Overview</p>
        <p>
            Generated on: {{ now()->format('d/m/Y H:i:s') }} |
            Agent: {{ auth()->user()->name }} ({{ auth()->user()->email }})
        </p>
    </div>

    <!-- Key Statistics -->
    <div class="section-title">Key Performance Indicators</div>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Referrals</div>
            <div class="stat-value">{{ number_format($totalReferrals) }}</div>
            <div class="stat-subtext">{{ $activeReferrals }} Active, {{ $pendingReferrals }} Pending</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Total Commissions</div>
            <div class="stat-value">${{ number_format($totalCommissions, 2) }}</div>
            <div class="stat-subtext">All time earnings</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Paid Commissions</div>
            <div class="stat-value">${{ number_format($paidCommissions, 2) }}</div>
            <div class="stat-subtext">Successfully received</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Pending Commissions</div>
            <div class="stat-value">${{ number_format($pendingCommissions, 2) }}</div>
            <div class="stat-subtext">Awaiting payment</div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="section-title">Performance Metrics</div>
    <div class="info-grid">
        <div class="info-box">
            <div class="info-box-title">Conversion Rate</div>
            <div class="info-box-value">
                {{ $totalReferrals > 0 ? number_format(($activeReferrals / $totalReferrals) * 100, 1) : '0.0' }}%
            </div>
        </div>

        <div class="info-box">
            <div class="info-box-title">Average Commission</div>
            <div class="info-box-value">
                ${{ $totalReferrals > 0 ? number_format($totalCommissions / $totalReferrals, 2) : '0.00' }}
            </div>
        </div>

        <div class="info-box">
            <div class="info-box-title">Commission Rate</div>
            <div class="info-box-value">
                {{ $totalCommissions > 0 ? number_format(($paidCommissions / $totalCommissions) * 100, 1) : '0.0' }}% Paid
            </div>
        </div>
    </div>

    <!-- Referral Status Distribution -->
    <div class="section-title">Referral Status Distribution</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Status</th>
                <th class="text-right">Count</th>
                <th class="text-right">Percentage</th>
                <th class="text-right">Commission Earned</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><span class="badge badge-active">Active</span></td>
                <td class="text-right">{{ number_format($activeReferrals) }}</td>
                <td class="text-right">
                    {{ $totalReferrals > 0 ? number_format(($activeReferrals / $totalReferrals) * 100, 1) : '0.0' }}%
                </td>
                <td class="text-right">
                    ${{ number_format($commissions->where('referral.status', 'active')->sum('amount'), 2) }}
                </td>
            </tr>
            <tr>
                <td><span class="badge badge-pending">Pending</span></td>
                <td class="text-right">{{ number_format($pendingReferrals) }}</td>
                <td class="text-right">
                    {{ $totalReferrals > 0 ? number_format(($pendingReferrals / $totalReferrals) * 100, 1) : '0.0' }}%
                </td>
                <td class="text-right">
                    ${{ number_format($commissions->where('referral.status', 'pending')->sum('amount'), 2) }}
                </td>
            </tr>
            <tr>
                <td><span class="badge badge-inactive">Inactive</span></td>
                <td class="text-right">{{ number_format($totalReferrals - $activeReferrals - $pendingReferrals) }}</td>
                <td class="text-right">
                    {{ $totalReferrals > 0 ? number_format((($totalReferrals - $activeReferrals - $pendingReferrals) / $totalReferrals) * 100, 1) : '0.0' }}%
                </td>
                <td class="text-right">
                    ${{ number_format($commissions->whereNotIn('referral.status', ['active', 'pending'])->sum('amount'), 2) }}
                </td>
            </tr>
            <tr style="font-weight: bold;">
                <td>Total</td>
                <td class="text-right">{{ number_format($totalReferrals) }}</td>
                <td class="text-right">100%</td>
                <td class="text-right">${{ number_format($totalCommissions, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- All Referrals -->
    @if($referrals->count() > 0)
        <div class="section-title">All Referrals ({{ $referrals->count() }})</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Ref ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Commission Earned</th>
                    <th>Date Referred</th>
                </tr>
            </thead>
            <tbody>
                @foreach($referrals as $referral)
                    <tr>
                        <td>#{{ str_pad($referral->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $referral->user->name ?? 'N/A' }}</td>
                        <td>{{ $referral->user->email ?? 'N/A' }}</td>
                        <td>{{ $referral->user->phone ?? 'N/A' }}</td>
                        <td>
                            <span class="badge badge-{{ strtolower($referral->status) }}">
                                {{ ucfirst($referral->status) }}
                            </span>
                        </td>
                        <td class="text-right">
                            ${{ number_format($commissions->where('referral_id', $referral->id)->sum('amount'), 2) }}
                        </td>
                        <td>{{ $referral->created_at->format('M d, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="section-title">Referrals</div>
        <div style="text-align: center; padding: 30px; color: #666; border: 1px solid #ddd;">
            No referrals found
        </div>
    @endif

    <!-- Commission History -->
    @if($commissions->count() > 0)
        <div class="section-title" style="page-break-before: always;">Commission History ({{ $commissions->count() }})</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Commission ID</th>
                    <th>Referral Name</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Payment Method</th>
                    <th>Date Earned</th>
                    <th>Date Paid</th>
                </tr>
            </thead>
            <tbody>
                @foreach($commissions as $commission)
                    <tr>
                        <td>#{{ str_pad($commission->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $commission->referral->user->name ?? 'N/A' }}</td>
                        <td class="text-right">${{ number_format($commission->amount, 2) }}</td>
                        <td>
                            <span class="badge badge-{{ strtolower($commission->status) }}">
                                {{ ucfirst($commission->status) }}
                            </span>
                        </td>
                        <td>{{ $commission->payment_method ?? 'N/A' }}</td>
                        <td>{{ $commission->created_at->format('M d, Y') }}</td>
                        <td>{{ $commission->paid_at ? $commission->paid_at->format('M d, Y') : '-' }}</td>
                    </tr>
                @endforeach
                <tr style="font-weight: bold; background-color: #f2f2f2;">
                    <td colspan="2">Total Commissions</td>
                    <td class="text-right">${{ number_format($commissions->sum('amount'), 2) }}</td>
                    <td colspan="4"></td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="section-title">Commission History</div>
        <div style="text-align: center; padding: 30px; color: #666; border: 1px solid #ddd;">
            No commissions found
        </div>
    @endif

    <!-- Commission Summary by Status -->
    <div class="section-title">Commission Summary by Status</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Status</th>
                <th class="text-right">Count</th>
                <th class="text-right">Total Amount</th>
                <th class="text-right">Average Amount</th>
                <th class="text-right">Percentage</th>
            </tr>
        </thead>
        <tbody>
            @php
                $paidCount = $commissions->where('status', 'paid')->count();
                $pendingCount = $commissions->where('status', 'pending')->count();
                $paidAmount = $commissions->where('status', 'paid')->sum('amount');
                $pendingAmount = $commissions->where('status', 'pending')->sum('amount');
                $totalAmount = $commissions->sum('amount');
                $totalCount = $commissions->count();
            @endphp
            <tr>
                <td><span class="badge badge-paid">Paid</span></td>
                <td class="text-right">{{ number_format($paidCount) }}</td>
                <td class="text-right">${{ number_format($paidAmount, 2) }}</td>
                <td class="text-right">${{ $paidCount > 0 ? number_format($paidAmount / $paidCount, 2) : '0.00' }}</td>
                <td class="text-right">{{ $totalAmount > 0 ? number_format(($paidAmount / $totalAmount) * 100, 1) : '0.0' }}%</td>
            </tr>
            <tr>
                <td><span class="badge badge-pending">Pending</span></td>
                <td class="text-right">{{ number_format($pendingCount) }}</td>
                <td class="text-right">${{ number_format($pendingAmount, 2) }}</td>
                <td class="text-right">${{ $pendingCount > 0 ? number_format($pendingAmount / $pendingCount, 2) : '0.00' }}</td>
                <td class="text-right">{{ $totalAmount > 0 ? number_format(($pendingAmount / $totalAmount) * 100, 1) : '0.0' }}%</td>
            </tr>
            <tr style="font-weight: bold; background-color: #f2f2f2;">
                <td>Total</td>
                <td class="text-right">{{ number_format($totalCount) }}</td>
                <td class="text-right">${{ number_format($totalAmount, 2) }}</td>
                <td class="text-right">${{ $totalCount > 0 ? number_format($totalAmount / $totalCount, 2) : '0.00' }}</td>
                <td class="text-right">100%</td>
            </tr>
        </tbody>
    </table>

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
                <td style="font-weight: bold;">Total Referrals</td>
                <td>{{ number_format($totalReferrals) }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Active Referrals</td>
                <td>{{ number_format($activeReferrals) }} ({{ $totalReferrals > 0 ? number_format(($activeReferrals / $totalReferrals) * 100, 1) : '0.0' }}%)</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Total Commissions Earned</td>
                <td>${{ number_format($totalCommissions, 2) }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Paid Commissions</td>
                <td>${{ number_format($paidCommissions, 2) }} ({{ $totalCommissions > 0 ? number_format(($paidCommissions / $totalCommissions) * 100, 1) : '0.0' }}%)</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Pending Commissions</td>
                <td>${{ number_format($pendingCommissions, 2) }} ({{ $totalCommissions > 0 ? number_format(($pendingCommissions / $totalCommissions) * 100, 1) : '0.0' }}%)</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Average Commission per Referral</td>
                <td>${{ $totalReferrals > 0 ? number_format($totalCommissions / $totalReferrals, 2) : '0.00' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Conversion Rate</td>
                <td>{{ $totalReferrals > 0 ? number_format(($activeReferrals / $totalReferrals) * 100, 1) : '0.0' }}%</td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Agent Dashboard System - Performance Report</p>
        <p>Page 1 of 1 | Report ID: AGENT-{{ now()->format('Ymd-His') }} | Generated by: {{ auth()->user()->email }}</p>
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
            // Uncomment the line below to auto-print on load
            window.print();
        };
    </script>
</body>
</html>

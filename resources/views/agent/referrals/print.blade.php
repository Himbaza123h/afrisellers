<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Referrals Report - {{ now()->format('M d, Y') }}</title>
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
            grid-template-columns: repeat(6, 1fr);
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
            font-size: 14px;
            color: #000;
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

        .badge-active { background-color: #d1fae5; color: #065f46; }
        .badge-pending { background-color: #fed7aa; color: #9a3412; }
        .badge-inactive { background-color: #e5e7eb; color: #374151; }
        .badge-rejected { background-color: #fee2e2; color: #991b1b; }

        .footer {
            text-align: center;
            font-size: 9px;
            color: #666;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
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
        <h1>REFERRALS REPORT</h1>
        <p>Complete Referral Network Overview</p>
        <p>
            Generated on: {{ now()->format('d/m/Y H:i:s') }} |
            Agent: {{ auth()->user()->name }} ({{ auth()->user()->email }})
        </p>
    </div>

    <!-- Key Statistics -->
    <div class="section-title">Key Statistics</div>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Referrals</div>
            <div class="stat-value">{{ number_format($stats['total']) }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Active</div>
            <div class="stat-value">{{ number_format($stats['active']) }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Pending</div>
            <div class="stat-value">{{ number_format($stats['pending']) }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Inactive</div>
            <div class="stat-value">{{ number_format($stats['inactive']) }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Rejected</div>
            <div class="stat-value">{{ number_format($stats['rejected']) }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Total Commissions</div>
            <div class="stat-value">${{ number_format($stats['total_commissions'], 2) }}</div>
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
                <th class="text-right">Total Commissions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><span class="badge badge-active">Active</span></td>
                <td class="text-right">{{ number_format($stats['active']) }}</td>
                <td class="text-right">{{ $stats['total'] > 0 ? number_format(($stats['active'] / $stats['total']) * 100, 1) : '0.0' }}%</td>
                <td class="text-right">${{ number_format($referrals->where('status', 'active')->sum(function($r) { return $r->commissions->sum('amount'); }), 2) }}</td>
            </tr>
            <tr>
                <td><span class="badge badge-pending">Pending</span></td>
                <td class="text-right">{{ number_format($stats['pending']) }}</td>
                <td class="text-right">{{ $stats['total'] > 0 ? number_format(($stats['pending'] / $stats['total']) * 100, 1) : '0.0' }}%</td>
                <td class="text-right">${{ number_format($referrals->where('status', 'pending')->sum(function($r) { return $r->commissions->sum('amount'); }), 2) }}</td>
            </tr>
            <tr>
                <td><span class="badge badge-inactive">Inactive</span></td>
                <td class="text-right">{{ number_format($stats['inactive']) }}</td>
                <td class="text-right">{{ $stats['total'] > 0 ? number_format(($stats['inactive'] / $stats['total']) * 100, 1) : '0.0' }}%</td>
                <td class="text-right">${{ number_format($referrals->where('status', 'inactive')->sum(function($r) { return $r->commissions->sum('amount'); }), 2) }}</td>
            </tr>
            <tr>
                <td><span class="badge badge-rejected">Rejected</span></td>
                <td class="text-right">{{ number_format($stats['rejected']) }}</td>
                <td class="text-right">{{ $stats['total'] > 0 ? number_format(($stats['rejected'] / $stats['total']) * 100, 1) : '0.0' }}%</td>
                <td class="text-right">${{ number_format($referrals->where('status', 'rejected')->sum(function($r) { return $r->commissions->sum('amount'); }), 2) }}</td>
            </tr>
            <tr style="font-weight: bold; background-color: #f2f2f2;">
                <td>Total</td>
                <td class="text-right">{{ number_format($stats['total']) }}</td>
                <td class="text-right">100%</td>
                <td class="text-right">${{ number_format($stats['total_commissions'], 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- All Referrals -->
    <div class="section-title">All Referrals ({{ $referrals->count() }})</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Ref Code</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
                <th class="text-right">Commissions</th>
                <th class="text-right">Total Earned</th>
                <th>Date Added</th>
            </tr>
        </thead>
        <tbody>
            @forelse($referrals as $referral)
                <tr>
                    <td style="font-family: monospace; font-weight: bold;">{{ $referral->referral_code }}</td>
                    <td>{{ $referral->name }}</td>
                    <td>{{ $referral->email }}</td>
                    <td>{{ $referral->phone ?? '-' }}</td>
                    <td><span class="badge badge-{{ strtolower($referral->status) }}">{{ ucfirst($referral->status) }}</span></td>
                    <td class="text-right">{{ $referral->commissions->count() }}</td>
                    <td class="text-right">${{ number_format($referral->commissions->sum('amount'), 2) }}</td>
                    <td>{{ $referral->created_at->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #666;">
                        No referrals found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Top Earners -->
    @php
        $topEarners = $referrals->sortByDesc(function($r) {
            return $r->commissions->sum('amount');
        })->take(10);
    @endphp

    @if($topEarners->count() > 0)
        <div class="section-title" style="page-break-before: always;">Top 10 Commission Earners</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Ref Code</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th class="text-right">Total Earned</th>
                    <th class="text-right">Commissions</th>
                    <th class="text-right">Avg. Commission</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topEarners as $index => $referral)
                    <tr>
                        <td class="text-center" style="font-weight: bold;">{{ $index + 1 }}</td>
                        <td style="font-family: monospace; font-weight: bold;">{{ $referral->referral_code }}</td>
                        <td>{{ $referral->name }}</td>
                        <td><span class="badge badge-{{ strtolower($referral->status) }}">{{ ucfirst($referral->status) }}</span></td>
                        <td class="text-right" style="font-weight: bold;">${{ number_format($referral->commissions->sum('amount'), 2) }}</td>
                        <td class="text-right">{{ $referral->commissions->count() }}</td>
                        <td class="text-right">
                            ${{ $referral->commissions->count() > 0 ? number_format($referral->commissions->sum('amount') / $referral->commissions->count(), 2) : '0.00' }}
                        </td>
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
                <td style="font-weight: bold;">Total Referrals</td>
                <td>{{ number_format($stats['total']) }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Active Referrals</td>
                <td>{{ number_format($stats['active']) }} ({{ $stats['total'] > 0 ? number_format(($stats['active'] / $stats['total']) * 100, 1) : '0.0' }}%)</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Pending Referrals</td>
                <td>{{ number_format($stats['pending']) }} ({{ $stats['total'] > 0 ? number_format(($stats['pending'] / $stats['total']) * 100, 1) : '0.0' }}%)</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Total Commissions Earned</td>
                <td>${{ number_format($stats['total_commissions'], 2) }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Average Commission per Referral</td>
                <td>${{ $stats['total'] > 0 ? number_format($stats['total_commissions'] / $stats['total'], 2) : '0.00' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Conversion Rate</td>
                <td>{{ $stats['total'] > 0 ? number_format(($stats['active'] / $stats['total']) * 100, 1) : '0.0' }}%</td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Referrals Management System - Comprehensive Report</p>
        <p>Page 1 of 1 | Report ID: REF-{{ now()->format('Ymd-His') }} | Generated by: {{ auth()->user()->email }}</p>
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

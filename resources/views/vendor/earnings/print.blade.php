<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Earnings Report - {{ now()->format('M d, Y') }}</title>
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

        .stats-table {
            width: 100%;
            margin-bottom: 15px;
        }

        .stats-table td {
            padding: 8px;
            border: 1px solid #000;
            text-align: center;
            vertical-align: middle;
        }

        .stats-label {
            font-weight: bold;
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
        }

        .stats-value {
            font-weight: bold;
            font-size: 14px;
            margin-top: 3px;
        }

        .stats-subtext {
            font-size: 9px;
            margin-top: 2px;
        }

        .main-table {
            margin-top: 15px;
            page-break-inside: avoid;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        .main-table th {
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

        .footer {
            text-align: center;
            font-size: 9px;
            color: #666;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #000;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-up {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-down {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-neutral {
            background-color: #f3f4f6;
            color: #374151;
        }

        .highlight {
            background-color: #fef3c7;
            font-weight: bold;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>EARNINGS REPORT</h1>
        <p>Revenue & Financial Performance Overview</p>
        <p>Generated on: {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
    </div>

    <!-- Statistics Section -->
    <div class="stats-section">
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">Total Earnings</div>
                    <div class="stats-value">${{ number_format($stats['total_earnings'], 2) }}</div>
                    <div class="stats-subtext">
                        @if($stats['earnings_change'] > 0)
                            <span class="badge badge-up">↑ {{ number_format(abs($stats['earnings_change']), 1) }}%</span>
                        @elseif($stats['earnings_change'] < 0)
                            <span class="badge badge-down">↓ {{ number_format(abs($stats['earnings_change']), 1) }}%</span>
                        @else
                            <span class="badge badge-neutral">0%</span>
                        @endif
                        vs previous period
                    </div>
                </td>
                <td>
                    <div class="stats-label">Total Transactions</div>
                    <div class="stats-value">{{ number_format($stats['total_transactions']) }}</div>
                    <div class="stats-subtext">Completed transactions</div>
                </td>
                <td>
                    <div class="stats-label">Average Transaction</div>
                    <div class="stats-value">${{ number_format($stats['average_transaction'], 2) }}</div>
                    <div class="stats-subtext">Per order</div>
                </td>
                <td>
                    <div class="stats-label">Date Range</div>
                    <div class="stats-value">
                        {{ \Carbon\Carbon::parse($startDate)->format('M d') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                    </div>
                    <div class="stats-subtext">{{ \Carbon\Carbon::parse($startDate)->diffInDays($endDate) + 1 }} days</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Top Earning Days -->
    @if($stats['top_days']->isNotEmpty())
        <div class="section-title">Top Earning Days</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 15%;">Date</th>
                    <th style="width: 15%;" class="text-right">Earnings</th>
                    <th style="width: 10%;" class="text-right">% of Total</th>
                    <th style="width: 15%;">Day of Week</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['top_days'] as $day)
                    <tr class="{{ $loop->first ? 'highlight' : '' }}">
                        <td>{{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}</td>
                        <td class="text-right">${{ number_format($day->total, 2) }}</td>
                        <td class="text-right">
                            {{ $stats['total_earnings'] > 0 ? number_format(($day->total / $stats['total_earnings']) * 100, 1) : 0 }}%
                        </td>
                        <td>{{ \Carbon\Carbon::parse($day->date)->format('l') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Payment Method Distribution -->
    @if($paymentMethodDistribution->count() > 0)
        <div class="section-title">Payment Method Distribution</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 20%;">Payment Method</th>
                    <th style="width: 15%;" class="text-right">Total Amount</th>
                    <th style="width: 10%;" class="text-right">Transaction Count</th>
                    <th style="width: 10%;" class="text-right">Average Amount</th>
                    <th style="width: 10%;" class="text-right">% of Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($paymentMethodDistribution as $method)
                    <tr>
                        <td>{{ ucwords(str_replace('_', ' ', $method->payment_method)) }}</td>
                        <td class="text-right">${{ number_format($method->total_amount, 2) }}</td>
                        <td class="text-right">{{ $method->count }}</td>
                        <td class="text-right">
                            {{ $method->count > 0 ? '$' . number_format($method->total_amount / $method->count, 2) : '$0.00' }}
                        </td>
                        <td class="text-right">
                            {{ $stats['total_earnings'] > 0 ? number_format(($method->total_amount / $stats['total_earnings']) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Earnings List -->
    <div class="section-title">Earnings Details</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 10%;">Transaction #</th>
                <th style="width: 10%;">Order #</th>
                <th style="width: 15%;">Customer</th>
                <th style="width: 10%;" class="text-right">Amount</th>
                <th style="width: 12%;">Payment Method</th>
                <th style="width: 12%;">Date & Time</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td><strong>{{ $transaction->transaction_number }}</strong></td>
                    <td>
                        @if($transaction->order)
                            {{ $transaction->order->order_number }}
                        @else
                            <span style="color: #999;">N/A</span>
                        @endif
                    </td>
                    <td>
                        @if($transaction->buyer)
                            <strong>{{ $transaction->buyer->name }}</strong><br>
                            <small>{{ $transaction->buyer->email }}</small>
                        @else
                            <span style="color: #999;">Unknown</span>
                        @endif
                    </td>
                    <td class="text-right">${{ number_format($transaction->amount, 2) }}</td>
                    <td>
                        @if($transaction->payment_method)
                            {{ ucwords(str_replace('_', ' ', $transaction->payment_method)) }}
                        @else
                            <span style="color: #999;">Not specified</span>
                        @endif
                    </td>
                    <td>{{ $transaction->completed_at->format('M d, Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px;">
                        No earnings found for this period
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Summary -->
    <div class="section-title">Report Summary</div>
    <table class="main-table">
        <tbody>
            <tr>
                <td style="width: 25%; font-weight: bold;">Total Records</td>
                <td>{{ $transactions->count() }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Filtered Payment Method</td>
                <td>{{ request('payment_method') ? ucwords(str_replace('_', ' ', request('payment_method'))) : 'All methods' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Search Term</td>
                <td>{{ request('search') ?: 'None' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Total Earnings</td>
                <td><strong>${{ number_format($stats['total_earnings'], 2) }}</strong></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Daily Average</td>
                <td>
                    @php
                        $daysCount = \Carbon\Carbon::parse($startDate)->diffInDays($endDate) + 1;
                        $dailyAverage = $daysCount > 0 ? $stats['total_earnings'] / $daysCount : 0;
                    @endphp
                    <strong>${{ number_format($dailyAverage, 2) }}</strong> per day
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Earnings Management System - Vendor Report</p>
        <p>Page 1 of 1 | Total Records: {{ $transactions->count() }} | Report ID: EARN-{{ now()->format('Ymd-His') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

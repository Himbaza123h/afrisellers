<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Transactions Report - {{ now()->format('M d, Y') }}</title>
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

        .badge-completed {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-failed {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-cancelled {
            background-color: #f3f4f6;
            color: #374151;
        }

        .badge-order {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-refund {
            background-color: #ede9fe;
            color: #5b21b6;
        }

        .badge-adjustment {
            background-color: #f5f5f5;
            color: #525252;
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
        <h1>TRANSACTIONS REPORT</h1>
        <p>Payment Transactions & Financial Overview</p>
        <p>Generated on: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Statistics Section -->
    <div class="stats-section">
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">Total Transactions</div>
                    <div class="stats-value">{{ number_format($stats['total']) }}</div>
                </td>
                <td>
                    <div class="stats-label">Completed</div>
                    <div class="stats-value">{{ number_format($stats['completed']) }}</div>
                    <div class="stats-subtext">{{ $stats['completed_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">Pending</div>
                    <div class="stats-value">{{ number_format($stats['pending']) }}</div>
                    <div class="stats-subtext">{{ $stats['pending_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">Failed</div>
                    <div class="stats-value">{{ number_format($stats['failed']) }}</div>
                    <div class="stats-subtext">Requires attention</div>
                </td>
                <td>
                    <div class="stats-label">Total Revenue</div>
                    <div class="stats-value">${{ number_format($stats['total_amount'], 2) }}</div>
                    <div class="stats-subtext">From completed transactions</div>
                </td>
                <td>
                    <div class="stats-label">Pending Amount</div>
                    <div class="stats-value">${{ number_format($stats['pending_amount'], 2) }}</div>
                    <div class="stats-subtext">Awaiting payment</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Status Distribution -->
    <div class="section-title">Status Distribution</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 20%;">Status</th>
                <th style="width: 15%;" class="text-right">Count</th>
                <th style="width: 15%;" class="text-right">Percentage</th>
            </tr>
        </thead>
        <tbody>
            @php $totalTransactions = $stats['total']; @endphp
            @foreach($statusDistribution as $status)
                <tr>
                    <td>{{ ucfirst($status->status) }}</td>
                    <td class="text-right">{{ $status->count }}</td>
                    <td class="text-right">{{ $totalTransactions > 0 ? number_format(($status->count / $totalTransactions) * 100, 1) : 0 }}%</td>
                </tr>
            @endforeach
            @if($totalTransactions > 0)
                <tr>
                    <td style="font-weight: bold;">Total</td>
                    <td class="text-right" style="font-weight: bold;">{{ $totalTransactions }}</td>
                    <td class="text-right" style="font-weight: bold;">100%</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Payment Method Distribution -->
    @if($paymentMethodDistribution->count() > 0)
        <div class="section-title">Payment Method Distribution</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 20%;">Payment Method</th>
                    <th style="width: 15%;" class="text-right">Count</th>
                    <th style="width: 15%;" class="text-right">Percentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($paymentMethodDistribution as $method)
                    <tr>
                        <td>{{ ucwords(str_replace('_', ' ', $method->payment_method)) }}</td>
                        <td class="text-right">{{ $method->count }}</td>
                        <td class="text-right">{{ $totalTransactions > 0 ? number_format(($method->count / $totalTransactions) * 100, 1) : 0 }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Transactions List -->
    <div class="section-title">Transactions Details</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 10%;">Transaction #</th>
                <th style="width: 10%;">Order #</th>
                <th style="width: 15%;">Customer</th>
                <th style="width: 8%;">Type</th>
                <th style="width: 10%;">Amount</th>
                <th style="width: 12%;">Payment Method</th>
                <th style="width: 8%;">Status</th>
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
                    <td>
                        @php
                            $badgeClass = match($transaction->type) {
                                'order' => 'badge-order',
                                'refund' => 'badge-refund',
                                'adjustment' => 'badge-adjustment',
                                default => 'badge-adjustment'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ ucfirst($transaction->type) }}</span>
                    </td>
                    <td class="text-right">${{ number_format($transaction->amount, 2) }}</td>
                    <td>
                        @if($transaction->payment_method)
                            {{ ucwords(str_replace('_', ' ', $transaction->payment_method)) }}
                        @else
                            <span style="color: #999;">Not specified</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $badgeClass = match($transaction->status) {
                                'completed' => 'badge-completed',
                                'pending' => 'badge-pending',
                                'failed' => 'badge-failed',
                                'cancelled' => 'badge-cancelled',
                                default => 'badge-pending'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ ucfirst($transaction->status) }}</span>
                    </td>
                    <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px;">
                        No transactions found
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
                <td style="font-weight: bold;">Date Range</td>
                <td>
                    @if(request('date_range'))
                        {{ request('date_range') }}
                    @else
                        All dates
                    @endif
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Filtered Status</td>
                <td>{{ request('status') ? ucfirst(request('status')) : 'All statuses' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Filtered Payment Method</td>
                <td>{{ request('payment_method') ? ucwords(str_replace('_', ' ', request('payment_method'))) : 'All methods' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Total Amount</td>
                <td><strong>${{ number_format($transactions->sum('amount'), 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Transaction Management System - Vendor Report</p>
        <p>Page 1 of 1 | Total Records: {{ $transactions->count() }} | Report ID: TXN-{{ now()->format('Ymd-His') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

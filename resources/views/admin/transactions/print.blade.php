<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Transactions Management Report</title>
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
            font-size: 16px;
            margin-top: 3px;
        }

        .stats-subtext {
            font-size: 9px;
            margin-top: 2px;
        }

        .main-table {
            margin-top: 15px;
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

        .main-table tbody tr {
            height: 30px;
        }

        .text-center {
            text-align: center;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-processing {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-completed {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-failed {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-refunded {
            background-color: #ede9fe;
            color: #5b21b6;
        }

        .type-payment {
            background-color: #d1fae5;
            color: #065f46;
        }

        .type-refund {
            background-color: #fed7aa;
            color: #c2410c;
        }

        .type-commission {
            background-color: #ede9fe;
            color: #5b21b6;
        }

        .type-payout {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .type-subscription {
            background-color: #f3e8ff;
            color: #7c3aed;
        }

        .footer {
            text-align: center;
            font-size: 9px;
            color: #666;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }

        .report-date {
            text-align: right;
            font-size: 10px;
            margin-bottom: 10px;
            color: #666;
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
        <h1>TRANSACTIONS MANAGEMENT REPORT</h1>
        <p>Complete Overview of All Financial Transactions</p>
    </div>

    <!-- Report Date -->
    <div class="report-date">
        Generated on: {{ now()->format('d/m/Y H:i:s') }}
    </div>

    <!-- Statistics Section -->
    <div class="stats-section">
        <!-- Main Stats Row -->
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">Total Transactions</div>
                    <div class="stats-value">{{ number_format($stats['total']) }}</div>
                </td>
                <td>
                    <div class="stats-label">Pending</div>
                    <div class="stats-value">{{ number_format($stats['pending']) }}</div>
                    <div class="stats-subtext" style="color: #92400e;">Awaiting</div>
                </td>
                <td>
                    <div class="stats-label">Completed</div>
                    <div class="stats-value">{{ number_format($stats['completed']) }}</div>
                    <div class="stats-subtext" style="color: #065f46;">{{ $stats['completed_percentage'] }}% success rate</div>
                </td>
                <td>
                    <div class="stats-label">Failed</div>
                    <div class="stats-value">{{ number_format($stats['failed']) }}</div>
                    <div class="stats-subtext" style="color: #991b1b;">{{ $stats['failed_percentage'] }}% failure rate</div>
                </td>
            </tr>
        </table>

        <!-- Financial Row -->
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">Total Revenue</div>
                    <div class="stats-value">${{ number_format($stats['total_amount'], 2) }}</div>
                    <div class="stats-subtext" style="color: #0d9488;">Completed transactions</div>
                </td>
                <td>
                    <div class="stats-label">Pending Amount</div>
                    <div class="stats-value">${{ number_format($stats['pending_amount'], 2) }}</div>
                    <div class="stats-subtext" style="color: #f59e0b;">Awaiting processing</div>
                </td>
                <td>
                    <div class="stats-label">Avg Transaction</div>
                    <div class="stats-value">${{ number_format($stats['avg_transaction'] ?? 0, 2) }}</div>
                    <div class="stats-subtext" style="color: #0ea5e9;">Average value</div>
                </td>
                <td>
                    <div class="stats-label">This Month</div>
                    <div class="stats-value">{{ number_format($stats['this_month']) }}</div>
                    <div class="stats-subtext" style="color: #7c3aed;">Monthly transactions</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Main Table -->
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 3%;">#</th>
                <th style="width: 12%;">Transaction Number</th>
                <th style="width: 20%;">Parties</th>
                <th style="width: 10%;">Type</th>
                <th style="width: 12%;">Amount</th>
                <th style="width: 10%;">Payment Method</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 10%;">Date</th>
                <th style="width: 13%;">Reference</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $transaction)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $transaction->transaction_number }}</strong>
                    </td>
                    <td>
                        <strong>Buyer:</strong> {{ $transaction->buyer->name ?? 'N/A' }}
                        <br><strong>Vendor:</strong> {{ $transaction->vendor->name ?? 'N/A' }}
                    </td>
                    <td>
                        @php
                            $typeClasses = [
                                'payment' => 'type-payment',
                                'refund' => 'type-refund',
                                'commission' => 'type-commission',
                                'payout' => 'type-payout',
                                'subscription' => 'type-subscription',
                            ];
                            $typeClass = $typeClasses[$transaction->type] ?? 'type-payment';
                        @endphp
                        <span class="status-badge {{ $typeClass }}">
                            {{ ucfirst($transaction->type) }}
                        </span>
                    </td>
                    <td>
                        <strong>{{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}</strong>
                    </td>
                    <td>
                        {{ ucfirst(str_replace('_', ' ', $transaction->payment_method ?? 'N/A')) }}
                    </td>
                    <td>
                        @php
                            $statusClasses = [
                                'pending' => 'status-pending',
                                'processing' => 'status-processing',
                                'completed' => 'status-completed',
                                'failed' => 'status-failed',
                                'refunded' => 'status-refunded',
                            ];
                            $statusClass = $statusClasses[$transaction->status] ?? 'status-pending';
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </td>
                    <td>
                        {{ $transaction->created_at->format('M d, Y') }}
                        <br>
                        <small>{{ $transaction->created_at->format('h:i A') }}</small>
                    </td>
                    <td>
                        @if($transaction->payment_reference)
                            {{ $transaction->payment_reference }}
                        @else
                            <small>No reference</small>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px;">
                        No transactions found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Transactions Management System - Administrator Report</p>
        <p>Page 1 of 1 | Total Records: {{ $transactions->count() }} | Report ID: TXN-{{ now()->format('Ymd-His') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

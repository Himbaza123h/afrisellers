<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Commission Management Report</title>
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

        .status-approved {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-paid {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-cancelled {
            background-color: #f3f4f6;
            color: #374151;
        }

        .type-vendor_sale {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .type-referral {
            background-color: #ede9fe;
            color: #5b21b6;
        }

        .type-regional_admin {
            background-color: #e0e7ff;
            color: #3730a3;
        }

        .type-platform_fee {
            background-color: #fce7f3;
            color: #9d174d;
        }

        .type-affiliate {
            background-color: #ecfccb;
            color: #365314;
        }

        .type-bonus {
            background-color: #fef3c7;
            color: #92400e;
        }

        .payment-status-unpaid {
            background-color: #fef3c7;
            color: #92400e;
        }

        .payment-status-processing {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .payment-status-paid {
            background-color: #d1fae5;
            color: #065f46;
        }

        .payment-status-failed {
            background-color: #fee2e2;
            color: #991b1b;
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
        <h1>COMMISSION MANAGEMENT REPORT</h1>
        <p>Complete Overview of All Commission Transactions</p>
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
                    <div class="stats-label">Total Commissions</div>
                    <div class="stats-value">{{ number_format($stats['total']) }}</div>
                </td>
                <td>
                    <div class="stats-label">Pending</div>
                    <div class="stats-value">{{ number_format($stats['pending']) }}</div>
                    <div class="stats-subtext" style="color: #92400e;">Awaiting Approval</div>
                </td>
                <td>
                    <div class="stats-label">Approved</div>
                    <div class="stats-value">{{ number_format($stats['approved']) }}</div>
                    <div class="stats-subtext" style="color: #1e40af;">Ready to Pay</div>
                </td>
                <td>
                    <div class="stats-label">Paid</div>
                    <div class="stats-value">{{ number_format($stats['paid']) }}</div>
                    <div class="stats-subtext" style="color: #065f46;">Completed</div>
                </td>
            </tr>
        </table>

        <!-- Financial Row -->
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">Total Paid</div>
                    <div class="stats-value">${{ number_format($stats['total_amount'], 2) }}</div>
                    <div class="stats-subtext" style="color: #059669;">Completed</div>
                </td>
                <td>
                    <div class="stats-label">Pending Amount</div>
                    <div class="stats-value">${{ number_format($stats['pending_amount'], 2) }}</div>
                    <div class="stats-subtext" style="color: #f59e0b;">Awaiting</div>
                </td>
                <td>
                    <div class="stats-label">This Month</div>
                    <div class="stats-value">{{ number_format($stats['this_month']) }}</div>
                    <div class="stats-subtext" style="color: #7c3aed;">Count</div>
                </td>
                <td>
                    <div class="stats-label">Month Total</div>
                    <div class="stats-value">${{ number_format($stats['this_month_amount'], 2) }}</div>
                    <div class="stats-subtext" style="color: #0ea5e9;">Monthly</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Main Table -->
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 3%;">#</th>
                <th style="width: 15%;">User</th>
                <th style="width: 12%;">Transaction</th>
                <th style="width: 10%;">Commission Type</th>
                <th style="width: 12%;">Amount</th>
                <th style="width: 10%;">Rate</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 10%;">Payment Status</th>
                <th style="width: 10%;">Created</th>
                <th style="width: 8%;">Paid Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($commissions as $index => $commission)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $commission->user->name ?? 'N/A' }}</strong>
                        <br><small>{{ $commission->user->email ?? 'N/A' }}</small>
                    </td>
                    <td>
                        <strong>{{ $commission->transaction->transaction_number ?? 'N/A' }}</strong>
                        <br><small>${{ number_format($commission->transaction_amount, 2) }}</small>
                    </td>
                    <td>
                        @php
                            $typeClasses = [
                                'vendor_sale' => 'type-vendor_sale',
                                'referral' => 'type-referral',
                                'regional_admin' => 'type-regional_admin',
                                'platform_fee' => 'type-platform_fee',
                                'affiliate' => 'type-affiliate',
                                'bonus' => 'type-bonus',
                            ];
                            $typeClass = $typeClasses[$commission->commission_type] ?? 'type-vendor_sale';
                        @endphp
                        <span class="status-badge {{ $typeClass }}">
                            {{ ucwords(str_replace('_', ' ', $commission->commission_type)) }}
                        </span>
                    </td>
                    <td>
                        <strong>{{ $commission->currency }} {{ number_format($commission->commission_amount, 2) }}</strong>
                    </td>
                    <td class="text-center">
                        <strong>{{ $commission->commission_rate }}%</strong>
                    </td>
                    <td>
                        @php
                            $statusClasses = [
                                'pending' => 'status-pending',
                                'approved' => 'status-approved',
                                'paid' => 'status-paid',
                                'cancelled' => 'status-cancelled',
                            ];
                            $statusClass = $statusClasses[$commission->status] ?? 'status-pending';
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ ucfirst($commission->status) }}
                        </span>
                    </td>
                    <td>
                        @php
                            $paymentClasses = [
                                'unpaid' => 'payment-status-unpaid',
                                'processing' => 'payment-status-processing',
                                'paid' => 'payment-status-paid',
                                'failed' => 'payment-status-failed',
                            ];
                            $paymentClass = $paymentClasses[$commission->payment_status] ?? 'payment-status-unpaid';
                        @endphp
                        <span class="status-badge {{ $paymentClass }}">
                            {{ ucfirst($commission->payment_status) }}
                        </span>
                    </td>
                    <td>
                        {{ $commission->created_at->format('M d, Y') }}
                        <br><small>{{ $commission->created_at->format('h:i A') }}</small>
                    </td>
                    <td class="text-center">
                        @if($commission->paid_at)
                            <strong>{{ $commission->paid_at->format('M d') }}</strong>
                            <br><small>{{ $commission->paid_at->format('Y') }}</small>
                        @else
                            <small>-</small>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 20px;">
                        No commissions found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Commission Management System - Administrator Report</p>
        <p>Page 1 of 1 | Total Records: {{ $commissions->count() }} | Report ID: COM-{{ now()->format('Ymd-His') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Orders Report - {{ $country->name }} - {{ now()->format('M d, Y') }}</title>
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

        .badge-pending {
            background-color: #fed7aa;
            color: #9a3412;
        }

        .badge-confirmed {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-processing {
            background-color: #e9d5ff;
            color: #6b21a8;
        }

        .badge-shipped {
            background-color: #c7d2fe;
            color: #3730a3;
        }

        .badge-delivered {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-paid {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-payment-pending {
            background-color: #fed7aa;
            color: #9a3412;
        }

        .badge-failed {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-refunded {
            background-color: #e9d5ff;
            color: #6b21a8;
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
        <h1>ORDERS REPORT - {{ strtoupper($country->name) }}</h1>
        <p>Country Admin: {{ auth()->user()->name }} ({{ auth()->user()->email }})</p>
        <p>Generated on: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Statistics -->
    <div class="stats-section">
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">Total Orders</div>
                    <div class="stats-value">{{ number_format($stats['total']) }}</div>
                </td>
                <td>
                    <div class="stats-label">Pending</div>
                    <div class="stats-value">{{ number_format($stats['pending']) }}</div>
                    <div class="stats-subtext">{{ $stats['pending_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">Processing</div>
                    <div class="stats-value">{{ number_format($stats['processing']) }}</div>
                    <div class="stats-subtext">{{ $stats['processing_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">Completed</div>
                    <div class="stats-value">{{ number_format($stats['completed']) }}</div>
                    <div class="stats-subtext">{{ $stats['completed_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">Total Revenue</div>
                    <div class="stats-value">${{ number_format($stats['total_revenue'], 2) }}</div>
                </td>
                <td>
                    <div class="stats-label">Avg Order Value</div>
                    <div class="stats-value">${{ number_format($stats['avg_order_value'], 2) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Orders List -->
    <div class="section-title">Orders List ({{ $orders->count() }} records)</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 12%;">Order Number</th>
                <th style="width: 18%;">Customer</th>
                <th style="width: 18%;">Vendor</th>
                <th style="width: 8%;" class="text-center">Items</th>
                <th style="width: 10%;" class="text-right">Total</th>
                <th style="width: 12%;">Payment Status</th>
                <th style="width: 12%;">Order Status</th>
                <th style="width: 10%;">Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td><strong>#{{ $order->order_number }}</strong></td>
                    <td>
                        <strong>{{ $order->buyer->name ?? 'N/A' }}</strong><br>
                        <small>{{ $order->buyer->email ?? 'N/A' }}</small>
                    </td>
                    <td>
                        <strong>{{ $order->vendor->name ?? 'N/A' }}</strong><br>
                        <small>{{ $order->vendor->email ?? 'N/A' }}</small>
                    </td>
                    <td class="text-center">{{ $order->items->count() }}</td>
                    <td class="text-right"><strong>{{ $order->formatted_total }}</strong></td>
                    <td>
                        @php
                            $paymentClass = match($order->payment_status ?? 'pending') {
                                'paid' => 'badge-paid',
                                'pending' => 'badge-payment-pending',
                                'failed' => 'badge-failed',
                                'refunded' => 'badge-refunded',
                                default => 'badge-payment-pending'
                            };
                            $paymentLabel = ucfirst($order->payment_status ?? 'pending');
                        @endphp
                        <span class="badge {{ $paymentClass }}">{{ $paymentLabel }}</span>
                    </td>
                    <td>
                        @php
                            $statusClass = match($order->status) {
                                'pending' => 'badge-pending',
                                'confirmed' => 'badge-confirmed',
                                'processing' => 'badge-processing',
                                'shipped' => 'badge-shipped',
                                'delivered' => 'badge-delivered',
                                'cancelled' => 'badge-cancelled',
                                default => 'badge-pending'
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ ucfirst($order->status) }}</span>
                    </td>
                    <td>
                        {{ $order->created_at->format('M d, Y') }}<br>
                        <small>{{ $order->created_at->format('h:i A') }}</small>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px;">
                        No orders found
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
                <td>{{ $orders->count() }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Country</td>
                <td>{{ $country->name }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Generated By</td>
                <td>{{ auth()->user()->name }} ({{ auth()->user()->email }})</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Status Distribution</td>
                <td>
                    Pending: {{ $stats['pending'] }} ({{ $stats['pending_percentage'] }}%) |
                    Processing: {{ $stats['processing'] }} ({{ $stats['processing_percentage'] }}%) |
                    Completed: {{ $stats['completed'] }} ({{ $stats['completed_percentage'] }}%) |
                    Cancelled: {{ $stats['cancelled'] }}
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Revenue Overview</td>
                <td>
                    Total Revenue: ${{ number_format($stats['total_revenue'], 2) }} |
                    Average Order Value: ${{ number_format($stats['avg_order_value'], 2) }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Country Admin Order Management System</p>
        <p>Page 1 of 1 | Report ID: ORD-{{ now()->format('Ymd-His') }} | Country: {{ $country->name }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

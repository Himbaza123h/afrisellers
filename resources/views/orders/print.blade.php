<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Orders Management Report</title>
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

        .status-confirmed {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-processing {
            background-color: #ede9fe;
            color: #5b21b6;
        }

        .status-shipped {
            background-color: #e0e7ff;
            color: #3730a3;
        }

        .status-delivered {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-paid {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-pending {
            background-color: #fef3c7;
            color: #92400e;
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
        <h1>ORDERS MANAGEMENT REPORT</h1>
        <p>Complete Overview of All Customer Orders</p>
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
                    <div class="stats-label">Total Orders</div>
                    <div class="stats-value">{{ $stats['total'] }}</div>
                </td>
                <td>
                    <div class="stats-label">Pending Orders</div>
                    <div class="stats-value">{{ $stats['pending'] }}</div>
                    <div class="stats-subtext" style="color: #92400e;">{{ $stats['pending_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">Delivered Orders</div>
                    <div class="stats-value">{{ $stats['delivered'] }}</div>
                    <div class="stats-subtext" style="color: #065f46;">{{ $stats['delivered_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">Paid Orders</div>
                    <div class="stats-value">{{ $stats['paid'] }}</div>
                    <div class="stats-subtext" style="color: #059669;">{{ $stats['paid_percentage'] }}% paid</div>
                </td>
            </tr>
        </table>

        <!-- Revenue Row -->
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">Total Revenue</div>
                    <div class="stats-value">${{ number_format($stats['total_revenue'], 2) }}</div>
                    <div class="stats-subtext" style="color: #0d9488;">From delivered orders</div>
                </td>
                <td>
                    <div class="stats-label">Average Order Value</div>
                    <div class="stats-value">${{ number_format($stats['avg_order_value'], 2) }}</div>
                    <div class="stats-subtext" style="color: #7c3aed;">Per delivered order</div>
                </td>
                <td>
                    <div class="stats-label">Today's Orders</div>
                    <div class="stats-value">{{ $stats['today'] }}</div>
                    <div class="stats-subtext" style="color: #f59e0b;">New today</div>
                </td>
                <td>
                    <div class="stats-label">This Week</div>
                    <div class="stats-value">{{ $stats['this_week'] }}</div>
                    <div class="stats-subtext" style="color: #0ea5e9;">Weekly activity</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Main Table -->
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 3%;">#</th>
                <th style="width: 12%;">Order Number</th>
                <th style="width: 20%;">Customer</th>
                <th style="width: 15%;">Vendor</th>
                <th style="width: 8%;" class="text-center">Items</th>
                <th style="width: 12%;">Total Amount</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 10%;">Payment</th>
                <th style="width: 10%;">Order Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $index => $order)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>#{{ $order->order_number }}</strong>
                    </td>
                    <td>
                        <strong>{{ $order->buyer->name ?? 'Unknown' }}</strong>
                        @if($order->buyer && $order->buyer->email)
                            <br><small>{{ $order->buyer->email }}</small>
                        @endif
                    </td>
                    <td>
                        {{ $order->vendor->name ?? 'Unknown' }}
                    </td>
                    <td class="text-center">{{ $order->items->count() }}</td>
                    <td>
                        <strong>${{ number_format($order->total, 2) }}</strong>
                        <br><small>{{ $order->currency }}</small>
                    </td>
                    <td>
                        @php
                            $statusClasses = [
                                'pending' => 'status-pending',
                                'confirmed' => 'status-confirmed',
                                'processing' => 'status-processing',
                                'shipped' => 'status-shipped',
                                'delivered' => 'status-delivered',
                                'cancelled' => 'status-cancelled',
                            ];
                            $statusClass = $statusClasses[$order->status] ?? 'status-pending';
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td>
                        @if($order->payment_status == 'paid')
                            <span class="badge-paid status-badge">Paid</span>
                        @else
                            <span class="badge-pending status-badge">Pending</span>
                        @endif
                    </td>
                    <td>
                        {{ $order->created_at->format('M d, Y') }}
                        <br>
                        <small>{{ $order->created_at->format('h:i A') }}</small>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px;">
                        No orders found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Orders Management System - Administrator Report</p>
        <p>Page 1 of 1 | Total Records: {{ $orders->count() }} | Report ID: ORD-{{ now()->format('Ymd-His') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

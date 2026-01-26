<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ ucfirst($reportType) }} Report - {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</title>
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

        .report-date {
            text-align: right;
            font-size: 10px;
            margin-bottom: 10px;
            color: #666;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #000;
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
        <h1>{{ strtoupper($reportType) }} SALES REPORT</h1>
        <p>{{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</p>
        <p>Generated on: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Statistics Section -->
    <div class="stats-section">
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">Total Revenue</div>
                    <div class="stats-value">${{ number_format($stats['total_revenue'], 2) }}</div>
                </td>
                <td>
                    <div class="stats-label">Total Orders</div>
                    <div class="stats-value">{{ number_format($stats['total_orders']) }}</div>
                    <div class="stats-subtext">Avg: ${{ number_format($stats['average_order_value'], 2) }}</div>
                </td>
                <td>
                    <div class="stats-label">Total Vendors</div>
                    <div class="stats-value">{{ number_format($stats['total_vendors']) }}</div>
                    <div class="stats-subtext">{{ number_format($stats['active_vendors']) }} active</div>
                </td>
                <td>
                    <div class="stats-label">Total Customers</div>
                    <div class="stats-value">{{ number_format($stats['total_customers']) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Main Report Data -->
    <div class="section-title">{{ ucfirst($reportType) }} Report Data</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 20%;">Period</th>
                <th style="width: 15%;" class="text-right">Revenue</th>
                @if(!in_array($reportType, ['product', 'vendor', 'customer']))
                    <th style="width: 10%;" class="text-right">Orders</th>
                    <th style="width: 10%;" class="text-right">Transactions</th>
                @else
                    @if($reportType == 'product')
                        <th style="width: 10%;" class="text-right">Quantity Sold</th>
                    @endif
                    <th style="width: 10%;" class="text-right">Orders</th>
                    @if(in_array($reportType, ['vendor', 'customer']))
                        <th style="width: 12%;" class="text-right">Avg Order Value</th>
                    @endif
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($reportData as $data)
                <tr>
                    <td>{{ $data->period_label }}</td>
                    <td class="text-right">${{ number_format($data->revenue, 2) }}</td>
                    @if(!in_array($reportType, ['product', 'vendor', 'customer']))
                        <td class="text-right">{{ $data->orders ?? 0 }}</td>
                        <td class="text-right">{{ $data->transaction_count }}</td>
                    @else
                        @if($reportType == 'product')
                            <td class="text-right">{{ $data->quantity_sold ?? 0 }}</td>
                        @endif
                        <td class="text-right">{{ $data->order_count }}</td>
                        @if(in_array($reportType, ['vendor', 'customer']))
                            <td class="text-right">${{ number_format($data->average_order_value ?? 0, 2) }}</td>
                        @endif
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center" style="padding: 10px;">No data available for this period</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Order Status Breakdown -->
    <div class="section-title">Order Status Breakdown</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 30%;">Status</th>
                <th style="width: 15%;" class="text-right">Count</th>
                <th style="width: 15%;" class="text-right">Percentage</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalOrders = array_sum($orderStatusBreakdown);
            @endphp
            @foreach($orderStatusBreakdown as $status => $count)
                <tr>
                    <td>{{ ucfirst($status) }}</td>
                    <td class="text-right">{{ $count }}</td>
                    <td class="text-right">{{ $totalOrders > 0 ? number_format(($count / $totalOrders) * 100, 1) : 0 }}%</td>
                </tr>
            @endforeach
            @if($totalOrders > 0)
                <tr>
                    <td style="font-weight: bold;">Total</td>
                    <td class="text-right" style="font-weight: bold;">{{ $totalOrders }}</td>
                    <td class="text-right" style="font-weight: bold;">100%</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Sales by Payment Method -->
    <div class="section-title">Sales by Payment Method</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 30%;">Payment Method</th>
                <th style="width: 15%;" class="text-right">Transactions</th>
                <th style="width: 20%;" class="text-right">Total Amount</th>
                <th style="width: 15%;" class="text-right">Percentage</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalPaymentAmount = $salesByPaymentMethod->sum('total');
            @endphp
            @forelse($salesByPaymentMethod as $payment)
                <tr>
                    <td>{{ ucfirst($payment->payment_method ?? 'Unknown') }}</td>
                    <td class="text-right">{{ $payment->count }}</td>
                    <td class="text-right">${{ number_format($payment->total, 2) }}</td>
                    <td class="text-right">{{ $totalPaymentAmount > 0 ? number_format(($payment->total / $totalPaymentAmount) * 100, 1) : 0 }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center" style="padding: 10px;">No payment data available</td>
                </tr>
            @endforelse
            @if($totalPaymentAmount > 0)
                <tr>
                    <td style="font-weight: bold;">Total</td>
                    <td class="text-right" style="font-weight: bold;">{{ $salesByPaymentMethod->sum('count') }}</td>
                    <td class="text-right" style="font-weight: bold;">${{ number_format($totalPaymentAmount, 2) }}</td>
                    <td class="text-right" style="font-weight: bold;">100%</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Top Performers Section -->
    <div style="page-break-before: always;">
        <div class="section-title">Top Performers</div>

        <!-- Top Products -->
        <div style="margin-bottom: 20px;">
            <h4 style="font-size: 11px; font-weight: bold; margin: 10px 0 5px 0;">Top Products</h4>
            <table class="main-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 35%;">Product Name</th>
                        <th style="width: 15%;" class="text-right">Quantity Sold</th>
                        <th style="width: 20%;" class="text-right">Total Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topProducts as $index => $product)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $product->name }}</td>
                            <td class="text-right">{{ $product->total_quantity }}</td>
                            <td class="text-right">${{ number_format($product->total_revenue, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center" style="padding: 10px;">No product data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Top Vendors -->
        <div style="margin-bottom: 20px;">
            <h4 style="font-size: 11px; font-weight: bold; margin: 10px 0 5px 0;">Top Vendors</h4>
            <table class="main-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 35%;">Vendor Name</th>
                        <th style="width: 15%;" class="text-right">Orders</th>
                        <th style="width: 20%;" class="text-right">Total Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topVendors as $index => $vendor)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $vendor->vendor->name ?? 'Unknown Vendor' }}</td>
                            <td class="text-right">{{ $vendor->order_count }}</td>
                            <td class="text-right">${{ number_format($vendor->total_revenue, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center" style="padding: 10px;">No vendor data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Top Customers -->
        <div>
            <h4 style="font-size: 11px; font-weight: bold; margin: 10px 0 5px 0;">Top Customers</h4>
            <table class="main-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 35%;">Customer Name</th>
                        <th style="width: 15%;" class="text-right">Orders</th>
                        <th style="width: 20%;" class="text-right">Total Spent</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topCustomers as $index => $customer)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $customer->buyer->name ?? 'Unknown Customer' }}</td>
                            <td class="text-right">{{ $customer->order_count }}</td>
                            <td class="text-right">${{ number_format($customer->total_spent, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center" style="padding: 10px;">No customer data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Platform Reports System - Administrator Report</p>
        <p>Report Type: {{ ucfirst($reportType) }} | Period: {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</p>
        <p>Report ID: REP-{{ now()->format('Ymd-His') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sales Report - {{ now()->format('M d, Y') }}</title>
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
        <h1>SALES REPORT - {{ strtoupper($reportType) }}</h1>
        <p>Detailed Sales Analysis & Reporting</p>
        <p>Generated on: {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Period: {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</p>
    </div>

    <!-- Statistics Section -->
    <div class="stats-section">
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">Total Sales</div>
                    <div class="stats-value">${{ number_format($summary['total_sales'], 2) }}</div>
                </td>
                <td>
                    <div class="stats-label">Total Orders</div>
                    <div class="stats-value">{{ number_format($summary['total_orders']) }}</div>
                </td>
                <td>
                    <div class="stats-label">Avg Order Value</div>
                    <div class="stats-value">${{ number_format($summary['average_order_value'], 2) }}</div>
                </td>
                <td>
                    <div class="stats-label">Items Sold</div>
                    <div class="stats-value">{{ number_format($summary['total_items_sold']) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Sales Report Table -->
    <div class="section-title">{{ ucfirst($reportType) }} Sales Report</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 25%;">
                    @if($reportType == 'product')
                        Product
                    @elseif($reportType == 'customer')
                        Customer
                    @else
                        Period
                    @endif
                </th>
                @if(in_array($reportType, ['daily', 'weekly', 'monthly']))
                    <th style="width: 15%;" class="text-right">Orders</th>
                    <th style="width: 15%;" class="text-right">Transactions</th>
                @endif
                @if($reportType == 'product')
                    <th style="width: 15%;" class="text-right">Units Sold</th>
                    <th style="width: 15%;" class="text-right">Orders</th>
                @endif
                @if($reportType == 'customer')
                    <th style="width: 15%;" class="text-right">Orders</th>
                    <th style="width: 15%;" class="text-right">Avg Order</th>
                @endif
                <th style="width: 15%;" class="text-right">Revenue</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData as $item)
                <tr>
                    <td>{{ $item->period_label ?? 'N/A' }}</td>
                    @if(in_array($reportType, ['daily', 'weekly', 'monthly']))
                        <td class="text-right">{{ $item->orders ?? 0 }}</td>
                        <td class="text-right">{{ $item->transaction_count }}</td>
                    @endif
                    @if($reportType == 'product')
                        <td class="text-right">{{ number_format($item->quantity_sold) }}</td>
                        <td class="text-right">{{ $item->order_count }}</td>
                    @endif
                    @if($reportType == 'customer')
                        <td class="text-right">{{ $item->order_count }}</td>
                        <td class="text-right">${{ number_format($item->average_order_value, 2) }}</td>
                    @endif
                    <td class="text-right"><strong>${{ number_format($item->revenue, 2) }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px;">
                        No data available for this period
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($reportData->isNotEmpty())
            <tfoot>
                <tr class="highlight">
                    <td><strong>TOTAL</strong></td>
                    @if(in_array($reportType, ['daily', 'weekly', 'monthly']))
                        <td class="text-right"><strong>{{ $reportData->sum('orders') }}</strong></td>
                        <td class="text-right"><strong>{{ $reportData->sum('transaction_count') }}</strong></td>
                    @endif
                    @if($reportType == 'product')
                        <td class="text-right"><strong>{{ number_format($reportData->sum('quantity_sold')) }}</strong></td>
                        <td class="text-right"><strong>{{ $reportData->sum('order_count') }}</strong></td>
                    @endif
                    @if($reportType == 'customer')
                        <td class="text-right"><strong>{{ $reportData->sum('order_count') }}</strong></td>
                        <td class="text-right"><strong>-</strong></td>
                    @endif
                    <td class="text-right"><strong>${{ number_format($reportData->sum('revenue'), 2) }}</strong></td>
                </tr>
            </tfoot>
        @endif
    </table>

    <!-- Sales by Payment Method -->
    @if($salesByPaymentMethod->count() > 0)
        <div class="section-title">Sales by Payment Method</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 30%;">Payment Method</th>
                    <th style="width: 15%;" class="text-right">Transactions</th>
                    <th style="width: 15%;" class="text-right">Amount</th>
                    <th style="width: 10%;" class="text-right">% of Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($salesByPaymentMethod as $method)
                    <tr>
                        <td>{{ ucfirst(str_replace('_', ' ', $method->payment_method ?? 'Unknown')) }}</td>
                        <td class="text-right">{{ $method->count }}</td>
                        <td class="text-right">${{ number_format($method->total, 2) }}</td>
                        <td class="text-right">
                            {{ $summary['total_sales'] > 0 ? number_format(($method->total / $summary['total_sales']) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Sales by Order Status -->
    @if($salesByStatus->count() > 0)
        <div class="section-title">Sales by Order Status</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 30%;">Order Status</th>
                    <th style="width: 15%;" class="text-right">Orders</th>
                    <th style="width: 15%;" class="text-right">Amount</th>
                    <th style="width: 10%;" class="text-right">% of Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($salesByStatus as $status)
                    <tr>
                        <td>{{ ucfirst($status->status) }}</td>
                        <td class="text-right">{{ $status->count }}</td>
                        <td class="text-right">${{ number_format($status->total, 2) }}</td>
                        <td class="text-right">
                            {{ $summary['total_sales'] > 0 ? number_format(($status->total / $summary['total_sales']) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Top Products -->
    @if($topProducts->count() > 0)
        <div class="section-title">Top Products</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 40%;">Product Name</th>
                    <th style="width: 15%;" class="text-right">Units Sold</th>
                    <th style="width: 15%;" class="text-right">Revenue</th>
                    <th style="width: 10%;" class="text-right">% of Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topProducts as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td class="text-right">{{ number_format($product->total_quantity) }}</td>
                        <td class="text-right">${{ number_format($product->total_revenue, 2) }}</td>
                        <td class="text-right">
                            {{ $summary['total_sales'] > 0 ? number_format(($product->total_revenue / $summary['total_sales']) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Summary -->
    <div class="section-title">Report Summary</div>
    <table class="main-table">
        <tbody>
            <tr>
                <td style="width: 25%; font-weight: bold;">Report Type</td>
                <td>{{ ucfirst($reportType) }} Report</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Date Range</td>
                <td>{{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Days Covered</td>
                <td>{{ $startDate->diffInDays($endDate) + 1 }} days</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Daily Average Sales</td>
                <td>
                    @php
                        $daysCount = $startDate->diffInDays($endDate) + 1;
                        $dailyAverage = $daysCount > 0 ? $summary['total_sales'] / $daysCount : 0;
                    @endphp
                    <strong>${{ number_format($dailyAverage, 2) }}</strong>
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Report ID</td>
                <td>REP-{{ strtoupper($reportType) }}-{{ now()->format('Ymd-His') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Sales Reporting System - Vendor Report</p>
        <p>Page 1 of 1 | Total Records: {{ $reportData->count() }} | Report ID: REP-{{ now()->format('Ymd-His') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

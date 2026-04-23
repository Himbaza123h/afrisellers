<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Performance Report - {{ now()->format('M d, Y') }}</title>
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

        .badge-up {
            background-color: #d1fae5;
            color: #065f46;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-down {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-neutral {
            background-color: #f3f4f6;
            color: #374151;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
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
        <h1>PERFORMANCE METRICS REPORT</h1>
        <p>Business Performance & Growth Analysis</p>
        <p>Generated on: {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Period: {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</p>
    </div>

    <!-- Key Metrics Section -->
    <div class="stats-section">
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">Total Revenue</div>
                    <div class="stats-value">${{ number_format($metrics['total_revenue'], 2) }}</div>
                    <div class="stats-subtext">
                        @if($comparison['revenue_change'] > 0)
                            <span class="badge-up">↑ {{ number_format(abs($comparison['revenue_change']), 1) }}%</span>
                        @elseif($comparison['revenue_change'] < 0)
                            <span class="badge-down">↓ {{ number_format(abs($comparison['revenue_change']), 1) }}%</span>
                        @else
                            <span class="badge-neutral">0%</span>
                        @endif
                        vs previous period
                    </div>
                </td>
                <td>
                    <div class="stats-label">Total Orders</div>
                    <div class="stats-value">{{ number_format($metrics['total_orders']) }}</div>
                    <div class="stats-subtext">
                        @if($comparison['orders_change'] > 0)
                            <span class="badge-up">↑ {{ number_format(abs($comparison['orders_change']), 1) }}%</span>
                        @elseif($comparison['orders_change'] < 0)
                            <span class="badge-down">↓ {{ number_format(abs($comparison['orders_change']), 1) }}%</span>
                        @else
                            <span class="badge-neutral">0%</span>
                        @endif
                        vs previous period
                    </div>
                </td>
                <td>
                    <div class="stats-label">Conversion Rate</div>
                    <div class="stats-value">{{ number_format($metrics['conversion_rate'], 1) }}%</div>
                    <div class="stats-subtext">Orders completed</div>
                </td>
                <td>
                    <div class="stats-label">Avg Order Value</div>
                    <div class="stats-value">${{ number_format($metrics['average_order_value'], 2) }}</div>
                    <div class="stats-subtext">Per transaction</div>
                </td>
                <td>
                    <div class="stats-label">Repeat Customers</div>
                    <div class="stats-value">{{ number_format($metrics['repeat_customers']) }}</div>
                    <div class="stats-subtext">{{ number_format($metrics['repeat_customer_rate'], 1) }}% of total</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Performance Metrics -->
    <div class="section-title">Performance Metrics</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 25%;">Metric</th>
                <th style="width: 20%;" class="text-right">Value</th>
                <th style="width: 25%;" class="text-right">Rate</th>
                <th style="width: 30%;">Description</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Orders</td>
                <td class="text-right">{{ number_format($metrics['total_orders']) }}</td>
                <td class="text-right">100%</td>
                <td>All orders placed during period</td>
            </tr>
            <tr>
                <td>Completed Orders</td>
                <td class="text-right">{{ number_format($metrics['completed_orders']) }}</td>
                <td class="text-right">{{ number_format($metrics['conversion_rate'], 1) }}%</td>
                <td>Successfully delivered orders</td>
            </tr>
            <tr>
                <td>Cancelled Orders</td>
                <td class="text-right">{{ number_format($metrics['cancelled_orders']) }}</td>
                <td class="text-right">{{ number_format($metrics['cancellation_rate'], 1) }}%</td>
                <td>Orders that were cancelled</td>
            </tr>
            <tr>
                <td>Total Customers</td>
                <td class="text-right">{{ number_format($metrics['total_customers']) }}</td>
                <td class="text-right">100%</td>
                <td>Unique customers who placed orders</td>
            </tr>
            <tr>
                <td>Active Products</td>
                <td class="text-right">{{ number_format($metrics['active_products']) }}</td>
                <td class="text-right">
                    {{ $metrics['total_products'] > 0 ? number_format(($metrics['active_products'] / $metrics['total_products']) * 100, 1) : 0 }}%
                </td>
                <td>Active products of total {{ $metrics['total_products'] }} products</td>
            </tr>
        </tbody>
    </table>

    <!-- Top Performing Products -->
    @if($productPerformance->count() > 0)
        <div class="section-title">Top Performing Products</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 30%;">Product</th>
                    <th style="width: 15%;" class="text-right">Units Sold</th>
                    <th style="width: 15%;" class="text-right">Orders</th>
                    <th style="width: 15%;" class="text-right">Avg Price</th>
                    <th style="width: 15%;" class="text-right">Revenue</th>
                    <th style="width: 10%;" class="text-right">% of Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productPerformance as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td class="text-right">{{ number_format($product->units_sold) }}</td>
                        <td class="text-right">{{ $product->order_count }}</td>
                        <td class="text-right">${{ number_format($product->average_price, 2) }}</td>
                        <td class="text-right"><strong>${{ number_format($product->revenue, 2) }}</strong></td>
                        <td class="text-right">
                            {{ $metrics['total_revenue'] > 0 ? number_format(($product->revenue / $metrics['total_revenue']) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Order Status Distribution -->
    @if($orderStatusDistribution->count() > 0)
        <div class="section-title">Order Status Distribution</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Status</th>
                    <th style="width: 15%;" class="text-right">Orders</th>
                    <th style="width: 15%;" class="text-right">Amount</th>
                    <th style="width: 15%;" class="text-right">% of Orders</th>
                    <th style="width: 15%;" class="text-right">% of Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orderStatusDistribution as $status)
                    <tr>
                        <td>{{ ucfirst($status->status) }}</td>
                        <td class="text-right">{{ $status->count }}</td>
                        <td class="text-right">${{ number_format($status->total, 2) }}</td>
                        <td class="text-right">
                            {{ $metrics['total_orders'] > 0 ? number_format(($status->count / $metrics['total_orders']) * 100, 1) : 0 }}%
                        </td>
                        <td class="text-right">
                            {{ $metrics['total_revenue'] > 0 ? number_format(($status->total / $metrics['total_revenue']) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Performance Trends Summary -->
    <div class="section-title">Performance Trends</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 25%;">Period</th>
                <th style="width: 20%;" class="text-right">Orders Trend</th>
                <th style="width: 20%;" class="text-right">Revenue Trend</th>
                <th style="width: 35%;">Analysis</th>
            </tr>
        </thead>
        <tbody>
            @if($trends['labels'])
                @php
                    $firstOrders = reset($trends['orders']);
                    $lastOrders = end($trends['orders']);
                    $ordersGrowth = $firstOrders > 0 ? (($lastOrders - $firstOrders) / $firstOrders) * 100 : 0;

                    $firstRevenue = reset($trends['revenue']);
                    $lastRevenue = end($trends['revenue']);
                    $revenueGrowth = $firstRevenue > 0 ? (($lastRevenue - $firstRevenue) / $firstRevenue) * 100 : 0;
                @endphp
                <tr>
                    <td>{{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }}</td>
                    <td class="text-right">
                        @if($ordersGrowth > 0)
                            <span class="badge-up">↑ {{ number_format($ordersGrowth, 1) }}%</span>
                        @elseif($ordersGrowth < 0)
                            <span class="badge-down">↓ {{ number_format(abs($ordersGrowth), 1) }}%</span>
                        @else
                            <span class="badge-neutral">0%</span>
                        @endif
                    </td>
                    <td class="text-right">
                        @if($revenueGrowth > 0)
                            <span class="badge-up">↑ {{ number_format($revenueGrowth, 1) }}%</span>
                        @elseif($revenueGrowth < 0)
                            <span class="badge-down">↓ {{ number_format(abs($revenueGrowth), 1) }}%</span>
                        @else
                            <span class="badge-neutral">0%</span>
                        @endif
                    </td>
                    <td>
                        @if($ordersGrowth > 0 && $revenueGrowth > 0)
                            Strong growth in both orders and revenue
                        @elseif($ordersGrowth > 0 && $revenueGrowth <= 0)
                            More orders but lower revenue per order
                        @elseif($ordersGrowth <= 0 && $revenueGrowth > 0)
                            Fewer orders but higher value per order
                        @else
                            Performance stable with minimal changes
                        @endif
                    </td>
                </tr>
            @else
                <tr>
                    <td colspan="4" class="text-center">No trend data available</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Report Summary -->
    <div class="section-title">Report Summary</div>
    <table class="main-table">
        <tbody>
            <tr>
                <td style="width: 25%; font-weight: bold;">Date Range</td>
                <td>{{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Days Covered</td>
                <td>{{ $startDate->diffInDays($endDate) + 1 }} days</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Daily Average Revenue</td>
                <td>
                    @php
                        $daysCount = $startDate->diffInDays($endDate) + 1;
                        $dailyAverage = $daysCount > 0 ? $metrics['total_revenue'] / $daysCount : 0;
                    @endphp
                    <strong>${{ number_format($dailyAverage, 2) }}</strong>
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Daily Average Orders</td>
                <td>
                    @php
                        $dailyOrders = $daysCount > 0 ? $metrics['total_orders'] / $daysCount : 0;
                    @endphp
                    <strong>{{ number_format($dailyOrders, 1) }}</strong>
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Report ID</td>
                <td>PERF-{{ now()->format('Ymd-His') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Performance Metrics System - Vendor Report</p>
        <p>Page 1 of 1 | Report ID: PERF-{{ now()->format('Ymd-His') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

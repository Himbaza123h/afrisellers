<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Vendor Dashboard Report - {{ now()->format('M d, Y') }}</title>
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            border: 1px solid #000;
            padding: 12px;
            text-align: center;
            background-color: #f9f9f9;
        }

        .stat-label {
            font-weight: bold;
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .stat-value {
            font-weight: bold;
            font-size: 16px;
            color: #000;
            margin-bottom: 5px;
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
            padding: 8px;
            text-align: left;
        }

        .data-table th {
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

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-active { background-color: #d1fae5; color: #065f46; }
        .badge-pending { background-color: #fed7aa; color: #9a3412; }
        .badge-completed { background-color: #dbeafe; color: #1e40af; }
        .badge-cancelled { background-color: #fee2e2; color: #991b1b; }

        .footer {
            text-align: center;
            font-size: 9px;
            color: #666;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }

        .chart-placeholder {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 15px 0;
            background-color: #f9f9f9;
            text-align: center;
            font-size: 10px;
            color: #666;
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
        <h1>VENDOR DASHBOARD REPORT</h1>
        <p>Performance Overview & Analytics</p>
        <p>
            Generated on: {{ now()->format('d/m/Y H:i:s') }} |
            Period: {{ ucfirst($filter) }} ({{ $currentStart->format('M d, Y') }} to {{ $currentEnd->format('M d, Y') }}) |
            Vendor: {{ $vendor->business_name ?? auth()->user()->name }}
        </p>
    </div>

    <!-- Key Statistics -->
    <div class="section-title">Key Performance Indicators</div>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value">${{ number_format($myRevenue, 2) }}</div>
            <div class="stat-subtext">Current period</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Total Products</div>
            <div class="stat-value">{{ number_format($totalProducts) }}</div>
            <div class="stat-subtext">{{ $activeProducts }} Active</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Total Orders</div>
            <div class="stat-value">{{ number_format($myOrders) }}</div>
            <div class="stat-subtext">Current period</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Pending Orders</div>
            <div class="stat-value">{{ $pendingOrders }}</div>
            <div class="stat-subtext">Requires attention</div>
        </div>
    </div>

    <!-- Order Status Overview -->
    <div class="section-title">Order Status Distribution</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Status</th>
                <th class="text-right">Order Count</th>
                <th class="text-right">Revenue</th>
                <th class="text-right">Avg. Order Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orderStatuses as $status)
                @if($status['count'] > 0)
                    <tr>
                        <td>{{ $status['status'] }}</td>
                        <td class="text-right">{{ number_format($status['count']) }}</td>
                        <td class="text-right">${{ number_format($status['revenue'], 2) }}</td>
                        <td class="text-right">
                            ${{ $status['count'] > 0 ? number_format($status['revenue'] / $status['count'], 2) : '0.00' }}
                        </td>
                    </tr>
                @endif
            @endforeach
            @if(array_sum(array_column($orderStatuses, 'count')) > 0)
                <tr style="font-weight: bold;">
                    <td>Total</td>
                    <td class="text-right">{{ number_format(array_sum(array_column($orderStatuses, 'count'))) }}</td>
                    <td class="text-right">${{ number_format(array_sum(array_column($orderStatuses, 'revenue')), 2) }}</td>
                    <td class="text-right">
                        ${{ array_sum(array_column($orderStatuses, 'count')) > 0 ?
                            number_format(array_sum(array_column($orderStatuses, 'revenue')) / array_sum(array_column($orderStatuses, 'count')), 2) :
                            '0.00' }}
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Recent Orders -->
    @if($recentOrders->count() > 0)
        <div class="section-title">Recent Orders (Last 5)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Product</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentOrders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>
                            @if($order->items->first() && $order->items->first()->product)
                                {{ Str::limit($order->items->first()->product->name, 30) }}
                            @else
                                Multiple Items
                            @endif
                        </td>
                        <td class="text-right">${{ number_format($order->total, 2) }}</td>
                        <td>
                            <span class="badge badge-{{ $order->status }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Top Products -->
    @if($topProducts->count() > 0)
        <div class="section-title">Top Performing Products</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th class="text-right">Sales Count</th>
                    <th class="text-right">Revenue</th>
                    <th class="text-right">Avg. Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topProducts as $product)
                    <tr>
                        <td>{{ Str::limit($product->name, 40) }}</td>
                        <td class="text-right">{{ number_format($product->sales_count) }}</td>
                        <td class="text-right">${{ number_format($product->revenue, 2) }}</td>
                        <td class="text-right">
                            ${{ $product->sales_count > 0 ? number_format($product->revenue / $product->sales_count, 2) : '0.00' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Sales Chart Data -->
    <div class="section-title">Sales Performance Data</div>
    <div class="chart-placeholder">
        <strong>Sales Data for {{ ucfirst($filter) }} Period</strong><br>
        Period: {{ implode(', ', $salesChartData['labels']) }}
        <br><br>
        <strong>Revenue by Period:</strong><br>
        @foreach($salesChartData['labels'] as $index => $label)
            {{ $label }}: ${{ number_format($salesChartData['sales'][$index] ?? 0, 2) }}<br>
        @endforeach
        <br>
        <strong>Orders by Period:</strong><br>
        @foreach($salesChartData['labels'] as $index => $label)
            {{ $label }}: {{ number_format($salesChartData['orders'][$index] ?? 0) }} orders<br>
        @endforeach
    </div>

    <!-- Report Summary -->
    <div class="section-title">Report Summary</div>
    <table class="data-table">
        <tbody>
            <tr>
                <td style="width: 30%; font-weight: bold;">Report Period</td>
                <td>{{ ucfirst($filter) }} ({{ $currentStart->format('M d, Y') }} to {{ $currentEnd->format('M d, Y') }})</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Total Revenue</td>
                <td>${{ number_format($myRevenue, 2) }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Total Orders</td>
                <td>{{ number_format($myOrders) }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Product Portfolio</td>
                <td>{{ number_format($totalProducts) }} products ({{ number_format($activeProducts) }} active)</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Average Order Value</td>
                <td>
                    ${{ $myOrders > 0 ? number_format($myRevenue / $myOrders, 2) : '0.00' }}
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Vendor Business</td>
                <td>{{ $vendor->business_name ?? 'N/A' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Vendor Dashboard System - Performance Report</p>
        <p>Page 1 of 1 | Report ID: DASH-{{ now()->format('Ymd-His') }} | Generated by: {{ auth()->user()->email }}</p>
        <p class="no-print">
            <button onclick="window.print()" style="padding: 5px 15px; background: #007bff; color: white; border: none; cursor: pointer;">
                Print Report
            </button>
        </p>
    </div>


    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

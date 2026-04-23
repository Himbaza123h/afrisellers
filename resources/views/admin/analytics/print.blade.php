<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Platform Analytics Report - {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</title>
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

        .metric-box {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            margin-bottom: 10px;
        }

        .metric-label {
            font-size: 9px;
            color: #666;
            margin-bottom: 3px;
        }

        .metric-value {
            font-size: 14px;
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
        <h1>PLATFORM ANALYTICS REPORT</h1>
        <p>{{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</p>
        <p>Generated on: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Key Metrics Section -->
    <div class="section-title">Key Platform Metrics</div>
    <table class="stats-table">
        <tr>
            <td>
                <div class="stats-label">Total Users</div>
                <div class="stats-value">{{ number_format($stats['total_users']) }}</div>
                <div class="stats-subtext">+{{ $stats['new_users'] }} new</div>
            </td>
            <td>
                <div class="stats-label">Total Vendors</div>
                <div class="stats-value">{{ number_format($stats['total_vendors']) }}</div>
                <div class="stats-subtext">{{ $stats['verified_vendors'] }} verified</div>
            </td>
            <td>
                <div class="stats-label">Total Revenue</div>
                <div class="stats-value">${{ number_format($stats['total_revenue'], 2) }}</div>
            </td>
            <td>
                <div class="stats-label">Total Orders</div>
                <div class="stats-value">{{ number_format($stats['total_orders']) }}</div>
                <div class="stats-subtext">{{ $stats['completed_orders'] }} completed</div>
            </td>
        </tr>
    </table>

    <!-- Secondary Metrics -->
    <div class="section-title">Secondary Metrics</div>
    <table class="stats-table">
        <tr>
            <td>
                <div class="stats-label">Total Products</div>
                <div class="stats-value">{{ number_format($stats['total_products']) }}</div>
            </td>
            <td>
                <div class="stats-label">Total RFQs</div>
                <div class="stats-value">{{ number_format($stats['total_rfqs']) }}</div>
            </td>
            <td>
                <div class="stats-label">Total Showrooms</div>
                <div class="stats-value">{{ number_format($stats['total_showrooms']) }}</div>
            </td>
            <td>
                <div class="stats-label">Total Tradeshows</div>
                <div class="stats-value">{{ number_format($stats['total_tradeshows']) }}</div>
            </td>
        </tr>
    </table>

    <!-- Performance Metrics -->
    <div class="section-title">Performance Metrics</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 25%;">Metric</th>
                <th style="width: 25%;" class="text-right">Total Clicks</th>
                <th style="width: 25%;" class="text-right">Total Impressions</th>
                <th style="width: 25%;" class="text-right">Average CTR</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Platform Performance</td>
                <td class="text-right">{{ number_format($performanceMetrics['total_clicks']) }}</td>
                <td class="text-right">{{ number_format($performanceMetrics['total_impressions']) }}</td>
                <td class="text-right">{{ $performanceMetrics['ctr'] }}%</td>
            </tr>
        </tbody>
    </table>

    <!-- Transaction & Escrow Stats -->
    <div style="display: flex; gap: 20px; margin: 20px 0;">
        <!-- Transaction Stats -->
        <div style="flex: 1;">
            <div class="section-title">Transaction Statistics</div>
            <table class="main-table">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th class="text-right">Count</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Successful</td>
                        <td class="text-right">{{ number_format($transactionStats['successful']) }}</td>
                    </tr>
                    <tr>
                        <td>Pending</td>
                        <td class="text-right">{{ number_format($transactionStats['pending']) }}</td>
                    </tr>
                    <tr>
                        <td>Failed</td>
                        <td class="text-right">{{ number_format($transactionStats['failed']) }}</td>
                    </tr>
                    <tr>
                        <td>Refunded</td>
                        <td class="text-right">{{ number_format($transactionStats['refunded']) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Escrow Stats -->
        <div style="flex: 1;">
            <div class="section-title">Escrow Statistics</div>
            <table class="main-table">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th class="text-right">Count</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total Escrows</td>
                        <td class="text-right">{{ number_format($escrowStats['total_escrows']) }}</td>
                        <td class="text-right">-</td>
                    </tr>
                    <tr>
                        <td>Active</td>
                        <td class="text-right">{{ number_format($escrowStats['active']) }}</td>
                        <td class="text-right">-</td>
                    </tr>
                    <tr>
                        <td>Released</td>
                        <td class="text-right">{{ number_format($escrowStats['released']) }}</td>
                        <td class="text-right">-</td>
                    </tr>
                    <tr>
                        <td>Disputed</td>
                        <td class="text-right">{{ number_format($escrowStats['disputed']) }}</td>
                        <td class="text-right">-</td>
                    </tr>
                    <tr>
                        <td><strong>Total Held</strong></td>
                        <td class="text-right">-</td>
                        <td class="text-right"><strong>${{ number_format($escrowStats['total_held'], 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Order Status Distribution -->
    <div class="section-title">Order Status Distribution</div>
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
                $totalOrders = array_sum($orderStatusDistribution->toArray());
            @endphp
            @foreach($orderStatusDistribution as $status => $count)
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

        <!-- Top Buyers -->
        <div>
            <h4 style="font-size: 11px; font-weight: bold; margin: 10px 0 5px 0;">Top Buyers</h4>
            <table class="main-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 35%;">Buyer Name</th>
                        <th style="width: 15%;" class="text-right">Orders</th>
                        <th style="width: 20%;" class="text-right">Total Spent</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topBuyers as $index => $buyer)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $buyer->buyer->name ?? 'Unknown Buyer' }}</td>
                            <td class="text-right">{{ $buyer->order_count }}</td>
                            <td class="text-right">${{ number_format($buyer->total_spent, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center" style="padding: 10px;">No buyer data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Regional Performance -->
    <div style="page-break-before: always;">
        <div class="section-title">Regional Performance</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Country</th>
                    <th style="width: 15%;" class="text-right">Vendors</th>
                    <th style="width: 15%;" class="text-right">Products</th>
                    <th style="width: 15%;" class="text-right">Orders</th>
                    <th style="width: 20%;" class="text-right">Revenue</th>
                </tr>
            </thead>
            <tbody>
                @forelse($regionalStats as $country)
                    <tr>
                        <td>{{ $country->name }}</td>
                        <td class="text-right">{{ number_format($country->vendors_count) }}</td>
                        <td class="text-right">{{ number_format($country->products_count) }}</td>
                        <td class="text-right">{{ number_format($country->orders_count) }}</td>
                        <td class="text-right">${{ number_format($country->total_revenue, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 10px;">No regional data available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Product Category Distribution -->
    <div class="section-title">Product Category Distribution</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 50%;">Category</th>
                <th style="width: 25%;" class="text-right">Product Count</th>
                <th style="width: 25%;" class="text-right">Percentage</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalProducts = $categoryDistribution->sum('count');
            @endphp
            @forelse($categoryDistribution as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td class="text-right">{{ $category->count }}</td>
                    <td class="text-right">{{ $totalProducts > 0 ? number_format(($category->count / $totalProducts) * 100, 1) : 0 }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center" style="padding: 10px;">No category data available</td>
                </tr>
            @endforelse
            @if($totalProducts > 0)
                <tr>
                    <td style="font-weight: bold;">Total</td>
                    <td class="text-right" style="font-weight: bold;">{{ $totalProducts }}</td>
                    <td class="text-right" style="font-weight: bold;">100%</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Platform Activity -->
    <div class="section-title">Platform Activity</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 50%;">Activity Type</th>
                <th style="width: 25%;" class="text-right">Count</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Product Views</td>
                <td class="text-right">{{ number_format($platformActivity['product_views']) }}</td>
            </tr>
            <tr>
                <td>Product Clicks</td>
                <td class="text-right">{{ number_format($platformActivity['product_clicks']) }}</td>
            </tr>
            <tr>
                <td>RFQs Submitted</td>
                <td class="text-right">{{ number_format($platformActivity['rfq_submitted']) }}</td>
            </tr>
            <tr>
                <td>Orders Placed</td>
                <td class="text-right">{{ number_format($platformActivity['orders_placed']) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Platform Analytics System - Administrator Report</p>
        <p>Period: {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</p>
        <p>Report ID: ANA-{{ now()->format('Ymd-His') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

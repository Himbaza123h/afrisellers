<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Regional Reports - {{ $region->name }} - {{ now()->format('M d, Y') }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
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
            font-size: 9px;
            color: #666;
        }

        .stats-section {
            margin-bottom: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }

        .stat-card {
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
        }

        .stat-label {
            font-weight: bold;
            font-size: 8px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .stat-value {
            font-weight: bold;
            font-size: 16px;
            margin: 5px 0;
        }

        .stat-subtext {
            font-size: 8px;
            color: #666;
            margin-top: 3px;
        }

        .main-table {
            margin-top: 15px;
            page-break-inside: avoid;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        .main-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 9px;
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
            font-size: 8px;
            color: #666;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #000;
        }

        .page-break {
            page-break-after: always;
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
        <h1>REGIONAL REPORTS - {{ strtoupper($region->name) }}</h1>
        <p>Regional Admin: {{ auth()->user()->name }} ({{ auth()->user()->email }})</p>
        <p>Report Period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
        <p>Generated on: {{ now()->format('d/m/Y H:i:s') }}</p>
        @if($countryFilter)
            <p>Filtered by Country: {{ $countries->where('id', $countryFilter)->first()->name ?? 'N/A' }}</p>
        @endif
    </div>

    <!-- Main Statistics Overview -->
    <div class="section-title">Executive Summary</div>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Vendors</div>
            <div class="stat-value">{{ number_format($vendorsStats['total']) }}</div>
            <div class="stat-subtext">{{ $vendorsStats['verified'] }} Verified | {{ $vendorsStats['pending'] }} Pending</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Products</div>
            <div class="stat-value">{{ number_format($productsStats['total']) }}</div>
            <div class="stat-subtext">{{ $productsStats['approved'] }} Approved | {{ $productsStats['pending'] }} Pending</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Orders</div>
            <div class="stat-value">{{ number_format($ordersStats['total']) }}</div>
            <div class="stat-subtext">Value: ${{ number_format($ordersStats['total_value'], 2) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Loads</div>
            <div class="stat-value">{{ number_format($loadsStats['total']) }}</div>
            <div class="stat-subtext">{{ $loadsStats['delivered'] }} Delivered | {{ $loadsStats['in_transit'] }} In Transit</div>
        </div>
    </div>

    <!-- Vendors & Products Section -->
    <div class="section-title">Vendors & Products Breakdown</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 25%;">Category</th>
                <th style="width: 15%;">Total</th>
                <th style="width: 15%;">Verified/Approved</th>
                <th style="width: 15%;">Pending</th>
                <th style="width: 15%;">Active</th>
                <th style="width: 15%;">New This Period</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Vendors</strong></td>
                <td class="text-center">{{ number_format($vendorsStats['total']) }}</td>
                <td class="text-center">{{ number_format($vendorsStats['verified']) }}</td>
                <td class="text-center">{{ number_format($vendorsStats['pending']) }}</td>
                <td class="text-center">{{ number_format($vendorsStats['active']) }}</td>
                <td class="text-center">{{ number_format($vendorsStats['new_this_month']) }}</td>
            </tr>
            <tr>
                <td><strong>Products</strong></td>
                <td class="text-center">{{ number_format($productsStats['total']) }}</td>
                <td class="text-center">{{ number_format($productsStats['approved']) }}</td>
                <td class="text-center">{{ number_format($productsStats['pending']) }}</td>
                <td class="text-center">-</td>
                <td class="text-center">{{ number_format($productsStats['new_this_month']) }}</td>
            </tr>
            <tr>
                <td><strong>Showrooms</strong></td>
                <td class="text-center">{{ number_format($showroomsStats['total']) }}</td>
                <td class="text-center">{{ number_format($showroomsStats['verified']) }}</td>
                <td class="text-center">-</td>
                <td class="text-center">{{ number_format($showroomsStats['active']) }}</td>
                <td class="text-center">-</td>
            </tr>
            <tr>
                <td><strong>Transporters</strong></td>
                <td class="text-center">{{ number_format($transportersStats['total']) }}</td>
                <td class="text-center">{{ number_format($transportersStats['verified']) }}</td>
                <td class="text-center">-</td>
                <td class="text-center">{{ number_format($transportersStats['active']) }}</td>
                <td class="text-center">-</td>
            </tr>
        </tbody>
    </table>

    <!-- Orders Breakdown -->
    <div class="section-title">Orders & Revenue Analysis</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 20%;">Status</th>
                <th style="width: 16%;">Count</th>
                <th style="width: 16%;">Percentage</th>
                <th style="width: 16%;">Total Value</th>
                <th style="width: 16%;">Avg Order Value</th>
                <th style="width: 16%;">Period Orders</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Pending</strong></td>
                <td class="text-center">{{ number_format($ordersStats['pending']) }}</td>
                <td class="text-center">{{ $ordersStats['total'] > 0 ? round(($ordersStats['pending'] / $ordersStats['total']) * 100, 1) : 0 }}%</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-center">-</td>
            </tr>
            <tr>
                <td><strong>Processing</strong></td>
                <td class="text-center">{{ number_format($ordersStats['processing']) }}</td>
                <td class="text-center">{{ $ordersStats['total'] > 0 ? round(($ordersStats['processing'] / $ordersStats['total']) * 100, 1) : 0 }}%</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-center">-</td>
            </tr>
            <tr>
                <td><strong>Shipped</strong></td>
                <td class="text-center">{{ number_format($ordersStats['shipped']) }}</td>
                <td class="text-center">{{ $ordersStats['total'] > 0 ? round(($ordersStats['shipped'] / $ordersStats['total']) * 100, 1) : 0 }}%</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-center">-</td>
            </tr>
            <tr>
                <td><strong>Delivered</strong></td>
                <td class="text-center">{{ number_format($ordersStats['delivered']) }}</td>
                <td class="text-center">{{ $ordersStats['total'] > 0 ? round(($ordersStats['delivered'] / $ordersStats['total']) * 100, 1) : 0 }}%</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-center">-</td>
            </tr>
            <tr>
                <td><strong>Cancelled</strong></td>
                <td class="text-center">{{ number_format($ordersStats['cancelled']) }}</td>
                <td class="text-center">{{ $ordersStats['total'] > 0 ? round(($ordersStats['cancelled'] / $ordersStats['total']) * 100, 1) : 0 }}%</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-center">-</td>
            </tr>
            <tr style="background-color: #f9f9f9; font-weight: bold;">
                <td><strong>TOTAL</strong></td>
                <td class="text-center">{{ number_format($ordersStats['total']) }}</td>
                <td class="text-center">100%</td>
                <td class="text-right">${{ number_format($ordersStats['total_value'], 2) }}</td>
                <td class="text-right">${{ $ordersStats['total'] > 0 ? number_format($ordersStats['total_value'] / $ordersStats['total'], 2) : '0.00' }}</td>
                <td class="text-center">{{ number_format($ordersStats['period_orders']) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Loads & Transportation -->
    <div class="section-title">Logistics & Transportation</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 25%;">Category</th>
                <th style="width: 15%;">Posted</th>
                <th style="width: 15%;">Assigned</th>
                <th style="width: 15%;">In Transit</th>
                <th style="width: 15%;">Delivered</th>
                <th style="width: 15%;">Cancelled</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Loads</strong></td>
                <td class="text-center">{{ number_format($loadsStats['posted']) }}</td>
                <td class="text-center">{{ number_format($loadsStats['assigned']) }}</td>
                <td class="text-center">{{ number_format($loadsStats['in_transit']) }}</td>
                <td class="text-center">{{ number_format($loadsStats['delivered']) }}</td>
                <td class="text-center">{{ number_format($loadsStats['cancelled']) }}</td>
            </tr>
            <tr>
                <td><strong>Period Loads</strong></td>
                <td colspan="5" class="text-center">{{ number_format($loadsStats['period_loads']) }} loads created in this period</td>
            </tr>
        </tbody>
    </table>

    <table class="main-table" style="margin-top: 10px;">
        <thead>
            <tr>
                <th style="width: 25%;">Transportation Metrics</th>
                <th style="width: 25%;">Total Transporters</th>
                <th style="width: 25%;">Total Fleet Size</th>
                <th style="width: 25%;">Delivery Success Rate</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Statistics</strong></td>
                <td class="text-center">
                    {{ number_format($transportersStats['total']) }}
                    <br><small>({{ $transportersStats['verified'] }} verified)</small>
                </td>
                <td class="text-center">
                    {{ number_format($transportersStats['total_fleet']) }} vehicles
                    <br><small>(Avg: {{ $transportersStats['total'] > 0 ? round($transportersStats['total_fleet'] / $transportersStats['total'], 1) : 0 }} per transporter)</small>
                </td>
                <td class="text-center">
                    {{ $loadsStats['total'] > 0 ? round(($loadsStats['delivered'] / $loadsStats['total']) * 100, 1) : 0 }}%
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Page Break for Revenue Section -->
    <div class="page-break"></div>

    <!-- Revenue by Country -->
    @if($revenueByCountry->count() > 0)
    <div class="section-title">Revenue Distribution by Country</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 40%;">Country</th>
                <th style="width: 30%;">Total Revenue</th>
                <th style="width: 30%;">Percentage of Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalRevenue = $revenueByCountry->sum('total_revenue');
            @endphp
            @foreach($revenueByCountry as $revenue)
            <tr>
                <td>{{ $revenue->country_name }}</td>
                <td class="text-right">${{ number_format($revenue->total_revenue, 2) }}</td>
                <td class="text-center">{{ $totalRevenue > 0 ? round(($revenue->total_revenue / $totalRevenue) * 100, 1) : 0 }}%</td>
            </tr>
            @endforeach
            <tr style="background-color: #f9f9f9; font-weight: bold;">
                <td><strong>TOTAL</strong></td>
                <td class="text-right"><strong>${{ number_format($totalRevenue, 2) }}</strong></td>
                <td class="text-center"><strong>100%</strong></td>
            </tr>
        </tbody>
    </table>
    @endif

    <!-- Monthly Trends -->
    @if($monthlyOrders->count() > 0)
    <div class="section-title">Monthly Trends (Last 6 Months)</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 40%;">Month</th>
                <th style="width: 20%;">Orders</th>
                <th style="width: 20%;">Revenue</th>
                <th style="width: 20%;">Avg Order Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthlyOrders as $monthly)
            <tr>
                <td>{{ \Carbon\Carbon::parse($monthly->month . '-01')->format('F Y') }}</td>
                <td class="text-center">{{ number_format($monthly->count) }}</td>
                <td class="text-right">${{ number_format($monthly->revenue, 2) }}</td>
                <td class="text-right">${{ $monthly->count > 0 ? number_format($monthly->revenue / $monthly->count, 2) : '0.00' }}</td>
            </tr>
            @endforeach
            <tr style="background-color: #f9f9f9; font-weight: bold;">
                <td><strong>TOTAL</strong></td>
                <td class="text-center"><strong>{{ number_format($monthlyOrders->sum('count')) }}</strong></td>
                <td class="text-right"><strong>${{ number_format($monthlyOrders->sum('revenue'), 2) }}</strong></td>
                <td class="text-right"><strong>${{ $monthlyOrders->sum('count') > 0 ? number_format($monthlyOrders->sum('revenue') / $monthlyOrders->sum('count'), 2) : '0.00' }}</strong></td>
            </tr>
        </tbody>
    </table>
    @endif

    <!-- Summary Section -->
    <div class="section-title">Report Summary</div>
    <table class="main-table">
        <tbody>
            <tr>
                <td style="width: 30%; font-weight: bold;">Region</td>
                <td>{{ $region->name }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Countries Covered</td>
                <td>{{ $countries->pluck('name')->join(', ') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Report Period</td>
                <td>{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Generated By</td>
                <td>{{ auth()->user()->name }} ({{ auth()->user()->email }})</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Total Businesses</td>
                <td>
                    Vendors: {{ number_format($vendorsStats['total']) }} |
                    Showrooms: {{ number_format($showroomsStats['total']) }} |
                    Transporters: {{ number_format($transportersStats['total']) }}
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Total Revenue</td>
                <td>${{ number_format($ordersStats['total_value'], 2) }} from {{ number_format($ordersStats['total']) }} orders</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Period Performance</td>
                <td>
                    Orders: {{ number_format($ordersStats['period_orders']) }} |
                    Revenue: ${{ number_format($ordersStats['period_value'], 2) }} |
                    New Vendors: {{ number_format($vendorsStats['new_this_month']) }}
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Logistics Performance</td>
                <td>
                    Total Loads: {{ number_format($loadsStats['total']) }} |
                    Delivered: {{ number_format($loadsStats['delivered']) }} ({{ $loadsStats['total'] > 0 ? round(($loadsStats['delivered'] / $loadsStats['total']) * 100, 1) : 0 }}%) |
                    Fleet Size: {{ number_format($transportersStats['total_fleet']) }} vehicles
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Regional Admin Reports System | {{ $region->name }}</p>
        <p>Report ID: REP-{{ now()->format('Ymd-His') }} | Page 1-2 | Confidential Document</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

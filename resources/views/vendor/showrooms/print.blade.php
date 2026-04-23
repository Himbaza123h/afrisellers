<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Showrooms Report - {{ now()->format('M d, Y') }}</title>
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

        .badge-active {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-inactive {
            background-color: #f3f4f6;
            color: #374151;
        }

        .badge-verified {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-featured {
            background-color: #fef3c7;
            color: #92400e;
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
        <h1>SHOWROOMS REPORT</h1>
        <p>Physical Showroom Locations & Management</p>
        <p>Generated on: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Statistics Section -->
    <div class="stats-section">
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">Total Showrooms</div>
                    <div class="stats-value">{{ number_format($stats['total_showrooms']) }}</div>
                </td>
                <td>
                    <div class="stats-label">Total Products</div>
                    <div class="stats-value">{{ number_format($stats['total_products']) }}</div>
                    <div class="stats-subtext">Across all showrooms</div>
                </td>
                <td>
                    <div class="stats-label">Total Views</div>
                    <div class="stats-value">{{ number_format($stats['total_views']) }}</div>
                    <div class="stats-subtext">All-time views</div>
                </td>
                <td>
                    <div class="stats-label">Total Inquiries</div>
                    <div class="stats-value">{{ number_format($stats['total_inquiries']) }}</div>
                    <div class="stats-subtext">Received inquiries</div>
                </td>
                <td>
                    <div class="stats-label">Active Showrooms</div>
                    <div class="stats-value">{{ number_format($stats['active_showrooms']) }}</div>
                    <div class="stats-subtext">{{ $stats['total_showrooms'] > 0 ? round(($stats['active_showrooms'] / $stats['total_showrooms']) * 100, 1) : 0 }}% of total</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Showrooms List -->
    <div class="section-title">Showrooms Details</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 15%;">Showroom Name</th>
                <th style="width: 10%;">Showroom #</th>
                <th style="width: 15%;">Location</th>
                <th style="width: 10%;">Business Type</th>
                <th style="width: 8%;" class="text-right">Products</th>
                <th style="width: 8%;" class="text-right">Views</th>
                <th style="width: 8%;" class="text-right">Inquiries</th>
                <th style="width: 8%;">Status</th>
                <th style="width: 10%;">Features</th>
            </tr>
        </thead>
        <tbody>
            @forelse($showrooms as $showroom)
                <tr>
                    <td><strong>{{ $showroom->name }}</strong></td>
                    <td>{{ $showroom->showroom_number ?? 'N/A' }}</td>
                    <td>
                        {{ $showroom->city }}, {{ $showroom->state_province }}<br>
                        <small>{{ $showroom->country->name ?? 'N/A' }}</small>
                    </td>
                    <td>{{ $showroom->business_type ?: 'N/A' }}</td>
                    <td class="text-right">{{ $showroom->products_count }}</td>
                    <td class="text-right">{{ number_format($showroom->views_count ?? 0) }}</td>
                    <td class="text-right">{{ $showroom->inquiries_count ?? 0 }}</td>
                    <td>
                        @if($showroom->status === 'active')
                            <span class="badge badge-active">Active</span>
                        @else
                            <span class="badge badge-inactive">Inactive</span>
                        @endif
                    </td>
                    <td>
                        @if($showroom->is_verified)
                            <span class="badge badge-verified">Verified</span>
                        @endif
                        @if($showroom->is_featured)
                            <span class="badge badge-featured">Featured</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px;">
                        No showrooms found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Country Distribution -->
    @if($countryDistribution->count() > 0)
        <div class="section-title">Country Distribution</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 40%;">Country</th>
                    <th style="width: 20%;" class="text-right">Showrooms</th>
                    <th style="width: 20%;" class="text-right">Percentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($countryDistribution as $distribution)
                    <tr>
                        <td>{{ $distribution['country']->name ?? 'Unknown' }}</td>
                        <td class="text-right">{{ $distribution['count'] }}</td>
                        <td class="text-right">{{ $distribution['percentage'] }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Status Distribution -->
    @if($statusDistribution->count() > 0)
        <div class="section-title">Status Distribution</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 40%;">Status</th>
                    <th style="width: 20%;" class="text-right">Count</th>
                    <th style="width: 20%;" class="text-right">Percentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($statusDistribution as $status => $data)
                    <tr>
                        <td>{{ ucfirst($status) }}</td>
                        <td class="text-right">{{ $data['count'] }}</td>
                        <td class="text-right">{{ $data['percentage'] }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Additional Metrics -->
    <div class="section-title">Additional Metrics</div>
    <table class="main-table">
        <tbody>
            <tr>
                <td style="width: 30%; font-weight: bold;">Verified Showrooms</td>
                <td style="width: 20%;" class="text-right">{{ $stats['verified_showrooms'] }}</td>
                <td style="width: 50%;">
                    {{ $stats['total_showrooms'] > 0 ? round(($stats['verified_showrooms'] / $stats['total_showrooms']) * 100, 1) : 0 }}% of total
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Featured Showrooms</td>
                <td class="text-right">{{ $stats['featured_showrooms'] }}</td>
                <td>
                    {{ $stats['total_showrooms'] > 0 ? round(($stats['featured_showrooms'] / $stats['total_showrooms']) * 100, 1) : 0 }}% of total
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Average Products per Showroom</td>
                <td class="text-right">
                    {{ $stats['total_showrooms'] > 0 ? round($stats['total_products'] / $stats['total_showrooms'], 1) : 0 }}
                </td>
                <td>Products per showroom</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Average Views per Showroom</td>
                <td class="text-right">
                    {{ $stats['total_showrooms'] > 0 ? round($stats['total_views'] / $stats['total_showrooms'], 0) : 0 }}
                </td>
                <td>Views per showroom</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Average Inquiries per Showroom</td>
                <td class="text-right">
                    {{ $stats['total_showrooms'] > 0 ? round($stats['total_inquiries'] / $stats['total_showrooms'], 1) : 0 }}
                </td>
                <td>Inquiries per showroom</td>
            </tr>
        </tbody>
    </table>

    <!-- Summary -->
    <div class="section-title">Report Summary</div>
    <table class="main-table">
        <tbody>
            <tr>
                <td style="width: 25%; font-weight: bold;">Total Showrooms</td>
                <td>{{ $stats['total_showrooms'] }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Total Products in Showrooms</td>
                <td>{{ $stats['total_products'] }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Active Showrooms</td>
                <td>{{ $stats['active_showrooms'] }} ({{ $stats['total_showrooms'] > 0 ? round(($stats['active_showrooms'] / $stats['total_showrooms']) * 100, 1) : 0 }}%)</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Report ID</td>
                <td>SHOW-{{ now()->format('Ymd-His') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Showroom Management System - Vendor Report</p>
        <p>Page 1 of 1 | Total Records: {{ $showrooms->count() }} | Report ID: SHOW-{{ now()->format('Ymd-His') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

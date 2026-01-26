<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Showrooms Report - {{ $country->name }} - {{ now()->format('M d, Y') }}</title>
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

        .badge-pending {
            background-color: #fed7aa;
            color: #9a3412;
        }

        .badge-inactive {
            background-color: #e5e7eb;
            color: #374151;
        }

        .badge-verified {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-unverified {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-featured {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-dealership {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-showroom {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-warehouse {
            background-color: #e5e7eb;
            color: #374151;
        }

        .badge-retail {
            background-color: #fce7f3;
            color: #9d174d;
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
        <h1>SHOWROOMS REPORT - {{ strtoupper($country->name) }}</h1>
        <p>Country Admin: {{ auth()->user()->name }} ({{ auth()->user()->email }})</p>
        <p>Generated on: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Statistics -->
    <div class="stats-section">
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">Total Showrooms</div>
                    <div class="stats-value">{{ number_format($stats['total']) }}</div>
                </td>
                <td>
                    <div class="stats-label">Active Showrooms</div>
                    <div class="stats-value">{{ number_format($stats['active']) }}</div>
                    <div class="stats-subtext">{{ $stats['active_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">Verified</div>
                    <div class="stats-value">{{ number_format($stats['verified']) }}</div>
                    <div class="stats-subtext">{{ $stats['verified_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">Featured</div>
                    <div class="stats-value">{{ number_format($stats['featured']) }}</div>
                    <div class="stats-subtext">{{ $stats['featured_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">Total Views</div>
                    <div class="stats-value">{{ number_format($stats['total_views']) }}</div>
                </td>
                <td>
                    <div class="stats-label">Total Inquiries</div>
                    <div class="stats-value">{{ number_format($stats['total_inquiries']) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Showrooms List -->
    <div class="section-title">Showrooms List ({{ $showrooms->count() }} records)</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 15%;">Showroom Name</th>
                <th style="width: 12%;">City</th>
                <th style="width: 15%;">Owner</th>
                <th style="width: 12%;">Business Type</th>
                <th style="width: 10%;" class="text-right">Products</th>
                <th style="width: 10%;" class="text-right">Views</th>
                <th style="width: 10%;">Verification</th>
                <th style="width: 8%;">Status</th>
                <th style="width: 8%;">Featured</th>
            </tr>
        </thead>
        <tbody>
            @forelse($showrooms as $showroom)
                <tr>
                    <td><strong>{{ $showroom->name }}</strong><br><small>{{ $showroom->showroom_number }}</small></td>
                    <td>{{ $showroom->city }}</td>
                    <td>{{ $showroom->user->name ?? 'N/A' }}</td>
                    <td>
                        @php
                            $badgeClass = match($showroom->business_type) {
                                'dealership' => 'badge-dealership',
                                'showroom' => 'badge-showroom',
                                'warehouse' => 'badge-warehouse',
                                'retail' => 'badge-retail',
                                default => 'badge-showroom'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ ucfirst($showroom->business_type) }}</span>
                    </td>
                    <td class="text-right">{{ $showroom->products->count() }}</td>
                    <td class="text-right">{{ number_format($showroom->views_count) }}</td>
                    <td>
                        @if($showroom->is_verified)
                            <span class="badge badge-verified">Verified</span>
                        @else
                            <span class="badge badge-unverified">Unverified</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $badgeClass = match($showroom->status) {
                                'active' => 'badge-active',
                                'pending' => 'badge-pending',
                                'inactive' => 'badge-inactive',
                                default => 'badge-inactive'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ ucfirst($showroom->status) }}</span>
                    </td>
                    <td>
                        @if($showroom->is_featured)
                            <span class="badge badge-featured">Yes</span>
                        @else
                            <span>No</span>
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

    <!-- Summary -->
    <div class="section-title">Report Summary</div>
    <table class="main-table">
        <tbody>
            <tr>
                <td style="width: 25%; font-weight: bold;">Total Records</td>
                <td>{{ $showrooms->count() }}</td>
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
                <td style="font-weight: bold;">Cities Covered</td>
                <td>{{ $cities->count() }} cities: {{ $cities->join(', ') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Business Types Distribution</td>
                <td>
                    @php
                        $businessTypes = $showrooms->groupBy('business_type')->map->count();
                    @endphp
                    @foreach($businessTypes as $type => $count)
                        {{ ucfirst($type) }}: {{ $count }}{{ !$loop->last ? ' | ' : '' }}
                    @endforeach
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Status Overview</td>
                <td>
                    Active: {{ $stats['active'] }} ({{ $stats['active_percentage'] }}%) |
                    Pending: {{ $stats['pending'] }} |
                    Verified: {{ $stats['verified'] }} ({{ $stats['verified_percentage'] }}%) |
                    Featured: {{ $stats['featured'] }} ({{ $stats['featured_percentage'] }}%)
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Country Admin Showroom Management System</p>
        <p>Page 1 of 1 | Report ID: SHOW-{{ now()->format('Ymd-His') }} | Country: {{ $country->name }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

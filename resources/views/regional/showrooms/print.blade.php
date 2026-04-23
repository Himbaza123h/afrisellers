<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Regional Showrooms Report - {{ $region->name }} - {{ now()->format('M d, Y') }}</title>
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

        .badge-verified { background-color: #d1fae5; color: #065f46; }
        .badge-unverified { background-color: #fee2e2; color: #991b1b; }
        .badge-active { background-color: #d1fae5; color: #065f46; }
        .badge-pending { background-color: #fef3c7; color: #92400e; }
        .badge-inactive { background-color: #f3f4f6; color: #374151; }
        .badge-featured { background-color: #fef3c7; color: #92400e; }

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
        <h1>REGIONAL SHOWROOMS REPORT - {{ strtoupper($region->name) }}</h1>
        <p>Regional Admin: {{ auth()->user()->name }} ({{ auth()->user()->email }})</p>
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
                <th style="width: 18%;">Showroom Name</th>
                <th style="width: 10%;">Country</th>
                <th style="width: 10%;">City</th>
                <th style="width: 13%;">Owner</th>
                <th style="width: 10%;">Business Type</th>
                <th style="width: 8%;">Products</th>
                <th style="width: 8%;">Views</th>
                <th style="width: 10%;">Verification</th>
                <th style="width: 8%;">Status</th>
                <th style="width: 5%;">Featured</th>
            </tr>
        </thead>
        <tbody>
            @forelse($showrooms as $showroom)
                <tr>
                    <td>
                        <strong>{{ Str::limit($showroom->name, 35) }}</strong>
                        <br><small>{{ $showroom->showroom_number }}</small>
                    </td>
                    <td>{{ $showroom->country->name ?? 'N/A' }}</td>
                    <td>{{ $showroom->city }}</td>
                    <td>
                        <strong>{{ $showroom->user->name ?? 'N/A' }}</strong>
                        <br><small>{{ Str::limit($showroom->user->email ?? 'N/A', 20) }}</small>
                    </td>
                    <td>{{ ucfirst($showroom->business_type ?? 'N/A') }}</td>
                    <td class="text-center">{{ number_format($showroom->products->count()) }}</td>
                    <td class="text-center">{{ number_format($showroom->views_count) }}</td>
                    <td>
                        @if($showroom->is_verified)
                            <span class="badge badge-verified">Verified</span>
                        @else
                            <span class="badge badge-unverified">Unverified</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $statusClass = match($showroom->status) {
                                'active' => 'badge-active',
                                'pending' => 'badge-pending',
                                'inactive' => 'badge-inactive',
                                default => 'badge-inactive'
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ ucfirst($showroom->status) }}</span>
                    </td>
                    <td class="text-center">
                        @if($showroom->is_featured)
                            <span class="badge badge-featured">★</span>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 20px;">
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
                <td style="font-weight: bold;">Region</td>
                <td>{{ $region->name }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Countries Covered</td>
                <td>{{ $countries->pluck('name')->join(', ') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Generated By</td>
                <td>{{ auth()->user()->name }} ({{ auth()->user()->email }})</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Status Distribution</td>
                <td>
                    Active: {{ $stats['active'] }} ({{ $stats['active_percentage'] }}%) |
                    Pending: {{ $stats['pending'] }} |
                    Featured: {{ $stats['featured'] }}
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Verification Status</td>
                <td>
                    Verified: {{ $stats['verified'] }} ({{ $stats['verified_percentage'] }}%) |
                    Unverified: {{ $stats['unverified'] }}
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Engagement Metrics</td>
                <td>
                    Total Views: {{ number_format($stats['total_views']) }} |
                    Total Inquiries: {{ number_format($stats['total_inquiries']) }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Regional Admin Showroom Management System</p>
        <p>Page 1 of 1 | Report ID: RS-{{ now()->format('Ymd-His') }} | Region: {{ $region->name }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

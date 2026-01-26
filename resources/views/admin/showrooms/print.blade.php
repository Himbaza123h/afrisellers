<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Showrooms Management Report</title>
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

        .status-verified {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-active {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-suspended {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-featured {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-standard {
            background-color: #e0e7ff;
            color: #3730a3;
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
        <h1>SHOWROOMS MANAGEMENT REPORT</h1>
        <p>Complete Overview of All Registered Showrooms</p>
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
                    <div class="stats-label">Total Showrooms</div>
                    <div class="stats-value">{{ $stats['total'] }}</div>
                </td>
                <td>
                    <div class="stats-label">Active Showrooms</div>
                    <div class="stats-value">{{ $stats['active'] }}</div>
                    <div class="stats-subtext" style="color: #1e40af;">{{ $stats['active_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">Verified Showrooms</div>
                    <div class="stats-value">{{ $stats['verified'] }}</div>
                    <div class="stats-subtext" style="color: #065f46;">{{ $stats['verified_percentage'] }}% verified</div>
                </td>
                <td>
                    <div class="stats-label">Featured Showrooms</div>
                    <div class="stats-value">{{ $stats['featured'] }}</div>
                    <div class="stats-subtext" style="color: #92400e;">{{ $stats['featured_percentage'] }}% featured</div>
                </td>
            </tr>
        </table>

        <!-- Performance Row -->
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">Total Views</div>
                    <div class="stats-value">{{ number_format($stats['total_views']) }}</div>
                    <div class="stats-subtext" style="color: #0d9488;">All showrooms</div>
                </td>
                <td>
                    <div class="stats-label">Total Inquiries</div>
                    <div class="stats-value">{{ number_format($stats['total_inquiries']) }}</div>
                    <div class="stats-subtext" style="color: #7c3aed;">Customer inquiries</div>
                </td>
                <td>
                    <div class="stats-label">This Month</div>
                    <div class="stats-value">{{ $stats['this_month'] }}</div>
                    <div class="stats-subtext" style="color: #f59e0b;">New this month</div>
                </td>
                <td>
                    <div class="stats-label">Avg. Rating</div>
                    <div class="stats-value">{{ $stats['avg_rating'] }}</div>
                    <div class="stats-subtext" style="color: #0ea5e9;">Average customer rating</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Main Table -->
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 3%;">#</th>
                <th style="width: 12%;">Showroom ID</th>
                <th style="width: 20%;">Showroom Name</th>
                <th style="width: 15%;">Owner/Vendor</th>
                <th style="width: 10%;" class="text-center">Products</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 10%;">Verification</th>
                <th style="width: 10%;">Featured</th>
                <th style="width: 10%;">Registration Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($showrooms as $index => $showroom)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>#{{ $showroom->id }}</strong>
                    </td>
                    <td>
                        <strong>{{ $showroom->name }}</strong>
                        @if($showroom->description)
                            <br><small>{{ Str::limit($showroom->description, 50) }}</small>
                        @endif
                    </td>
                    <td>
                        {{ $showroom->vendor->name ?? 'Unknown' }}
                        @if($showroom->vendor && $showroom->vendor->email)
                            <br><small>{{ $showroom->vendor->email }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $showroom->products_count ?? 0 }}</td>
                    <td>
                        @php
                            $statusClasses = [
                                'active' => 'status-active',
                                'pending' => 'status-pending',
                                'suspended' => 'status-suspended',
                                'inactive' => 'status-suspended',
                            ];
                            $statusClass = $statusClasses[$showroom->status] ?? 'status-pending';
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ ucfirst($showroom->status) }}
                        </span>
                    </td>
                    <td>
                        @if($showroom->is_verified)
                            <span class="status-badge status-verified">Verified</span>
                        @else
                            <span class="status-badge status-pending">Pending</span>
                        @endif
                    </td>
                    <td>
                        @if($showroom->is_featured)
                            <span class="badge-featured status-badge">Featured</span>
                        @else
                            <span class="badge-standard status-badge">Standard</span>
                        @endif
                    </td>
                    <td>
                        {{ $showroom->created_at->format('M d, Y') }}
                        <br>
                        <small>{{ $showroom->created_at->format('h:i A') }}</small>
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

    <!-- Footer -->
    <div class="footer">
        <p>Showrooms Management System - Administrator Report</p>
        <p>Page 1 of 1 | Total Records: {{ $showrooms->count() }} | Report ID: SHW-{{ now()->format('Ymd-His') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

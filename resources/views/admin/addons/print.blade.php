<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Addon Management Report</title>
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

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-global {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-country {
            background-color: #d1fae5;
            color: #065f46;
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
        <h1>ADDON MANAGEMENT REPORT</h1>
        <p>Complete Overview of All Promotional Addons</p>
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
                    <div class="stats-label">Total Addons</div>
                    <div class="stats-value">{{ number_format($stats['total']) }}</div>
                </td>
                <td>
                    <div class="stats-label">Global Addons</div>
                    <div class="stats-value">{{ number_format($stats['global']) }}</div>
                    <div class="stats-subtext" style="color: #1e40af;">All Countries</div>
                </td>
                <td>
                    <div class="stats-label">Country Specific</div>
                    <div class="stats-value">{{ number_format($stats['country_specific']) }}</div>
                    <div class="stats-subtext" style="color: #065f46;">Specific Countries</div>
                </td>
                <td>
                    <div class="stats-label">Active Subscriptions</div>
                    <div class="stats-value">{{ number_format($stats['active_subscriptions']) }}</div>
                    <div class="stats-subtext" style="color: #dc2626;">Active Users</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Main Table -->
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 3%;">#</th>
                <th style="width: 20%;">Location</th>
                <th style="width: 15%;">Country</th>
                <th style="width: 12%;">Price</th>
                <th style="width: 10%;">Type</th>
                <th style="width: 10%;">Subscriptions</th>
                <th style="width: 10%;">Active</th>
                <th style="width: 10%;">Revenue</th>
                <th style="width: 10%;">Created</th>
            </tr>
        </thead>
        <tbody>
            @forelse($addons as $index => $addon)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $addon->locationX }}</strong>
                        <br><small>{{ ucfirst(str_replace('_', ' ', $addon->locationY)) }}</small>
                    </td>
                    <td>
                        @if($addon->country)
                            <span class="badge badge-country">
                                {{ $addon->country->name }}
                            </span>
                        @else
                            <span class="badge badge-global">
                                <i class="fas fa-globe"></i> Global
                            </span>
                        @endif
                    </td>
                    <td>
                        <strong>${{ number_format($addon->price, 2) }}</strong>
                        <br><small>/30 days</small>
                    </td>
                    <td class="text-center">
                        {{ $addon->country_id ? 'Country' : 'Global' }}
                    </td>
                    <td class="text-center">
                        <strong>{{ $addon->addonUsers->count() }}</strong>
                    </td>
                    <td class="text-center">
                        <strong>{{ $addon->activeAddonUsers->count() }}</strong>
                    </td>
                    <td>
                        <strong>${{ number_format($addon->price * ($addon->addonUsers->whereNotNull('paid_at')->count() ?? 0), 2) }}</strong>
                    </td>
<td>
    {{ optional($addon->created_at)->format('M d, Y') ?? 'N/A' }}
    @if($addon->created_at)
        <br><small>{{ $addon->created_at->format('h:i A') }}</small>
    @endif
</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px;">
                        No addons found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Addon Management System - Administrator Report</p>
        <p>Page 1 of 1 | Total Records: {{ $addons->count() }} | Report ID: ADD-{{ now()->format('Ymd-His') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

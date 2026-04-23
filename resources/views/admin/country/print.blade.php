<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Countries Management Report</title>
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
        }

        .stats-label {
            font-weight: bold;
            font-size: 9px;
            color: #666;
        }

        .stats-value {
            font-weight: bold;
            font-size: 16px;
            margin-top: 3px;
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

        .main-table tbody tr td {
            border-bottom: none;
        }

        .main-table tbody tr:last-child td {
            border-bottom: 1px solid #000;
        }

        .text-center {
            text-align: center;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }

        .status-active {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-inactive {
            background-color: #f3f4f6;
            color: #1f2937;
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
        <h1>COUNTRIES MANAGEMENT REPORT</h1>
        <p>Complete Overview of Countries and Regional Distribution</p>
    </div>

    <!-- Report Date -->
    <div class="report-date">
        Generated on: {{ now()->format('d/m/Y H:i:s') }}
    </div>

    <!-- Statistics Section -->
    <div class="stats-section">
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">TOTAL COUNTRIES</div>
                    <div class="stats-value">{{ $stats['total'] }}</div>
                </td>
                <td>
                    <div class="stats-label">ACTIVE COUNTRIES</div>
                    <div class="stats-value">{{ $stats['active'] }}</div>
                    <div style="font-size: 9px; color: #059669;">{{ $stats['active_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">INACTIVE</div>
                    <div class="stats-value">{{ $stats['inactive'] }}</div>
                    <div style="font-size: 9px; color: #6b7280;">{{ $stats['inactive_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">TOTAL VENDORS</div>
                    <div class="stats-value">{{ $stats['total_vendors'] }}</div>
                </td>
            </tr>
        </table>

        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">TOTAL REGIONS</div>
                    <div class="stats-value">{{ $stats['total_regions'] }}</div>
                    <div style="font-size: 9px; color: #4f46e5;">Coverage areas</div>
                </td>
                <td>
                    <div class="stats-label">AVG COUNTRIES/REGION</div>
                    <div class="stats-value">{{ $stats['avg_countries_per_region'] }}</div>
                    <div style="font-size: 9px; color: #14b8a6;">Distribution average</div>
                </td>
                <td>
                    <div class="stats-label">COUNTRIES WITH FLAGS</div>
                    <div class="stats-value">{{ $stats['countries_with_flags'] }}</div>
                    <div style="font-size: 9px; color: #f59e0b;">{{ $stats['flags_percentage'] }}% complete</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Main Table -->
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 20%;">Country</th>
                <th style="width: 10%;">Code</th>
                <th style="width: 20%;">Region</th>
                <th style="width: 10%;">Vendors</th>
                <th style="width: 15%;">Created Date</th>
                <th style="width: 10%;">Flag</th>
                <th style="width: 10%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($countries as $index => $country)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $country->name }}</strong></td>
                    <td>{{ $country->code ?? 'N/A' }}</td>
                    <td>{{ $country->region->name ?? 'Not assigned' }}</td>
                    <td class="text-center">{{ $country->vendors_count ?? 0 }}</td>
                    <td>
                        {{ $country->created_at->format('M d, Y') }}<br>
                        <small>{{ $country->created_at->format('h:i A') }}</small>
                    </td>
                    <td class="text-center">{{ $country->flag_url ? 'Yes' : 'No' }}</td>
                    <td class="text-center">
                        @php
                            $statusClasses = [
                                'active' => 'status-active',
                                'inactive' => 'status-inactive',
                            ];
                            $statusClass = $statusClasses[$country->status] ?? 'status-inactive';
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ ucfirst($country->status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px;">
                        No countries found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Countries Management System - Confidential Report</p>
        <p>Page 1 of 1 | Total Records: {{ $countries->count() }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

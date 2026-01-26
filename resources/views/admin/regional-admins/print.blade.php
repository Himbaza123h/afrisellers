<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Regional Administrators Report</title>
    <style>
        @page {
            size: A4;
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

        .text-right {
            text-align: right;
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

        .status-suspended {
            background-color: #fee2e2;
            color: #991b1b;
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
        <h1>REGIONAL ADMINISTRATORS REPORT</h1>
        <p>Complete Overview of Regional Admin Management</p>
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
                    <div class="stats-label">TOTAL ADMINISTRATORS</div>
                    <div class="stats-value">{{ $stats['total'] }}</div>
                </td>
                <td>
                    <div class="stats-label">ACTIVE ADMINS</div>
                    <div class="stats-value">{{ $stats['active'] }}</div>
                    <div style="font-size: 9px; color: #059669;">{{ $stats['active_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">INACTIVE</div>
                    <div class="stats-value">{{ $stats['inactive'] }}</div>
                    <div style="font-size: 9px; color: #6b7280;">{{ $stats['inactive_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">SUSPENDED</div>
                    <div class="stats-value">{{ $stats['suspended'] }}</div>
                </td>
            </tr>
        </table>

        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">REGIONS COVERED</div>
                    <div class="stats-value">{{ $stats['regions_covered'] }}</div>
                    <div style="font-size: 9px; color: #059669;">Active assignments</div>
                </td>
                <td>
                    <div class="stats-label">UNASSIGNED REGIONS</div>
                    <div class="stats-value">{{ $stats['unassigned_regions'] }}</div>
                    <div style="font-size: 9px; color: #d97706;">Needs assignment</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Main Table -->
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 20%;">Administrator</th>
                <th style="width: 25%;">Contact</th>
                <th style="width: 15%;">Region</th>
                <th style="width: 15%;">Assigned Date</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 10%;">User ID</th>
            </tr>
        </thead>
        <tbody>
            @forelse($regionalAdmins as $index => $regionalAdmin)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $regionalAdmin->user->name }}</strong><br>
                        <small>ID: #{{ $regionalAdmin->id }}</small>
                    </td>
                    <td>
                        {{ $regionalAdmin->user->email }}
                        @if($regionalAdmin->user->phone)
                            <br><small>{{ $regionalAdmin->user->phone }}</small>
                        @endif
                    </td>
                    <td>{{ $regionalAdmin->region->name }}</td>
                    <td>
                        {{ $regionalAdmin->assigned_at->format('M d, Y') }}<br>
                        <small>{{ $regionalAdmin->assigned_at->format('h:i A') }}</small>
                    </td>
                    <td class="text-center">
                        @php
                            $statusClasses = [
                                'active' => 'status-active',
                                'inactive' => 'status-inactive',
                                'suspended' => 'status-suspended',
                            ];
                            $statusClass = $statusClasses[$regionalAdmin->status] ?? 'status-inactive';
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ ucfirst($regionalAdmin->status) }}
                        </span>
                    </td>
                    <td class="text-center">{{ $regionalAdmin->user_id }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px;">
                        No regional administrators found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Regional Administrators Management System - Confidential Report</p>
        <p>Page 1 of 1 | Total Records: {{ $regionalAdmins->count() }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

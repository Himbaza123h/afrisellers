<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Vendor Profiles Report</title>
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

        .status-verified {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-rejected {
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
        <h1>VENDOR PROFILES REPORT</h1>
        <p>Complete Overview of Vendor Applications and Verification Status</p>
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
                    <div class="stats-label">TOTAL APPLICATIONS</div>
                    <div class="stats-value">{{ $stats['total'] }}</div>
                </td>
                <td>
                    <div class="stats-label">PENDING REVIEW</div>
                    <div class="stats-value">{{ $stats['pending'] }}</div>
                    <div style="font-size: 9px; color: #92400e;">{{ $stats['pending_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">VERIFIED</div>
                    <div class="stats-value">{{ $stats['verified'] }}</div>
                    <div style="font-size: 9px; color: #059669;">{{ $stats['verified_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">REJECTED</div>
                    <div class="stats-value">{{ $stats['rejected'] }}</div>
                </td>
            </tr>
        </table>

        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">TODAY'S APPLICATIONS</div>
                    <div class="stats-value">{{ $stats['today'] }}</div>
                </td>
                <td>
                    <div class="stats-label">THIS WEEK</div>
                    <div class="stats-value">{{ $stats['this_week'] }}</div>
                </td>
                <td>
                    <div class="stats-label">THIS MONTH</div>
                    <div class="stats-value">{{ $stats['this_month'] }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Main Table -->
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 20%;">Business Name</th>
                <th style="width: 15%;">Owner</th>
                <th style="width: 15%;">Registration</th>
                <th style="width: 15%;">Location</th>
                <th style="width: 15%;">Submitted</th>
                <th style="width: 15%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($businessProfiles as $index => $profile)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $profile->business_name }}</strong></td>
                    <td>
                        {{ $profile->user->name ?? 'N/A' }}<br>
                        <small>{{ $profile->user->email ?? 'N/A' }}</small>
                    </td>
                    <td>{{ $profile->business_registration_number }}</td>
                    <td>
                        {{ $profile->city }}<br>
                        <small>{{ $profile->country->name ?? 'N/A' }}</small>
                    </td>
                    <td>
                        {{ $profile->created_at->format('M d, Y') }}<br>
                        <small>{{ $profile->created_at->format('h:i A') }}</small>
                    </td>
                    <td class="text-center">
                        @php
                            $statusClasses = [
                                'verified' => 'status-verified',
                                'pending' => 'status-pending',
                                'rejected' => 'status-rejected',
                            ];
                            $statusClass = $statusClasses[$profile->verification_status] ?? 'status-pending';
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ ucfirst($profile->verification_status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px;">
                        No vendor profiles found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Vendor Management System - Confidential Report</p>
        <p>Page 1 of 1 | Total Records: {{ $businessProfiles->count() }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

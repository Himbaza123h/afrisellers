<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Transporters Management Report</title>
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

        .text-right {
            text-align: right;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
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

        .badge-verified {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-unverified {
            background-color: #fef3c7;
            color: #92400e;
        }

        .rating-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }

        .rating-excellent {
            background-color: #dcfce7;
            color: #166534;
        }

        .rating-good {
            background-color: #fef3c7;
            color: #92400e;
        }

        .rating-poor {
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
        <h1>TRANSPORTERS MANAGEMENT REPORT</h1>
        <p>Complete Overview of Logistics and Transportation Companies</p>
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
                    <div class="stats-label">Total Transporters</div>
                    <div class="stats-value">{{ $stats['total'] }}</div>
                </td>
                <td>
                    <div class="stats-label">Active Transporters</div>
                    <div class="stats-value">{{ $stats['active'] }}</div>
                    <div class="stats-subtext" style="color: #059669;">{{ $stats['active_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">Verified</div>
                    <div class="stats-value">{{ $stats['verified'] }}</div>
                    <div class="stats-subtext" style="color: #0d9488;">{{ $stats['verified_percentage'] }}% verified</div>
                </td>
                <td>
                    <div class="stats-label">Suspended</div>
                    <div class="stats-value">{{ $stats['suspended'] }}</div>
                    <div class="stats-subtext" style="color: #dc2626;">{{ $stats['suspended_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">New This Month</div>
                    <div class="stats-value">{{ $stats['this_month'] }}</div>
                    <div class="stats-subtext" style="color: #7c3aed;">Growth indicator</div>
                </td>
            </tr>
        </table>

        <!-- Performance Stats Row -->
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">Total Deliveries</div>
                    <div class="stats-value">{{ number_format($stats['total_deliveries']) }}</div>
                </td>
                <td>
                    <div class="stats-label">Success Rate</div>
                    <div class="stats-value">{{ $stats['success_rate'] }}%</div>
                    <div class="stats-subtext" style="color: #059669;">{{ number_format($stats['successful_deliveries']) }} successful</div>
                </td>
                <td>
                    <div class="stats-label">Average Rating</div>
                    <div class="stats-value">{{ $stats['avg_rating'] }}/5</div>
                    <div class="stats-subtext" style="color: #f59e0b;">Overall performance</div>
                </td>
                <td>
                    <div class="stats-label">Total Fleet Size</div>
                    <div class="stats-value">{{ number_format($stats['total_fleet_size']) }}</div>
                    <div class="stats-subtext" style="color: #0ea5e9;">Vehicles in service</div>
                </td>
                <td>
                    <div class="stats-label">Weekly Growth</div>
                    <div class="stats-value">{{ $stats['this_week'] }}</div>
                    <div class="stats-subtext" style="color: #8b5cf6;">New this week</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Main Table -->
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 3%;">#</th>
                <th style="width: 18%;">Company Details</th>
                <th style="width: 12%;">Registration</th>
                <th style="width: 15%;">Contact Information</th>
                <th style="width: 10%;">Location</th>
                <th style="width: 8%;" class="text-center">Fleet</th>
                <th style="width: 15%;">Performance Metrics</th>
                <th style="width: 12%;">Status & Verification</th>
                <th style="width: 7%;" class="text-center">Joined</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transporters as $index => $transporter)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $transporter->company_name }}</strong>
                        @if($transporter->user)
                            <br><small>Owner: {{ $transporter->user->name }}</small>
                        @endif
                    </td>
                    <td>
                        <div><strong>Reg #:</strong> {{ $transporter->registration_number ?? 'N/A' }}</div>
                        <div><strong>License:</strong> {{ $transporter->license_number ?? 'N/A' }}</div>
                    </td>
                    <td>
                        <div><strong>Email:</strong> {{ $transporter->email }}</div>
                        <div><strong>Phone:</strong> {{ $transporter->phone ?? 'N/A' }}</div>
                    </td>
                    <td>{{ $transporter->country->name ?? 'Not specified' }}</td>
                    <td class="text-center">
                        <strong>{{ $transporter->fleet_size }}</strong>
                        <br><small>vehicles</small>
                    </td>
                    <td>
                        <div>
                            <strong>Rating:</strong>
                            @php
                                $ratingClass = 'rating-poor';
                                if ($transporter->average_rating >= 4) $ratingClass = 'rating-excellent';
                                elseif ($transporter->average_rating >= 3) $ratingClass = 'rating-good';
                            @endphp
                            <span class="rating-badge {{ $ratingClass }}">
                                {{ number_format($transporter->average_rating, 1) }}/5
                            </span>
                        </div>
                        <div><strong>Deliveries:</strong> {{ number_format($transporter->total_deliveries) }}</div>
                        <div><strong>Success Rate:</strong> {{ $transporter->success_rate }}%</div>
                    </td>
                    <td>
                        @php
                            $statusClasses = [
                                'active' => 'status-active',
                                'inactive' => 'status-inactive',
                                'suspended' => 'status-suspended',
                            ];
                            $statusClass = $statusClasses[$transporter->status] ?? 'status-inactive';
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ ucfirst($transporter->status) }}
                        </span>

                        <div style="margin-top: 3px;">
                            @if($transporter->is_verified)
                                <span class="badge-verified status-badge">Verified</span>
                            @else
                                <span class="badge-unverified status-badge">Unverified</span>
                            @endif
                        </div>
                    </td>
                    <td class="text-center">
                        {{ $transporter->created_at->format('M d, Y') }}
                        <br>
                        <small>{{ $transporter->created_at->format('h:i A') }}</small>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px;">
                        No transporters found in the system
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Transporters Management System - Confidential Business Report</p>
        <p>Page 1 of 1 | Total Records: {{ $transporters->count() }} | Report ID: TR-{{ now()->format('Ymd-His') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

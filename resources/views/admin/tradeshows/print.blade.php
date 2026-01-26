<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tradeshows Management Report</title>
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

        .status-upcoming {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-ongoing {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-completed {
            background-color: #f3f4f6;
            color: #374151;
        }

        .status-published {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-draft {
            background-color: #e0e7ff;
            color: #3730a3;
        }

        .status-suspended {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-verified {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-featured {
            background-color: #fef3c7;
            color: #92400e;
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
        <h1>TRADESHOWS MANAGEMENT REPORT</h1>
        <p>Complete Overview of All Registered Tradeshows and Exhibitions</p>
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
                    <div class="stats-label">Total Tradeshows</div>
                    <div class="stats-value">{{ $stats['total'] }}</div>
                </td>
                <td>
                    <div class="stats-label">Upcoming</div>
                    <div class="stats-value">{{ $stats['upcoming'] }}</div>
                    <div class="stats-subtext" style="color: #1e40af;">{{ $stats['upcoming_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">Ongoing</div>
                    <div class="stats-value">{{ $stats['ongoing'] }}</div>
                    <div class="stats-subtext" style="color: #065f46;">Active now</div>
                </td>
                <td>
                    <div class="stats-label">Completed</div>
                    <div class="stats-value">{{ $stats['completed'] }}</div>
                    <div class="stats-subtext" style="color: #374151;">Past events</div>
                </td>
            </tr>
        </table>

        <!-- Performance Row -->
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">Verified</div>
                    <div class="stats-value">{{ $stats['verified'] }}</div>
                    <div class="stats-subtext" style="color: #059669;">{{ $stats['verified_percentage'] }}% verified</div>
                </td>
                <td>
                    <div class="stats-label">Featured</div>
                    <div class="stats-value">{{ $stats['featured'] }}</div>
                    <div class="stats-subtext" style="color: #92400e;">Special events</div>
                </td>
                <td>
                    <div class="stats-label">Expected Visitors</div>
                    <div class="stats-value">{{ number_format($stats['total_expected_visitors']) }}</div>
                    <div class="stats-subtext" style="color: #7c3aed;">Total capacity</div>
                </td>
                <td>
                    <div class="stats-label">This Month</div>
                    <div class="stats-value">{{ $stats['this_month'] }}</div>
                    <div class="stats-subtext" style="color: #f59e0b;">New this month</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Main Table -->
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 3%;">#</th>
                <th style="width: 12%;">Tradeshow Number</th>
                <th style="width: 20%;">Tradeshow Name</th>
                <th style="width: 15%;">Venue & Location</th>
                <th style="width: 8%;" class="text-center">Event Dates</th>
                <th style="width: 10%;">Expected Visitors</th>
                <th style="width: 8%;">Event Status</th>
                <th style="width: 8%;">Status</th>
                <th style="width: 8%;">Verified</th>
                <th style="width: 8%;">Registration Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tradeshows as $index => $tradeshow)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $tradeshow->tradeshow_number }}</strong>
                    </td>
                    <td>
                        <strong>{{ $tradeshow->name }}</strong>
                        @if($tradeshow->industry)
                            <br><small>{{ $tradeshow->industry }}</small>
                        @endif
                    </td>
                    <td>
                        {{ $tradeshow->venue_name }}
                        <br><small>{{ $tradeshow->city }}, {{ $tradeshow->country->name ?? 'N/A' }}</small>
                    </td>
                    <td class="text-center">
                        {{ $tradeshow->start_date->format('M d') }}
                        <br>to
                        <br>{{ $tradeshow->end_date->format('M d, Y') }}
                    </td>
                    <td>
                        <strong>{{ number_format($tradeshow->expected_visitors) }}</strong>
                        @if($tradeshow->duration_days)
                            <br><small>{{ $tradeshow->duration_days }} days</small>
                        @endif
                    </td>
                    <td>
                        @php
                            $now = now();
                            if ($tradeshow->start_date > $now) {
                                $eventStatus = 'Upcoming';
                                $eventStatusClass = 'status-upcoming';
                            } elseif ($tradeshow->start_date <= $now && $tradeshow->end_date >= $now) {
                                $eventStatus = 'Ongoing';
                                $eventStatusClass = 'status-ongoing';
                            } else {
                                $eventStatus = 'Completed';
                                $eventStatusClass = 'status-completed';
                            }
                        @endphp
                        <span class="status-badge {{ $eventStatusClass }}">
                            {{ $eventStatus }}
                        </span>
                    </td>
                    <td>
                        @php
                            $statusClasses = [
                                'published' => 'status-published',
                                'pending' => 'status-pending',
                                'draft' => 'status-draft',
                                'suspended' => 'status-suspended',
                            ];
                            $statusClass = $statusClasses[$tradeshow->status] ?? 'status-pending';
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ ucfirst($tradeshow->status) }}
                        </span>
                    </td>
                    <td>
                        @if($tradeshow->is_verified)
                            <span class="badge-verified status-badge">Verified</span>
                        @else
                            <span class="status-pending status-badge">Pending</span>
                        @endif
                        @if($tradeshow->is_featured)
                            <br><span class="badge-featured status-badge">Featured</span>
                        @endif
                    </td>
                    <td>
                        {{ $tradeshow->created_at->format('M d, Y') }}
                        <br>
                        <small>{{ $tradeshow->created_at->format('h:i A') }}</small>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 20px;">
                        No tradeshows found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Tradeshows Management System - Administrator Report</p>
        <p>Page 1 of 1 | Total Records: {{ $tradeshows->count() }} | Report ID: TRD-{{ now()->format('Ymd-His') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

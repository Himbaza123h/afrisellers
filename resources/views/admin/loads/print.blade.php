<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Load Management Report</title>
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

        .status-posted {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-bidding {
            background-color: #ede9fe;
            color: #5b21b6;
        }

        .status-assigned {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-in_transit {
            background-color: #fed7aa;
            color: #c2410c;
        }

        .status-delivered {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-assigned {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-unassigned {
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
        <h1>LOAD MANAGEMENT REPORT</h1>
        <p>Complete Overview of All Freight Loads and Shipments</p>
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
                    <div class="stats-label">Total Loads</div>
                    <div class="stats-value">{{ $stats['total'] }}</div>
                </td>
                <td>
                    <div class="stats-label">Posted</div>
                    <div class="stats-value">{{ $stats['posted'] }}</div>
                    <div class="stats-subtext" style="color: #1e40af;">Active loads</div>
                </td>
                <td>
                    <div class="stats-label">In Transit</div>
                    <div class="stats-value">{{ $stats['in_transit'] }}</div>
                    <div class="stats-subtext" style="color: #c2410c;">{{ $stats['in_transit_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">Delivered</div>
                    <div class="stats-value">{{ $stats['delivered'] }}</div>
                    <div class="stats-subtext" style="color: #065f46;">{{ $stats['delivered_percentage'] }}% completed</div>
                </td>
            </tr>
        </table>

        <!-- Performance Row -->
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">Cancelled</div>
                    <div class="stats-value">{{ $stats['cancelled'] }}</div>
                    <div class="stats-subtext" style="color: #991b1b;">Lost loads</div>
                </td>
                <td>
                    <div class="stats-label">Total Bids</div>
                    <div class="stats-value">{{ number_format($stats['total_bids']) }}</div>
                    <div class="stats-subtext" style="color: #7c3aed;">All bids</div>
                </td>
                <td>
                    <div class="stats-label">Total Weight</div>
                    <div class="stats-value">{{ $stats['total_weight'] }}</div>
                    <div class="stats-subtext" style="color: #0d9488;">Total KG</div>
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
                <th style="width: 12%;">Load Number</th>
                <th style="width: 18%;">Route</th>
                <th style="width: 15%;">Cargo Details</th>
                <th style="width: 10%;">Pickup Date</th>
                <th style="width: 12%;">Transporter</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 10%;">Weight</th>
                <th style="width: 10%;">Created Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($loads as $index => $load)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $load->load_number }}</strong>
                        <br><small>By: {{ $load->user->name ?? 'N/A' }}</small>
                    </td>
                    <td>
                        <strong>{{ $load->origin_city }}</strong>
                        <br><small>{{ $load->originCountry->name ?? 'N/A' }}</small>
                        <br>→
                        <br><strong>{{ $load->destination_city }}</strong>
                        <br><small>{{ $load->destinationCountry->name ?? 'N/A' }}</small>
                    </td>
                    <td>
                        <span class="status-badge" style="background-color: #f3f4f6; color: #374151;">
                            {{ ucfirst($load->cargo_type ?? 'General') }}
                        </span>
                        @if($load->budget)
                            <br><small>${{ number_format($load->budget, 2) }}</small>
                        @endif
                    </td>
                    <td>
                        @if($load->pickup_date)
                            {{ $load->pickup_date->format('M d, Y') }}
                            @php
                                $daysUntilPickup = now()->diffInDays($load->pickup_date, false);
                                if ($daysUntilPickup < 0) {
                                    $urgencyClass = 'status-cancelled';
                                    $urgencyText = 'Overdue';
                                } elseif ($daysUntilPickup <= 2) {
                                    $urgencyClass = 'status-in_transit';
                                    $urgencyText = 'Urgent';
                                } elseif ($daysUntilPickup <= 7) {
                                    $urgencyClass = 'status-assigned';
                                    $urgencyText = 'Soon';
                                } else {
                                    $urgencyClass = 'status-delivered';
                                    $urgencyText = 'Scheduled';
                                }
                            @endphp
                            <br><span class="status-badge {{ $urgencyClass }}">{{ $urgencyText }}</span>
                        @else
                            <small>Not set</small>
                        @endif
                    </td>
                    <td>
                        @if($load->assignedTransporter)
                            {{ $load->assignedTransporter->name }}
                            <br><span class="badge-assigned status-badge">Assigned</span>
                        @else
                            <span class="badge-unassigned status-badge">Not Assigned</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $statusClasses = [
                                'posted' => 'status-posted',
                                'bidding' => 'status-bidding',
                                'assigned' => 'status-assigned',
                                'in_transit' => 'status-in_transit',
                                'delivered' => 'status-delivered',
                                'cancelled' => 'status-cancelled',
                            ];
                            $statusClass = $statusClasses[$load->status] ?? 'status-posted';
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ ucfirst($load->status) }}
                        </span>
                    </td>
                    <td>
                        <strong>{{ number_format($load->weight ?? 0) }} kg</strong>
                    </td>
                    <td>
                        {{ $load->created_at->format('M d, Y') }}
                        <br>
                        <small>{{ $load->created_at->format('h:i A') }}</small>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px;">
                        No loads found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Load Management System - Administrator Report</p>
        <p>Page 1 of 1 | Total Records: {{ $loads->count() }} | Report ID: LOD-{{ now()->format('Ymd-His') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

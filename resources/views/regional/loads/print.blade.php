<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Regional Loads Report - {{ $region->name }} - {{ now()->format('M d, Y') }}</title>
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

        .badge-posted { background-color: #dbeafe; color: #1e40af; }
        .badge-bidding { background-color: #fef3c7; color: #92400e; }
        .badge-assigned { background-color: #e9d5ff; color: #6b21a8; }
        .badge-in_transit { background-color: #c7d2fe; color: #3730a3; }
        .badge-delivered { background-color: #d1fae5; color: #065f46; }
        .badge-cancelled { background-color: #fee2e2; color: #991b1b; }

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
        <h1>REGIONAL LOADS REPORT - {{ strtoupper($region->name) }}</h1>
        <p>Regional Admin: {{ auth()->user()->name }} ({{ auth()->user()->email }})</p>
        <p>Generated on: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Statistics -->
    <div class="stats-section">
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">Total Loads</div>
                    <div class="stats-value">{{ number_format($stats['total']) }}</div>
                </td>
                <td>
                    <div class="stats-label">Posted/Bidding</div>
                    <div class="stats-value">{{ number_format($stats['posted'] + $stats['bidding']) }}</div>
                    <div class="stats-subtext">{{ $stats['posted_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">In Transit</div>
                    <div class="stats-value">{{ number_format($stats['in_transit']) }}</div>
                </td>
                <td>
                    <div class="stats-label">Delivered</div>
                    <div class="stats-value">{{ number_format($stats['delivered']) }}</div>
                    <div class="stats-subtext">{{ $stats['delivered_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">Assigned</div>
                    <div class="stats-value">{{ number_format($stats['assigned']) }}</div>
                </td>
                <td>
                    <div class="stats-label">Total Bids</div>
                    <div class="stats-value">{{ number_format($stats['total_bids']) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Loads List -->
    <div class="section-title">Loads List ({{ $loads->count() }} records)</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 10%;">Load Number</th>
                <th style="width: 8%;">Date</th>
                <th style="width: 15%;">Route</th>
                <th style="width: 12%;">Shipper</th>
                <th style="width: 10%;">Cargo Type</th>
                <th style="width: 8%;">Pickup Date</th>
                <th style="width: 10%;">Budget</th>
                <th style="width: 7%;">Bids</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 10%;">Countries</th>
            </tr>
        </thead>
        <tbody>
            @forelse($loads as $load)
                <tr>
                    <td>
                        <strong>{{ $load->load_number }}</strong>
                        @if($load->tracking_number)
                            <br><small>{{ $load->tracking_number }}</small>
                        @endif
                    </td>
                    <td>{{ $load->created_at->format('M d, Y') }}</td>
                    <td>
                        <div style="line-height: 1.4;">
                            <strong>From:</strong> {{ $load->origin_city }}<br>
                            <strong>To:</strong> {{ $load->destination_city }}
                        </div>
                    </td>
                    <td>
                        <strong>{{ $load->user->name ?? 'N/A' }}</strong>
                        <br><small>{{ Str::limit($load->user->email ?? 'N/A', 20) }}</small>
                    </td>
                    <td>{{ ucfirst($load->cargo_type ?? 'N/A') }}</td>
                    <td>
                        {{ $load->pickup_date ? $load->pickup_date->format('M d, Y') : 'N/A' }}
                        @if($load->pickup_time_start)
                            <br><small>{{ $load->pickup_time_start->format('h:i A') }}</small>
                        @endif
                    </td>
                    <td class="text-right">
                        @if($load->budget)
                            <strong>{{ $load->currency }} {{ number_format($load->budget, 2) }}</strong>
                            <br><small>{{ ucfirst($load->pricing_type) }}</small>
                        @else
                            <span>Not set</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $load->bids->count() }}</td>
                    <td>
                        @php
                            $statusClass = match($load->status) {
                                'posted' => 'badge-posted',
                                'bidding' => 'badge-bidding',
                                'assigned' => 'badge-assigned',
                                'in_transit' => 'badge-in_transit',
                                'delivered' => 'badge-delivered',
                                'cancelled' => 'badge-cancelled',
                                default => 'badge-posted'
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $load->status)) }}</span>
                    </td>
                    <td>
                        <div style="line-height: 1.4;">
                            <strong>O:</strong> {{ $load->originCountry->name ?? 'N/A' }}<br>
                            <strong>D:</strong> {{ $load->destinationCountry->name ?? 'N/A' }}
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 20px;">
                        No loads found
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
                <td>{{ $loads->count() }}</td>
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
                <td style="font-weight: bold;">Load Status Distribution</td>
                <td>
                    Posted: {{ $stats['posted'] }} ({{ $stats['posted_percentage'] }}%) |
                    Bidding: {{ $stats['bidding'] }} |
                    Assigned: {{ $stats['assigned'] }} |
                    In Transit: {{ $stats['in_transit'] }} |
                    Delivered: {{ $stats['delivered'] }} ({{ $stats['delivered_percentage'] }}%) |
                    Cancelled: {{ $stats['cancelled'] }}
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Bidding Activity</td>
                <td>Total Bids: {{ number_format($stats['total_bids']) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Regional Admin Load Management System</p>
        <p>Page 1 of 1 | Report ID: RL-{{ now()->format('Ymd-His') }} | Region: {{ $region->name }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

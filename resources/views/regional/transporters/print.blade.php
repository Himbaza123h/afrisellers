<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Regional Transporters Report - {{ $region->name }} - {{ now()->format('M d, Y') }}</title>
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
        .badge-unverified { background-color: #fef3c7; color: #92400e; }
        .badge-active { background-color: #d1fae5; color: #065f46; }
        .badge-inactive { background-color: #f3f4f6; color: #374151; }
        .badge-suspended { background-color: #fee2e2; color: #991b1b; }

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
        <h1>REGIONAL TRANSPORTERS REPORT - {{ strtoupper($region->name) }}</h1>
        <p>Regional Admin: {{ auth()->user()->name }} ({{ auth()->user()->email }})</p>
        <p>Generated on: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Statistics -->
    <div class="stats-section">
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">Total Transporters</div>
                    <div class="stats-value">{{ number_format($stats['total']) }}</div>
                </td>
                <td>
                    <div class="stats-label">Verified</div>
                    <div class="stats-value">{{ number_format($stats['verified']) }}</div>
                    <div class="stats-subtext">{{ $stats['verified_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">Active</div>
                    <div class="stats-value">{{ number_format($stats['active']) }}</div>
                    <div class="stats-subtext">{{ $stats['active_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">Total Fleet</div>
                    <div class="stats-value">{{ number_format($stats['total_fleet']) }}</div>
                </td>
                <td>
                    <div class="stats-label">Unverified</div>
                    <div class="stats-value">{{ number_format($stats['unverified']) }}</div>
                </td>
                <td>
                    <div class="stats-label">Avg Rating</div>
                    <div class="stats-value">{{ number_format($stats['average_rating'], 1) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Transporters List -->
    <div class="section-title">Transporters List ({{ $transporters->count() }} records)</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 18%;">Company Name</th>
                <th style="width: 12%;">Owner</th>
                <th style="width: 10%;">Country</th>
                <th style="width: 15%;">Contact</th>
                <th style="width: 8%;">Fleet Size</th>
                <th style="width: 8%;">Rating</th>
                <th style="width: 10%;">Registration</th>
                <th style="width: 10%;">Verification</th>
                <th style="width: 9%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transporters as $transporter)
                <tr>
                    <td>
                        <strong>{{ Str::limit($transporter->company_name ?? 'N/A', 30) }}</strong>
                        <br><small>Reg: {{ $transporter->registration_number ?? 'N/A' }}</small>
                    </td>
                    <td>
                        <strong>{{ $transporter->user->name ?? 'N/A' }}</strong>
                        <br><small>{{ $transporter->created_at->format('M d, Y') }}</small>
                    </td>
                    <td>{{ $transporter->country->name ?? 'N/A' }}</td>
                    <td>
                        {{ Str::limit($transporter->email ?? 'N/A', 20) }}
                        @if($transporter->phone)
                            <br><small>{{ $transporter->phone }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($transporter->fleet_size ?? 0) }}</td>
                    <td class="text-center">
                        {{ number_format($transporter->average_rating ?? 0, 1) }}
                        <br><small>({{ $transporter->total_deliveries ?? 0 }} deliveries)</small>
                    </td>
                    <td>{{ $transporter->created_at->format('M d, Y') }}</td>
                    <td>
                        @if($transporter->is_verified)
                            <span class="badge badge-verified">Verified</span>
                        @else
                            <span class="badge badge-unverified">Unverified</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $statusClass = match($transporter->status) {
                                'active' => 'badge-active',
                                'inactive' => 'badge-inactive',
                                'suspended' => 'badge-suspended',
                                default => 'badge-inactive'
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ ucfirst($transporter->status) }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px;">
                        No transporters found
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
                <td>{{ $transporters->count() }}</td>
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
                    Suspended: {{ $stats['suspended'] }} |
                    Inactive: {{ $stats['inactive'] }}
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
                <td style="font-weight: bold;">Fleet & Performance</td>
                <td>
                    Total Fleet: {{ number_format($stats['total_fleet']) }} vehicles |
                    Average Rating: {{ number_format($stats['average_rating'], 1) }}/5.0 |
                    Avg Fleet per Transporter: {{ $stats['total'] > 0 ? number_format($stats['total_fleet'] / $stats['total'], 1) : 0 }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Regional Admin Transporter Management System</p>
        <p>Page 1 of 1 | Report ID: RT-{{ now()->format('Ymd-His') }} | Region: {{ $region->name }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

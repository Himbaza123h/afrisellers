<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Regional Vendors Report - {{ $region->name }} - {{ now()->format('M d, Y') }}</title>
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
        .badge-unverified { background-color: #fed7aa; color: #9a3412; }
        .badge-active { background-color: #d1fae5; color: #065f46; }
        .badge-suspended { background-color: #fee2e2; color: #991b1b; }
        .badge-inactive { background-color: #f3f4f6; color: #374151; }

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
        <h1>REGIONAL VENDORS REPORT - {{ strtoupper($region->name) }}</h1>
        <p>Regional Admin: {{ auth()->user()->name }} ({{ auth()->user()->email }})</p>
        <p>Generated on: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Statistics -->
    <div class="stats-section">
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">Total Vendors</div>
                    <div class="stats-value">{{ number_format($stats['total']) }}</div>
                </td>
                <td>
                    <div class="stats-label">Active Vendors</div>
                    <div class="stats-value">{{ number_format($stats['active']) }}</div>
                    <div class="stats-subtext">{{ $stats['active_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">Suspended</div>
                    <div class="stats-value">{{ number_format($stats['suspended']) }}</div>
                </td>
                <td>
                    <div class="stats-label">Email Verified</div>
                    <div class="stats-value">{{ number_format($stats['email_verified']) }}</div>
                </td>
                <td>
                    <div class="stats-label">Business Verified</div>
                    <div class="stats-value">{{ number_format($stats['business_verified']) }}</div>
                    <div class="stats-subtext">{{ $stats['verified_percentage'] }}% of total</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Vendors List -->
    <div class="section-title">Vendors List ({{ $vendors->count() }} records)</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 20%;">Business Name</th>
                <th style="width: 12%;">Country</th>
                <th style="width: 15%;">Owner</th>
                <th style="width: 12%;">Contact</th>
                <th style="width: 10%;">Email Status</th>
                <th style="width: 12%;">Business Status</th>
                <th style="width: 10%;">Account Status</th>
                <th style="width: 9%;">Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vendors as $vendor)
                <tr>
                    <td><strong>{{ $vendor->businessProfile->business_name ?? 'N/A' }}</strong></td>
                    <td>{{ $vendor->businessProfile->country->name ?? 'N/A' }}</td>
                    <td>
                        <strong>{{ $vendor->user->name ?? 'N/A' }}</strong>
                        <br><small>{{ $vendor->user->email ?? 'N/A' }}</small>
                    </td>
                    <td>
                        @if($vendor->businessProfile && $vendor->businessProfile->phone)
                            {{ $vendor->businessProfile->full_phone }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if($vendor->email_verified)
                            <span class="badge badge-verified">Verified</span>
                        @else
                            <span class="badge badge-unverified">Unverified</span>
                        @endif
                    </td>
                    <td>
                        @if($vendor->businessProfile && $vendor->businessProfile->is_admin_verified)
                            <span class="badge badge-verified">Verified</span>
                        @else
                            <span class="badge badge-unverified">Unverified</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $statusClass = match($vendor->account_status) {
                                'active' => 'badge-active',
                                'suspended' => 'badge-suspended',
                                'inactive' => 'badge-inactive',
                                default => 'badge-inactive'
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ ucfirst($vendor->account_status) }}</span>
                    </td>
                    <td>{{ $vendor->created_at->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px;">
                        No vendors found
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
                <td>{{ $vendors->count() }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Region</td>
                <td>{{ $region->name }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Countries Covered</td>
                <td>{{ $region->countries->pluck('name')->join(', ') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Generated By</td>
                <td>{{ auth()->user()->name }} ({{ auth()->user()->email }})</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Account Status Distribution</td>
                <td>
                    Active: {{ $stats['active'] }} ({{ $stats['active_percentage'] }}%) |
                    Suspended: {{ $stats['suspended'] }} |
                    Email Verified: {{ $stats['email_verified'] }}
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Business Verification</td>
                <td>
                    Verified: {{ $stats['business_verified'] }} ({{ $stats['verified_percentage'] }}%) |
                    Unverified: {{ $stats['total'] - $stats['business_verified'] }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Regional Admin Vendor Management System</p>
        <p>Page 1 of 1 | Report ID: RV-{{ now()->format('Ymd-His') }} | Region: {{ $region->name }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

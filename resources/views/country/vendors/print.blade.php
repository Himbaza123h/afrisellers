<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Vendors Report - {{ $country->name }} - {{ now()->format('M d, Y') }}</title>
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

        .badge-active {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-suspended {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-inactive {
            background-color: #e5e7eb;
            color: #374151;
        }

        .badge-verified {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-pending {
            background-color: #fed7aa;
            color: #9a3412;
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
        <h1>VENDORS REPORT - {{ strtoupper($country->name) }}</h1>
        <p>Country Admin: {{ auth()->user()->name }} ({{ auth()->user()->email }})</p>
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
                    <div class="stats-subtext">Vendors</div>
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
                <th style="width: 8%;">ID</th>
                <th style="width: 20%;">Business Name</th>
                <th style="width: 15%;">Owner</th>
                <th style="width: 15%;">Email</th>
                <th style="width: 12%;">Phone</th>
                <th style="width: 10%;">Email Status</th>
                <th style="width: 10%;">Business Status</th>
                <th style="width: 10%;">Account Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vendors as $vendor)
                <tr>
                    <td><code>#{{ $vendor->id }}</code></td>
                    <td><strong>{{ $vendor->businessProfile->business_name ?? 'N/A' }}</strong></td>
                    <td>{{ $vendor->user->name ?? 'N/A' }}</td>
                    <td>{{ $vendor->user->email ?? 'N/A' }}</td>
                    <td>{{ $vendor->businessProfile->phone ?? 'N/A' }}</td>
                    <td>
                        @if($vendor->email_verified)
                            <span class="badge badge-verified">Verified</span>
                        @else
                            <span class="badge badge-pending">Pending</span>
                        @endif
                    </td>
                    <td>
                        @if($vendor->businessProfile && $vendor->businessProfile->is_admin_verified)
                            <span class="badge badge-verified">Verified</span>
                        @else
                            <span class="badge badge-pending">Unverified</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $badgeClass = match($vendor->account_status) {
                                'active' => 'badge-active',
                                'suspended' => 'badge-suspended',
                                'inactive' => 'badge-inactive',
                                default => 'badge-inactive'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ ucfirst($vendor->account_status) }}</span>
                    </td>
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
                <td style="font-weight: bold;">Country</td>
                <td>{{ $country->name }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Generated By</td>
                <td>{{ auth()->user()->name }} ({{ auth()->user()->email }})</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Account Status Distribution</td>
                <td>
                    Active: {{ $stats['active'] }} |
                    Suspended: {{ $stats['suspended'] }} |
                    Email Verified: {{ $stats['email_verified'] }}
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Business Verification Rate</td>
                <td>{{ $stats['verified_percentage'] }}% ({{ $stats['business_verified'] }} of {{ $stats['total'] }})</td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Country Admin Vendor Management System</p>
        <p>Page 1 of 1 | Report ID: VEND-{{ now()->format('Ymd-His') }} | Country: {{ $country->name }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

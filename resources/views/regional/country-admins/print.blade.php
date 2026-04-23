<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Regional Country Admins Report - {{ $region->name }} - {{ now()->format('M d, Y') }}</title>
    <style>
        @page {
            size: A4 portrait;
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

        .badge-active { background-color: #d1fae5; color: #065f46; }
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
        <h1>REGIONAL COUNTRY ADMINS REPORT - {{ strtoupper($region->name) }}</h1>
        <p>Regional Admin: {{ auth()->user()->name }} ({{ auth()->user()->email }})</p>
        <p>Generated on: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Statistics -->
    <div class="stats-section">
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">Total Admins</div>
                    <div class="stats-value">{{ number_format($stats['total']) }}</div>
                </td>
                <td>
                    <div class="stats-label">Active</div>
                    <div class="stats-value">{{ number_format($stats['active']) }}</div>
                    <div class="stats-subtext">{{ $stats['active_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">Inactive</div>
                    <div class="stats-value">{{ number_format($stats['inactive']) }}</div>
                </td>
                <td>
                    <div class="stats-label">Countries</div>
                    <div class="stats-value">{{ $countries->count() }}</div>
                    <div class="stats-subtext">In {{ $region->name }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Country Admins List -->
    <div class="section-title">Country Administrators List ({{ $countryAdmins->count() }} records)</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 25%;">Name</th>
                <th style="width: 20%;">Country</th>
                <th style="width: 25%;">Email</th>
                <th style="width: 15%;">Phone</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 15%;">Created Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($countryAdmins as $admin)
                <tr>
                    <td>
                        <strong>{{ $admin->name ?? 'N/A' }}</strong>
                        <br><small>ID: #{{ $admin->id }}</small>
                    </td>
                    <td>{{ $admin->country->name ?? 'N/A' }}</td>
                    <td>{{ $admin->email ?? 'N/A' }}</td>
                    <td>{{ $admin->phone ?? 'N/A' }}</td>
                    <td>
                        @if($admin->deleted_at === null)
                            <span class="badge badge-active">Active</span>
                        @else
                            <span class="badge badge-inactive">Inactive</span>
                        @endif
                    </td>
                    <td>{{ $admin->created_at ? $admin->created_at->format('M d, Y') : 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px;">
                        No country administrators found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Countries Coverage -->
    <div class="section-title">Countries Coverage</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 30%;">Country</th>
                <th style="width: 20%;">Country Code</th>
                <th style="width: 30%;">Administrator</th>
                <th style="width: 20%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($countries as $country)
                @php
                    $admin = $countryAdmins->where('country_id', $country->id)->first();
                @endphp
                <tr>
                    <td><strong>{{ $country->name }}</strong></td>
                    <td>{{ $country->code ?? 'N/A' }}</td>
                    <td>
                        @if($admin)
                            {{ $admin->name }}
                            <br><small>{{ $admin->email }}</small>
                        @else
                            <em style="color: #999;">No admin assigned</em>
                        @endif
                    </td>
                    <td>
                        @if($admin)
                            <span class="badge badge-active">Assigned</span>
                        @else
                            <span class="badge" style="background-color: #fef3c7; color: #92400e;">Unassigned</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary -->
    <div class="section-title">Report Summary</div>
    <table class="main-table">
        <tbody>
            <tr>
                <td style="width: 25%; font-weight: bold;">Total Records</td>
                <td>{{ $countryAdmins->count() }}</td>
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
                    Inactive: {{ $stats['inactive'] }}
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Coverage Rate</td>
                <td>
                    @php
                        $assignedCountries = $countryAdmins->pluck('country_id')->unique()->count();
                        $coverage = $countries->count() > 0 ? round(($assignedCountries / $countries->count()) * 100) : 0;
                    @endphp
                    {{ $assignedCountries }} out of {{ $countries->count() }} countries ({{ $coverage }}%)
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Regional Admin Country Administrator Management System</p>
        <p>Page 1 of 1 | Report ID: CA-{{ now()->format('Ymd-His') }} | Region: {{ $region->name }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Audit Logs Report - {{ now()->format('M d, Y') }}</title>
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

        .report-date {
            text-align: right;
            font-size: 10px;
            margin-bottom: 10px;
            color: #666;
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

        .badge-create {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-update {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-delete {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-view {
            background-color: #fef3c7;
            color: #92400e;
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
        <h1>AUDIT LOGS REPORT</h1>
        <p>System Activity Tracking Report</p>
        <p>Generated on: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Statistics Section -->
    <div class="stats-section">
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">Total Logs</div>
                    <div class="stats-value">{{ number_format($stats['total_logs']) }}</div>
                </td>
                <td>
                    <div class="stats-label">Today's Logs</div>
                    <div class="stats-value">{{ number_format($stats['today_logs']) }}</div>
                    <div class="stats-subtext">Current Day</div>
                </td>
                <td>
                    <div class="stats-label">This Week</div>
                    <div class="stats-value">{{ number_format($stats['this_week_logs']) }}</div>
                    <div class="stats-subtext">Current Week</div>
                </td>
                <td>
                    <div class="stats-label">Action Types</div>
                    <div class="stats-value">{{ count($stats['actions_by_type']) }}</div>
                    <div class="stats-subtext">Unique Actions</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Action Distribution -->
    <div class="section-title">Action Type Distribution</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 20%;">Action Type</th>
                <th style="width: 15%;" class="text-right">Count</th>
                <th style="width: 15%;" class="text-right">Percentage</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalLogs = $stats['total_logs'];
            @endphp
            @foreach($stats['actions_by_type'] as $action => $count)
                <tr>
                    <td>{{ ucfirst($action) }}</td>
                    <td class="text-right">{{ $count }}</td>
                    <td class="text-right">{{ $totalLogs > 0 ? number_format(($count / $totalLogs) * 100, 1) : 0 }}%</td>
                </tr>
            @endforeach
            @if($totalLogs > 0)
                <tr>
                    <td style="font-weight: bold;">Total</td>
                    <td class="text-right" style="font-weight: bold;">{{ $totalLogs }}</td>
                    <td class="text-right" style="font-weight: bold;">100%</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Audit Logs Data -->
    <div class="section-title">Audit Logs Details</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 15%;">User</th>
                <th style="width: 10%;">Action</th>
                <th style="width: 15%;">Model</th>
                <th style="width: 25%;">Description</th>
                <th style="width: 15%;">IP Address</th>
                <th style="width: 10%;">Date</th>
                <th style="width: 10%;">Time</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td>
                        <strong>{{ $log->user ? $log->user->name : 'System' }}</strong>
                        @if($log->user && $log->user->email)
                            <br><small>{{ $log->user->email }}</small>
                        @endif
                    </td>
                    <td>
                        @php
                            $badgeClass = match($log->action) {
                                'created' => 'badge-create',
                                'updated' => 'badge-update',
                                'deleted' => 'badge-delete',
                                default => 'badge-view'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ ucfirst($log->action) }}</span>
                    </td>
                    <td>
                        @if($log->model_type)
                            {{ class_basename($log->model_type) }}
                        @else
                            <span class="text-gray-400">N/A</span>
                        @endif
                    </td>
                    <td>{{ Str::limit($log->description, 60) }}</td>
                    <td>
                        @if($log->ip_address)
                            <code>{{ $log->ip_address }}</code>
                        @else
                            <span class="text-gray-400">N/A</span>
                        @endif
                    </td>
                    <td>{{ $log->created_at->format('M d, Y') }}</td>
                    <td>{{ $log->created_at->format('h:i A') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px;">
                        No audit logs found
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
                <td>{{ $logs->count() }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Date Range</td>
                <td>
                    @if(request('date_from') && request('date_to'))
                        {{ \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') }} to {{ \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') }}
                    @else
                        All dates
                    @endif
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Filtered Actions</td>
                <td>{{ request('action') ? ucfirst(request('action')) : 'All actions' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Filtered Models</td>
                <td>{{ request('model') ? request('model') : 'All models' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Search Term</td>
                <td>{{ request('search') ?: 'None' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Audit Log System - Administrator Report</p>
        <p>Page 1 of 1 | Total Records: {{ $logs->count() }} | Report ID: AUD-{{ now()->format('Ymd-His') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

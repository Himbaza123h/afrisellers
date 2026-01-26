<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Escrow Management Report</title>
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

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-active {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-released {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-refunded {
            background-color: #ede9fe;
            color: #5b21b6;
        }

        .status-disputed {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-cancelled {
            background-color: #f3f4f6;
            color: #374151;
        }

        .type-order {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .type-service {
            background-color: #ede9fe;
            color: #5b21b6;
        }

        .type-milestone {
            background-color: #e0e7ff;
            color: #3730a3;
        }

        .type-custom {
            background-color: #f3f4f6;
            color: #374151;
        }

        .badge-disputed {
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
        <h1>ESCROW MANAGEMENT REPORT</h1>
        <p>Complete Overview of All Escrow Transactions</p>
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
                    <div class="stats-label">Total Escrows</div>
                    <div class="stats-value">{{ number_format($stats['total']) }}</div>
                </td>
                <td>
                    <div class="stats-label">Pending</div>
                    <div class="stats-value">{{ number_format($stats['pending']) }}</div>
                    <div class="stats-subtext" style="color: #92400e;">Awaiting</div>
                </td>
                <td>
                    <div class="stats-label">Active</div>
                    <div class="stats-value">{{ number_format($stats['active']) }}</div>
                    <div class="stats-subtext" style="color: #1e40af;">Held</div>
                </td>
                <td>
                    <div class="stats-label">Released</div>
                    <div class="stats-value">{{ number_format($stats['released']) }}</div>
                    <div class="stats-subtext" style="color: #065f46;">Complete</div>
                </td>
            </tr>
        </table>

        <!-- Financial Row -->
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">Total Held</div>
                    <div class="stats-value">${{ number_format($stats['total_held'], 2) }}</div>
                    <div class="stats-subtext" style="color: #f59e0b;">In Escrow</div>
                </td>
                <td>
                    <div class="stats-label">Total Released</div>
                    <div class="stats-value">${{ number_format($stats['total_released'], 2) }}</div>
                    <div class="stats-subtext" style="color: #059669;">Completed</div>
                </td>
                <td>
                    <div class="stats-label">Disputed</div>
                    <div class="stats-value">{{ number_format($stats['disputed']) }}</div>
                    <div class="stats-subtext" style="color: #dc2626;">Issues</div>
                </td>
                <td>
                    <div class="stats-label">Awaiting Release</div>
                    <div class="stats-value">{{ number_format($stats['awaiting_release']) }}</div>
                    <div class="stats-subtext" style="color: #7c3aed;">Ready</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Main Table -->
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 3%;">#</th>
                <th style="width: 12%;">Escrow Number</th>
                <th style="width: 20%;">Parties</th>
                <th style="width: 12%;">Amount</th>
                <th style="width: 10%;">Type</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 10%;">Disputed</th>
                <th style="width: 13%;">Timeline</th>
                <th style="width: 10%;">Days Held</th>
            </tr>
        </thead>
        <tbody>
            @forelse($escrows as $index => $escrow)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $escrow->escrow_number }}</strong>
                        <br><small>{{ $escrow->created_at->format('M d, Y') }}</small>
                    </td>
                    <td>
                        <strong>Buyer:</strong> {{ $escrow->buyer->name ?? 'N/A' }}
                        <br><strong>Vendor:</strong> {{ $escrow->vendor->name ?? 'N/A' }}
                    </td>
                    <td>
                        <strong>{{ $escrow->currency }} {{ number_format($escrow->amount, 2) }}</strong>
                        <br><small>Vendor: ${{ number_format($escrow->vendor_amount, 2) }}</small>
                        @if($escrow->platform_fee > 0)
                            <br><small>Fee: ${{ number_format($escrow->platform_fee, 2) }}</small>
                        @endif
                    </td>
                    <td>
                        @php
                            $typeClasses = [
                                'order' => 'type-order',
                                'service' => 'type-service',
                                'milestone' => 'type-milestone',
                                'custom' => 'type-custom',
                            ];
                            $typeClass = $typeClasses[$escrow->escrow_type] ?? 'type-order';
                        @endphp
                        <span class="status-badge {{ $typeClass }}">
                            {{ ucfirst($escrow->escrow_type) }}
                        </span>
                    </td>
                    <td>
                        @php
                            $statusClasses = [
                                'pending' => 'status-pending',
                                'active' => 'status-active',
                                'released' => 'status-released',
                                'refunded' => 'status-refunded',
                                'disputed' => 'status-disputed',
                                'cancelled' => 'status-cancelled',
                            ];
                            $statusClass = $statusClasses[$escrow->status] ?? 'status-pending';
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ ucfirst($escrow->status) }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if($escrow->disputed)
                            <span class="badge-disputed status-badge">Disputed</span>
                        @else
                            <small>No</small>
                        @endif
                    </td>
                    <td>
                        @if($escrow->held_at)
                            Held: {{ $escrow->held_at->format('M d') }}
                            <br>
                        @endif
                        @if($escrow->expected_release_at && $escrow->status === 'active')
                            Release: {{ $escrow->expected_release_at->format('M d') }}
                            <br>
                        @endif
                        @if($escrow->released_at)
                            <strong>Released: {{ $escrow->released_at->format('M d') }}</strong>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($escrow->held_at)
                            <strong>{{ $escrow->days_held ?? 0 }}</strong>
                            <br><small>days</small>
                        @else
                            <small>-</small>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px;">
                        No escrows found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Escrow Management System - Administrator Report</p>
        <p>Page 1 of 1 | Total Records: {{ $escrows->count() }} | Report ID: ESC-{{ now()->format('Ymd-His') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

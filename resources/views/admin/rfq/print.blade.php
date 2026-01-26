<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>RFQs Management Report</title>
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

        .status-accepted {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-closed {
            background-color: #f3f4f6;
            color: #1f2937;
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
        <h1>RFQs MANAGEMENT REPORT</h1>
        <p>Complete Overview of All Request for Quotations</p>
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
                    <div class="stats-label">Total RFQs</div>
                    <div class="stats-value">{{ $stats['total'] }}</div>
                </td>
                <td>
                    <div class="stats-label">Pending RFQs</div>
                    <div class="stats-value">{{ $stats['pending'] }}</div>
                    <div class="stats-subtext" style="color: #92400e;">{{ $stats['pending_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">Accepted RFQs</div>
                    <div class="stats-value">{{ $stats['accepted'] }}</div>
                    <div class="stats-subtext" style="color: #065f46;">{{ $stats['accepted_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">Response Rate</div>
                    <div class="stats-value">{{ $stats['response_rate'] }}%</div>
                    <div class="stats-subtext" style="color: #0d9488;">With vendor messages</div>
                </td>
                <td>
                    <div class="stats-label">This Week</div>
                    <div class="stats-value">{{ $stats['this_week'] }}</div>
                    <div class="stats-subtext" style="color: #7c3aed;">Weekly activity</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Main Table -->
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 3%;">#</th>
                <th style="width: 12%;">RFQ Number</th>
                <th style="width: 20%;">Buyer Information</th>
                <th style="width: 15%;">Product</th>
                <th style="width: 10%;" class="text-center">Messages</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 15%;">Inquiry Message</th>
                <th style="width: 15%;">Created Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rfqs as $index => $rfq)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>#RFQ-{{ str_pad($rfq->id, 6, '0', STR_PAD_LEFT) }}</strong>
                    </td>
                    <td>
                        <strong>{{ $rfq->name ?? 'N/A' }}</strong>
                        @if($rfq->email)
                            <br><small>{{ $rfq->email }}</small>
                        @endif
                        @if($rfq->phone)
                            <br><small>{{ $rfq->phone_code }} {{ $rfq->phone }}</small>
                        @endif
                        @if($rfq->country)
                            <br><small>{{ $rfq->country->name }}</small>
                        @endif
                    </td>
                    <td>
                        {{ $rfq->product ? $rfq->product->name : 'General Inquiry' }}
                        @if($rfq->product && $rfq->product->sku)
                            <br><small>SKU: {{ $rfq->product->sku }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $rfq->messages_count }}</td>
                    <td>
                        @php
                            $statusClasses = [
                                'pending' => 'status-pending',
                                'accepted' => 'status-accepted',
                                'rejected' => 'status-rejected',
                                'closed' => 'status-closed',
                            ];
                            $statusClass = $statusClasses[$rfq->status] ?? 'status-pending';
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ ucfirst($rfq->status) }}
                        </span>
                    </td>
                    <td>
                        {{ \Illuminate\Support\Str::limit($rfq->message, 100) }}
                    </td>
                    <td>
                        {{ $rfq->created_at->format('M d, Y') }}
                        <br>
                        <small>{{ $rfq->created_at->format('h:i A') }}</small>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px;">
                        No RFQs found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>RFQs Management System - Administrator Report</p>
        <p>Page 1 of 1 | Total Records: {{ $rfqs->count() }} | Report ID: RFQ-{{ now()->format('Ymd-His') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

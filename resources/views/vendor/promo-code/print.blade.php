<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Promo Codes Report - {{ now()->format('M d, Y') }}</title>
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

        .badge-inactive {
            background-color: #e5e7eb;
            color: #374151;
        }

        .badge-expired {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-exhausted {
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
        <h1>PROMO CODES REPORT</h1>
        <p>Promotional Discount Codes Overview</p>
        <p>Generated on: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Statistics Section -->
    <div class="stats-section">
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">Total Codes</div>
                    <div class="stats-value">{{ number_format($stats['total']) }}</div>
                </td>
                <td>
                    <div class="stats-label">Active Codes</div>
                    <div class="stats-value">{{ number_format($stats['active']) }}</div>
                    <div class="stats-subtext">{{ $stats['total'] > 0 ? round(($stats['active'] / $stats['total']) * 100, 1) : 0 }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">Inactive Codes</div>
                    <div class="stats-value">{{ number_format($stats['inactive']) }}</div>
                    <div class="stats-subtext">Paused</div>
                </td>
                <td>
                    <div class="stats-label">Expired Codes</div>
                    <div class="stats-value">{{ number_format($stats['expired']) }}</div>
                    <div class="stats-subtext">Past validity</div>
                </td>
                <td>
                    <div class="stats-label">Total Usage</div>
                    <div class="stats-value">{{ number_format($stats['total_uses']) }}</div>
                    <div class="stats-subtext">All time</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Discount Type Distribution -->
    @if($discountTypeDistribution->count() > 0)
        <div class="section-title">Discount Type Distribution</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 30%;">Discount Type</th>
                    <th style="width: 20%;" class="text-right">Count</th>
                    <th style="width: 20%;" class="text-right">Percentage</th>
                </tr>
            </thead>
            <tbody>
                @php $totalCodes = $stats['total']; @endphp
                @foreach($discountTypeDistribution as $type)
                    <tr>
                        <td>{{ ucfirst($type->discount_type) }}</td>
                        <td class="text-right">{{ $type->count }}</td>
                        <td class="text-right">{{ $totalCodes > 0 ? number_format(($type->count / $totalCodes) * 100, 1) : 0 }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Promo Codes List -->
    <div class="section-title">Promo Codes Details</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 12%;">Code</th>
                <th style="width: 12%;">Discount</th>
                <th style="width: 12%;">Min Purchase</th>
                <th style="width: 18%;">Validity Period</th>
                <th style="width: 12%;">Usage</th>
                <th style="width: 10%;">Products</th>
                <th style="width: 12%;">Status</th>
                <th style="width: 12%;">Created</th>
            </tr>
        </thead>
        <tbody>
            @forelse($promoCodes as $promo)
                <tr>
                    <td>
                        <strong>{{ $promo->code }}</strong>
                        @if($promo->description)
                            <br><span style="font-size: 9px; color: #666;">{{ Str::limit($promo->description, 30) }}</span>
                        @endif
                    </td>
                    <td>
                        <strong style="color: #dc2626;">
                            @if($promo->discount_type === 'percentage')
                                {{ $promo->discount_value }}% off
                            @else
                                {{ $promo->currency }} {{ number_format($promo->discount_value, 2) }}
                            @endif
                        </strong>
                        @if($promo->discount_type === 'percentage' && $promo->max_discount_amount)
                            <br><span style="font-size: 9px;">Max: {{ $promo->currency }} {{ number_format($promo->max_discount_amount, 2) }}</span>
                        @endif
                    </td>
                    <td>
                        @if($promo->min_purchase_amount)
                            {{ $promo->currency }} {{ number_format($promo->min_purchase_amount, 2) }}
                        @else
                            <span style="color: #999;">No minimum</span>
                        @endif
                    </td>
                    <td>
                        <strong>Start:</strong> {{ $promo->start_date->format('M d, Y') }}<br>
                        <strong>End:</strong> {{ $promo->end_date->format('M d, Y') }}
                        @php
                            $now = now();
                            $daysLeft = round($now->diffInDays($promo->end_date, false));
                        @endphp
                        @if($promo->end_date->isPast())
                            <br><span style="color: #dc2626; font-weight: bold;">Expired</span>
                        @elseif($promo->start_date->isFuture())
                            <br><span style="color: #2563eb;">Starts in {{ $promo->start_date->diffInDays($now) }}d</span>
                        @else
                            <br><span style="color: #059669;">{{ $daysLeft }}d remaining</span>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $promo->usage_count }}</strong>
                        @if($promo->usage_limit)
                            / {{ $promo->usage_limit }}
                        @else
                            <br><span style="font-size: 9px; color: #666;">Unlimited</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $promo->products_count }}</td>
                    <td>
                        @php
                            $isExpired = $promo->end_date->isPast();
                            $isExhausted = $promo->usage_limit && $promo->usage_count >= $promo->usage_limit;

                            if ($isExpired) {
                                $badgeClass = 'badge-expired';
                                $statusText = 'Expired';
                            } elseif ($isExhausted) {
                                $badgeClass = 'badge-exhausted';
                                $statusText = 'Exhausted';
                            } else {
                                $badgeClass = $promo->status === 'active' ? 'badge-active' : 'badge-inactive';
                                $statusText = ucfirst($promo->status);
                            }
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                    </td>
                    <td>{{ $promo->created_at->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px;">
                        No promo codes found
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
                <td>{{ $promoCodes->count() }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Date Range</td>
                <td>
                    @if(request('date_range'))
                        {{ request('date_range') }}
                    @else
                        All dates
                    @endif
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Filtered Status</td>
                <td>{{ request('status') ? ucfirst(request('status')) : 'All statuses' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Filtered Type</td>
                <td>{{ request('discount_type') ? ucfirst(request('discount_type')) : 'All types' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Search Term</td>
                <td>{{ request('search') ?: 'None' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Promo Code Management System - Vendor Report</p>
        <p>Page 1 of 1 | Total Records: {{ $promoCodes->count() }} | Report ID: PMC-{{ now()->format('Ymd-His') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>


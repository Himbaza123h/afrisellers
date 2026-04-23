<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Addons Report - {{ now()->format('M d, Y') }}</title>
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

        .badge-expired {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-product {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-supplier {
            background-color: #e9d5ff;
            color: #6b21a8;
        }

        .badge-showroom {
            background-color: #e0e7ff;
            color: #3730a3;
        }

        .badge-tradeshow {
            background-color: #cffafe;
            color: #155e75;
        }

        .badge-loadboad {
            background-color: #fed7aa;
            color: #9a3412;
        }

        .badge-car {
            background-color: #d1fae5;
            color: #065f46;
        }

        .highlight {
            background-color: #fef3c7;
            font-weight: bold;
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
        <h1>ADDONS MANAGEMENT REPORT</h1>
        <p>Promotional Addon Subscriptions Overview</p>
        <p>Generated on: {{ now()->format('d/m/Y H:i:s') }}</p>
        @if(request('date_range'))
            <p>Period: {{ request('date_range') }}</p>
        @endif
    </div>

    <!-- Statistics Section -->
    <div class="stats-section">
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">Total Addons</div>
                    <div class="stats-value">{{ $stats['total'] }}</div>
                    <div class="stats-subtext">All time subscriptions</div>
                </td>
                <td>
                    <div class="stats-label">Active</div>
                    <div class="stats-value">{{ $stats['active'] }}</div>
                    <div class="stats-subtext">
                        <span class="badge badge-active">{{ $stats['active_percentage'] }}%</span>
                        of total
                    </div>
                </td>
                <td>
                    <div class="stats-label">Expired</div>
                    <div class="stats-value">{{ $stats['expired'] }}</div>
                    <div class="stats-subtext">
                        <span class="badge badge-expired">{{ $stats['expired_percentage'] }}%</span>
                        of total
                    </div>
                </td>
                <td>
                    <div class="stats-label">Pending</div>
                    <div class="stats-value">{{ $stats['pending'] }}</div>
                    <div class="stats-subtext">Unpaid subscriptions</div>
                </td>
            </tr>
        </table>

        <!-- Spending Stats -->
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">Total Spent</div>
                    <div class="stats-value">${{ number_format($stats['total_spent'], 2) }}</div>
                    <div class="stats-subtext">All purchases</div>
                </td>
                <td>
                    <div class="stats-label">Active Value</div>
                    <div class="stats-value">${{ number_format($stats['active_value'], 2) }}</div>
                    <div class="stats-subtext">Current promotions</div>
                </td>
                <td>
                    <div class="stats-label">Average Per Addon</div>
                    <div class="stats-value">
                        @php
                            $avgPrice = $stats['total'] > 0 ? $stats['total_spent'] / $stats['total'] : 0;
                        @endphp
                        ${{ number_format($avgPrice, 2) }}
                    </div>
                    <div class="stats-subtext">Per subscription</div>
                </td>
                <td>
                    <div class="stats-label">Total Records</div>
                    <div class="stats-value">{{ $addonUsers->count() }}</div>
                    <div class="stats-subtext">In this report</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Type Distribution -->
    @if($typeDistribution->count() > 0)
        <div class="section-title">Addon Type Distribution</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 30%;">Type</th>
                    <th style="width: 20%;" class="text-right">Count</th>
                    <th style="width: 20%;" class="text-right">% of Total</th>
                    <th style="width: 30%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($typeDistribution as $type)
                    @php
                        $typeLabels = [
                            'product' => 'Product',
                            'supplier' => 'Supplier',
                            'showroom' => 'Showroom',
                            'tradeshow' => 'Tradeshow',
                            'loadboad' => 'Load Board',
                            'car' => 'Car',
                        ];
                        $typeBadges = [
                            'product' => 'badge-product',
                            'supplier' => 'badge-supplier',
                            'showroom' => 'badge-showroom',
                            'tradeshow' => 'badge-tradeshow',
                            'loadboad' => 'badge-loadboad',
                            'car' => 'badge-car',
                        ];
                    @endphp
                    <tr>
                        <td>
                            <span class="badge {{ $typeBadges[$type->type] ?? '' }}">
                                {{ $typeLabels[$type->type] ?? ucfirst($type->type) }}
                            </span>
                        </td>
                        <td class="text-right">{{ $type->count }}</td>
                        <td class="text-right">
                            {{ $stats['total'] > 0 ? number_format(($type->count / $stats['total']) * 100, 1) : 0 }}%
                        </td>
                        <td>
                            @php
                                $activeCount = \App\Models\AddonUser::where('user_id', auth()->id())
                                    ->where('type', $type->type)
                                    ->whereNotNull('paid_at')
                                    ->where('ended_at', '>', now())
                                    ->count();
                            @endphp
                            {{ $activeCount }} active
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Status Distribution -->
    <div class="section-title">Status Distribution</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 30%;">Status</th>
                <th style="width: 20%;" class="text-right">Count</th>
                <th style="width: 20%;" class="text-right">% of Total</th>
                <th style="width: 30%;">Badge</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Active</strong></td>
                <td class="text-right">{{ $statusDistribution['active'] }}</td>
                <td class="text-right">
                    {{ $stats['total'] > 0 ? number_format(($statusDistribution['active'] / $stats['total']) * 100, 1) : 0 }}%
                </td>
                <td><span class="badge badge-active">ACTIVE</span></td>
            </tr>
            <tr>
                <td><strong>Expired</strong></td>
                <td class="text-right">{{ $statusDistribution['expired'] }}</td>
                <td class="text-right">
                    {{ $stats['total'] > 0 ? number_format(($statusDistribution['expired'] / $stats['total']) * 100, 1) : 0 }}%
                </td>
                <td><span class="badge badge-expired">EXPIRED</span></td>
            </tr>
            <tr>
                <td><strong>Pending</strong></td>
                <td class="text-right">{{ $statusDistribution['pending'] }}</td>
                <td class="text-right">
                    {{ $stats['total'] > 0 ? number_format(($statusDistribution['pending'] / $stats['total']) * 100, 1) : 0 }}%
                </td>
                <td><span class="badge badge-pending">PENDING</span></td>
            </tr>
        </tbody>
    </table>

    <!-- Addons Details List -->
    <div class="section-title">Addons Details</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 15%;">Location</th>
                <th style="width: 10%;">Type</th>
                <th style="width: 15%;">Item</th>
                <th style="width: 10%;">Duration</th>
                <th style="width: 12%;">Expires</th>
                <th style="width: 10%;" class="text-right">Price</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 13%;">Purchase Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($addonUsers as $addonUser)
                <tr>
                    <td>
                        <strong>{{ $addonUser->addon->locationX }}</strong><br>
                        <small style="color: #666;">{{ ucfirst(str_replace('_', ' ', $addonUser->addon->locationY)) }}</small><br>
                        <small style="color: #999;">
                            @if($addonUser->addon->country)
                                {{ $addonUser->addon->country->name }}
                            @else
                                Global
                            @endif
                        </small>
                    </td>
                    <td>
                        @php
                            $typeLabels = [
                                'product' => ['Product', 'badge-product'],
                                'supplier' => ['Supplier', 'badge-supplier'],
                                'showroom' => ['Showroom', 'badge-showroom'],
                                'tradeshow' => ['Tradeshow', 'badge-tradeshow'],
                                'loadboad' => ['Load Board', 'badge-loadboad'],
                                'car' => ['Car', 'badge-car'],
                            ];
                            $typeInfo = $typeLabels[$addonUser->type] ?? ['Unknown', ''];
                        @endphp
                        <span class="badge {{ $typeInfo[1] }}">{{ $typeInfo[0] }}</span>
                    </td>
                    <td>
                        @php
                            $relatedEntity = null;
                            switch($addonUser->type) {
                                case 'product':
                                    $relatedEntity = $addonUser->product;
                                    break;
                                case 'supplier':
                                    $relatedEntity = $addonUser->supplier;
                                    break;
                                case 'showroom':
                                    $relatedEntity = $addonUser->showroom;
                                    break;
                                case 'tradeshow':
                                    $relatedEntity = $addonUser->tradeshow;
                                    break;
                                case 'loadboad':
                                    $relatedEntity = $addonUser->loadboad;
                                    break;
                                case 'car':
                                    $relatedEntity = $addonUser->car;
                                    break;
                            }
                        @endphp
                        {{ $relatedEntity->name ?? 'N/A' }}
                    </td>
                    <td><strong>{{ $addonUser->paid_days }} days</strong></td>
                    <td>
                        @if($addonUser->ended_at)
                            {{ $addonUser->ended_at->format('M d, Y') }}
                            @if($addonUser->isActive() && $addonUser->days_remaining)
                                <br><small style="color: #ea580c;">{{ round($addonUser->days_remaining) }}d left</small>
                            @endif
                        @else
                            <span style="color: #999;">-</span>
                        @endif
                    </td>
                    <td class="text-right"><strong>${{ number_format($addonUser->addon->price, 2) }}</strong></td>
                    <td>
                        @if($addonUser->isActive())
                            <span class="badge badge-active">ACTIVE</span>
                        @elseif($addonUser->isExpired())
                            <span class="badge badge-expired">EXPIRED</span>
                        @else
                            <span class="badge badge-pending">PENDING</span>
                        @endif
                    </td>
                    <td>
                        @if($addonUser->paid_at)
                            {{ $addonUser->paid_at->format('M d, Y') }}
                        @else
                            <span style="color: #999;">Not paid</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px;">
                        No addons found for this period
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
                <td>{{ $addonUsers->count() }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Filtered Type</td>
                <td>{{ request('type') ? ucwords(request('type')) : 'All types' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Filtered Status</td>
                <td>{{ request('status') ? ucwords(request('status')) : 'All statuses' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Search Term</td>
                <td>{{ request('search') ?: 'None' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Total Investment</td>
                <td><strong>${{ number_format($stats['total_spent'], 2) }}</strong></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Active Promotions Value</td>
                <td><strong>${{ number_format($stats['active_value'], 2) }}</strong></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Average Cost Per Addon</td>
                <td>
                    @php
                        $avgCost = $stats['total'] > 0 ? $stats['total_spent'] / $stats['total'] : 0;
                    @endphp
                    <strong>${{ number_format($avgCost, 2) }}</strong>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Addons Management System - Vendor Report</p>
        <p>Page 1 of 1 | Total Records: {{ $addonUsers->count() }} | Report ID: ADDON-{{ now()->format('Ymd-His') }}</p>
        <p>Generated by: {{ auth()->user()->name }} ({{ auth()->user()->email }})</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

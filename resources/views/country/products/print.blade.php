<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Products Report - {{ $country->name }} - {{ now()->format('M d, Y') }}</title>
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

        .badge-pending {
            background-color: #fed7aa;
            color: #9a3412;
        }

        .badge-inactive {
            background-color: #e5e7eb;
            color: #374151;
        }

        .badge-verified {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-unverified {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-negotiable {
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
        <h1>PRODUCTS REPORT - {{ strtoupper($country->name) }}</h1>
        <p>Country Admin: {{ auth()->user()->name }} ({{ auth()->user()->email }})</p>
        <p>Generated on: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Statistics -->
    <div class="stats-section">
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">Total Products</div>
                    <div class="stats-value">{{ number_format($stats['total']) }}</div>
                </td>
                <td>
                    <div class="stats-label">Active Products</div>
                    <div class="stats-value">{{ number_format($stats['active']) }}</div>
                    <div class="stats-subtext">{{ $stats['active_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">Pending Approval</div>
                    <div class="stats-value">{{ number_format($stats['pending']) }}</div>
                </td>
                <td>
                    <div class="stats-label">Verified Products</div>
                    <div class="stats-value">{{ number_format($stats['verified']) }}</div>
                    <div class="stats-subtext">{{ $stats['verified_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">Total Views</div>
                    <div class="stats-value">{{ number_format($stats['total_views']) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Products List -->
    <div class="section-title">Products List ({{ $products->count() }} records)</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 20%;">Product Name</th>
                <th style="width: 15%;">Vendor</th>
                <th style="width: 12%;">Category</th>
                <th style="width: 15%;" class="text-right">Price Range</th>
                <th style="width: 8%;" class="text-right">Views</th>
                <th style="width: 10%;">Verification</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 10%;">Created Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                <tr>
                    <td><strong>{{ $product->name }}</strong></td>
                    <td>{{ $product->user->name ?? 'N/A' }}</td>
                    <td>{{ $product->productCategory->name ?? 'Uncategorized' }}</td>
                    <td class="text-right">
                        @if($product->prices->count() > 0)
                            @php
                                $minPrice = $product->prices->min('price');
                                $maxPrice = $product->prices->max('price');
                            @endphp
                            ${{ number_format($minPrice, 2) }}
                            @if($minPrice != $maxPrice)
                                - ${{ number_format($maxPrice, 2) }}
                            @endif
                            @if($product->is_negotiable)
                                <div><span class="badge badge-negotiable">Negotiable</span></div>
                            @endif
                        @else
                            <span style="color: #999;">No pricing</span>
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($product->views) }}</td>
                    <td>
                        @if($product->is_admin_verified)
                            <span class="badge badge-verified">Verified</span>
                        @else
                            <span class="badge badge-unverified">Unverified</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $badgeClass = match($product->status) {
                                'active' => 'badge-active',
                                'pending' => 'badge-pending',
                                'inactive' => 'badge-inactive',
                                default => 'badge-inactive'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ ucfirst($product->status) }}</span>
                    </td>
                    <td>{{ $product->created_at->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px;">
                        No products found
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
                <td>{{ $products->count() }}</td>
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
                <td style="font-weight: bold;">Verification Status</td>
                <td>
                    Verified: {{ $stats['verified'] }} ({{ $stats['verified_percentage'] }}%) |
                    Unverified: {{ $stats['unverified'] }}
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Product Status</td>
                <td>
                    Active: {{ $stats['active'] }} ({{ $stats['active_percentage'] }}%) |
                    Pending: {{ $stats['pending'] }} |
                    Total Views: {{ number_format($stats['total_views']) }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Country Admin Product Management System</p>
        <p>Page 1 of 1 | Report ID: PROD-{{ now()->format('Ymd-His') }} | Country: {{ $country->name }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

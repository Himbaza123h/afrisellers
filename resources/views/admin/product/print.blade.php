<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Products Management Report</title>
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

        .text-right {
            text-align: right;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-active {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-inactive {
            background-color: #f3f4f6;
            color: #1f2937;
        }

        .status-draft {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-verified {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-unverified {
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

        .product-image {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border: 1px solid #ddd;
            border-radius: 4px;
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
        <h1>PRODUCTS MANAGEMENT REPORT</h1>
        <p>{{ auth()->user()->hasRole('admin') ? 'Complete Overview of All Products' : 'Your Products Inventory' }}</p>
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
                    <div class="stats-label">Total Products</div>
                    <div class="stats-value">{{ $stats['total'] ?? 0 }}</div>
                </td>
                <td>
                    <div class="stats-label">Active Products</div>
                    <div class="stats-value">{{ $stats['active'] ?? 0 }}</div>
                    <div class="stats-subtext" style="color: #059669;">{{ $stats['active_percentage'] ?? 0 }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">Inactive Products</div>
                    <div class="stats-value">{{ $stats['inactive'] ?? 0 }}</div>
                    <div class="stats-subtext" style="color: #6b7280;">{{ $stats['inactive_percentage'] ?? 0 }}% of total</div>
                </td>
                @if(auth()->user()->hasRole('admin'))
                <td>
                    <div class="stats-label">Verified Products</div>
                    <div class="stats-value">{{ $stats['verified'] ?? 0 }}</div>
                    <div class="stats-subtext" style="color: #0d9488;">{{ $stats['total'] > 0 ? round(($stats['verified'] / $stats['total']) * 100, 1) : 0 }}% verified</div>
                </td>
                <td>
                    <div class="stats-label">Draft Products</div>
                    <div class="stats-value">{{ $stats['draft'] ?? 0 }}</div>
                    <div class="stats-subtext" style="color: #f59e0b;">Unpublished drafts</div>
                </td>
                @else
                <td>
                    <div class="stats-label">Draft Products</div>
                    <div class="stats-value">{{ $stats['draft'] ?? 0 }}</div>
                    <div class="stats-subtext" style="color: #f59e0b;">Unpublished drafts</div>
                </td>
                @endif
            </tr>
        </table>
    </div>

    <!-- Main Table -->
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 3%;">#</th>
                <th style="width: 20%;">Product Details</th>
                <th style="width: 15%;">Vendor Information</th>
                <th style="width: 12%;">Category</th>
                <th style="width: 10%;">Pricing</th>
                <th style="width: 10%;">Status</th>
                @if(auth()->user()->hasRole('admin'))
                <th style="width: 8%;">Verified</th>
                @endif
                <th style="width: 12%;">Created Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $index => $product)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $product->name }}</strong>
                        @if($product->short_description)
                            <br><small>{{ \Illuminate\Support\Str::limit($product->short_description, 60) }}</small>
                        @endif
                        @if($product->sku)
                            <br><small><strong>SKU:</strong> {{ $product->sku }}</small>
                        @endif
                    </td>
                    <td>
                        @php
                            $vendor = $product->user->vendor ?? null;
                            $businessProfile = $vendor->businessProfile ?? null;
                        @endphp
                        <div>
                            @if($businessProfile)
                                <strong>{{ $businessProfile->business_name }}</strong>
                            @elseif($product->user)
                                <strong>{{ $product->user->name }}</strong>
                            @else
                                <strong>N/A</strong>
                            @endif
                            @if($product->user && $product->user->email)
                                <br><small>{{ $product->user->email }}</small>
                            @endif
                        </div>
                    </td>
                    <td>
                        {{ $product->productCategory->name ?? 'Uncategorized' }}
                    </td>
                    <td>
                        @php
                            $firstPriceTier = $product->prices->first();
                        @endphp
                        @if($firstPriceTier)
                            <strong>{{ number_format($firstPriceTier->price, 0) }} {{ $firstPriceTier->currency }}</strong>
                            @if($product->prices->count() > 1)
                                <br><small>{{ $product->prices->count() }} price tiers</small>
                            @endif
                        @else
                            <small>No price set</small>
                        @endif
                    </td>
                    <td>
                        @php
                            $statusClasses = [
                                'active' => 'status-active',
                                'inactive' => 'status-inactive',
                                'draft' => 'status-draft',
                            ];
                            $statusClass = $statusClasses[$product->status] ?? 'status-inactive';
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ ucfirst($product->status) }}
                        </span>
                    </td>
                    @if(auth()->user()->hasRole('admin'))
                    <td>
                        @if($product->is_admin_verified)
                            <span class="badge-verified status-badge">Verified</span>
                        @else
                            <span class="badge-unverified status-badge">Unverified</span>
                        @endif
                    </td>
                    @endif
                    <td>
                        {{ $product->created_at->format('M d, Y') }}
                        <br>
                        <small>{{ $product->created_at->format('h:i A') }}</small>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ auth()->user()->hasRole('admin') ? '8' : '7' }}" class="text-center" style="padding: 20px;">
                        No products found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Products Management System - {{ auth()->user()->hasRole('admin') ? 'Administrator Report' : 'Vendor Report' }}</p>
        <p>Page 1 of 1 | Total Records: {{ $products->count() }} | Report ID: PRD-{{ now()->format('Ymd-His') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

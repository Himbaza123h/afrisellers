<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Products Report - {{ now()->format('M d, Y') }}</title>
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
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-draft {
            background-color: #e5e7eb;
            color: #374151;
        }

        .badge-low-stock {
            background-color: #fed7aa;
            color: #9a3412;
        }

        .badge-out-stock {
            background-color: #fecaca;
            color: #991b1b;
        }

        .badge-in-stock {
            background-color: #d1fae5;
            color: #065f46;
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
        <h1>PRODUCTS INVENTORY REPORT</h1>
        <p>Product Management & Inventory Overview</p>
        <p>Generated on: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Statistics Section -->
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
                    <div class="stats-label">Total Categories</div>
                    <div class="stats-value">{{ $stats['categories'] }}</div>
                    <div class="stats-subtext">Categorized</div>
                </td>
                <td>
                    <div class="stats-label">Low Stock Alert</div>
                    <div class="stats-value">{{ number_format($stats['low_stock']) }}</div>
                    <div class="stats-subtext">{{ $stats['low_stock'] > 0 ? 'Needs attention' : 'All good' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Status Distribution -->
    <div class="section-title">Status Distribution</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 20%;">Status</th>
                <th style="width: 15%;" class="text-right">Count</th>
                <th style="width: 15%;" class="text-right">Percentage</th>
            </tr>
        </thead>
        <tbody>
            @php $totalProducts = $stats['total']; @endphp
            @foreach($statusDistribution as $status)
                <tr>
                    <td>{{ ucfirst($status->status) }}</td>
                    <td class="text-right">{{ $status->count }}</td>
                    <td class="text-right">{{ $totalProducts > 0 ? number_format(($status->count / $totalProducts) * 100, 1) : 0 }}%</td>
                </tr>
            @endforeach
            @if($totalProducts > 0)
                <tr>
                    <td style="font-weight: bold;">Total</td>
                    <td class="text-right" style="font-weight: bold;">{{ $totalProducts }}</td>
                    <td class="text-right" style="font-weight: bold;">100%</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Category Distribution -->
    @if($categoryDistribution->count() > 0)
        <div class="section-title">Category Distribution</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 20%;">Category</th>
                    <th style="width: 15%;" class="text-right">Products</th>
                    <th style="width: 15%;" class="text-right">Percentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categoryDistribution as $category)
                    <tr>
                        <td>{{ $category->productCategory ? $category->productCategory->name : 'Uncategorized' }}</td>
                        <td class="text-right">{{ $category->count }}</td>
                        <td class="text-right">{{ $totalProducts > 0 ? number_format(($category->count / $totalProducts) * 100, 1) : 0 }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Products List -->
    <div class="section-title">Products Details</div>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 8%;">SKU</th>
                <th style="width: 20%;">Product Name</th>
                <th style="width: 12%;">Category</th>
                <th style="width: 10%;">Stock</th>
                <th style="width: 12%;">Price</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 10%;">Created Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                <tr>
                    <td><code>{{ $product->sku ?? 'N/A' }}</code></td>
                    <td><strong>{{ $product->name }}</strong></td>
                    <td>
                        @if($product->productCategory)
                            {{ $product->productCategory->name }}
                        @else
                            <span style="color: #999;">Uncategorized</span>
                        @endif
                    </td>
                    <td>
                        @php $stock = $product->min_order_quantity ?? 0; @endphp
                        @if($stock == 0)
                            <span class="badge badge-out-stock">Out of Stock</span>
                        @elseif($stock < 50)
                            <span class="badge badge-low-stock">{{ $stock }} - Low</span>
                        @else
                            <span class="badge badge-in-stock">{{ $stock }}</span>
                        @endif
                    </td>
                    <td>
                        @if($product->prices->first())
                            @php
                                $price = $product->prices->first();
                                $currencySymbols = ['USD' => '$', 'EUR' => '€', 'GBP' => '£', 'RWF' => 'RF'];
                                $symbol = $currencySymbols[$price->currency] ?? $price->currency;
                            @endphp
                            <strong>{{ $symbol }} {{ number_format($price->price, 2) }}</strong>
                        @else
                            <span style="color: #999;">No price</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $badgeClass = match($product->status) {
                                'active' => 'badge-active',
                                'inactive' => 'badge-inactive',
                                'draft' => 'badge-draft',
                                default => 'badge-draft'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ ucfirst($product->status) }}</span>
                    </td>
                    <td>{{ $product->created_at->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px;">
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
                <td style="font-weight: bold;">Filtered Status</td>
                <td>{{ request('status') ? ucfirst(request('status')) : 'All statuses' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Filtered Category</td>
                <td>
                    @if(request('category'))
                        @php
                            $category = \App\Models\ProductCategory::find(request('category'));
                        @endphp
                        {{ $category ? $category->name : 'N/A' }}
                    @else
                        All categories
                    @endif
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Search Term</td>
                <td>{{ request('search') ?: 'None' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Product Inventory System - Vendor Report</p>
        <p>Page 1 of 1 | Total Records: {{ $products->count() }} | Report ID: PRD-{{ now()->format('Ymd-His') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

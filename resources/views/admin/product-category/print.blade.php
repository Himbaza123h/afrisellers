<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Product Categories Management Report</title>
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

        .status-active {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-inactive {
            background-color: #f3f4f6;
            color: #1f2937;
        }

        .badge-main {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-sub {
            background-color: #f3e8ff;
            color: #7c3aed;
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

        .indent-level {
            padding-left: 20px;
            font-weight: normal;
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
        <h1>PRODUCT CATEGORIES MANAGEMENT REPORT</h1>
        <p>Complete Overview of Product Categories and Subcategories</p>
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
                    <div class="stats-label">Total Categories</div>
                    <div class="stats-value">{{ $stats['total'] }}</div>
                </td>
                <td>
                    <div class="stats-label">Main Categories</div>
                    <div class="stats-value">{{ $stats['main_categories'] }}</div>
                    <div class="stats-subtext" style="color: #1e40af;">Top-level categories</div>
                </td>
                <td>
                    <div class="stats-label">Sub Categories</div>
                    <div class="stats-value">{{ $stats['sub_categories'] }}</div>
                    <div class="stats-subtext" style="color: #7c3aed;">{{ $stats['sub_category_percentage'] }}% of total</div>
                </td>
                <td>
                    <div class="stats-label">Active Categories</div>
                    <div class="stats-value">{{ $stats['active'] }}</div>
                    <div class="stats-subtext" style="color: #059669;">{{ $stats['active_percentage'] }}% active</div>
                </td>
                <td>
                    <div class="stats-label">With Products</div>
                    <div class="stats-value">{{ $stats['with_products'] }}</div>
                    <div class="stats-subtext" style="color: #f59e0b;">Categories in use</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Main Table -->
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 3%;">#</th>
                <th style="width: 25%;">Category Name</th>
                <th style="width: 20%;">Parent Category</th>
                <th style="width: 20%;">Description</th>
                <th style="width: 8%;">Products</th>
                <th style="width: 8%;">Status</th>
                <th style="width: 16%;">Created Date</th>
            </tr>
        </thead>
        <tbody>
            @php
                $mainCategories = $categories->where('parent_id', null);
                $index = 0;
            @endphp

            @forelse($mainCategories as $mainCategory)
                @php
                    $subCategories = $categories->where('parent_id', $mainCategory->id);
                    $hasSubCategories = $subCategories->count() > 0;
                @endphp

                <!-- Main Category Row -->
                <tr>
                    <td class="text-center">{{ ++$index }}</td>
                    <td>
                        <strong>{{ $mainCategory->name }}</strong>
                        <br><span class="badge-main status-badge">Main Category</span>
                    </td>
                    <td>
                        <em>—</em>
                    </td>
                    <td>
                        {{ $mainCategory->description ? \Illuminate\Support\Str::limit($mainCategory->description, 60) : 'No description' }}
                    </td>
                    <td class="text-center">{{ $mainCategory->products->count() }}</td>
                    <td>
                        @php
                            $statusClasses = [
                                'active' => 'status-active',
                                'inactive' => 'status-inactive',
                            ];
                            $statusClass = $statusClasses[$mainCategory->status] ?? 'status-inactive';
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ ucfirst($mainCategory->status) }}
                        </span>
                    </td>
                    <td>
                        {{ $mainCategory->created_at->format('M d, Y') }}
                        <br>
                        <small>{{ $mainCategory->created_at->format('h:i A') }}</small>
                    </td>
                </tr>

                <!-- Sub Categories -->
                @foreach($subCategories as $subCategory)
                <tr>
                    <td class="text-center">{{ ++$index }}</td>
                    <td class="indent-level">
                        <span style="margin-left: 20px;">{{ $subCategory->name }}</span>
                        <br><span class="badge-sub status-badge">Sub-category</span>
                    </td>
                    <td>
                        <strong>{{ $mainCategory->name }}</strong>
                    </td>
                    <td>
                        {{ $subCategory->description ? \Illuminate\Support\Str::limit($subCategory->description, 60) : 'No description' }}
                    </td>
                    <td class="text-center">{{ $subCategory->products->count() }}</td>
                    <td>
                        @php
                            $statusClasses = [
                                'active' => 'status-active',
                                'inactive' => 'status-inactive',
                            ];
                            $statusClass = $statusClasses[$subCategory->status] ?? 'status-inactive';
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ ucfirst($subCategory->status) }}
                        </span>
                    </td>
                    <td>
                        {{ $subCategory->created_at->format('M d, Y') }}
                        <br>
                        <small>{{ $subCategory->created_at->format('h:i A') }}</small>
                    </td>
                </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px;">
                        No categories found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Product Categories Management System - Administrator Report</p>
        <p>Page 1 of 1 | Total Records: {{ $categories->count() }} | Report ID: CAT-{{ now()->format('Ymd-His') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

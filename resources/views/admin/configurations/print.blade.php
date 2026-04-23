<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Configurations - Print</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .meta-info {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }

        .meta-info div {
            font-size: 11px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background-color: #f3f4f6;
            padding: 10px 8px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            border: 1px solid #ddd;
        }

        td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 11px;
        }

        tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 500;
        }

        .badge-active {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-inactive {
            background-color: #f3f4f6;
            color: #374151;
        }

        .badge-string {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-integer {
            background-color: #e9d5ff;
            color: #6b21a8;
        }

        .badge-boolean {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-array {
            background-color: #fed7aa;
            color: #92400e;
        }

        .badge-json {
            background-color: #fce7f3;
            color: #9f1239;
        }

        .badge-text {
            background-color: #f3f4f6;
            color: #374151;
        }

        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        code {
            background-color: #f3f4f6;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 10px;
        }

        @media print {
            body {
                padding: 10px;
            }

            .no-print {
                display: none !important;
            }

            @page {
                margin: 0.5cm;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>System Configurations</h1>
        <p>Complete list of system configurations</p>
    </div>

    <div class="meta-info">
        <div>
            <strong>Generated:</strong> {{ now()->format('F d, Y h:i A') }}
        </div>
        <div>
            <strong>Total Records:</strong> {{ $configurations->count() }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">ID</th>
                <th style="width: 25%;">Unique ID</th>
                <th style="width: 10%;">Type</th>
                <th style="width: 35%;">Value</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 15%;">Last Updated</th>
            </tr>
        </thead>
        <tbody>
            @forelse($configurations as $config)
                <tr>
                    <td>{{ $config->id }}</td>
                    <td><strong>{{ $config->unique_id }}</strong></td>
                    <td>
                        <span class="badge badge-{{ $config->type }}">
                            {{ ucfirst($config->type) }}
                        </span>
                    </td>
                    <td>
                        @if(in_array($config->type, ['array', 'json']))
                            <code>{{ is_array($config->value) ? json_encode($config->value) : $config->value }}</code>
                        @elseif($config->type === 'boolean')
                            <strong>{{ $config->value ? 'True' : 'False' }}</strong>
                        @elseif($config->type === 'text')
                            {{ Str::limit($config->value, 100) }}
                        @else
                            {{ $config->value }}
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $config->is_active ? 'badge-active' : 'badge-inactive' }}">
                            {{ $config->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>{{ $config->updated_at->format('M d, Y h:i A') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px; color: #999;">
                        No configurations found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>© {{ date('Y') }} Afrisellers. All rights reserved.</p>
        <p>This is a system-generated report. Page {{ $configurations->currentPage() ?? 1 }}</p>
    </div>

    <script>
        // Auto-print on load
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

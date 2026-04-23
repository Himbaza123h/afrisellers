<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agents Report – {{ $country->name }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            color: #1f2937;
            background: #fff;
        }

        /* ---- Page layout ---- */
        .page { max-width: 960px; margin: 0 auto; padding: 30px 24px; }

        /* ---- Header ---- */
        .header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            border-bottom: 3px solid #4f46e5;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .header-left h1 { font-size: 22px; font-weight: 700; color: #1e1b4b; }
        .header-left p  { font-size: 11px; color: #6b7280; margin-top: 4px; }
        .header-right   { text-align: right; font-size: 11px; color: #6b7280; }
        .header-right strong { display: block; font-size: 13px; color: #374151; }

        /* ---- Stats ---- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        .stat-box {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 14px;
            text-align: center;
        }
        .stat-box .num   { font-size: 22px; font-weight: 700; color: #1e1b4b; }
        .stat-box .lbl   { font-size: 10px; color: #6b7280; margin-top: 3px; text-transform: uppercase; letter-spacing: .5px; }
        .stat-box .pct   { font-size: 10px; font-weight: 600; color: #4f46e5; margin-top: 4px; }

        /* ---- Filter info ---- */
        .filter-bar {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 8px 14px;
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 16px;
        }
        .filter-bar span { font-weight: 600; color: #374151; }

        /* ---- Table ---- */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead tr { background: #eef2ff; }
        thead th {
            padding: 8px 10px;
            text-align: left;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #3730a3;
            border-bottom: 2px solid #c7d2fe;
        }
        tbody tr { border-bottom: 1px solid #f3f4f6; }
        tbody tr:nth-child(even) { background: #fafafa; }
        tbody td { padding: 7px 10px; vertical-align: middle; }

        .avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #e0e7ff;
            color: #4338ca;
            font-weight: 700;
            font-size: 12px;
            margin-right: 8px;
        }
        .name-cell { display: flex; align-items: center; }

        /* ---- Badge ---- */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 600;
        }
        .badge-active    { background:#dcfce7; color:#15803d; }
        .badge-suspended { background:#fee2e2; color:#b91c1c; }
        .badge-inactive  { background:#f3f4f6; color:#6b7280; }

        /* ---- Footer ---- */
        .footer {
            border-top: 1px solid #e5e7eb;
            padding-top: 12px;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #9ca3af;
        }

        /* ---- No-result ---- */
        .empty {
            text-align: center;
            padding: 40px 0;
            color: #9ca3af;
        }

        /* ---- Print ---- */
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            @page { size: A4 landscape; margin: 15mm; }
        }
    </style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="header">
        <div class="header-left">
            <h1>Agents Report</h1>
            <p>{{ $country->name }} &bull; Country Admin Panel</p>
        </div>
        <div class="header-right">
            <strong>Afrisellers</strong>
            Generated: {{ now()->format('M d, Y \a\t h:i A') }}
        </div>
    </div>

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-box">
            <div class="num">{{ number_format($stats['total']) }}</div>
            <div class="lbl">Total Agents</div>
        </div>
        <div class="stat-box">
            <div class="num">{{ number_format($stats['active']) }}</div>
            <div class="lbl">Active</div>
            <div class="pct">{{ $stats['active_percentage'] }}%</div>
        </div>
        <div class="stat-box">
            <div class="num">{{ number_format($stats['suspended']) }}</div>
            <div class="lbl">Suspended</div>
        </div>
        <div class="stat-box">
            <div class="num">{{ number_format($stats['inactive']) }}</div>
            <div class="lbl">Inactive</div>
        </div>
    </div>

    {{-- Active filters note --}}
    @php
        $filters = array_filter([
            request('search')   ? 'Search: "'.request('search').'"'    : null,
            request('status')   ? 'Status: '.ucfirst(request('status')) : null,
            request('date_from') && request('date_to')
                ? 'Date: '.request('date_from').' to '.request('date_to') : null,
        ]);
    @endphp
    @if(count($filters))
        <div class="filter-bar">
            <span>Filters applied:</span> {{ implode(' &bull; ', $filters) }}
            &nbsp;&bull;&nbsp; Showing {{ $agents->count() }} of {{ $stats['total'] }} agents
        </div>
    @else
        <div class="filter-bar">
            Showing all <span>{{ $agents->count() }}</span> agents in {{ $country->name }}
        </div>
    @endif

    {{-- Table --}}
    @if($agents->isEmpty())
        <div class="empty">
            <p style="font-size:14px; font-weight:600;">No agents found</p>
            <p style="margin-top:6px;">Try adjusting your filter criteria.</p>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Agent Name</th>
                    <th>Email Address</th>
                    <th>Country</th>
                    <th>Registered</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($agents as $i => $agent)
                    @php
                        $map = [
                            'active'    => ['Active',    'badge-active'],
                            'suspended' => ['Suspended', 'badge-suspended'],
                        ];
                        [$lbl, $badgeCls] = $map[$agent->status] ?? ['Inactive', 'badge-inactive'];
                    @endphp
                    <tr>
                        <td style="color:#9ca3af;">{{ $i + 1 }}</td>
                        <td>
                            <div class="name-cell">
                                <span class="avatar">{{ strtoupper(substr($agent->name,0,1)) }}</span>
                                <div>
                                    <div style="font-weight:600;">{{ $agent->name }}</div>
                                    <div style="font-size:10px;color:#9ca3af;">ID #{{ $agent->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $agent->email }}</td>
                        <td>{{ $agent->country->name ?? '—' }}</td>
                        <td>
                            {{ $agent->created_at->format('M d, Y') }}<br>
                            <span style="font-size:10px;color:#9ca3af;">{{ $agent->created_at->format('h:i A') }}</span>
                        </td>
                        <td><span class="badge {{ $badgeCls }}">{{ $lbl }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <span>Afrisellers &bull; {{ $country->name }} &bull; Agents Report</span>
        <span>Total: {{ $agents->count() }} agent(s) &bull; {{ now()->format('Y-m-d') }}</span>
    </div>

</div>

{{-- Print / Close bar (hidden on actual print) --}}
<div class="no-print" style="position:fixed;bottom:0;left:0;right:0;background:#1e1b4b;padding:10px 20px;display:flex;gap:12px;justify-content:center;">
    <button onclick="window.print()"
        style="background:#4f46e5;color:#fff;border:none;padding:8px 20px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;">
        🖨 Print / Save as PDF
    </button>
    <button onclick="window.close()"
        style="background:#374151;color:#fff;border:none;padding:8px 20px;border-radius:6px;font-size:13px;cursor:pointer;">
        ✕ Close
    </button>
</div>
</body>
</html>

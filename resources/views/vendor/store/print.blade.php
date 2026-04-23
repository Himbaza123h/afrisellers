<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Report — {{ $vendor->businessProfile->business_name ?? 'My Store' }}</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f8fafc;
            color: #1e293b;
            padding: 32px;
        }

        /* ── Header ─────────────────────────────── */
.report-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 24px;
            background: linear-gradient(135deg, #ff0808 0%, #c20000 100%);
            border-radius: 12px;
            color: #fff;
            margin-bottom: 24px;
        }
        .report-header .logo-wrap {
            display: flex;
            align-items: center;
            gap: 16px;
        }
.report-header img.biz-logo {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            object-fit: cover;
            border: 2px solid rgba(255,255,255,.35);
        }
        .report-header .biz-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            background: rgba(255,255,255,.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }
.report-header h1 { font-size: 17px; font-weight: 700; }
        .report-header p  { font-size: 12px; opacity: .8; margin-top: 3px; }
        .report-header .meta { text-align: right; font-size: 11px; opacity: .75; }
        .report-header .meta strong { display: block; font-size: 13px; opacity: 1; }

        /* ── Section title ───────────────────────── */
.section-title {
            font-size: 13px;
            font-weight: 700;
            color: #ff0808;
            margin-bottom: 12px;
            padding-bottom: 5px;
            border-bottom: 2px solid #ffe0e0;
            letter-spacing: .3px;
        }

        /* ── Stat cards grid ─────────────────────── */
.cards-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 22px;
        }
        .stat-card {
            background: #fff;
            border-radius: 10px;
            padding: 14px 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
        }
        .stat-card .icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px;
            margin-bottom: 8px;
        }
        .stat-card .label { font-size: 10px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; }
        .stat-card .value { font-size: 18px; font-weight: 800; color: #1e293b; margin-top: 2px; }
        .stat-card .sub   { font-size: 10px; color: #94a3b8; margin-top: 2px; }

        /* colour helpers */
        .bg-purple { background: #ede9fe; color: #6d28d9; }
        .bg-green  { background: #dcfce7; color: #16a34a; }
        .bg-amber  { background: #fef9c3; color: #ca8a04; }
        .bg-blue   { background: #dbeafe; color: #2563eb; }
        .bg-indigo { background: #e0e7ff; color: #4338ca; }
        .bg-yellow { background: #fef3c7; color: #d97706; }
        .bg-cyan   { background: #cffafe; color: #0891b2; }
        .bg-teal   { background: #ccfbf1; color: #0d9488; }
        .bg-pink   { background: #fce7f3; color: #db2777; }

.charts-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 22px;
        }
        .chart-card {
            background: #fff;
            border-radius: 10px;
            padding: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
        }
        .chart-card h3 { font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 12px; }
        .chart-card canvas { max-height: 180px; }

        /* full-width chart */
        .chart-card.full { grid-column: span 2; }
        .chart-card.full canvas { max-height: 160px; }
        /* ── Account info table ───────────────────── */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 28px;
        }
        .info-card {
            background: #fff;
            border-radius: 14px;
            padding: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 4px rgba(0,0,0,.05);
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
        }
        .info-row:last-child { border-bottom: none; }
        .info-row .key { color: #64748b; font-weight: 500; }
        .info-row .val { font-weight: 700; color: #1e293b; }
        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 2px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 700;
        }
        .badge-green  { background: #dcfce7; color: #15803d; }
        .badge-orange { background: #ffedd5; color: #c2410c; }
        .badge-red    { background: #fee2e2; color: #b91c1c; }

        /* ── Footer ──────────────────────────────── */
        .report-footer {
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        /* ── Print button (hidden when printing) ─── */
        .print-btn-wrap {
            text-align: right;
            margin-bottom: 24px;
        }
.print-btn {
            background: #ff0808;
            color: #fff;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }
        .print-btn:hover { background: #c20000; }
        .print-btn:hover { background: #4338ca; }

        @media print {
            body { background: #fff; padding: 16px; }
            .print-btn-wrap { display: none; }
            .report-header { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .stat-card .icon { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

    <!-- Print button -->
    <div class="print-btn-wrap">
        <button class="print-btn" onclick="window.print()">🖨️ Print / Save as PDF</button>
    </div>

    <!-- ── Header ───────────────────────────────────── -->
    <div class="report-header">
        <div class="logo-wrap">
            @if($vendor->businessProfile->logo)
                <img class="biz-logo" src="{{ Storage::url($vendor->businessProfile->logo) }}" alt="Logo">
            @else
                <div class="biz-icon">🏪</div>
            @endif
            <div>
                <h1>{{ $vendor->businessProfile->business_name ?? 'My Store' }}</h1>
                <p>{{ $vendor->businessProfile->business_email ?? '' }}</p>
                @if($vendor->businessProfile->city || $vendor->businessProfile->address)
                    <p>{{ $vendor->businessProfile->city }}{{ $vendor->businessProfile->city ? ', ' : '' }}{{ $vendor->businessProfile->address }}</p>
                @endif
            </div>
        </div>
        <div class="meta">
            <strong>Store Performance Report</strong>
            Generated: {{ now()->format('d M Y, H:i') }}
            @if($vendor->plan)
                <br>Plan: {{ $vendor->plan->name }}
            @endif
        </div>
    </div>

    <!-- ── KPI Cards ─────────────────────────────────── -->
    <p class="section-title">Key Metrics</p>
    <div class="cards-grid">
        <div class="stat-card">
            <div class="icon bg-purple">📦</div>
            <div class="label">Total Products</div>
            <div class="value">{{ $stats['total_products'] }}</div>
            <div class="sub">All listings</div>
        </div>
        <div class="stat-card">
            <div class="icon bg-green">✅</div>
            <div class="label">Active Products</div>
            <div class="value">{{ $stats['active_products'] }}</div>
            <div class="sub">Currently visible</div>
        </div>
        <div class="stat-card">
            <div class="icon bg-amber">⏳</div>
            <div class="label">Pending</div>
            <div class="value">{{ $stats['pending_products'] }}</div>
            <div class="sub">Awaiting approval</div>
        </div>
        <div class="stat-card">
            <div class="icon bg-blue">👁️</div>
            <div class="label">Total Views</div>
            <div class="value">{{ number_format($stats['total_views']) }}</div>
            <div class="sub">Product views</div>
        </div>
        <div class="stat-card">
            <div class="icon bg-indigo">✉️</div>
            <div class="label">Total Inquiries</div>
            <div class="value">{{ $stats['total_inquiries'] }}</div>
            <div class="sub">Buyer messages</div>
        </div>
        <div class="stat-card">
            <div class="icon bg-yellow">⭐</div>
            <div class="label">Total Reviews</div>
            <div class="value">{{ $stats['total_reviews'] }}</div>
            <div class="sub">Approved reviews</div>
        </div>
        <div class="stat-card">
            <div class="icon bg-yellow">🌟</div>
            <div class="label">Avg Rating</div>
            <div class="value">{{ number_format($stats['average_rating'], 1) }}<span style="font-size:13px;color:#94a3b8">/5</span></div>
            <div class="sub">Star rating</div>
        </div>
        <div class="stat-card">
            @php $ctr = $stats['total_impressions'] > 0 ? ($stats['total_clicks'] / $stats['total_impressions']) * 100 : 0; @endphp
            <div class="icon bg-pink">📈</div>
            <div class="label">Click-Through Rate</div>
            <div class="value">{{ number_format($ctr, 2) }}<span style="font-size:13px;color:#94a3b8">%</span></div>
            <div class="sub">Impressions → Clicks</div>
        </div>
    </div>

    <!-- ── Charts ────────────────────────────────────── -->
    <p class="section-title">Visual Analytics</p>
    <div class="charts-grid">

        <!-- Donut: Product Status -->
        <div class="chart-card">
            <h3>📦 Product Status Breakdown</h3>
            <canvas id="donutChart"></canvas>
        </div>

        <!-- Bar: Engagement -->
        <div class="chart-card">
            <h3>📊 Engagement Overview</h3>
            <canvas id="barChart"></canvas>
        </div>

        <!-- Horizontal bar: Impressions vs Clicks vs Views — full width -->
        <div class="chart-card full">
            <h3>🔍 Impressions · Views · Clicks · Reviews</h3>
            <canvas id="hbarChart"></canvas>
        </div>

    </div>

    <!-- ── Account Info ───────────────────────────────── -->
    <p class="section-title">Account Information</p>
    <div class="info-grid">
        <div class="info-card">
            <div class="info-row">
                <span class="key">Verification Status</span>
                <span class="val">
                    @if($vendor->verification_status === 'verified')
                        <span class="badge badge-green">✔ Verified</span>
                    @elseif($vendor->verification_status === 'pending')
                        <span class="badge badge-orange">⏳ Pending</span>
                    @else
                        <span class="badge badge-red">✖ Rejected</span>
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span class="key">Account Status</span>
                <span class="val">
                    @if($vendor->account_status === 'active')
                        <span class="badge badge-green">✔ Active</span>
                    @else
                        <span class="badge badge-red">✖ Suspended</span>
                    @endif
                </span>
            </div>
            @if($vendor->plan)
            <div class="info-row">
                <span class="key">Current Plan</span>
                <span class="val" style="color:#6d28d9">{{ $vendor->plan->name }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="key">Member Since</span>
                @php $years = round($stats['account_age_days'] / 365.25, 1); @endphp
                <span class="val">{{ $years < 1 ? 'Less than a year' : $years . ' ' . ($years == 1.0 ? 'year' : 'years') }}</span>
            </div>
        </div>
        <div class="info-card">
            <div class="info-row">
                <span class="key">Business Email</span>
                <span class="val">{{ $vendor->businessProfile->business_email ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="key">Phone</span>
                <span class="val">{{ ($vendor->businessProfile->phone_code ?? '') . ' ' . ($vendor->businessProfile->phone ?? '—') }}</span>
            </div>
            <div class="info-row">
                <span class="key">City</span>
                <span class="val">{{ $vendor->businessProfile->city ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="key">Website</span>
                <span class="val">{{ $vendor->businessProfile->website ?? '—' }}</span>
            </div>
        </div>
    </div>

    <!-- ── Footer ────────────────────────────────────── -->
    <div class="report-footer">
        Report generated on {{ now()->format('l, d F Y \a\t H:i') }} &nbsp;·&nbsp;
        {{ $vendor->businessProfile->business_name ?? 'Store' }} &nbsp;·&nbsp;
        Confidential
    </div>

    <!-- ── Chart.js scripts ───────────────────────────── -->
    <script>
        const stats = {
            total_products:    {{ $stats['total_products'] }},
            active_products:   {{ $stats['active_products'] }},
            pending_products:  {{ $stats['pending_products'] }},
            inactive:          {{ $stats['total_products'] - $stats['active_products'] - $stats['pending_products'] }},
            total_views:       {{ $stats['total_views'] }},
            total_inquiries:   {{ $stats['total_inquiries'] }},
            total_reviews:     {{ $stats['total_reviews'] }},
            average_rating:    {{ $stats['average_rating'] }},
            total_impressions: {{ $stats['total_impressions'] }},
            total_clicks:      {{ $stats['total_clicks'] }},
        };

        // ── 1. Donut — Product Status ─────────────────
        new Chart(document.getElementById('donutChart'), {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Pending', 'Inactive'],
                datasets: [{
                    data: [stats.active_products, stats.pending_products, stats.inactive < 0 ? 0 : stats.inactive],
                    backgroundColor: ['#22c55e', '#f59e0b', '#e2e8f0'],
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                cutout: '65%',
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 12 }, padding: 14 } },
                    tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed}` } }
                }
            }
        });

        // ── 2. Bar — Engagement ───────────────────────
        new Chart(document.getElementById('barChart'), {
            type: 'bar',
            data: {
                labels: ['Views', 'Inquiries', 'Reviews'],
                datasets: [{
                    label: 'Count',
                    data: [stats.total_views, stats.total_inquiries, stats.total_reviews],
                    backgroundColor: ['#6366f1', '#0ea5e9', '#f59e0b'],
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 } } },
                    x: { grid: { display: false }, ticks: { font: { size: 12, weight: '600' } } }
                }
            }
        });

        // ── 3. Horizontal Bar — Full funnel ───────────
        new Chart(document.getElementById('hbarChart'), {
            type: 'bar',
            data: {
                labels: ['Impressions', 'Views', 'Clicks', 'Reviews'],
                datasets: [{
                    label: 'Total',
                    data: [stats.total_impressions, stats.total_views, stats.total_clicks, stats.total_reviews],
                    backgroundColor: ['#818cf8', '#34d399', '#fb923c', '#f472b6'],
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 } } },
                    y: { grid: { display: false }, ticks: { font: { size: 12, weight: '600' } } }
                }
            }
        });
    </script>
</body>
</html>

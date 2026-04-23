@extends('layouts.home')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .stat-card        { transition: transform .2s, box-shadow .2s; }
    .stat-card:hover  { transform: translateY(-2px); box-shadow: 0 4px 12px -2px rgba(0,0,0,.12); }
    .tab-btn          { transition: color .15s, border-color .15s; }
    .tab-btn.active   { color: #2563eb; border-bottom: 2px solid #2563eb; }
    .chart-wrap       { position: relative; }
    .section-panel    { display: none; }
    .section-panel.visible { display: block; }
    @media print { .no-print { display: none !important; } }
</style>
@endpush

@section('page-content')
<div class="space-y-5">

    {{-- ══════════════════════════════════════════════
         HEADER
    ══════════════════════════════════════════════ --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between no-print">
        <div>
            <h1 class="text-xl font-black text-gray-900 uppercase">Analytics & Performance</h1>
            <p class="mt-0.5 text-xs text-gray-500">Store traffic · Product views · Article engagement · Video metrics</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="window.open('{{ route('vendor.performance.print') }}'+window.location.search,'_blank')"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium shadow-sm">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         PERIOD FILTER
    ══════════════════════════════════════════════ --}}
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm no-print">
        <form method="GET" action="{{ route('vendor.performance.index') }}"
              class="flex flex-wrap gap-3 items-end">

            {{-- Quick periods --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Period</label>
                <div class="flex gap-1">
                    @foreach(['weekly'=>'Week','monthly'=>'Month','yearly'=>'Year','custom'=>'Custom'] as $val => $label)
                        <button type="submit" name="period" value="{{ $val }}"
                                class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition-colors
                                       {{ $period === $val
                                          ? 'bg-blue-600 text-white border-blue-600'
                                          : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Custom range (only shown when custom) --}}
            <div class="{{ $period === 'custom' ? '' : 'hidden' }}" id="customRange">
                <label class="block text-xs font-medium text-gray-600 mb-1">Date Range</label>
                <div class="flex gap-2 items-center">
                    <input type="text" id="dateRangePicker" name="date_range"
                           value="{{ $dateRange }}"
                           placeholder="Pick dates…" readonly
                           class="px-3 py-1.5 text-xs border border-gray-300 rounded-lg w-48 cursor-pointer">
                    <button type="submit"
                            class="px-3 py-1.5 text-xs font-semibold bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Apply
                    </button>
                </div>
            </div>

            {{-- Current range display --}}
            <div class="ml-auto text-right">
                <p class="text-xs text-gray-400">Showing</p>
                <p class="text-xs font-semibold text-gray-700">
                    {{ $currentStart->format('M d') }} – {{ $currentEnd->format('M d, Y') }}
                </p>
            </div>
        </form>
    </div>

    {{-- ══════════════════════════════════════════════
         TABS
    ══════════════════════════════════════════════ --}}
    <div class="flex gap-1 border-b border-gray-200 no-print overflow-x-auto">
        @foreach([
            'overview'  => ['fas fa-th-large',       'Overview'],
            'store'     => ['fas fa-store',           'Store & Profile'],
            'products'  => ['fas fa-box',             'Products'],
            'articles'  => ['fas fa-newspaper',       'Articles'],
            'orders'    => ['fas fa-shopping-cart',   'Orders'],
        ] as $tab => $info)
        <button onclick="switchTab('{{ $tab }}')" id="tab-{{ $tab }}"
                class="tab-btn px-4 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-900 whitespace-nowrap flex items-center gap-1.5">
            <i class="{{ $info[0] }} text-xs"></i> {{ $info[1] }}
        </button>
        @endforeach
    </div>

    {{-- ══════════════════════════════════════════════
         TAB: OVERVIEW
    ══════════════════════════════════════════════ --}}
    <div id="panel-overview" class="section-panel space-y-5">

        {{-- KPI row --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @php
            $kpis = [
                ['Store Visits',      $metrics['store_visits'],        $metrics['store_visits_change'],  'fa-store',         'blue'],
                ['Profile Views',     $metrics['profile_views'],       $metrics['profile_views_change'], 'fa-user-circle',   'indigo'],
                ['Product Views',     $metrics['product_views'],       $metrics['product_views_change'], 'fa-box',           'violet'],
                ['Article Views',     $metrics['article_views'],       $metrics['article_views_change'], 'fa-newspaper',     'amber'],
                ['Revenue',           '$'.number_format($metrics['total_revenue'],2), $metrics['revenue_change'], 'fa-dollar-sign', 'emerald'],
                ['Orders',            $metrics['total_orders'],        0,                                'fa-shopping-cart', 'rose'],
            ];
            @endphp

            @foreach($kpis as [$label, $value, $change, $icon, $color])
            <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-8 h-8 bg-{{ $color }}-100 rounded-lg flex items-center justify-center">
                        <i class="fas {{ $icon }} text-{{ $color }}-600 text-sm"></i>
                    </div>
                    @if($change > 0)
                        <span class="text-[10px] font-bold text-green-700 bg-green-100 px-1.5 py-0.5 rounded-full">
                            ↑ {{ number_format(abs($change),1) }}%
                        </span>
                    @elseif($change < 0)
                        <span class="text-[10px] font-bold text-red-700 bg-red-100 px-1.5 py-0.5 rounded-full">
                            ↓ {{ number_format(abs($change),1) }}%
                        </span>
                    @else
                        <span class="text-[10px] font-bold text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded-full">—</span>
                    @endif
                </div>
                <p class="text-xs text-gray-500">{{ $label }}</p>
                <p class="text-lg font-black text-gray-900">{{ $value }}</p>
            </div>
            @endforeach
        </div>

        {{-- Revenue + Orders chart --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-900 mb-4">Revenue & Orders Over Time</h3>
            <div class="chart-wrap h-52">
                <canvas id="revenueOrdersChart"></canvas>
            </div>
        </div>

        {{-- 2-col row: profile funnel + product funnel --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Profile engagement funnel --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-900 mb-4">Profile Engagement</h3>
                @php
                $pFunnel = [
                    ['Views',           $metrics['profile_views'],  'bg-blue-500'],
                    ['Contact Clicks',  $metrics['contact_clicks'], 'bg-indigo-500'],
                    ['WhatsApp Clicks', $metrics['whatsapp_clicks'],'bg-green-500'],
                    ['RFQs Received',   $metrics['profile_rfqs'],   'bg-amber-500'],
                ];
                $pMax = max(1, ...array_column($pFunnel,'1'));
                @endphp
                <div class="space-y-3">
                    @foreach($pFunnel as [$label, $val, $cls])
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-600">{{ $label }}</span>
                            <span class="font-semibold text-gray-900">{{ number_format($val) }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="{{ $cls }} h-2 rounded-full" style="width:{{ round(($val/$pMax)*100) }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Product engagement funnel --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-900 mb-4">Product Engagement</h3>
                @php
                $prFunnel = [
                    ['Impressions',   $metrics['product_impressions'], 'bg-violet-500'],
                    ['Views',         $metrics['product_views'],       'bg-purple-500'],
                    ['Clicks',        $metrics['product_clicks'],      'bg-pink-500'],
                    ['Cart Adds',     $metrics['cart_adds'],           'bg-rose-500'],
                    ['Wishlist Adds', $metrics['wishlist_adds'],       'bg-orange-500'],
                    ['RFQs',          $metrics['product_rfqs'],        'bg-amber-500'],
                ];
                $prMax = max(1, ...array_column($prFunnel,'1'));
                @endphp
                <div class="space-y-2.5">
                    @foreach($prFunnel as [$label, $val, $cls])
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-600">{{ $label }}</span>
                            <span class="font-semibold text-gray-900">{{ number_format($val) }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            <div class="{{ $cls }} h-1.5 rounded-full" style="width:{{ round(($val/$prMax)*100) }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         TAB: STORE & PROFILE
    ══════════════════════════════════════════════ --}}
    <div id="panel-store" class="section-panel space-y-5">

        {{-- Stats row --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @php
            $storeKpis = [
                ['Profile Views',   $metrics['profile_views'],   $metrics['profile_views_change'],  'fa-eye',          'blue'],
                ['Contact Clicks',  $metrics['contact_clicks'],  0,                                 'fa-phone',        'green'],
                ['WhatsApp Clicks', $metrics['whatsapp_clicks'], 0,                                 'fab fa-whatsapp', 'emerald'],
                ['RFQs Received',   $metrics['profile_rfqs'],    0,                                 'fa-file-invoice', 'amber'],
            ];
            @endphp
            @foreach($storeKpis as [$label, $value, $change, $icon, $color])
            <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <div class="w-9 h-9 bg-{{ $color }}-100 rounded-lg flex items-center justify-center mb-3">
                    <i class="{{ $icon }} text-{{ $color }}-600"></i>
                </div>
                <p class="text-xs text-gray-500">{{ $label }}</p>
                <p class="text-xl font-black text-gray-900">{{ number_format($value) }}</p>
                @if($change != 0)
                <p class="text-xs {{ $change > 0 ? 'text-green-600' : 'text-red-600' }} mt-1 font-medium">
                    {{ $change > 0 ? '↑' : '↓' }} {{ number_format(abs($change),1) }}% vs prev period
                </p>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Profile views chart --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-900 mb-4">Profile Views Over Time</h3>
            <div class="chart-wrap h-52">
                <canvas id="profileViewsChart"></canvas>
            </div>
        </div>

        {{-- Click breakdown doughnut --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-900 mb-4">Click Breakdown</h3>
                <div class="chart-wrap h-48">
                    <canvas id="clickBreakdownChart"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-900 mb-4">Content Summary</h3>
                <div class="space-y-3 mt-2">
                    @php
                    $summary = [
                        ['Total Products',     $metrics['total_products'],     'bg-violet-100 text-violet-700'],
                        ['Active Products',    $metrics['active_products'],    'bg-green-100 text-green-700'],
                        ['Total Articles',     $metrics['total_articles'],     'bg-amber-100 text-amber-700'],
                        ['Published Articles', $metrics['published_articles'], 'bg-blue-100 text-blue-700'],
                    ];
                    @endphp
                    @foreach($summary as [$label, $val, $cls])
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-700">{{ $label }}</span>
                        <span class="text-sm font-bold px-2.5 py-0.5 rounded-full {{ $cls }}">{{ $val }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         TAB: PRODUCTS
    ══════════════════════════════════════════════ --}}
    <div id="panel-products" class="section-panel space-y-5">

        {{-- Product metric tiles --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @php
            $prodKpis = [
                ['Impressions',   $metrics['product_impressions'], 'fa-eye',           'slate'],
                ['Views',         $metrics['product_views'],       'fa-box-open',      'violet'],
                ['Video Views',   $metrics['video_views'],         'fa-play-circle',   'pink'],
                ['Watch Time',    $metrics['video_watch_time_min'].'m', 'fa-clock',    'rose'],
            ];
            @endphp
            @foreach($prodKpis as [$label, $value, $icon, $color])
            <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <div class="w-9 h-9 bg-{{ $color }}-100 rounded-lg flex items-center justify-center mb-3">
                    <i class="fas {{ $icon }} text-{{ $color }}-600"></i>
                </div>
                <p class="text-xs text-gray-500">{{ $label }}</p>
                <p class="text-xl font-black text-gray-900">{{ $value }}</p>
            </div>
            @endforeach
        </div>

        {{-- Product views chart --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-900 mb-4">Product Views Over Time</h3>
            <div class="chart-wrap h-52">
                <canvas id="productViewsChart"></canvas>
            </div>
        </div>

        {{-- Impressions vs Clicks --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-900 mb-1">Impressions vs Clicks</h3>
            <p class="text-xs text-gray-400 mb-4">How many times products appeared vs how many times they were clicked</p>
            <div class="chart-wrap h-52">
                <canvas id="impressionClicksChart"></canvas>
            </div>
        </div>

        {{-- Cart & Wishlist --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-rose-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-rose-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Cart Adds</p>
                        <p class="text-2xl font-black text-gray-900">{{ number_format($metrics['cart_adds']) }}</p>
                    </div>
                </div>
                <div class="h-1 bg-gray-100 rounded-full">
                    <div class="h-1 bg-rose-500 rounded-full"
                         style="width:{{ $metrics['product_views'] > 0 ? min(100,round(($metrics['cart_adds']/$metrics['product_views'])*100)) : 0 }}%">
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-1">
                    {{ $metrics['product_views'] > 0 ? round(($metrics['cart_adds']/$metrics['product_views'])*100,1) : 0 }}% of views
                </p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-heart text-orange-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Wishlist Adds</p>
                        <p class="text-2xl font-black text-gray-900">{{ number_format($metrics['wishlist_adds']) }}</p>
                    </div>
                </div>
                <div class="h-1 bg-gray-100 rounded-full">
                    <div class="h-1 bg-orange-500 rounded-full"
                         style="width:{{ $metrics['product_views'] > 0 ? min(100,round(($metrics['wishlist_adds']/$metrics['product_views'])*100)) : 0 }}%">
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-1">
                    {{ $metrics['product_views'] > 0 ? round(($metrics['wishlist_adds']/$metrics['product_views'])*100,1) : 0 }}% of views
                </p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-invoice text-amber-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Product RFQs</p>
                        <p class="text-2xl font-black text-gray-900">{{ number_format($metrics['product_rfqs']) }}</p>
                    </div>
                </div>
                <div class="h-1 bg-gray-100 rounded-full">
                    <div class="h-1 bg-amber-500 rounded-full"
                         style="width:{{ $metrics['product_views'] > 0 ? min(100,round(($metrics['product_rfqs']/$metrics['product_views'])*100)) : 0 }}%">
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-1">
                    {{ $metrics['product_views'] > 0 ? round(($metrics['product_rfqs']/$metrics['product_views'])*100,1) : 0 }}% of views
                </p>
            </div>
        </div>

        {{-- Top Products by views (analytics) --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900">Top Products by Views (All Time)</h3>
                <span class="text-xs text-gray-400">{{ $topProductsByViews->count() }} products</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Product</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-600 uppercase">Views</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-600 uppercase">Impressions</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-600 uppercase">Clicks</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-600 uppercase">Cart</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-600 uppercase">Wishlist</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-600 uppercase">Video</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-600 uppercase">RFQs</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($topProductsByViews as $p)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 max-w-[180px] truncate">{{ $p['name'] }}</td>
                            <td class="px-4 py-3 text-right text-sm text-gray-700">{{ number_format($p['views']) }}</td>
                            <td class="px-4 py-3 text-right text-sm text-gray-500">{{ number_format($p['impressions']) }}</td>
                            <td class="px-4 py-3 text-right text-sm text-gray-500">{{ number_format($p['clicks']) }}</td>
                            <td class="px-4 py-3 text-right text-sm text-rose-600 font-medium">{{ number_format($p['cart_adds']) }}</td>
                            <td class="px-4 py-3 text-right text-sm text-orange-600">{{ number_format($p['wishlist_adds']) }}</td>
                            <td class="px-4 py-3 text-right text-sm text-purple-600">{{ number_format($p['video_views']) }}</td>
                            <td class="px-4 py-3 text-right text-sm text-amber-600 font-semibold">{{ number_format($p['rfq_count']) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-gray-400 text-sm">
                                <i class="fas fa-box text-3xl mb-2 block"></i> No product analytics yet
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Order-based product performance --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-900">Top Products by Revenue (Orders)</h3>
                <p class="text-xs text-gray-400 mt-0.5">Based on completed orders in the selected period</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Product</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-600 uppercase">Units</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-600 uppercase">Orders</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-600 uppercase">Avg Price</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-600 uppercase">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($productPerformance as $p)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900 max-w-[200px] truncate">{{ $p->name }}</td>
                            <td class="px-4 py-3 text-right text-gray-700">{{ number_format($p->units_sold) }}</td>
                            <td class="px-4 py-3 text-right text-gray-700">{{ $p->order_count }}</td>
                            <td class="px-4 py-3 text-right text-gray-500">${{ number_format($p->average_price, 2) }}</td>
                            <td class="px-4 py-3 text-right font-bold text-emerald-600">${{ number_format($p->revenue, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-gray-400 text-sm">
                                <i class="fas fa-box text-3xl mb-2 block"></i> No order data in this period
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         TAB: ARTICLES
    ══════════════════════════════════════════════ --}}
    <div id="panel-articles" class="section-panel space-y-5">

        {{-- Article KPIs --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @php
            $artKpis = [
                ['Views',       $metrics['article_views'],    $metrics['article_views_change'], 'fa-eye',         'blue'],
                ['Likes',       $metrics['article_likes'],    0,                                'fa-heart',       'rose'],
                ['Comments',    $metrics['article_comments'], 0,                                'fa-comment',     'violet'],
                ['Shares',      $metrics['article_shares'],   0,                                'fa-share-alt',   'green'],
            ];
            @endphp
            @foreach($artKpis as [$label, $value, $change, $icon, $color])
            <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <div class="w-9 h-9 bg-{{ $color }}-100 rounded-lg flex items-center justify-center mb-3">
                    <i class="fas {{ $icon }} text-{{ $color }}-600"></i>
                </div>
                <p class="text-xs text-gray-500">{{ $label }}</p>
                <p class="text-xl font-black text-gray-900">{{ number_format($value) }}</p>
                @if($change != 0)
                <p class="text-[10px] {{ $change > 0 ? 'text-green-600' : 'text-red-600' }} font-medium mt-1">
                    {{ $change > 0 ? '↑' : '↓' }} {{ number_format(abs($change),1) }}%
                </p>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Reading metrics --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 md:col-span-1">
                <h3 class="text-sm font-bold text-gray-900 mb-4">Reading Behaviour</h3>
                <div class="space-y-4">
                    <div class="text-center p-4 bg-blue-50 rounded-xl">
                        <p class="text-3xl font-black text-blue-700">{{ $metrics['avg_read_time_sec'] }}s</p>
                        <p class="text-xs text-blue-600 mt-1">Avg Read Time</p>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-xl">
                        <p class="text-3xl font-black text-green-700">{{ $metrics['avg_completion_rate'] }}%</p>
                        <p class="text-xs text-green-600 mt-1">Avg Completion Rate</p>
                    </div>
                    <div class="text-center p-4 bg-amber-50 rounded-xl">
                        <p class="text-3xl font-black text-amber-700">{{ number_format($metrics['article_bookmarks']) }}</p>
                        <p class="text-xs text-amber-600 mt-1">Bookmarks</p>
                    </div>
                </div>
            </div>

            {{-- Article views chart --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 md:col-span-2">
                <h3 class="text-sm font-bold text-gray-900 mb-4">Article Views Over Time</h3>
                <div class="chart-wrap h-56">
                    <canvas id="articleViewsChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Top articles table --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900">Top Articles (All Time)</h3>
                <a href="{{ route('vendor.articles.index') }}"
                   class="text-xs text-blue-600 hover:underline font-medium">View All →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Title</th>
                            <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-600 uppercase">Views</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-600 uppercase">Likes</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-600 uppercase">Comments</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-600 uppercase">Completion</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-600 uppercase">Avg Read</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($topArticles as $a)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900 max-w-[200px] truncate">{{ $a['title'] }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full
                                    {{ $a['status'] === 'published' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($a['status']) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-blue-600 font-semibold">{{ number_format($a['views']) }}</td>
                            <td class="px-4 py-3 text-right text-rose-500">{{ number_format($a['likes']) }}</td>
                            <td class="px-4 py-3 text-right text-violet-600">{{ number_format($a['comments']) }}</td>
                            <td class="px-4 py-3 text-right text-green-600">{{ number_format($a['completion_rate'],1) }}%</td>
                            <td class="px-4 py-3 text-right text-gray-500">{{ number_format($a['avg_read_time']) }}s</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-400 text-sm">
                                <i class="fas fa-newspaper text-3xl mb-2 block"></i> No article analytics yet
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         TAB: ORDERS
    ══════════════════════════════════════════════ --}}
    <div id="panel-orders" class="section-panel space-y-5">

        {{-- Order KPIs --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @php
            $ordKpis = [
                ['Revenue',        '$'.number_format($metrics['total_revenue'],2), 'fa-dollar-sign',  'emerald'],
                ['Orders',         $metrics['total_orders'],                       'fa-shopping-cart','blue'],
                ['Completed',      $metrics['completed_orders'],                   'fa-check-circle', 'green'],
                ['Avg Order Value','$'.number_format($metrics['avg_order_value'],2),'fa-receipt',      'amber'],
            ];
            @endphp
            @foreach($ordKpis as [$label, $value, $icon, $color])
            <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <div class="w-9 h-9 bg-{{ $color }}-100 rounded-lg flex items-center justify-center mb-3">
                    <i class="fas {{ $icon }} text-{{ $color }}-600"></i>
                </div>
                <p class="text-xs text-gray-500">{{ $label }}</p>
                <p class="text-xl font-black text-gray-900">{{ $value }}</p>
            </div>
            @endforeach
        </div>

        {{-- Revenue & Orders chart --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-900 mb-4">Revenue & Orders Over Time</h3>
            <div class="chart-wrap h-56">
                <canvas id="ordersRevenueChart"></canvas>
            </div>
        </div>

        {{-- Conversion & cancellation --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
                <p class="text-xs text-gray-500 mb-2">Conversion Rate</p>
                <p class="text-4xl font-black text-blue-700">{{ $metrics['conversion_rate'] }}%</p>
                <p class="text-xs text-gray-400 mt-1">Views → Orders</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
                <p class="text-xs text-gray-500 mb-2">Cancellation Rate</p>
                <p class="text-4xl font-black {{ $metrics['cancellation_rate'] > 10 ? 'text-red-600' : 'text-green-600' }}">
                    {{ $metrics['cancellation_rate'] }}%
                </p>
                <p class="text-xs text-gray-400 mt-1">{{ $metrics['cancelled_orders'] }} cancelled orders</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
                <p class="text-xs text-gray-500 mb-2">Completion Rate</p>
                <p class="text-4xl font-black text-emerald-600">
                    {{ $metrics['total_orders'] > 0 ? round(($metrics['completed_orders']/$metrics['total_orders'])*100,1) : 0 }}%
                </p>
                <p class="text-xs text-gray-400 mt-1">{{ $metrics['completed_orders'] }} of {{ $metrics['total_orders'] }}</p>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ──────────────────────────────────────────────────────────────
//  CHART DATA FROM PHP
// ──────────────────────────────────────────────────────────────
const revenueChart         = @json($revenueChart);
const profileChart         = @json($profileChartData);
const productViewsChart    = @json($productViewsChart);
const impressionClickChart = @json($impressionClickChart);
const articleChart         = @json($articleViewsChart);

const metrics = {
    contactClicks:  {{ $metrics['contact_clicks'] }},
    whatsappClicks: {{ $metrics['whatsapp_clicks'] }},
    websiteClicks:  {{ $metrics['website_clicks'] }},
};

// ──────────────────────────────────────────────────────────────
//  CHART DEFAULTS
// ──────────────────────────────────────────────────────────────
Chart.defaults.font.family = 'inherit';
Chart.defaults.font.size   = 11;
Chart.defaults.color       = '#6b7280';

function lineOpts(labels, datasets) {
    return {
        type: 'line',
        data: { labels, datasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'top', labels: { boxWidth: 10, padding: 14 } } },
            scales: {
                x: { grid: { display: false }, ticks: { maxTicksLimit: 10 } },
                y: { beginAtZero: true, grid: { color: '#f3f4f6' } },
            },
        },
    };
}

function barOpts(labels, datasets) {
    return {
        type: 'bar',
        data: { labels, datasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'top', labels: { boxWidth: 10, padding: 14 } } },
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, grid: { color: '#f3f4f6' } },
            },
        },
    };
}

// ──────────────────────────────────────────────────────────────
//  INIT ALL CHARTS
// ──────────────────────────────────────────────────────────────
function initCharts() {

    // 1. Revenue & Orders (overview tab)
    const rCtx = document.getElementById('revenueOrdersChart');
    if (rCtx) new Chart(rCtx, lineOpts(revenueChart.labels, [
        { label: 'Revenue ($)', data: revenueChart.revenue,
          borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,.1)',
          fill: true, tension: .4, borderWidth: 2, pointRadius: 3 },
        { label: 'Orders', data: revenueChart.orders,
          borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,.08)',
          fill: true, tension: .4, borderWidth: 2, pointRadius: 3,
          yAxisID: 'y2' },
    ]));

    // 2. Profile views chart
    const pvCtx = document.getElementById('profileViewsChart');
    if (pvCtx) new Chart(pvCtx, lineOpts(profileChart.labels, [
        { label: 'Profile Views', data: profileChart.data,
          borderColor: '#6366f1', backgroundColor: 'rgba(99,102,241,.1)',
          fill: true, tension: .4, borderWidth: 2, pointRadius: 3 },
    ]));

    // 3. Click breakdown doughnut
    const cbCtx = document.getElementById('clickBreakdownChart');
    if (cbCtx) new Chart(cbCtx, {
        type: 'doughnut',
        data: {
            labels: ['Contact Clicks', 'WhatsApp Clicks', 'Website Clicks'],
            datasets: [{ data: [metrics.contactClicks, metrics.whatsappClicks, metrics.websiteClicks],
                         backgroundColor: ['#6366f1','#10b981','#3b82f6'],
                         borderWidth: 0, hoverOffset: 6 }],
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 12 } } },
            cutout: '65%',
        },
    });

    // 4. Product views chart
    const prodCtx = document.getElementById('productViewsChart');
    if (prodCtx) new Chart(prodCtx, lineOpts(productViewsChart.labels, [
        { label: 'Product Views', data: productViewsChart.data,
          borderColor: '#8b5cf6', backgroundColor: 'rgba(139,92,246,.1)',
          fill: true, tension: .4, borderWidth: 2, pointRadius: 3 },
    ]));

    // 5. Impressions vs Clicks bar chart
    const icCtx = document.getElementById('impressionClicksChart');
    if (icCtx) new Chart(icCtx, barOpts(impressionClickChart.labels, [
        { label: 'Impressions', data: impressionClickChart.data1,
          backgroundColor: 'rgba(139,92,246,.7)', borderRadius: 4 },
        { label: 'Clicks',      data: impressionClickChart.data2,
          backgroundColor: 'rgba(236,72,153,.7)', borderRadius: 4 },
    ]));

    // 6. Article views chart
    const artCtx = document.getElementById('articleViewsChart');
    if (artCtx) new Chart(artCtx, lineOpts(articleChart.labels, [
        { label: 'Article Views', data: articleChart.data,
          borderColor: '#f59e0b', backgroundColor: 'rgba(245,158,11,.1)',
          fill: true, tension: .4, borderWidth: 2, pointRadius: 3 },
    ]));

    // 7. Orders tab revenue/orders chart (same data, different canvas)
    const ordCtx = document.getElementById('ordersRevenueChart');
    if (ordCtx) new Chart(ordCtx, barOpts(revenueChart.labels, [
        { label: 'Revenue ($)', data: revenueChart.revenue,
          backgroundColor: 'rgba(16,185,129,.75)', borderRadius: 4 },
        { label: 'Orders',      data: revenueChart.orders,
          backgroundColor: 'rgba(59,130,246,.7)',  borderRadius: 4 },
    ]));
}

// ──────────────────────────────────────────────────────────────
//  TAB SWITCHING
// ──────────────────────────────────────────────────────────────
const TAB_IDS = ['overview','store','products','articles','orders'];

function switchTab(tab) {
    TAB_IDS.forEach(id => {
        document.getElementById('tab-' + id).classList.remove('active');
        document.getElementById('panel-' + id).classList.remove('visible');
    });
    document.getElementById('tab-' + tab).classList.add('active');
    document.getElementById('panel-' + tab).classList.add('visible');
    localStorage.setItem('perf_tab', tab);
}

document.addEventListener('DOMContentLoaded', () => {
    initCharts();
    const saved = localStorage.getItem('perf_tab') || 'overview';
    switchTab(saved);

    // Flatpickr
    flatpickr('#dateRangePicker', {
        mode: 'range',
        dateFormat: 'Y-m-d',
        showMonths: 2,
        locale: { rangeSeparator: ' to ' },
        defaultDate: '{{ $dateRange }}',
        onReady: function(_sel, _str, fp) {
            const btn = document.querySelector('[value="custom"]');
            if (btn) btn.addEventListener('click', () => fp.open());
        }
    });

    // Show custom range picker when custom period is selected
    document.querySelectorAll('[name="period"]').forEach(btn => {
        btn.addEventListener('click', () => {
            const cr = document.getElementById('customRange');
            if (btn.value === 'custom') cr.classList.remove('hidden');
            else cr.classList.add('hidden');
        });
    });
});
</script>
@endpush

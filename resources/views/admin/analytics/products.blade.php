@extends('layouts.home')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
</style>
@endpush

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Product Analytics</h1>
            <p class="mt-1 text-sm text-gray-500">Detailed product performance and sales metrics</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <form method="GET" class="flex gap-2">
                <input type="text" name="date_range" id="dateRangePicker" value="{{ request('date_range') }}" readonly placeholder="Select date range" class="px-4 py-2 border border-gray-300 rounded-lg cursor-pointer">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    <i class="fas fa-sync"></i>Update
                </button>
            </form>
            <a href="{{ route('admin.analytics.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                <i class="fas fa-arrow-left"></i>Back
            </a>
        </div>
    </div>

    <!-- Top Products by Revenue -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Top Products by Revenue</h3>
            <span class="px-3 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">Top 50</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Rank</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Product</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">SKU</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Orders</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Qty Sold</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Revenue</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Avg Price</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($productStats as $index => $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center w-10 h-10 rounded-lg
                                    {{ $index === 0 ? 'bg-gradient-to-br from-yellow-200 to-yellow-400' :
                                       ($index === 1 ? 'bg-gradient-to-br from-gray-200 to-gray-400' :
                                       ($index === 2 ? 'bg-gradient-to-br from-orange-200 to-orange-400' : 'bg-gradient-to-br from-blue-100 to-blue-200')) }}">
                                    <span class="text-sm font-bold {{ $index < 3 ? 'text-white' : 'text-blue-700' }}">{{ $index + 1 }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-semibold text-gray-900">{{ Str::limit($product->name, 40) }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-mono text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ $product->sku }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                    {{ number_format($product->order_count) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="text-sm font-semibold text-blue-600">{{ number_format($product->total_quantity) }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="text-sm font-bold text-green-600">${{ number_format($product->total_revenue, 2) }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="text-sm text-gray-700">${{ number_format($product->total_revenue / $product->total_quantity, 2) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">No product data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Category Distribution -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Category Distribution</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Chart -->
            <div>
                <canvas id="categoryChart" style="height: 300px;"></canvas>
            </div>

            <!-- Category List -->
            <div class="space-y-3">
                @foreach($categoryDistribution as $index => $category)
                    @php
                        $colors = ['blue', 'purple', 'green', 'orange', 'red', 'indigo', 'pink', 'yellow', 'teal', 'cyan'];
                        $color = $colors[$index % count($colors)];
                    @endphp
                    <div class="flex items-center justify-between p-3 bg-{{ $color }}-50 rounded-lg hover:bg-{{ $color }}-100 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-{{ $color }}-200 rounded-lg flex items-center justify-center">
                                <span class="text-lg font-bold text-{{ $color }}-700">{{ $index + 1 }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $category->name }}</p>
                                <p class="text-xs text-gray-500">{{ number_format($category->count) }} products</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-{{ $color }}-600">{{ number_format(($category->count / $categoryDistribution->sum('count')) * 100, 1) }}%</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Product Performance Matrix -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Product Performance Matrix</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $bestSeller = $productStats->first();
                $highestQuantity = $productStats->sortByDesc('total_quantity')->first();
                $mostOrders = $productStats->sortByDesc('order_count')->first();
                $highestAvgPrice = $productStats->sortByDesc(fn($p) => $p->total_revenue / $p->total_quantity)->first();
            @endphp

            <!-- Best Seller -->
            <div class="p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-xl border border-green-200">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-trophy text-green-600 text-xl"></i>
                    <h4 class="text-sm font-semibold text-green-900">Best Seller</h4>
                </div>
                @if($bestSeller)
                    <p class="text-sm font-semibold text-gray-900 mb-1">{{ Str::limit($bestSeller->name, 25) }}</p>
                    <p class="text-xs text-gray-600 mb-2">{{ $bestSeller->sku }}</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-600">Revenue</span>
                        <span class="text-sm font-bold text-green-600">${{ number_format($bestSeller->total_revenue, 0) }}</span>
                    </div>
                @else
                    <p class="text-sm text-gray-500">No data</p>
                @endif
            </div>

            <!-- Highest Volume -->
            <div class="p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border border-blue-200">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-boxes text-blue-600 text-xl"></i>
                    <h4 class="text-sm font-semibold text-blue-900">Highest Volume</h4>
                </div>
                @if($highestQuantity)
                    <p class="text-sm font-semibold text-gray-900 mb-1">{{ Str::limit($highestQuantity->name, 25) }}</p>
                    <p class="text-xs text-gray-600 mb-2">{{ $highestQuantity->sku }}</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-600">Qty Sold</span>
                        <span class="text-sm font-bold text-blue-600">{{ number_format($highestQuantity->total_quantity) }}</span>
                    </div>
                @else
                    <p class="text-sm text-gray-500">No data</p>
                @endif
            </div>

            <!-- Most Popular -->
            <div class="p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl border border-purple-200">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-star text-purple-600 text-xl"></i>
                    <h4 class="text-sm font-semibold text-purple-900">Most Popular</h4>
                </div>
                @if($mostOrders)
                    <p class="text-sm font-semibold text-gray-900 mb-1">{{ Str::limit($mostOrders->name, 25) }}</p>
                    <p class="text-xs text-gray-600 mb-2">{{ $mostOrders->sku }}</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-600">Orders</span>
                        <span class="text-sm font-bold text-purple-600">{{ number_format($mostOrders->order_count) }}</span>
                    </div>
                @else
                    <p class="text-sm text-gray-500">No data</p>
                @endif
            </div>

            <!-- Premium Product -->
            <div class="p-4 bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl border border-amber-200">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-crown text-amber-600 text-xl"></i>
                    <h4 class="text-sm font-semibold text-amber-900">Premium Product</h4>
                </div>
                @if($highestAvgPrice)
                    <p class="text-sm font-semibold text-gray-900 mb-1">{{ Str::limit($highestAvgPrice->name, 25) }}</p>
                    <p class="text-xs text-gray-600 mb-2">{{ $highestAvgPrice->sku }}</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-600">Avg Price</span>
                        <span class="text-sm font-bold text-amber-600">${{ number_format($highestAvgPrice->total_revenue / $highestAvgPrice->total_quantity, 2) }}</span>
                    </div>
                @else
                    <p class="text-sm text-gray-500">No data</p>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
flatpickr("#dateRangePicker", {
    mode: "range",
    dateFormat: "Y-m-d",
    showMonths: 2,
    locale: { rangeSeparator: " to " }
});

// Category Distribution Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: @json($categoryDistribution->pluck('name')),
        datasets: [{
            data: @json($categoryDistribution->pluck('count')),
            backgroundColor: [
                '#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', '#ef4444',
                '#6366f1', '#ec4899', '#eab308', '#14b8a6', '#06b6d4'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: { size: 12 }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(1);
                        return `${label}: ${value} (${percentage}%)`;
                    }
                }
            }
        }
    }
});
</script>
@endpush

@endsection

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
            <h1 class="text-2xl font-bold text-gray-900">Regional Analytics</h1>
            <p class="mt-1 text-sm text-gray-500">Performance metrics by country and region</p>
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

    <!-- Regional Performance Map -->
    <div class="grid grid-cols-1 gap-6">
        @foreach($regionalData as $country)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <span class="text-4xl">{{ $country->flag }}</span>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ $country->name }}</h3>
                            <p class="text-xs text-gray-500">{{ $country->code }} • {{ $country->region ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-green-600">${{ number_format($country->total_revenue, 2) }}</div>
                        <div class="text-xs text-gray-500">Total Revenue</div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-center gap-2 mb-1">
                            <i class="fas fa-store text-blue-600"></i>
                            <p class="text-xs font-medium text-gray-700">Vendors</p>
                        </div>
                        <p class="text-xl font-bold text-gray-900">{{ number_format($country->vendors_count) }}</p>
                    </div>

                    <div class="p-4 bg-purple-50 rounded-lg">
                        <div class="flex items-center gap-2 mb-1">
                            <i class="fas fa-users text-purple-600"></i>
                            <p class="text-xs font-medium text-gray-700">Buyers</p>
                        </div>
                        <p class="text-xl font-bold text-gray-900">{{ number_format($country->buyers_count ?? 0) }}</p>
                    </div>

                    <div class="p-4 bg-green-50 rounded-lg">
                        <div class="flex items-center gap-2 mb-1">
                            <i class="fas fa-box text-green-600"></i>
                            <p class="text-xs font-medium text-gray-700">Products</p>
                        </div>
                        <p class="text-xl font-bold text-gray-900">{{ number_format($country->products_count) }}</p>
                    </div>

                    <div class="p-4 bg-orange-50 rounded-lg">
                        <div class="flex items-center gap-2 mb-1">
                            <i class="fas fa-shopping-cart text-orange-600"></i>
                            <p class="text-xs font-medium text-gray-700">Orders</p>
                        </div>
                        <p class="text-xl font-bold text-gray-900">{{ number_format($country->orders_count) }}</p>
                    </div>
                </div>

                <!-- Additional Metrics -->
                @if($country->orders_count > 0)
                    <div class="mt-4 pt-4 border-t flex items-center justify-between">
                        <div>
                            <span class="text-xs text-gray-500">Average Order Value</span>
                            <span class="ml-2 text-sm font-semibold text-gray-900">${{ number_format($country->total_revenue / $country->orders_count, 2) }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Avg Products per Vendor</span>
                            <span class="ml-2 text-sm font-semibold text-gray-900">{{ $country->vendors_count > 0 ? number_format($country->products_count / $country->vendors_count, 1) : 0 }}</span>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach

        @if($regionalData->isEmpty())
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-globe text-2xl text-gray-400"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-900">No Regional Data Available</p>
                    <p class="text-xs text-gray-500">Regional statistics will appear here once data is collected</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Regional Comparison Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Regional Performance Comparison</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Country</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Vendors</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Buyers</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Products</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Orders</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Revenue</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Avg Order</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($regionalData as $country)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">{{ $country->flag }}</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $country->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right text-sm text-gray-700">{{ number_format($country->vendors_count) }}</td>
                            <td class="px-4 py-3 text-right text-sm text-gray-700">{{ number_format($country->buyers_count ?? 0) }}</td>
                            <td class="px-4 py-3 text-right text-sm text-gray-700">{{ number_format($country->products_count) }}</td>
                            <td class="px-4 py-3 text-right text-sm text-gray-700">{{ number_format($country->orders_count) }}</td>
                            <td class="px-4 py-3 text-right text-sm font-semibold text-green-600">${{ number_format($country->total_revenue, 2) }}</td>
                            <td class="px-4 py-3 text-right text-sm text-gray-700">
                                ${{ $country->orders_count > 0 ? number_format($country->total_revenue / $country->orders_count, 2) : '0.00' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">No regional data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
flatpickr("#dateRangePicker", {
    mode: "range",
    dateFormat: "Y-m-d",
    showMonths: 2,
    locale: { rangeSeparator: " to " }
});
</script>
@endpush

@endsection

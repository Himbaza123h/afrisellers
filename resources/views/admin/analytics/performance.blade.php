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
            <h1 class="text-2xl font-bold text-gray-900">Performance Analytics</h1>
            <p class="mt-1 text-sm text-gray-500">Track clicks, impressions, and engagement metrics</p>
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

    <!-- Top Performing Products -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Performing Products (by Clicks)</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Rank</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Product</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Clicks</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Impressions</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">CTR</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($topPerformingProducts as $index => $performance)
                        @php
                            $ctr = $performance->total_impressions > 0 ? ($performance->total_clicks / $performance->total_impressions) * 100 : 0;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center w-8 h-8 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg">
                                    <span class="text-sm font-bold text-blue-700">{{ $index + 1 }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if($performance->product && $performance->product->featured_image)
                                        <img src="{{ Storage::url($performance->product->featured_image) }}" alt="{{ $performance->product->name }}" class="w-12 h-12 rounded-lg object-cover">
                                    @else
                                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-box text-gray-400"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $performance->product->name ?? 'Unknown' }}</p>
                                        <p class="text-xs text-gray-500">SKU: {{ $performance->product->sku ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                    <i class="fas fa-mouse-pointer mr-1"></i>{{ number_format($performance->total_clicks) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                    <i class="fas fa-eye mr-1"></i>{{ number_format($performance->total_impressions) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                @php
                                    $ctrClass = $ctr >= 5 ? 'bg-green-100 text-green-800' : ($ctr >= 2 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $ctrClass }}">
                                    {{ number_format($ctr, 2) }}%
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($performance->product)
                                    @if($performance->product->status === 'active')
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ ucfirst($performance->product->status) }}</span>
                                    @endif
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Deleted</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">No performance data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Detailed Performance Data -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">All Performance Records</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Product</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Vendor</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Country</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Clicks</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Impressions</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">CTR</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($performanceData as $performance)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-gray-900">{{ Str::limit($performance->product->name ?? 'Unknown', 30) }}</div>
                                <div class="text-xs text-gray-500">{{ $performance->product->sku ?? 'N/A' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm text-gray-900">{{ $performance->vendor->business_name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    @if($performance->country)
                                        <span class="text-xl">{{ $performance->country->flag }}</span>
                                        <span class="text-sm text-gray-700">{{ $performance->country->name }}</span>
                                    @else
                                        <span class="text-sm text-gray-500">N/A</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="text-sm font-semibold text-blue-600">{{ number_format($performance->clicks) }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="text-sm font-semibold text-purple-600">{{ number_format($performance->impressions) }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                @php
                                    $ctr = $performance->ctr;
                                    $ctrClass = $ctr >= 5 ? 'text-green-600' : ($ctr >= 2 ? 'text-yellow-600' : 'text-red-600');
                                @endphp
                                <span class="text-sm font-semibold {{ $ctrClass }}">{{ number_format($ctr, 2) }}%</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-xs text-gray-500">{{ $performance->created_at->format('M d, Y') }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">No performance data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($performanceData->hasPages())
            <div class="mt-4">
                {{ $performanceData->links() }}
            </div>
        @endif
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

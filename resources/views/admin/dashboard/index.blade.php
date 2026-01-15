@extends('layouts.home')

@section('page-content')
<!-- Welcome Section -->
<div class="mb-4 sm:mb-6">
    <p class="text-xs text-gray-500 mb-1">Welcome back,</p>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <h1 class="text-xl sm:text-2xl font-black text-gray-900 uppercase">{{ auth()->user()->name }}</h1>
        <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm w-fit">
            <span class="text-xs font-semibold text-gray-600">System Status</span>
            <span class="flex items-center gap-1.5 text-green-600 font-bold text-xs">
                <span class="w-1.5 h-1.5 bg-green-600 rounded-full animate-pulse"></span>
                Operational
            </span>
        </div>
    </div>
</div>

<!-- Quick Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">
    <!-- Global Revenue -->
    <div class="bg-white rounded-lg p-4 border border-gray-200 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-globe text-lg text-green-600"></i>
            </div>
            <span class="text-green-600 text-xs font-bold">This Month</span>
        </div>
        <p class="text-gray-600 text-xs mb-1">Global Revenue</p>
        <p class="text-2xl font-black text-gray-900">${{ number_format($globalRevenue, 0) }}</p>
    </div>

    <!-- Active Vendors -->
    <div class="bg-white rounded-lg p-4 border border-gray-200 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-store text-lg text-blue-600"></i>
            </div>
            <span class="text-blue-600 text-xs font-bold">Active</span>
        </div>
        <p class="text-gray-600 text-xs mb-1">Total Vendors</p>
        <p class="text-2xl font-black text-gray-900">{{ number_format($activeVendors) }}</p>
    </div>

    <!-- Total Orders -->
    <div class="bg-white rounded-lg p-4 border border-gray-200 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-shopping-cart text-lg text-purple-600"></i>
            </div>
            <span class="text-purple-600 text-xs font-bold">This Month</span>
        </div>
        <p class="text-gray-600 text-xs mb-1">Total Orders</p>
        <p class="text-2xl font-black text-gray-900">{{ number_format($totalOrders) }}</p>
    </div>

    <!-- Pending Approvals -->
    <div class="bg-white rounded-lg p-4 border border-gray-200 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-lg text-orange-600"></i>
            </div>
            <span class="text-orange-600 text-xs font-bold">Action Needed</span>
        </div>
        <p class="text-gray-600 text-xs mb-1">Pending Approvals</p>
        <p class="text-2xl font-black text-gray-900">{{ $pendingApprovals }}</p>
    </div>
</div>

<!-- Charts & Activity -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4 sm:mb-6">
    <!-- Chart Section -->
    <div class="lg:col-span-2 bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-2">
            <h3 class="text-sm font-bold text-gray-900">Regional Performance</h3>
            <div class="flex gap-1.5">
                <button class="px-3 py-1 text-xs font-bold text-white rounded-md bg-[#ff0808]">Weekly</button>
                <button class="px-3 py-1 text-xs text-gray-600 hover:bg-gray-100 rounded-md">Monthly</button>
                <button class="px-3 py-1 text-xs text-gray-600 hover:bg-gray-100 rounded-md">Yearly</button>
            </div>
        </div>
        <div class="h-64">
            <canvas id="regionalChart"></canvas>
        </div>
    </div>

    <!-- Activity Section -->
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Recent Activity</h3>
        <div class="space-y-3">
            @forelse($recentActivities as $activity)
            <div class="pb-3 border-b border-gray-100 last:border-0 last:pb-0">
                <div class="flex items-start gap-2 mb-2">
                    <div class="w-8 h-8 bg-{{ $activity['color'] }}-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-{{ $activity['icon'] }} text-{{ $activity['color'] }}-600 text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-900 truncate">{{ $activity['title'] }}</p>
                        <p class="text-[10px] text-gray-600 truncate">{{ $activity['description'] }}</p>
                    </div>
                </div>
                <div class="flex gap-1.5 ml-10">
                    @foreach($activity['actions'] ?? [] as $action)
                    <button class="px-2.5 py-1 bg-{{ $action['color'] }}-600 text-white text-[10px] font-bold rounded hover:bg-{{ $action['color'] }}-700 transition-colors">
                        <i class="fas fa-{{ $action['icon'] }} mr-1"></i>{{ $action['label'] }}
                    </button>
                    @endforeach
                </div>
            </div>
            @empty
            <div class="text-center py-4">
                <p class="text-xs text-gray-500">No pending activities</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Regional Statistics -->
<div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm mb-4 sm:mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-bold text-gray-900">Regional Statistics</h3>
        <button class="text-[#ff0808] font-bold hover:underline text-xs">View Details</button>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        @foreach($regionalStats as $region)
        <div class="p-3 bg-gradient-to-br from-{{ $region['color'] }}-50 to-white rounded-lg border border-{{ $region['color'] }}-100">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-gray-900">{{ $region['name'] }}</span>
                <span class="text-sm font-black text-gray-900">${{ number_format($region['revenue']/1000) }}k</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5 mb-2">
                <div class="bg-{{ $region['color'] }}-600 h-1.5 rounded-full" style="width: {{ $region['percentage'] }}%"></div>
            </div>
            <span class="inline-block text-[10px] bg-{{ $region['badge_color'] ?? $region['color'] }}-600 text-white px-2 py-0.5 rounded-full font-bold mb-1.5">{{ $region['status'] }}</span>
            <p class="text-[10px] text-gray-600">{{ number_format($region['vendors']) }} Vendors • {{ number_format($region['orders']) }} Orders</p>
        </div>
        @endforeach
    </div>
</div>

<!-- User Hierarchy Table -->
<div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-bold text-gray-900">User Hierarchy Overview</h3>
        <button class="text-[#ff0808] font-bold hover:underline text-xs">Manage All</button>
    </div>
    <div class="overflow-x-auto -mx-4 sm:mx-0">
        <table class="min-w-full">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="text-left py-2 px-3 text-[10px] font-bold text-gray-600 uppercase">Role</th>
                    <th class="text-left py-2 px-3 text-[10px] font-bold text-gray-600 uppercase">Total</th>
                    <th class="text-left py-2 px-3 text-[10px] font-bold text-gray-600 uppercase">Active</th>
                    <th class="text-left py-2 px-3 text-[10px] font-bold text-gray-600 uppercase">Pending</th>
                    <th class="text-left py-2 px-3 text-[10px] font-bold text-gray-600 uppercase">Suspended</th>
                    <th class="text-left py-2 px-3 text-[10px] font-bold text-gray-600 uppercase">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($userHierarchy as $hierarchy)
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                    <td class="py-2.5 px-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-{{ $hierarchy['color'] }}-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-{{ $hierarchy['icon'] }} text-{{ $hierarchy['color'] }}-600 text-xs"></i>
                            </div>
                            <span class="font-semibold text-gray-900 text-xs">{{ $hierarchy['role'] }}</span>
                        </div>
                    </td>
                    <td class="py-2.5 px-3 font-bold text-gray-900 text-xs">{{ number_format($hierarchy['total']) }}</td>
                    <td class="py-2.5 px-3">
                        <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-[10px] font-bold">{{ number_format($hierarchy['active']) }}</span>
                    </td>
                    <td class="py-2.5 px-3">
                        <span class="px-2 py-0.5 bg-orange-100 text-orange-700 rounded-full text-[10px] font-bold">{{ number_format($hierarchy['pending']) }}</span>
                    </td>
                    <td class="py-2.5 px-3">
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded-full text-[10px] font-bold">{{ number_format($hierarchy['suspended']) }}</span>
                    </td>
                    <td class="py-2.5 px-3">
                        <button class="text-[#ff0808] hover:underline font-bold text-xs">Manage</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Regional Performance Chart
const ctx = document.getElementById('regionalChart');
const regionalData = @json($regionalData);

const labels = Object.keys(regionalData).slice(0, 5);
const revenues = Object.values(regionalData).slice(0, 5).map(item => item.revenue);
const vendors = Object.values(regionalData).slice(0, 5).map(item => item.vendors);

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Revenue ($k)',
            data: revenues,
            backgroundColor: '#ff0808',
            borderRadius: 4,
        }, {
            label: 'Vendors',
            data: vendors,
            backgroundColor: '#3b82f6',
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'bottom',
                labels: {
                    padding: 10,
                    font: { size: 10 },
                    boxWidth: 30,
                    boxHeight: 10
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: '#f3f4f6' },
                ticks: { font: { size: 9 } }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 9 } }
            }
        }
    }
});
</script>
@endpush

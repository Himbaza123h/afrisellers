@extends('layouts.home')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .tab-content { animation: fadeIn 0.3s ease-in-out; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .custom-scrollbar::-webkit-scrollbar { height: 8px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #888; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #555; }
</style>
@endpush

@section('page-content')
<div class="space-y-4">
    <!-- Page Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Country Transporters Management</h1>
            <p class="mt-1 text-xs text-gray-500">Monitor transporters in {{ Auth::user()->country->name ?? 'your country' }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="window.open('{{ route('country.transporters.print') }}' + window.location.search, '_blank')" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex gap-2 border-b border-gray-200 no-print">
        <button onclick="switchTab('overview')" id="tab-overview" class="tab-button px-4 py-2 text-sm font-semibold text-blue-600 border-b-2 border-blue-600 transition-colors">
            <i class="fas fa-chart-line mr-2"></i> Overview
        </button>
        <button onclick="switchTab('transporters')" id="tab-transporters" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-truck mr-2"></i> Transporters
        </button>
        <button onclick="switchTab('analytics')" id="tab-analytics" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-chart-bar mr-2"></i> Analytics
        </button>
    </div>

    <!-- Overview Tab Content (Default) -->
    <div id="tab-overview-content" class="tab-content">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Transporters -->
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Transporters</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-flag mr-1 text-[8px]"></i> Country
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                        <i class="fas fa-truck text-blue-600"></i>
                    </div>
                </div>
            </div>

            <!-- Verified -->
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Verified</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['verified']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $stats['verified_percentage'] }}%
                            </span>
                            <span class="text-xs text-gray-500">of total</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
            </div>

            <!-- Active -->
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Active</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['active']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ $stats['active_percentage'] }}%
                            </span>
                            <span class="text-xs text-gray-500">of total</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg">
                        <i class="fas fa-user-check text-indigo-600"></i>
                    </div>
                </div>
            </div>

            <!-- Total Fleet -->
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Fleet</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_fleet']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                <i class="fas fa-car mr-1 text-[8px]"></i> Vehicles
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                        <i class="fas fa-shipping-fast text-purple-600"></i>
                    </div>
                </div>
            </div>

            <!-- Unverified -->
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Unverified</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['unverified']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1 text-[8px]"></i> Review
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg">
                        <i class="fas fa-hourglass-half text-yellow-600"></i>
                    </div>
                </div>
            </div>

            <!-- Suspended -->
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Suspended</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['suspended']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                <i class="fas fa-ban mr-1 text-[8px]"></i> Action
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg">
                        <i class="fas fa-pause-circle text-orange-600"></i>
                    </div>
                </div>
            </div>

            <!-- Average Rating -->
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Avg Rating</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['average_rating'] ?? 0, 1) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800">
                                <i class="fas fa-star mr-1 text-[8px]"></i> Rating
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-teal-50 to-teal-100 rounded-lg">
                        <i class="fas fa-star text-teal-600"></i>
                    </div>
                </div>
            </div>

            <!-- Inactive -->
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Inactive</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['inactive']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <i class="fas fa-power-off mr-1 text-[8px]"></i> Offline
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg">
                        <i class="fas fa-user-slash text-gray-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages -->
        @if(session('success'))
            <div class="p-4 bg-green-50 rounded-md border border-green-200 flex items-start gap-3 mt-4">
                <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
                <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
                <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mt-4">
            <form method="GET" action="{{ route('country.transporters.index') }}" class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                    <!-- Search -->
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search transporters..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    <!-- Verification -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Verification</label>
                        <select name="is_verified" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Status</option>
                            <option value="verified" {{ request('is_verified') == 'verified' ? 'selected' : '' }}>Verified</option>
                            <option value="unverified" {{ request('is_verified') == 'unverified' ? 'selected' : '' }}>Unverified</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>9:02 PM        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>

                <!-- Sort By -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Sort By</label>
                    <select name="sort_by" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date</option>
                        <option value="company_name" {{ request('sort_by') == 'company_name' ? 'selected' : '' }}>Company</option>
                        <option value="average_rating" {{ request('sort_by') == 'average_rating' ? 'selected' : '' }}>Rating</option>
                        <option value="total_deliveries" {{ request('sort_by') == 'total_deliveries' ? 'selected' : '' }}>Deliveries</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                    <i class="fas fa-filter text-sm"></i> Apply
                </button>
                <a href="{{ route('country.transporters.index') }}" class="inline-flex items-center gap-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                    <i class="fas fa-undo text-sm"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Transporters Tab Content (Hidden by default) -->
<div id="tab-transporters-content" class="tab-content hidden">
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-900">Transporters List</h2>
                <span class="px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">
                    {{ $transporters->total() }} {{ Str::plural('transporter', $transporters->total()) }}
                </span>
            </div>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Company</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Owner</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Contact</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Fleet</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Rating</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Verified</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($transporters as $transporter)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-semibold text-gray-900">{{ Str::limit($transporter->company_name ?? 'N/A', 20) }}</span>
                                    <span class="text-xs text-gray-500">{{ $transporter->registration_number ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center justify-center w-8 h-8 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex-shrink-0">
                                        <span class="text-xs font-semibold text-blue-700">{{ substr($transporter->user->name ?? 'U', 0, 1) }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900">{{ Str::limit($transporter->user->name ?? 'N/A', 15) }}</span>
                                        <span class="text-xs text-gray-500">{{ $transporter->created_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-900">{{ Str::limit($transporter->email ?? 'N/A', 20) }}</span>
                                    @if($transporter->phone)
                                        <span class="text-xs text-gray-500">{{ $transporter->phone }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-md text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ $transporter->fleet_size ?? 0 }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-star text-yellow-400 text-xs"></i>
                                    <span class="text-sm font-medium text-gray-900">{{ number_format($transporter->average_rating ?? 0, 1) }}</span>
                                    <span class="text-xs text-gray-500">({{ $transporter->total_deliveries ?? 0 }})</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($transporter->is_verified)
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Verified</span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Unverified</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusColors = [
                                        'active' => ['Active', 'bg-green-100 text-green-800'],
                                        'inactive' => ['Inactive', 'bg-gray-100 text-gray-800'],
                                        'suspended' => ['Suspended', 'bg-red-100 text-red-800'],
                                    ];
                                    $status = $statusColors[$transporter->status] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $status[1] }}">{{ $status[0] }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('country.transporters.show', $transporter->id) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-500">
                                    <i class="fas fa-truck text-4xl mb-3 text-gray-300"></i>
                                    <p class="text-base font-medium">No transporters found</p>
                                    <p class="text-sm">Transporters from your country will appear here</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($transporters, 'hasPages') && $transporters->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing {{ $transporters->firstItem() }}-{{ $transporters->lastItem() }} of {{ $transporters->total() }}
                </div>
                <div>
                    {{ $transporters->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Analytics Tab Content (Hidden by default) -->
<div id="tab-analytics-content" class="tab-content hidden">
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Transporter Analytics</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-sm font-medium text-gray-900 mb-2">Fleet Distribution</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_fleet']) }}</p>
                <p class="text-xs text-gray-500 mt-1">Total vehicles across all transporters</p>
            </div>

            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-sm font-medium text-gray-900 mb-2">Average Rating</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['average_rating'] ?? 0, 1) }}/5.0</p>
                <p class="text-xs text-gray-500 mt-1">Overall service quality</p>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    window.switchTab = function(tabName) {
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
            button.classList.add('text-gray-600');
        });

        document.getElementById('tab-' + tabName + '-content').classList.remove('hidden');

        const activeTab = document.getElementById('tab-' + tabName);
        activeTab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
        activeTab.classList.remove('text-gray-600');
    };

    // Auto-dismiss success messages
    const successAlert = document.querySelector('.bg-green-50');
    if (successAlert) {
        setTimeout(function() {
            successAlert.style.transition = 'opacity 0.3s';
            successAlert.style.opacity = '0';
            setTimeout(function() {
                successAlert.remove();
            }, 300);
        }, 5000);
    }
});
</script>
@endpush


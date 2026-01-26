@extends('layouts.home')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .badge-action {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    @media print {
        .no-print { display: none !important; }
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
            <h1 class="text-xl font-bold text-gray-900">Showrooms</h1>
            <p class="mt-1 text-xs text-gray-500">Manage your physical showroom locations and displays</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <button onclick="window.open('{{ route('vendor.showrooms.print') }}', '_blank')" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            <a href="{{ route('vendor.showrooms.create') }}"
               class="inline-flex items-center gap-2 px-3 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-plus"></i>
                <span>Add Showroom</span>
            </a>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex gap-2 border-b border-gray-200 no-print">
        <button onclick="switchTab('all')" id="tab-all" class="tab-button px-4 py-2 text-sm font-semibold text-blue-600 border-b-2 border-blue-600 transition-colors">
            All
        </button>
        <button onclick="switchTab('stats')" id="tab-stats" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            Stats
        </button>
        <button onclick="switchTab('showrooms')" id="tab-showrooms" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            Showrooms
        </button>
    </div>

    <!-- Stats Section -->
    <div id="stats-section" class="stats-container hidden">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Showrooms</p>
                        <p class="text-lg font-bold text-gray-900">{{ $showrooms->total() }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-building mr-1 text-[8px]"></i> All
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                        <i class="fas fa-building text-lg text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Products</p>
                        <p class="text-lg font-bold text-gray-900">{{ $showrooms->sum('products_count') }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                <i class="fas fa-box mr-1 text-[8px]"></i> In showrooms
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                        <i class="fas fa-box text-lg text-purple-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Views</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($showrooms->sum('views_count')) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-eye mr-1 text-[8px]"></i> All time
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                        <i class="fas fa-eye text-lg text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Inquiries</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($showrooms->sum('inquiries_count')) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                <i class="fas fa-envelope mr-1 text-[8px]"></i> Received
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-amber-50 to-amber-100 rounded-lg">
                        <i class="fas fa-envelope text-lg text-amber-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mt-4">
            <h3 class="text-base font-semibold text-gray-900 mb-3">Showroom Status</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @php
                    $activeCount = $showrooms->where('status', 'active')->count();
                    $verifiedCount = $showrooms->where('is_verified', true)->count();
                    $featuredCount = $showrooms->where('is_featured', true)->count();
                    $totalCount = $showrooms->total();
                @endphp

                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs font-medium text-gray-600 mb-1">Active</p>
                    <p class="text-lg font-bold text-gray-900">{{ $activeCount }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $totalCount > 0 ? number_format(($activeCount / $totalCount) * 100, 1) : 0 }}%
                    </p>
                </div>

                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs font-medium text-gray-600 mb-1">Verified</p>
                    <p class="text-lg font-bold text-gray-900">{{ $verifiedCount }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $totalCount > 0 ? number_format(($verifiedCount / $totalCount) * 100, 1) : 0 }}%
                    </p>
                </div>

                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs font-medium text-gray-600 mb-1">Featured</p>
                    <p class="text-lg font-bold text-gray-900">{{ $featuredCount }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $totalCount > 0 ? number_format(($featuredCount / $totalCount) * 100, 1) : 0 }}%
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Showrooms Section -->
    <div id="showrooms-section" class="showrooms-container">
        <!-- Messages -->
        @if(session('success'))
            <div class="p-4 bg-green-50 rounded-md border border-green-200 flex items-start gap-3">
                <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
                <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
                <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-red-50 rounded-md border border-red-200 flex items-start gap-3">
                <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
                <p class="text-sm font-medium text-red-900 flex-1">{{ session('error') }}</p>
                <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
            </div>
        @endif

        <!-- Showrooms Table -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-900">Showroom List</h2>
                    <span class="px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">
                        {{ $showrooms->total() }} {{ Str::plural('showroom', $showrooms->total()) }}
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Showroom</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Location</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Products</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Views</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($showrooms as $showroom)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="relative flex-shrink-0">
                                            @if($showroom->primary_image)
                                                <img src="{{ $showroom->primary_image }}" alt="{{ $showroom->name }}" class="w-12 h-12 rounded-lg object-cover border border-gray-200">
                                            @else
                                                <div class="w-12 h-12 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center border border-gray-200">
                                                    <i class="fas fa-building text-gray-400 text-sm"></i>
                                                </div>
                                            @endif
                                            @if($showroom->is_featured)
                                                <div class="absolute -top-1 -right-1">
                                                    <span class="inline-flex items-center justify-center w-4 h-4 bg-yellow-400 rounded-full">
                                                        <i class="fas fa-star text-white text-[6px]"></i>
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-semibold text-gray-900">{{ $showroom->name }}</span>
                                            <span class="text-xs text-gray-500">{{ $showroom->showroom_number ?? '' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900">{{ $showroom->city }}</span>
                                        <span class="text-xs text-gray-500">{{ $showroom->country->name ?? '' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-md text-xs font-medium bg-purple-100 text-purple-800">
                                        <i class="fas fa-box mr-1"></i>
                                        {{ $showroom->products_count }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-eye mr-1"></i>
                                        {{ number_format($showroom->views_count ?? 0) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-1">
                                        @if($showroom->status === 'active')
                                            <span class="badge-action bg-green-100 text-green-700">
                                                <i class="fas fa-circle text-[6px]"></i>
                                                Active
                                            </span>
                                        @else
                                            <span class="badge-action bg-gray-100 text-gray-700">
                                                <i class="fas fa-circle text-[6px]"></i>
                                                Inactive
                                            </span>
                                        @endif
                                        @if($showroom->is_verified)
                                            <span class="text-xs text-blue-600 flex items-center gap-1">
                                                <i class="fas fa-shield-check text-[8px]"></i> Verified
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('vendor.showrooms.show', $showroom->id) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium px-2 py-1 rounded hover:bg-blue-50" title="View">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        <a href="{{ route('vendor.showrooms.products', $showroom->id) }}" class="text-purple-600 hover:text-purple-700 text-sm font-medium px-2 py-1 rounded hover:bg-purple-50" title="Products">
                                            <i class="fas fa-box text-sm"></i>
                                        </a>
                                        <a href="{{ route('vendor.showrooms.edit', $showroom->id) }}" class="text-green-600 hover:text-green-700 text-sm font-medium px-2 py-1 rounded hover:bg-green-50" title="Edit">
                                            <i class="fas fa-edit text-sm"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-2">
                                            <i class="fas fa-building text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium">No showrooms yet</p>
                                        <p class="text-xs text-gray-400 mt-1">Create your first showroom</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($showrooms->hasPages())
                <div class="px-4 py-3 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-700">Showing {{ $showrooms->firstItem() }}-{{ $showrooms->lastItem() }} of {{ $showrooms->total() }}</span>
                        <div class="text-sm">{{ $showrooms->links() }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Tab Switching
function switchTab(tab) {
    // Remove active state from all tabs
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
        btn.classList.add('text-gray-600');
    });

    // Add active state to selected tab
    const activeTab = document.getElementById(`tab-${tab}`);
    activeTab.classList.remove('text-gray-600');
    activeTab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');

    // Show/hide sections
    const statsSection = document.getElementById('stats-section');
    const showroomsSection = document.getElementById('showrooms-section');

    switch(tab) {
        case 'all':
            statsSection.style.display = 'block';
            showroomsSection.style.display = 'block';
            break;
        case 'stats':
            statsSection.style.display = 'block';
            showroomsSection.style.display = 'none';
            break;
        case 'showrooms':
            statsSection.style.display = 'none';
            showroomsSection.style.display = 'block';
            break;
    }
}

// Initialize with All tab active
document.addEventListener('DOMContentLoaded', function() {
    switchTab('all');
});
</script>
@endpush

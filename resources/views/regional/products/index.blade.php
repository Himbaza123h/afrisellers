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
    .product-image { transition: transform 0.3s; }
    .product-image:hover { transform: scale(1.05); }
</style>
@endpush

@section('page-content')
<div class="space-y-4">
    <!-- Page Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Regional Products Management</h1>
            <p class="mt-1 text-xs text-gray-500">Monitor products across {{ $region->name }} region ({{ $countries->count() }} countries)</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="window.open('{{ route('regional.products.print') }}' + window.location.search, '_blank')" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
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
        <button onclick="switchTab('products')" id="tab-products" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-box mr-2"></i> Products
        </button>
        <button onclick="switchTab('countries')" id="tab-countries" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-globe mr-2"></i> Countries
        </button>
        <button onclick="switchTab('categories')" id="tab-categories" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-tag mr-2"></i> Categories
        </button>
        <button onclick="switchTab('verification')" id="tab-verification" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-shield-check mr-2"></i> Verification
        </button>
    </div>

    <!-- Overview Tab Content (Default) -->
    <div id="tab-overview-content" class="tab-content">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Products</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-globe mr-1 text-[8px]"></i> Regional
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                        <i class="fas fa-box text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Active Products</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['active']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $stats['active_percentage'] }}%
                            </span>
                            <span class="text-xs text-gray-500">of total</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Pending Approval</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['pending']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1 text-[8px]"></i> Awaiting
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Verified Products</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['verified']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ $stats['verified_percentage'] }}%
                            </span>
                            <span class="text-xs text-gray-500">of total</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg">
                        <i class="fas fa-shield-check text-indigo-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Unverified</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['unverified']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-exclamation-triangle mr-1 text-[8px]"></i> Needs review
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-red-50 to-red-100 rounded-lg">
                        <i class="fas fa-exclamation-circle text-red-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Views</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_views']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                <i class="fas fa-eye mr-1 text-[8px]"></i> All time
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                        <i class="fas fa-chart-line text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages -->
        @if(session('success'))
            <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-start gap-2 mt-4">
                <i class="fas fa-check-circle text-green-600 mt-0.5 text-sm"></i>
                <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
                <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times text-sm"></i></button>
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mt-4">
            <form method="GET" action="{{ route('regional.products.index') }}" class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                    <!-- Search -->
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    <!-- Country -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Country</label>
                        <select name="country_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Countries</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Date Range</label>
                        <input type="text" id="dateRangePicker" placeholder="Select dates" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg cursor-pointer text-sm">
                        <input type="hidden" name="date_from" id="dateFrom" value="{{ request('date_from') }}">
                        <input type="hidden" name="date_to" id="dateTo" value="{{ request('date_to') }}">
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                    <!-- Verification -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Verification</label>
                        <select name="verification" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All</option>
                            <option value="verified" {{ request('verification') == 'verified' ? 'selected' : '' }}>Verified</option>
                            <option value="unverified" {{ request('verification') == 'unverified' ? 'selected' : '' }}>Unverified</option>
                        </select>
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Category</label>
                        <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sort By -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Sort By</label>
                        <select name="sort_by" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date</option>
                            <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                            <option value="views" {{ request('sort_by') == 'views' ? 'selected' : '' }}>Views</option>
                            <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
                        </select>
                    </div>

                    <!-- Sort Order -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Order</label>
                        <select name="sort_order" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                            <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        <i class="fas fa-filter text-sm"></i> Apply
                    </button>
                    <a href="{{ route('regional.products.index') }}" class="inline-flex items-center gap-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                        <i class="fas fa-undo text-sm"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Tab Content (Hidden by default) -->
    <div id="tab-products-content" class="tab-content hidden">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-900">Products List</h2>
                    <span class="px-2 py-1 text-xs font-semibold text-gray-700 bg-gray-100 rounded-full">
                    {{ $countries->count() }} countries
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($countries as $country)
                    @php
                        $countryProducts = \App\Models\Product::where('country_id', $country->id)->count();
                        $colors = ['blue', 'green', 'purple', 'orange', 'red', 'indigo', 'yellow'];
                        $color = $colors[$loop->index % count($colors)];
                    @endphp
                    <div class="p-4 bg-gradient-to-br from-{{ $color }}-50 to-white rounded-lg border border-{{ $color }}-100">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-10 h-10 bg-{{ $color }}-100 rounded-lg">
                                <i class="fas fa-flag text-{{ $color }}-600"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-gray-900">{{ $country->name }}</h4>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-xs text-gray-600">{{ $countryProducts }} products</span>
                                    @if($countryProducts > 0)
                                        <span class="text-xs font-medium text-{{ $color }}-700">
                                            {{ $stats['total'] > 0 ? round(($countryProducts / $stats['total']) * 100) : 0 }}%
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Categories Tab Content -->
    <div id="tab-categories-content" class="tab-content hidden">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-900">Product Categories</h3>
                <span class="px-2 py-1 text-xs font-semibold text-gray-700 bg-gray-100 rounded-full">
                    {{ $categories->count() }} categories
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($categories as $category)
                    @php
                        $categoryCount = \App\Models\Product::whereIn('country_id', $countries->pluck('id'))
                            ->where('product_category_id', $category->id)
                            ->count();
                        $colors = ['blue', 'green', 'purple', 'orange', 'red', 'indigo', 'yellow'];
                        $color = $colors[$loop->index % count($colors)];
                    @endphp
                    <div class="p-4 bg-gradient-to-br from-{{ $color }}-50 to-white rounded-lg border border-{{ $color }}-100">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-10 h-10 bg-{{ $color }}-100 rounded-lg">
                                <i class="fas fa-tag text-{{ $color }}-600"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-gray-900">{{ $category->name }}</h4>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-xs text-gray-600">{{ $categoryCount }} products</span>
                                    @if($categoryCount > 0)
                                        <span class="text-xs font-medium text-{{ $color }}-700">
                                            {{ $stats['total'] > 0 ? round(($categoryCount / $stats['total']) * 100) : 0 }}%
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Verification Tab Content -->
    <div id="tab-verification-content" class="tab-content hidden">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-900">Verification Status</h3>
                <div class="flex gap-2">
                    <span class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">
                        {{ $stats['verified'] }} Verified
                    </span>
                    <span class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">
                        {{ $stats['unverified'] }} Unverified
                    </span>
                </div>
            </div>

            <!-- Verification Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="p-4 bg-gradient-to-br from-green-50 to-white rounded-lg border border-green-100">
                    <h4 class="text-sm font-semibold text-green-700 mb-2">Verified Products</h4>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['verified'] }}</p>
                    <div class="mt-2">
                        <div class="w-full bg-green-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ $stats['verified_percentage'] }}%"></div>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">{{ $stats['verified_percentage'] }}% of total products</p>
                    </div>
                </div>

                <div class="p-4 bg-gradient-to-br from-red-50 to-white rounded-lg border border-red-100">
                    <h4 class="text-sm font-semibold text-red-700 mb-2">Unverified Products</h4>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['unverified'] }}</p>
                    <div class="mt-2">
                        <div class="w-full bg-red-200 rounded-full h-2">
                            <div class="bg-red-600 h-2 rounded-full" style="width: {{ $stats['total'] > 0 ? round(($stats['unverified'] / $stats['total']) * 100) : 0 }}%"></div>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">
                            {{ $stats['total'] > 0 ? round(($stats['unverified'] / $stats['total']) * 100) : 0 }}% of total products
                        </p>
                    </div>
                </div>
            </div>

            <!-- Pending Approvals -->
            @php
                $pendingProducts = \App\Models\Product::whereIn('country_id', $countries->pluck('id'))
                    ->where('status', 'pending')
                    ->take(5)
                    ->get();
            @endphp

            @if($pendingProducts->count() > 0)
                <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Pending Approvals</h4>
                    <div class="space-y-3">
                        @foreach($pendingProducts as $product)
                            <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg hover:bg-yellow-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                                        <i class="fas fa-clock text-yellow-600 text-sm"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $product->country->name ?? 'N/A' }} • {{ $product->user->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="flex gap-1">
                                    <form action="{{ route('regional.products.approve', $product->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-700 text-sm font-medium px-2 py-1 rounded hover:bg-green-50" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('regional.products.reject', $product->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium px-2 py-1 rounded hover:bg-red-50" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // Date Range Picker
    flatpickr("#dateRangePicker", {
        mode: "range",
        dateFormat: "Y-m-d",
        showMonths: 2,
        onChange: function(selectedDates) {
            if (selectedDates.length === 2) {
                document.getElementById('dateFrom').value = flatpickr.formatDate(selectedDates[0], 'Y-m-d');
                document.getElementById('dateTo').value = flatpickr.formatDate(selectedDates[1], 'Y-m-d');
            }
        },
        defaultDate: [
            document.getElementById('dateFrom').value,
            document.getElementById('dateTo').value
        ].filter(d => d)
    });

    // Tab Switching Function
    function switchTab(tabName) {
        // Remove active state from all tabs
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
            btn.classList.add('text-gray-600');
        });

        // Add active state to selected tab
        const activeTab = document.getElementById(`tab-${tabName}`);
        activeTab.classList.remove('text-gray-600');
        activeTab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');

        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Show selected tab content
        document.getElementById(`tab-${tabName}-content`).classList.remove('hidden');
    }

    // Initialize with Overview tab active
    document.addEventListener('DOMContentLoaded', function() {
        switchTab('overview');
    });
</script>
@endpush
@endsection

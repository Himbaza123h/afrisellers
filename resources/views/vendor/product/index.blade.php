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
            <h1 class="text-xl font-bold text-gray-900">Products Management</h1>
            <p class="mt-1 text-xs text-gray-500">Manage your product inventory and listings</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <button onclick="window.open('{{ route('vendor.product.print') }}' + window.location.search, '_blank')" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            <button onclick="document.getElementById('exportForm').submit()" class="inline-flex items-center gap-2 px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-download"></i>
                <span>Export CSV</span>
            </button>
            <a href="{{ route('vendor.product.create') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-plus"></i>
                <span>Add Product</span>
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
        <button onclick="switchTab('products')" id="tab-products" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            Products
        </button>
    </div>

    <!-- Stats Section -->
    <div id="stats-section" class="stats-container hidden">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Products</p>
                        <p class="text-lg font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-box mr-1 text-[8px]"></i> All products
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                        <i class="fas fa-box text-xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Active Products</p>
                        <p class="text-lg font-bold text-gray-900">{{ $stats['active'] ?? 0 }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $stats['active_percentage'] ?? 0 }}%
                            </span>
                            <span class="text-xs text-gray-500">of total</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                        <i class="fas fa-check-circle text-xl text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Categories</p>
                        <p class="text-lg font-bold text-gray-900">{{ $stats['categories'] ?? 0 }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                {{ $stats['categorized'] ?? 0 }}
                            </span>
                            <span class="text-xs text-gray-500">categorized</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                        <i class="fas fa-layer-group text-xl text-purple-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Low Stock</p>
                        <p class="text-lg font-bold text-gray-900">{{ $stats['low_stock'] ?? 0 }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            @if(($stats['low_stock'] ?? 0) > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    <i class="fas fa-exclamation-triangle mr-1 text-[8px]"></i> Needs attention
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1 text-[8px]"></i> All good
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg">
                        <i class="fas fa-box-open text-xl text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mt-4">
            <h3 class="text-base font-semibold text-gray-900 mb-3">Status Distribution</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @php
                    $statusDistribution = [
                        'active' => ['count' => $stats['active'] ?? 0, 'color' => 'green', 'icon' => 'fa-check-circle'],
                        'inactive' => ['count' => $stats['inactive'] ?? 0, 'color' => 'red', 'icon' => 'fa-times-circle'],
                        'draft' => ['count' => $stats['draft'] ?? 0, 'color' => 'gray', 'icon' => 'fa-file'],
                        'pending' => ['count' => $stats['pending'] ?? 0, 'color' => 'yellow', 'icon' => 'fa-clock'],
                    ];
                @endphp

                @foreach($statusDistribution as $status => $data)
                    @if($data['count'] > 0)
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs font-medium text-gray-600 mb-1">{{ ucfirst($status) }}</p>
                            <p class="text-lg font-bold text-gray-900">{{ $data['count'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $stats['total'] > 0 ? number_format(($data['count'] / $stats['total']) * 100, 1) : 0 }}%
                            </p>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <div id="products-section" class="products-container">
        <!-- Filters -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 no-print">
            <form method="GET" action="{{ route('vendor.product.index') }}" class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                    <!-- Search -->
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
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

                    <!-- Status -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Date Range</label>
                        <input type="text" id="dateRange" placeholder="Select dates" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg cursor-pointer text-sm">
                        <input type="hidden" name="date_from" id="dateFrom" value="{{ request('date_from') }}">
                        <input type="hidden" name="date_to" id="dateTo" value="{{ request('date_to') }}">
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        <i class="fas fa-filter text-sm"></i> Apply
                    </button>
                    <a href="{{ route('vendor.product.index') }}" class="inline-flex items-center gap-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                        <i class="fas fa-undo text-sm"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Messages -->
        @if(session('success'))
            <div class="p-4 bg-green-50 rounded-md border border-green-200 flex items-start gap-3">
                <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
                <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
                <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 bg-red-50 rounded-md border border-red-200">
                <p class="text-sm font-medium text-red-900 mb-2">Please fix the following errors:</p>
                <ul class="space-y-1 text-sm text-red-700 list-disc list-inside">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <!-- Products Table -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden mt-4">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-900">Product List</h2>
                    <span class="px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">
                        {{ $products->total() }} {{ Str::plural('product', $products->total()) }}
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase w-8">
                                <input type="checkbox" class="w-4 h-4 rounded">
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Product</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Category</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Stock</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Price</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($products as $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <input type="checkbox" class="w-4 h-4 rounded">
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @php
                                            $img = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                                        @endphp
                                        @if($img)
                                            <img src="{{ $img->thumbnail_url ?? $img->image_url }}" alt="{{ $product->name }}"
                                                class="w-10 h-10 rounded-md object-cover border border-gray-200">
                                        @else
                                            <div class="w-10 h-10 bg-gray-100 rounded-md flex items-center justify-center">
                                                <i class="fas fa-image text-gray-400 text-sm"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 truncate max-w-[180px]">
                                                {{ Str::limit($product->name, 30) }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $product->sku ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($product->productCategory)
                                        <span class="px-2 py-1 text-xs font-medium text-gray-700 bg-gray-100 rounded">
                                            {{ $product->productCategory->name }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">Uncategorized</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @php $stock = $product->min_order_quantity ?? 0; @endphp
                                    @if($stock == 0)
                                        <span class="badge-action bg-red-100 text-red-700">
                                            <i class="fas fa-times text-xs"></i>
                                            Out of Stock
                                        </span>
                                    @elseif($stock < 50)
                                        <span class="badge-action bg-orange-100 text-orange-700">
                                            <i class="fas fa-exclamation-triangle text-xs"></i>
                                            {{ $stock }} - Low
                                        </span>
                                    @else
                                        <span class="badge-action bg-green-100 text-green-700">
                                            <i class="fas fa-check text-xs"></i>
                                            {{ $stock }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($product->prices->first())
                                        @php
                                            $price = $product->prices->first();
                                            $currencySymbols = [
                                                'USD' => '$',
                                                'EUR' => '€',
                                                'GBP' => '£',
                                                'RWF' => 'RF',
                                                'KES' => 'KSh',
                                                'UGX' => 'USh',
                                                'TZS' => 'TSh',
                                            ];
                                            $symbol = $currencySymbols[$price->currency] ?? $price->currency;
                                        @endphp
                                        <span class="text-sm font-semibold text-gray-900">{{ $symbol }} {{ number_format($price->price, 2) }}</span>
                                    @else
                                        <span class="text-sm text-gray-400">No price</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusConfig = [
                                            'active' => ['Published', 'bg-green-100 text-green-700'],
                                            'inactive' => ['Inactive', 'bg-red-100 text-red-700'],
                                            'draft' => ['Draft', 'bg-gray-100 text-gray-700']
                                        ];
                                        $statusData = $statusConfig[$product->status] ?? ['Unknown', 'bg-yellow-100 text-yellow-700'];
                                    @endphp
                                    <span class="badge-action {{ $statusData[1] }}">
                                        <i class="fas fa-circle text-[6px]"></i>
                                        {{ $statusData[0] }}
                                    </span>
                                    @if($product->is_negotiable)
                                        <span class="ml-2 px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                            <i class="fas fa-handshake text-xs mr-1"></i>Negotiable
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('vendor.product.show', $product) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium px-2 py-1 rounded hover:bg-blue-50">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        <a href="{{ route('vendor.product.edit', $product) }}" class="text-green-600 hover:text-green-700 text-sm font-medium px-2 py-1 rounded hover:bg-green-50">
                                            <i class="fas fa-edit text-sm"></i>
                                        </a>
                                        <form action="{{ route('vendor.product.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Delete this product?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium px-2 py-1 rounded hover:bg-red-50">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-2">
                                            <i class="fas fa-box-open text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium">No products found</p>
                                        <p class="text-xs text-gray-400 mt-1">Try adjusting your filters</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div class="px-4 py-3 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-700">Showing {{ $products->firstItem() }}-{{ $products->lastItem() }} of {{ $products->total() }}</span>
                        <div class="text-sm">{{ $products->links() }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Hidden Export Form -->
<form id="exportForm" action="{{ route('vendor.product.export') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="search" value="{{ request('search') }}">
    <input type="hidden" name="category" value="{{ request('category') }}">
    <input type="hidden" name="status" value="{{ request('status') }}">
    <input type="hidden" name="date_from" value="{{ request('date_from') }}">
    <input type="hidden" name="date_to" value="{{ request('date_to') }}">
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
// Date Range Picker
flatpickr("#dateRange", {
    mode: "range",
    dateFormat: "Y-m-d",
    showMonths: 2,
    onChange: function(selectedDates) {
        if (selectedDates.length === 2) {
            document.getElementById('dateFrom').value = flatpickr.formatDate(selectedDates[0], 'Y-m-d');
            document.getElementById('dateTo').value = flatpickr.formatDate(selectedDates[1], 'Y-m-d');
        }
    },
    defaultDate: [document.getElementById('dateFrom').value, document.getElementById('dateTo').value].filter(d => d)
});

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
    const productsSection = document.getElementById('products-section');

    switch(tab) {
        case 'all':
            statsSection.style.display = 'none';
            productsSection.style.display = 'block';
            break;
        case 'stats':
            statsSection.style.display = 'block';
            productsSection.style.display = 'none';
            break;
        case 'products':
            statsSection.style.display = 'none';
            productsSection.style.display = 'block';
            break;
    }
}

// Initialize with All tab active
document.addEventListener('DOMContentLoaded', function() {
    switchTab('all');
});
</script>
@endpush

@endsection

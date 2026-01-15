@extends('layouts.home')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .custom-scrollbar::-webkit-scrollbar { height: 8px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #888; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #555; }
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
</style>
@endpush

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Products Management</h1>
            <p class="mt-1 text-sm text-gray-500">Manage your product inventory and listings</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-download"></i>
                <span>Import</span>
            </button>
            <button class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-upload"></i>
                <span>Export</span>
            </button>
            <a href="{{ route('vendor.product.create') }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-[#ff0808] text-white rounded-md hover:bg-red-700 transition-colors font-bold shadow-md text-sm">
                <i class="fas fa-plus"></i>
                <span>Add Product</span>
            </a>

        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Products</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-arrow-up mr-1 text-[10px]"></i> 12%
                        </span>
                        <span class="text-xs text-gray-500">from last month</span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                    <i class="fas fa-box text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Active Products</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['active'] ?? 0 }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $stats['active_percentage'] ?? 0 }}%
                        </span>
                        <span class="text-xs text-gray-500">of total</span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-green-50 to-green-100 rounded-xl">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Categories</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['categories'] ?? 0 }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            {{ $stats['categorized'] ?? 0 }}
                        </span>
                        <span class="text-xs text-gray-500">categorized</span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl">
                    <i class="fas fa-layer-group text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Low Stock Alert</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['low_stock'] ?? 0 }}</p>
                    <div class="mt-3">
                        @if(($stats['low_stock'] ?? 0) > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                <i class="fas fa-exclamation-triangle mr-1 text-[10px]"></i> Needs attention
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1 text-[10px]"></i> All good
                            </span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl">
                    <i class="fas fa-box-open text-2xl text-orange-600"></i>
                </div>
            </div>
        </div>
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

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <form method="GET" action="{{ route('vendor.product.index') }}" class="space-y-4">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 mt-2"></i>
            </div>

            <div class="flex flex-wrap gap-3 items-center">
                <label class="text-sm font-medium text-gray-700">Filters:</label>

                <div class="relative">
                    <input type="text" id="dateRangePicker" name="date_range" value="{{ request('date_range') }}" readonly placeholder="Date range" class="pl-10 pr-4 py-2.5 border border-gray-300 rounded-md w-56 cursor-pointer bg-white">
                    <i class="fas fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none mt-2"></i>
                </div>

                <select name="status" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-md appearance-none bg-white">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>

                <select name="category" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-md appearance-none bg-white">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>

                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium">
                    <i class="fas fa-filter"></i> Apply
                </button>

                @if(request()->hasAny(['search', 'date_range', 'status', 'category']))
                    <a href="{{ route('vendor.product.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 font-medium">
                        <i class="fas fa-times"></i> Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-hidden bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-left w-12"><input type="checkbox" class="w-4 h-4 rounded"></th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Product</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Category</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Stock</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Price</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4"><input type="checkbox" class="w-4 h-4 rounded"></td>
                            <td class="px-6 py-4">
                                <div class="flex gap-3 items-center">
                                    @php $img = $product->images->where('is_primary', true)->first() ?? $product->images->first(); @endphp
                                    @if($img)
                                        <img src="{{ $img->thumbnail_url ?? $img->image_url }}" alt="{{ $product->name }}" class="w-12 h-12 rounded-md object-cover border">
                                    @else
                                        <div class="w-12 h-12 bg-gray-100 rounded-md flex items-center justify-center"><i class="fas fa-image text-gray-400"></i></div>
                                    @endif
                                    <span class="text-sm font-medium text-gray-900">{{ Str::limit($product->name, 40) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($product->productCategory)
                                    <span class="px-2.5 py-1 rounded-md text-xs font-medium bg-purple-100 text-purple-800">{{ $product->productCategory->name }}</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-600">Uncategorized</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php $stock = $product->stock_quantity ?? 0; @endphp
                                @if($stock == 0)
                                    <span class="px-2.5 py-1 rounded-md text-xs font-medium bg-red-100 text-red-800">Out of Stock</span>
                                @elseif($stock < 50)
                                    <span class="px-2.5 py-1 rounded-md text-xs font-medium bg-orange-100 text-orange-800">{{ $stock }} - Low</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-md text-xs font-medium bg-green-100 text-green-800">{{ $stock }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
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
                            <td class="px-6 py-4">
                                @php
                                    $sc = ['active' => ['Published', 'bg-green-100 text-green-800'], 'inactive' => ['Inactive', 'bg-red-100 text-red-800'], 'draft' => ['Draft', 'bg-gray-100 text-gray-800']];
                                    $s = $sc[$product->status] ?? ['Unknown', 'bg-yellow-100 text-yellow-800'];
                                @endphp
                                <span class="px-3 py-1.5 rounded-full text-xs font-medium {{ $s[1] }}">{{ $s[0] }}</span>
                                @if($product->is_negotiable)
                                    <span class="ml-2 px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                        <i class="fas fa-handshake mr-1"></i>Negotiable
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('vendor.product.show', $product) }}" class="p-2 text-gray-600 rounded-md hover:bg-blue-50 hover:text-blue-600"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('vendor.product.edit', $product) }}" class="p-2 text-gray-600 rounded-md hover:bg-green-50 hover:text-green-600"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('vendor.product.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Delete this product?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-600 rounded-md hover:bg-red-50 hover:text-red-600"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-box-open text-4xl text-gray-300"></i>
                                    </div>
                                    <p class="text-lg font-semibold text-gray-900 mb-1">No products found</p>
                                    <p class="text-sm text-gray-500 mb-6">Get started by adding your first product</p>
                                    <a href="{{ route('vendor.product.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-gradient-to-r from-[#6366f1] to-[#5558e3] text-white text-sm rounded font-medium shadow-sm hover:shadow-md hover:from-[#5558e3] hover:to-[#4a4dd4] transition-all duration-200 active:scale-95">
                                        <i class="fas fa-plus text-xs"></i>
                                        <span>Add Product</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($products, 'hasPages') && $products->hasPages())
            <div class="px-6 py-4 border-t bg-gray-50">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-700">Showing {{ $products->firstItem() }}-{{ $products->lastItem() }} of {{ $products->total() }}</span>
                    <div>{{ $products->links() }}</div>
                </div>
            </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
flatpickr("#dateRangePicker", {
    mode: "range",
    dateFormat: "Y-m-d",
    showMonths: 2,
    locale: { rangeSeparator: " to " },
    onClose: function(dates, str, inst) {
        if (dates.length === 2) inst.element.closest('form').submit();
    }
});

document.addEventListener('click', function(e) {
    if (!e.target.closest('button')) {
        document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.add('hidden'));
    }
});
</script>
@endsection

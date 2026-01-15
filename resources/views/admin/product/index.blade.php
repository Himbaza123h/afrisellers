@extends('layouts.home')

@push('styles')
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
            <p class="mt-1 text-sm text-gray-500">
                @if(auth()->user()->hasRole('admin'))
                    View and verify all products
                @else
                    Manage your products and inventory
                @endif
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            <button class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-download"></i>
                <span>Export</span>
            </button>
            @if(auth()->user()->isVendor() && !auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.product.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 transition-all font-medium shadow-sm">
                    <i class="fas fa-plus"></i>
                    <span>Add Product</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Statistics Cards -->
    @if(auth()->user()->hasRole('admin'))
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-600 mb-1">Total Products</p>
                        <p class="text-lg font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                        <div class="mt-3 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-box mr-1 text-[10px]"></i> All vendors
                            </span>
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
                        <p class="text-sm font-medium text-gray-600 mb-1">Verified Products</p>
                        <p class="text-lg font-bold text-gray-900">{{ $stats['verified'] ?? 0 }}</p>
                        <div class="mt-3 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $stats['total'] > 0 ? round(($stats['verified'] / $stats['total']) * 100, 1) : 0 }}%
                            </span>
                            <span class="text-xs text-gray-500">of total</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                        <i class="fas fa-check-circle text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-600 mb-1">Unverified Products</p>
                        <p class="text-lg font-bold text-gray-900">{{ $stats['unverified'] ?? 0 }}</p>
                        <div class="mt-3 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                {{ $stats['total'] > 0 ? round(($stats['unverified'] / $stats['total']) * 100, 1) : 0 }}%
                            </span>
                            <span class="text-xs text-gray-500">pending</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl">
                        <i class="fas fa-exclamation-circle text-2xl text-yellow-600"></i>
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
                            <span class="text-xs text-gray-500">live now</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-green-50 to-green-100 rounded-xl">
                        <i class="fas fa-check text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-600 mb-1">Total Products</p>
                        <p class="text-lg font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                        <div class="mt-3 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-box mr-1 text-[10px]"></i> Your products
                            </span>
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
                        <p class="text-sm font-medium text-gray-600 mb-1">Inactive Products</p>
                        <p class="text-lg font-bold text-gray-900">{{ $stats['inactive'] ?? 0 }}</p>
                        <div class="mt-3 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $stats['inactive_percentage'] ?? 0 }}%
                            </span>
                            <span class="text-xs text-gray-500">of total</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl">
                        <i class="fas fa-pause-circle text-2xl text-gray-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-600 mb-1">Draft Products</p>
                        <p class="text-lg font-bold text-gray-900">{{ $stats['draft'] ?? 0 }}</p>
                        <div class="mt-3 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-pen mr-1 text-[10px]"></i> Unpublished
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl">
                        <i class="fas fa-file-alt text-2xl text-yellow-600"></i>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Messages -->
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <p class="text-sm font-medium text-red-900 flex-1">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-red-50 rounded-lg border border-red-200">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
                <div class="flex-1">
                    <p class="text-sm font-medium text-red-900 mb-2">Please fix the following errors:</p>
                    <ul class="space-y-1 text-sm text-red-700">
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
            </div>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <form method="GET" action="{{ route('admin.product.index') }}" class="space-y-4">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by product name, description, or SKU..." class="w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 mt-2"></i>
            </div>

            <div class="flex flex-wrap gap-3 items-center">
                <label class="text-sm font-medium text-gray-700">Filters:</label>

                <select name="category_id" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>

                <select name="status" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>

                @if(auth()->user()->hasRole('admin'))
                    <select name="verified" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                        <option value="">All Verification</option>
                        <option value="yes" {{ request('verified') == 'yes' ? 'selected' : '' }}>Verified</option>
                        <option value="no" {{ request('verified') == 'no' ? 'selected' : '' }}>Unverified</option>
                    </select>
                @endif

                <select name="sort_by" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Sort by Date</option>
                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                    <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
                    <option value="views" {{ request('sort_by') == 'views' ? 'selected' : '' }}>Views</option>
                </select>

                <select name="sort_order" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                </select>

                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    <i class="fas fa-filter"></i> Apply
                </button>

                @if(request()->hasAny(['search', 'category_id', 'status', 'verified', 'sort_by']))
                    <a href="{{ route('admin.product.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
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
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Vendor</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Category</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Price</th>
                        @if(auth()->user()->isVendor() && !auth()->user()->hasRole('admin'))
                            <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                        @endif
                        @if(auth()->user()->hasRole('admin'))
                            <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Verified</th>
                        @endif
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Created</th>
                        <th class="px-6 py-4 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4"><input type="checkbox" class="w-4 h-4 rounded"></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @php
                                        $featuredImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                                    @endphp
                                    @if($featuredImage)
                                        <img src="{{ $featuredImage->thumbnail_url ?? $featuredImage->image_url }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded-lg border border-gray-200 shadow-sm">
                                    @else
                                        <div class="w-12 h-12 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center border border-gray-200">
                                            <i class="text-gray-400 fas fa-image"></i>
                                        </div>
                                    @endif
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-900">{{ \Illuminate\Support\Str::limit($product->name, 40) }}</span>
                                        @if($product->short_description)
                                            <span class="text-xs text-gray-500 mt-0.5">{{ \Illuminate\Support\Str::limit($product->short_description, 50) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $vendor = $product->user->vendor ?? null;
                                    $businessProfile = $vendor->businessProfile ?? null;
                                @endphp
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-900">
                                        @if($businessProfile)
                                            {{ $businessProfile->business_name }}
                                        @elseif($product->user)
                                            {{ $product->user->name }}
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                    @if($product->user && $product->user->email)
                                        <span class="text-xs text-gray-500">{{ $product->user->email }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($product->productCategory)
                                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">{{ $product->productCategory->name }}</span>
                                @else
                                    <span class="text-xs text-gray-400 italic">Uncategorized</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $firstPriceTier = $product->prices->first();
                                @endphp
                                @if($firstPriceTier)
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-900">{{ number_format($firstPriceTier->price, 0) }} {{ $firstPriceTier->currency }}</span>
                                        @if($product->prices->count() > 1)
                                            <span class="text-xs text-gray-500">{{ $product->prices->count() }} price tiers</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">No price set</span>
                                @endif
                            </td>
                            @if(auth()->user()->isVendor() && !auth()->user()->hasRole('admin'))
                                <td class="px-6 py-4">
                                    <form action="{{ route('admin.product.toggle-status', $product) }}" method="POST" class="inline">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="px-3 py-1.5 rounded-full text-xs font-medium transition-colors
                                            {{ $product->status === 'active' ? 'bg-green-100 text-green-800 hover:bg-green-200' :
                                               ($product->status === 'inactive' ? 'bg-gray-100 text-gray-800 hover:bg-gray-200' : 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200') }}">
                                            {{ ucfirst($product->status) }}
                                        </button>
                                    </form>
                                </td>
                            @endif
                            @if(auth()->user()->hasRole('admin'))
                                <td class="px-6 py-4">
                                    <form action="{{ route('admin.product.toggle-verification', $product) }}" method="POST" class="inline">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="px-3 py-1.5 rounded-full text-xs font-medium transition-colors
                                            {{ $product->is_admin_verified ? 'bg-blue-100 text-blue-800 hover:bg-blue-200' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                                            <i class="fas fa-{{ $product->is_admin_verified ? 'check' : 'times' }}-circle mr-1"></i>
                                            {{ $product->is_admin_verified ? 'Verified' : 'Unverified' }}
                                        </button>
                                    </form>
                                </td>
                            @endif
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm text-gray-900">{{ $product->created_at->format('M d, Y') }}</span>
                                    <span class="text-xs text-gray-500">{{ $product->created_at->format('h:i A') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.product.show', $product) }}" class="p-2 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(auth()->user()->isVendor() && !auth()->user()->hasRole('admin'))
                                        <a href="{{ route('admin.product.edit', $product) }}" class="p-2 text-gray-600 rounded-lg hover:bg-yellow-50 hover:text-yellow-600" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.product.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-gray-600 rounded-lg hover:bg-red-50 hover:text-red-600" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->hasRole('admin') ? '9' : '9' }}" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-box text-4xl text-gray-300"></i>
                                    </div>
                                    <p class="text-lg font-semibold text-gray-900 mb-1">No products found</p>
                                    <p class="text-sm text-gray-500 mb-6">
                                        @if(auth()->user()->hasRole('admin'))
                                            No products have been added yet.
                                        @else
                                            Get started by adding your first product
                                        @endif
                                    </p>
                                    @if(auth()->user()->isVendor() && !auth()->user()->hasRole('admin'))
                                        <a href="{{ route('admin.product.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 transition-all font-medium">
                                            <i class="fas fa-plus"></i>
                                            <span>Add Product</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <!-- Results Info -->
                    <div class="text-sm text-gray-700">
                        Showing
                        <span class="font-medium">{{ $products->firstItem() ?? 0 }}</span>
                        to
                        <span class="font-medium">{{ $products->lastItem() ?? 0 }}</span>
                        of
                        <span class="font-medium">{{ $products->total() }}</span>
                        results
                    </div>

                    <!-- Pagination Links -->
                    <div class="flex items-center gap-2">
                        {{-- Previous Button --}}
                        @if ($products->onFirstPage())
                            <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 rounded-lg cursor-not-allowed">
                                <i class="fas fa-chevron-left"></i>
                            </span>
                        @else
                            <a href="{{ $products->previousPageUrl() }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        @endif

                        {{-- Page Numbers --}}
                        <div class="hidden sm:flex items-center gap-2">
                            @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                                @if ($page == $products->currentPage())
                                    <span class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-lg">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        </div>

                        {{-- Mobile Current Page Info --}}
                        <div class="sm:hidden px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg">
                            {{ $products->currentPage() }} / {{ $products->lastPage() }}
                        </div>

                        {{-- Next Button --}}
                        @if ($products->hasMorePages())
                            <a href="{{ $products->nextPageUrl() }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        @else
                            <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 rounded-lg cursor-not-allowed">
                                <i class="fas fa-chevron-right"></i>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-hide success/error messages after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50');
        alerts.forEach(function(alert) {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 500);
        });
    }, 5000);

    // Handle "select all" checkbox functionality
    const selectAllCheckbox = document.querySelector('thead input[type="checkbox"]');
    const rowCheckboxes = document.querySelectorAll('tbody input[type="checkbox"]');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }

    // Update "select all" checkbox state when individual checkboxes change
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(rowCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(rowCheckboxes).some(cb => cb.checked);

            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
            }
        });
    });

    // Print functionality
    window.addEventListener('beforeprint', function() {
        // Hide action buttons and checkboxes when printing
        document.querySelectorAll('input[type="checkbox"], .fas.fa-eye, .fas.fa-edit, .fas.fa-trash').forEach(el => {
            el.style.display = 'none';
        });
    });

    window.addEventListener('afterprint', function() {
        // Show action buttons and checkboxes after printing
        document.querySelectorAll('input[type="checkbox"], .fas.fa-eye, .fas.fa-edit, .fas.fa-trash').forEach(el => {
            el.style.display = '';
        });
    });
</script>
@endpush

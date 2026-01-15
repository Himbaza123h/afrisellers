@extends('layouts.home')

@push('styles')
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    @media print {
        .no-print { display: none; }
    }
</style>
@endpush

@section('page-content')
<div class="space-y-6">
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

    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.product-category.index') }}" class="flex items-center justify-center w-10 h-10 text-gray-600 transition-colors bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-gray-900 shadow-sm">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Category Details</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $productCategory->name }}</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-3 no-print">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
        </div>
    </div>

    <!-- Category Information Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-600"></i>
                Category Information
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="flex items-start gap-3">
                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                        <i class="fas fa-boxes text-blue-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 mb-1">Category Name</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $productCategory->name }}</p>
                        <p class="text-xs text-gray-500">ID: {{ $productCategory->id }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg">
                        <i class="fas fa-info text-yellow-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 mb-1">Status</p>
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium {{ $productCategory->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($productCategory->status) }}
                        </span>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                        <i class="fas fa-calendar text-purple-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 mb-1">Created At</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $productCategory->created_at->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $productCategory->created_at->format('h:i A') }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                        <i class="fas fa-clock text-green-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 mb-1">Last Updated</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $productCategory->updated_at->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $productCategory->updated_at->format('h:i A') }}</p>
                    </div>
                </div>
            </div>

            <!-- Countries Section -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-xs font-medium text-gray-500 mb-3 flex items-center gap-2">
                    <i class="fas fa-globe text-gray-400"></i>
                    Available Countries
                </p>
                @if($productCategory->countries->count() > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($productCategory->countries as $country)
                            <span class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium bg-blue-50 text-blue-800 rounded-lg border border-blue-100">
                                @if($country->flag_url)
                                    <img src="{{ $country->flag_url }}" alt="{{ $country->name }}" class="w-5 h-4 object-cover rounded border border-gray-300">
                                @endif
                                {{ $country->name }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <div class="p-4 bg-purple-50 rounded-lg border border-purple-100">
                        <p class="text-sm text-purple-800 font-medium">
                            <i class="fas fa-globe mr-2"></i>Global Category (Available in all countries)
                        </p>
                    </div>
                @endif
            </div>

            <!-- Description Section -->
            @if($productCategory->description)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-xs font-medium text-gray-500 mb-2 flex items-center gap-2">
                        <i class="fas fa-align-left text-gray-400"></i>
                        Description
                    </p>
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $productCategory->description }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Products</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $productCategory->products->count() }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-tag mr-1 text-[10px]"></i> In this category
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                    <i class="fas fa-tag text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Countries</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $productCategory->countries->count() }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <i class="fas fa-globe mr-1 text-[10px]"></i> {{ $productCategory->countries->count() > 0 ? 'Specific' : 'Global' }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl">
                    <i class="fas fa-globe text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Status</p>
                    <p class="text-2xl font-bold text-gray-900">{{ ucfirst($productCategory->status) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $productCategory->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            <i class="fas fa-{{ $productCategory->status === 'active' ? 'check' : 'pause' }}-circle mr-1 text-[10px]"></i> {{ $productCategory->status === 'active' ? 'Live' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-{{ $productCategory->status === 'active' ? 'green' : 'gray' }}-50 to-{{ $productCategory->status === 'active' ? 'green' : 'gray' }}-100 rounded-xl">
                    <i class="fas fa-{{ $productCategory->status === 'active' ? 'check' : 'pause' }}-circle text-2xl text-{{ $productCategory->status === 'active' ? 'green' : 'gray' }}-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Section (if any) -->
    @if($productCategory->products->count() > 0)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-tag text-gray-600"></i>
                    Products in this Category ({{ $productCategory->products->count() }})
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Product Name</th>
                            <th class="px-6 py-3 text-xs font-semibold text-left text-gray-700 uppercase">SKU</th>
                            <th class="px-6 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                            <th class="px-6 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Created</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($productCategory->products->take(10) as $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium text-gray-900">{{ $product->name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-600">{{ $product->sku ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $product->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-600">{{ $product->created_at->format('M d, Y') }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($productCategory->products->count() > 10)
                <div class="p-4 bg-gray-50 text-center border-t">
                    <p class="text-sm text-gray-600">Showing 10 of {{ $productCategory->products->count() }} products</p>
                </div>
            @endif
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 no-print">
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.product-category.edit', $productCategory) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors font-semibold shadow-md">
                <i class="fas fa-edit"></i>
                <span>Edit Category</span>
            </a>

            <form action="{{ route('admin.product-category.toggle-status', $productCategory) }}" method="POST" class="inline">
                @csrf
                @method('POST')
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 text-white rounded-lg transition-colors font-semibold shadow-md {{ $productCategory->status === 'active' ? 'bg-gray-600 hover:bg-gray-700' : 'bg-green-600 hover:bg-green-700' }}">
                    <i class="fas fa-{{ $productCategory->status === 'active' ? 'pause' : 'play' }}"></i>
                    <span>{{ $productCategory->status === 'active' ? 'Deactivate' : 'Activate' }}</span>
                </button>
            </form>

            <form action="{{ route('admin.product-category.destroy', $productCategory) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this category? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-semibold shadow-md">
                    <i class="fas fa-trash"></i>
                    <span>Delete Category</span>
                </button>
            </form>

            <a href="{{ route('admin.product-category.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-semibold ml-auto">
                <i class="fas fa-arrow-left"></i>
                <span>Back to List</span>
            </a>
        </div>
    </div>
</div>
@endsection

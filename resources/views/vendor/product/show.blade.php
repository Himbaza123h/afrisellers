@extends('layouts.home')

@push('styles')
<style>
    .info-card { transition: transform 0.2s, box-shadow 0.2s; }
    .info-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
</style>
@endpush

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('vendor.product.index') }}" class="p-2 text-gray-600 rounded-md hover:bg-gray-100 transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Product Details</h1>
                <p class="mt-1 text-sm text-gray-500">View and manage product information</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('vendor.product.price.edit', $product) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-md transition-all font-medium shadow-sm">
                <i class="fas fa-dollar-sign"></i>
                <span>Set Price</span>
            </a>
            <a href="{{ route('vendor.product.promo.edit', $product) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-md transition-all font-medium shadow-sm">
                <i class="fas fa-ticket-alt"></i>
                <span>Manage Promo Codes</span>
            </a>
            <form action="{{ route('vendor.product.toggle-status', $product) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 {{ $product->status === 'active' ? 'bg-gray-600 hover:bg-gray-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-md transition-all font-medium shadow-sm">
                    <i class="fas fa-{{ $product->status === 'active' ? 'pause' : 'play' }}"></i>
                    <span>{{ $product->status === 'active' ? 'Deactivate' : 'Activate' }}</span>
                </button>
            </form>
            <a href="{{ route('vendor.product.edit', $product) }}" class="inline-flex items-center gap-2 px-3 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-md transition-all font-medium shadow-sm">
                <i class="fas fa-edit"></i>
                <span>Edit Product</span>
            </a>
            <form action="{{ route('vendor.product.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-md transition-all font-medium shadow-sm">
                    <i class="fas fa-trash"></i>
                    <span>Delete</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-md border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div class="info-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Status</p>
                    <p class="text-lg font-bold text-gray-900 capitalize">{{ $product->status }}</p>
                    <div class="mt-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $product->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            <i class="fas fa-circle mr-1 text-[8px]"></i> {{ ucfirst($product->status) }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-md">
                    <i class="fas fa-{{ $product->status === 'active' ? 'check-circle' : 'pause-circle' }} text-xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="info-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Price Tiers</p>
                    <p class="text-lg font-bold text-gray-900">{{ $product->prices->count() }}</p>
                    <p class="text-xs text-gray-500 mt-2">pricing levels</p>
                </div>
                <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-md">
                    <i class="fas fa-layer-group text-xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="info-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Variations</p>
                    <p class="text-lg font-bold text-gray-900">{{ $product->variations->count() }}</p>
                    <p class="text-xs text-gray-500 mt-2">product options</p>
                </div>
                <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-md">
                    <i class="fas fa-sitemap text-xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="info-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Verification</p>
                    <p class="text-lg font-bold text-gray-900">{{ $product->is_admin_verified ? 'Verified' : 'Pending' }}</p>
                    <div class="mt-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $product->is_admin_verified ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' }}">
                            <i class="fas fa-{{ $product->is_admin_verified ? 'shield-alt' : 'clock' }} mr-1 text-[10px]"></i> {{ $product->is_admin_verified ? 'Approved' : 'Review' }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-md">
                    <i class="fas fa-shield-alt text-xl text-indigo-600"></i>
                </div>
            </div>
        </div>
        <div class="info-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
    <div class="flex items-center justify-between">
        <div class="flex-1">
            <p class="text-sm font-medium text-gray-600 mb-1">Pricing</p>
            <p class="text-lg font-bold text-gray-900">{{ $product->is_negotiable ? 'Negotiable' : 'Fixed' }}</p>
            <div class="mt-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $product->is_negotiable ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                    <i class="fas fa-{{ $product->is_negotiable ? 'handshake' : 'tag' }} mr-1 text-[8px]"></i> {{ $product->is_negotiable ? 'Negotiable' : 'Fixed' }}
                </span>
            </div>
        </div>
        <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-md">
            <i class="fas fa-{{ $product->is_negotiable ? 'handshake' : 'tag' }} text-xl text-yellow-600"></i>
        </div>
    </div>
</div>
    </div>

    <!-- Basic Information -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
            <i class="fas fa-info-circle text-blue-600"></i>
            Basic Information
        </h2>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Product Name</label>
                <p class="text-lg font-bold text-gray-900">{{ $product->name }}</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Slug</label>
                <p class="text-sm text-gray-700 font-mono bg-gray-50 px-3 py-2 rounded-md border border-gray-200">{{ $product->slug }}</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Category</label>
                @if($product->productCategory)
                    <span class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium bg-purple-100 text-purple-800 border border-purple-200">
                        <i class="fas fa-tag mr-2"></i> {{ $product->productCategory->name }}
                    </span>
                @else
                    <span class="text-sm text-gray-400">Uncategorized</span>
                @endif
            </div>

            @if($product->country)
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Country</label>
                <span class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium bg-blue-100 text-blue-800 border border-blue-200">
                    <i class="fas fa-globe mr-2"></i> {{ $product->country->name }}
                </span>
            </div>
            @endif

            @if($product->short_description)
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Short Description</label>
                <p class="text-sm text-gray-700">{{ $product->short_description }}</p>
            </div>
            @endif

            @if($product->description)
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Description</label>
                <div class="p-4 bg-gray-50 rounded-md border border-gray-200">
                    <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $product->description }}</p>
                </div>
            </div>
            @endif

            @if($product->overview)
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Overview</label>
                <div class="p-4 bg-gray-50 rounded-md border border-gray-200">
                    <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $product->overview }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Product Variations -->
    @if($product->variations && $product->variations->count() > 0)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
            <i class="fas fa-sitemap text-purple-600"></i>
            Product Variations
        </h2>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            @foreach($product->variations->groupBy('variation_type') as $type => $variations)
            <div class="p-5 bg-gray-50 rounded-xl border border-gray-200">
                <h4 class="text-sm font-bold text-gray-900 mb-3 capitalize flex items-center gap-2">
                    <i class="fas fa-tags text-purple-600"></i>
                    {{ str_replace('_', ' ', $type) }}
                </h4>
                <div class="flex flex-wrap gap-2">
                    @foreach($variations->where('is_active', true) as $variation)
                    <span class="px-3 py-2 bg-white border border-gray-300 rounded-md text-sm text-gray-700 font-medium hover:border-purple-300 transition-colors">
                        {{ $variation->variation_name }}
                        @if($variation->variation_value)
                            <span class="text-xs text-gray-500 ml-1">({{ $variation->variation_value }})</span>
                        @endif
                    </span>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Promo Codes -->
@if($product->promoCodes && $product->promoCodes->count() > 0)
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
    <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
        <i class="fas fa-ticket-alt text-red-600"></i>
        Applicable Promo Codes
    </h2>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
        @foreach($product->promoCodes as $promo)
        <div class="p-5 bg-gradient-to-br from-red-50 to-orange-50 rounded-xl border border-red-200">
            <div class="flex items-start justify-between mb-3">
                <span class="px-3 py-1.5 bg-red-600 text-white text-sm font-bold rounded-md">
                    {{ $promo->code }}
                </span>
                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $promo->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ ucfirst($promo->status) }}
                </span>
            </div>

            <p class="text-sm text-gray-700 mb-3">{{ $promo->description }}</p>

            <div class="space-y-2 text-xs text-gray-600">
                <div class="flex items-center gap-2">
                    <i class="fas fa-percentage text-red-600"></i>
                    <span class="font-medium">
                        {{ $promo->discount_type === 'percentage' ? $promo->discount_value . '%' : $promo->currency . ' ' . number_format($promo->discount_value, 2) }} off
                    </span>
                </div>

                @if($promo->min_purchase_amount)
                <div class="flex items-center gap-2">
                    <i class="fas fa-shopping-cart text-red-600"></i>
                    <span>Min: {{ $promo->currency }} {{ number_format($promo->min_purchase_amount, 2) }}</span>
                </div>
                @endif

                <div class="flex items-center gap-2">
                    <i class="fas fa-calendar text-red-600"></i>
                    <span>Valid until {{ $promo->end_date->format('M d, Y') }}</span>
                </div>

                @if($promo->usage_limit)
                <div class="flex items-center gap-2">
                    <i class="fas fa-users text-red-600"></i>
                    <span>{{ $promo->usage_count }}/{{ $promo->usage_limit }} used</span>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

    <!-- Specifications -->
    @php
        $specs = is_array($product->specifications) ? $product->specifications : (is_string($product->specifications) ? json_decode($product->specifications, true) : []);
    @endphp
    @if($specs && count($specs) > 0)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
            <i class="fas fa-list-ul text-indigo-600"></i>
            Specifications
        </h2>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach($specs as $key => $value)
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">{{ $key }}</p>
                <p class="text-sm font-bold text-gray-900">{{ $value }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Product Images -->
    @if($product->images && $product->images->count() > 0)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
            <i class="fas fa-images text-pink-600"></i>
            Product Images
        </h2>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-5">
            @foreach($product->images->sortBy('sort_order') as $image)
            <div class="relative rounded-xl overflow-hidden border border-gray-200 group">
                <img src="{{ $image->image_url }}" alt="{{ $image->alt_text ?? $product->name }}" class="w-full h-48 object-cover">
                @if($image->is_primary)
                    <span class="absolute top-2 left-2 px-2.5 py-1 bg-blue-600 text-white text-xs font-bold rounded-md shadow-lg">
                        <i class="fas fa-star mr-1"></i> Primary
                    </span>
                @endif
                @if($image->alt_text)
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-3">
                        <p class="text-xs text-white font-medium">{{ $image->alt_text }}</p>
                    </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Timeline Information -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
            <i class="fas fa-clock text-gray-600"></i>
            Timeline
        </h2>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-md">
                    <i class="fas fa-calendar-plus text-blue-600"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Created At</p>
                    <p class="text-sm font-bold text-gray-900">{{ $product->created_at->format('F d, Y') }}</p>
                    <p class="text-xs text-gray-500">{{ $product->created_at->format('h:i A') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-md">
                    <i class="fas fa-calendar-check text-purple-600"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Last Updated</p>
                    <p class="text-sm font-bold text-gray-900">{{ $product->updated_at->format('F d, Y') }}</p>
                    <p class="text-xs text-gray-500">{{ $product->updated_at->format('h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

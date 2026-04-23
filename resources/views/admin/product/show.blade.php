@extends('layouts.home')

@push('styles')
<style>
    .info-card { transition: transform 0.2s, box-shadow 0.2s; }
    .info-card:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .image-gallery img { transition: transform 0.3s; }
    .image-gallery img:hover { transform: scale(1.05); }
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
            <a href="{{ route('admin.vendor.product.index') }}" class="flex items-center justify-center w-10 h-10 text-gray-600 transition-colors bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-gray-900 shadow-sm">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Product Details</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $product->name }}</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            @if(auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.vendor.product.edit', $product) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#ff0808] text-white rounded-lg hover:bg-[#cc0606] transition-all font-medium shadow-sm">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
            @elseif(auth()->user()->isVendor() && $product->user_id === auth()->id())
                <a href="{{ route('admin.vendor.product.edit', $product) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Product Overview Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-600"></i>
                Product Information
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="flex items-start gap-3">
                    <div class="flex items-center justify-center w-10 h-10 bg-blue-50 rounded-lg">
                        <i class="fas fa-tag text-blue-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 mb-1">Status</p>
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium
                            {{ $product->status === 'active' ? 'bg-green-100 text-green-800' :
                               ($product->status === 'inactive' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($product->status) }}
                        </span>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="flex items-center justify-center w-10 h-10 bg-purple-50 rounded-lg">
                        <i class="fas fa-user text-purple-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 mb-1">Vendor</p>
                        @php
                            $businessProfile = $product->user->businessProfile ?? null;
                        @endphp
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $businessProfile->business_name ?? $product->user->name ?? 'N/A' }}
                        </p>
                        @if($product->user?->email)
                            <p class="text-xs text-gray-500">{{ $product->user->email }}</p>
                        @endif
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="flex items-center justify-center w-10 h-10 bg-yellow-50 rounded-lg">
                        <i class="fas fa-folder text-yellow-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 mb-1">Category</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $product->productCategory->name ?? 'Uncategorized' }}</p>
                        @if($product->country)
                            <p class="text-xs text-gray-500">{{ $product->country->name }}</p>
                        @endif
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="flex items-center justify-center w-10 h-10 bg-green-50 rounded-lg">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 mb-1">Verification</p>
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium {{ $product->is_admin_verified ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $product->is_admin_verified ? 'Verified' : 'Unverified' }}
                        </span>
                    </div>
                </div>
            </div>

            @if($product->short_description)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-xs font-medium text-gray-500 mb-2 flex items-center gap-2">
                        <i class="fas fa-align-left text-gray-400"></i>
                        Short Description
                    </p>
                    <p class="text-sm text-gray-700 bg-gray-50 rounded-lg p-4 border border-gray-100">{!! $product->short_description !!}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Base Price</p>
                    @php $firstPriceTier = $product->prices->first(); @endphp
                    @if($firstPriceTier)
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($firstPriceTier->price, 0) }} <span class="text-base">{{ $firstPriceTier->currency }}</span></p>
                        @if($product->prices->count() > 1)
                            <div class="mt-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-layer-group mr-1 text-[10px]"></i> {{ $product->prices->count() }} tiers
                                </span>
                            </div>
                        @endif
                    @else
                        <p class="text-2xl font-bold text-gray-900">N/A</p>
                    @endif
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-green-50 rounded-xl">
                    <i class="fas fa-dollar-sign text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Min Order Qty</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($product->min_order_quantity ?? 0) }}</p>
                    <div class="mt-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <i class="fas fa-box mr-1 text-[10px]"></i> Pieces
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-purple-50 rounded-xl">
                    <i class="fas fa-boxes text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Sold Count</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($product->sold_count ?? 0) }}</p>
                    @if($product->hot_selling_rank ?? false)
                        <div class="mt-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                <i class="fas fa-fire mr-1 text-[10px]"></i> #{{ $product->hot_selling_rank }}
                            </span>
                        </div>
                    @endif
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-orange-50 rounded-xl">
                    <i class="fas fa-shopping-cart text-2xl text-orange-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Rating</p>
                    @if(($product->rating ?? 0) > 0)
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($product->rating, 1) }} <i class="fas fa-star text-yellow-400 text-base"></i></p>
                        <div class="mt-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-comment mr-1 text-[10px]"></i> {{ $product->reviews_count ?? 0 }} reviews
                            </span>
                        </div>
                    @else
                        <p class="text-2xl font-bold text-gray-900">N/A</p>
                    @endif
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-yellow-50 rounded-xl">
                    <i class="fas fa-star text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Images -->
    @if($product->images->count() > 0)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-images text-blue-600"></i>
                    Product Images ({{ $product->images->count() }})
                </h2>

                {{-- Main image viewer --}}
                @php
                    $primaryImg  = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
                    $primaryRaw  = $primaryImg->image_url ?? '';
                    $primaryUrl  = str_starts_with($primaryRaw, 'http') ? $primaryRaw : asset($primaryRaw);
                @endphp
                <div class="mb-4 rounded-xl overflow-hidden bg-gray-100 border border-gray-200 flex items-center justify-center" style="height:300px;">
                    <img id="mainProductImage"
                         src="{{ $primaryUrl }}"
                         alt="{{ $product->name }}"
                         class="max-h-full max-w-full object-contain transition-opacity duration-200">
                </div>

                {{-- Thumbnails --}}
                <div class="grid grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3 image-gallery">
                    @foreach($product->images->sortBy('sort_order') as $image)
                        @php
                            $thumbRaw = $image->image_url ?? '';
                            $thumbUrl = str_starts_with($thumbRaw, 'http') ? $thumbRaw : asset($thumbRaw);
                        @endphp
                        <div class="relative group cursor-pointer"
                             onclick="setMainImage('{{ $thumbUrl }}', this)">
                            <div class="aspect-square rounded-lg overflow-hidden border-2 transition-all
                                        {{ $image->is_primary ? 'border-[#ff0808]' : 'border-transparent hover:border-gray-300' }}">
                                <img src="{{ $thumbUrl }}"
                                     alt="{{ $image->alt_text ?? $product->name }}"
                                     class="w-full h-full object-cover">
                            </div>
                            @if($image->is_primary)
                                <div class="absolute -top-1 -right-1 w-5 h-5 bg-[#ff0808] rounded-full flex items-center justify-center shadow">
                                    <i class="fas fa-star text-white" style="font-size:8px;"></i>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Video -->
    @if($product->video_url)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-video text-blue-600"></i>
                    Product Video
                </h2>
                <div class="rounded-xl overflow-hidden border border-gray-200 bg-black">
                    <video controls class="w-full max-h-80 object-contain bg-black" preload="metadata">
                        <source src="{{ str_starts_with($product->video_url, 'http') ? $product->video_url : \Illuminate\Support\Facades\Storage::url($product->video_url) }}" type="video/mp4">
                        <source src="{{ str_starts_with($product->video_url, 'http') ? $product->video_url : \Illuminate\Support\Facades\Storage::url($product->video_url) }}" type="video/webm">
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>
        </div>
    @endif

    <!-- Price Tiers -->
    @if($product->prices->count() > 0)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-layer-group text-green-600"></i>
                    Price Tiers ({{ $product->prices->count() }})
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($product->prices as $tier)
                        <div class="info-card p-5 bg-gray-50 rounded-xl border border-gray-200 shadow-sm">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="flex items-center justify-center w-10 h-10 bg-green-50 rounded-lg">
                                    <i class="fas fa-boxes text-green-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Quantity Range</p>
                                    <p class="text-sm font-bold text-gray-900">
                                        {{ number_format($tier->min_quantity) }}
                                        {{ $tier->max_quantity ? '- ' . number_format($tier->max_quantity) : '+' }}
                                    </p>
                                </div>
                            </div>
                            <div class="pt-3 border-t border-gray-200">
                                <p class="text-xs text-gray-500 mb-1">Price per piece</p>
                                <p class="text-2xl font-bold text-red-600">{{ number_format($tier->price, 2) }} <span class="text-base">{{ $tier->currency }}</span></p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Product Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @if($product->description)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-align-left text-purple-600"></i>
                        Description
                    </h2>
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 rich-content">
                        {!! $product->description !!}
                    </div>
                </div>
            </div>
        @endif

        @if($product->overview)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-list-ul text-blue-600"></i>
                        Overview
                    </h2>
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                        <p class="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $product->overview }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Specifications -->
    @if($product->specifications && count($product->specifications) > 0)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-indigo-600"></i>
                    Specifications
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($product->specifications as $key => $value)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-8 h-8 bg-white rounded-lg border border-gray-200">
                                    <i class="fas fa-tag text-indigo-600 text-sm"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
                            </div>
                            <span class="text-sm font-bold text-gray-900">{{ $value }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Variations -->
    @if($product->variations && $product->variations->count() > 0)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-palette text-pink-600"></i>
                    Product Variations
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($product->variations->groupBy('variation_type') as $type => $variations)
                        <div class="info-card p-5 bg-gray-50 rounded-xl border border-gray-200 shadow-sm">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="flex items-center justify-center w-8 h-8 bg-pink-50 rounded-lg">
                                    <i class="fas fa-layer-group text-pink-600 text-sm"></i>
                                </div>
                                <h4 class="text-sm font-semibold text-gray-900 capitalize">{{ str_replace('_', ' ', $type) }}</h4>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($variations as $variation)
                                    <span class="px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 font-medium hover:border-pink-300 hover:bg-pink-50 transition-colors">
                                        {{ $variation->variation_name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Additional Information -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-gray-600"></i>
                Additional Information
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-8 h-8 bg-white rounded-lg border border-gray-200">
                            <i class="fas fa-calendar-plus text-blue-600 text-sm"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Created At</span>
                    </div>
                    <span class="text-sm font-bold text-gray-900">{{ $product->created_at->format('M d, Y') }}</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-8 h-8 bg-white rounded-lg border border-gray-200">
                            <i class="fas fa-calendar-check text-green-600 text-sm"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Last Updated</span>
                    </div>
                    <span class="text-sm font-bold text-gray-900">{{ $product->updated_at->format('M d, Y') }}</span>
                </div>
                @if($product->is_negotiable)
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-100">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-8 h-8 bg-white rounded-lg border border-blue-200">
                                <i class="fas fa-handshake text-blue-600 text-sm"></i>
                            </div>
                            <span class="text-sm font-medium text-blue-700">Price Negotiable</span>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-blue-600 text-white">
                            <i class="fas fa-check mr-1"></i>Yes
                        </span>
                    </div>
                @endif
                @if($product->is_lower_priced ?? false)
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-8 h-8 bg-white rounded-lg border border-green-200">
                                <i class="fas fa-tag text-green-600 text-sm"></i>
                            </div>
                            <span class="text-sm font-medium text-green-700">Lower Priced</span>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-600 text-white">
                            <i class="fas fa-check mr-1"></i>Yes
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex flex-wrap items-center gap-3">
                @if(auth()->user()->hasRole('admin'))
                    <form action="{{ route('admin.vendor.product.toggle-verification', $product) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3 text-white rounded-lg transition-all font-semibold shadow-md
                                    {{ $product->is_admin_verified ? 'bg-gray-600 hover:bg-gray-700' : 'bg-blue-600 hover:bg-blue-700' }}">
                            <i class="fas fa-{{ $product->is_admin_verified ? 'times' : 'check' }}-circle"></i>
                            {{ $product->is_admin_verified ? 'Unverify Product' : 'Verify Product' }}
                        </button>
                    </form>
                    <a href="{{ route('admin.vendor.product.edit', $product) }}"
                       class="inline-flex items-center gap-2 px-6 py-3 bg-[#ff0808] text-white rounded-lg hover:bg-[#cc0606] transition-all font-semibold shadow-md">
                        <i class="fas fa-edit"></i> Edit Product
                    </a>
                @elseif(auth()->user()->isVendor() && $product->user_id === auth()->id())
                    <a href="{{ route('admin.vendor.product.edit', $product) }}"
                       class="inline-flex items-center gap-2 px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-all font-semibold shadow-md">
                        <i class="fas fa-edit"></i> Edit Product
                    </a>
                @endif

                <a href="{{ route('admin.vendor.product.index') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-semibold ml-auto">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function setMainImage(url, el) {
    const main = document.getElementById('mainProductImage');
    main.classList.add('opacity-0');
    setTimeout(() => {
        main.src = url;
        main.classList.remove('opacity-0');
    }, 150);
    document.querySelectorAll('.image-gallery .aspect-square').forEach(d => {
        d.classList.remove('border-[#ff0808]');
        d.classList.add('border-transparent', 'hover:border-gray-300');
    });
    el.querySelector('.aspect-square').classList.remove('border-transparent', 'hover:border-gray-300');
    el.querySelector('.aspect-square').classList.add('border-[#ff0808]');
}

setTimeout(() => {
    document.querySelectorAll('.bg-green-50, .bg-red-50').forEach(el => {
        el.style.transition = 'opacity 0.5s';
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 500);
    });
}, 5000);
</script>

<style>
.rich-content h1 { font-size: 1.25rem; font-weight: 700; margin: 0.75rem 0 0.5rem; }
.rich-content h2 { font-size: 1.1rem;  font-weight: 700; margin: 0.75rem 0 0.5rem; }
.rich-content h3 { font-size: 1rem;    font-weight: 600; margin: 0.5rem 0 0.35rem; }
.rich-content p  { margin-bottom: 0.5rem; font-size: 0.875rem; }
.rich-content ul { list-style: disc;    padding-left: 1.25rem; margin-bottom: 0.5rem; }
.rich-content ol { list-style: decimal; padding-left: 1.25rem; margin-bottom: 0.5rem; }
.rich-content li { margin-bottom: 0.25rem; font-size: 0.875rem; }
.rich-content strong { font-weight: 600; }
.rich-content em { font-style: italic; }
.rich-content a  { color: #ff0808; text-decoration: underline; }
.rich-content blockquote {
    border-left: 3px solid #d1d5db;
    padding-left: 1rem;
    color: #6b7280;
    margin: 0.5rem 0;
    font-style: italic;
}
#mainProductImage { transition: opacity 0.15s ease; }
</style>
@endsection

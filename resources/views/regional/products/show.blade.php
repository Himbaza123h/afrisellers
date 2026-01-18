@extends('layouts.home')

@push('styles')
<style>
    .image-gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 12px; }
    .gallery-image { transition: transform 0.3s; cursor: pointer; }
    .gallery-image:hover { transform: scale(1.05); }
    @media print {
        .no-print { display: none !important; }
        .print-only { display: block !important; }
    }
</style>
@endpush

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between no-print">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('regional.products.index') }}" class="text-gray-600 hover:text-gray-900 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Product Details</h1>
            </div>
            <div class="flex items-center gap-2">
                <p class="text-sm text-gray-500">{{ $product->name }}</p>
                <span class="px-2 py-0.5 rounded-md text-xs font-medium bg-blue-100 text-blue-800">
                    {{ $product->country->name ?? 'N/A' }}
                </span>
            </div>
        </div>
        <div class="flex flex-wrap gap-3">
            @if(!$product->is_admin_verified)
                <form action="{{ route('regional.products.approve', $product->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all font-medium shadow-sm">
                        <i class="fas fa-check"></i>
                        <span>Approve Product</span>
                    </button>
                </form>
                <form action="{{ route('regional.products.reject', $product->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all font-medium shadow-sm">
                        <i class="fas fa-times"></i>
                        <span>Reject Product</span>
                    </button>
                </form>
            @endif
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
        </div>
    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <!-- Product Info Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Product ID</p>
                <p class="text-lg font-bold text-gray-900">#{{ $product->id }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Country</p>
                <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ $product->country->name ?? 'N/A' }}
                </span>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Created Date</p>
                <p class="text-sm font-semibold text-gray-900">{{ $product->created_at->format('M d, Y') }}</p>
                <p class="text-xs text-gray-500">{{ $product->created_at->format('h:i A') }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Verification Status</p>
                @if($product->is_admin_verified)
                    <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-1"></i> Verified
                    </span>
                @else
                    <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <i class="fas fa-times-circle mr-1"></i> Unverified
                    </span>
                @endif
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Product Status</p>
                @php
                    $statusColors = [
                        'active' => ['Active', 'bg-green-100 text-green-800'],
                        'pending' => ['Pending', 'bg-yellow-100 text-yellow-800'],
                        'inactive' => ['Inactive', 'bg-gray-100 text-gray-800'],
                    ];
                    $status = $statusColors[$product->status] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
                @endphp
                <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-medium {{ $status[1] }}">
                    {{ $status[0] }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Product Images -->
            @if($product->images->count() > 0)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Product Images</h3>
                    <div class="image-gallery">
                        @foreach($product->images as $image)
                            <img src="{{ asset('storage/' . $image->image_path) }}"
                                 alt="{{ $product->name }}"
                                 class="gallery-image w-full h-32 object-cover rounded-lg border border-gray-200"
                                 onclick="window.open(this.src, '_blank')">
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Product Information -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Product Information</h3>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Product Name</label>
                        <p class="mt-1 text-gray-900 font-semibold text-lg">{{ $product->name }}</p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Category</label>
                            <p class="mt-1">
                                <span class="px-3 py-1 rounded-md text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ $product->productCategory->name ?? 'Uncategorized' }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Country</label>
                            <p class="mt-1">
                                <span class="px-3 py-1 rounded-md text-sm font-medium bg-indigo-100 text-indigo-800">
                                    <i class="fas fa-map-marker-alt mr-1"></i> {{ $product->country->name ?? 'N/A' }}
                                </span>
                            </p>
                        </div>
                    </div>

                    @if($product->short_description)
                        <div>
                            <label class="text-sm font-medium text-gray-700">Short Description</label>
                            <p class="mt-1 text-gray-700">{{ $product->short_description }}</p>
                        </div>
                    @endif

                    @if($product->description)
                        <div>
                            <label class="text-sm font-medium text-gray-700">Full Description</label>
                            <div class="mt-1 text-gray-700 prose max-w-none">
                                {!! nl2br(e($product->description)) !!}
                            </div>
                        </div>
                    @endif

                    @if($product->overview)
                        <div>
                            <label class="text-sm font-medium text-gray-700">Overview</label>
                            <div class="mt-1 text-gray-700 prose max-w-none">
                                {!! nl2br(e($product->overview)) !!}
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Min Order Quantity</label>
                            <p class="mt-1 text-gray-900 font-semibold">{{ $product->min_order_quantity ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Negotiable</label>
                            <p class="mt-1">
                                @if($product->is_negotiable)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i> Yes
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-times mr-1"></i> No
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Specifications -->
            @if($product->specifications && count($product->specifications) > 0)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Specifications</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($product->specifications as $key => $value)
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs font-medium text-gray-600 mb-1">{{ ucfirst(str_replace('_', ' ', $key)) }}</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $value }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Pricing -->
            @if($product->prices->count() > 0)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Pricing Information</h3>
                    <div class="space-y-3">
                        @foreach($product->prices as $price)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-700">
                                        {{ $price->min_quantity }} - {{ $price->max_quantity ?? '∞' }} units
                                    </p>
                                    @if($price->description)
                                        <p class="text-xs text-gray-500 mt-1">{{ $price->description }}</p>
                                    @endif
                                </div>
                                <p class="text-lg font-bold text-gray-900">${{ number_format($price->price, 2) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Variations -->
            @if($product->variations->count() > 0)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Product Variations</h3>
                    <div class="space-y-3">
                        @foreach($product->variations as $variation)
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="font-semibold text-gray-900">{{ $variation->name }}</p>
                                    @if($variation->price)
                                        <p class="text-lg font-bold text-gray-900">${{ number_format($variation->price, 2) }}</p>
                                    @endif
                                </div>
                                @if($variation->sku)
                                    <p class="text-xs text-gray-500">SKU: {{ $variation->sku }}</p>
                                @endif
                                @if($variation->stock_quantity !== null)
                                    <p class="text-xs text-gray-600 mt-1">
                                        Stock: <span class="font-medium">{{ $variation->stock_quantity }}</span>
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Reviews -->
            @if($product->reviews->count() > 0)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer Reviews ({{ $product->reviews->count() }})</h3>
                    <div class="space-y-4">
                        @foreach($product->reviews->take(5) as $review)
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-semibold text-blue-700">{{ substr($review->user->name ?? 'U', 0, 1) }}</span>
                                        </div>
                                        <span class="font-medium text-gray-900">{{ $review->user->name ?? 'Anonymous' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star text-xs {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                @if($review->comment)
                                    <p class="text-sm text-gray-700">{{ $review->comment }}</p>
                                @endif
                                <p class="text-xs text-gray-500 mt-2">{{ $review->created_at->diffForHumans() }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Statistics -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h3>
                <div class="space-y-4">
                    <div class="p-3 bg-purple-50 rounded-lg">
                        <p class="text-xs font-medium text-purple-600 mb-1">Total Views</p>
                        <p class="text-2xl font-bold text-purple-900">{{ number_format($product->views) }}</p>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <p class="text-xs font-medium text-blue-600 mb-1">Total Orders</p>
                        <p class="text-2xl font-bold text-blue-900">{{ number_format($orderStats['total_orders']) }}</p>
                    </div>
                    <div class="p-3 bg-green-50 rounded-lg">
                        <p class="text-xs font-medium text-green-600 mb-1">Quantity Sold</p>
                        <p class="text-2xl font-bold text-green-900">{{ number_format($orderStats['total_quantity_sold']) }}</p>
                    </div>
                    <div class="p-3 bg-emerald-50 rounded-lg">
                        <p class="text-xs font-medium text-emerald-600 mb-1">Total Revenue</p>
                        <p class="text-2xl font-bold text-emerald-900">${{ number_format($orderStats['total_revenue'], 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Location Information -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Location</h3>
                <div class="space-y-3">
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <p class="text-xs font-medium text-blue-600 mb-1">Country</p>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-map-marker-alt text-blue-700"></i>
                            <p class="text-sm font-semibold text-blue-900">{{ $product->country->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    @if($product->country && $product->country->region)
                        <div class="p-3 bg-indigo-50 rounded-lg">
                            <p class="text-xs font-medium text-indigo-600 mb-1">Region</p>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-globe text-indigo-700"></i>
                                <p class="text-sm font-semibold text-indigo-900">{{ $product->country->region->name }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Vendor Info -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Vendor Information</h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-100 to-purple-200 rounded-full">
                            <span class="text-lg font-bold text-purple-700">{{ substr($product->user->name ?? 'V', 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $product->user->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">Vendor</p>
                        </div>
                    </div>

                    <div class="pt-3 border-t space-y-2">
                        <div class="flex items-center gap-2 text-sm">
                            <i class="fas fa-envelope text-gray-400 w-5"></i>
                            <span class="text-gray-900">{{ $product->user->email ?? 'N/A' }}</span>
                        </div>
                        @if($product->user->phone ?? false)
                            <div class="flex items-center gap-2 text-sm">
                                <i class="fas fa-phone text-gray-400 w-5"></i>
                                <span class="text-gray-900">{{ $product->user->phone }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Showrooms -->
            @if($product->showrooms->count() > 0)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">In Showrooms</h3>
                    <div class="space-y-2">
                        @foreach($product->showrooms as $showroom)
                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <p class="text-sm font-semibold text-gray-900">{{ $showroom->name }}</p>
                                @if($showroom->pivot->added_at)
                                    <p class="text-xs text-gray-500 mt-1">
                                        Added: {{ \Carbon\Carbon::parse($showroom->pivot->added_at)->format('M d, Y') }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 no-print">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    @if(!$product->is_admin_verified)
                        <form action="{{ route('regional.products.approve', $product->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium text-sm">
                                <i class="fas fa-check mr-2"></i> Approve Product
                            </button>
                        </form>
                        <form action="{{ route('regional.products.reject', $product->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium text-sm">
                                <i class="fas fa-times mr-2"></i> Reject Product
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('regional.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-medium text-sm">
                            <i class="fas fa-trash mr-2"></i> Delete Product
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

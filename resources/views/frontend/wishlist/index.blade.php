@extends('layouts.app')

@section('title', 'My Wishlist')

@section('content')
<div class="py-6 md:py-10 min-h-screen bg-gray-50">
    <div class="container px-4 mx-auto">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-gray-900">My Wishlist</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $wishlists->total() }} saved {{ Str::plural('product', $wishlists->total()) }}</p>
            </div>
            <a href="{{ route('home') }}" class="text-sm text-[#ff0808] hover:underline font-medium">
                <i class="fas fa-arrow-left mr-1"></i> Continue Shopping
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-300 rounded-lg text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if($wishlists->isNotEmpty())
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 md:gap-4">
                @foreach($wishlists as $item)
                    @php
                        $product = $item->product;
                        if (!$product) continue;
                        $image = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
                        $firstTier = $product->prices->first();
                        $currencySymbols = ['USD'=>'$','EUR'=>'€','GBP'=>'£','RWF'=>'RF','KES'=>'KSh'];
                        $symbol = $firstTier ? ($currencySymbols[$firstTier->currency ?? 'USD'] ?? '$') : '$';
                        $price  = $firstTier ? ($firstTier->price - ($firstTier->discount ?? 0)) : null;
                    @endphp
                    <div class="group bg-white rounded-xl border border-gray-200 hover:shadow-md transition-all overflow-hidden relative">

                        {{-- Remove button --}}
                        <form action="{{ route('wishlist.remove', $item->id) }}" method="POST" class="absolute top-2 left-2 z-10">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-7 h-7 bg-white rounded-full shadow flex items-center justify-center hover:bg-red-50 transition-colors" title="Remove">
                                <i class="fas fa-times text-xs text-gray-400 hover:text-red-500"></i>
                            </button>
                        </form>

                        {{-- Heart badge --}}
                        <div class="absolute top-2 right-2 z-10 w-7 h-7 bg-red-50 rounded-full flex items-center justify-center">
                            <i class="fas fa-heart text-xs text-red-500"></i>
                        </div>

                        <a href="{{ route('products.show', $product->slug) }}" class="block">
                            <div class="overflow-hidden bg-gray-100" style="height:180px">
                                @if($image)
                                    <img src="{{ str_starts_with($image->image_url, 'http') ? $image->image_url : Storage::url($image->image_url) }}"
                                         alt="{{ $product->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                        <i class="fas fa-box text-4xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="p-3">
                                <h3 class="text-xs font-semibold text-gray-800 line-clamp-2 mb-1 group-hover:text-[#ff0808] transition-colors">
                                    {{ $product->name }}
                                </h3>
                                <p class="text-[10px] text-gray-400 mb-2">{{ $product->productCategory->name ?? '—' }}</p>
                                @if($price)
                                    <p class="text-sm font-bold text-gray-900">{{ $symbol }}{{ number_format($price, 2) }}</p>
                                @else
                                    <p class="text-xs text-gray-400">Price not set</p>
                                @endif
                                <p class="text-[10px] text-gray-400 mt-1">
                                    Min. {{ $product->min_order_quantity ?? 1 }} pieces
                                </p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $wishlists->links() }}
            </div>
        @else
            <div class="py-20 text-center">
                <i class="far fa-heart text-6xl text-gray-200 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-500 mb-2">Your wishlist is empty</h3>
                <p class="text-sm text-gray-400 mb-6">Save products you love by clicking the heart icon</p>
                <a href="{{ route('home') }}"
                   class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#ff0808] text-white text-sm font-semibold rounded-lg hover:bg-[#cc0606] transition-colors">
                    <i class="fas fa-shopping-bag"></i> Browse Products
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

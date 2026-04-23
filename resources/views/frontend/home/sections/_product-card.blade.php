{{--
    Partial: frontend/home/sections/_product-card.blade.php
    Variables: $product, $image, $businessProfile, $mainPrice, $maxPrice, $currency, $badge, $showExporter
--}}
<div class="bg-white rounded-lg border border-transparent shadow-sm overflow-hidden hover:shadow-lg hover:border-[#faafaf] transition-all">
    <a href="{{ route('products.show', $product->slug) }}" class="block relative h-24 md:h-32 overflow-hidden group">
        @if($image)
            <img src="{{ $image->image_url }}"
                 alt="{{ $product->name }}"
                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                 loading="lazy">
        @else
            <div class="flex justify-center items-center w-full h-full bg-gray-100">
                <span class="text-xl md:text-2xl">📦</span>
            </div>
        @endif

        <span class="absolute top-1 right-1 md:top-2 md:right-2 bg-[#ff0808] text-white text-[8px] md:text-[10px] font-bold px-1 md:px-1.5 py-0.5 rounded-full">
            {{ $badge }}
        </span>

        @if(($showExporter ?? false) && $businessProfile && $businessProfile->verification_status === 'verified')
        <span class="absolute top-1 left-1 md:top-2 md:left-2 bg-green-600 text-white text-[8px] md:text-[10px] font-bold px-1 md:px-1.5 py-0.5 rounded">
            ✓ {{ __('messages.exporters') }}
        </span>
        @elseif(!($showExporter ?? false) && $product->is_admin_verified)
        <span class="absolute top-1 left-1 md:top-2 md:left-2 bg-green-600 text-white text-[8px] md:text-[10px] font-bold px-1 md:px-1.5 py-0.5 rounded">
            ✓ {{ __('messages.verified') }}
        </span>
        @endif
    </a>

    <div class="p-2 md:p-3">
        <a href="{{ route('products.show', $product->slug) }}">
            <h4 class="text-[10px] md:text-xs font-bold text-gray-900 mb-1 md:mb-2 hover:text-[#ff0808] transition-colors line-clamp-2 min-h-[2rem]">
                {{ $product->name }}
            </h4>
        </a>

        @if($product->productCategory)
        <div class="mb-1 md:mb-2">
            <span class="inline-flex items-center gap-0.5 text-[8px] md:text-[10px] font-medium text-purple-600 bg-purple-50 px-1 md:px-1.5 py-0.5 rounded">
                <svg class="w-2 h-2 md:w-2.5 md:h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <span class="truncate">{{ $product->productCategory->name }}</span>
            </span>
        </div>
        @endif

        <div class="text-[#ff0808] font-bold text-[10px] md:text-xs mb-1 md:mb-2">
            {{ $currency }} {{ number_format($mainPrice, 2) }}
            @if($maxPrice && $maxPrice != $mainPrice)
                - {{ number_format($maxPrice, 2) }}
            @endif
        </div>

        <div class="text-[8px] md:text-[10px] text-gray-500 mb-1 md:mb-2">
            {{ __('messages.moq') }}: {{ number_format($product->min_order_quantity) }} pcs
        </div>

        <div class="flex items-center gap-1 text-[8px] md:text-[10px] text-gray-500 mb-1 md:mb-2">
            <svg class="w-2 h-2 md:w-2.5 md:h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            </svg>
            <span class="truncate">{{ $product->country->name ?? '' }}</span>
        </div>

        @if($businessProfile)
        <div class="mb-1 md:mb-2">
            <span class="inline-flex items-center gap-0.5 text-[8px] md:text-[10px] font-medium text-blue-600 bg-blue-50 px-1 md:px-1.5 py-0.5 rounded">
                <svg class="w-2 h-2 md:w-2.5 md:h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span class="truncate">{{ $businessProfile->business_name }}</span>
            </span>
        </div>
        @endif

        @if($product->is_admin_verified)
        <div class="flex items-center gap-0.5 md:gap-1 text-green-600">
            <svg class="w-2 h-2 md:w-2.5 md:h-2.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="font-medium text-[8px] md:text-[10px]">{{ __('messages.verified') }}</span>
        </div>
        @endif
    </div>
</div>

{{--
    Partial: frontend/home/sections/_supplier-card.blade.php
    Variables: $supplier, $image
--}}
<div class="bg-white rounded-lg border border-transparent shadow-sm overflow-hidden hover:shadow-lg hover:border-[#faafaf] transition-all">
    <div class="block relative h-24 md:h-32 overflow-hidden group">
        @if($image)
            <img src="{{ $image->image_url }}"
                 alt="{{ $supplier->business_name }}"
                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                 loading="lazy">
        @else
            <div class="flex justify-center items-center w-full h-full bg-blue-50">
                <span class="text-2xl md:text-4xl">🏢</span>
            </div>
        @endif

        <span class="absolute top-1 right-1 md:top-2 md:right-2 bg-[#ff0808] text-white text-[8px] md:text-[10px] font-bold px-1 md:px-1.5 py-0.5 rounded-full">
            ⭐ {{ __('messages.top_exporter') }}
        </span>
        <span class="absolute top-1 left-1 md:top-2 md:left-2 bg-green-600 text-white text-[8px] md:text-[10px] font-bold px-1 md:px-1.5 py-0.5 rounded">
            ✓ {{ __('messages.verified') }}
        </span>
    </div>

    <div class="p-2 md:p-3">
        <h4 class="text-[10px] md:text-xs font-bold text-gray-900 mb-1 md:mb-2 line-clamp-2 min-h-[2rem]">
            {{ $supplier->business_name }}
        </h4>

        <div class="flex items-center gap-1 text-[8px] md:text-[10px] text-gray-500 mb-1 md:mb-2">
            <svg class="w-2 h-2 md:w-2.5 md:h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            </svg>
            <span class="truncate">{{ $supplier->city }}, {{ $supplier->country->name ?? '' }}</span>
        </div>

        <div class="mb-1 md:mb-2">
            <span class="inline-flex items-center gap-0.5 text-[8px] md:text-[10px] font-medium text-purple-600 bg-purple-50 px-1 md:px-1.5 py-0.5 rounded">
                <svg class="w-2 h-2 md:w-2.5 md:h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span>{{ number_format($supplier->products_count) }} {{ __('messages.products') }}</span>
            </span>
        </div>

        @if($supplier->business_type)
        <div class="mb-1 md:mb-2">
            <span class="inline-flex items-center gap-0.5 text-[8px] md:text-[10px] font-medium text-blue-600 bg-blue-50 px-1 md:px-1.5 py-0.5 rounded">
                <svg class="w-2 h-2 md:w-2.5 md:h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span class="truncate">{{ $supplier->business_type }}</span>
            </span>
        </div>
        @endif

        <div class="flex items-center gap-0.5 md:gap-1 text-[8px] md:text-[10px] font-medium text-[#ff0808]">
            <svg class="w-2 h-2 md:w-2.5 md:h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            <span>{{ __('messages.view_profile') }}</span>
        </div>
    </div>
</div>

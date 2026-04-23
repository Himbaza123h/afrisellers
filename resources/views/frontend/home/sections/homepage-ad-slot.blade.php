@php
    $slotAds = \App\Models\AddonUser::whereNotNull('paid_at')
        ->where(function($q) {
            $q->whereNull('ended_at')->orWhere('ended_at', '>', now());
        })
        ->whereHas('addon', function($q) use ($adPosition) {
            $q->where('locationX', 'Homepage')
              ->where('locationY', $adPosition);
        })
        ->with(['addon', 'product.images', 'product.productCategory', 'product.prices', 'user.vendor.businessProfile'])
        ->get();

    if ($slotAds->isEmpty()) return;

    $slotId  = 'adslot-' . Str::slug($adPosition);
    $adCount = $slotAds->count();

    // Pair ads into groups of 2 for two-column layout
    $adPairs = $slotAds->chunk(2);
@endphp

<div class="py-2 w-full md:py-3" id="{{ $slotId }}-wrap">
    <div class="container px-4 mx-auto">

        {{-- Sponsored label --}}
        <div class="flex gap-2 items-center mb-2">
            <span class="inline-flex items-center gap-1 text-[9px] font-semibold text-gray-400 uppercase tracking-widest">
                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                Sponsored
            </span>
            <div class="flex-1 h-px bg-gray-200"></div>
        </div>

        {{-- Banner container --}}
        <div class="relative overflow-hidden shadow-md bg-[#0a0a0a]"

             id="{{ $slotId }}"
             style="height:112px; border-radius:3px;"
             onmouseenter="stopAdSlide('{{ $slotId }}')"
             onmouseleave="startAdSlide('{{ $slotId }}')">

            {{-- All pairs stacked, fade in/out --}}
            @foreach($adPairs as $pairIndex => $pair)
            <div class="ad-pair-{{ $slotId }} absolute inset-0 flex transition-opacity duration-1000 ease-in-out"
                 style="gap:2px; opacity:{{ $pairIndex === 0 ? '1' : '0' }}; z-index:{{ $pairIndex === 0 ? '2' : '1' }};">

                @foreach($pair as $adIndex => $ad)
                @php
                    $isProduct = !empty($ad->product_id) && $ad->product;

                    $themes = [
                        ['#0f172a', '#ff0808'],
                        ['#7c2d12', '#fbbf24'],
                        ['#064e3b', '#34d399'],
                        ['#1e1b4b', '#818cf8'],
                        ['#831843', '#f9a8d4'],
                        ['#134e4a', '#2dd4bf'],
                        ['#1e3a5f', '#60a5fa'],
                        ['#451a03', '#fcd34d'],
                    ];
                    $themeIdx  = ($pairIndex * 2 + $adIndex) % count($themes);
                    [$bgColor, $accentColor] = $themes[$themeIdx];

                    if ($isProduct) {
                        $adUrl   = route('products.show', $ad->product->slug);
                        $adImage = ($ad->product->images->where('is_primary', true)->first() ?? $ad->product->images->first())?->image_url;
                        $adTitle = $ad->product->name;
                        $adSub   = $ad->product->productCategory?->name ?? '';
                        $adBadge = 'FEATURED PRODUCT';
                        $minPrice = $ad->product->prices?->min('price');
                        $currency = $ad->product->prices?->first()?->currency ?? 'RWF';
                        if ($minPrice) $adSub = 'From ' . number_format($minPrice, 0) . ' ' . $currency . ($adSub ? ' · ' . $adSub : '');
                    } else {
                        $bp      = $ad->user?->vendor?->businessProfile;
                        $adUrl   = $bp ? route('business-profile.products', $bp->id) : '#';
                        $adImage = $bp?->logo_url ?? null;
                        $adTitle = $bp?->business_name ?? $ad->user?->name ?? 'Supplier';
                        $adSub   = $bp?->business_type ?? 'Verified Supplier';
                        $adBadge = 'VERIFIED SUPPLIER';
                    }
                @endphp

                <a href="{{ $adUrl }}"
                   class="overflow-hidden relative flex-1 group ad-cell"
                   style="background:{{ $bgColor }}; text-decoration:none !important; display:block;">

                    {{-- Background image --}}
                    @if($adImage)
                        <img src="{{ $adImage }}"
                             alt="{{ $adTitle }}"
                             class="object-cover absolute inset-0 w-full h-full transition-transform duration-700 group-hover:scale-105"
                             loading="lazy">
                        <div class="absolute inset-0"
                             style="background:linear-gradient(to right, {{ $bgColor }} 0%, {{ $bgColor }}cc 45%, rgba(0,0,0,0.05) 100%);"></div>
                    @else
                        {{-- Text ad style: dot pattern --}}
                        <div class="absolute inset-0 opacity-10"
                             style="background-image:radial-gradient(circle,#fff 1px,transparent 1px);
                                    background-size:18px 18px;"></div>
                    @endif

                    {{-- Shimmer --}}
                    <div class="absolute inset-0 pointer-events-none ad-slot-shimmer"></div>

                    {{-- Content --}}
                    <div class="flex relative z-10 items-center px-5 h-full md:px-7">
                        <div class="min-w-0">

                            <span class="inline-block mb-1 px-2 py-0.5 text-[8px] font-black tracking-widest uppercase rounded-sm leading-none"
                                  style="background:{{ $accentColor }};
                                         color:{{ in_array($accentColor, ['#f9a8d4','#fbbf24','#fcd34d','#34d399','#2dd4bf']) ? '#0a0a0a' : '#fff' }};">
                                {{ $adBadge }}
                            </span>

                            <h3 class="font-black leading-tight text-white drop-shadow-lg"
                                style="font-size:clamp(12px,1.4vw,16px);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $adTitle }}
                            </h3>

                            @if($adSub)
                            <p class="mt-0.5 truncate drop-shadow text-white/70"
                               style="font-size:clamp(9px,0.9vw,11px);">
                                {{ $adSub }}
                            </p>
                            @endif

                        </div>
                    </div>

                    {{-- Bottom accent line --}}
                    <div class="absolute right-0 bottom-0 left-0 h-0.5 opacity-0 transition-opacity duration-500 group-hover:opacity-60"
                         style="background:linear-gradient(to right, transparent, {{ $accentColor }}, transparent);"></div>

                </a>
                @endforeach

                {{-- Fill slot if odd --}}
                @if($pair->count() === 1)
                <a href="#" class="relative flex-1 overflow-hidden group bg-[#111]" style="display:block;">
                    <div class="absolute inset-0 opacity-5"
                         style="background-image:repeating-linear-gradient(45deg,#fff 0,#fff 1px,transparent 0,transparent 50%);
                                background-size:10px 10px;"></div>
                    <div class="flex relative z-10 justify-center items-center h-full">
                        <p class="text-white/30 text-[10px] font-black uppercase tracking-widest">Sponsored Slot</p>
                    </div>
                </a>
                @endif

            </div>
            @endforeach

            {{-- AD label --}}
            <span class="absolute top-1 right-2 z-30 text-white/20 text-[8px] font-bold tracking-widest pointer-events-none select-none">AD</span>

        </div>
    </div>
</div>

@once
@push('scripts')
<script>
(function(){
    const STATE = {}, TIMERS = {}, TOTALS = {};
    const INTERVAL = 5500;

    function initSlot(id){
        const pairs = document.querySelectorAll('.ad-pair-' + id);
        TOTALS[id] = pairs.length;
        STATE[id]  = 0;
        if(TOTALS[id] > 1) window.startAdSlide(id);
    }

    function moveFade(id){
        document.querySelectorAll('.ad-pair-' + id).forEach((el, i) => {
            el.style.opacity = i === STATE[id] ? '1' : '0';
            el.style.zIndex  = i === STATE[id] ? '2' : '1';
        });
    }

    window.startAdSlide = function(id){
        if(TIMERS[id]) clearInterval(TIMERS[id]);
        if((TOTALS[id] || 0) <= 1) return;
        TIMERS[id] = setInterval(() => {
            STATE[id] = ((STATE[id] || 0) + 1) % (TOTALS[id] || 1);
            moveFade(id);
        }, INTERVAL);
    };

    window.stopAdSlide = function(id){
        if(TIMERS[id]) clearInterval(TIMERS[id]);
    };

    document.addEventListener('DOMContentLoaded', function(){
        document.querySelectorAll('[id^="adslot-"]').forEach(el => {
            if(!el.id.endsWith('-track') && !el.id.endsWith('-dots') && !el.id.endsWith('-wrap')){
                initSlot(el.id);
            }
        });
    });
})();
</script>
@endpush
@endonce

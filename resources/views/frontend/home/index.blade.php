@extends('layouts.app')

@section('title', 'Home - Africa\'s Leading B2B Marketplace')

@section('content')

@php
    // Load ALL sections in sort_order
    $allSections = \App\Models\UISection::ordered()->get()->keyBy('section_key');

    $section = fn(string $key) => $allSections->get($key) ?? new \App\Models\UISection([
        'section_key'  => $key,
        'is_active'    => false,
        'number_items' => 4,
        'is_slide'     => false,
        'is_fade'      => false,
        'is_flip'      => false,
        'allow_manual' => false,
        'manual_items' => [],
    ]);

    // Ordered keys from DB (respects admin drag-drop order)
    $orderedKeys = $allSections->keys()->toArray();

    // Append any section keys not yet in DB
    $knownKeys = ['hero_section','browse_by_regions','weekly_special_offers','hot_deals','most_popular_suppliers','trending_products'];
    foreach ($knownKeys as $k) {
        if (!in_array($k, $orderedKeys)) $orderedKeys[] = $k;
    }

    // Track if weekly+hotdeals combined partial already rendered
    $combinedRendered = false;
@endphp

    @foreach($orderedKeys as $key)
        @php $uiSection = $section($key); @endphp

        @if(!$uiSection->is_active)
            @continue
        @endif

        @switch($key)

            @case('hero_section')
                @if(request()->routeIs('home'))
                    @include('frontend.home.sections.home-hero', ['uiSection' => $uiSection])
                @else
                    @include('frontend.home.sections.hero', ['uiSection' => $uiSection])
                @endif
                @include('frontend.home.sections.homepage-ad-slot', ['adPosition' => 'Heroads'])
                @break

            @case('browse_by_regions')
                @include('frontend.home.sections.browse-by-region', ['uiSection' => $uiSection])
                @break

            @case('weekly_special_offers')
                @php $combinedRendered = true; @endphp
                @include('frontend.home.sections.recommended-suppliers-hot-deals', [
                    'weeklyOffersSection' => $uiSection,
                    'hotDealsSection'     => $section('hot_deals'),
                ])
                @include('frontend.home.sections.homepage-ad-slot', ['adPosition' => 'Weeklyads'])
                @break

            @case('hot_deals')
                {{-- Only render standalone if weekly_special_offers didn't already render it --}}
                @if(!$combinedRendered)
                    @include('frontend.home.sections.recommended-suppliers-hot-deals', [
                        'weeklyOffersSection' => $section('weekly_special_offers'),
                        'hotDealsSection'     => $uiSection,
                    ])
                @endif
                @include('frontend.home.sections.homepage-ad-slot', ['adPosition' => 'Weeklyads'])
                @break

            @case('most_popular_suppliers')
                @include('frontend.home.sections.featured-suppliers', ['uiSection' => $uiSection])
                @include('frontend.home.sections.selectbycategopry')
                @include('frontend.home.sections.homepage-ad-slot', ['adPosition' => 'Popularads'])
                @break

            @case('trending_products')
                @include('frontend.home.sections.trending-products', ['uiSection' => $uiSection])
                @include('frontend.home.sections.homepage-ad-slot', ['adPosition' => 'Specialads'])
                @break

        @endswitch
    @endforeach



    {{-- Static sections — always render, always at the bottom --}}
    @include('frontend.home.sections.categories')
    @include('frontend.home.sections.most-articles')
    @include('frontend.home.sections.regional-showcases')
    @include('frontend.home.sections.partners')
    @include('frontend.home.sections.join-afrisellers')
    @include('frontend.home.sections.why-choose')

{{-- ── Fixed Right-Side Vertical Ad Strip ── --}}
@php
// Load real running ads for homepage_right position
$realRightAds = \App\Models\Advertisement::where('position', 'homepage_right')
    ->where('status', 'running')
    ->where('end_date', '>=', now())
    ->orderBy('approved_at', 'desc')
    ->get();

$rightSideAds = $realRightAds->count() > 0
    ? $realRightAds->map(fn($ad) => [
        'real_id' => $ad->id,
        'type'    => $ad->type,
        'media'   => $ad->media_url,
        'bg'      => $ad->bg_gradient ?? 'linear-gradient(180deg,#ff0808 0%,#c80000 100%)',
        'headline'=> $ad->headline ?? $ad->title,
        'sub'     => $ad->sub_text ?? '',
        'url'     => $ad->destination_url ?? '#',
        'badge'   => $ad->badge_text ?? 'SPONSORED',
        'overlay' => $ad->overlay_color ?? 'rgba(0,0,0,0.55)',
        'accent'  => $ad->accent_color ?? '#ff0808',
        'pattern' => $ad->type === 'text',
    ])->toArray()
    : [
        [
            'type'    => 'image',
            'media'   => 'https://images.pexels.com/photos/3184465/pexels-photo-3184465.jpeg?auto=compress&cs=tinysrgb&w=200&h=500&fit=crop',
            'headline'=> 'Grow Your Business',
            'sub'     => 'Reach 1M+ buyers',
            'badge'   => 'SPONSORED',
            'url'     => '#',
            'overlay' => 'rgba(13,31,60,0.68)',
            'accent'  => '#ff0808',
        ],
        [
            'type'    => 'image',
            'media'   => 'https://images.pexels.com/photos/1640777/pexels-photo-1640777.jpeg?auto=compress&cs=tinysrgb&w=200&h=500&fit=crop',
            'headline'=> 'Fresh Produce Deals',
            'sub'     => 'Direct from farmers',
            'badge'   => 'FEATURED',
            'url'     => '#',
            'overlay' => 'rgba(20,83,45,0.72)',
            'accent'  => '#86efac',
        ],
        [
            'type'    => 'gif',
            'media'   => 'https://media.giphy.com/media/3o7abKhOpu0NwenH3O/giphy.gif',
            'headline'=> 'Flash Sale!',
            'sub'     => '48 hours only',
            'badge'   => 'LIVE',
            'url'     => '#',
            'overlay' => 'rgba(0,0,0,0.50)',
            'accent'  => '#f43f5e',
        ],
        [
            'type'    => 'image',
            'media'   => 'https://images.pexels.com/photos/2599244/pexels-photo-2599244.jpeg?auto=compress&cs=tinysrgb&w=200&h=500&fit=crop',
            'headline'=> 'Electronics Bulk',
            'sub'     => 'Verified suppliers',
            'badge'   => 'SALE',
            'url'     => '#',
            'overlay' => 'rgba(0,48,73,0.72)',
            'accent'  => '#34d399',
        ],
        [
            'type'    => 'text',
            'bg'      => 'linear-gradient(180deg,#ff0808 0%,#c80000 100%)',
            'headline'=> '🔥 70% OFF',
            'sub'     => 'Limited time only',
            'badge'   => 'DEAL',
            'url'     => '#',
            'overlay' => 'transparent',
            'accent'  => '#fff',
            'pattern' => true,
        ],
        [
            'type'    => 'image',
            'media'   => 'https://images.pexels.com/photos/906494/pexels-photo-906494.jpeg?auto=compress&cs=tinysrgb&w=200&h=500&fit=crop',
            'headline'=> 'Construction Deals',
            'sub'     => 'Bulk orders welcome',
            'badge'   => 'B2B',
            'url'     => '#',
            'overlay' => 'rgba(67,20,7,0.72)',
            'accent'  => '#fb923c',
        ],
        [
            'type'    => 'text',
            'bg'      => 'linear-gradient(180deg,#064e3b 0%,#022c22 100%)',
            'headline'=> '🌍 Export Ready?',
            'sub'     => 'We connect you globally',
            'badge'   => 'EXPORT',
            'url'     => '#',
            'overlay' => 'transparent',
            'accent'  => '#6ee7b7',
            'pattern' => true,
        ],
        [
            'type'    => 'image',
            'media'   => 'https://images.pexels.com/photos/1082529/pexels-photo-1082529.jpeg?auto=compress&cs=tinysrgb&w=200&h=500&fit=crop',
            'headline'=> 'Beauty & Cosmetics',
            'sub'     => 'African brands global',
            'badge'   => 'TRENDING',
            'url'     => '#',
            'overlay' => 'rgba(131,24,67,0.70)',
            'accent'  => '#f9a8d4',
        ],
    ];
@endphp

{{-- Fixed Right Ad Strip --}}
@php
$adChunks = array_chunk($rightSideAds, 2);
$chunkCount = count($adChunks);
$slotHeight = floor(100 / $chunkCount);
@endphp

{{-- <div id="fixedRightAd" class="hidden xl:block" --}}
<div id="fixedRightAd" class="hidden"
     style="position:fixed; right:12px; top:0; width:106px; z-index:49; pointer-events:auto;
            border-radius:6px; overflow:hidden;
            box-shadow: -6px 0 20px rgba(0,0,0,0.25), 0 4px 16px rgba(0,0,0,0.2), 2px 0 8px rgba(0,0,0,0.15);">

    @foreach($adChunks as $ci => $chunk)
    <div style="position:absolute; left:0; right:0; top:{{ $ci * $slotHeight }}vh; height:{{ $slotHeight }}vh; overflow:hidden;
                {{ $ci > 0 ? 'box-shadow: 0 -3px 8px rgba(0,0,0,0.35); border-top: 1px solid rgba(255,255,255,0.08);' : '' }}">

        @foreach($chunk as $ai => $rad)
        <a href="{{ $rad['url'] }}"
           class="rsa-slot-{{ $ci }} group"
           style="position:absolute; inset:0; width:100%; height:100%; display:block; text-decoration:none !important;
                  opacity:{{ $ai === 0 ? '1' : '0' }};
                  z-index:{{ $ai === 0 ? '2' : '1' }};
                  transition: opacity 1s ease-in-out;
                  overflow:hidden;">

            @if($rad['type'] === 'image')
                <img src="{{ $rad['media'] }}" alt="{{ $rad['headline'] }}"
                     style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;">
            @elseif($rad['type'] === 'gif')
                <img src="{{ $rad['media'] }}" alt="{{ $rad['headline'] }}"
                     style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;">
            @elseif($rad['type'] === 'video')
                <video style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;"
                       autoplay muted loop playsinline preload="none">
                    <source src="{{ $rad['media'] }}" type="video/mp4">
                </video>
            @elseif($rad['type'] === 'text')
                <div style="position:absolute;inset:0;background:{{ $rad['bg'] ?? '#ff0808' }};"></div>
                @if(!empty($rad['pattern']))
                <div style="position:absolute;inset:0;opacity:0.1;background-image:radial-gradient(circle,#fff 1px,transparent 1px);background-size:12px 12px;"></div>
                @endif
            @endif

            @if(isset($rad['overlay']) && $rad['overlay'] !== 'transparent')
            <div style="position:absolute;inset:0;background:linear-gradient(to top,{{ $rad['overlay'] }} 0%,rgba(0,0,0,0.1) 60%,transparent 100%);"></div>
            @endif

            {{-- Top inner shadow on each card --}}
            <div style="position:absolute;top:0;left:0;right:0;height:28px;background:linear-gradient(to bottom,rgba(0,0,0,0.35),transparent);z-index:5;pointer-events:none;"></div>

            <div style="position:absolute;bottom:0;left:0;right:0;z-index:10;padding:8px;">
                @if(!empty($rad['badge']))
                <span style="display:inline-block;margin-bottom:3px;padding:1px 5px;font-size:7px;font-weight:900;letter-spacing:.1em;text-transform:uppercase;border-radius:2px;
                             background:{{ $rad['accent'] ?? '#ff0808' }};
                             color:{{ in_array($rad['accent']??'',['#86efac','#6ee7b7','#fbbf24','#facc15','#fcd34d','#f9a8d4','#fff'])?'#0a0a0a':'#fff' }};">
                    {{ $rad['badge'] }}
                </span>
                @endif
                <p style="color:#fff;font-weight:900;font-size:10px;line-height:1.3;margin:0;text-shadow:0 1px 4px rgba(0,0,0,0.6);">{{ $rad['headline'] }}</p>
                <p style="color:rgba(255,255,255,0.65);font-size:8px;margin:2px 0 0 0;text-shadow:0 1px 3px rgba(0,0,0,0.5);">{{ $rad['sub'] }}</p>
            </div>

            <span style="position:absolute;top:6px;left:6px;z-index:20;color:rgba(255,255,255,0.25);font-size:7px;font-weight:700;letter-spacing:.1em;pointer-events:none;">AD</span>

            {{-- Left accent bar --}}
            <div style="position:absolute;left:0;top:0;bottom:0;width:3px;background:{{ $rad['accent'] ?? '#ff0808' }};opacity:0.75;z-index:15;box-shadow:1px 0 6px {{ $rad['accent'] ?? '#ff0808' }};"></div>

        </a>
        @endforeach

    </div>
    @endforeach

</div>

<script>
(function(){
    const CHUNKS = {{ $chunkCount }};
    for(let ci = 0; ci < CHUNKS; ci++){
        const items = document.querySelectorAll('.rsa-slot-' + ci);
        if(items.length <= 1) continue;
        let cur = 0;
        setTimeout(function(){
            setInterval(function(){
                items[cur].style.opacity = '0';
                items[cur].style.zIndex  = '1';
                cur = (cur + 1) % items.length;
                items[cur].style.opacity = '1';
                items[cur].style.zIndex  = '2';
            }, 4500);
        }, ci * 1200);
    }

    function reposition(){
        const el = document.getElementById('fixedRightAd');
        if(!el) return;

        const adsBanner  = document.getElementById('afri-ad-banner');
        const mainHeader = document.getElementById('mainHeader');
        const navBar     = document.getElementById('navBar');

        const navBarEl     = document.getElementById('navBar');
        const navBarBottom = navBarEl ? navBarEl.getBoundingClientRect().bottom : 0;
        const headerBottom = mainHeader ? mainHeader.getBoundingClientRect().bottom : 0;
        const topPos = Math.max(navBarBottom, headerBottom, 0) + 12;

        const joinSection = document.querySelector(
            '[data-section="join-afrisellers"], .join-afrisellers-section, section:has(+ section:last-of-type)'
        );
        const footer = document.querySelector('footer');

        let bottomBoundary = 12;

        if(joinSection){
            const rect = joinSection.getBoundingClientRect();
            if(rect.top < window.innerHeight){
                bottomBoundary = window.innerHeight - rect.top + 12;
            }
        } else if(footer){
            const footerRect = footer.getBoundingClientRect();
            const stopsEarly = 400;
            const distFromBottom = window.innerHeight - (footerRect.top - stopsEarly);
            bottomBoundary = Math.max(12, distFromBottom);
        }

        const newHeight = window.innerHeight - topPos - bottomBoundary;

        el.style.top    = topPos + 'px';
        el.style.height = Math.max(0, newHeight) + 'px';

        const slots = el.querySelectorAll(':scope > div');
        if(slots.length > 0){
            const slotH = 100 / slots.length;
            slots.forEach((s, i) => {
                s.style.top    = (i * slotH) + '%';
                s.style.height = slotH + '%';
            });
        }

        el.style.visibility = newHeight <= 20 ? 'hidden' : 'visible';
    }

    window.addEventListener('DOMContentLoaded', reposition);
    window.addEventListener('scroll',  reposition, {passive:true});
    window.addEventListener('resize',  reposition, {passive:true});
    setTimeout(reposition, 300);
    setTimeout(reposition, 800);
})();
</script>

@endsection

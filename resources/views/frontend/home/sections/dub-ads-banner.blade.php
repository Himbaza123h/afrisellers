{{--
    ╔══════════════════════════════════════════════════════════════╗
    ║           AFRISELLERS — TOP HEADER ADVERTISEMENT BANNER      ║
    ║  Two-column · Fade · Image/GIF/Video/Text · Real + Fallback  ║
    ╚══════════════════════════════════════════════════════════════╝
--}}

@php

// ── Load real running ads from DB ─────────────────────────────────
$realAds = \App\Models\Advertisement::where('position', 'homepage_header')
    ->where('status', 'running')
    ->where('end_date', '>=', now())
    ->orderBy('approved_at', 'desc')
    ->get();

// ── Map real ads to unified structure ─────────────────────────────
$realAdsMapped = $realAds->map(fn($ad) => [
    'real_id' => $ad->id,
    'type'    => $ad->type,
    'media'   => $ad->media_url,
    'bg'      => $ad->bg_gradient ?? 'linear-gradient(135deg,#ff0808 0%,#c80000 100%)',
    'headline'=> $ad->headline ?? $ad->title,
    'sub'     => $ad->sub_text ?? '',
    'cta_url' => $ad->destination_url ?? '#',
    'badge'   => $ad->badge_text ?? 'SPONSORED',
    'overlay' => $ad->overlay_color ?? 'rgba(0,0,0,0.55)',
    'accent'  => $ad->accent_color ?? '#ff0808',
    'pattern' => $ad->type === 'text',
])->toArray();

// ── Fallback dummy data ────────────────────────────────────────────
$dummyAds = [
    [
        'real_id' => null,
        'type'    => 'image',
        'media'   => 'https://images.pexels.com/photos/3184465/pexels-photo-3184465.jpeg?auto=compress&cs=tinysrgb&w=900&h=220&fit=crop',
        'headline'=> 'Scale Your Business Across Africa',
        'sub'     => 'Reach 1M+ verified buyers on Afrisellers',
        'cta_url' => '#',
        'badge'   => 'SPONSORED',
        'overlay' => 'rgba(13,31,60,0.72)',
        'accent'  => '#ff0808',
    ],
    [
        'real_id' => null,
        'type'    => 'image',
        'media'   => 'https://images.pexels.com/photos/1544946/pexels-photo-1544946.jpeg?auto=compress&cs=tinysrgb&w=900&h=220&fit=crop',
        'headline'=> 'Premium Fashion. Wholesale Prices.',
        'sub'     => 'Top designers. Direct factory deals.',
        'cta_url' => '#',
        'badge'   => 'NEW',
        'overlay' => 'rgba(91,0,0,0.70)',
        'accent'  => '#fbbf24',
    ],
    [
        'real_id' => null,
        'type'    => 'image',
        'media'   => 'https://images.pexels.com/photos/2599244/pexels-photo-2599244.jpeg?auto=compress&cs=tinysrgb&w=900&h=220&fit=crop',
        'headline'=> 'Electronics at Unbeatable Prices',
        'sub'     => 'Verified suppliers. Bulk discounts available.',
        'cta_url' => '#',
        'badge'   => 'SALE',
        'overlay' => 'rgba(0,48,73,0.75)',
        'accent'  => '#34d399',
    ],
    [
        'real_id' => null,
        'type'    => 'image',
        'media'   => 'https://images.pexels.com/photos/1640777/pexels-photo-1640777.jpeg?auto=compress&cs=tinysrgb&w=900&h=220&fit=crop',
        'headline'=> 'Fresh Produce. Pan-African Logistics.',
        'sub'     => 'Farm to market — faster than ever.',
        'cta_url' => '#',
        'badge'   => 'FEATURED',
        'overlay' => 'rgba(20,83,45,0.78)',
        'accent'  => '#86efac',
    ],
    [
        'real_id' => null,
        'type'    => 'image',
        'media'   => 'https://images.pexels.com/photos/3182812/pexels-photo-3182812.jpeg?auto=compress&cs=tinysrgb&w=900&h=220&fit=crop',
        'headline'=> 'Advertise on Afrisellers Today',
        'sub'     => '500,000+ monthly visitors. Premium placements.',
        'cta_url' => '#',
        'badge'   => 'AD SPACE',
        'overlay' => 'rgba(30,27,75,0.78)',
        'accent'  => '#a78bfa',
    ],
    [
        'real_id' => null,
        'type'    => 'image',
        'media'   => 'https://images.pexels.com/photos/906494/pexels-photo-906494.jpeg?auto=compress&cs=tinysrgb&w=900&h=220&fit=crop',
        'headline'=> 'Construction Materials. Bulk Orders.',
        'sub'     => 'Trusted suppliers across 50+ African countries.',
        'cta_url' => '#',
        'badge'   => 'B2B',
        'overlay' => 'rgba(67,20,7,0.75)',
        'accent'  => '#fb923c',
    ],
    [
        'real_id' => null,
        'type'    => 'image',
        'media'   => 'https://images.pexels.com/photos/1082529/pexels-photo-1082529.jpeg?auto=compress&cs=tinysrgb&w=900&h=220&fit=crop',
        'headline'=> 'Beauty & Cosmetics — Going Global',
        'sub'     => 'African beauty brands now available worldwide.',
        'cta_url' => '#',
        'badge'   => 'TRENDING',
        'overlay' => 'rgba(131,24,67,0.72)',
        'accent'  => '#f9a8d4',
    ],
    [
        'real_id' => null,
        'type'    => 'image',
        'media'   => 'https://images.pexels.com/photos/3756679/pexels-photo-3756679.jpeg?auto=compress&cs=tinysrgb&w=900&h=220&fit=crop',
        'headline'=> 'Industrial Equipment & Machinery',
        'sub'     => 'Heavy-duty imports at competitive pricing.',
        'cta_url' => '#',
        'badge'   => 'IMPORT',
        'overlay' => 'rgba(15,23,42,0.78)',
        'accent'  => '#38bdf8',
    ],
    [
        'real_id' => null,
        'type'    => 'image',
        'media'   => 'https://images.pexels.com/photos/1396122/pexels-photo-1396122.jpeg?auto=compress&cs=tinysrgb&w=900&h=220&fit=crop',
        'headline'=> 'Agricultural Equipment Deals',
        'sub'     => 'Modern tools for modern African farmers.',
        'cta_url' => '#',
        'badge'   => 'HOT DEAL',
        'overlay' => 'rgba(23,37,84,0.72)',
        'accent'  => '#facc15',
    ],
    [
        'real_id' => null,
        'type'    => 'image',
        'media'   => 'https://images.pexels.com/photos/3184292/pexels-photo-3184292.jpeg?auto=compress&cs=tinysrgb&w=900&h=220&fit=crop',
        'headline'=> 'Join 50,000+ Afrisellers Businesses',
        'sub'     => 'List your company free. Sell across 54 countries.',
        'cta_url' => '#',
        'badge'   => 'FREE',
        'overlay' => 'rgba(6,78,59,0.78)',
        'accent'  => '#6ee7b7',
    ],
    [
        'real_id' => null,
        'type'    => 'image',
        'media'   => 'https://images.pexels.com/photos/5632397/pexels-photo-5632397.jpeg?auto=compress&cs=tinysrgb&w=900&h=220&fit=crop',
        'headline'=> 'Mobile Phones & Accessories',
        'sub'     => 'Latest models. Wholesale pricing available.',
        'cta_url' => '#',
        'badge'   => 'POPULAR',
        'overlay' => 'rgba(17,24,39,0.75)',
        'accent'  => '#818cf8',
    ],
    [
        'real_id' => null,
        'type'    => 'image',
        'media'   => 'https://images.pexels.com/photos/1267338/pexels-photo-1267338.jpeg?auto=compress&cs=tinysrgb&w=900&h=220&fit=crop',
        'headline'=> 'Textiles & Fabric Exports',
        'sub'     => 'Authentic African fabrics shipped worldwide.',
        'cta_url' => '#',
        'badge'   => 'EXPORT',
        'overlay' => 'rgba(88,28,135,0.72)',
        'accent'  => '#e879f9',
    ],
    [
        'real_id' => null,
        'type'    => 'gif',
        'media'   => 'https://media.giphy.com/media/3o7abKhOpu0NwenH3O/giphy.gif',
        'headline'=> 'Flash Sale — 48 Hours Only!',
        'sub'     => 'Prices dropping every hour. Don\'t miss out.',
        'cta_url' => '#',
        'badge'   => 'LIVE SALE',
        'overlay' => 'rgba(0,0,0,0.52)',
        'accent'  => '#f43f5e',
    ],
    [
        'real_id' => null,
        'type'    => 'gif',
        'media'   => 'https://media.giphy.com/media/l0HlBO7eyXzSZkJri/giphy.gif',
        'headline'=> 'New Tech Drops Every Week',
        'sub'     => 'Subscribe & get notified before everyone else.',
        'cta_url' => '#',
        'badge'   => 'TECH',
        'overlay' => 'rgba(15,23,42,0.58)',
        'accent'  => '#60a5fa',
    ],
    [
        'real_id' => null,
        'type'    => 'video',
        'media'   => 'https://www.w3schools.com/html/mov_bbb.mp4',
        'headline'=> 'Logistics Redefined for Africa',
        'sub'     => 'Real-time tracking. Pan-continental delivery.',
        'cta_url' => '#',
        'badge'   => 'VIDEO AD',
        'overlay' => 'rgba(0,0,0,0.52)',
        'accent'  => '#34d399',
    ],
    [
        'real_id' => null,
        'type'    => 'text',
        'bg'      => 'linear-gradient(135deg,#ff0808 0%,#c80000 100%)',
        'headline'=> '🔥 MEGA SALE — UP TO 70% OFF',
        'sub'     => 'Thousands of verified products. Limited time only.',
        'cta_url' => '#',
        'badge'   => 'LIMITED TIME',
        'overlay' => 'transparent',
        'accent'  => '#fff',
        'pattern' => true,
    ],
    [
        'real_id' => null,
        'type'    => 'text',
        'bg'      => 'linear-gradient(135deg,#1e3a5f 0%,#0d1f3c 100%)',
        'headline'=> '💎 VERIFIED SUPPLIERS. ZERO RISK.',
        'sub'     => 'Every supplier vetted. Every transaction protected.',
        'cta_url' => '#',
        'badge'   => 'TRUST',
        'overlay' => 'transparent',
        'accent'  => '#facc15',
        'pattern' => true,
    ],
    [
        'real_id' => null,
        'type'    => 'text',
        'bg'      => 'linear-gradient(135deg,#064e3b 0%,#022c22 100%)',
        'headline'=> '🌍 EXPORT READY? WE CONNECT YOU.',
        'sub'     => 'From Rwanda to the world — start exporting today.',
        'cta_url' => '#',
        'badge'   => 'EXPORT',
        'overlay' => 'transparent',
        'accent'  => '#6ee7b7',
        'pattern' => true,
    ],
    [
        'real_id' => null,
        'type'    => 'text',
        'bg'      => 'linear-gradient(135deg,#78350f 0%,#451a03 100%)',
        'headline'=> '☕ AFRICAN COFFEE & TEA EXPORTS',
        'sub'     => 'Rwanda. Ethiopia. Kenya. Premium grade.',
        'cta_url' => '#',
        'badge'   => 'AGRI',
        'overlay' => 'transparent',
        'accent'  => '#fcd34d',
        'pattern' => true,
    ],
];

// ── Use real ads if available, otherwise fallback to dummy ─────────
$headerAds = count($realAdsMapped) > 0 ? $realAdsMapped : $dummyAds;

// ── Pair into 2-column groups ──────────────────────────────────────
$adPairs = array_chunk($headerAds, 2);
@endphp

{{-- ═══════════════════ AD BANNER ═══════════════════ --}}
<div class="bg-white px-6 sm:px-10 md:px-16">
<div id="afri-ad-banner" class="relative overflow-hidden bg-[#0a0a0a] rounded-sm" style="height:112px; box-shadow: -6px 0 16px rgba(255,255,255,0.6), 6px 0 16px rgba(255,255,255,0.6), 0 4px 12px rgba(0,0,0,0.2);">

    {{-- All pairs stacked, fade in/out --}}
    @foreach($adPairs as $pairIndex => $pair)
    <div class="ad-pair absolute inset-0 flex transition-opacity duration-1000 ease-in-out"
         style="gap:2px; opacity:{{ $pairIndex === 0 ? '1' : '0' }}; z-index:{{ $pairIndex === 0 ? '2' : '1' }};">

        @foreach($pair as $adIndex => $ad)
        <a href="{{ $ad['cta_url'] ?? '#' }}"
           class="relative flex-1 overflow-hidden group ad-cell"
           data-ad-id="{{ $pairIndex }}-{{ $adIndex }}"
           data-real-id="{{ $ad['real_id'] ?? '' }}"
           onclick="trackAdClick(this)">

            {{-- ── Media ── --}}
            @if($ad['type'] === 'image')
                <img src="{{ $ad['media'] }}"
                     alt="{{ $ad['headline'] }}"
                     class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                     loading="lazy">

            @elseif($ad['type'] === 'gif')
                <img src="{{ $ad['media'] }}"
                     alt="{{ $ad['headline'] }}"
                     class="absolute inset-0 w-full h-full object-cover">

            @elseif($ad['type'] === 'video')
                <video class="absolute inset-0 w-full h-full object-cover"
                       autoplay muted loop playsinline preload="none">
                    <source src="{{ $ad['media'] }}" type="video/mp4">
                </video>

            @elseif($ad['type'] === 'text')
                <div class="absolute inset-0"
                     style="background:{{ $ad['bg'] ?? '#ff0808' }};"></div>
                @if(!empty($ad['pattern']))
                <div class="absolute inset-0 opacity-10"
                     style="background-image:radial-gradient(circle,#fff 1px,transparent 1px);
                            background-size:18px 18px;"></div>
                @endif
            @endif

            {{-- ── Overlay ── --}}
            @if(isset($ad['overlay']) && $ad['overlay'] !== 'transparent')
            <div class="absolute inset-0"
                 style="background:linear-gradient(to right,{{ $ad['overlay'] }} 0%,{{ $ad['overlay'] }} 45%,rgba(0,0,0,0.05) 100%);"></div>
            @endif

            {{-- ── Shimmer ── --}}
            <div class="ad-shimmer absolute inset-0 pointer-events-none"></div>

            {{-- ── Content ── --}}
            <div class="relative z-10 h-full flex items-center px-5 md:px-7">
                <div class="min-w-0">

                    @if(!empty($ad['badge']))
                    <span class="inline-block mb-1 px-2 py-0.5 text-[8px] font-black tracking-widest uppercase rounded-sm leading-none"
                          style="background:{{ $ad['accent'] ?? '#ff0808' }};
                                 color:{{ in_array($ad['accent']??'', ['#fff','#86efac','#6ee7b7','#fbbf24','#facc15','#fcd34d','#f9a8d4','#fb923c','#34d399','#38bdf8','#60a5fa','#818cf8','#e879f9']) ? '#0a0a0a' : '#fff' }};">
                        {{ $ad['badge'] }}
                    </span>
                    @endif

                    <h3 class="text-white font-black leading-tight drop-shadow-lg"
                        style="font-size:clamp(12px,1.4vw,16px);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $ad['headline'] }}
                    </h3>

                    <p class="text-white/70 mt-0.5 truncate drop-shadow"
                       style="font-size:clamp(9px,0.9vw,11px);">
                        {{ $ad['sub'] }}
                    </p>

                </div>
            </div>

        </a>
        @endforeach

        {{-- ── Fill odd pair with "Advertise Here" slot ── --}}
        @if(count($pair) === 1)
        <a href="#" class="relative flex-1 overflow-hidden group bg-[#111]" style="display:block;text-decoration:none;">
            <div class="absolute inset-0 opacity-5"
                 style="background-image:repeating-linear-gradient(45deg,#fff 0,#fff 1px,transparent 0,transparent 50%);
                        background-size:10px 10px;"></div>
            <div class="relative z-10 h-full flex items-center justify-center">
                <div class="text-center">
                    <p class="text-white/35 text-[10px] font-black uppercase tracking-widest mb-0.5">Advertise Here</p>
                    <p class="text-white/60 font-black text-sm">900 × 112px · Premium Slot</p>
                </div>
            </div>
        </a>
        @endif

    </div>
    @endforeach

    {{-- AD label --}}
    <span class="absolute top-1 right-2 z-30 text-white/20 text-[8px] font-bold tracking-widest pointer-events-none select-none">AD</span>

</div>
</div>
{{-- ═══════════ STYLES ═══════════ --}}
<style>
#afri-ad-banner { transition: height .35s ease, opacity .35s ease; }
.ad-cell { -webkit-tap-highlight-color:transparent; text-decoration:none !important; display:block; }
.ad-shimmer {
    background: linear-gradient(105deg, transparent 35%, rgba(255,255,255,.06) 50%, transparent 65%);
    transform: translateX(-110%);
}
.ad-cell:hover .ad-shimmer {
    transform: translateX(110%);
    transition: transform .65s ease;
}
</style>

{{-- ═══════════ SCRIPT ═══════════ --}}
<script>
(function(){
    const TOTAL    = {{ count($adPairs) }};
    const INTERVAL = 5500;
    let   cur = 0, timer = null, hidden = false;
    const banner = document.getElementById('afri-ad-banner');

    function move(){
        document.querySelectorAll('#afri-ad-banner .ad-pair').forEach((el, i) => {
            el.style.opacity = i === cur ? '1' : '0';
            el.style.zIndex  = i === cur ? '2' : '1';
        });
    }

    function next(){ cur = ((cur + 1) % TOTAL); move(); }

    function reset(){
        clearInterval(timer);
        if(TOTAL > 1) timer = setInterval(next, INTERVAL);
    }
    reset();

    if(banner){
        banner.addEventListener('mouseenter', () => clearInterval(timer));
        banner.addEventListener('mouseleave', reset);

        // Touch swipe
        let tx = 0;
        banner.addEventListener('touchstart', e => { tx = e.changedTouches[0].screenX; }, {passive:true});
        banner.addEventListener('touchend',   e => {
            const d = tx - e.changedTouches[0].screenX;
            if(Math.abs(d) > 40){
                cur = ((cur + (d > 0 ? 1 : -1) + TOTAL) % TOTAL);
                move();
                reset();
            }
        }, {passive:true});
    }

    // Hide on scroll down, show on scroll back to top
    window.addEventListener('scroll', () => {
        if(!banner) return;
        const y = window.pageYOffset;
        if(y > 60 && !hidden){
            banner.style.height  = '0';
            banner.style.opacity = '0';
            banner.style.overflow = 'hidden';
            hidden = true;
        } else if(y <= 60 && hidden){
            banner.style.height  = '112px';
            banner.style.opacity = '1';
            banner.style.overflow = 'visible';
            hidden = false;
        }
    }, {passive:true});

    // Track clicks — fires for real ads only
    window.trackAdClick = function(el) {
        const realId = el.getAttribute('data-real-id');
        if(!realId) return;
        fetch('/ads/track-click', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({ id: realId })
        }).catch(() => {});
    };

    // Track impressions for visible real ads
    function trackImpression(realId){
        if(!realId) return;
        fetch('/ads/track-impression', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({ id: realId })
        }).catch(() => {});
    }

    // Track impression of first visible pair on load
    document.addEventListener('DOMContentLoaded', () => {
        const firstPair = document.querySelector('#afri-ad-banner .ad-pair');
        if(firstPair){
            firstPair.querySelectorAll('.ad-cell').forEach(cell => {
                const rid = cell.getAttribute('data-real-id');
                if(rid) trackImpression(rid);
            });
        }
    });

})();
</script>

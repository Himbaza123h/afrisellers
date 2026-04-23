@php
    $realPartners = \App\Models\Partner::active()->ordered()->get()->map(fn($p) => [
        'name'        => $p->name,
        'logo'        => $p->logo_url,
        'industry'    => $p->industry ?? '',
        'type'        => $p->partner_type ?? '',
        'website_url' => $p->website_url ?? '#',
    ])->toArray();

    // Fallback if no partners in DB yet
    if (empty($realPartners)) {
        $realPartners = [
            ['name' => 'Maersk',        'logo' => 'https://logowik.com/content/uploads/images/t_ap-moller-maersk-group5209.logowik.com.webp',                                              'industry' => 'Logistics',              'type' => 'Global Partner',      'website_url' => '#'],
            ['name' => 'DHL',           'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/ac/DHL_Logo.svg/1280px-DHL_Logo.svg.png',                                        'industry' => 'Express Shipping',       'type' => 'Strategic Partner',   'website_url' => '#'],
            ['name' => 'Standard Bank', 'logo' => 'https://brandlogos.net/wp-content/uploads/2025/10/standard_bank-logo_brandlogos.net_aqqyw.png',                                         'industry' => 'Financial Services',     'type' => 'Banking Partner',     'website_url' => '#'],
            ['name' => 'Ecobank',       'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/98/Ecobank_Logo.svg/1280px-Ecobank_Logo.svg.png',                                'industry' => 'Pan-African Banking',    'type' => 'Financial Partner',   'website_url' => '#'],
            ['name' => 'SGS',           'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e9/SGS_Logo.svg/1280px-SGS_Logo.svg.png',                                        'industry' => 'Certification',          'type' => 'Quality Partner',     'website_url' => '#'],
            ['name' => 'Bureau Veritas','logo' => 'https://iconape.com/wp-content/png_logo_vector/iso-9001-bureau-veritas-logo.png',                                                       'industry' => 'Testing & Cert.',        'type' => 'Compliance Partner',  'website_url' => '#'],
            ['name' => 'Kuehne+Nagel',  'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c6/K%C3%BChne_%2B_Nagel_logo.svg/1280px-K%C3%BChne_%2B_Nagel_logo.svg.png',    'industry' => 'Logistics',              'type' => 'Shipping Partner',    'website_url' => '#'],
            ['name' => 'CMA CGM',       'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d1/CMA_CGM_logo.svg/960px-CMA_CGM_logo.svg.png',                                'industry' => 'Container Shipping',     'type' => 'Global Carrier',      'website_url' => '#'],
            ['name' => 'DP World',      'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2d/DP_World_2021_logo.svg/1280px-DP_World_2021_logo.svg.png',                   'industry' => 'Port Operations',        'type' => 'Infrastructure',      'website_url' => '#'],
            ['name' => 'QIMA',          'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fa/QIMA_-_Logo.png/330px-QIMA_-_Logo.png',                                      'industry' => 'Quality Control',        'type' => 'Testing Partner',     'website_url' => '#'],
            ['name' => 'African Union', 'logo' => 'https://au.int/sites/default/files/pages/31823-img-au_logo.jpg',                                                                        'industry' => 'International Org.',     'type' => 'Strategic Partner',   'website_url' => '#'],
            ['name' => 'Afreximbank',   'logo' => 'https://geometricpower.com/wp-content/uploads/2021/11/Afreximbank-Logo-Wide.png',                                                       'industry' => 'Trade Finance',          'type' => 'Development Partner', 'website_url' => '#'],
        ];
    }
@endphp

<!-- Our Trusted Partners - Professional Single Row -->
<section class="py-12 md:py-16 bg-white overflow-hidden">
    <div class="container px-4 mx-auto">

    <div class="flex justify-between items-center mb-3 md:mb-4">
        <h2 class="text-base md:text-lg lg:text-xl font-bold text-gray-900">{{ __('messages.our_network') }}</h2>
        <a href="{{ route('partner.request.form') }}"
        class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white text-xs font-bold rounded-lg hover:bg-red-700 transition-all shadow-sm">
            <i class="fas fa-handshake"></i>
            Request to be a Partner
        </a>
    </div>
        <div class="text-center max-w-3xl mx-auto mb-10 md:mb-12">
            <p class="text-xs md:text-sm text-gray-500">
                {{ __('messages.partner_global_subtitle') ?? 'Connecting African suppliers with verified buyers across 50+ countries' }}
            </p>
        </div>

        <!-- Partners Marquee -->
        <div class="relative w-full">
            <div class="partners-marquee-container">
                <div class="partners-track">

                    {{-- First set --}}
                    @foreach($realPartners as $partner)
                        <a href="{{ $partner['website_url'] }}" target="_blank" class="partner-item" style="text-decoration:none;">
                            <div class="partner-logo-wrapper">
                                <img src="{{ $partner['logo'] }}"
                                     alt="{{ $partner['name'] }} logo"
                                     class="partner-logo"
                                     loading="lazy"
                                     onerror="this.src='https://via.placeholder.com/120x60/e6f0ff/2563eb?text={{ urlencode($partner['name']) }}'">
                            </div>
                            <div class="partner-info">
                                <span class="partner-name">{{ $partner['name'] }}</span>
                                <span class="partner-type">{{ $partner['type'] }}</span>
                            </div>
                        </a>
                    @endforeach

                    {{-- Duplicate set for seamless loop --}}
                    @foreach($realPartners as $partner)
                        <a href="{{ $partner['website_url'] }}" target="_blank" class="partner-item" style="text-decoration:none;">
                            <div class="partner-logo-wrapper">
                                <img src="{{ $partner['logo'] }}"
                                     alt="{{ $partner['name'] }} logo"
                                     class="partner-logo"
                                     loading="lazy"
                                     onerror="this.src='https://via.placeholder.com/120x60/e6f0ff/2563eb?text={{ urlencode($partner['name']) }}'">
                            </div>
                            <div class="partner-info">
                                <span class="partner-name">{{ $partner['name'] }}</span>
                                <span class="partner-type">{{ $partner['type'] }}</span>
                            </div>
                        </a>
                    @endforeach

                </div>
            </div>

            <!-- Gradient Overlays -->
            <div class="marquee-gradient-left"></div>
            <div class="marquee-gradient-right"></div>
        </div>

    </div>
</section>

<style>
    .partners-marquee-container {
        width: 100%;
        overflow: hidden;
        position: relative;
        padding: 20px 0;
        background: linear-gradient(to right, white, rgba(255,255,255,0.95), white);
    }

    .partners-track {
        display: flex;
        gap: 40px;
        width: max-content;
        animation: scrollProfessional 40s linear infinite;
        will-change: transform;
        backface-visibility: hidden;
        -webkit-font-smoothing: antialiased;
    }

    .partners-track:hover {
        animation-play-state: paused;
    }

    .partner-item {
        flex-shrink: 0;
        width: 200px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 20px 25px;
        background: white;
        border-radius: 12px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #f0f2f5;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        cursor: pointer;
    }

    .partner-item:hover {
        border-color: #3b82f6;
        box-shadow: 0 20px 30px -10px rgba(59,130,246,0.15);
        transform: translateY(-5px);
        background: linear-gradient(to bottom, white, #fafcff);
    }

    .partner-logo-wrapper {
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
        background: white;
    }

    .partner-logo {
        max-width: 140px;
        max-height: 50px;
        width: auto;
        height: auto;
        object-fit: contain;
        transition: transform 0.3s ease;
        filter: brightness(0.95) contrast(1.1);
    }

    .partner-item:hover .partner-logo {
        transform: scale(1.05);
        filter: brightness(1.1) contrast(1.2);
    }

    .partner-info {
        text-align: center;
        width: 100%;
    }

    .partner-name {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 4px;
        letter-spacing: -0.01em;
    }

    .partner-type {
        display: inline-block;
        font-size: 10px;
        font-weight: 600;
        color: #3b82f6;
        background: #eff6ff;
        padding: 3px 10px;
        border-radius: 30px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        border: 1px solid rgba(59,130,246,0.2);
    }

    .marquee-gradient-left,
    .marquee-gradient-right {
        position: absolute;
        top: 0;
        bottom: 0;
        width: 180px;
        z-index: 2;
        pointer-events: none;
    }

    .marquee-gradient-left {
        left: 0;
        background: linear-gradient(to right, white 0%, rgba(255,255,255,0.85) 40%, transparent 100%);
    }

    .marquee-gradient-right {
        right: 0;
        background: linear-gradient(to left, white 0%, rgba(255,255,255,0.85) 40%, transparent 100%);
    }

    @keyframes scrollProfessional {
        0%   { transform: translateX(0); }
        100% { transform: translateX(calc(-50% - 20px)); }
    }

    @media (max-width: 768px) {
        .partner-item { width: 160px; padding: 15px 18px; }
        .partner-logo { max-width: 110px; max-height: 40px; }
        .partner-name { font-size: 11px; }
        .partner-type { font-size: 8px; padding: 2px 8px; }
        .partners-track { gap: 25px; animation: scrollProfessional 30s linear infinite; }
        .marquee-gradient-left, .marquee-gradient-right { width: 80px; }
    }

    @media (max-width: 480px) {
        .partner-item { width: 140px; padding: 12px 15px; }
        .partner-logo { max-width: 100px; max-height: 35px; }
        .partners-track { gap: 20px; animation: scrollProfessional 25s linear infinite; }
        .marquee-gradient-left, .marquee-gradient-right { width: 50px; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.addEventListener('visibilitychange', function () {
            const track = document.querySelector('.partners-track');
            if (track) {
                track.style.animationPlayState = document.hidden ? 'paused' : 'running';
            }
        });
    });
</script>

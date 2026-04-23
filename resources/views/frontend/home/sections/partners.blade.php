@php
    $realPartners = \App\Models\Partner::active()->ordered()->get();
@endphp

@if($realPartners->isEmpty())
    <section class="py-12 md:py-16 bg-white">
        <div class="container px-4 mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-base md:text-lg lg:text-xl font-bold text-gray-900">Our Network</h2>
                <a href="{{ route('partner.request.form') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white text-xs font-bold rounded-lg hover:bg-red-700 transition-all shadow-sm">
                    <i class="fas fa-handshake"></i>
                    Request to be a Partner
                </a>
            </div>
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-handshake text-gray-300 text-2xl"></i>
                </div>
                <p class="text-gray-500 text-sm font-medium">No partners found yet.</p>
                <p class="text-gray-400 text-xs mt-1">Be the first to partner with us.</p>
                <a href="{{ route('partner.request.form') }}"
                   class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 bg-[#ff0808] text-white text-xs font-bold rounded-lg hover:bg-red-700 transition-all">
                    <i class="fas fa-plus"></i> Become a Partner
                </a>
            </div>
        </div>
    </section>
@else
<section class="py-12 md:py-16 bg-white overflow-hidden">
    <div class="container px-4 mx-auto">

        <div class="flex justify-between items-center mb-3 md:mb-4">
            <h2 class="text-base md:text-lg lg:text-xl font-bold text-gray-900">Our Network</h2>
            <a href="{{ route('partner.request.form') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white text-xs font-bold rounded-lg hover:bg-red-700 transition-all shadow-sm">
                <i class="fas fa-handshake"></i>
                Request to be a Partner
            </a>
        </div>

        <div class="text-center max-w-3xl mx-auto mb-10 md:mb-12">
            <p class="text-xs md:text-sm text-gray-500">
                Connecting African suppliers with verified buyers across 50+ countries
            </p>
        </div>

        <!-- Partners Marquee -->
        <div class="relative w-full">
            <div class="partners-marquee-container">
                <div class="partners-track">

                    {{-- First set --}}
                    @foreach($realPartners as $partner)
                        <a href="{{ route('partners.show', ['id' => $partner->partnerRequest->id, 'name' => str()->slug($partner->name)]) }}"
                           class="partner-item" style="text-decoration:none;">
                            <div class="partner-logo-wrapper">
                                @if($partner->logo_url)
                                    <img src="{{ $partner->logo_url }}"
                                         alt="{{ $partner->name }} logo"
                                         class="partner-logo"
                                         loading="lazy"
                                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                                    <div class="partner-logo-fallback" style="display:none;">
                                        <i class="fas fa-building text-gray-300 text-2xl"></i>
                                    </div>
                                @else
                                    <div class="partner-logo-fallback">
                                        <i class="fas fa-building text-gray-300 text-2xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="partner-info">
                                <span class="partner-name">{{ $partner->name }}</span>
                                @if($partner->partner_type)
                                    <span class="partner-type">{{ $partner->partner_type }}</span>
                                @endif
                            </div>
                        </a>
                    @endforeach

                    {{-- Duplicate set for seamless loop --}}
                    @foreach($realPartners as $partner)
                        <a href="{{ route('partners.show', ['id' => $partner->partnerRequest->id, 'name' => str()->slug($partner->name)]) }}"
                           class="partner-item" style="text-decoration:none;">
                            <div class="partner-logo-wrapper">
                                @if($partner->logo_url)
                                    <img src="{{ $partner->logo_url }}"
                                         alt="{{ $partner->name }} logo"
                                         class="partner-logo"
                                         loading="lazy"
                                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                                    <div class="partner-logo-fallback" style="display:none;">
                                        <i class="fas fa-building text-gray-300 text-2xl"></i>
                                    </div>
                                @else
                                    <div class="partner-logo-fallback">
                                        <i class="fas fa-building text-gray-300 text-2xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="partner-info">
                                <span class="partner-name">{{ $partner->name }}</span>
                                @if($partner->partner_type)
                                    <span class="partner-type">{{ $partner->partner_type }}</span>
                                @endif
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
        width: 100%;
    }

    .partner-logo-fallback {
        width: 60px;
        height: 60px;
        background: #f3f4f6;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
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
@endif

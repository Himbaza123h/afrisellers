@extends('layouts.app')

@section('title', $partner->name . ' — Partner')

@section('content')

@php
    $introUrl  = $partner->intro_url ?? $partner->intro ?? null;
    $isVideo   = false;
    $isYoutube = false;
    $embedUrl  = null;
    $fallbackImg = 'https://images.pexels.com/photos/20371057/pexels-photo-20371057.jpeg';

    if ($introUrl) {
        if (Str::contains($introUrl, ['youtube.com', 'youtu.be'])) {
            $isVideo   = true;
            $isYoutube = true;
            if (Str::contains($introUrl, 'youtu.be/')) {
                $videoId  = Str::afterLast($introUrl, 'youtu.be/');
                $videoId  = explode('?', $videoId)[0];
            } else {
                parse_str(parse_url($introUrl, PHP_URL_QUERY), $params);
                $videoId = $params['v'] ?? '';
            }
            $embedUrl = 'https://www.youtube.com/embed/' . $videoId . '?autoplay=0&rel=0';
        }
        elseif (Str::contains($introUrl, 'vimeo.com')) {
            $isVideo   = true;
            $isYoutube = false;
            $videoId   = Str::afterLast($introUrl, '/');
            $embedUrl  = 'https://player.vimeo.com/video/' . $videoId;
        }
        elseif (Str::contains(strtolower($introUrl), ['.mp4', '.webm', '.ogg', '.mov'])) {
            $isVideo   = true;
            $isYoutube = false;
        }
    }
@endphp

<style>
    .partner-page {
        background: #f4f4f6;
        min-height: 100vh;
    }

    /* ── Hero ── */
    .hero-wrap {
        position: relative;
        width: 100%;
        background: #000;
        overflow: hidden;
    }
    .hero-media {
        width: 100%;
        height: 340px;
        object-fit: cover;
        display: block;
    }
    @media (min-width: 640px)  { .hero-media { height: 420px; } }
    @media (min-width: 1024px) { .hero-media { height: 520px; } }

    .hero-iframe-wrap {
        position: relative;
        width: 100%;
        height: 340px;
    }
    @media (min-width: 640px)  { .hero-iframe-wrap { height: 420px; } }
    @media (min-width: 1024px) { .hero-iframe-wrap { height: 520px; } }

    .hero-iframe-wrap iframe {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        border: none;
    }

    .hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(
            to bottom,
            rgba(0,0,0,0.18) 0%,
            transparent 40%,
            rgba(0,0,0,0.55) 100%
        );
        pointer-events: none;
    }

    /* ── Back btn ── */
    .back-btn {
        position: absolute;
        top: 1rem;
        left: 1rem;
        z-index: 30;
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 50%;
        background: rgba(0,0,0,0.45);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        text-decoration: none;
        transition: background .2s;
    }
    .back-btn:hover { background: rgba(0,0,0,0.65); }

    /* ── Partner badge ── */
    .partner-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        z-index: 30;
        display: flex;
        align-items: center;
        gap: .6rem;
        background: rgba(0,0,0,0.52);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border-radius: 1.25rem;
        padding: .5rem .9rem .5rem .5rem;
        max-width: calc(100% - 4.5rem);
    }
    .partner-badge-logo {
        width: 2.4rem;
        height: 2.4rem;
        border-radius: .6rem;
        object-fit: contain;
        background: #fff;
        padding: 3px;
        flex-shrink: 0;
    }
    .partner-badge-name {
        color: #fff;
        font-weight: 800;
        font-size: .85rem;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .partner-badge-verified {
        color: #4ade80;
        font-size: .65rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 3px;
        margin-top: 2px;
    }

    /* ── Hero bottom label ── */
    .hero-bottom-label {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 10;
        padding: .75rem 1.25rem;
        pointer-events: none;
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
    }
    .hero-partner-name {
        color: #fff;
        font-size: 1.3rem;
        font-weight: 900;
        letter-spacing: -.01em;
        text-shadow: 0 2px 12px rgba(0,0,0,.4);
        line-height: 1.2;
    }
    @media (min-width: 640px) { .hero-partner-name { font-size: 1.7rem; } }
    .hero-tag {
        background: #ff0808;
        color: #fff;
        font-size: .65rem;
        font-weight: 700;
        padding: .25rem .7rem;
        border-radius: 100px;
        white-space: nowrap;
    }

    /* ── Sticky tab nav ── */
    .tab-nav-outer {
        background: #fff;
        border-bottom: 1px solid #e8e8ec;
        position: sticky;
        top: 0;
        z-index: 50;
        box-shadow: 0 2px 8px rgba(0,0,0,.05);
    }
    .tab-nav-inner {
        display: flex;
        overflow-x: auto;
        scrollbar-width: none;
        -ms-overflow-style: none;
        gap: 0;
    }
    .tab-nav-inner::-webkit-scrollbar { display: none; }

    .tab-btn {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        gap: .4rem;
        padding: .85rem 1.1rem;
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #888;
        border-bottom: 2.5px solid transparent;
        cursor: pointer;
        background: none;
        border-top: none;
        border-left: none;
        border-right: none;
        transition: color .18s, border-color .18s;
        white-space: nowrap;
    }
    .tab-btn i { font-size: .7rem; }

    /* ── Content area ── */
    .tab-content-area {
        padding: 1.5rem 1rem 2rem;
    }
    @media (min-width: 640px) { .tab-content-area { padding: 2rem 1.5rem 3rem; } }
    @media (min-width: 1024px) { .tab-content-area { padding: 2.5rem 2.5rem 4rem; } }

    /* ── Cards ── */
    .profile-card {
        background: #fff;
        border-radius: 1.1rem;
        border: 1px solid #ebebef;
        box-shadow: 0 1px 6px rgba(0,0,0,.04);
        overflow: hidden;
        margin-bottom: 1.25rem;
    }
    .profile-card-header {
        display: flex;
        align-items: center;
        gap: .65rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f0f0f4;
    }
    .profile-card-icon {
        width: 2rem;
        height: 2rem;
        border-radius: .55rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: .75rem;
    }
    .profile-card-title {
        font-size: .85rem;
        font-weight: 800;
        color: #1a1a2e;
        letter-spacing: -.01em;
    }

    /* ── Stats strip ── */
    .stats-strip {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        border-top: 1px solid #f0f0f4;
        border-bottom: 1px solid #f0f0f4;
        margin: .75rem 0 1rem;
    }
    .stat-cell {
        text-align: center;
        padding: .9rem .5rem;
        border-right: 1px solid #f0f0f4;
    }
    .stat-cell:last-child { border-right: none; }
    .stat-icon {
        width: 2rem;
        height: 2rem;
        border-radius: .5rem;
        background: #f5f5f8;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto .5rem;
        font-size: .75rem;
    }
    .stat-label {
        font-size: .58rem;
        color: #aaa;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-bottom: .15rem;
    }
    .stat-value {
        font-size: 1rem;
        font-weight: 900;
        color: #1a1a2e;
        letter-spacing: -.02em;
        line-height: 1.1;
    }
    .stat-sub {
        font-size: .6rem;
        font-weight: 500;
        color: #999;
    }

    /* ── Tag pills ── */
    .pill {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .35rem .85rem;
        border-radius: 100px;
        font-size: .72rem;
        font-weight: 700;
    }
    .pill-red   { background: #fff1f1; color: #ff0808; border: 1px solid #ffd0d0; }
    .pill-gray  { background: #f3f4f6; color: #555; }
    .pill-green { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }

    /* ── Detail rows ── */
    .detail-row {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f5f5f8;
    }
    .detail-row:last-child { border-bottom: none; }
    .detail-icon {
        width: 2.1rem;
        height: 2.1rem;
        border-radius: .55rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .75rem;
        flex-shrink: 0;
        margin-top: .05rem;
    }
    .detail-label {
        font-size: .6rem;
        color: #aaa;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-bottom: .2rem;
    }
    .detail-value {
        font-size: .85rem;
        font-weight: 700;
        color: #1a1a2e;
    }

    /* ── Website CTA card ── */
    .website-cta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.25rem;
    }
    .website-cta-btn {
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .55rem 1.1rem;
        background: #ff0808;
        color: #fff;
        font-size: .75rem;
        font-weight: 800;
        border-radius: .65rem;
        text-decoration: none;
        transition: opacity .18s, transform .15s;
    }
    .website-cta-btn:active { transform: scale(.96); }
    .website-cta-btn:hover  { opacity: .88; }

    /* ── Footer CTA ── */
    .footer-cta {
        background: linear-gradient(135deg, #ff0808 0%, #b50000 100%);
        border-radius: 1.1rem;
        padding: 1.75rem 1.5rem;
        text-align: center;
        margin-bottom: .5rem;
    }
    .footer-cta-icon {
        width: 2.8rem;
        height: 2.8rem;
        background: rgba(255,255,255,.18);
        border-radius: .75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.1rem;
        color: #fff;
    }
    .footer-cta h3 {
        color: #fff;
        font-weight: 900;
        font-size: 1rem;
        margin-bottom: .4rem;
        letter-spacing: -.01em;
    }
    .footer-cta p {
        color: rgba(255,255,255,.75);
        font-size: .78rem;
        line-height: 1.5;
        margin-bottom: 1.1rem;
    }
    .footer-cta-btn {
        display: inline-block;
        padding: .6rem 1.5rem;
        background: #fff;
        color: #ff0808;
        font-size: .78rem;
        font-weight: 800;
        border-radius: .65rem;
        text-decoration: none;
        transition: opacity .18s;
    }
    .footer-cta-btn:hover { opacity: .9; }

    /* ── Section divider ── */
    .section-divider {
        text-align: center;
        margin-bottom: 1.25rem;
    }
    .section-divider p {
        font-size: .75rem;
        color: #999;
        font-weight: 600;
        margin-bottom: .5rem;
    }
    .section-divider .bar-wrap {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .2rem;
    }
    .bar-red  { height: 3px; width: 2rem; background: #ff0808; border-radius: 100px; }
    .bar-amber{ height: 3px; width: .5rem; background: #f59e0b; border-radius: 100px; }

    /* ── Description text ── */
    .description-text {
        font-size: .88rem;
        color: #4a4a6a;
        line-height: 1.75;
        margin-bottom: 1.1rem;
    }
    .description-text strong { color: #ff0808; }
</style>

<div class="partner-page py-3 px-2 sm:px-4 lg:px-6">
<div class="max-w-7xl mx-auto">

    <div class="bg-white overflow-hidden shadow-lg">

        {{-- ══════════════════════════════════════════════
             HERO
        ══════════════════════════════════════════════ --}}
        <div class="hero-wrap">

            {{-- Back --}}
            <a href="javascript:history.back()" class="back-btn">
                <i class="fas fa-arrow-left" style="font-size:.78rem;"></i>
            </a>

            {{-- Badge --}}
            <div class="partner-badge">
                @if($partner->logo_url)
                    <img src="{{ $partner->logo_url }}" alt="{{ $partner->name }}" class="partner-badge-logo">
                @endif
                <div style="min-width:0;">
                    <div class="partner-badge-name">{{ $partner->name }}</div>
                    <div class="partner-badge-verified">
                        <i class="fas fa-check-circle"></i> Verified on Afrisellers
                    </div>
                </div>
            </div>

            {{-- Media --}}
            @if($introUrl && $isVideo)
                @if($isYoutube || Str::contains($introUrl, 'vimeo.com'))
                    <div class="hero-iframe-wrap">
                        <iframe src="{{ $embedUrl }}"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen></iframe>
                    </div>
                @else
                    <video class="hero-media" controls playsinline preload="metadata">
                        <source src="{{ $introUrl }}">
                    </video>
                @endif
            @else
                @php $heroImg = ($introUrl && !$isVideo) ? $introUrl : $fallbackImg; @endphp
                <div style="position:relative;">
                    <img src="{{ $heroImg }}"
                         alt="{{ $partner->name }}"
                         class="hero-media"
                         onerror="this.src='{{ $fallbackImg }}'">
                    <div class="hero-overlay"></div>
                </div>
            @endif

            {{-- Bottom label overlay (only for image heroes) --}}
            @if(!($introUrl && $isVideo))
            <div class="hero-bottom-label">
                <div>
                    <div class="hero-partner-name">{{ $partner->name }}</div>
                    @if($partner->partner_type)
                        <div style="margin-top:.4rem;">
                            <span class="hero-tag">{{ $partner->partner_type }}</span>
                        </div>
                    @endif
                </div>
                @if($partner->country)
                    <span style="color:rgba(255,255,255,.8);font-size:.72rem;font-weight:600;display:flex;align-items:center;gap:.3rem;flex-shrink:0;">
                        <i class="fas fa-map-marker-alt" style="font-size:.65rem;color:#ff0808;"></i>
                        {{ $partner->country }}
                    </span>
                @endif
            </div>
            @endif

        </div>{{-- /hero --}}

        {{-- ══════════════════════════════════════════════
             STICKY TAB NAV
        ══════════════════════════════════════════════ --}}
        <div class="tab-nav-outer">
            <div class="tab-nav-inner">
                <button class="tab-btn" data-tab="overview" onclick="switchTab('overview')">
                    <i class="fas fa-home"></i> Overview
                </button>
                <button class="tab-btn" data-tab="company-info" onclick="switchTab('company-info')">
                    <i class="fas fa-building"></i> Company
                </button>
                <button class="tab-btn" data-tab="branding" onclick="switchTab('branding')">
                    <i class="fas fa-palette"></i> Branding
                </button>
                <button class="tab-btn" data-tab="contact" onclick="switchTab('contact')">
                    <i class="fas fa-envelope"></i> Contact
                </button>
                <button class="tab-btn" data-tab="social" onclick="switchTab('social')">
                    <i class="fas fa-share-alt"></i> Social
                </button>
                <button class="tab-btn" data-tab="business-type" onclick="switchTab('business-type')">
                    <i class="fas fa-briefcase"></i> Business
                </button>
                <button class="tab-btn" data-tab="operations" onclick="switchTab('operations')">
                    <i class="fas fa-cogs"></i> Operations
                </button>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════
             TAB CONTENT WRAPPER
        ══════════════════════════════════════════════ --}}
        <div class="tab-content-area">

            {{-- ── TAB: Overview ── --}}
            <div id="tab-overview" class="tab-content">

                <div class="section-divider">
                    <p>About <strong style="color:#ff0808;">{{ $partner->name }}</strong></p>
                    <div class="bar-wrap">
                        <div class="bar-red"></div>
                        <div class="bar-amber"></div>
                    </div>
                </div>

                <div class="profile-card">
                    <div style="padding:1.25rem 1.25rem 0;">
                        @if($partner->description)
                            <p class="description-text">
                                <strong>{{ $partner->name }}</strong>
                                {{ ' ' . $partner->description }}
                            </p>
                        @endif
                    </div>

                    @if($partner->established || $partner->presence_countries || $partner->services)
                    <div class="stats-strip">
                        @if($partner->established)
                        <div class="stat-cell">
                            <div class="stat-icon"><i class="fas fa-calendar-alt" style="color:#ff0808;"></i></div>
                            <div class="stat-label">Est.</div>
                            <div class="stat-value">{{ $partner->established }}</div>
                        </div>
                        @endif
                        @if($partner->presence_countries)
                        <div class="stat-cell">
                            <div class="stat-icon"><i class="fas fa-globe" style="color:#0ea5e9;"></i></div>
                            <div class="stat-label">Presence</div>
                            <div class="stat-value">{{ $partner->presence_countries }}<span class="stat-sub">+</span></div>
                            <div class="stat-sub">Countries</div>
                        </div>
                        @endif
                        @if($partner->services)
                        <div class="stat-cell">
                            <div class="stat-icon"><i class="fas fa-th-list" style="color:#8b5cf6;"></i></div>
                            <div class="stat-label">Services</div>
                            <div class="stat-value" style="font-size:.7rem;line-height:1.3;color:#444;">{{ Str::limit($partner->services_string, 28) }}</div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <div style="padding:.75rem 1.25rem 1.25rem;display:flex;flex-wrap:wrap;gap:.5rem;">
                        @if($partner->partner_type)
                            <span class="pill pill-red"><i class="fas fa-tag" style="font-size:.6rem;"></i>{{ $partner->partner_type }}</span>
                        @endif
                        @if($partner->industry)
                            <span class="pill pill-gray"><i class="fas fa-industry" style="font-size:.6rem;"></i>{{ $partner->industry }}</span>
                        @endif
                        @if($partner->country)
                            <span class="pill pill-gray"><i class="fas fa-map-marker-alt" style="font-size:.6rem;"></i>{{ $partner->country }}</span>
                        @endif
                    </div>
                </div>

                @if($partner->website_url)
                <div class="profile-card">
                    <div class="website-cta">
                        <div style="display:flex;align-items:center;gap:.85rem;min-width:0;">
                            <div class="detail-icon" style="background:#fff1f1;color:#ff0808;flex-shrink:0;">
                                <i class="fas fa-globe"></i>
                            </div>
                            <div style="min-width:0;">
                                <div class="detail-label">Official Website</div>
                                <div class="detail-value" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                    {{ parse_url($partner->website_url, PHP_URL_HOST) ?? $partner->website_url }}
                                </div>
                            </div>
                        </div>
                        <a href="{{ $partner->website_url }}" target="_blank" rel="noopener noreferrer"
                           class="website-cta-btn">
                            Visit <i class="fas fa-external-link-alt" style="font-size:.6rem;"></i>
                        </a>
                    </div>
                </div>
                @endif

                <div class="footer-cta">
                    <div class="footer-cta-icon"><i class="fas fa-handshake"></i></div>
                    <h3>Join Our Network</h3>
                    <p>Connect with African suppliers and buyers across 50+ countries.</p>
                    <a href="{{ route('partner.request.form') }}" class="footer-cta-btn">
                        Become a Partner
                    </a>
                </div>

            </div>{{-- /tab-overview --}}

            {{-- ── TAB: Company Info ── --}}
            <div id="tab-company-info" class="tab-content hidden">

                <div class="profile-card">
                    <div class="profile-card-header">
                        <div class="profile-card-icon" style="background:#eef2ff;color:#4f46e5;">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <span class="profile-card-title">Company Details</span>
                    </div>

                    @if($partner->registration_number)
                    <div class="detail-row">
                        <div class="detail-icon" style="background:#eef2ff;color:#4f46e5;"><i class="fas fa-id-badge"></i></div>
                        <div>
                            <div class="detail-label">Registration Number</div>
                            <div class="detail-value">{{ $partner->registration_number }}</div>
                        </div>
                    </div>
                    @endif

                    @if($partner->partner_type)
                    <div class="detail-row">
                        <div class="detail-icon" style="background:#fff1f1;color:#ff0808;"><i class="fas fa-handshake"></i></div>
                        <div>
                            <div class="detail-label">Partnership Type</div>
                            <span class="pill pill-red">{{ $partner->partner_type }}</span>
                        </div>
                    </div>
                    @endif

                    @if($partner->established)
                    <div class="detail-row">
                        <div class="detail-icon" style="background:#fffbeb;color:#d97706;"><i class="fas fa-calendar-alt"></i></div>
                        <div>
                            <div class="detail-label">Year Established</div>
                            <div class="detail-value">
                                {{ $partner->established }}
                                <span style="font-size:.72rem;font-weight:500;color:#999;margin-left:.3rem;">({{ date('Y') - $partner->established }}+ years)</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($partner->country)
                    <div class="detail-row">
                        <div class="detail-icon" style="background:#f0fdfa;color:#0d9488;"><i class="fas fa-globe"></i></div>
                        <div>
                            <div class="detail-label">Country</div>
                            <div class="detail-value">{{ $partner->country }}</div>
                        </div>
                    </div>
                    @endif

                    @if($partner->physical_address)
                    <div class="detail-row">
                        <div class="detail-icon" style="background:#fff7ed;color:#ea580c;"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <div class="detail-label">Physical Address</div>
                            <div class="detail-value">{{ $partner->physical_address }}</div>
                        </div>
                    </div>
                    @endif

                    @if($partner->website_url)
                    <div class="detail-row">
                        <div class="detail-icon" style="background:#ecfeff;color:#0891b2;"><i class="fas fa-globe"></i></div>
                        <div style="min-width:0;">
                            <div class="detail-label">Website</div>
                            <a href="{{ $partner->website_url }}" target="_blank"
                               style="font-size:.85rem;font-weight:700;color:#ff0808;display:flex;align-items:center;gap:.3rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                {{ parse_url($partner->website_url, PHP_URL_HOST) }}
                                <i class="fas fa-external-link-alt" style="font-size:.6rem;"></i>
                            </a>
                        </div>
                    </div>
                    @endif
                </div>

            </div>{{-- /tab-company-info --}}

            {{-- ── TAB: Branding ── --}}
            <div id="tab-branding" class="tab-content hidden">
                @include('frontend.company.tabs.branding', ['profile' => $partner])
            </div>

            {{-- ── TAB: Contact ── --}}
            <div id="tab-contact" class="tab-content hidden">
                @include('frontend.company.tabs.contact', ['profile' => $partner])
            </div>

            {{-- ── TAB: Social ── --}}
            <div id="tab-social" class="tab-content hidden">
                @include('frontend.company.tabs.social', ['profile' => $partner])
            </div>

            {{-- ── TAB: Business Type ── --}}
            <div id="tab-business-type" class="tab-content hidden">
                @include('frontend.company.tabs.business', ['profile' => $partner])
            </div>

            {{-- ── TAB: Operations ── --}}
            <div id="tab-operations" class="tab-content hidden">
                @include('frontend.company.tabs.operations', ['profile' => $partner])
            </div>

        </div>{{-- /tab-content-area --}}

    </div>{{-- /white card --}}

</div>{{-- /max-w-5xl --}}
</div>{{-- /partner-page --}}

<script>
const TAB_STYLES = {
    'overview':      { border: '#ff0808', color: '#ff0808' },
    'company-info':  { border: '#4f46e5', color: '#4f46e5' },
    'branding':      { border: '#9333ea', color: '#9333ea' },
    'contact':       { border: '#16a34a', color: '#16a34a' },
    'social':        { border: '#0ea5e9', color: '#0ea5e9' },
    'business-type': { border: '#ea580c', color: '#ea580c' },
    'operations':    { border: '#374151', color: '#374151' },
};

function switchTab(tabId) {
    localStorage.setItem('partner_profile_tab', tabId);

    // Hide all
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));

    // Show target
    const target = document.getElementById('tab-' + tabId);
    if (target) target.classList.remove('hidden');

    // Reset all buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.style.color = '#888';
        btn.style.borderBottomColor = 'transparent';
    });

    // Activate clicked button
    const activeBtn = document.querySelector(`[data-tab="${tabId}"]`);
    if (activeBtn && TAB_STYLES[tabId]) {
        activeBtn.style.color = TAB_STYLES[tabId].color;
        activeBtn.style.borderBottomColor = TAB_STYLES[tabId].border;

        // Scroll the nav so this tab is visible
        activeBtn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const saved = localStorage.getItem('partner_profile_tab') || 'overview';
    switchTab(saved);
});
</script>

@endsection

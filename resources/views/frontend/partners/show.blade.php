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
        // YouTube
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
        // Vimeo
        elseif (Str::contains($introUrl, 'vimeo.com')) {
            $isVideo   = true;
            $isYoutube = false;
            $videoId   = Str::afterLast($introUrl, '/');
            $embedUrl  = 'https://player.vimeo.com/video/' . $videoId;
        }
        // Direct video file
        elseif (Str::contains(strtolower($introUrl), ['.mp4', '.webm', '.ogg', '.mov'])) {
            $isVideo   = true;
            $isYoutube = false;
        }
        // Otherwise treat as image
    }
@endphp

{{-- ============================================================
     PAGE WRAPPER — constrained width, centered
============================================================ --}}
<div class="min-h-screen bg-gray-100 py-4 px-3 sm:px-6">
<div class="max-w-7xl mx-auto">

    <div class="min-h-screen bg-gray-50 rounded-md overflow-hidden shadow-xl">

        {{-- ============================================================
             HERO — Video or Image at top
        ============================================================ --}}
        <div class="relative w-full bg-black" style="overflow: hidden;">

            {{-- Back button — top LEFT --}}
            <a href="javascript:history.back()"
               class="absolute top-4 left-4 z-20 w-9 h-9 bg-black/40 backdrop-blur-sm rounded-full flex items-center justify-center text-white hover:bg-black/60 transition-colors">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>

            {{-- Partner badge — top RIGHT, always visible over hero --}}
            <div class="absolute top-4 right-4 z-20 flex items-center gap-2 bg-black/50 backdrop-blur-sm rounded-2xl px-2.5 py-2" style="max-width: calc(100% - 4rem);">
                @if($partner->logo_url)
                    <img src="{{ $partner->logo_url }}" alt="{{ $partner->name }}"
                         class="w-8 h-8 sm:w-9 sm:h-9 object-contain bg-white rounded-lg p-1 flex-shrink-0">
                @endif
                <div class="min-w-0">
                    <p class="text-white font-bold text-xs sm:text-sm leading-tight truncate">{{ $partner->name }}</p>
                    <p class="text-green-400 text-[10px] font-semibold flex items-center gap-1 mt-0.5 whitespace-nowrap">
                        <i class="fas fa-check-circle text-[9px]"></i> Verified on Afrisellers
                    </p>
                </div>
            </div>

            @if($introUrl && $isVideo)

                @if($isYoutube || Str::contains($introUrl, 'vimeo.com'))
                    {{-- Embed iframe video --}}
                    <div class="relative w-full" style="height: 280px;">
                        <iframe src="{{ $embedUrl }}"
                                class="absolute inset-0 w-full h-full"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen></iframe>
                    </div>

                    {{-- Subtle bottom fade --}}
                    <div class="absolute bottom-0 left-0 right-0 z-10 bg-gradient-to-t from-black/50 to-transparent px-4 py-3 pointer-events-none">
                        <p class="text-white/50 text-[10px] flex items-center gap-1">
                            <i class="fas fa-play text-[9px]"></i> Video Header
                        </p>
                    </div>

                @else
                    {{-- Direct video file --}}
                    <video class="w-full object-cover" style="max-height: 300px;"
                           controls playsinline preload="metadata">
                        <source src="{{ $introUrl }}">
                    </video>
                @endif

            @else
                {{-- Image (intro image or fallback) --}}
                @php $heroImg = ($introUrl && !$isVideo) ? $introUrl : $fallbackImg; @endphp
                <div class="relative w-full" style="height: 280px;">
                    <img src="{{ $heroImg }}"
                         alt="{{ $partner->name }}"
                         class="absolute inset-0 w-full h-full object-cover"
                         onerror="this.src='{{ $fallbackImg }}'">
                    {{-- Dark overlay --}}
                    <div class="absolute inset-0 bg-black/35"></div>
                </div>
            @endif

        </div>{{-- /hero --}}

        {{-- ============================================================
             ABOUT SECTION
        ============================================================ --}}
        <div class="px-4 mt-6">

            {{-- Section label --}}
            <div class="text-center mb-4">
                <p class="text-sm text-gray-500 font-medium">
                    Scroll &rarr; <span class="text-[#ff0808] font-bold">About {{ $partner->name }}</span>
                </p>
                <div class="flex items-center justify-center gap-1 mt-1.5">
                    <div class="h-0.5 w-8 bg-[#ff0808] rounded-full"></div>
                    <div class="h-0.5 w-2 bg-amber-400 rounded-full"></div>
                </div>
            </div>

            {{-- About card --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-5">

                {{-- Description --}}
                @if($partner->description)
                    <p class="text-sm text-gray-700 leading-relaxed mb-5">
                        <span class="text-[#ff0808] font-bold">{{ $partner->name }}</span>
                        {{ ' ' . $partner->description }}
                    </p>
                @endif

                {{-- Stats row --}}
                @if($partner->established || $partner->presence_countries || $partner->services)
                <div class="grid grid-cols-3 gap-3 py-4 border-t border-b border-gray-100 mb-5">

                    @if($partner->established)
                    <div class="text-center">
                        <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center mx-auto mb-1.5">
                            <i class="fas fa-calendar-alt text-[#ff0808] text-sm"></i>
                        </div>
                        <p class="text-[10px] text-gray-400 font-semibold uppercase mb-0.5">Established</p>
                        <p class="text-base font-bold text-gray-900">{{ $partner->established }}</p>
                    </div>
                    @endif

                    @if($partner->presence_countries)
                    <div class="text-center">
                        <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center mx-auto mb-1.5">
                            <i class="fas fa-globe text-[#ff0808] text-sm"></i>
                        </div>
                        <p class="text-[10px] text-gray-400 font-semibold uppercase mb-0.5">Presence</p>
                        <p class="text-base font-bold text-gray-900">{{ $partner->presence_countries }}+
                            <span class="text-xs font-normal text-gray-500">Countries</span>
                        </p>
                    </div>
                    @endif

                    @if($partner->services)
                    <div class="text-center">
                        <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center mx-auto mb-1.5">
                            <i class="fas fa-th-list text-[#ff0808] text-sm"></i>
                        </div>
                        <p class="text-[10px] text-gray-400 font-semibold uppercase mb-0.5">Services</p>
                        <p class="text-xs font-semibold text-gray-800 leading-tight">
                            {{ Str::limit($partner->services_string, 30) }}
                        </p>
                    </div>
                    @endif

                </div>
                @endif

                {{-- Info chips --}}
                <div class="flex flex-wrap gap-2">
                    @if($partner->partner_type)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-[#ff0808] text-xs font-semibold rounded-full border border-red-100">
                            <i class="fas fa-tag text-[10px]"></i>{{ $partner->partner_type }}
                        </span>
                    @endif
                    @if($partner->industry)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full">
                            <i class="fas fa-industry text-[10px]"></i>{{ $partner->industry }}
                        </span>
                    @endif
                    @if($partner->country)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full">
                            <i class="fas fa-map-marker-alt text-[10px]"></i>{{ $partner->country }}
                        </span>
                    @endif
                </div>

            </div>

        </div>

        {{-- ============================================================
             VISIT WEBSITE — clean inline row
        ============================================================ --}}
        @if($partner->website_url)
        <div class="px-4 pb-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 bg-red-50 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-globe text-[#ff0808] text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-gray-400 font-medium">Official Website</p>
                        <p class="text-sm font-semibold text-gray-800 truncate">
                            {{ parse_url($partner->website_url, PHP_URL_HOST) ?? $partner->website_url }}
                        </p>
                    </div>
                </div>
                <a href="{{ $partner->website_url }}" target="_blank" rel="noopener noreferrer"
                   class="flex-shrink-0 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-white text-xs font-bold transition-all active:scale-95"
                   style="background: #ff0808;">
                    Visit <i class="fas fa-external-link-alt text-[10px]"></i>
                </a>
            </div>
        </div>
        @endif

        {{-- ============================================================
             BECOME A PARTNER FOOTER CTA
        ============================================================ --}}
        <div class="mx-4 mb-6 rounded-2xl overflow-hidden"
             style="background: linear-gradient(135deg, #ff0808 0%, #cc0000 100%);">
            <div class="px-5 py-5 text-white text-center">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-handshake text-white text-lg"></i>
                </div>
                <h3 class="font-bold text-sm mb-1">Join Our Network</h3>
                <p class="text-red-100 text-xs mb-3 leading-relaxed">
                    Connect with African suppliers and buyers across 50+ countries.
                </p>
                <a href="{{ route('partner.request.form') }}"
                   class="inline-block px-5 py-2 bg-white text-[#ff0808] text-xs font-bold rounded-xl hover:bg-red-50 transition-colors">
                    Become a Partner
                </a>
            </div>
        </div>

    </div>{{-- /inner card --}}
</div>{{-- /max-w --}}
</div>{{-- /page wrapper --}}

@endsection

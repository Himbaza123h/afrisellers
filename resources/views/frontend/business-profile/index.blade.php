@extends('layouts.app')

@section('title', $businessProfile->business_name . ' - Business Profile')

@section('content')
<div class="bg-gray-50 to-gray-100 min-h-screen py-6">
    <div class="container mx-auto px-4 max-w-7xl">

        <!-- Business Profile Header Card -->
        <div class="bg-white rounded border border-gray-200 shadow-sm overflow-hidden mb-6">
            <!-- Cover Image / Banner -->
            <div class="h-40 bg-cover bg-center relative" style="background-image: url('{{ $businessProfile->cover_image ?? 'https://images.pexels.com/photos/346529/pexels-photo-346529.jpeg' }}');">
                <div class="absolute inset-0 bg-gradient-to-r from-red-700 to-[#ff0808] opacity-70"></div>

                <!-- Share Buttons Overlay -->
                <div class="absolute top-4 right-4 flex gap-2">
                    <button onclick="shareOnFacebook()" class="w-9 h-9 bg-white/90 hover:bg-white rounded-full flex items-center justify-center shadow-lg transition-all hover:scale-110" title="Share on Facebook">
                        <svg class="w-5 h-5 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </button>
                    <button onclick="shareOnTwitter()" class="w-9 h-9 bg-white/90 hover:bg-white rounded-full flex items-center justify-center shadow-lg transition-all hover:scale-110" title="Share on X (Twitter)">
                        <svg class="w-5 h-5 text-black" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </button>
                    <button onclick="shareOnLinkedIn()" class="w-9 h-9 bg-white/90 hover:bg-white rounded-full flex items-center justify-center shadow-lg transition-all hover:scale-110" title="Share on LinkedIn">
                        <svg class="w-5 h-5 text-[#0A66C2]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Profile Info -->
            <div class="px-6 py-5">
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Business Logo -->
                    <div class="-mt-16 flex-shrink-0 relative z-10">
                        <div class="w-24 h-24 rounded border-4 border-white shadow-lg flex items-center justify-center overflow-hidden bg-white">
                            @if($businessProfile->logo)
                                <img src="{{ $businessProfile->logo }}" alt="{{ $businessProfile->business_name }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-12 h-12 text-[#ff0808]" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                                </svg>
                            @endif
                        </div>
                    </div>

                    <!-- Business Details -->
                    <div class="flex-1">
                        <div class="flex items-start justify-between flex-wrap gap-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <h1 class="text-2xl font-bold text-gray-900">{{ $businessProfile->business_name }}</h1>
                                    @if($businessProfile->is_admin_verified)
                                    <span class="inline-flex items-center gap-1 bg-green-600 text-white text-xs font-semibold px-2.5 py-1 rounded">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Verified
                                    </span>
                                    @endif
                                </div>

                                <!-- Location -->
                                <div class="flex items-center gap-2 text-gray-600 text-sm mb-3">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span class="font-medium">{{ $businessProfile->city }}, {{ $businessProfile->country->name ?? '' }}</span>
                                </div>

                                <!-- Quick Stats -->
                                <div class="flex flex-wrap gap-4 text-sm mb-3">
                                    @if($businessProfile->year_established)
                                    <div class="flex items-center gap-1.5 text-gray-600">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span>Est. {{ $businessProfile->year_established }}</span>
                                    </div>
                                    @endif
                                    @if($businessProfile->business_type)
                                    <div class="flex items-center gap-1.5 text-gray-600">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                        <span>{{ $businessProfile->business_type }}</span>
                                    </div>
                                    @endif
                                    @if($businessProfile->response_time)
                                    <div class="flex items-center gap-1.5 text-gray-600">
                                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>Responds in {{ $businessProfile->response_time }}h</span>
                                    </div>
                                    @endif
                                </div>

                                <!-- Contact Info Bar -->
                                <div class="bg-gray-50 rounded-lg px-4 py-3 border border-gray-200">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-xs">
                                        @if($businessProfile->phone)
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-[#ff0808]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                            <a href="tel:{{ $businessProfile->phone_code }}{{ $businessProfile->phone }}" class="text-gray-700 hover:text-[#ff0808] font-medium">
                                                {{ $businessProfile->phone_code }} {{ $businessProfile->phone }}
                                            </a>
                                        </div>
                                        @endif

                                        @if($businessProfile->whatsapp_number)
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                            </svg>
                                            <a href="https://wa.me/{{ $businessProfile->phone_code }}{{ $businessProfile->whatsapp_number }}" target="_blank" class="text-gray-700 hover:text-green-600 font-medium">
                                                WhatsApp
                                            </a>
                                        </div>
                                        @endif

                                        @if($businessProfile->business_email)
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-[#ff0808]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                            <a href="mailto:{{ $businessProfile->business_email }}" class="text-gray-700 hover:text-[#ff0808] font-medium truncate">
                                                {{ $businessProfile->business_email }}
                                            </a>
                                        </div>
                                        @endif

                                        @if($businessProfile->website)
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-[#ff0808]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                            </svg>
                                            <a href="{{ $businessProfile->website }}" target="_blank" class="text-gray-700 hover:text-[#ff0808] font-medium truncate">
                                                Visit Website
                                            </a>
                                        </div>
                                        @endif

                                        @if($businessProfile->operating_hours)
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-[#ff0808]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="text-gray-700 font-medium truncate">{{ $businessProfile->operating_hours }}</span>
                                        </div>
                                        @endif

                                        @if($businessProfile->languages_spoken)
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-[#ff0808]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                                            </svg>
                                            <span class="text-gray-700 font-medium truncate">{{ $businessProfile->languages_spoken }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('request-quote.show', ['businessProfileId' => $businessProfile->id]) }}"
                                   class="inline-flex items-center gap-2 bg-[#ff0808] hover:bg-[#dd0606] text-white text-sm font-semibold py-2 px-4 rounded transition-all duration-200 shadow-sm hover:shadow-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    Request Info
                                </a>
                                <button class="inline-flex items-center gap-2 border border-gray-300 hover:border-[#ff0808] hover:text-[#ff0808] text-gray-700 text-sm font-semibold py-2 px-4 rounded transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                    </svg>
                                    Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="bg-white rounded border border-gray-200 shadow-sm overflow-hidden mb-6">
            <div class="flex border-b border-gray-200">
                <button onclick="switchTab('overview')"
                        id="tab-overview"
                        class="tab-button flex-1 px-6 py-4 text-sm font-semibold text-gray-600 hover:text-[#ff0808] hover:bg-gray-50 transition-all duration-200 border-b-2 border-transparent">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Overview</span>
                    </div>
                </button>
                <button onclick="switchTab('products')"
                        id="tab-products"
                        class="tab-button flex-1 px-6 py-4 text-sm font-semibold text-gray-600 hover:text-[#ff0808] hover:bg-gray-50 transition-all duration-200 border-b-2 border-transparent">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <span>Products</span>
                        <span class="ml-1 text-xs bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full">{{ $businessProfile->user->products->count() }}</span>
                    </div>
                </button>
                <button onclick="switchTab('videos')"
                        id="tab-videos"
                        class="tab-button flex-1 px-6 py-4 text-sm font-semibold text-gray-600 hover:text-[#ff0808] hover:bg-gray-50 transition-all duration-200 border-b-2 border-transparent">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Videos</span>
                    </div>
                </button>
                <button onclick="switchTab('articles')"
                        id="tab-articles"
                        class="tab-button flex-1 px-6 py-4 text-sm font-semibold text-gray-600 hover:text-[#ff0808] hover:bg-gray-50 transition-all duration-200 border-b-2 border-transparent">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                        <span>Articles</span>
                    </div>
                </button>
</div>
        </div>

        {{-- ── Company Profile Ad Banner ── --}}
        @php
        $realCompanyAds = \App\Models\Advertisement::where('position', 'company_profile')
            ->where('status', 'running')
            ->where('end_date', '>=', now())
            ->orderBy('approved_at', 'desc')
            ->get();

        $companyAdPairs = $realCompanyAds->count() > 0
            ? $realCompanyAds->map(fn($ad) => [
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
            ])->chunk(2)->toArray()
            : [
                [
                    [
                        'real_id' => null, 'type' => 'image',
                        'media'   => 'https://images.pexels.com/photos/3184465/pexels-photo-3184465.jpeg?auto=compress&cs=tinysrgb&w=900&h=220&fit=crop',
                        'headline'=> 'Grow Your Business Across Africa',
                        'sub'     => 'Reach 1M+ verified buyers on Afrisellers',
                        'cta_url' => '#', 'badge' => 'SPONSORED',
                        'overlay' => 'rgba(13,31,60,0.72)', 'accent' => '#ff0808',
                    ],
                    [
                        'real_id' => null, 'type' => 'image',
                        'media'   => 'https://images.pexels.com/photos/1640777/pexels-photo-1640777.jpeg?auto=compress&cs=tinysrgb&w=900&h=220&fit=crop',
                        'headline'=> 'Fresh Produce. Pan-African Logistics.',
                        'sub'     => 'Farm to market — faster than ever.',
                        'cta_url' => '#', 'badge' => 'FEATURED',
                        'overlay' => 'rgba(20,83,45,0.78)', 'accent' => '#86efac',
                    ],
                ],
                [
                    [
                        'real_id' => null, 'type' => 'text',
                        'bg'      => 'linear-gradient(135deg,#ff0808 0%,#c80000 100%)',
                        'headline'=> '🔥 MEGA SALE — UP TO 70% OFF',
                        'sub'     => 'Thousands of verified products. Limited time only.',
                        'cta_url' => '#', 'badge' => 'LIMITED TIME',
                        'overlay' => 'transparent', 'accent' => '#fff', 'pattern' => true,
                    ],
                    [
                        'real_id' => null, 'type' => 'image',
                        'media'   => 'https://images.pexels.com/photos/2599244/pexels-photo-2599244.jpeg?auto=compress&cs=tinysrgb&w=900&h=220&fit=crop',
                        'headline'=> 'Electronics at Unbeatable Prices',
                        'sub'     => 'Verified suppliers. Bulk discounts available.',
                        'cta_url' => '#', 'badge' => 'SALE',
                        'overlay' => 'rgba(0,48,73,0.75)', 'accent' => '#34d399',
                    ],
                ],
            ];

        $cpAdTotal = count($companyAdPairs);
        $cpAdId    = 'cp-ad-' . $businessProfile->id;
        @endphp

        @if($cpAdTotal > 0)
        <div class="mb-6 relative overflow-hidden bg-[#0a0a0a]" id="{{ $cpAdId }}" style="height:112px; border-radius:3px;">

            @foreach($companyAdPairs as $pairIndex => $pair)
            <div class="cp-ad-pair-{{ $cpAdId }} absolute inset-0 flex transition-opacity duration-1000 ease-in-out"
                 style="gap:2px; opacity:{{ $pairIndex===0?'1':'0' }}; z-index:{{ $pairIndex===0?'2':'1' }};">

                @foreach($pair as $adIndex => $ad)
                <a href="{{ $ad['cta_url'] ?? '#' }}"
                   class="relative flex-1 overflow-hidden group cp-ad-cell"
                   data-real-id="{{ $ad['real_id'] ?? '' }}"
                   onclick="trackCpAdClick(this)">

                    @if($ad['type'] === 'image')
                        <img src="{{ $ad['media'] }}" alt="{{ $ad['headline'] }}"
                             class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                             loading="lazy">
                    @elseif($ad['type'] === 'gif')
                        <img src="{{ $ad['media'] }}" alt="{{ $ad['headline'] }}"
                             class="absolute inset-0 w-full h-full object-cover">
                    @elseif($ad['type'] === 'video')
                        <video class="absolute inset-0 w-full h-full object-cover" autoplay muted loop playsinline preload="none">
                            <source src="{{ $ad['media'] }}" type="video/mp4">
                        </video>
                    @elseif($ad['type'] === 'text')
                        <div class="absolute inset-0" style="background:{{ $ad['bg'] ?? '#ff0808' }};"></div>
                        @if(!empty($ad['pattern']))
                        <div class="absolute inset-0 opacity-10"
                             style="background-image:radial-gradient(circle,#fff 1px,transparent 1px);background-size:18px 18px;"></div>
                        @endif
                    @endif

                    @if(isset($ad['overlay']) && $ad['overlay'] !== 'transparent')
                    <div class="absolute inset-0"
                         style="background:linear-gradient(to right,{{ $ad['overlay'] }} 0%,{{ $ad['overlay'] }} 45%,rgba(0,0,0,0.05) 100%);"></div>
                    @endif

                    {{-- Shimmer --}}
                    <div class="cp-ad-shimmer absolute inset-0 pointer-events-none"></div>

                    <div class="relative z-10 h-full flex items-center px-5 md:px-7">
                        <div class="min-w-0">
                            @if(!empty($ad['badge']))
                            <span class="inline-block mb-1 px-2 py-0.5 text-[8px] font-black tracking-widest uppercase rounded-sm leading-none"
                                  style="background:{{ $ad['accent'] ?? '#ff0808' }};
                                         color:{{ in_array($ad['accent']??'',['#fff','#86efac','#6ee7b7','#fbbf24','#facc15','#fcd34d','#f9a8d4','#34d399'])?'#0a0a0a':'#fff' }};">
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

                    <span class="absolute top-1 right-2 z-20 text-white/20 text-[8px] font-bold tracking-widest pointer-events-none select-none">AD</span>
                </a>
                @endforeach

                @if(count($pair) === 1)
                <a href="#" class="relative flex-1 overflow-hidden group bg-[#111]" style="display:block;text-decoration:none;">
                    <div class="absolute inset-0 opacity-5"
                         style="background-image:repeating-linear-gradient(45deg,#fff 0,#fff 1px,transparent 0,transparent 50%);background-size:10px 10px;"></div>
                    <div class="relative z-10 h-full flex items-center justify-center">
                        <p class="text-white/35 text-[10px] font-black uppercase tracking-widest">Advertise Here · 800 × 112px</p>
                    </div>
                </a>
                @endif

            </div>
            @endforeach

        </div>

        <style>
        .cp-ad-cell { -webkit-tap-highlight-color:transparent; text-decoration:none !important; display:block; }
        .cp-ad-shimmer {
            background: linear-gradient(105deg, transparent 35%, rgba(255,255,255,.06) 50%, transparent 65%);
            transform: translateX(-110%);
        }
        .cp-ad-cell:hover .cp-ad-shimmer { transform: translateX(110%); transition: transform .65s ease; }
        </style>

        <script>
        (function(){
            const ID       = '{{ $cpAdId }}';
            const TOTAL    = {{ $cpAdTotal }};
            const INTERVAL = 5500;
            let cur = 0, timer = null;

            function move(){
                document.querySelectorAll('.cp-ad-pair-' + ID).forEach((el,i) => {
                    el.style.opacity = i===cur ? '1' : '0';
                    el.style.zIndex  = i===cur ? '2' : '1';
                });
            }

            function reset(){
                clearInterval(timer);
                if(TOTAL > 1) timer = setInterval(() => { cur=(cur+1)%TOTAL; move(); }, INTERVAL);
            }
            reset();

            const banner = document.getElementById(ID);
            if(banner){
                banner.addEventListener('mouseenter', () => clearInterval(timer));
                banner.addEventListener('mouseleave', reset);
            }

            window.trackCpAdClick = function(el){
                const rid = el.getAttribute('data-real-id');
                if(!rid) return;
                fetch('/ads/track-click', {
                    method:'POST',
                    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content||''},
                    body: JSON.stringify({id: rid})
                }).catch(()=>{});
            };
        })();
        </script>
        @endif

        <!-- Tab Content -->
        <div class="relative">
            <!-- Overview Tab -->
            <div id="content-overview" class="tab-content">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- About Us - 2 columns -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded border border-gray-200 shadow-sm p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#ff0808]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                About Us
                            </h3>
                            <div class="bg-gray-50 rounded p-4">
                                <p class="text-gray-700 text-sm leading-relaxed">
                                    {{ $businessProfile->description ?? 'Leading manufacturer and exporter of premium agricultural products in Rwanda with over 15 years of experience.' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Company Info - 1 column -->
                    <div>
                        <div class="bg-white rounded border border-gray-200 shadow-sm p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#ff0808]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Company Info
                            </h3>
                            <div class="space-y-4">
                                @if($businessProfile->year_established)
                                <div>
                                    <span class="text-xs text-gray-500 block mb-1">Year Established</span>
                                    <p class="text-sm font-semibold text-gray-900">{{ $businessProfile->year_established }}</p>
                                </div>
                                @endif

                                @if($businessProfile->business_type)
                                <div class="pt-4 border-t border-gray-200">
                                    <span class="text-xs text-gray-500 block mb-1">Business Type</span>
                                    <p class="text-sm font-semibold text-gray-900">{{ $businessProfile->business_type }}</p>
                                </div>
                                @endif

                                @if($businessProfile->certifications)
                                <div class="pt-4 border-t border-gray-200">
                                    <span class="text-xs text-gray-500 block mb-1">Certifications</span>
                                    <p class="text-sm font-semibold text-gray-900">{{ $businessProfile->certifications }}</p>
                                </div>
                                @endif

                                <div class="pt-4 border-t border-gray-200">
                                    <span class="text-xs text-gray-500 block mb-1">Location</span>
                                    <p class="text-sm font-semibold text-gray-900">{{ $businessProfile->city }}, {{ $businessProfile->country->name }}</p>
                                </div>

                                @if($businessProfile->contact_person_name)
                                <div class="pt-4 border-t border-gray-200">
                                    <span class="text-xs text-gray-500 block mb-1">Contact Person</span>
                                    <p class="text-sm font-semibold text-gray-900">{{ $businessProfile->contact_person_name }}</p>
                                    @if($businessProfile->contact_person_position)
                                        <p class="text-xs text-gray-600 mt-0.5">{{ $businessProfile->contact_person_position }}</p>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Tab -->
            <div id="content-products" class="tab-content hidden">
                <div class="bg-white rounded border border-gray-200 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#ff0808]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Our Products
                        </h2>
                        <span class="text-sm text-gray-500">{{ $businessProfile->user->products->count() }} products</span>
                    </div>

                    @if($businessProfile->user->products->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        @foreach($businessProfile->user->products as $product)
                            @php
                                $image = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                                $price = $product->prices->first();
                            @endphp

                            <a href="{{ route('products.show', $product->slug) }}"
                               class="group bg-white border border-gray-200 rounded overflow-hidden hover:shadow-lg hover:border-[#ff0808] transition-all duration-300 hover:-translate-y-1">
                                <!-- Product Image -->
                                <div class="relative h-36 overflow-hidden bg-gray-50">
                                    @if($image)
                                        <img src="{{ $image->image_url }}"
                                             alt="{{ $product->name }}"
                                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                             loading="lazy">
                                    @else
                                        <div class="flex justify-center items-center w-full h-full bg-gray-100 to-gray-200">
                                            <span class="text-4xl">📦</span>
                                        </div>
                                    @endif

                                    @if($product->is_admin_verified)
                                    <span class="absolute top-2 right-2 bg-green-600 text-white text-xs font-semibold px-2 py-0.5 rounded flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </span>
                                    @endif
                                </div>

                                <!-- Product Info -->
                                <div class="p-3">
                                    <h3 class="text-xs font-semibold text-gray-900 mb-2 line-clamp-2 group-hover:text-[#ff0808] transition-colors min-h-[2.5rem]">
                                        {{ $product->name }}
                                    </h3>

                                    @if($price)
                                    <p class="text-sm font-bold text-[#ff0808] mb-1">
                                        {{ number_format($price->price, 0) }} {{ $price->currency }}
                                    </p>
                                    @endif

                                    <p class="text-xs text-gray-600">
                                        MOQ: {{ number_format($product->min_order_quantity) }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-16">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">No Products Yet</h3>
                        <p class="text-gray-600 text-sm">This business hasn't added any products.</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Videos Tab -->
            <div id="content-videos" class="tab-content hidden">
                <div class="bg-white rounded border border-gray-200 shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#ff0808]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Company Video
                    </h2>

                    <div class="relative aspect-video bg-gray-900 rounded overflow-hidden shadow-lg mb-6">
                        <video class="w-full h-full object-cover" controls poster="https://images.pexels.com/photos/6169027/pexels-photo-6169027.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1">
                            <source src="https://cdn.coverr.co/videos/coverr-a-farmer-walking-in-a-field-9330/1080p.mp4" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded p-4">
                        <p class="text-sm text-blue-800">
                            <strong>About this video:</strong> Learn more about our company, facilities, and commitment to quality.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Articles Tab -->
            <div id="content-articles" class="tab-content hidden">
                <div class="bg-white rounded border border-gray-200 shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#ff0808]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                        Articles & Industry News
                    </h2>

                    @if($businessProfile->user->articles->where('status', 'published')->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($businessProfile->user->articles->where('status', 'published')->take(6) as $article)
                            <article class="border border-gray-200 rounded overflow-hidden hover:shadow-lg transition-all duration-300 hover:border-[#ff0808] group">
                                <div class="relative h-36 overflow-hidden">
                                    <a href="{{ route('business-profile.products.singleArticle', ['businessProfileId' => $businessProfile->id, 'articleSlug' => $article->slug]) }}">
                                        <img src="{{ $article->featured_image ?? 'https://images.pexels.com/photos/1072824/pexels-photo-1072824.jpeg?auto=compress&cs=tinysrgb&w=400' }}"
                                             alt="{{ $article->title }}"
                                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    </a>
                                </div>
                                <div class="p-3">
                                    <h3 class="text-sm font-bold text-gray-900 mb-1.5 group-hover:text-[#ff0808] transition-colors line-clamp-2">
                                        {{ $article->title }}
                                    </h3>
                                    <p class="text-xs text-gray-600 mb-2 line-clamp-3">
                                        {{ $article->excerpt }}
                                    </p>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-gray-500 text-[10px]">📅 {{ $article->formatted_published_date }}</span>
                                        <span class="text-[#ff0808] text-xs font-semibold group-hover:underline">
                                            <a href="{{ route('business-profile.products.singleArticle', ['businessProfileId' => $businessProfile->id, 'articleSlug' => $article->slug]) }}">
                                                Read More →
                                            </a>
                                        </span>
                                    </div>
                                </div>
                            </article>
                            @endforeach
                        </div>

                        @if($businessProfile->user->articles->where('status', 'published')->count() > 6)
                        <div class="mt-5 text-center">
                            <a href="#"
                               class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#ff0808] hover:bg-[#dd0606] text-white text-sm font-semibold rounded transition-all">
                                View All Articles
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </a>
                        </div>
                        @endif
                    @else
                        <div class="text-center py-12">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-3">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                </svg>
                            </div>
                            <h3 class="text-base font-bold text-gray-900 mb-1.5">No Articles Yet</h3>
                            <p class="text-gray-600 text-xs">This business hasn't published any articles.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    /* Tab Styling */
    .tab-button {
        position: relative;
    }

    .tab-button.active {
        color: #ff0808;
        border-bottom-color: #ff0808;
        background-color: #fff5f5;
    }

    /* Tab Content Animations */
    .tab-content {
        animation: fadeIn 0.4s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .tab-content.hidden {
        display: none;
    }

    /* Smooth transitions */
    * {
        transition-property: color, background-color, border-color, transform, box-shadow;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }
</style>

<script>
    // Tab Switching Function
    function switchTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Remove active class from all tabs
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active');
        });

        // Show selected tab content
        document.getElementById('content-' + tabName).classList.remove('hidden');

        // Add active class to selected tab
        document.getElementById('tab-' + tabName).classList.add('active');

        // Save to localStorage
        localStorage.setItem('activeBusinessTab', tabName);
    }

    // Share Functions
    function shareOnFacebook() {
        const url = encodeURIComponent(window.location.href);
        const title = encodeURIComponent('{{ $businessProfile->business_name }}');
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, 'facebook-share', 'width=580,height=400');
    }

    function shareOnTwitter() {
        const url = encodeURIComponent(window.location.href);
        const text = encodeURIComponent('Check out {{ $businessProfile->business_name }} on AfriSellers');
        window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, 'twitter-share', 'width=580,height=400');
    }

    function shareOnLinkedIn() {
        const url = encodeURIComponent(window.location.href);
        window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, 'linkedin-share', 'width=580,height=400');
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Restore last active tab or default to overview
        const savedTab = localStorage.getItem('activeBusinessTab') || 'overview';
        switchTab(savedTab);
    });
</script>
@endsection

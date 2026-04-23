@php
    // Get featured businesses with one product each for hero slider (3 businesses)
    $featuredBusinesses = App\Models\User::query()
        ->whereHas('vendor.addonUsers', function($q) {
            $q->whereNotNull('supplier_id')
              ->whereNotNull('paid_at')
              ->where(function ($q) {
                  $q->whereNull('ended_at')
                    ->orWhere('ended_at', '>', now());
              });
        })
        ->with(['products' => function($query) {
            $query->where('status', 'active')
                  ->where('is_admin_verified', true)
                  ->with('images')
                  ->latest()
                  ->limit(1);
        }, 'vendor.businessProfile'])
        ->whereHas('products', function($query) {
            $query->where('status', 'active')
                  ->where('is_admin_verified', true);
        })
        ->whereHas('vendor.businessProfile')
        ->limit(3)
        ->get()
        ->map(function($user) {
            $product = $user->products->first();
            $image = $product?->images?->first();
            $business = $user->vendor->businessProfile;
            return [
                'business' => $business,
                'product' => $product,
                'image' => $image
            ];
        });

    // Stats for hero section
    $vendors = App\Models\Vendor\Vendor::where('account_status', 'active')->get();
    $products = App\Models\Product::where('status', 'active')->get();
    $countries = App\Models\Country::where('status', 'active')->get();
@endphp
<section class="hero-section py-4 bg-gray-50">
    <div class="container mx-auto px-4 max-w-7xl">
        <div class="grid lg:grid-cols-2 gap-5 items-center">

            <!-- Left Side - Text Content -->
            <div class="order-2 lg:order-1">
                <div class="mb-2">
                    <span class="inline-block bg-[#ff0808] text-white px-2.5 py-1 rounded-full text-xs font-bold">
                        <i class="fas fa-trophy mr-1"></i>{{ __('messages.hero_badge') }}
                    </span>
                </div>

                <h1 class="hero-title text-xl md:text-2xl lg:text-3xl font-black mb-3 leading-tight text-gray-900 font-display">
                    {{ __('messages.hero_title') }}
                </h1>

                <p class="hero-subtitle text-sm md:text-base mb-4 text-gray-700 font-semibold">
                    {{ __('messages.hero_subtitle') }}
                </p>

                <div class="relative">
                    <form action="{{ route('global.search') }}" method="GET" class="search-container bg-white rounded-lg p-1 flex items-center gap-2 mb-4 shadow-lg border-2 border-gray-200">
                        <i class="fas fa-search text-gray-400 ml-2 text-xs"></i>
                        <input
                            type="text"
                            name="query"
                            id="heroSearchInput"
                            placeholder="{{ __('messages.search_placeholder') }}"
                            class="search-input flex-1 px-2 py-2 text-gray-900 text-xs font-medium focus:outline-none"
                            autocomplete="off"
                        >
                        <button type="submit" class="search-btn bg-[#ff0808] hover:bg-red-700 text-white px-4 py-2 rounded-md font-bold transition-colors whitespace-nowrap text-xs">
                            <i class="fas fa-search mr-1"></i>{{ __('messages.search_button') }}
                        </button>
                    </form>
                    <div id="heroSearchResults" class="absolute w-full bg-white rounded-lg shadow-xl mt-1 hidden z-50 max-h-96 overflow-y-auto"></div>
                </div>

                <!-- Stats -->
                <div class="stats-container grid grid-cols-3 gap-3">
                    <div>
                        <div class="stat-number text-xl font-black text-[#ff0808] mb-0.5 ml-3">{{ $vendors->count()}}+ </div>
                        <div class="stat-label text-xs font-bold text-gray-600">
                            <i class="fas fa-store mr-1"></i>{{ __('messages.suppliers_stat') }}
                        </div>
                    </div>
                    <div>
                        <div class="stat-number text-xl font-black text-[#ff0808] mb-0.5">{{ $products->count()}}+</div>
                        <div class="stat-label text-xs font-bold text-gray-600">
                            <i class="fas fa-box mr-1"></i>{{ __('messages.products_stat') }}
                        </div>
                    </div>
                    <div>
                        <div class="stat-number text-xl font-black text-[#ff0808] mb-0.5 ml-2">{{$countries->count()}}+</div>
                        <div class="stat-label text-xs font-bold text-gray-600">
                            <i class="fas fa-globe mr-1"></i>{{ __('messages.countries_stat') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Sliding Images with Real Business Data -->
            <div class="order-1 lg:order-2">
                <div class="image-slider relative h-[280px] lg:h-[360px] rounded-xl overflow-hidden shadow-xl">

                    @if($featuredBusinesses && $featuredBusinesses->count() > 0)
                        @if($featuredBusinesses->count() === 1)
                            {{-- Single static card (no animation, no dots) --}}
                            @php
                                $featured = $featuredBusinesses->first();
                                $business = $featured['business'];
                                $product = $featured['product'];
                                $image = $featured['image'];
                            @endphp

                            <div class="hero-slide absolute inset-0 opacity-100">
                                <img src="{{ $image->image_url ?? 'https://images.pexels.com/photos/1093837/pexels-photo-1093837.jpeg' }}"
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-cover imageLI">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>

                                <div class="slide-card absolute bottom-3 left-3 right-3 bg-white rounded-lg p-3 shadow-xl">
                                    <div class="flex items-center gap-2">
                                        <!-- Business Logo -->
                                        <a href="{{ route('business-profile.products', $business->id) }}"
                                           class="company-logo w-10 h-10 bg-[#ff0808] to-red-700 rounded-lg flex items-center justify-center text-white text-sm font-bold flex-shrink-0 hover:scale-110 transition-transform">
                                            {{ strtoupper(substr($business->business_name, 0, 2)) }}
                                        </a>

                                        <!-- Business Info -->
                                        <div class="flex-1 min-w-0">
                                            <a href="{{ route('business-profile.products', $business->id) }}"
                                               class="company-name text-sm font-black text-gray-900 mb-0.5 font-display hover:text-[#ff0808] transition-colors block truncate">
                                                {{ $business->business_name }}
                                            </a>
                                            <a href="{{ route('products.show', $product->slug) }}"
                                               class="company-desc text-xs text-gray-600 font-semibold hover:text-[#ff0808] transition-colors block truncate">
                                                <i class="fas fa-box mr-1 text-[#ff0808]"></i>{{ Str::limit($product->name, 30) }}
                                            </a>
                                        </div>

                                        <!-- Rating & Verification -->
                                        <div class="rating-container hidden sm:flex flex-col items-end">
                                            @if($business->is_admin_verified)
                                                <div class="flex items-center gap-1 mb-1">
                                                    <i class="fas fa-check-circle text-green-500 text-xs"></i>
                                                    <span class="text-[9px] font-bold text-green-600">Verified</span>
                                                </div>
                                            @endif
                                            @if($business->country)
                                                <div class="text-[9px] text-gray-500 flex items-center gap-1">
                                                    <i class="fas fa-map-marker-alt text-[#ff0808]"></i>
                                                    {{ $business->country->name }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Multiple slides with animation --}}
                            @foreach($featuredBusinesses as $index => $featured)
                                @php
                                    $business = $featured['business'];
                                    $product = $featured['product'];
                                    $image = $featured['image'];
                                @endphp

                                <!-- Slide {{ $index + 1 }} -->
                                <div class="hero-slide slide-{{ $index + 1 }} absolute inset-0 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }} transition-opacity duration-1000">
                                    <img src="{{ $image->image_url ?? 'https://images.pexels.com/photos/1093837/pexels-photo-1093837.jpeg' }}"
                                         alt="{{ $product->name }}"
                                         class="w-full h-full object-cover imageLI">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>

                                    <div class="slide-card absolute bottom-3 left-3 right-3 bg-white rounded-lg p-3 shadow-xl">
                                        <div class="flex items-center gap-2">
                                            <!-- Business Logo -->
                                            <a href="{{ route('business-profile.products', $business->id) }}"
                                               class="company-logo w-10 h-10 bg-[#ff0808] to-red-700 rounded-lg flex items-center justify-center text-white text-sm font-bold flex-shrink-0 hover:scale-110 transition-transform">
                                                {{ strtoupper(substr($business->business_name, 0, 2)) }}
                                            </a>

                                            <!-- Business Info -->
                                            <div class="flex-1 min-w-0">
                                                <a href="{{ route('business-profile.products', $business->id) }}"
                                                   class="company-name text-sm font-black text-gray-900 mb-0.5 font-display hover:text-[#ff0808] transition-colors block truncate">
                                                    {{ $business->business_name }}
                                                </a>
                                                <a href="{{ route('products.show', $product->slug) }}"
                                                   class="company-desc text-xs text-gray-600 font-semibold hover:text-[#ff0808] transition-colors block truncate">
                                                    <i class="fas fa-box mr-1 text-[#ff0808]"></i>{{ Str::limit($product->name, 30) }}
                                                </a>
                                            </div>

                                            <!-- Rating & Verification -->
                                            <div class="rating-container hidden sm:flex flex-col items-end">
                                                @if($business->is_admin_verified)
                                                    <div class="flex items-center gap-1 mb-1">
                                                        <i class="fas fa-check-circle text-green-500 text-xs"></i>
                                                        <span class="text-[9px] font-bold text-green-600">Verified</span>
                                                    </div>
                                                @endif
                                                @if($business->country)
                                                    <div class="text-[9px] text-gray-500 flex items-center gap-1">
                                                        <i class="fas fa-map-marker-alt text-[#ff0808]"></i>
                                                        {{ $business->country->name }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Slide Navigation Dots (only for multiple slides) -->
                            <div class="slide-dots absolute top-3 right-3 flex gap-1 z-10">
                                @foreach($featuredBusinesses as $index => $featured)
                                    <button class="hero-dot w-1.5 h-1.5 rounded-full bg-white/50 hover:bg-white transition-all {{ $index === 0 ? 'bg-white' : '' }}"
                                            data-slide="{{ $index }}"></button>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <!-- Fallback if no featured businesses available -->
                        <div class="hero-slide absolute inset-0 opacity-100">
                            <img src="https://images.pexels.com/photos/1093837/pexels-photo-1093837.jpeg"
                                 alt="Global Business"
                                 class="w-full h-full object-cover imageLI">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>

                            <div class="slide-card absolute bottom-3 left-3 right-3 bg-white rounded-lg p-3 shadow-xl">
                                <div class="flex items-center gap-2">
                                    <div class="company-logo w-10 h-10 bg-[#ff0808] to-red-700 rounded-lg flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                        GB
                                    </div>
                                    <div class="flex-1">
                                        <div class="company-name text-sm font-black text-gray-900 mb-0.5 font-display">AFRISELLERS</div>
                                        <div class="company-desc text-xs text-gray-600 font-semibold">
                                            <i class="fas fa-shipping-fast mr-1 text-[#ff0808]"></i>Selling With Us Is Leading Market
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</section>

<style>
/* Mobile First - Small screens (up to 640px) */
@media (max-width: 640px) {
    .hero-section {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }

    .hero-title {
        font-size: 1.5rem;
        line-height: 1.75rem;
        margin-bottom: 0.5rem;
    }

    .hero-subtitle {
        font-size: 0.75rem;
        margin-bottom: 0.75rem;
    }

    .search-container {
        flex-direction: column;
        padding: 0.375rem;
        gap: 0.375rem;
        margin-bottom: 0.75rem;
    }

    .search-input {
        padding: 0.5rem;
        font-size: 0.75rem;
        width: 100%;
    }

    .search-btn {
        padding: 0.5rem 0.75rem;
        font-size: 0.625rem;
        width: 100%;
    }

    .stats-container {
        gap: 0.5rem;
    }

    .stat-number {
        font-size: 1.25rem;
    }

    .stat-label {
        font-size: 0.625rem;
    }

    .image-slider {
        height: 200px;
        border-radius: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .slide-card {
        bottom: 0.5rem;
        left: 0.5rem;
        right: 0.5rem;
        padding: 0.5rem;
    }

    .company-logo {
        width: 2rem;
        height: 2rem;
        font-size: 0.75rem;
    }

    .company-name {
        font-size: 0.75rem;
    }

    .company-desc {
        font-size: 0.625rem;
    }

    .slide-dots {
        top: 0.5rem;
        right: 0.5rem;
    }

    .hero-dot {
        width: 0.375rem;
        height: 0.375rem;
    }
}

/* Medium screens (641px to 768px) */
@media (min-width: 641px) and (max-width: 768px) {
    .hero-section {
        padding-top: 1.5rem;
        padding-bottom: 1.5rem;
    }

    .hero-title {
        font-size: 2rem;
        margin-bottom: 0.75rem;
    }

    .hero-subtitle {
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }

    .search-container {
        padding: 0.5rem;
        margin-bottom: 1rem;
    }

    .search-input {
        padding: 0.625rem;
    }

    .search-btn {
        padding: 0.625rem 1rem;
    }

    .stats-container {
        gap: 0.75rem;
    }

    .stat-number {
        font-size: 1.5rem;
    }

    .image-slider {
        height: 240px;
    }

    .slide-card {
        bottom: 0.75rem;
        left: 0.75rem;
        right: 0.75rem;
        padding: 0.625rem;
    }

    .company-logo {
        width: 2.25rem;
        height: 2.25rem;
        font-size: 0.875rem;
    }

    .company-name {
        font-size: 1rem;
    }
}

/* Large screens (769px to 1024px) */
@media (min-width: 769px) and (max-width: 1024px) {
    .hero-section {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }

    .hero-title {
        font-size: 2.5rem;
    }

    .hero-subtitle {
        font-size: 1rem;
    }

    .image-slider {
        height: 320px;
    }
}

.imageLI {
    max-width: 100%;
    height: auto;
}

@media (max-width: 768px) {
    .hero-dot {
        min-width: 8px;
        min-height: 8px;
    }

    button {
        cursor: pointer;
    }
}

/* Desktop slider animation - only for multiple slides */
@media (min-width: 769px) {
    @keyframes slideAnimation {
        0% { opacity: 1; }
        33.33% { opacity: 1; }
        36.66% { opacity: 0; }
        96.66% { opacity: 0; }
        100% { opacity: 1; }
    }

    .slide-1 {
        animation: slideAnimation 15s infinite;
    }

    .slide-2 {
        animation: slideAnimation 15s infinite 5s;
    }

    .slide-3 {
        animation: slideAnimation 15s infinite 10s;
    }
}

/* Mobile slider animation - only for multiple slides */
@media (max-width: 768px) {
    @keyframes mobileSlideAnimation {
        0% { opacity: 1; }
        33.33% { opacity: 1; }
        41.66% { opacity: 0; }
        91.66% { opacity: 0; }
        100% { opacity: 1; }
    }

    .slide-1 {
        animation: mobileSlideAnimation 12s infinite;
    }

    .slide-2 {
        animation: mobileSlideAnimation 12s infinite 4s;
    }

    .slide-3 {
        animation: mobileSlideAnimation 12s infinite 8s;
    }
}

/* Manual dot navigation */
.hero-dot.active {
    background-color: white;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.hero-dot');

    if (slides.length <= 1) return; // No need for manual control if only one slide

    let currentSlide = 0;

    // Dot click handler
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            currentSlide = index;
            updateSlides();
        });
    });

    function updateSlides() {
        slides.forEach((slide, index) => {
            if (index === currentSlide) {
                slide.style.opacity = '1';
            } else {
                slide.style.opacity = '0';
            }
        });

        dots.forEach((dot, index) => {
            if (index === currentSlide) {
                dot.classList.add('active', 'bg-white');
                dot.classList.remove('bg-white/50');
            } else {
                dot.classList.remove('active', 'bg-white');
                dot.classList.add('bg-white/50');
            }
        });
    }
});
</script>

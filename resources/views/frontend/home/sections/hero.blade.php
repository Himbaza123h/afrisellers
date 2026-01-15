<section class="hero-section py-6 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="grid lg:grid-cols-2 gap-6 items-center">

            <!-- Left Side - Text Content -->
            <div class="order-2 lg:order-1">
                <div class="mb-3">
                    <span class="inline-block bg-[#ff0808] text-white px-3 py-1.5 rounded-full text-xs font-bold">
                        <i class="fas fa-trophy mr-1"></i>{{ __('messages.hero_badge') }}
                    </span>
                </div>

                <h1 class="hero-title text-lg md:text-3xl lg:text-5xl font-black mb-4 leading-tight text-gray-900 font-display">
                    {{ __('messages.hero_title') }}
                </h1>

                <p class="hero-subtitle text-base md:text-lg mb-6 text-gray-700 font-semibold">
                    {{ __('messages.hero_subtitle') }}
                </p>

                <div class="relative">
                    <form action="{{ route('global.search') }}" method="GET" class="search-container bg-white rounded-lg p-1.5 flex items-center gap-2 mb-6 shadow-lg border-2 border-gray-200">
                        <i class="fas fa-search text-gray-400 ml-3 text-sm"></i>
                        <input
                            type="text"
                            name="query"
                            id="heroSearchInput"
                            placeholder="{{ __('messages.search_placeholder') }}"
                            class="search-input flex-1 px-3 py-2.5 text-gray-900 text-sm font-medium focus:outline-none"
                            autocomplete="off"
                        >
                        <button type="submit" class="search-btn bg-[#ff0808] hover:bg-red-700 text-white px-5 py-2.5 rounded-md font-bold transition-colors whitespace-nowrap text-sm">
                            <i class="fas fa-search mr-1.5"></i>{{ __('messages.search_button') }}
                        </button>
                    </form>
                    <div id="heroSearchResults" class="absolute w-full bg-white rounded-lg shadow-xl mt-1 hidden z-50 max-h-96 overflow-y-auto"></div>
                </div>

                <!-- Stats -->
                <div class="stats-container grid grid-cols-3 gap-4">
                    <div>
                        <div class="stat-number text-2xl font-black text-[#ff0808] mb-0.5 ml-4">{{ $vendors->count()}}+ </div>
                        <div class="stat-label text-xs font-bold text-gray-600">
                            <i class="fas fa-store mr-1"></i>{{ __('messages.suppliers_stat') }}
                        </div>
                    </div>
                    <div>
                        <div class="stat-number text-2xl font-black text-[#ff0808] mb-0.5">{{ $products->count()}}+</div>
                        <div class="stat-label text-xs font-bold text-gray-600">
                            <i class="fas fa-box mr-1"></i>{{ __('messages.products_stat') }}
                        </div>
                    </div>
                    <div>
                        <div class="stat-number text-2xl font-black text-[#ff0808] mb-0.5 ml-3">{{$countries->count()}}+</div>
                        <div class="stat-label text-xs font-bold text-gray-600">
                            <i class="fas fa-globe mr-1"></i>{{ __('messages.countries_stat') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Sliding Images with Real Business Data -->
            <div class="order-1 lg:order-2">
                <div class="image-slider relative h-[350px] lg:h-[450px] rounded-xl overflow-hidden shadow-xl">

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

                                <div class="slide-card absolute bottom-4 left-4 right-4 bg-white rounded-lg p-4 shadow-xl">
                                    <div class="flex items-center gap-3">
                                        <!-- Business Logo -->
                                        <a href="{{ route('business-profile.products', $business->id) }}"
                                           class="company-logo w-12 h-12 bg-gradient-to-br from-[#ff0808] to-red-700 rounded-lg flex items-center justify-center text-white text-base font-bold flex-shrink-0 hover:scale-110 transition-transform">
                                            {{ strtoupper(substr($business->business_name, 0, 2)) }}
                                        </a>

                                        <!-- Business Info -->
                                        <div class="flex-1 min-w-0">
                                            <a href="{{ route('business-profile.products', $business->id) }}"
                                               class="company-name text-base font-black text-gray-900 mb-0.5 font-display hover:text-[#ff0808] transition-colors block truncate">
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
                                                    <i class="fas fa-check-circle text-green-500 text-sm"></i>
                                                    <span class="text-[10px] font-bold text-green-600">Verified</span>
                                                </div>
                                            @endif
                                            @if($business->country)
                                                <div class="text-[10px] text-gray-500 flex items-center gap-1">
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

                                    <div class="slide-card absolute bottom-4 left-4 right-4 bg-white rounded-lg p-4 shadow-xl">
                                        <div class="flex items-center gap-3">
                                            <!-- Business Logo -->
                                            <a href="{{ route('business-profile.products', $business->id) }}"
                                               class="company-logo w-12 h-12 bg-gradient-to-br from-[#ff0808] to-red-700 rounded-lg flex items-center justify-center text-white text-base font-bold flex-shrink-0 hover:scale-110 transition-transform">
                                                {{ strtoupper(substr($business->business_name, 0, 2)) }}
                                            </a>

                                            <!-- Business Info -->
                                            <div class="flex-1 min-w-0">
                                                <a href="{{ route('business-profile.products', $business->id) }}"
                                                   class="company-name text-base font-black text-gray-900 mb-0.5 font-display hover:text-[#ff0808] transition-colors block truncate">
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
                                                        <i class="fas fa-check-circle text-green-500 text-sm"></i>
                                                        <span class="text-[10px] font-bold text-green-600">Verified</span>
                                                    </div>
                                                @endif
                                                @if($business->country)
                                                    <div class="text-[10px] text-gray-500 flex items-center gap-1">
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
                            <div class="slide-dots absolute top-4 right-4 flex gap-1.5 z-10">
                                @foreach($featuredBusinesses as $index => $featured)
                                    <button class="hero-dot w-2 h-2 rounded-full bg-white/50 hover:bg-white transition-all {{ $index === 0 ? 'bg-white' : '' }}"
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

                            <div class="slide-card absolute bottom-4 left-4 right-4 bg-white rounded-lg p-4 shadow-xl">
                                <div class="flex items-center gap-3">
                                    <div class="company-logo w-12 h-12 bg-gradient-to-br from-[#ff0808] to-red-700 rounded-lg flex items-center justify-center text-white text-base font-bold flex-shrink-0">
                                        GB
                                    </div>
                                    <div class="flex-1">
                                        <div class="company-name text-base font-black text-gray-900 mb-0.5 font-display">AFRISELLERS</div>
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
        padding-top: 1.5rem;
        padding-bottom: 1.5rem;
    }

    .hero-title {
        font-size: 1.875rem;
        line-height: 2.25rem;
        margin-bottom: 0.75rem;
    }

    .hero-subtitle {
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }

    .search-container {
        flex-direction: column;
        padding: 0.5rem;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .search-input {
        padding: 0.625rem;
        font-size: 0.875rem;
        width: 100%;
    }

    .search-btn {
        padding: 0.625rem 1rem;
        font-size: 0.75rem;
        width: 100%;
    }

    .stats-container {
        gap: 0.75rem;
    }

    .stat-number {
        font-size: 1.5rem;
    }

    .stat-label {
        font-size: 0.625rem;
    }

    .image-slider {
        height: 250px;
        border-radius: 0.75rem;
        margin-bottom: 0.75rem;
    }

    .slide-card {
        bottom: 0.75rem;
        left: 0.75rem;
        right: 0.75rem;
        padding: 0.75rem;
    }

    .company-logo {
        width: 2.5rem;
        height: 2.5rem;
        font-size: 0.875rem;
    }

    .company-name {
        font-size: 0.875rem;
    }

    .company-desc {
        font-size: 0.625rem;
    }

    .slide-dots {
        top: 0.75rem;
        right: 0.75rem;
    }

    .hero-dot {
        width: 0.5rem;
        height: 0.5rem;
    }
}

/* Medium screens (641px to 768px) */
@media (min-width: 641px) and (max-width: 768px) {
    .hero-section {
        padding-top: 2rem;
        padding-bottom: 2rem;
    }

    .hero-title {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }

    .hero-subtitle {
        font-size: 1rem;
        margin-bottom: 1.5rem;
    }

    .search-container {
        padding: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .search-input {
        padding: 0.75rem;
    }

    .search-btn {
        padding: 0.75rem 1.5rem;
    }

    .stats-container {
        gap: 1rem;
    }

    .stat-number {
        font-size: 1.75rem;
    }

    .image-slider {
        height: 300px;
    }

    .slide-card {
        bottom: 1rem;
        left: 1rem;
        right: 1rem;
        padding: 0.875rem;
    }

    .company-logo {
        width: 2.75rem;
        height: 2.75rem;
        font-size: 1rem;
    }

    .company-name {
        font-size: 1.125rem;
    }
}

/* Large screens (769px to 1024px) */
@media (min-width: 769px) and (max-width: 1024px) {
    .hero-section {
        padding-top: 1.5rem;
        padding-bottom: 1.5rem;
    }

    .hero-title {
        font-size: 3rem;
    }

    .hero-subtitle {
        font-size: 1.125rem;
    }

    .image-slider {
        height: 400px;
    }
}

.imageLI {
    max-width: 100%;
    height: auto;
}

@media (max-width: 768px) {
    .hero-dot {
        min-width: 10px;
        min-height: 10px;
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

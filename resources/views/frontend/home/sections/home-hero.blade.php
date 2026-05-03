@include('frontend.home.sections.ads-banner')

@php
    $settings = App\Models\Setting::all();

    // Available Loads
    $availableLoads = collect([]);
    if (class_exists('App\Models\Load')) {
        $availableLoads = App\Models\Load::where('status', 'posted')
            ->with(['originCountry', 'destinationCountry'])
            ->latest()
            ->limit(8)
            ->get();
    }

    // Available Cars
    $availableCars = collect([]);
    if (class_exists('App\Models\Car')) {
        $availableCars = App\Models\Car::where('availability_status', 'available')
            ->where('is_verified', true)
            ->with(['fromCountry', 'toCountry', 'user'])
            ->select([
                'id', 'listing_number', 'user_id', 'make', 'model', 'year',
                'vehicle_type', 'cargo_capacity', 'cargo_capacity_unit',
                'from_city', 'from_country_id', 'to_city', 'to_country_id',
                'flexible_destination', 'price', 'pricing_type', 'currency',
                'price_negotiable', 'mileage', 'images', 'driver_included',
                'rating', 'completed_trips'
            ])
            ->orderByDesc('is_featured')
            ->orderByDesc('rating')
            ->limit(8)
            ->get();
    }

    // Upcoming Tradeshows
    $upcomingTradeshows = collect([]);
    if (class_exists('App\Models\Tradeshow')) {
        $upcomingTradeshows = App\Models\Tradeshow::where('status', 'published')
            ->where('start_date', '>=', now())
            ->where('start_date', '<=', now()->addMonths(6))
            ->with(['country', 'user'])
            ->orderBy('start_date')
            ->limit(6)
            ->get();
    }

    // Featured Showrooms
    $featuredShowrooms = collect([]);
    if (class_exists('App\Models\Showroom')) {
        $featuredShowrooms = App\Models\Showroom::where('status', 'active')
            ->where('is_featured', true)
            ->where('is_verified', true)
            ->with(['country', 'user'])
            ->orderByDesc('rating')
            ->limit(8)
            ->get();
    }

    // New Arrival Products
    $newArrivalProducts = App\Models\Product::where('status', 'active')
        ->where('is_admin_verified', true)
        ->where('created_at', '>=', now()->subDays(2))
        ->with(['images', 'productCategory'])
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

    // New Arrival Companies
    $newArrivalCompanies = App\Models\User::whereHas('products', function ($query) {
            $query->where('created_at', '>=', now()->subDays(2))
                  ->where('status', 'active')
                  ->where('is_admin_verified', true);
        })
        ->with(['vendor.businessProfile', 'products' => function ($query) {
            $query->where('created_at', '>=', now()->subDays(2))
                  ->where('status', 'active')
                  ->where('is_admin_verified', true)
                  ->with('images');
        }])
        ->withCount(['products as new_products_count' => function ($query) {
            $query->where('created_at', '>=', now()->subDays(2))
                  ->where('status', 'active')
                  ->where('is_admin_verified', true);
        }])
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

    // ── Hero Background Images from Configuration ─────────────────
    // Get ALL active homepage_image configs (global + every country variant)
    $heroConfigs = \App\Models\Configuration::where('unique_id', 'homepage_image')
                    ->where('is_active', true)
                    ->get();

    // Default hero = global config (country_id IS NULL), fallback to first
    $heroConfig = $heroConfigs->whereNull('country_id')->first()
               ?? $heroConfigs->first();

    // Build images array for the default/global config
    $heroImages = [];
    if ($heroConfig) {
        if (!empty($heroConfig->files) && is_array($heroConfig->files)) {
            foreach ($heroConfig->files as $file) {
                $heroImages[] = $file['url'] ?? \Illuminate\Support\Facades\Storage::disk('public')->url($file['path']);
            }
        } elseif (!empty($heroConfig->value)) {
            foreach (explode(',', $heroConfig->value) as $path) {
                $path = trim($path);
                if ($path) $heroImages[] = \Illuminate\Support\Facades\Storage::disk('public')->url($path);
            }
        }
    }

    // Fallback
    // if (empty($heroImages)) {
    //     $heroImages = [asset('images/hero-default.jpg')];
    // }
    // Fallback
if (empty($heroImages)) {
    $heroImages = [asset('images/hero-default.jpg')];
}

// Always append girl image as the last slide
$heroImages[] = asset('girlimage.png');

    // Build map: country_id (0 = global) => [image urls]
    // This is passed to JS so switching country swaps slides instantly
    $heroImagesByCountry = [];
    foreach ($heroConfigs as $cfg) {
        $key  = $cfg->country_id ?? 0;
        $urls = [];
        if (!empty($cfg->files) && is_array($cfg->files)) {
            foreach ($cfg->files as $f) {
                $urls[] = $f['url'] ?? \Illuminate\Support\Facades\Storage::disk('public')->url($f['path']);
            }
        } elseif (!empty($cfg->value)) {
            foreach (explode(',', $cfg->value) as $path) {
                $path = trim($path);
                if ($path) $urls[] = \Illuminate\Support\Facades\Storage::disk('public')->url($path);
            }
        }
        if (!empty($urls)) $heroImagesByCountry[$key] = $urls;
    }

    // ── Hero Title & Subtitle ─────────────────────────────────────
$titleConfigs    = \App\Models\Configuration::where('unique_id', 'homepage_title')   ->where('is_active', true)->get();
$subtitleConfigs = \App\Models\Configuration::where('unique_id', 'homepage_subtitle')->where('is_active', true)->get();

// Default = global (country_id null)
$defaultTitle    = $titleConfigs->whereNull('country_id')->first()?->value
                ?? $titleConfigs->first()?->value
                ?? 'Online Grocery Shopping';

$defaultSubtitle = $subtitleConfigs->whereNull('country_id')->first()?->value
                ?? $subtitleConfigs->first()?->value
                ?? 'Source quality products from verified suppliers across Africa';

// Build maps: country_id (0 = global) => text  — for JS switching
$titlesByCountry    = [];
foreach ($titleConfigs as $cfg) {
    $titlesByCountry[$cfg->country_id ?? 0] = $cfg->value;
}

$subtitlesByCountry = [];
foreach ($subtitleConfigs as $cfg) {
    $subtitlesByCountry[$cfg->country_id ?? 0] = $cfg->value;
}

    // ── UISection hero settings ────────────────────────────────────
    $heroSection   = \App\Models\UISection::where('section_key', 'hero_section')->first();
    $animationMode = $heroSection?->getAnimationMode() ?? 'fade'; // slide | fade | flip | none
    $slideCount    = count($heroImages);
    $isMultiple    = $slideCount > 1;
    $slideInterval = 5000;
@endphp

<!-- Top Bar -->
<div id="topBar" class="bg-[#ff0808] text-white py-1 transition-transform duration-300">
    <div class="container flex justify-between items-center px-4 mx-auto text-[10px] md:text-xs">
        <div class="flex gap-2 items-center">
            <a href="tel:{{ $settings->where('key', 'company_phone')->first()?->value ?? '+250 780 879126' }}" class="transition-colors hover:text-red-100">
                <i class="mr-1 fas fa-phone text-[9px]"></i>
                <span class="hidden sm:inline">Helpline: </span>
                <span class="hidden sm:inline">{{ $settings->where('key', 'company_phone')->first()?->value ?? '+1(469)837-9001 | +250 788 797 687 | +250 780 879126' }}</span>
            </a>
        </div>
        <div class="flex gap-2 items-center">
            <x-language-switcher />
            <div class="flex items-center px-1">
                @php $topBarCountries = App\Models\Country::where('status', 'active')->orderBy('name')->get(); @endphp
                <select name="country" id="country"
                        class="text-black text-[9px] md:text-[10px] py-0.5 px-1 md:px-1.5 rounded bg-white"
                        onchange="HeroSlideshow.applyCountry(this.value); saveSelectedCountry(this.value);">
                    <option selected disabled>Select a country</option>
                    @foreach ($topBarCountries as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Main Header -->
<div id="mainHeader" class="bg-white border-b border-gray-200 sticky top-0 z-50 transition-all duration-300">
    <div class="container mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between py-2 sm:py-2.5">
            <div class="shrink-0">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('mainlogo.png') }}" alt="Afrisellers" class="h-6 sm:h-8">
                </a>
            </div>

            <div class="hidden md:flex flex-1 max-w-2xl mx-4 lg:mx-8">
                <div class="flex items-center gap-2 w-full">
                    <button class="flex items-center gap-2 px-2 sm:px-3 py-1.5 bg-white border border-gray-300 rounded text-xs text-gray-700 hover:bg-gray-50 flex-shrink-0">
                        <i class="fas fa-store text-gray-500 text-xs"></i>
                        <span class="font-medium hidden sm:inline">Marketplace</span>
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    <div class="relative w-full">
                        <form action="{{ route('global.search') }}" method="GET" class="relative w-full">
                            <input type="text" name="query" id="navSearchInput"
                                placeholder="{{ __('messages.search_placeholder') }}"
                                class="w-full px-3 py-1.5 md:px-2 md:py-1 lg:py-2 lg:pr-32 rounded-lg border-2 border-gray-300 focus:border-[#ff0808] focus:outline-none font-medium text-xs"
                                autocomplete="off">
                            <button type="submit" class="absolute right-0 top-0 h-full px-4 md:px-5 lg:px-6 bg-[#ff0808] text-white rounded-r-lg hover:bg-red-700 transition-colors font-bold text-sm">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                        <div id="navSearchResults" class="absolute w-full bg-white rounded-lg shadow-xl mt-1 hidden z-50 max-h-96 overflow-y-auto"></div>
                    </div>
                </div>
            </div>

            <div class="flex gap-2 items-center md:gap-3 lg:gap-5">
            <button id="mobile-menu-toggle" onclick="openMobileMenu()" class="md:hidden text-gray-700 hover:text-[#ff0808]">
                <i class="text-base fas fa-bars"></i>
            </button>
            <div class="flex gap-2 items-center md:gap-3">
                @auth
                    <div class="w-7 h-7 md:w-9 md:h-9 rounded-full bg-[#ff0808] flex items-center justify-center">
                        <span class="text-xs md:text-sm font-semibold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                    </div>
                    <div class="hidden gap-2 items-center text-gray-700 lg:flex">
                        @php
                            $user = auth()->user();
                            $dashboardRoute = null;
                            if ($user) {
                                $isAdmin = $user->roles()->where('roles.id', 1)->where('roles.name', 'Admin')->where('roles.slug', 'admin')->exists();
                                if ($isAdmin) {
                                    $dashboardRoute = route('admin.dashboard.home');
                                } else {
                                    $isRegionalAdmin = $user->roles()->where('roles.slug', 'regional_admin')->exists();
                                    if ($isRegionalAdmin) {
                                        $dashboardRoute = route('regional.dashboard.home');
                                    } else {
                                        $isCountryAdmin = $user->roles()->where('roles.slug', 'country_admin')->exists();
                                        if ($isCountryAdmin) {
                                            $dashboardRoute = route('country.dashboard.home');
                                        } else {
                                            $isAgent = $user->roles()->where('roles.slug', 'agent')->exists();
                                            if ($isAgent) {
                                                $dashboardRoute = route('agent.dashboard.home');
                                            } elseif ($user->is_partner) {
                                                $dashboardRoute = route('partner.dashboard');
                                            } else {
                                                $vendor = App\Models\Vendor\Vendor::where('user_id', $user->id)->first();
                                                $dashboardRoute = $vendor ? route('vendor.dashboard.home') : route('buyer.dashboard.home');
                                            }
                                        }
                                    }
                                }
                            }
                        @endphp
                        @if ($dashboardRoute)
                            <a href="{{ $dashboardRoute }}" class="hover:text-[#ff0808] font-semibold transition-colors text-xs">{{ __('messages.dashboard') }}</a>
                        @endif
                        <span class="text-gray-400">|</span>
                        <form action="{{ route('auth.logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="hover:text-[#ff0808] font-semibold transition-colors text-xs">{{ __('messages.logout') }}</button>
                        </form>
                    </div>
                @else
                    <div class="flex justify-center items-center w-7 h-7 md:w-9 md:h-9 rounded-full border-2 border-gray-300">
                        <i class="text-xs text-gray-400 fas fa-user"></i>
                    </div>
                    <div class="hidden gap-2 items-center text-gray-700 lg:flex">
                        <a href="{{ route('auth.signin') }}" class="hover:text-[#ff0808] font-semibold transition-colors text-xs">{{ __('messages.login') }}</a>
                        <span class="text-gray-400">|</span>
                        <a href="{{ route('auth.register') }}" class="hover:text-[#ff0808] font-semibold transition-colors text-xs">{{ __('messages.registration') }}</a>
                    </div>
                @endauth
            </div>

                <a href="{{ route('livestream') }}" class="text-gray-700 hover:text-[#ff0808] flex items-center gap-1.5 relative transition-colors">
                    <div class="relative">
                        <i class="text-base md:text-lg fas fa-video"></i>
                        <span class="absolute -top-1.5 -right-1.5 bg-[#ff0808] text-white text-[9px] rounded-full w-3.5 h-3.5 flex items-center justify-center font-bold shadow-md">2</span>
                    </div>
                    <span class="hidden lg:flex items-center gap-1 bg-[#ff0808] text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold">
                        <span class="w-1 h-1 bg-white rounded-full animate-pulse"></span>
                        {{ __('messages.live') }}
                    </span>
                </a>
<!-- Currency Switcher -->
<div id="currencySwitcherWrapper">
    <button id="currencyBtn"
        onclick="toggleCurrencyDropdown()"
        class="flex items-center gap-1 text-gray-700 hover:text-[#ff0808] transition-colors text-xs font-semibold px-1 py-1">
        <i class="fas fa-coins text-base md:text-lg"></i>
        <span id="currencyLabel" class="hidden lg:inline">USD</span>
        <i id="currencyChevron" class="fas fa-chevron-down text-[9px] hidden lg:inline transition-transform duration-200"></i>
    </button>
</div>

<!-- Currency Dropdown — FIXED outside all stacking contexts -->
<div id="currencyDropdown"
    class="hidden bg-white rounded-xl border border-gray-200 w-56 max-h-80 overflow-y-auto"
    style="position:fixed; z-index:9999999; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
    <div class="px-3 py-2 border-b border-gray-100 sticky top-0 bg-white rounded-t-xl flex items-center justify-between">
        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Currency</p>
        <span id="currencyLoadingIndicator" class="text-[9px] text-gray-400 hidden">
            <i class="fas fa-spinner fa-spin mr-1"></i>Updating…
        </span>
    </div>
    <div id="currencyList">
        <div class="flex items-center justify-center py-6 text-gray-400 text-xs">
            <i class="fas fa-spinner fa-spin mr-2"></i> Loading rates…
        </div>
    </div>
</div>

<a href="{{ route('cart.index') }}" class="relative flex gap-2 items-center text-gray-700 transition-colors hover:text-[#ff0808]">
                    <div class="relative">
                        <i class="text-base md:text-lg fas fa-shopping-cart"></i>
                        <span id="cartCount" class="absolute -top-1.5 -right-1.5 bg-[#ff0808] text-white text-[9px] rounded-full w-3.5 h-3.5 flex items-center justify-center font-bold shadow-md">0</span>
                    </div>
                    <span class="hidden text-xs font-semibold lg:inline">Cart</span>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Nav Bar --}}
<div id="navBar" class="bg-[#1a2942] text-white hidden lg:block sticky top-0 z-[99999] transition-all duration-300">
    <div class="container mx-auto px-6">
        <div class="flex items-center">
            <div class="relative" style="width: 288px; flex-shrink: 0;">
                <button class="flex items-center justify-center gap-2 text-xs font-medium hover:bg-white/10 transition-colors px-4 py-2.5 w-full">
                    <span>All Categories</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                <div class="absolute right-0 top-0 bottom-0 w-px bg-white/10"></div>
            </div>

            <div class="relative nav-item-wrapper flex-1">
                <a href="{{ route('featured-suppliers') }}" class="nav-item block text-xs hover:bg-white/10 transition-colors text-center px-4 py-2.5 border-r border-white/10">Featured Suppliers</a>
                @php
                    $featuredSuppliers = App\Models\BusinessProfile::where('verification_status', 'verified')->where('is_admin_verified', true)->with(['user', 'country'])->limit(6)->get();
                    $userIds = $featuredSuppliers->pluck('user_id');
                    $supplierRatings = [];
                    foreach ($userIds as $userId) {
                        $allReviews = App\Models\ProductUserReview::whereHas('product', function ($query) use ($userId) {
                            $query->where('user_id', $userId)->where('status', 'active')->where('is_admin_verified', true);
                        })->where('status', true)->get();
                        $supplierRatings[$userId] = ['rating' => $allReviews->count() > 0 ? $allReviews->avg('mark') : 0, 'count' => $allReviews->count()];
                    }
                @endphp
                <div class="nav-dropdown hidden absolute left-0 top-full bg-white shadow-2xl border-t-4 border-[#ff0808] w-full z-50">
                    <div class="py-2">
                        <a href="{{ route('featured-suppliers') }}" class="dropdown-link block px-4 py-2 text-xs text-gray-700 hover:bg-[#fff5f5] hover:text-[#ff0808] transition-colors font-semibold" data-subdropdown="suppliers-all">
                            <i class="fas fa-list mr-2"></i>View All Featured Suppliers
                        </a>
                        @forelse($featuredSuppliers as $supplier)
                            <a href="{{ route('business-profile.products', $supplier->id) }}" class="block px-4 py-2 text-xs text-gray-700 hover:bg-[#fff5f5] hover:text-[#ff0808] transition-colors">{{ $supplier->business_name }}</a>
                        @empty
                            <p class="px-4 py-2 text-xs text-gray-500">No featured suppliers available.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="relative nav-item-wrapper flex-1">
                <a href="" class="nav-item block text-xs hover:bg-white/10 transition-colors text-center px-4 py-2.5 border-r border-white/10">New Arrival</a>
                <div class="nav-dropdown hidden absolute left-0 top-full bg-white shadow-2xl border-t-4 border-[#ff0808] w-full z-50">
                    <div class="py-2">
                        <a href="#" class="dropdown-link block px-4 py-2 text-xs text-gray-700 hover:bg-[#fff5f5] hover:text-[#ff0808] transition-colors" data-subdropdown="new-products"><i class="fas fa-box mr-2"></i>By Products</a>
                        <a href="#" class="dropdown-link block px-4 py-2 text-xs text-gray-700 hover:bg-[#fff5f5] hover:text-[#ff0808] transition-colors" data-subdropdown="new-companies"><i class="fas fa-building mr-2"></i>By Company</a>
                    </div>
                </div>
            </div>

            <div class="relative nav-item-wrapper flex-1">
                <a href="" class="nav-item block text-xs hover:bg-white/10 transition-colors text-center px-4 py-2.5 border-r border-white/10">Loadboard</a>
                <div class="nav-dropdown hidden absolute left-0 top-full bg-white shadow-2xl border-t-4 border-[#ff0808] w-full z-50">
                    <div class="py-2">
                        <a href="#" class="dropdown-link block px-4 py-2 text-xs text-gray-700 hover:bg-[#fff5f5] hover:text-[#ff0808] transition-colors" data-subdropdown="available-loads"><i class="fas fa-box mr-2"></i>Available Loads</a>
                        <a href="#" class="dropdown-link block px-4 py-2 text-xs text-gray-700 hover:bg-[#fff5f5] hover:text-[#ff0808] transition-colors" data-subdropdown="available-cars"><i class="fas fa-car mr-2"></i>Available Cars</a>
                    </div>
                </div>
            </div>

            <div class="relative nav-item-wrapper flex-1">
                <a href="" class="nav-item block text-xs hover:bg-white/10 transition-colors text-center px-4 py-2.5 border-r border-white/10">Tradeshow</a>
                <div class="nav-dropdown hidden absolute left-0 top-full bg-white shadow-2xl border-t-4 border-[#ff0808] w-full z-50">
                    <div class="py-2">
                        <a href="#" class="dropdown-link block px-4 py-2 text-xs text-gray-700 hover:bg-[#fff5f5] hover:text-[#ff0808] transition-colors" data-subdropdown="trade-shows"><i class="fas fa-calendar-alt mr-2"></i>Trade Shows</a>
                        <a href="#" class="dropdown-link block px-4 py-2 text-xs text-gray-700 hover:bg-[#fff5f5] hover:text-[#ff0808] transition-colors" data-subdropdown="showrooms"><i class="fas fa-store mr-2"></i>Show Room</a>
                    </div>
                </div>
            </div>

            <div class="relative nav-item-wrapper flex-1">
                <a href="{{ route('rfqs.create') }}" class="nav-item block text-xs hover:bg-white/10 transition-colors text-center px-4 py-2.5">Send RFQs</a>
            </div>
        </div>
    </div>
</div>



@php
    // Featured categories via addon — must be defined FIRST
$featuredCategoryProductIds = \App\Models\AddonUser::whereNotNull('paid_at')
    ->where(function($q) {
        $q->whereNull('ended_at')->orWhere('ended_at', '>', now());
    })
    ->whereHas('addon', function($q) {
        $q->where('locationX', 'Category')
          ->where('locationY', 'featuredlisting');
    })
    ->whereNotNull('product_id')
    ->pluck('product_id')
    ->toArray();

$featuredCategoryIds = \App\Models\Product::whereIn('id', $featuredCategoryProductIds)
    ->where('status', 'active')
    ->where('is_admin_verified', true)
    ->pluck('product_category_id')
    ->unique()
    ->toArray();

$marketplaceCategories = App\Models\ProductCategory::where('status', 'active')
    ->withCount(['products' => function($query) { $query->where('status', 'active')->where('is_admin_verified', true); }])
    ->with(['products' => function ($query) { $query->where('status', 'active')->where('is_admin_verified', true)->with('images')->limit(10); }])
    ->orderBy('name')
    ->get()
    ->sortByDesc(function($category) use ($featuredCategoryIds) {
        return in_array($category->id, $featuredCategoryIds) ? 1 : 0;
    })
    ->values();
@endphp

{{-- HERO SECTION --}}
<div class="relative overflow-hidden hero-section" id="heroSection">

    {{-- Background slides --}}
    <div class="hero-slides-container absolute inset-0 z-0" id="heroSlidesContainer">
        @foreach($heroImages as $i => $imgUrl)
            <div class="hero-slide absolute inset-0 bg-cover bg-center"
                 style="background-image: url('{{ $imgUrl }}'); opacity: {{ $i === 0 ? '1' : '0' }}; z-index: {{ $i === 0 ? '1' : '0' }};"
                 data-index="{{ $i }}"></div>
        @endforeach
        <div class="absolute inset-0 bg-gradient-to-b from-black/25 via-black/30 to-black/50 z-10"></div>
    </div>

    {{-- Dots --}}
    <div class="hero-dots-wrapper absolute bottom-4 left-1/2 -translate-x-1/2 z-30 flex gap-2" id="heroDotsWrapper">
        @foreach($heroImages as $i => $imgUrl)
            <button class="hero-dot w-2 h-2 rounded-full transition-all duration-300 {{ $i === 0 ? 'bg-white w-6' : 'bg-white/50' }}"
                    data-dot="{{ $i }}"></button>
        @endforeach
    </div>

    {{-- Arrows --}}
    <button id="heroPrev" class="absolute left-3 top-1/2 -translate-y-1/2 z-30 w-8 h-8 bg-black/30 hover:bg-black/60 text-white rounded-full flex items-center justify-center transition-all {{ $slideCount <= 1 ? 'hidden' : '' }}">
        <i class="fas fa-chevron-left text-xs"></i>
    </button>
    <button id="heroNext" class="absolute right-3 top-1/2 -translate-y-1/2 z-30 w-8 h-8 bg-black/30 hover:bg-black/60 text-white rounded-full flex items-center justify-center transition-all {{ $slideCount <= 1 ? 'hidden' : '' }}">
        <i class="fas fa-chevron-right text-xs"></i>
    </button>

    {{-- Content --}}
    <div class="container mx-auto px-3 sm:px-4 md:px-6 relative z-20">
        <div class="flex gap-0 relative flex-col lg:flex-row">

            {{-- Left Sidebar --}}
            <div id="categorySidebar" class="w-full lg:w-72 flex-shrink-0 relative z-30 mb-4 lg:mb-0 hidden lg:block" style="margin-top: 15px; margin-bottom: 15px;">
                <div class="bg-white shadow-xl overflow-hidden rounded">
                    <div class="px-3 py-2 border-b border-gray-200">
                        <button class="flex items-center justify-between flex-1 text-gray-800 font-semibold text-sm hover:text-[#ff0808]">
                            <span>All Categories</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                    </div>
                    <div class="overflow-y-auto" style="max-height: 380px;">
                        @foreach($marketplaceCategories as $category)
                            <div class="relative">
                                @php $isFeaturedCat = in_array($category->id, $featuredCategoryIds); @endphp
<button class="category-item w-full text-left px-3 py-2.5 transition-colors flex items-center gap-2 border-b border-gray-100
               {{ $isFeaturedCat ? 'bg-[#fff5f5] hover:bg-[#ffe8e8] border-l-2 border-l-[#ff0808]' : 'hover:bg-[#fff5f5]' }}"
        data-category-id="{{ $category->id }}"
        data-category-slug="{{ $category->slug ?? \Illuminate\Support\Str::slug($category->name) }}"
        data-category-name="{{ $category->name }}">
    <span class="text-gray-600 font-semibold text-sm w-4">{{ $category->products_count }}</span>
    <span class="text-gray-700 text-sm flex-1">{{ $category->name }}</span>
    @if($isFeaturedCat)
        <span class="flex-shrink-0 text-[8px] font-bold text-[#ff0808] bg-red-50 border border-red-200 px-1 py-0.5 rounded">
            ⭐ Featured
        </span>
    @endif
</button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Main Hero Content --}}
            <div class="flex-1 relative min-h-[280px] sm:min-h-[320px] md:min-h-[380px] lg:min-h-[450px]">
                <div class="relative z-20 h-full flex items-center px-2 sm:px-4 md:px-6 lg:px-10 py-4 sm:py-6 md:py-8 lg:py-10">
                    <div class="w-full max-w-[750px]">
                    <h1 id="heroTitle" class="text-lg sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl font-black text-white leading-tight mb-2 sm:mb-3 uppercase drop-shadow-lg">
                        {{ $defaultTitle }}
                    </h1>
                    <p id="heroSubtitle" class="text-xs sm:text-sm md:text-base text-white/95 mb-3 sm:mb-4 drop-shadow-md">
                        {{ $defaultSubtitle }}
                    </p>

                        <div class="mb-3 lg:hidden">
                            <button id="showCategoriesBtn" class="w-full px-3 sm:px-4 md:px-5 py-2 sm:py-2.5 bg-white hover:bg-gray-100 text-[#ff0808] font-bold rounded text-[10px] sm:text-xs md:text-sm shadow-lg transition-all text-center flex items-center justify-center gap-2">
                                <i class="fas fa-th-large"></i><span>Show Categories</span>
                            </button>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 mb-3 sm:mb-4 md:mb-6">
                            <a href="{{ route('buyer.compare.index') }}" class="px-3 sm:px-4 md:px-5 py-2 sm:py-2.5 bg-[#ff0808] hover:bg-[#e00707] text-white font-bold rounded text-[10px] sm:text-xs md:text-sm shadow-lg transition-all text-center whitespace-nowrap">
                                Join Free to Source Products
                            </a>
                            <a href="{{ route('auth.register') }}" class="px-3 sm:px-4 md:px-5 py-2 sm:py-2.5 bg-green-600 hover:bg-green-700 text-white font-bold rounded text-[10px] sm:text-xs md:text-sm shadow-lg transition-all text-center whitespace-nowrap">
                                List Your Company
                            </a>
                        </div>

                        @php
                            $verifiedSuppliersCount = App\Models\BusinessProfile::where('verification_status', 'verified')->where('is_admin_verified', true)->count();
                            $totalTransactions = App\Models\Order::where('status', 'completed')->count();
                        @endphp
                        <div class="flex flex-col sm:flex-row gap-0 mb-3 sm:mb-4 md:mb-6 bg-white/40 backdrop-blur-sm rounded-md overflow-hidden w-full sm:w-fit">
                            <div class="flex items-center gap-1.5 sm:gap-2 text-[9px] sm:text-[10px] md:text-xs px-1.5 sm:px-2 md:px-2.5 py-1.5 hover:bg-white/60 transition-colors border-b sm:border-b-0 sm:border-r border-white/30">
                                <div class="w-3.5 h-3.5 sm:w-4 sm:h-4 md:w-5 md:h-5 bg-teal-500 rounded-full flex items-center justify-center flex-shrink-0"><i class="fas fa-check text-white text-[7px] sm:text-[8px] md:text-[10px]"></i></div>
                                <span class="text-gray-900 font-medium truncate">{{ number_format($verifiedSuppliersCount) }}+ Verified</span>
                            </div>
                            <div class="flex items-center gap-1.5 sm:gap-2 text-[9px] sm:text-[10px] md:text-xs px-1.5 sm:px-2 md:px-2.5 py-1.5 hover:bg-white/60 transition-colors border-b sm:border-b-0 sm:border-r border-white/30">
                                <div class="w-3.5 h-3.5 sm:w-4 sm:h-4 md:w-5 md:h-5 bg-teal-500 rounded-full flex items-center justify-center flex-shrink-0"><i class="fas fa-shield-alt text-white text-[7px] sm:text-[8px] md:text-[10px]"></i></div>
                                <span class="text-gray-900 font-medium truncate">{{ number_format($totalTransactions) }}+ Secure</span>
                            </div>
                            <div class="flex items-center gap-1.5 sm:gap-2 text-[9px] sm:text-[10px] md:text-xs px-1.5 sm:px-2 md:px-2.5 py-1.5 hover:bg-white/60 transition-colors">
                                <div class="w-3.5 h-3.5 sm:w-4 sm:h-4 md:w-5 md:h-5 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0"><i class="fas fa-globe text-white text-[7px] sm:text-[8px] md:text-[10px]"></i></div>
                                <span class="text-gray-900 font-medium truncate">Global Network</span>
                            </div>
                        </div>

                        <div class="w-full max-w-[700px]">
                            <form action="{{ route('global.search') }}" method="GET" class="bg-white shadow-xl p-1.5 sm:p-2 md:p-2.5 flex flex-col sm:flex-row gap-1.5 sm:gap-2 items-stretch rounded">
                                <select name="type" class="px-2 sm:px-3 py-1.5 sm:py-2 bg-white border border-gray-300 text-[9px] sm:text-[10px] md:text-xs text-gray-700 hover:bg-gray-50 rounded flex-shrink-0">
                                    <option value="products">Products</option>
                                    <option value="suppliers">Suppliers</option>
                                    <option value="rfqs">RFQs</option>
                                </select>
                                <input type="text" name="query" placeholder="Search for products..." class="flex-1 px-2 sm:px-3 py-1.5 sm:py-2 border border-gray-300 text-[9px] sm:text-[10px] md:text-xs focus:outline-none focus:border-[#ff0808] rounded min-w-0">
                                <button type="submit" class="px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 bg-[#ff0808] hover:bg-[#e00707] text-white font-semibold text-[10px] sm:text-xs md:text-sm flex items-center justify-center rounded">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- <div class="hidden xl:block absolute bottom-0 right-0 pointer-events-none" style="width:320px;height:580px;top:-180px;z-index:15;">
                    <img src="{{ asset('girlimage.png') }}" alt="African professional" class="w-full h-full object-contain object-bottom" style="filter: drop-shadow(0 10px 25px rgba(0,0,0,0.15));">
                </div> --}}
                <div class="hidden xl:block absolute bottom-0 right-0 pointer-events-none" style="width:420px;height:620px;top:-160px;z-index:15;" id="heroRightSlides">
                @foreach($heroImages as $i => $imgUrl)
                    <img src="{{ $imgUrl }}" alt="slide {{ $i }}"
                        class="hero-right-slide absolute inset-0 w-full h-full object-contain object-bottom"
                        style="opacity: {{ $i === 0 ? '1' : '0' }}; transition: none; filter: drop-shadow(0 10px 30px rgba(0,0,0,0.25));"
                        data-index="{{ $i }}">
                @endforeach
            </div>
            </div>
        </div>
    </div>
</div>

{{-- Mobile Categories Modal --}}
<div id="categoriesModal" class="fixed inset-0 bg-white z-[99999] hidden overflow-y-auto">
    <div class="min-h-screen">
        <div class="sticky top-0 bg-[#ff0808] text-white px-4 py-3 flex items-center justify-between shadow-lg z-10">
            <h2 class="text-lg font-bold">All Categories</h2>
            <button id="closeCategoriesModal" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-white/20 transition-colors"><i class="fas fa-times text-xl"></i></button>
        </div>
        <div class="p-4">
            @foreach($marketplaceCategories as $category)
                <button class="modal-category-item w-full text-left px-4 py-3 hover:bg-[#fff5f5] transition-colors flex items-center justify-between border-b border-gray-200"
                        data-category-id="{{ $category->id }}" data-category-slug="{{ $category->slug ?? \Illuminate\Support\Str::slug($category->name) }}" data-category-name="{{ $category->name }}">
                    <div class="flex items-center gap-3">
                        <span class="text-[#ff0808] font-bold text-base w-8">{{ $category->products_count }}</span>
                        <span class="text-gray-900 font-medium text-base">{{ $category->name }}</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                </button>
            @endforeach
        </div>
    </div>
</div>

{{-- Full-Width Subdropdown --}}
<div id="categorySubDropdown" class="hidden fixed bg-white shadow-2xl border-t-4 border-[#ff0808] z-[9999] left-0 right-0" style="top:0;">
    <div class="container mx-auto px-4 sm:px-6 py-4 sm:py-6">
        <div class="flex items-start justify-between mb-4">
            <h3 id="subDropdownTitle" class="text-base sm:text-lg font-bold text-gray-900"></h3>
            <button id="closeSubDropdown" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times text-lg sm:text-xl"></i></button>
        </div>
        <div id="subDropdownContent" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 sm:gap-4"></div>
        <div class="mt-4 sm:mt-6 pt-3 sm:pt-4 border-t">
            <a href="#" id="viewAllLink" class="text-[#ff0808] hover:text-[#e00707] font-semibold text-xs sm:text-sm">View All →</a>
        </div>
    </div>
</div>

<!-- Mobile Slide-Out Menu -->
<div id="mobileMenuOverlay" class="fixed inset-0 bg-black/50 z-[999998] hidden lg:hidden" onclick="closeMobileMenu()"></div>

<div id="mobileMenuDrawer" class="fixed top-0 right-0 h-full w-72 bg-white z-[999999] transform translate-x-full transition-transform duration-300 ease-in-out lg:hidden shadow-2xl flex flex-col">

    {{-- Header --}}
    <div class="bg-[#1a2942] px-4 py-4 flex items-center justify-between flex-shrink-0">
        <img src="{{ asset('mainlogo.png') }}" alt="Afrisellers" class="h-6">
        <button onclick="closeMobileMenu()" class="text-white/70 hover:text-white">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>

    {{-- Auth Section --}}
    <div class="px-4 py-4 border-b border-gray-100 flex-shrink-0">
        @auth
            @php
                $mUser = auth()->user();
                $mDashRoute = null;
                $isAdmin = $mUser->roles()->where('roles.id', 1)->where('roles.slug', 'admin')->exists();
                if ($isAdmin) { $mDashRoute = route('admin.dashboard.home'); }
                else {
                    $isRA = $mUser->roles()->where('roles.slug', 'regional_admin')->exists();
                    if ($isRA) { $mDashRoute = route('regional.dashboard.home'); }
                    else {
                        $isCA = $mUser->roles()->where('roles.slug', 'country_admin')->exists();
                        if ($isCA) { $mDashRoute = route('country.dashboard.home'); }
                        else {
                            $isAgent = $mUser->roles()->where('roles.slug', 'agent')->exists();
                            if ($isAgent) { $mDashRoute = route('agent.dashboard.home'); }
                            else {
                                $mVendor = App\Models\Vendor\Vendor::where('user_id', $mUser->id)->first();
                                $mDashRoute = $mVendor ? route('vendor.dashboard.home') : route('buyer.dashboard.home');
                            }
                        }
                    }
                }
            @endphp
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-[#ff0808] flex items-center justify-center flex-shrink-0">
                    <span class="text-sm font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-bold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                @if($mDashRoute)
                    <a href="{{ $mDashRoute }}"
                       class="flex-1 text-center py-2 bg-[#1a2942] text-white text-xs font-bold rounded-lg hover:bg-[#0f1c2e] transition-colors">
                        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                    </a>
                @endif
                <form action="{{ route('auth.logout') }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit"
                            class="w-full py-2 bg-red-50 text-[#ff0808] border border-red-100 text-xs font-bold rounded-lg hover:bg-red-100 transition-colors">
                        <i class="fas fa-sign-out-alt mr-1"></i> Logout
                    </button>
                </form>
            </div>
        @else
            <div class="flex gap-2">
                <a href="{{ route('auth.signin') }}"
                   class="flex-1 text-center py-2.5 bg-[#ff0808] text-white text-xs font-bold rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-sign-in-alt mr-1"></i> Login
                </a>
                <a href="{{ route('auth.register') }}"
                   class="flex-1 text-center py-2.5 bg-[#1a2942] text-white text-xs font-bold rounded-lg hover:bg-[#0f1c2e] transition-colors">
                    <i class="fas fa-user-plus mr-1"></i> Register
                </a>
            </div>
        @endauth
    </div>

    {{-- Nav Links --}}
    <div class="flex-1 overflow-y-auto py-2">
        <p class="px-4 py-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">Navigation</p>

        <a href="{{ route('featured-suppliers') }}"
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-[#fff5f5] hover:text-[#ff0808] transition-colors border-b border-gray-50">
            <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-store text-purple-600 text-xs"></i>
            </div>
            Featured Suppliers
        </a>

        <a href="#"
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-[#fff5f5] hover:text-[#ff0808] transition-colors border-b border-gray-50">
            <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-box-open text-green-600 text-xs"></i>
            </div>
            New Arrivals
        </a>

        <a href="#"
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-[#fff5f5] hover:text-[#ff0808] transition-colors border-b border-gray-50">
            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-truck text-blue-600 text-xs"></i>
            </div>
            Loadboard
        </a>

        <a href="#"
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-[#fff5f5] hover:text-[#ff0808] transition-colors border-b border-gray-50">
            <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-calendar-alt text-amber-600 text-xs"></i>
            </div>
            Tradeshow
        </a>

        <a href="{{ route('rfqs.create') }}"
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-[#fff5f5] hover:text-[#ff0808] transition-colors border-b border-gray-50">
            <div class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-paper-plane text-[#ff0808] text-xs"></i>
            </div>
            Send RFQs
        </a>

        <a href="{{ route('cart.index') }}"
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-[#fff5f5] hover:text-[#ff0808] transition-colors border-b border-gray-50">
            <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-shopping-cart text-orange-600 text-xs"></i>
            </div>
            Cart
        </a>

        <a href="{{ route('livestream') }}"
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-[#fff5f5] hover:text-[#ff0808] transition-colors">
            <div class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-video text-[#ff0808] text-xs"></i>
            </div>
            <span>Live</span>
            <span class="ml-auto flex items-center gap-1 bg-[#ff0808] text-white text-[9px] px-1.5 py-0.5 rounded-full font-bold">
                <span class="w-1 h-1 bg-white rounded-full animate-pulse"></span> LIVE
            </span>
        </a>
    </div>


</div>

@push('scripts')
<script>
// ══════════════════════════════════════════════════════════════════
// HERO SLIDESHOW — exposed as window.HeroSlideshow so country
// switcher can call rebuildSlides / applyCountry from outside
// ══════════════════════════════════════════════════════════════════
window.HeroSlideshow = (function () {

    // ── Config passed from PHP ────────────────────────────────────
    const ANIM_MODE        = '{{ $animationMode }}';
    const AUTO_INTERVAL_MS = {{ $slideInterval }};
    const imagesByCountry    = {!! json_encode($heroImagesByCountry) !!};
    const titlesByCountry    = {!! json_encode($titlesByCountry) !!};
    const subtitlesByCountry = {!! json_encode($subtitlesByCountry) !!};

    // ── Internal state ────────────────────────────────────────────
    const container   = document.getElementById('heroSlidesContainer');
    const dotsWrapper = document.getElementById('heroDotsWrapper');
    const prevBtn     = document.getElementById('heroPrev');
    const nextBtn     = document.getElementById('heroNext');

    let current = 0;
    let timer   = null;

    // ── Helpers ───────────────────────────────────────────────────
    function getSlides() {
        return Array.from(container.querySelectorAll('.hero-slide'));
    }

    function getDots() {
        return Array.from(dotsWrapper.querySelectorAll('.hero-dot'));
    }

    function updateDots(idx) {
        getDots().forEach((d, i) => {
            d.classList.toggle('bg-white',   i === idx);
            d.classList.toggle('w-6',        i === idx);
            d.classList.toggle('bg-white/50', i !== idx);
        });
    }

    function initSlideStyles(slides) {
        slides.forEach((s, i) => {
            s.style.transition = 'none';
            s.style.opacity    = i === 0 ? '1' : '0';
            s.style.zIndex     = i === 0 ? '1' : '0';
            if (ANIM_MODE === 'slide') s.style.transform = i === 0 ? 'translateX(0)' : 'translateX(100%)';
            if (ANIM_MODE === 'flip')  s.style.transform = i === 0 ? 'rotateY(0deg)' : 'rotateY(90deg)';
        });
    }

    // ── Core goTo ─────────────────────────────────────────────────
function goTo(next) {
    const slides = getSlides();
    const total  = slides.length;
    if (total === 0) return;

    const prev    = current;
    current       = ((next % total) + total) % total;
    const prevEl  = slides[prev];
    const nextEl  = slides[current];
    const dur     = ANIM_MODE === 'none' ? '0ms' : '800ms';
    const ease    = 'cubic-bezier(0.77,0,0.175,1)';

    // Sync right-side image slides
    const rightSlides = Array.from(document.querySelectorAll('.hero-right-slide'));
    rightSlides.forEach((s, i) => {
        s.style.transition = `opacity ${dur} ${ease}`;
        s.style.opacity    = i === current ? '1' : '0';
    });

        if (ANIM_MODE === 'fade' || ANIM_MODE === 'none') {
            prevEl.style.transition = `opacity ${dur} ${ease}`;
            nextEl.style.transition = `opacity ${dur} ${ease}`;
            nextEl.style.zIndex = '2'; prevEl.style.zIndex = '1';
            nextEl.style.opacity = '1'; prevEl.style.opacity = '0';

        } else if (ANIM_MODE === 'slide') {
            const dir = next >= prev ? 1 : -1;
            nextEl.style.transition = 'none';
            nextEl.style.transform  = `translateX(${dir * 100}%)`;
            nextEl.style.opacity    = '1';
            nextEl.style.zIndex     = '2';
            prevEl.style.zIndex     = '1';
            requestAnimationFrame(() => requestAnimationFrame(() => {
                nextEl.style.transition = `transform ${dur} ${ease}`;
                prevEl.style.transition = `transform ${dur} ${ease}`;
                nextEl.style.transform  = 'translateX(0)';
                prevEl.style.transform  = `translateX(${-dir * 100}%)`;
            }));

        } else if (ANIM_MODE === 'flip') {
            prevEl.style.transition = `transform ${dur} ${ease}, opacity ${dur}`;
            nextEl.style.transition = `transform ${dur} ${ease}, opacity ${dur}`;
            nextEl.style.zIndex = '2'; prevEl.style.zIndex = '1';
            prevEl.style.transform = 'rotateY(-90deg)'; prevEl.style.opacity = '0';
            nextEl.style.transform = 'rotateY(0deg)';  nextEl.style.opacity = '1';
        }

        updateDots(current);
    }

    // ── Auto-timer ────────────────────────────────────────────────
    function startTimer() {
        clearInterval(timer);
        if (getSlides().length > 1) {
            timer = setInterval(() => goTo(current + 1), AUTO_INTERVAL_MS);
        }
    }

    function resetTimer() { startTimer(); }

    // ── Rebuild slides from a new url array ───────────────────────
    function rebuildSlides(urls) {
        if (!urls || !urls.length) return;

        clearInterval(timer);
        current = 0;

        // Remove old slides (keep the overlay div)
        container.querySelectorAll('.hero-slide').forEach(s => s.remove());

        // Create new slides
        urls.forEach((url, i) => {
            const div = document.createElement('div');
            div.className = 'hero-slide absolute inset-0 bg-cover bg-center';
            div.style.backgroundImage = `url('${url}')`;
            div.style.opacity = i === 0 ? '1' : '0';
            div.style.zIndex  = i === 0 ? '1' : '0';
            div.style.transition = 'none';
            div.dataset.index = i;
            // Insert before the overlay (last child)
            container.insertBefore(div, container.lastElementChild);
        });

        // Rebuild dots
        dotsWrapper.innerHTML = '';
        urls.forEach((_, i) => {
            const btn = document.createElement('button');
            btn.className = `hero-dot w-2 h-2 rounded-full transition-all duration-300 ${i === 0 ? 'bg-white w-6' : 'bg-white/50'}`;
            btn.dataset.dot = i;
            btn.addEventListener('click', () => { goTo(i); resetTimer(); });
            dotsWrapper.appendChild(btn);
        });

    //     // Show/hide arrows
    //     const multiSlide = urls.length > 1;
    //     if (prevBtn) prevBtn.classList.toggle('hidden', !multiSlide);
    //     if (nextBtn) nextBtn.classList.toggle('hidden', !multiSlide);

    //     startTimer();
    // }
    // Show/hide arrows
    const multiSlide = urls.length > 1;
    if (prevBtn) prevBtn.classList.toggle('hidden', !multiSlide);
    if (nextBtn) nextBtn.classList.toggle('hidden', !multiSlide);

    // Rebuild right-side slides
    const rightContainer = document.getElementById('heroRightSlides');
    if (rightContainer) {
        rightContainer.querySelectorAll('.hero-right-slide').forEach(s => s.remove());
        urls.forEach((url, i) => {
            const img = document.createElement('img');
            img.src = url;
            img.alt = `slide ${i}`;
            img.className = 'hero-right-slide absolute inset-0 w-full h-full object-contain object-bottom';
            img.style.opacity    = i === 0 ? '1' : '0';
            img.style.transition = 'none';
            img.style.filter     = 'drop-shadow(0 10px 30px rgba(0,0,0,0.25))';
            img.dataset.index    = i;
            rightContainer.appendChild(img);
        });
    }

    startTimer();
}

    // ── Apply country: pick country-specific or fall back global ──
function applyCountry(id) {
    const key  = parseInt(id) || 0;

    // Swap background images
    const urls = imagesByCountry[key] || imagesByCountry[0] || null;
    if (urls) rebuildSlides(urls);

    // Swap title (country-specific → global fallback → leave as-is)
    const titleEl = document.getElementById('heroTitle');
    if (titleEl) {
        titleEl.textContent = titlesByCountry[key] || titlesByCountry[0] || titleEl.textContent;
    }

    // Swap subtitle
    const subtitleEl = document.getElementById('heroSubtitle');
    if (subtitleEl) {
        subtitleEl.textContent = subtitlesByCountry[key] || subtitlesByCountry[0] || subtitleEl.textContent;
    }
}

    // ── Boot ──────────────────────────────────────────────────────
    function init() {
        const slides = getSlides();
        initSlideStyles(slides);

        // Wire arrows
        if (prevBtn) prevBtn.addEventListener('click', () => { goTo(current - 1); resetTimer(); });
        if (nextBtn) nextBtn.addEventListener('click', () => { goTo(current + 1); resetTimer(); });

        // Wire dots
        getDots().forEach((dot, i) => {
            dot.addEventListener('click', () => { goTo(i); resetTimer(); });
        });

        // Touch swipe
        let touchStartX = 0;
        const heroEl = document.getElementById('heroSection');
        heroEl.addEventListener('touchstart', e => { touchStartX = e.changedTouches[0].screenX; }, { passive: true });
        heroEl.addEventListener('touchend',   e => {
            const diff = touchStartX - e.changedTouches[0].screenX;
            if (Math.abs(diff) > 50) { goTo(diff > 0 ? current + 1 : current - 1); resetTimer(); }
        }, { passive: true });

        startTimer();

        // Restore saved country from localStorage
        const saved = localStorage.getItem('uiselected_country');
        if (saved) {
            // Set the dropdown value
            const sel = document.getElementById('country');
            if (sel) sel.value = saved;
            applyCountry(saved);
        }
    }

    // Run after DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Public API
    return { applyCountry, rebuildSlides };

})();

// ── Persist country selection ─────────────────────────────────────
// ── Persist country selection ─────────────────────────────────────
function saveSelectedCountry(id) {
    localStorage.setItem('uiselected_country', id);
}

// ══════════════════════════════════════════════════════════════════
// CURRENCY SWITCHER — live rates via open.er-api.com (free, no key)
// ══════════════════════════════════════════════════════════════════
(function () {

    // Currency metadata: code → { name, symbol }
    // Rates come from the API, not hardcoded here
    const CURRENCY_META = {
        USD: { name: 'US Dollar',           symbol: '$'    },
        EUR: { name: 'Euro',                symbol: '€'    },
        GBP: { name: 'British Pound',       symbol: '£'    },
        RWF: { name: 'Rwandan Franc',       symbol: 'Fr'   },
        KES: { name: 'Kenyan Shilling',     symbol: 'KSh'  },
        UGX: { name: 'Ugandan Shilling',    symbol: 'USh'  },
        TZS: { name: 'Tanzanian Shilling',  symbol: 'TSh'  },
        ETB: { name: 'Ethiopian Birr',      symbol: 'Br'   },
        NGN: { name: 'Nigerian Naira',      symbol: '₦'    },
        GHS: { name: 'Ghanaian Cedi',       symbol: 'GH₵'  },
        ZAR: { name: 'South African Rand',  symbol: 'R'    },
        EGP: { name: 'Egyptian Pound',      symbol: 'E£'   },
        MAD: { name: 'Moroccan Dirham',     symbol: 'د.م'  },
        XOF: { name: 'CFA Franc (West)',    symbol: 'CFA'  },
        XAF: { name: 'CFA Franc (Central)', symbol: 'CFA'  },
        ZMW: { name: 'Zambian Kwacha',      symbol: 'ZK'   },
        MWK: { name: 'Malawian Kwacha',     symbol: 'MK'   },
        BIF: { name: 'Burundian Franc',     symbol: 'Fr'   },
        DZD: { name: 'Algerian Dinar',      symbol: 'دج'   },
        AED: { name: 'UAE Dirham',          symbol: 'د.إ'  },
        CNY: { name: 'Chinese Yuan',        symbol: '¥'    },
        INR: { name: 'Indian Rupee',        symbol: '₹'    },
        JPY: { name: 'Japanese Yen',        symbol: '¥'    },
        CAD: { name: 'Canadian Dollar',     symbol: 'CA$'  },
        AUD: { name: 'Australian Dollar',   symbol: 'A$'   },
        CHF: { name: 'Swiss Franc',         symbol: 'CHF'  },
        BRL: { name: 'Brazilian Real',      symbol: 'R$'   },
        MXN: { name: 'Mexican Peso',        symbol: '$'    },
        MUR: { name: 'Mauritian Rupee',     symbol: '₨'    },
        TND: { name: 'Tunisian Dinar',      symbol: 'د.ت'  },
        SOS: { name: 'Somali Shilling',     symbol: 'Sh'   },
        SDG: { name: 'Sudanese Pound',      symbol: '£'    },
        ZWL: { name: 'Zimbabwean Dollar',   symbol: 'Z$'   },
        CDF: { name: 'Congolese Franc',     symbol: 'FC'   },
        GMD: { name: 'Gambian Dalasi',      symbol: 'D'    },
        GNF: { name: 'Guinean Franc',       symbol: 'Fr'   },
    };

    // Storage keys
    const KEY_CODE       = 'ui_currency_code';
    const KEY_RATE       = 'ui_currency_usd_rate';
    const KEY_SYMBOL     = 'ui_currency_symbol';
    const KEY_RATES_CACHE = 'ui_currency_rates_cache';
    const KEY_RATES_TIME  = 'ui_currency_rates_time';
    const CACHE_TTL_MS   = 6 * 60 * 60 * 1000; // 6 hours

    // Live rates loaded from API
    let liveRates = {};  // code → rate (1 USD = X)

    function getSavedCode() {
        return localStorage.getItem(KEY_CODE) || 'USD';
    }

    function saveCurrency(code, rate, symbol) {
        localStorage.setItem(KEY_CODE,   code);
        localStorage.setItem(KEY_RATE,   rate);
        localStorage.setItem(KEY_SYMBOL, symbol);
    }

    function updateLabel(code) {
        const lbl = document.getElementById('currencyLabel');
        if (lbl) lbl.textContent = code;
    }

    function buildList() {
        const list = document.getElementById('currencyList');
        if (!list) return;
        const active = getSavedCode();

        // Show currencies we have meta for, in order of CURRENCY_META keys
        const codes = Object.keys(CURRENCY_META).filter(c => liveRates[c] !== undefined);

        if (!codes.length) {
            list.innerHTML = '<div class="px-3 py-4 text-xs text-gray-400 text-center">Rates unavailable</div>';
            return;
        }

        list.innerHTML = codes.map(code => {
            const meta = CURRENCY_META[code];
            const rate = liveRates[code] ?? '—';
            const isActive = code === active;
            return `
            <button onclick="window.CurrencySwitcher.select('${code}')"
                class="w-full text-left flex items-center justify-between px-3 py-2 transition-colors text-xs border-b border-gray-50
                       ${isActive ? 'bg-[#fff5f5] text-[#ff0808] font-bold' : 'text-gray-700 hover:bg-[#fff5f5] hover:text-[#ff0808]'}">
                <span class="flex items-center gap-2">
                    <span class="w-6 text-center font-mono font-bold text-[10px] ${isActive ? 'text-[#ff0808]' : 'text-gray-400'}">${meta.symbol}</span>
                    <span class="flex-1">${meta.name}</span>
                </span>
                <span class="font-mono text-[10px] ${isActive ? 'text-[#ff0808]' : 'text-gray-400'}">${code}</span>
            </button>`;
        }).join('');
    }

    function select(code) {
        const meta = CURRENCY_META[code];
        const rate = liveRates[code];
        if (!meta || rate === undefined) return;

        saveCurrency(code, rate, meta.symbol);
        updateLabel(code);
        buildList();
        closeDropdown();

        window.dispatchEvent(new CustomEvent('currencyChanged', {
            detail: { code, rateToUSD: rate, symbol: meta.symbol }
        }));
    }

    function closeDropdown() {
        const dd  = document.getElementById('currencyDropdown');
        const chv = document.getElementById('currencyChevron');
        if (dd)  dd.classList.add('hidden');
        if (chv) chv.style.transform = 'rotate(0deg)';
    }

    // Fetch from open.er-api.com — completely free, no API key
    async function fetchRates() {
        // Check cache first
        const cachedRates = localStorage.getItem(KEY_RATES_CACHE);
        const cachedTime  = parseInt(localStorage.getItem(KEY_RATES_TIME) || '0');

        if (cachedRates && (Date.now() - cachedTime < CACHE_TTL_MS)) {
            liveRates = JSON.parse(cachedRates);
            buildList();
            restoreSelection();
            return;
        }

        const loader = document.getElementById('currencyLoadingIndicator');
        if (loader) loader.classList.remove('hidden');

        try {
            const res  = await fetch('https://open.er-api.com/v6/latest/USD');
            const data = await res.json();

            if (data.result === 'success' && data.rates) {
                liveRates = data.rates;
                // Cache the rates
                localStorage.setItem(KEY_RATES_CACHE, JSON.stringify(liveRates));
                localStorage.setItem(KEY_RATES_TIME, Date.now());
            } else {
                throw new Error('Bad response');
            }
        } catch (err) {
            console.warn('[CurrencySwitcher] Live fetch failed, using fallback rates.', err);
            // Fallback hardcoded rates — only used if API is down
            liveRates = {
                USD:1, EUR:0.92, GBP:0.79, RWF:1320, KES:129, UGX:3750,
                TZS:2650, ETB:57, NGN:1600, GHS:15.5, ZAR:18.5, EGP:48,
                MAD:9.9, XOF:602, XAF:602, ZMW:27, MWK:1730, BIF:2870,
                DZD:134, AED:3.67, CNY:7.25, INR:83, JPY:149, CAD:1.36,
                AUD:1.53, CHF:0.89, BRL:4.97, MXN:17.2, MUR:45.5,
                TND:3.11, CDF:2760, GMD:67, GNF:8600,
            };
        } finally {
            if (loader) loader.classList.add('hidden');
            buildList();
            restoreSelection();
        }
    }

    function restoreSelection() {
        const saved = getSavedCode();
        if (liveRates[saved]) {
            const meta = CURRENCY_META[saved] || { symbol: saved };
            saveCurrency(saved, liveRates[saved], meta.symbol);
            updateLabel(saved);
        }
    }

function init() {
        updateLabel(getSavedCode());

        // Close when clicking outside both the button wrapper and the dropdown
        document.addEventListener('mousedown', function (e) {
            const wrapper = document.getElementById('currencySwitcherWrapper');
            const dd      = document.getElementById('currencyDropdown');
            if (!dd || dd.classList.contains('hidden')) return;
            if (wrapper && wrapper.contains(e.target)) return;
            if (dd.contains(e.target)) return;
            closeDropdown();
        });

        // Fetch live rates
        fetchRates();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Public API
    window.CurrencySwitcher = { select, getSavedCode, fetchRates };

})();

function toggleCurrencyDropdown() {
    const btn = document.getElementById('currencyBtn');
    const dd  = document.getElementById('currencyDropdown');
    const chv = document.getElementById('currencyChevron');
    if (!dd || !btn) return;

    const isHidden = dd.classList.contains('hidden');

    if (isHidden) {
        // Position the fixed dropdown below the button
        const rect = btn.getBoundingClientRect();
        dd.style.top  = (rect.bottom + 6) + 'px';
        // Align right edge with button right edge, but keep on screen
        let left = rect.right - 224; // 224 = w-56
        if (left < 8) left = 8;
        dd.style.left = left + 'px';
        dd.classList.remove('hidden');
        if (chv) chv.style.transform = 'rotate(180deg)';
    } else {
        dd.classList.add('hidden');
        if (chv) chv.style.transform = 'rotate(0deg)';
    }
}

function saveSelectedCountry(id) {
    localStorage.setItem('uiselected_country', id);
    // Re-sort categories: featured ones for selected country float to top
    applyCategoryFeaturedOrder(id);
}

function applyCategoryFeaturedOrder(countryId) {
    // Featured product IDs per country from PHP
    const featuredIds = {!! json_encode(
        \App\Models\AddonUser::whereNotNull('paid_at')
            ->where(function($q) { $q->whereNull('ended_at')->orWhere('ended_at', '>', now()); })
            ->whereHas('addon', function($q) { $q->where('locationX', 'Category')->where('locationY', 'featuredlisting'); })
            ->whereNotNull('product_id')
            ->with(['product'])
            ->get()
            ->groupBy(fn($au) => optional($au->product)->country_id ?? 0)
            ->map(fn($group) => $group->pluck('product.product_category_id')->unique()->values())
    ) !!};

    const id = parseInt(countryId) || 0;
    const featured = featuredIds[id] || featuredIds[0] || [];

    document.querySelectorAll('.category-item').forEach(btn => {
        const catId = parseInt(btn.dataset.categoryId);
        if (featured.includes(catId)) {
            btn.classList.add('bg-[#fff5f5]', 'border-l-2', 'border-l-[#ff0808]');
        } else {
            btn.classList.remove('bg-[#fff5f5]', 'border-l-2', 'border-l-[#ff0808]');
        }
    });
}

// ══════════════════════════════════════════════════════════════════
// PAGE JS — nav, categories, search
// ══════════════════════════════════════════════════════════════════
function openMobileMenu() {
    document.getElementById('mobileMenuDrawer').classList.remove('translate-x-full');
    document.getElementById('mobileMenuOverlay').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeMobileMenu() {
    document.getElementById('mobileMenuDrawer').classList.add('translate-x-full');
    document.getElementById('mobileMenuOverlay').classList.add('hidden');
    document.body.style.overflow = '';
}

document.addEventListener('DOMContentLoaded', function () {

    // Mobile categories modal
    const showCategoriesBtn    = document.getElementById('showCategoriesBtn');
    const categoriesModal      = document.getElementById('categoriesModal');
    const closeCategoriesModal = document.getElementById('closeCategoriesModal');
    if (showCategoriesBtn)    showCategoriesBtn.addEventListener('click',    () => { categoriesModal.classList.remove('hidden'); document.body.style.overflow = 'hidden'; });
    if (closeCategoriesModal) closeCategoriesModal.addEventListener('click', () => { categoriesModal.classList.add('hidden');    document.body.style.overflow = ''; });

    // Category data for subdropdown
    const categoryData = {
        @foreach($marketplaceCategories as $category)
        '{{ $category->id }}': {
            name: '{{ $category->name }}', slug: '{{ $category->slug }}',
            products: [
                @foreach($category->products->take(10) as $product)
                { id: {{ $product->id }}, name: '{{ addslashes($product->name) }}', slug: '{{ $product->slug }}', price: '{{ number_format($product->base_price, 2) }}', currency: '{{ $product->currency }}', image: '{{ ($product->images->where("is_primary", true)->first() ?? $product->images->first())?->image_url ?? "" }}', categoryName: '{{ $product->productCategory->name ?? "Uncategorized" }}' },
                @endforeach
            ]
        },
        @endforeach
    };

    const suppliersData = [
        @foreach($featuredSuppliers as $supplier)
        { id: {{ $supplier->id }}, name: '{{ addslashes($supplier->business_name) }}', rating: {{ $supplierRatings[$supplier->user_id]['rating'] ?? 0 }}, reviewsCount: {{ $supplierRatings[$supplier->user_id]['count'] ?? 0 }} },
        @endforeach
    ];

    // Scroll — hide topbar
    const topBar     = document.getElementById('topBar');
    const mainHeader = document.getElementById('mainHeader');
    const navBar     = document.getElementById('navBar');
    window.addEventListener('scroll', function () {
        if (window.pageYOffset > 50) {
            topBar.style.transform = 'translateY(-100%)';
            if (navBar) navBar.style.top = mainHeader.offsetHeight + 'px';
        } else {
            topBar.style.transform = 'translateY(0)';
            if (navBar) navBar.style.top = '0';
        }
    });

    // Nav dropdowns hover
    document.querySelectorAll('.nav-item-wrapper').forEach(wrapper => {
        const navItem     = wrapper.querySelector('.nav-item');
        const navDropdown = wrapper.querySelector('.nav-dropdown');
        if (!navDropdown) return;
        let hideTimeout;
        const show = () => { clearTimeout(hideTimeout); document.querySelectorAll('.nav-dropdown').forEach(d => { if (d !== navDropdown) d.classList.add('hidden'); }); navDropdown.classList.remove('hidden'); };
        const hide = () => { hideTimeout = setTimeout(() => navDropdown.classList.add('hidden'), 150); };
        navItem.addEventListener('mouseenter', show);
        navItem.addEventListener('mouseleave', hide);
        navDropdown.addEventListener('mouseenter', () => clearTimeout(hideTimeout));
        navDropdown.addEventListener('mouseleave', hide);
    });

    // Subdropdown
    const subDropdown      = document.getElementById('categorySubDropdown');
    const subDropdownTitle = document.getElementById('subDropdownTitle');
    const subDropdownContent = document.getElementById('subDropdownContent');
    const viewAllLink      = document.getElementById('viewAllLink');
    const closeSubDropdown = document.getElementById('closeSubDropdown');
    const colorClasses     = ['from-green-50 to-green-100','from-blue-50 to-blue-100','from-purple-50 to-purple-100','from-orange-50 to-orange-100','from-red-50 to-red-100','from-yellow-50 to-yellow-100','from-indigo-50 to-indigo-100','from-teal-50 to-teal-100'];

    function positionAndShowDropdown() {
        const nb = document.querySelector('.bg-\\[\\#1a2942\\]');
        subDropdown.style.top = (nb ? nb.getBoundingClientRect().bottom : document.getElementById('mainHeader').getBoundingClientRect().bottom) + 'px';
        subDropdown.classList.remove('hidden');
    }

    function showSuppliersSubdropdown() {
        subDropdownTitle.textContent = 'Featured Suppliers';
        viewAllLink.href = '{{ route("featured-suppliers") }}';
        subDropdownContent.innerHTML = '';
        subDropdownContent.className = 'grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3 sm:gap-4';
        suppliersData.forEach(s => {
            const card = document.createElement('a');
            card.href = `/business-profile/${s.id}/products`;
            card.className = 'group';
            card.innerHTML = `<div class="bg-white border-2 border-gray-200 rounded-lg p-3 hover:border-[#ff0808] hover:shadow-md transition-all text-center"><div class="flex justify-center items-center mx-auto mb-2 w-12 h-12 text-lg font-bold text-gray-700 bg-gray-100 rounded-full">${s.name.substring(0,1).toUpperCase()}</div><h4 class="font-semibold text-gray-900 text-xs group-hover:text-[#ff0808] line-clamp-2">${s.name}</h4>${s.rating > 0 ? `<p class="mt-1 text-[10px] text-gray-600">⭐ ${s.rating.toFixed(1)} (${s.reviewsCount})</p>` : '<p class="mt-1 text-[10px] text-gray-500">No ratings yet</p>'}<span class="inline-block px-2 py-1 mt-2 text-[9px] text-green-700 bg-green-50 rounded border border-green-200">Verified</span></div>`;
            subDropdownContent.appendChild(card);
        });
        positionAndShowDropdown();
    }

    function showCategorySubdropdown(categoryId, categoryName, categorySlug) {
        const data = categoryData[categoryId];
        if (!data) return;
        subDropdownTitle.textContent = categoryName;
        viewAllLink.href = `/category/${categorySlug}`;
        subDropdownContent.innerHTML = '';
        subDropdownContent.className = 'grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 sm:gap-4';
        if (!data.products.length) {
            subDropdownContent.innerHTML = '<div class="col-span-full py-8 text-center"><p class="text-gray-500 text-xs">No products available in this category yet.</p></div>';
        } else {
            data.products.forEach((p, idx) => {
                const cc = colorClasses[idx % colorClasses.length];
                const card = document.createElement('a');
                card.href = `/products/${p.slug}`;
                card.className = 'group';
                card.innerHTML = `<div class="bg-gradient-to-br ${cc} rounded-lg p-3 text-center hover:shadow-lg transition-all">${p.image ? `<img src="${p.image}" alt="${p.name}" class="object-cover mb-2 w-full h-24 rounded-lg">` : '<div class="mb-2 text-3xl">📦</div>'}<h4 class="font-bold text-gray-900 group-hover:text-[#ff0808] text-xs line-clamp-2">${p.name}</h4><p class="mt-1 text-[10px] text-gray-600">${p.price} ${p.currency}</p></div>`;
                subDropdownContent.appendChild(card);
            });
        }
        positionAndShowDropdown();
    }

    document.querySelectorAll('.dropdown-link').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelectorAll('.nav-dropdown').forEach(d => d.classList.add('hidden'));
            const type = this.getAttribute('data-subdropdown');
            if (type === 'suppliers-all') showSuppliersSubdropdown();
            else { subDropdownTitle.textContent = type; subDropdownContent.innerHTML = '<div class="col-span-full py-8 text-center text-gray-400 text-sm">Content loading...</div>'; positionAndShowDropdown(); }
        });
    });

document.querySelectorAll('.category-item').forEach(item => {
    item.addEventListener('click', function (e) {
        e.preventDefault();
        showCategorySubdropdown(
            this.getAttribute('data-category-id'),
            this.getAttribute('data-category-name'),
            this.getAttribute('data-category-slug')
        );
    });
});

document.querySelectorAll('.modal-category-item').forEach(item => {
    item.addEventListener('click', function (e) {
        e.preventDefault();
        categoriesModal.classList.add('hidden');
        document.body.style.overflow = '';
        showCategorySubdropdown(
            this.getAttribute('data-category-id'),
            this.getAttribute('data-category-name'),
            this.getAttribute('data-category-slug')
        );
    });
});

    if (closeSubDropdown) closeSubDropdown.addEventListener('click', () => subDropdown.classList.add('hidden'));

    document.addEventListener('click', function (e) {
        if (subDropdown && !subDropdown.contains(e.target)
            && !e.target.closest('.category-item')
            && !e.target.closest('.modal-category-item')
            && !e.target.closest('.dropdown-link')
            && !e.target.closest('.nav-dropdown')) {
            subDropdown.classList.add('hidden');
        }
    });

    // Search suggestions
    const navSearch = { input: document.getElementById('navSearchInput'), results: document.getElementById('navSearchResults') };
    if (navSearch.input && navSearch.results) {
        let searchTimer;
        navSearch.input.addEventListener('input', function () {
            clearTimeout(searchTimer);
            const q = this.value.trim();
            if (q.length < 2) { navSearch.results.classList.add('hidden'); return; }
            searchTimer = setTimeout(() => {
                fetch(`/search/suggestions?query=${encodeURIComponent(q)}`)
                    .then(r => r.json())
                    .then(data => {
                        if (!data.length) { navSearch.results.innerHTML = '<div class="p-4 text-gray-500 text-sm">No results found</div>'; navSearch.results.classList.remove('hidden'); return; }
                        navSearch.results.innerHTML = data.map(item => `<a href="${item.url}" class="block p-3 hover:bg-gray-50 border-b last:border-b-0"><div class="flex items-center gap-3"><span class="text-xs font-semibold text-gray-500 uppercase">${item.type}</span><div class="flex-1 min-w-0"><div class="font-semibold text-sm text-gray-900 truncate">${item.title}</div><div class="text-xs text-gray-600 truncate">${item.description||''}</div></div></div></a>`).join('');
                        navSearch.results.classList.remove('hidden');
                    }).catch(console.error);
            }, 300);
        });
        document.addEventListener('click', e => { if (!navSearch.input.contains(e.target) && !navSearch.results.contains(e.target)) navSearch.results.classList.add('hidden'); });
    }
});
</script>
@endpush

<style>
.hero-slides-container { overflow: hidden; }
.hero-slide { will-change: transform, opacity; backface-visibility: hidden; }
.hero-dot   { cursor: pointer; }
.category-item:hover, .modal-category-item:hover { background-color: #fff5f5; cursor: pointer; }
.nav-item:hover { background-color: rgba(255,255,255,0.1); }
.nav-item-wrapper:hover .nav-dropdown { display: block !important; }
.hero-section { position: relative; z-index: 1; }
.nav-dropdown { position: absolute; z-index: 999999 !important; }
#categorySidebar      { z-index: 30; }
#categoriesModal      { z-index: 99999; }
#categorySubDropdown  { z-index: 99998; }
.nav-dropdown         { z-index: 99997 !important; }
#fixedRightAd         { z-index: 40 !important; }
#topBar               { z-index: 51 !important; position: relative; }
#mainHeader           { z-index: 52 !important; }
#navBar               { z-index: 99999 !important; }
.nav-item-wrapper:hover .nav-dropdown { display: block !important; }
@media (max-width: 1279px) {
    #categorySubDropdown { margin-left: 0 !important; left: 0 !important; right: 0 !important; }
    .xl\:block { display: none !important; }
}
</style>

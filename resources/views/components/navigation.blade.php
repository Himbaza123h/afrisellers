<nav id="main-nav" class="bg-white shadow-md transition-all duration-300">
<!-- Advertisement Banner - 100% Isolated with Inline Styles -->
<style>
/* ONLY animation keyframes - ultra-specific selector to avoid conflicts */
@keyframes adBannerScroll-unique-id-2024 {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
#ad-banner-unique-2024 .ad-ticker-animate {
    animation: adBannerScroll-unique-id-2024 40s linear infinite;
}
#ad-banner-unique-2024 .ad-ticker-container:hover .ad-ticker-animate {
    animation-play-state: paused;
}
@media (max-width: 374px) {
    #ad-banner-unique-2024 .ad-ticker-animate { animation-duration: 50s; }
}
@media (min-width: 768px) {
    #ad-banner-unique-2024 .ad-ticker-animate { animation-duration: 35s; }
}
@media (min-width: 1024px) {
    #ad-banner-unique-2024 .ad-ticker-animate { animation-duration: 30s; }
}
@media (prefers-reduced-motion: reduce) {
    #ad-banner-unique-2024 .ad-ticker-animate { animation: none; }
}
</style>
 @unless(request()->routeIs('partner.request.form') || request()->routeIs('partner.request.success'))
@include('frontend.home.sections.dub-ads-banner')
 @endunless
    @php
        $settings = App\Models\Setting::all();

        // Categories with products
        $categories = App\Models\ProductCategory::where('status', 'active')
            ->with([
                'products' => function ($query) {
                    $query
                        ->where('status', 'active')
                        ->where('is_admin_verified', true)
                        ->with('images')
                        ->limit(8);
                },
            ])
            ->orderBy('name')
            ->get();

        // Featured Suppliers
        $featuredSuppliers = App\Models\BusinessProfile::where('verification_status', 'verified')
            ->where('is_admin_verified', true)
            ->with(['user', 'country'])
            ->limit(6)
            ->get();

        // Calculate supplier ratings
        $userIds = $featuredSuppliers->pluck('user_id');
        $supplierRatings = [];
        foreach ($userIds as $userId) {
            $allReviews = App\Models\ProductUserReview::whereHas('product', function ($query) use ($userId) {
                $query
                    ->where('user_id', $userId)
                    ->where('status', 'active')
                    ->where('is_admin_verified', true);
            })
                ->where('status', true)
                ->get();

            $avgRating = $allReviews->count() > 0 ? $allReviews->avg('mark') : 0;
            $reviewsCount = $allReviews->count();
            $supplierRatings[$userId] = [
                'rating' => $avgRating,
                'count' => $reviewsCount,
            ];
        }

        // New Arrival Products (Last 2 Days)
        $newArrivalProducts = App\Models\Product::where('status', 'active')
            ->where('is_admin_verified', true)
            ->where('created_at', '>=', now()->subDays(2))
            ->with(['images', 'productCategory'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // New Arrival Companies (Last 2 Days)
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

        // Top RFQs
        $canSeeTopRfqs = auth()->check();
        $topRFQs = collect([]);
        if ($canSeeTopRfqs && class_exists('App\Models\RFQs')) {
            $topRFQs = App\Models\RFQs::with(['product.productCategory', 'country'])
                ->withCount('messages')
                ->latest()
                ->limit(6)
                ->get();
        }

        // Available Loads - Match HomeController exactly
        $availableLoads = collect([]);
        if (class_exists('App\Models\Load')) {
            $availableLoads = App\Models\Load::where('status', 'posted')
                ->with(['originCountry', 'destinationCountry'])
                ->latest()
                ->limit(8)
                ->get();
        }

        // Available Cars - Match HomeController exactly
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

        // Upcoming Tradeshows - Match HomeController exactly
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

        // Featured Showrooms - Match HomeController exactly
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

        // Countries for selector
        $countries = App\Models\Country::where('status', 'active')->orderBy('name')->limit(7)->get();
    @endphp
    <!-- Top Bar -->
<div class="bg-[#ff0808] text-white py-1">
    <div class="container flex justify-between items-center px-3 mx-auto text-xs md:text-xs">
        <!-- Left: Phone Number -->
        <div class="flex gap-2 items-center">
            <a href="tel:{{ $settings->where('key', 'company_phone')->first()?->value ?? '+250 780 879126' }}" class="transition-colors hover:text-red-100">
                <i class="mr-1 fas fa-phone text-xs"></i>
                <span class="hidden sm:inline">Helpline: </span>
                <span class="hidden sm:inline">{{ $settings->where('key', 'company_phone')->first()?->value ?? '+1(469)837-9001 | +250 788 797 687 | +250 780 879126' }}</span>
            </a>
        </div>


        <!-- Right: Language Switcher -->
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

    <!-- Main Navigation -->
    <div class="container px-3 py-2 mx-auto md:py-3 lg:py-4">
        <div class="flex gap-3 justify-between items-center md:gap-4 lg:gap-6">
            <!-- Logo -->
           <div class="shrink-0">
                <a href="/" class="flex items-center">
                    <img src="{{ asset('mainlogo.png') }}"
                        alt="AfriSellers" class="h-6 sm:h-8">
                </a>
            </div>

            <!-- Search Bar - Hidden on mobile, shown on md+ -->
            <div class="hidden flex-1 max-w-2xl md:flex">
            <div class="relative w-full">
                <form action="{{ route('global.search') }}" method="GET" class="relative w-full">
                    <input type="text" name="query" id="navSearchInput" placeholder="{{ __('messages.search_placeholder') }}"
                        class="w-full px-2 py-1 pr-24 md:px-3 md:py-1.5 lg:py-2 lg:pr-32 rounded-lg border-2 border-gray-300 focus:border-[#ff0808] focus:outline-none font-medium text-xs"
                        autocomplete="off">
                    <button type="submit"
                        class="absolute right-0 top-0 h-full px-3 md:px-4 lg:px-6 bg-[#ff0808] text-white rounded-r-lg hover:bg-red-700 transition-colors font-bold text-xs">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                <div id="navSearchResults" class="absolute w-full bg-white rounded-lg shadow-xl mt-1 hidden z-50 max-h-96 overflow-y-auto"></div>
            </div>
            </div>

            <!-- Right Menu -->
            <div class="flex gap-2 items-center md:gap-3 lg:gap-6">
                <!-- Mobile Menu Toggle -->
            <button id="mobile-menu-toggle" onclick="openMobileMenu()" class="md:hidden text-gray-700 hover:text-[#ff0808]">
                <i class="text-md fas fa-bars"></i>
            </button>
                <!-- User Authentication Section -->
                <div class="flex gap-2 items-center md:gap-3">
                    @auth
                        <!-- Authenticated User -->
                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-[#ff0808] flex items-center justify-center">
                            <span class="text-xs md:text-xs font-semibold text-white">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </span>
                        </div>
                        <div class="hidden gap-2 items-center text-gray-700 lg:flex">
                            @php
                                $user = auth()->user();
                                $dashboardRoute = null;
                                $userRole = null;

                                if ($user) {
                                    // Check for Admin
                                    $isAdmin = $user
                                        ->roles()
                                        ->where('roles.id', 1)
                                        ->where('roles.name', 'Admin')
                                        ->where('roles.slug', 'admin')
                                        ->exists();

                                    if ($isAdmin) {
                                        $dashboardRoute = route('admin.dashboard.home');
                                        $userRole = 'Admin';
                                    } else {
                                        // Check for Regional Admin
                                        $isRegionalAdmin = $user
                                            ->roles()
                                            ->where('roles.slug', 'regional_admin')
                                            ->exists();

                                        if ($isRegionalAdmin) {
                                            $dashboardRoute = route('regional.dashboard.home');
                                            $userRole = 'Regional Admin';
                                        } else {
                                            // Check for Country Admin
                                            $isCountryAdmin = $user
                                                ->roles()
                                                ->where('roles.slug', 'country_admin')
                                                ->exists();

                                            if ($isCountryAdmin) {
                                                $dashboardRoute = route('country.dashboard.home');
                                                $userRole = 'Country Admin';
                                            } else {
                                                // Check for Agent
                                                $isAgent = $user
                                                    ->roles()
                                                    ->where('roles.slug', 'agent')
                                                    ->exists();

                                                if ($isAgent) {
                                                    $dashboardRoute = route('agent.dashboard.home');
                                                    $userRole = 'Agent';
                                                } else {
                                                    // Check for Vendor
                                                    $vendor = App\Models\Vendor\Vendor::where('user_id', $user->id)->first();

                                                    if ($vendor) {
                                                        $dashboardRoute = route('vendor.dashboard.home');
                                                        $userRole = 'Vendor';
                                                    } else {
                                                        // Default to Buyer
                                                        $dashboardRoute = route('buyer.dashboard.home');
                                                        $userRole = 'Buyer';
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            @endphp

                            @if ($dashboardRoute)
                                <a href="{{ $dashboardRoute }}"
                                    class="hover:text-[#ff0808] font-semibold transition-colors text-xs">
                                    {{ __('messages.dashboard') }}
                                </a>
                            @endif

                            <span class="text-gray-400">|</span>

                            <form action="{{ route('auth.logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="hover:text-[#ff0808] font-semibold transition-colors text-xs">
                                    {{ __('messages.logout') }}
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- Guest User -->
                        <div class="flex justify-center items-center w-8 h-8 md:w-10 md:h-10 rounded-full border-2 border-gray-300">
                            <i class="text-xs md:text-xs text-gray-400 fas fa-user"></i>
                        </div>
                        <div class="hidden gap-2 items-center text-gray-700 lg:flex">
                            <a href="{{ route('auth.signin') }}"
                                class="hover:text-[#ff0808] font-semibold transition-colors text-xs">
                                {{ __('messages.login') }}
                            </a>
                            <span class="text-gray-400">|</span>
                            <a href="{{ route('auth.register') }}"
                                class="hover:text-[#ff0808] font-semibold transition-colors text-xs">
                                {{ __('messages.registration') }}
                            </a>
                        </div>
                    @endauth
                </div>

                <!-- Livestream Link -->
                <a href="{{ route('livestream') }}"
                    class="text-gray-700 hover:text-[#ff0808] flex items-center gap-1.5 md:gap-2 relative transition-colors">
                    <div class="relative">
                        <i class="text-base md:text-md lg:text-lg fas fa-video"></i>
                        <span
                            class="absolute -top-1.5 -right-1.5 md:-top-2 md:-right-2 bg-[#ff0808] text-white text-[10px] md:text-xs rounded-full w-4 h-4 md:w-5 md:h-5 flex items-center justify-center font-bold shadow-md">7</span>
                    </div>
                    <span
                        class="hidden lg:flex items-center gap-1 bg-[#ff0808] text-white text-xs px-1 py-0.5 rounded-full font-bold">
                        <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
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

            <!-- Cart Icon -->
            <a href="{{ route('cart.index') }}"
                class="relative flex gap-2 items-center text-gray-700 transition-colors hover:text-[#ff0808]">
                <div class="relative">
                    <i class="text-base fas fa-shopping-cart md:text-md lg:text-lg"></i>
                    <span id="cartCount"
                        class="absolute -top-1.5 -right-1.5 md:-top-2 md:-right-2 bg-[#ff0808] text-white text-[10px] md:text-xs rounded-full w-4 h-4 md:w-5 md:h-5 flex items-center justify-center font-bold shadow-md">
                        0
                    </span>
                </div>
                <span class="hidden text-xs font-semibold lg:inline">Cart</span>
            </a>

                        <!-- Wishlist Icon -->
            <a href="{{ route('wishlist.index') }}"
                class="relative flex gap-2 items-center text-gray-700 transition-colors hover:text-[#ff0808]">
                <div class="relative">
                    <i class="text-base fas fa-heart md:text-md lg:text-lg"></i>
                    <span id="wishlistCount"
                        class="absolute -top-1.5 -right-1.5 md:-top-2 md:-right-2 bg-[#ff0808] text-white text-[10px] md:text-xs rounded-full w-4 h-4 md:w-5 md:h-5 flex items-center justify-center font-bold shadow-md">
                        0
                    </span>
                </div>
                <span class="hidden text-xs font-semibold lg:inline">Wishlist</span>
            </a>
            </div>
        </div>

        <!-- Mobile Search Bar -->
        <div class="mt-3 md:hidden">
            <div class="relative">
                <input type="text" placeholder="{{ __('messages.search_mobile_placeholder') }}"
                    class="w-full px-2 py-1 pr-14 rounded-lg border-2 border-gray-300 focus:border-[#ff0808] focus:outline-none font-medium text-xs">
                <button
                    class="absolute right-0 top-0 h-full px-2 bg-[#ff0808] text-white rounded-r-lg hover:bg-red-700 transition-colors text-xs">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>


 @if(request()->routeIs('partners.show') ||  request()->routeIs('company.*'))
     @include('frontend.company.partials.nav', ['profile' => $partner])
 @else


    <!-- Category Navigation -->
    @if (Route::currentRouteName() !== 'home')
        <div class="relative bg-gray-100 border-t border-gray-200">
            <div class="container px-3 mx-auto">
                <div class="flex overflow-x-auto gap-3 md:gap-4 lg:gap-6 items-center py-1.5 md:py-2 scrollbar-hide">
                    <!-- All Categories -->
                    <div class="relative">
                        <button id="categories-btn"
                            class="flex items-center gap-1.5 md:gap-2 text-gray-700 hover:text-[#ff0808] font-bold whitespace-nowrap transition-colors bg-white px-2 py-1.5 md:px-3 md:py-1 rounded-lg shadow-sm text-xs md:text-xs">
                            <i class="text-xs md:text-xs fas fa-th"></i>
                            <span>{{ __('messages.all_categories') }}</span>
                            <i class="text-[10px] md:text-xs fas fa-chevron-down"></i>
                        </button>
                    </div>

                    <!-- Featured Suppliers -->
                    <div class="relative">
                        <a href="#"
                            class="nav-dropdown-trigger text-gray-700 hover:text-[#ff0808] font-bold whitespace-nowrap transition-colors flex items-center gap-1.5 md:gap-2 text-xs md:text-xs"
                            data-dropdown="suppliers-menu">
                            {{ __('messages.featured_suppliers') }}
                            <i class="text-[10px] md:text-xs fas fa-chevron-down"></i>
                        </a>
                    </div>

                    <!-- New Arrivals -->
                    <div class="relative">
                        <a href="#"
                            class="nav-dropdown-trigger text-gray-700 hover:text-[#ff0808] font-bold whitespace-nowrap transition-colors flex items-center gap-1.5 md:gap-2 text-xs md:text-xs"
                            data-dropdown="arrivals-menu">
                            {{ __('messages.new_arrivals') }}
                            <i class="text-[10px] md:text-xs fas fa-chevron-down"></i>
                        </a>
                    </div>

                    <!-- Top RFQs -->
                    @if($canSeeTopRfqs ?? false)
                    <div class="relative">
                        <a href="#"
                            class="nav-dropdown-trigger text-gray-700 hover:text-[#ff0808] font-bold whitespace-nowrap transition-colors flex items-center gap-1.5 md:gap-2 text-xs md:text-xs"
                            data-dropdown="rfq-menu">
                            {{ __('messages.top_rfqs') }}
                            <i class="text-[10px] md:text-xs fas fa-chevron-down"></i>
                        </a>
                    </div>
                    @endif

                    <!-- Loadboard -->
                    <div class="relative">
                        <a href="#"
                            class="nav-dropdown-trigger text-gray-700 hover:text-[#ff0808] font-bold whitespace-nowrap transition-colors flex items-center gap-1.5 md:gap-2 text-xs md:text-xs"
                            data-dropdown="loadboard-menu">
                            {{ __('messages.loadboard') }}
                            <i class="text-[10px] md:text-xs fas fa-chevron-down"></i>
                        </a>
                    </div>

                    <!-- Trade Shows -->
                    <div class="relative">
                        <a href="#"
                            class="nav-dropdown-trigger text-gray-700 hover:text-[#ff0808] font-bold whitespace-nowrap transition-colors flex items-center gap-1.5 md:gap-2 text-xs md:text-xs"
                            data-dropdown="tradeshows-menu">
                            {{ __('messages.trade_shows') }}
                            <i class="text-[10px] md:text-xs fas fa-chevron-down"></i>
                        </a>
                    </div>

                    <!-- Compare -->
                    <div class="relative">
                        <a href="{{ route('buyer.compare.index') }}"
                            class="text-gray-700 hover:text-[#ff0808] font-bold whitespace-nowrap transition-colors flex items-center gap-1.5 md:gap-2 text-xs md:text-xs">
                            <i class="text-[10px] md:text-xs fas fa-balance-scale"></i>
                            Compare
                            @if(count(session('compare_products', [])) > 0)
                                <span class="bg-[#ff0808] text-white text-[10px] md:text-xs rounded-full w-4 h-4 md:w-5 md:h-5 flex items-center justify-center font-bold shadow-md">
                                    {{ count(session('compare_products', [])) }}
                                </span>
                            @endif
                        </a>
                    </div>

                    <!-- Send RFQs Button -->
                    <div class="relative">
                        <a href="{{ route('rfqs.create') }}"
                            class="flex items-center gap-1.5 md:gap-2 text-white hover:text-white font-bold whitespace-nowrap transition-colors bg-[#ff0808] hover:bg-red-700 px-2 py-1.5 md:px-3 md:py-1 rounded-lg shadow-sm text-xs md:text-xs">
                            <i class="text-xs md:text-xs fas fa-file-invoice"></i>
                            <span>Send RFQs</span>
                        </a>
                    </div>
                </div>
            </div>

            @php
                $categories = App\Models\ProductCategory::where('status', 'active')
                    ->with([
                        'products' => function ($query) {
                            $query
                                ->where('status', 'active')
                                ->where('is_admin_verified', true)
                                ->with('images')
                                ->limit(8);
                        },
                    ])
                    ->orderBy('name')
                    ->get();
            @endphp
            <!-- All Categories Mega Menu -->
            <div id="categories-menu"
                class="hidden absolute left-0 right-0 top-full bg-white shadow-2xl z-[9999] border-t-4 border-[#ff0808]">
                <div class="container mx-auto">
                    <div class="grid grid-cols-1 gap-0 lg:grid-cols-12">
                        <!-- Left Sidebar -->
                        <div class="p-3 md:p-4 lg:p-6 bg-gray-50 border-r border-gray-200 lg:col-span-3">
                            <h3
                                class="flex gap-1.5 md:gap-2 items-center mb-3 md:mb-4 lg:mb-6 text-xs md:text-base lg:text-md font-black text-gray-900">
                                <i class="fas fa-layer-group text-[#ff0808] text-xs md:text-xs"></i>
                                {{ __('messages.browse_by_category') }}
                            </h3>
                            <div class="pb-2 relative">
                                <input type="text"
                                       id="category-search-input"
                                       placeholder="Search categories..."
                                       class="w-full px-2 py-1 md:px-3 md:py-1.5 pl-9 md:pl-10 pr-3 md:pr-4 rounded-lg border-2 border-gray-300 focus:border-[#ff0808] focus:outline-none font-medium text-xs md:text-xs">
                                <i class="absolute left-2.5 md:left-3 top-1/2 -translate-y-1/2 mt-1 text-gray-400 fas fa-search text-xs md:text-xs"></i>
                            </div>
                            <div id="categories-list" class="space-y-1.5 md:space-y-2 lg:space-y-3 max-h-[400px] md:max-h-[500px] overflow-y-auto">
                                @foreach ($categories as $index => $category)
                                    <button
                                        class="category-sidebar-btn w-full text-left px-1.5 md:px-2 lg:px-3 py-1 md:py-1.5 lg:py-2 rounded-lg {{ $index === 0 ? 'bg-[#ff0808] text-white font-bold' : 'bg-white text-gray-700 font-semibold' }} transition-all hover:bg-red-50 hover:text-[#ff0808] text-xs md:text-xs"
                                        data-category="category-{{ $category->id }}"
                                        data-category-name="{{ strtolower($category->name) }}">
                                        <i class="mr-1.5 md:mr-2 fas fa-box text-xs md:text-xs"></i><span class="category-name">{{ $category->name }}</span>
                                        <i class="float-right mt-0.5 md:mt-1 fas fa-chevron-right text-xs"></i>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Right Content Area -->
                        <div class="p-3 md:p-4 lg:p-8 lg:col-span-9">
                            @foreach ($categories as $index => $category)
                                <div id="category-{{ $category->id }}"
                                    class="category-content {{ $index === 0 ? '' : 'hidden' }}">
                                    <div class="flex justify-between items-center mb-3 md:mb-4 lg:mb-6">
                                        <h2
                                            class="flex gap-1.5 md:gap-2 lg:gap-3 items-center text-base md:text-lg lg:text-2xl font-black text-gray-900">
                                            <span class="text-lg md:text-2xl lg:text-4xl">📦</span>
                                            <span class="hidden sm:inline">{{ $category->name }}</span>
                                            <span
                                                class="sm:hidden">{{ \Illuminate\Support\Str::limit($category->name, 15) }}</span>
                                        </h2>
                                        <a href="{{ route('products.search', ['type' => 'category', 'slug' => \Illuminate\Support\Str::slug($category->name)]) }}"
                                            class="text-[#ff0808] hover:text-red-700 font-bold flex items-center gap-1 md:gap-2 text-xs md:text-xs">
                                            View All →
                                        </a>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 md:gap-3 lg:gap-6 md:grid-cols-4">
                                        @forelse($category->products->take(8) as $product)
                                            @php
                                                $colorClasses = [
                                                    'from-green-50 to-green-100',
                                                    'from-blue-50 to-blue-100',
                                                    'from-purple-50 to-purple-100',
                                                    'from-gray-50 to-gray-100',
                                                    'from-orange-50 to-orange-100',
                                                    'from-red-50 to-red-100',
                                                    'from-yellow-50 to-yellow-100',
                                                    'from-indigo-50 to-indigo-100',
                                                ];
                                                $colorClass = $colorClasses[$loop->index % count($colorClasses)];
                                                $featuredImage =
                                                    $product->images->where('is_primary', true)->first() ??
                                                    $product->images->first();
                                            @endphp
                                            <a href="{{ route('products.show', $product->slug) }}" class="group">
                                                <div
                                                    class="bg-gradient-to-br {{ $colorClass }} rounded-lg md:rounded-xl p-2.5 md:p-4 lg:p-6 text-center hover:shadow-lg transition-all">
                                                    @if ($featuredImage)
                                                        <img src="{{ $featuredImage->image_url }}"
                                                            alt="{{ $product->name }}"
                                                            class="object-cover mb-1.5 md:mb-2 lg:mb-4 w-full h-20 md:h-24 lg:h-32 rounded-md md:rounded-lg">
                                                    @else
                                                        <div class="mb-1.5 md:mb-2 lg:mb-4 text-lg md:text-2xl lg:text-4xl">📦</div>
                                                    @endif
                                                    <h4
                                                        class="font-bold text-gray-900 group-hover:text-[#ff0808] text-xs md:text-xs line-clamp-2">
                                                        {{ $product->name }}
                                                    </h4>
                                                    <p class="mt-0.5 md:mt-1 text-[10px] md:text-xs lg:text-xs text-gray-600 nav-price-convert"
                                       data-price-native="{{ $product->base_price }}"
                                       data-price-currency="{{ $product->currency }}">
                                        {{ number_format($product->base_price, 2) }}
                                        {{ $product->currency }}
                                    </p>
                                                </div>
                                            </a>
                                        @empty
                                            <div class="col-span-4 py-6 md:py-8 text-center">
                                                <p class="text-gray-500 text-xs">No products available in this category yet.
                                                </p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Featured Suppliers Dropdown -->
            <div id="suppliers-menu"
                class="nav-dropdown-menu hidden absolute left-0 right-0 top-full bg-white shadow-2xl z-[9999] border-t-4 border-yellow-500">
                <div class="container p-3 md:p-4 lg:p-8 mx-auto">
                    <div class="flex justify-between items-center mb-3 md:mb-4 lg:mb-6">
                        <h2 class="text-xs md:text-base lg:text-lg font-bold text-gray-900">Featured Suppliers</h2>
                        <a href="{{ route('featured-suppliers') }}"
                            class="text-[#ff0808] hover:text-red-700 font-semibold text-xs md:text-xs flex items-center gap-1 md:gap-2">
                            View All →
                        </a>
                    </div>
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 md:gap-3 lg:gap-4">
                        @php
                            $featuredSuppliers = App\Models\BusinessProfile::where('verification_status', 'verified')
                                ->where('is_admin_verified', true)
                                ->with(['user', 'country'])
                                ->limit(6)
                                ->get();

                            $userIds = $featuredSuppliers->pluck('user_id');
                            $supplierRatings = [];
                            foreach ($userIds as $userId) {
                                $allReviews = App\Models\ProductUserReview::whereHas('product', function ($query) use (
                                    $userId,
                                ) {
                                    $query
                                        ->where('user_id', $userId)
                                        ->where('status', 'active')
                                        ->where('is_admin_verified', true);
                                })
                                    ->where('status', true)
                                    ->get();

                                $avgRating = $allReviews->count() > 0 ? $allReviews->avg('mark') : 0;
                                $reviewsCount = $allReviews->count();
                                $supplierRatings[$userId] = [
                                    'rating' => $avgRating,
                                    'count' => $reviewsCount,
                                ];
                            }
                        @endphp

                        @forelse($featuredSuppliers as $supplier)
                            @php
                                $rating = $supplierRatings[$supplier->user_id]['rating'] ?? 0;
                                $reviewsCount = $supplierRatings[$supplier->user_id]['count'] ?? 0;
                                $initial = strtoupper(substr($supplier->business_name, 0, 1));
                            @endphp
                            <a href="{{ route('business-profile.products', $supplier->id) }}" class="group">
                                <div
                                    class="bg-white border-2 border-gray-200 rounded-md md:rounded-lg p-2 md:p-3 lg:p-4 hover:border-[#ff0808] hover:shadow-md transition-all text-center">
                                    <div
                                        class="flex justify-center items-center mx-auto mb-1.5 md:mb-2 lg:mb-3 w-10 h-10 md:w-12 md:h-12 lg:w-14 lg:h-14 text-xs md:text-xs lg:text-md font-bold text-gray-700 bg-gray-100 rounded-full">
                                        {{ $initial }}</div>
                                    <h4
                                        class="font-semibold text-gray-900 text-[10px] md:text-xs lg:text-xs group-hover:text-[#ff0808] transition-colors line-clamp-2">
                                        {{ $supplier->business_name }}</h4>
                                    @if ($rating > 0)
                                        <p class="mt-0.5 md:mt-1 text-[10px] md:text-xs text-gray-600">⭐ {{ number_format($rating, 1) }}
                                            ({{ number_format($reviewsCount) }})
                                        </p>
                                    @else
                                        <p class="mt-0.5 md:mt-1 text-[10px] md:text-xs text-gray-500">No ratings yet</p>
                                    @endif
                                    <span
                                        class="inline-block px-1.5 md:px-1 py-0.5 md:py-1 mt-1 md:mt-2 text-[10px] md:text-xs text-green-700 bg-green-50 rounded border border-green-200">Verified</span>
                                </div>
                            </a>
                        @empty
                            <div class="col-span-full py-6 md:py-8 text-center">
                                <p class="text-xs md:text-xs text-gray-500">No featured suppliers available yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            <!-- New Arrivals Dropdown -->
<!-- New Arrivals Dropdown -->
<div id="arrivals-menu"
    class="nav-dropdown-menu hidden absolute left-0 right-0 top-full bg-white shadow-2xl z-[9999] border-t-4 border-green-500">
    <div class="container p-3 mx-auto md:p-4 lg:p-6">
        <!-- Switcher Tabs -->
        <div class="flex gap-2 mb-3 md:mb-4 border-b-2 border-gray-200">
            <button id="products-arrivals-tab"
                class="arrivals-tab px-2 py-1.5 md:px-3 md:py-1 font-bold text-xs md:text-xs border-b-4 border-green-500 text-green-500 transition-all">
                <i class="fas fa-box mr-1 md:mr-2"></i>By Products
            </button>
            <button id="companies-arrivals-tab"
                class="arrivals-tab px-2 py-1.5 md:px-3 md:py-1 font-bold text-xs md:text-xs border-b-4 border-transparent text-gray-500 hover:text-green-500 transition-all">
                <i class="fas fa-building mr-1 md:mr-2"></i>By Company
            </button>
        </div>

        <!-- By Products Content -->
        <div id="products-arrivals-content" class="arrivals-content">
            <div class="flex justify-between items-center mb-2 md:mb-3">
                <h2 class="text-xs md:text-base font-bold text-gray-900">New Products (Last 2 Days)</h2>
            </div>
            <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 md:gap-3">
                @php
                    $productColorClasses = [
                        'from-green-50 to-green-100',
                        'from-blue-50 to-blue-100',
                        'from-purple-50 to-purple-100',
                        'from-orange-50 to-orange-100',
                        'from-red-50 to-red-100',
                        'from-yellow-50 to-yellow-100',
                        'from-indigo-50 to-indigo-100',
                        'from-teal-50 to-teal-100',
                        'from-pink-50 to-pink-100',
                        'from-cyan-50 to-cyan-100',
                    ];
                @endphp

                @forelse($newArrivalProducts as $index => $product)
                    @php
                        $colorClass = $productColorClasses[$index % count($productColorClasses)];
                        $featuredImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                        $categoryName = $product->productCategory->name ?? 'Uncategorized';
                    @endphp
                    <a href="{{ route('products.show', $product->slug) }}" class="group">
                        <div class="p-2 md:p-3 bg-gradient-to-br {{ $colorClass }} rounded-lg border-2 border-gray-200 transition-all hover:border-green-500 hover:shadow-md text-center">
                            @if($featuredImage)
                                <img src="{{ $featuredImage->image_url }}" alt="{{ $product->name }}"
                                     class="w-full h-20 md:h-24 object-cover rounded-md mb-1.5 md:mb-2">
                            @else
                                <div class="w-full h-20 md:h-24 bg-gray-100 rounded-md mb-1.5 md:mb-2 flex items-center justify-center">
                                    <span class="text-2xl md:text-md">📦</span>
                                </div>
                            @endif
                            <span class="inline-block px-1 py-0.5 text-[9px] md:text-[10px] font-bold text-green-700 bg-green-50 rounded border border-green-200 mb-1">
                                NEW
                            </span>
                            <h4 class="font-semibold text-gray-900 group-hover:text-green-600 text-xs md:text-xs transition-colors line-clamp-2">
                                {{ $product->name }}
                            </h4>
                            <p class="mt-0.5 text-[10px] md:text-xs text-gray-600">{{ $categoryName }}</p>
<p class="mt-0.5 text-[10px] md:text-xs text-green-600 font-medium nav-price-convert"
                               data-price-native="{{ $product->base_price }}"
                               data-price-currency="{{ $product->currency }}">
                                {{ number_format($product->base_price, 2) }} {{ $product->currency }}
                            </p>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-8 text-center">
                        <div class="text-6xl mb-3">📦</div>
                        <p class="text-xs text-gray-500">No new products in the last 2 days.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- By Company Content -->
        <div id="companies-arrivals-content" class="arrivals-content hidden">
            <div class="flex justify-between items-center mb-2 md:mb-3">
                <h2 class="text-xs md:text-base font-bold text-gray-900">New Companies (Last 2 Days)</h2>
            </div>
            <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 md:gap-3">
                @forelse($newArrivalCompanies as $index => $user)
                    @php
                        $businessName = $user->vendor?->businessProfile?->business_name ?? $user->name;
                        $initial = strtoupper(substr($businessName, 0, 1));
                        $newProduct = $user->products->first();
                        $productImage = $newProduct?->images->where('is_primary', true)->first() ?? $newProduct?->images->first();
                        $productCount = $user->new_products_count;
                    @endphp
                    <a href="{{ $user->vendor?->businessProfile ? route('business-profile.products', $user->vendor->businessProfile->id) : '#' }}"
                       class="group block">
                        <div class="p-2 md:p-3 bg-white rounded-lg border-2 border-gray-200 transition-all hover:border-green-500 hover:shadow-md text-center">
                            @if($productImage)
                                <img src="{{ $productImage->image_url }}" alt="{{ $businessName }}"
                                     class="w-full h-20 md:h-24 object-cover rounded-md mb-1.5 md:mb-2">
                            @else
                                <div class="flex justify-center items-center mx-auto mb-1.5 md:mb-2 w-16 h-16 md:w-20 md:h-20 text-xs md:text-md font-bold text-gray-700 bg-gray-100 rounded-full">
                                    {{ $initial }}
                                </div>
                            @endif
                            <span class="inline-block px-1 py-0.5 text-[9px] md:text-[10px] font-bold text-green-700 bg-green-50 rounded border border-green-200 mb-1">
                                {{ $productCount }} NEW {{ $productCount == 1 ? 'ITEM' : 'ITEMS' }}
                            </span>
                            <h4 class="font-semibold text-gray-900 group-hover:text-green-600 text-xs md:text-xs transition-colors line-clamp-2">
                                {{ $businessName }}
                            </h4>
                            @if($newProduct)
                                <p class="mt-0.5 text-[10px] md:text-xs text-gray-600 line-clamp-1">
                                    Latest: {{ $newProduct->name }}
                                </p>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-8 text-center">
                        <div class="text-6xl mb-3">🏢</div>
                        <p class="text-xs text-gray-500">No new companies in the last 2 days.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Top RFQs Dropdown -->
@if($canSeeTopRfqs ?? false)
<div id="rfq-menu"
    class="nav-dropdown-menu hidden absolute left-0 right-0 top-full bg-white shadow-2xl z-[9999] border-t-4 border-orange-500">
    <div class="container p-4 mx-auto md:p-8">
        <div class="flex justify-between items-center mb-4 md:mb-6">
            <h2 class="text-lg font-bold text-gray-900 md:text-2xl">Top RFQs</h2>
            <a href="{{ route('rfqs.create') }}"
                class="text-[#ff0808] hover:text-red-700 font-semibold text-xs md:text-base flex items-center gap-2">
                Request →
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4">
            @forelse($topRFQs as $index => $rfq)
                @php
                    $colorClasses = [
                        ['bg' => 'bg-orange-500', 'hover' => 'hover:border-orange-500'],
                        ['bg' => 'bg-blue-500', 'hover' => 'hover:border-blue-500'],
                        ['bg' => 'bg-green-500', 'hover' => 'hover:border-green-500'],
                        ['bg' => 'bg-purple-500', 'hover' => 'hover:border-purple-500'],
                        ['bg' => 'bg-red-500', 'hover' => 'hover:border-red-500'],
                    ];
                    $colorClass = $colorClasses[$index % count($colorClasses)];
                    $categoryName = $rfq->product && $rfq->product->productCategory
                        ? $rfq->product->productCategory->name
                        : 'General';
                    $location = $rfq->country ? $rfq->country->name : ($rfq->city ? $rfq->city : 'N/A');
                    $timeAgo = $rfq->created_at->diffForHumans();
                    $messagePreview = \Illuminate\Support\Str::limit($rfq->message, 80);
                    $title = $rfq->product
                        ? $rfq->product->name
                        : ($rfq->name ? $rfq->name . ' - RFQ' : 'RFQ #' . $rfq->id);
                @endphp
                <a href="{{ route('rfqs.create') }}"
                    class="block p-4 bg-white rounded-lg border-2 border-gray-200 transition-all hover:shadow-md {{ $colorClass['hover'] }}">
                    <div class="flex flex-col h-full">
                        <div class="flex-1">
                            <h4 class="mb-2 text-xs font-bold text-gray-900 md:text-base line-clamp-2">{{ $title }}</h4>
                            <p class="mb-3 text-xs text-gray-600 md:text-xs line-clamp-2">{!! $messagePreview !!}</p>
                            <div class="flex flex-wrap gap-2 text-xs text-gray-600">
                                @if($location !== 'N/A')
                                    <span><i class="mr-1 fas fa-map-marker-alt"></i>{{ $location }}</span>
                                @endif
                                <span><i class="mr-1 fas fa-calendar"></i>{{ $timeAgo }}</span>
                                <span><i class="mr-1 fas fa-tag"></i>{{ $categoryName }}</span>
                            </div>
                        </div>
                        <div class="flex gap-2 items-center justify-between mt-3 pt-3 border-t border-gray-200">
                            <span
                                class="inline-block px-2 py-1 text-xs font-bold text-white {{ $colorClass['bg'] }} rounded-lg">
                                {{ $rfq->messages_count }} {{ $rfq->messages_count === 1 ? 'Quote' : 'Quotes' }}
                            </span>
                            <p class="text-xs text-gray-500">{{ ucfirst($rfq->status) }}</p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-8 text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-2"></i>
                    <p>No RFQs available at the moment.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endif

            <!-- Loadboard Dropdown -->
            <div id="loadboard-menu"
                class="nav-dropdown-menu hidden absolute left-0 right-0 top-full bg-white shadow-2xl z-[9999] border-t-4 border-purple-500">
                <div class="container p-4 mx-auto md:p-8">
                    <!-- Switcher Tabs -->
                    <div class="flex gap-2 mb-4 md:mb-6 border-b-2 border-gray-200">
                        <button id="loads-tab"
                            class="loadboard-tab px-3 py-1 md:px-6 md:py-2 font-bold text-xs md:text-base border-b-4 border-purple-500 text-purple-500 transition-all">
                            <i class="fas fa-box mr-2"></i>Available Loads
                        </button>
                        <button id="cars-tab"
                            class="loadboard-tab px-3 py-1 md:px-6 md:py-2 font-bold text-xs md:text-base border-b-4 border-transparent text-gray-500 hover:text-purple-500 transition-all">
                            <i class="fas fa-car mr-2"></i>Available Cars
                        </button>
                    </div>

<!-- Available Loads Content -->
<div id="loads-content" class="loadboard-content">
    <div class="flex justify-between items-center mb-3 md:mb-4">
        <h2 class="text-base md:text-md font-bold text-gray-900">Available Loads</h2>
        <a href="{{ route('loadboard.loads.index') }}"
            class="text-[#ff0808] hover:text-red-700 font-semibold text-xs md:text-xs flex items-center gap-1">
            View All →
        </a>
    </div>
    <div class="grid grid-cols-2 gap-2 md:grid-cols-4 md:gap-3">
        @php
            $loadColors = [
                ['bg' => 'bg-purple-500', 'hover' => 'hover:border-purple-500'],
                ['bg' => 'bg-blue-500', 'hover' => 'hover:border-blue-500'],
                ['bg' => 'bg-green-500', 'hover' => 'hover:border-green-500'],
                ['bg' => 'bg-orange-500', 'hover' => 'hover:border-orange-500'],
                ['bg' => 'bg-red-500', 'hover' => 'hover:border-red-500'],
                ['bg' => 'bg-indigo-500', 'hover' => 'hover:border-indigo-500'],
                ['bg' => 'bg-pink-500', 'hover' => 'hover:border-pink-500'],
                ['bg' => 'bg-yellow-600', 'hover' => 'hover:border-yellow-600'],
            ];
        @endphp

        @forelse($availableLoads ?? [] as $index => $load)
            @php
                $colorClass = $loadColors[$index % count($loadColors)];
                $originCity = $load->origin_city ?? 'N/A';
                $destinationCity = $load->destination_city ?? 'N/A';
                $weight = $load->weight ? number_format($load->weight, 0) : 'N/A';
                $budget = $load->budget ? '$' . number_format($load->budget / 1000, 1) . 'K' : 'TBD';
                $pickupDate = $load->pickup_date ? $load->pickup_date->format('M d, Y') : 'TBD';
                $cargoType = $load->cargo_type ?? 'General';
            @endphp
            <a href="{{ route('loadboard.loads.show', $load->load_number) }}"
                class="block p-2 md:p-3 bg-white rounded-lg border-2 border-gray-200 transition-all hover:shadow-md {{ $colorClass['hover'] }}">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-xs md:text-xs font-bold text-gray-900 truncate">{{ $originCity }} → {{ $destinationCity }}</h4>
                        <p class="text-[10px] md:text-xs text-gray-600">{{ $load->pricing_type ?? 'Full Truckload' }}</p>
                    </div>
                    <span class="px-1.5 py-0.5 text-[10px] md:text-xs font-bold text-white {{ $colorClass['bg'] }} rounded ml-1 whitespace-nowrap">{{ $budget }}</span>
                </div>
                <div class="space-y-0.5 text-[10px] md:text-xs text-gray-700">
                    <p class="truncate"><i class="mr-1 {{ $colorClass['bg'] }} text-white rounded-full px-1 fas fa-box text-[8px] md:text-[10px]"></i>{{ $weight }} {{ $load->weight_unit ?? 'kg' }} {{ $cargoType }}</p>
                    <p class="truncate"><i class="mr-1 {{ $colorClass['bg'] }} text-white rounded-full px-1 fas fa-calendar text-[8px] md:text-[10px]"></i>{{ $pickupDate }}</p>
                </div>
            </a>
        @empty
            <div class="col-span-4 py-6 text-center">
                <p class="text-gray-500 text-xs">No available loads at the moment.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Available Cars Content -->
<div id="cars-content" class="loadboard-content hidden">
    <div class="flex justify-between items-center mb-3 md:mb-4">
        <h2 class="text-base md:text-md font-bold text-gray-900">Available Vehicles for Hire</h2>
        <a href="{{ route('loadboard.cars.index') }}"
            class="text-[#ff0808] hover:text-red-700 font-semibold text-xs md:text-xs flex items-center gap-1">
            View All →
        </a>
    </div>
    <div class="grid grid-cols-2 gap-2 md:grid-cols-4 md:gap-3">
        @php
            $carColors = [
                ['bg' => 'bg-purple-500', 'hover' => 'hover:border-purple-500'],
                ['bg' => 'bg-blue-500', 'hover' => 'hover:border-blue-500'],
                ['bg' => 'bg-green-500', 'hover' => 'hover:border-green-500'],
                ['bg' => 'bg-orange-500', 'hover' => 'hover:border-orange-500'],
                ['bg' => 'bg-red-500', 'hover' => 'hover:border-red-500'],
                ['bg' => 'bg-indigo-500', 'hover' => 'hover:border-indigo-500'],
                ['bg' => 'bg-pink-500', 'hover' => 'hover:border-pink-500'],
                ['bg' => 'bg-yellow-600', 'hover' => 'hover:border-yellow-600'],
            ];
        @endphp

        @forelse($availableCars ?? [] as $index => $car)
            @php
                $colorClass = $carColors[$index % count($carColors)];
                $fullName = $car->full_name ?? 'Unknown Vehicle';
                $vehicleType = $car->vehicle_type ?? 'Vehicle';
                $mileage = $car->mileage ? number_format($car->mileage) . ' km' : 'N/A';

                // Format price based on pricing type
                if ($car->price) {
                    $priceDisplay = '$' . number_format($car->price / 1000, 1) . 'K';
                    if ($car->pricing_type === 'per_trip') {
                        $priceDisplay .= '/trip';
                    } elseif ($car->pricing_type === 'per_day') {
                        $priceDisplay .= '/day';
                    } elseif ($car->pricing_type === 'per_km') {
                        $priceDisplay .= '/km';
                    }
                } else {
                    $priceDisplay = 'Contact';
                }

                // Get route information
                $fromLocation = $car->from_city ?? ($car->fromCountry->name ?? 'Unknown');
                $toLocation = $car->flexible_destination
                    ? 'Flexible'
                    : ($car->to_city ?? ($car->toCountry->name ?? 'Any'));

                // Get cargo capacity
                $capacity = $car->cargo_capacity
                    ? number_format($car->cargo_capacity, 1) . ' ' . $car->cargo_capacity_unit
                    : 'N/A';

                // Get rating display
                $ratingDisplay = $car->rating > 0
                    ? '⭐ ' . number_format($car->rating, 1) . ' (' . $car->completed_trips . ' trips)'
                    : 'New';
            @endphp
            <a href="{{ route('loadboard.cars.show', $car->listing_number) }}"
                class="block p-2 md:p-3 bg-white rounded-lg border-2 border-gray-200 transition-all hover:shadow-md {{ $colorClass['hover'] }}">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-xs md:text-xs font-bold text-gray-900 truncate">{{ $fullName }}</h4>
                        <p class="text-[10px] md:text-xs text-gray-600">{{ $vehicleType }}</p>
                    </div>
                    <span class="px-1.5 py-0.5 text-[10px] md:text-xs font-bold text-white {{ $colorClass['bg'] }} rounded ml-1 whitespace-nowrap">
                        {{ $priceDisplay }}
                    </span>
                </div>

                <div class="space-y-0.5 text-[10px] md:text-xs text-gray-700 mb-1.5">
                    <p class="truncate">
                        <i class="mr-1 text-white rounded-full px-1 fas fa-route text-[8px] md:text-[10px] {{ $colorClass['bg'] }}"></i>
                        {{ $fromLocation }} → {{ $toLocation }}
                    </p>
                    <p class="truncate">
                        <i class="mr-1 text-white rounded-full px-1 fas fa-weight-hanging text-[8px] md:text-[10px] {{ $colorClass['bg'] }}"></i>
                        {{ $capacity }} capacity
                    </p>
                </div>

                <div class="flex justify-between items-center mt-1.5">
                    <span class="text-[9px] md:text-[10px] text-gray-500">
                        {{ $ratingDisplay }}
                    </span>
                    @if($car->driver_included)
                        <span class="inline-block px-1.5 py-0.5 text-[9px] md:text-[10px] font-semibold text-blue-700 bg-blue-50 rounded border border-blue-200">
                            Driver ✓
                        </span>
                    @endif
                </div>

                @if($car->price_negotiable)
                    <span class="inline-block mt-1 px-1.5 py-0.5 text-[9px] md:text-[10px] font-semibold text-green-700 bg-green-50 rounded border border-green-200">
                        Negotiable
                    </span>
                @endif
            </a>
        @empty
            <div class="col-span-4 py-6 text-center">
                <p class="text-gray-500 text-xs">No available vehicles at the moment.</p>
            </div>
        @endforelse
    </div>
</div>
                </div>
            </div>

<!-- Trade Shows Dropdown -->
<div id="tradeshows-menu"
    class="nav-dropdown-menu hidden absolute left-0 right-0 top-full bg-white shadow-2xl z-[9999] border-t-4 border-[#ff0808]">
    <div class="container p-3 mx-auto md:p-4 lg:p-6">
        <!-- Switcher Tabs -->
        <div class="flex gap-2 mb-3 md:mb-4 border-b-2 border-gray-200">
            <button id="tradeshows-tab"
                class="tradeshows-tab px-2 py-1.5 md:px-3 md:py-1 font-bold text-xs md:text-xs border-b-4 border-[#ff0808] text-[#ff0808] transition-all">
                <i class="fas fa-calendar-alt mr-1 md:mr-2"></i>Trade Shows
            </button>
            <button id="showroom-tab"
                class="tradeshows-tab px-2 py-1.5 md:px-3 md:py-1 font-bold text-xs md:text-xs border-b-4 border-transparent text-gray-500 hover:text-[#ff0808] transition-all">
                <i class="fas fa-store mr-1 md:mr-2"></i>Show Room
            </button>
        </div>

<!-- Trade Shows Content -->
<div id="tradeshows-content" class="tradeshows-content">
    <div class="flex justify-between items-center mb-2 md:mb-3">
        <h2 class="text-xs md:text-base font-bold text-gray-900">Upcoming Trade Shows</h2>
        <a href="{{ route('tradeshows.index') }}"
            class="text-[#ff0808] hover:text-red-700 font-semibold text-[10px] md:text-xs flex items-center gap-1">
            View All →
        </a>
    </div>
    <div class="grid grid-cols-1 gap-2 md:grid-cols-3 md:gap-3">
        @forelse($upcomingTradeshows as $tradeshow)
            @php
                $colors = ['red', 'blue', 'purple'];
                $color = $colors[$loop->index % 3];
                $monthShort = $tradeshow->start_date->format('M');
                $day = $tradeshow->start_date->format('d');
            @endphp
            <a href="{{ route('tradeshows.show', $tradeshow->slug) }}"
               class="block bg-white rounded-lg overflow-hidden border-2 border-gray-200 hover:border-{{ $color }}-500 hover:shadow-md transition-all">
                <div class="flex justify-center items-center h-20 md:h-24 text-white bg-{{ $color }}-400 to-{{ $color }}-600">
                    <div class="text-center">
                        <div class="text-md md:text-2xl font-black">{{ strtoupper($monthShort) }}</div>
                        <div class="text-2xl md:text-4xl font-black">{{ $day }}</div>
                    </div>
                </div>
                <div class="p-2 md:p-3">
                    <h4 class="mb-1 text-xs md:text-xs font-bold text-gray-900 line-clamp-2">{{ $tradeshow->name }}</h4>
                    <p class="mb-1 text-[10px] md:text-xs text-gray-600 line-clamp-2">
                        {{ $tradeshow->industry }} - {{ $tradeshow->expected_exhibitors }}+ exhibitors
                    </p>
                    <div class="space-y-0.5 text-[10px] md:text-xs text-gray-700">
                        <p><i class="fas fa-map-marker-alt mr-1 text-{{ $color }}-500 text-[8px] md:text-[10px]"></i>{{ $tradeshow->city }}, {{ $tradeshow->country->name }}</p>
                        <p><i class="fas fa-clock mr-1 text-{{ $color }}-500 text-[8px] md:text-[10px]"></i>{{ $tradeshow->start_date->format('M d') }} - {{ $tradeshow->end_date->format('M d, Y') }}</p>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-3 py-6 text-center">
                <p class="text-gray-500 text-xs">No upcoming tradeshows at the moment.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Show Room Content -->
<div id="showroom-content" class="tradeshows-content hidden">
    <div class="flex justify-between items-center mb-2 md:mb-3">
        <h2 class="text-xs md:text-base font-bold text-gray-900">Featured Showrooms</h2>
        <a href="{{ route('showrooms.index') }}"
           class="text-[#ff0808] hover:text-red-700 font-semibold text-[10px] md:text-xs flex items-center gap-1">
            View All →
        </a>
    </div>
    <div class="grid grid-cols-2 gap-2 md:grid-cols-4 md:gap-3">
        @forelse($featuredShowrooms as $showroom)
            @php
                $emojis = ['🏢', '💎', '👗', '🚗', '🛋️', '💻', '🏥', '🍽️'];
                $emoji = $emojis[$loop->index % count($emojis)];
                $colors = ['red', 'blue', 'green', 'orange'];
                $color = $colors[$loop->index % count($colors)];
            @endphp
            <a href="{{ route('showrooms.show', $showroom->slug) }}"
               class="block p-2 bg-white rounded-lg border-2 border-gray-200 transition-all hover:shadow-md hover:border-{{ $color }}-500">
                <div class="flex justify-center items-center mb-1.5 w-full h-16 md:h-20 bg-gray-100 rounded-lg">
                    @if($showroom->primary_image)
                        <img src="{{ $showroom->primary_image }}" alt="{{ $showroom->name }}" class="w-full h-full object-cover rounded-lg">
                    @else
                        <span class="text-2xl md:text-4xl">{{ $emoji }}</span>
                    @endif
                </div>
                <h4 class="text-xs md:text-xs font-bold text-gray-900 truncate">{{ $showroom->name }}</h4>
                <p class="mt-0.5 text-[10px] md:text-xs text-gray-600 truncate">{{ $showroom->industry }}</p>
                <p class="mt-0.5 text-[10px] md:text-xs text-gray-500">
                    <i class="fas fa-map-marker-alt mr-1 text-[8px]"></i>{{ $showroom->city }}
                </p>
                @if($showroom->rating > 0)
                    <p class="mt-1 text-[10px] text-amber-600">
                        ⭐ {{ number_format($showroom->rating, 1) }}
                    </p>
                @endif
                @if($showroom->is_verified)
                    <span class="inline-block mt-1 px-1.5 py-0.5 text-[9px] font-semibold text-green-700 bg-green-50 rounded border border-green-200">
                        Verified
                    </span>
                @endif
            </a>
        @empty
            <div class="col-span-4 py-6 text-center">
                <p class="text-gray-500 text-xs">No featured showrooms available.</p>
            </div>
        @endforelse
    </div>
</div>
    </div>
</div>



    @endif
        </div>
@endif
    <script>
// Loadboard Tab Switcher
document.addEventListener('DOMContentLoaded', function() {
    const loadsTab = document.getElementById('loads-tab');
    const carsTab = document.getElementById('cars-tab');
    const loadsContent = document.getElementById('loads-content');
    const carsContent = document.getElementById('cars-content');

    if (loadsTab && carsTab && loadsContent && carsContent) {
        loadsTab.addEventListener('click', function() {
            // Show loads content
            loadsContent.classList.remove('hidden');
            carsContent.classList.add('hidden');

            // Update tab styles
            loadsTab.classList.add('border-purple-500', 'text-purple-500');
            loadsTab.classList.remove('border-transparent', 'text-gray-500');
            carsTab.classList.remove('border-purple-500', 'text-purple-500');
            carsTab.classList.add('border-transparent', 'text-gray-500');
        });

        carsTab.addEventListener('click', function() {
            // Show cars content
            carsContent.classList.remove('hidden');
            loadsContent.classList.add('hidden');

            // Update tab styles
            carsTab.classList.add('border-purple-500', 'text-purple-500');
            carsTab.classList.remove('border-transparent', 'text-gray-500');
            loadsTab.classList.remove('border-purple-500', 'text-purple-500');
            loadsTab.classList.add('border-transparent', 'text-gray-500');
        });
    }
});

// New Arrivals Tab Switcher
document.addEventListener('DOMContentLoaded', function() {
    const productsArrivalsTab = document.getElementById('products-arrivals-tab');
    const companiesArrivalsTab = document.getElementById('companies-arrivals-tab');
    const productsArrivalsContent = document.getElementById('products-arrivals-content');
    const companiesArrivalsContent = document.getElementById('companies-arrivals-content');

    if (productsArrivalsTab && companiesArrivalsTab && productsArrivalsContent && companiesArrivalsContent) {
        productsArrivalsTab.addEventListener('click', function() {
            productsArrivalsContent.classList.remove('hidden');
            companiesArrivalsContent.classList.add('hidden');

            productsArrivalsTab.classList.add('border-green-500', 'text-green-500');
            productsArrivalsTab.classList.remove('border-transparent', 'text-gray-500');
            companiesArrivalsTab.classList.remove('border-green-500', 'text-green-500');
            companiesArrivalsTab.classList.add('border-transparent', 'text-gray-500');
        });

        companiesArrivalsTab.addEventListener('click', function() {
            companiesArrivalsContent.classList.remove('hidden');
            productsArrivalsContent.classList.add('hidden');

            companiesArrivalsTab.classList.add('border-green-500', 'text-green-500');
            companiesArrivalsTab.classList.remove('border-transparent', 'text-gray-500');
            productsArrivalsTab.classList.remove('border-green-500', 'text-green-500');
            productsArrivalsTab.classList.add('border-transparent', 'text-gray-500');
        });
    }
});


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
// Trade Shows Tab Switcher
document.addEventListener('DOMContentLoaded', function() {
    const tradeshowsTab = document.getElementById('tradeshows-tab');
    const showroomTab = document.getElementById('showroom-tab');
    const tradeshowsContent = document.getElementById('tradeshows-content');
    const showroomContent = document.getElementById('showroom-content');

    if (tradeshowsTab && showroomTab && tradeshowsContent && showroomContent) {
        tradeshowsTab.addEventListener('click', function() {
            tradeshowsContent.classList.remove('hidden');
            showroomContent.classList.add('hidden');

            tradeshowsTab.classList.add('border-[#ff0808]', 'text-[#ff0808]');
            tradeshowsTab.classList.remove('border-transparent', 'text-gray-500');
            showroomTab.classList.remove('border-[#ff0808]', 'text-[#ff0808]');
            showroomTab.classList.add('border-transparent', 'text-gray-500');
        });

        showroomTab.addEventListener('click', function() {
            showroomContent.classList.remove('hidden');
            tradeshowsContent.classList.add('hidden');

            showroomTab.classList.add('border-[#ff0808]', 'text-[#ff0808]');
            showroomTab.classList.remove('border-transparent', 'text-gray-500');
            tradeshowsTab.classList.remove('border-[#ff0808]', 'text-[#ff0808]');
            tradeshowsTab.classList.add('border-transparent', 'text-gray-500');
        });
    }
});
</script>
<script>
// Load wishlist count on page load
(function() {
    fetch('{{ route("wishlist.count") }}', {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        const badge = document.getElementById('wishlistCount');
        if (badge) badge.textContent = data.count;
    })
    .catch(() => {});
})();
</script>
<!-- Mobile Overlay -->
<div id="mobileMenuOverlay" class="fixed inset-0 bg-black/50 z-[999998] hidden lg:hidden" onclick="closeMobileMenu()"></div>

<!-- Mobile Drawer -->
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

        <a href="{{ route('wishlist.index') }}"
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-[#fff5f5] hover:text-[#ff0808] transition-colors border-b border-gray-50">
            <div class="w-8 h-8 bg-pink-50 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-heart text-pink-600 text-xs"></i>
            </div>
            Wishlist
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

<script>
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
</script>
<script>
// ══════════════════════════════════════════════════════════════════
// CURRENCY SWITCHER — live rates via open.er-api.com (free, no key)
// ══════════════════════════════════════════════════════════════════
(function () {

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
        CDF: { name: 'Congolese Franc',     symbol: 'FC'   },
        GMD: { name: 'Gambian Dalasi',      symbol: 'D'    },
        GNF: { name: 'Guinean Franc',       symbol: 'Fr'   },
    };

    const KEY_CODE        = 'ui_currency_code';
    const KEY_RATE        = 'ui_currency_usd_rate';
    const KEY_SYMBOL      = 'ui_currency_symbol';
    const KEY_RATES_CACHE = 'ui_currency_rates_cache';
    const KEY_RATES_TIME  = 'ui_currency_rates_time';
    const CACHE_TTL_MS    = 6 * 60 * 60 * 1000;

    let liveRates = {};

    function getSavedCode() { return localStorage.getItem(KEY_CODE) || 'USD'; }

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
        const codes  = Object.keys(CURRENCY_META).filter(c => liveRates[c] !== undefined);
        if (!codes.length) {
            list.innerHTML = '<div class="px-3 py-4 text-xs text-gray-400 text-center">Rates unavailable</div>';
            return;
        }
        list.innerHTML = codes.map(code => {
            const meta     = CURRENCY_META[code];
            const isActive = code === active;
            return `<button onclick="window.CurrencySwitcher.select('${code}')"
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

    async function fetchRates() {
        const cachedRates = localStorage.getItem(KEY_RATES_CACHE);
        const cachedTime  = parseInt(localStorage.getItem(KEY_RATES_TIME) || '0');
        if (cachedRates && (Date.now() - cachedTime < CACHE_TTL_MS)) {
            liveRates = JSON.parse(cachedRates);
            buildList(); restoreSelection(); return;
        }
        const loader = document.getElementById('currencyLoadingIndicator');
        if (loader) loader.classList.remove('hidden');
        try {
            const res  = await fetch('https://open.er-api.com/v6/latest/USD');
            const data = await res.json();
            if (data.result === 'success' && data.rates) {
                liveRates = data.rates;
                localStorage.setItem(KEY_RATES_CACHE, JSON.stringify(liveRates));
                localStorage.setItem(KEY_RATES_TIME, Date.now());
            } else throw new Error('Bad response');
        } catch (err) {
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
            buildList(); restoreSelection();
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
        document.addEventListener('mousedown', function (e) {
            const wrapper = document.getElementById('currencySwitcherWrapper');
            const dd      = document.getElementById('currencyDropdown');
            if (!dd || dd.classList.contains('hidden')) return;
            if (wrapper && wrapper.contains(e.target)) return;
            if (dd.contains(e.target)) return;
            closeDropdown();
        });
        fetchRates();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    window.CurrencySwitcher = { select, getSavedCode, fetchRates };

})();

function toggleCurrencyDropdown() {
    const btn = document.getElementById('currencyBtn');
    const dd  = document.getElementById('currencyDropdown');
    const chv = document.getElementById('currencyChevron');
    if (!dd || !btn) return;
    const isHidden = dd.classList.contains('hidden');
    if (isHidden) {
        const rect = btn.getBoundingClientRect();
        dd.style.top  = (rect.bottom + 6) + 'px';
        let left = rect.right - 224;
        if (left < 8) left = 8;
        dd.style.left = left + 'px';
        dd.classList.remove('hidden');
        if (chv) chv.style.transform = 'rotate(180deg)';
    } else {
        dd.classList.add('hidden');
        if (chv) chv.style.transform = 'rotate(0deg)';
    }
}

// ── Convert nav dropdown prices on currency change ────────────────────────
function getLiveRatesNav() {
    try { return JSON.parse(localStorage.getItem('ui_currency_rates_cache') || '{}'); } catch(e) { return {}; }
}

function convertNavPrices() {
    const rate   = parseFloat(localStorage.getItem('ui_currency_usd_rate') || '1');
    const symbol = localStorage.getItem('ui_currency_symbol') || '$';
    const rates  = getLiveRatesNav();

    document.querySelectorAll('.nav-price-convert').forEach(function(el) {
        const native   = parseFloat(el.dataset.priceNative);
        const currency = el.dataset.priceCurrency;
        if (isNaN(native)) return;
        const nativeRate = rates[currency] || 1;
        const converted  = (native / nativeRate) * rate;
        const formatted  = converted >= 1000
            ? Math.round(converted).toLocaleString()
            : converted.toFixed(2);
        el.textContent = symbol + formatted;
    });
}

window.addEventListener('currencyChanged', convertNavPrices);
document.addEventListener('DOMContentLoaded', function() { setTimeout(convertNavPrices, 400); });
</script>
</nav>

<style>
    /* Fix dropdown positioning and scrolling */
    .nav-dropdown-menu,
    #categories-menu {
        max-height: calc(100vh - 100px);
        overflow-y: auto;
        overflow-x: hidden;
        scroll-behavior: smooth;
    }

    /* Custom scrollbar for dropdowns */
    .nav-dropdown-menu::-webkit-scrollbar,
    #categories-menu::-webkit-scrollbar {
        width: 6px;
    }

    @media (min-width: 768px) {
        .nav-dropdown-menu::-webkit-scrollbar,
        #categories-menu::-webkit-scrollbar {
            width: 8px;
        }
    }

    .nav-dropdown-menu::-webkit-scrollbar-track,
    #categories-menu::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .nav-dropdown-menu::-webkit-scrollbar-thumb,
    #categories-menu::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    .nav-dropdown-menu::-webkit-scrollbar-thumb:hover,
    #categories-menu::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* CRITICAL: Ensure page stays scrollable */
    body {
        overflow-y: scroll !important;
    }

    html {
        overflow-y: scroll !important;
    }

    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    /* Mobile optimizations */
    @media (max-width: 640px) {
        .nav-dropdown-menu,
        #categories-menu {
            max-height: calc(100vh - 80px);
        }
    }
</style>





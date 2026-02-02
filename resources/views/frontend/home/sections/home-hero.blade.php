{{-- Home Hero Section - Fully Responsive with Primary Color #ff0808 --}}
{{-- Approach 1: Hover shows simple dropdown, Click shows full subdropdown --}}

@php
    $settings = App\Models\Setting::all();
@endphp

<!-- Top Bar - Smaller and hides on scroll -->
<div id="topBar" class="bg-[#ff0808] text-white py-1 transition-transform duration-300">
    <div class="container flex justify-between items-center px-4 mx-auto text-[10px] md:text-xs">
        <!-- Left: Phone Number -->
        <div class="flex gap-2 items-center">
            <a href="tel:{{ $settings->where('key', 'company_phone')->first()?->value ?? '+250 780 879126' }}" class="transition-colors hover:text-red-100">
                <i class="mr-1 fas fa-phone text-[9px]"></i>
                <span class="hidden sm:inline">Helpline: </span>
                <span class="hidden sm:inline">{{ $settings->where('key', 'company_phone')->first()?->value ?? '+1(469)837-9001 | +250 788 797 687 | +250 780 879126' }}</span>
            </a>
        </div>

        <!-- Right: Language Switcher -->
        <div class="flex gap-2 items-center">
            <x-language-switcher />
            <div class="flex items-center px-1">
                @php
                    $countries = App\Models\Country::where('status', 'active')->orderBy('name')->limit(7)->get();
                @endphp
                <select name="country" id="country" class="text-black text-[9px] md:text-[10px] py-0.5 px-1 md:px-1.5 rounded bg-white">
                    <option selected disabled>Select a country</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Main Header - Sticky -->
<div id="mainHeader" class="bg-white border-b border-gray-200 sticky top-0 z-50 transition-all duration-300">
    <div class="container mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between py-2 sm:py-2.5">
            {{-- Logo --}}
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}">
                    <img src="https://afrisellers.com/public/uploads/all/rcIW6v7SfbxlCbrTIBU6CXQNggsQbKVO1a8vXheE.png"
                         alt="Afrisellers"
                         class="h-6 sm:h-8">
                </a>
            </div>

            {{-- Center Search Bar --}}
            <div class="hidden md:flex flex-1 max-w-2xl mx-4 lg:mx-8">
                <div class="flex items-center gap-2 w-full">
                    {{-- Marketplace Dropdown --}}
                    <button class="flex items-center gap-2 px-2 sm:px-3 py-1.5 bg-white border border-gray-300 rounded text-xs text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-store text-gray-500 text-xs"></i>
                        <span class="font-medium hidden sm:inline">Marketplace</span>
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>

                    {{-- Search Input --}}
                    <form action="{{ route('global.search') }}" method="GET" class="flex-1 flex">
                        <input type="text"
                               name="query"
                               placeholder="Search..."
                               class="flex-1 px-3 py-1.5 border border-gray-300 border-r-0 rounded-l text-xs focus:outline-none focus:border-[#ff0808]">
                        <button type="submit" class="px-4 sm:px-5 bg-[#ff0808] hover:bg-[#e00707] text-white rounded-r">
                            <i class="fas fa-search text-sm"></i>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Right Actions --}}
            <div class="flex gap-2 items-center md:gap-3 lg:gap-5">
                <!-- Mobile Menu Toggle -->
                <button id="mobile-menu-toggle" class="md:hidden text-gray-700 hover:text-[#ff0808]">
                    <i class="text-base fas fa-bars"></i>
                </button>

                <!-- User Authentication Section -->
                <div class="flex gap-2 items-center md:gap-3">
                    @auth
                        <div class="w-7 h-7 md:w-9 md:h-9 rounded-full bg-[#ff0808] flex items-center justify-center">
                            <span class="text-xs md:text-sm font-semibold text-white">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </span>
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
                                                } else {
                                                    $vendor = App\Models\Vendor\Vendor::where('user_id', $user->id)->first();
                                                    if ($vendor) {
                                                        $dashboardRoute = route('vendor.dashboard.home');
                                                    } else {
                                                        $dashboardRoute = route('buyer.dashboard.home');
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            @endphp

                            @if ($dashboardRoute)
                                <a href="{{ $dashboardRoute }}" class="hover:text-[#ff0808] font-semibold transition-colors text-xs">
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
                        <div class="flex justify-center items-center w-7 h-7 md:w-9 md:h-9 rounded-full border-2 border-gray-300">
                            <i class="text-xs text-gray-400 fas fa-user"></i>
                        </div>
                        <div class="hidden gap-2 items-center text-gray-700 lg:flex">
                            <a href="{{ route('auth.signin') }}" class="hover:text-[#ff0808] font-semibold transition-colors text-xs">
                                {{ __('messages.login') }}
                            </a>
                            <span class="text-gray-400">|</span>
                            <a href="{{ route('auth.register') }}" class="hover:text-[#ff0808] font-semibold transition-colors text-xs">
                                {{ __('messages.registration') }}
                            </a>
                        </div>
                    @endauth
                </div>

                <!-- Livestream Link -->
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

                <!-- Cart Icon -->
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

{{-- Main Navigation Bar (Dark Navy Blue) - Sticky --}}
<div id="navBar" class="bg-[#1a2942] text-white hidden lg:block sticky top-0 z-40 transition-all duration-300">
    <div class="container mx-auto px-6">
        <div class="flex items-center">
            {{-- All Categories Button --}}
            <div class="relative" style="width: 288px; flex-shrink-0;">
                <button class="flex items-center justify-center gap-2 text-xs font-medium hover:bg-white/10 transition-colors px-4 py-2.5 w-full">
                    <span>All Categories</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                <div class="absolute right-0 top-0 bottom-0 w-px bg-white/10"></div>
            </div>

            {{-- Featured Suppliers --}}
            <div class="relative nav-item-wrapper flex-1">
                <a href="{{ route('featured-suppliers') }}" class="nav-item block text-xs hover:bg-white/10 transition-colors text-center px-4 py-2.5 border-r border-white/10">
                    Featured Suppliers
                </a>
                @php
                    $featuredSuppliers = App\Models\BusinessProfile::where('verification_status', 'verified')
                        ->where('is_admin_verified', true)
                        ->with(['user', 'country'])
                        ->limit(6)
                        ->get();

                    $userIds = $featuredSuppliers->pluck('user_id');
                    $supplierRatings = [];
                    foreach ($userIds as $userId) {
                        $allReviews = App\Models\ProductUserReview::whereHas('product', function ($query) use ($userId) {
                            $query->where('user_id', $userId)->where('status', 'active')->where('is_admin_verified', true);
                        })->where('status', true)->get();

                        $avgRating = $allReviews->count() > 0 ? $allReviews->avg('mark') : 0;
                        $reviewsCount = $allReviews->count();
                        $supplierRatings[$userId] = [
                            'rating' => $avgRating,
                            'count' => $reviewsCount,
                        ];
                    }
                @endphp
                <div class="nav-dropdown hidden absolute left-0 top-full bg-white shadow-2xl border-t-4 border-[#ff0808] w-full z-50">
                    <div class="py-2">
                        <a href="{{ route('featured-suppliers') }}"
                           class="dropdown-link block px-4 py-2 text-xs text-gray-700 hover:bg-[#fff5f5] hover:text-[#ff0808] transition-colors font-semibold"
                           data-subdropdown="suppliers-all">
                            <i class="fas fa-list mr-2"></i>View All Featured Suppliers
                        </a>
                        @forelse($featuredSuppliers as $supplier)
                            @php
                                $rating = $supplierRatings[$supplier->user_id]['rating'] ?? 0;
                                $reviewsCount = $supplierRatings[$supplier->user_id]['count'] ?? 0;
                            @endphp
                            <a href="{{ route('business-profile.products', $supplier->id) }}"
                               class="block px-4 py-2 text-xs text-gray-700 hover:bg-[#fff5f5] hover:text-[#ff0808] transition-colors">
                                {{ $supplier->business_name }}
                            </a>
                        @empty
                            <p class="px-4 py-2 text-xs text-gray-500">No featured suppliers available.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- New Arrival --}}
            <div class="relative nav-item-wrapper flex-1">
                <a href="" class="nav-item block text-xs hover:bg-white/10 transition-colors text-center px-4 py-2.5 border-r border-white/10">
                    New Arrival
                </a>
                <div class="nav-dropdown hidden absolute left-0 top-full bg-white shadow-2xl border-t-4 border-[#ff0808] w-full z-50">
                    <div class="py-2">
                        <a href="#"
                           class="dropdown-link block px-4 py-2 text-xs text-gray-700 hover:bg-[#fff5f5] hover:text-[#ff0808] transition-colors"
                           data-subdropdown="new-products">
                            <i class="fas fa-box mr-2"></i>By Products
                        </a>
                        <a href="#"
                           class="dropdown-link block px-4 py-2 text-xs text-gray-700 hover:bg-[#fff5f5] hover:text-[#ff0808] transition-colors"
                           data-subdropdown="new-companies">
                            <i class="fas fa-building mr-2"></i>By Company
                        </a>
                    </div>
                </div>
            </div>

            {{-- Loadboard --}}
            <div class="relative nav-item-wrapper flex-1">
                <a href="" class="nav-item block text-xs hover:bg-white/10 transition-colors text-center px-4 py-2.5 border-r border-white/10">
                    Loadboard
                </a>
                <div class="nav-dropdown hidden absolute left-0 top-full bg-white shadow-2xl border-t-4 border-[#ff0808] w-full z-50">
                    <div class="py-2">
                        <a href="#"
                           class="dropdown-link block px-4 py-2 text-xs text-gray-700 hover:bg-[#fff5f5] hover:text-[#ff0808] transition-colors"
                           data-subdropdown="available-loads">
                            <i class="fas fa-box mr-2"></i>Available Loads
                        </a>
                        <a href="#"
                           class="dropdown-link block px-4 py-2 text-xs text-gray-700 hover:bg-[#fff5f5] hover:text-[#ff0808] transition-colors"
                           data-subdropdown="available-cars">
                            <i class="fas fa-car mr-2"></i>Available Cars
                        </a>
                    </div>
                </div>
            </div>

            {{-- Tradeshow --}}
            <div class="relative nav-item-wrapper flex-1">
                <a href="" class="nav-item block text-xs hover:bg-white/10 transition-colors text-center px-4 py-2.5 border-r border-white/10">
                    Tradeshow
                </a>
                <div class="nav-dropdown hidden absolute left-0 top-full bg-white shadow-2xl border-t-4 border-[#ff0808] w-full z-50">
                    <div class="py-2">
                        <a href="#"
                           class="dropdown-link block px-4 py-2 text-xs text-gray-700 hover:bg-[#fff5f5] hover:text-[#ff0808] transition-colors"
                           data-subdropdown="trade-shows">
                            <i class="fas fa-calendar-alt mr-2"></i>Trade Shows
                        </a>
                        <a href="#"
                           class="dropdown-link block px-4 py-2 text-xs text-gray-700 hover:bg-[#fff5f5] hover:text-[#ff0808] transition-colors"
                           data-subdropdown="showrooms">
                            <i class="fas fa-store mr-2"></i>Show Room
                        </a>
                    </div>
                </div>
            </div>

            {{-- Send RFQs --}}
            <div class="relative nav-item-wrapper flex-1">
                <a href="{{ route('rfqs.create') }}" class="nav-item block text-xs hover:bg-white/10 transition-colors text-center px-4 py-2.5">
                    Send RFQs
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Hero Section --}}
<div class="relative overflow-hidden hero-section" style="background-image: url('https://images.pexels.com/photos/11955628/pexels-photo-11955628.jpeg?w=1400&h=600&fit=crop'); background-size: cover; background-position: center;">
    <div class="absolute inset-0 bg-white/60"></div>

    <div class="container mx-auto px-4 sm:px-6 relative">
        <div class="flex gap-0 relative flex-col lg:flex-row">

            {{-- Left Sidebar - Marketplace --}}
            <div class="w-full lg:w-72 flex-shrink-0 relative z-30 mb-4 lg:mb-0" style="margin-top: 15px; margin-bottom: 15px;">
                <div class="bg-white shadow-xl overflow-hidden" style="height: auto; min-height: 250px; max-height: 420px; border-radius: 4px;">
                    <div class="px-3 py-2 border-b border-gray-200">
                        <button class="flex items-center justify-between w-full text-gray-800 font-semibold text-sm hover:text-[#ff0808]">
                            <span>All Categories</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                    </div>

                    <div class="overflow-y-auto" style="max-height: calc(420px - 40px);">
                        @php
                            // Get all active categories from database
                            $marketplaceCategories = App\Models\ProductCategory::where('status', 'active')
                                ->withCount(['products' => function($query) {
                                    $query->where('status', 'active')->where('is_admin_verified', true);
                                }])
                                ->with([
                                    'products' => function ($query) {
                                        $query->where('status', 'active')
                                            ->where('is_admin_verified', true)
                                            ->with('images')
                                            ->limit(10);
                                    }
                                ])
                                ->orderBy('name')
                                ->get();
                        @endphp

                        @foreach($marketplaceCategories as $category)
                            <div class="relative">
                                <button class="category-item w-full text-left px-3 py-2.5 hover:bg-[#fff5f5] transition-colors flex items-center gap-2 border-b border-gray-100"
                                        data-category-id="{{ $category->id }}"
                                        data-category-slug="{{ $category->slug }}"
                                        data-category-name="{{ $category->name }}">
                                    <span class="text-gray-600 font-semibold text-sm w-4">{{ $category->products_count }}</span>
                                    <span class="text-gray-700 text-sm flex-1">{{ $category->name }}</span>
                                </button>
                            </div>
                        @endforeach

                        {{-- All Regions Button --}}
                        @php
                            $activeRegionsCount = App\Models\Country::where('status', 'active')->count();
                            $activeRegions = App\Models\Country::where('status', 'active')
                                ->orderBy('name')
                                ->get();
                        @endphp
                        <div class="relative">
                            <button class="category-item w-full text-left px-3 py-2.5 hover:bg-[#fff5f5] transition-colors flex items-center gap-2 border-b border-gray-100"
                                    data-category-id="all-regions"
                                    data-category-slug="all-regions"
                                    data-category-name="All Regions">
                                <span class="text-gray-600 font-semibold text-sm w-4">{{ $activeRegionsCount }}</span>
                                <span class="text-gray-700 text-sm flex-1">All Regions</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Hero Content --}}
            <div class="flex-1 relative" style="min-height: 300px; lg:min-height: 450px;">
                <div class="relative z-20 h-full flex items-center px-4 lg:px-10 py-6 lg:py-10">
                    <div class="w-full" style="max-width: 750px;">
                        <h1 class="text-xl md:text-2xl lg:text-3xl xl:text-4xl font-black text-gray-900 leading-tight mb-3 uppercase">
                            Online Grocery Shopping
                        </h1>

                        <p class="text-sm lg:text-base text-gray-700 mb-4">
                            Source quality products from verified suppliers across Africa
                        </p>

                        <div class="flex flex-col sm:flex-row gap-3 mb-6">
                            <a href="" class="px-5 py-2 bg-[#ff0808] hover:bg-[#e00707] text-white font-bold rounded text-xs shadow-lg transition-all text-center">
                                Join Free to Source Products
                            </a>
                            <a href="" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded text-xs shadow-lg transition-all text-center">
                                List Your Company
                            </a>
                        </div>

                        {{-- Trust Indicators --}}
                        @php
                            $verifiedSuppliersCount = App\Models\BusinessProfile::where('verification_status', 'verified')->where('is_admin_verified', true)->count();
                            $totalTransactions = App\Models\Order::where('status', 'completed')->count();
                        @endphp
                        <div class="flex flex-col sm:flex-row gap-0 mb-6 bg-white/40 backdrop-blur-sm rounded-md overflow-hidden w-fit">
                            <div class="flex items-center gap-2 text-xs px-2.5 py-1.5 hover:bg-white/60 transition-colors border-b sm:border-b-0 sm:border-r border-white/30">
                                <div class="w-5 h-5 bg-teal-500 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-check text-white text-[10px]"></i>
                                </div>
                                <span class="text-gray-900 font-medium">{{ number_format($verifiedSuppliersCount) }}+ Verified Suppliers</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs px-2.5 py-1.5 hover:bg-white/60 transition-colors border-b sm:border-b-0 sm:border-r border-white/30">
                                <div class="w-5 h-5 bg-teal-500 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-shield-alt text-white text-[10px]"></i>
                                </div>
                                <span class="text-gray-900 font-medium">{{ number_format($totalTransactions) }}+ Secure Transactions</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs px-2.5 py-1.5 hover:bg-white/60 transition-colors">
                                <div class="w-5 h-5 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-globe text-white text-[10px]"></i>
                                </div>
                                <span class="text-gray-900 font-medium">Global Buyer Network</span>
                            </div>
                        </div>

                        {{-- Search Bar - Fully Responsive --}}
                        <div class="flex flex-col sm:flex-row gap-2 items-stretch w-full relative" style="z-index: 1000; max-width: 700px;">
                            <form action="{{ route('global.search') }}" method="GET" class="bg-white shadow-xl p-2 flex-1 flex flex-col sm:flex-row gap-2 items-stretch" style="border-radius: 4px;">
                                <select name="type" class="flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-xs text-gray-700 hover:bg-gray-50 flex-shrink-0" style="border-radius: 4px;">
                                    <option value="products">Products</option>
                                    <option value="suppliers">Suppliers</option>
                                    <option value="rfqs">RFQs</option>
                                </select>

                                <input type="text"
                                       name="query"
                                       placeholder="Search for products..."
                                       class="flex-1 px-3 py-2 border border-gray-300 text-xs focus:outline-none focus:border-[#ff0808]"
                                       style="border-radius: 4px; min-width: 150px;">

                                <button type="submit" class="px-5 py-2 bg-[#ff0808] hover:bg-[#e00707] text-white font-semibold text-xs flex items-center justify-center flex-shrink-0" style="border-radius: 4px; min-width: 50px;">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Girl Image - PUSHED FAR RIGHT --}}
                <div class="hidden xl:block absolute bottom-0 pointer-events-none" style="width: 380px; height: 700px; top: -250px; right: 0; z-index: 15;">
                    <img src="{{ asset('girlimage.png') }}"
                         alt="African professional"
                         class="w-full h-full object-contain object-bottom"
                         style="filter: drop-shadow(0 10px 25px rgba(0,0,0,0.15));">
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Full-Width Subdropdown --}}
<div id="categorySubDropdown" class="hidden fixed bg-white shadow-2xl border-t-4 border-[#ff0808] z-[9999]" style="top: 0; left: 0; right: 0; margin-left: 288px;">
    <div class="container mx-auto px-6 py-6">
        <div class="flex items-start justify-between mb-4">
            <h3 id="subDropdownTitle" class="text-lg font-bold text-gray-900"></h3>
            <button id="closeSubDropdown" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div id="subDropdownContent" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            {{-- Content will be dynamically loaded based on selection --}}
        </div>

        <div class="mt-6 pt-4 border-t">
            <a href="#" id="viewAllLink" class="text-[#ff0808] hover:text-[#e00707] font-semibold text-sm">
                View All →
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Store category data
    const categoryData = {
        @foreach($marketplaceCategories as $category)
        '{{ $category->id }}': {
            name: '{{ $category->name }}',
            slug: '{{ $category->slug }}',
            products: [
                @foreach($category->products->take(10) as $product)
                {
                    id: {{ $product->id }},
                    name: '{{ addslashes($product->name) }}',
                    slug: '{{ $product->slug }}',
                    price: '{{ number_format($product->base_price, 2) }}',
                    currency: '{{ $product->currency }}',
                    image: '{{ ($product->images->where("is_primary", true)->first() ?? $product->images->first())?->image_url ?? "" }}',
                    categoryName: '{{ $product->productCategory->name ?? "Uncategorized" }}'
                },
                @endforeach
            ]
        },
        @endforeach
        'all-regions': {
            name: 'All Regions',
            slug: 'all-regions',
            regions: [
                @foreach($activeRegions as $region)
                {
                    id: {{ $region->id }},
                    name: '{{ addslashes($region->name) }}',
                    code: '{{ $region->code }}'
                },
                @endforeach
            ]
        }
    };

    // Store suppliers data
    const suppliersData = [
        @foreach($featuredSuppliers as $supplier)
        {
            id: {{ $supplier->id }},
            name: '{{ addslashes($supplier->business_name) }}',
            rating: {{ $supplierRatings[$supplier->user_id]['rating'] ?? 0 }},
            reviewsCount: {{ $supplierRatings[$supplier->user_id]['count'] ?? 0 }}
        },
        @endforeach
    ];

    // Scroll behavior for topbar
    let lastScroll = 0;
    const topBar = document.getElementById('topBar');
    const mainHeader = document.getElementById('mainHeader');
    const navBar = document.getElementById('navBar');

    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;

        if (currentScroll > 50) {
            topBar.style.transform = 'translateY(-100%)';
            mainHeader.style.top = '0';
            if (navBar) {
                navBar.style.top = mainHeader.offsetHeight + 'px';
            }
        } else {
            topBar.style.transform = 'translateY(0)';
            mainHeader.style.top = '0';
            if (navBar) {
                navBar.style.top = '0';
            }
        }

        lastScroll = currentScroll;
    });

    // Navigation dropdowns - HOVER to show simple dropdown
    const navWrappers = document.querySelectorAll('.nav-item-wrapper');
    navWrappers.forEach(wrapper => {
        const navItem = wrapper.querySelector('.nav-item');
        const navDropdown = wrapper.querySelector('.nav-dropdown');
        if (navDropdown) {
            let hideTimeout;
            const showNavDropdown = () => {
                clearTimeout(hideTimeout);
                document.querySelectorAll('.nav-dropdown').forEach(d => {
                    if (d !== navDropdown) d.classList.add('hidden');
                });
                navDropdown.classList.remove('hidden');
            };
            const hideNavDropdown = () => {
                hideTimeout = setTimeout(() => {
                    navDropdown.classList.add('hidden');
                }, 150);
            };
            navItem.addEventListener('mouseenter', showNavDropdown);
            navItem.addEventListener('mouseleave', hideNavDropdown);
            navDropdown.addEventListener('mouseenter', () => clearTimeout(hideTimeout));
            navDropdown.addEventListener('mouseleave', hideNavDropdown);
        }
    });

    // Dropdown links - CLICK to show full subdropdown
    const dropdownLinks = document.querySelectorAll('.dropdown-link');
    const subDropdown = document.getElementById('categorySubDropdown');
    const subDropdownTitle = document.getElementById('subDropdownTitle');
    const subDropdownContent = document.getElementById('subDropdownContent');
    const viewAllLink = document.getElementById('viewAllLink');
    const closeSubDropdown = document.getElementById('closeSubDropdown');

    const colorClasses = [
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

    dropdownLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const subdropdownType = this.getAttribute('data-subdropdown');

            // Hide all simple dropdowns
            document.querySelectorAll('.nav-dropdown').forEach(d => d.classList.add('hidden'));

            // Show appropriate full subdropdown
            switch(subdropdownType) {
                case 'suppliers-all':
                    showSuppliersSubdropdown();
                    break;
                case 'new-products':
                    showNewProductsSubdropdown();
                    break;
                case 'new-companies':
                    showNewCompaniesSubdropdown();
                    break;
                case 'available-loads':
                    showAvailableLoadsSubdropdown();
                    break;
                case 'available-cars':
                    showAvailableCarsSubdropdown();
                    break;
                case 'trade-shows':
                    showTradeShowsSubdropdown();
                    break;
                case 'showrooms':
                    showShowroomsSubdropdown();
                    break;
                case 'rfq-templates':
                    showRFQTemplatesSubdropdown();
                    break;
            }
        });
    });

    function showSuppliersSubdropdown() {
        subDropdownTitle.textContent = 'Featured Suppliers';
        viewAllLink.href = '{{ route("featured-suppliers") }}';

        subDropdownContent.innerHTML = '';
        subDropdownContent.className = 'grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4';

        suppliersData.forEach((supplier, index) => {
            const initial = supplier.name.substring(0, 1).toUpperCase();
            const supplierCard = document.createElement('a');
            supplierCard.href = `/business-profile/${supplier.id}/products`;
            supplierCard.className = 'group';
            supplierCard.innerHTML = `
                <div class="bg-white border-2 border-gray-200 rounded-lg p-4 hover:border-[#ff0808] hover:shadow-md transition-all text-center">
                    <div class="flex justify-center items-center mx-auto mb-3 w-16 h-16 text-xl font-bold text-gray-700 bg-gray-100 rounded-full">
                        ${initial}
                    </div>
                    <h4 class="font-semibold text-gray-900 text-sm group-hover:text-[#ff0808] transition-colors line-clamp-2">
                        ${supplier.name}
                    </h4>
                    ${supplier.rating > 0 ? `
                        <p class="mt-1 text-xs text-gray-600">⭐ ${supplier.rating.toFixed(1)} (${supplier.reviewsCount})</p>
                    ` : `
                        <p class="mt-1 text-xs text-gray-500">No ratings yet</p>
                    `}
                    <span class="inline-block px-2 py-1 mt-2 text-xs text-green-700 bg-green-50 rounded border border-green-200">Verified</span>
                </div>
            `;
            subDropdownContent.appendChild(supplierCard);
        });

        positionAndShowDropdown();
    }

    function showNewProductsSubdropdown() {
        subDropdownTitle.textContent = 'New Products';
        viewAllLink.href = '#';

        subDropdownContent.innerHTML = '';
        subDropdownContent.className = 'grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4';

        subDropdownContent.innerHTML = `
            <div class="col-span-full py-8 text-center">
                <div class="text-6xl mb-3">📦</div>
                <p class="text-sm text-gray-500">New products will appear here</p>
            </div>
        `;

        positionAndShowDropdown();
    }

    function showNewCompaniesSubdropdown() {
        subDropdownTitle.textContent = 'New Companies';
        viewAllLink.href = '#';

        subDropdownContent.innerHTML = '';
        subDropdownContent.className = 'grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4';

        subDropdownContent.innerHTML = `
            <div class="col-span-full py-8 text-center">
                <div class="text-6xl mb-3">🏢</div>
                <p class="text-sm text-gray-500">New companies will appear here</p>
            </div>
        `;

        positionAndShowDropdown();
    }

    function showAvailableLoadsSubdropdown() {
        subDropdownTitle.textContent = 'Available Loads';
        viewAllLink.href = '{{ route("loadboard.loads.index") }}';

        subDropdownContent.innerHTML = '';
        subDropdownContent.className = 'grid grid-cols-2 md:grid-cols-4 gap-3';

        subDropdownContent.innerHTML = `
            <div class="col-span-full py-8 text-center">
                <div class="text-6xl mb-3">🚚</div>
                <p class="text-sm text-gray-500">Available loads will appear here</p>
            </div>
        `;

        positionAndShowDropdown();
    }

    function showAvailableCarsSubdropdown() {
        subDropdownTitle.textContent = 'Available Vehicles';
        viewAllLink.href = '{{ route("loadboard.loads.index") }}';

        subDropdownContent.innerHTML = '';
        subDropdownContent.className = 'grid grid-cols-2 md:grid-cols-4 gap-3';

        subDropdownContent.innerHTML = `
            <div class="col-span-full py-8 text-center">
                <div class="text-6xl mb-3">🚗</div>
                <p class="text-sm text-gray-500">Available vehicles will appear here</p>
            </div>
        `;

        positionAndShowDropdown();
    }

    function showTradeShowsSubdropdown() {
        subDropdownTitle.textContent = 'Upcoming Trade Shows';
        viewAllLink.href = '{{ route("tradeshows.index") }}';

        subDropdownContent.innerHTML = '';
        subDropdownContent.className = 'grid grid-cols-1 md:grid-cols-3 gap-3';

        subDropdownContent.innerHTML = `
            <div class="col-span-full py-8 text-center">
                <div class="text-6xl mb-3">📅</div>
                <p class="text-sm text-gray-500">Upcoming tradeshows will appear here</p>
            </div>
        `;

        positionAndShowDropdown();
    }

    function showShowroomsSubdropdown() {
        subDropdownTitle.textContent = 'Featured Showrooms';
        viewAllLink.href = '{{ route("tradeshows.index") }}';

        subDropdownContent.innerHTML = '';
        subDropdownContent.className = 'grid grid-cols-2 md:grid-cols-4 gap-3';

        subDropdownContent.innerHTML = `
            <div class="col-span-full py-8 text-center">
                <div class="text-6xl mb-3">🏬</div>
                <p class="text-sm text-gray-500">Featured showrooms will appear here</p>
            </div>
        `;

        positionAndShowDropdown();
    }

    function showRFQTemplatesSubdropdown() {
        subDropdownTitle.textContent = 'RFQ Templates';
        viewAllLink.href = '{{ route("rfqs.create") }}';

        subDropdownContent.innerHTML = '';
        subDropdownContent.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4';

        subDropdownContent.innerHTML = `
            <div class="col-span-full py-8 text-center">
                <div class="text-6xl mb-3">📋</div>
                <p class="text-sm text-gray-500">RFQ templates will appear here</p>
            </div>
        `;

        positionAndShowDropdown();
    }

    function positionAndShowDropdown() {
        const navBarElement = document.querySelector('.bg-\\[\\#1a2942\\]');
        if (navBarElement) {
            const navBarRect = navBarElement.getBoundingClientRect();
            subDropdown.style.top = (navBarRect.bottom) + 'px';
        }
        subDropdown.classList.remove('hidden');
    }

    // Category subdropdown (original functionality from sidebar)
    const categoryItems = document.querySelectorAll('.category-item');

    categoryItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const categoryId = this.getAttribute('data-category-id');
            const categoryName = this.getAttribute('data-category-name');
            const categorySlug = this.getAttribute('data-category-slug');
            const data = categoryData[categoryId];

            if (!data) return;

            subDropdownTitle.textContent = categoryName;

            // Update view all link
            if (categoryId === 'all-regions') {
                viewAllLink.href = '#';
            } else {
                viewAllLink.href = `/products/search?type=category&slug=${categorySlug}`;
            }

            // Clear previous content
            subDropdownContent.innerHTML = '';
            subDropdownContent.className = 'grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4';

            if (categoryId === 'all-regions') {
                // Display regions
                data.regions.forEach((region, index) => {
                    const regionCard = document.createElement('div');
                    regionCard.className = 'bg-white border-2 border-gray-200 rounded-lg p-4 hover:border-[#ff0808] hover:shadow-md transition-all text-center cursor-pointer';
                    regionCard.innerHTML = `
                        <div class="flex justify-center items-center mx-auto mb-3 w-16 h-16 text-2xl font-bold text-gray-700 bg-gray-100 rounded-full">
                            ${region.code || region.name.substring(0, 2).toUpperCase()}
                        </div>
                        <h4 class="font-semibold text-gray-900 text-sm">
                            ${region.name}
                        </h4>
                    `;
                    subDropdownContent.appendChild(regionCard);
                });
            } else {
                // Display products
                if (data.products.length === 0) {
                    subDropdownContent.innerHTML = '<div class="col-span-full py-8 text-center"><p class="text-gray-500 text-sm">No products available in this category yet.</p></div>';
                } else {
                    data.products.forEach((product, index) => {
                        const colorClass = colorClasses[index % colorClasses.length];
                        const productCard = document.createElement('a');
                        productCard.href = `/products/${product.slug}`;
                        productCard.className = 'group';
                        productCard.innerHTML = `
                            <div class="bg-gradient-to-br ${colorClass} rounded-lg p-4 text-center hover:shadow-lg transition-all">
                                ${product.image ? `
                                    <img src="${product.image}" alt="${product.name}" class="object-cover mb-3 w-full h-32 rounded-lg">
                                ` : `
                                    <div class="mb-3 text-4xl">📦</div>
                                `}
                                <h4 class="font-bold text-gray-900 group-hover:text-[#ff0808] text-sm line-clamp-2">
                                    ${product.name}
                                </h4>
                                <p class="mt-1 text-xs text-gray-600">
                                    ${product.price} ${product.currency}
                                </p>
                            </div>
                        `;
                        subDropdownContent.appendChild(productCard);
                    });
                }
            }

            positionAndShowDropdown();
        });
    });

    if (closeSubDropdown) {
        closeSubDropdown.addEventListener('click', function() {
            subDropdown.classList.add('hidden');
        });
    }

    document.addEventListener('click', function(e) {
        if (subDropdown && !subDropdown.contains(e.target) &&
            !e.target.closest('.category-item') &&
            !e.target.closest('.dropdown-link') &&
            !e.target.closest('.nav-dropdown')) {
            subDropdown.classList.add('hidden');
        }
    });
});
</script>
@endpush

<style>
.category-item:hover {
    background-color: #fff5f5;
    cursor: pointer;
}
.nav-item:hover {
    background-color: rgba(255, 255, 255, 0.1);
}
.hero-section {
    position: relative;
    z-index: 1;
}
#categorySubDropdown {
    transition: all 0.3s ease;
}

/* Topbar hide/show transition */
#topBar {
    position: relative;
    z-index: 60;
}

#mainHeader.scrolled {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Responsive Adjustments */
@media (max-width: 1279px) {
    #categorySubDropdown {
        margin-left: 0 !important;
    }
}
@media (max-width: 1024px) {
    .xl\:block {
        display: none !important;
    }
}
@media (max-width: 768px) {
    .hero-section {
        min-height: auto !important;
    }
}
</style>

<nav id="main-nav" class="bg-white shadow-md transition-all duration-300">
    @php
        $settings = App\Models\Setting::all();
    @endphp
    <!-- Top Bar -->
    <div class="bg-[#ff0808] text-white py-2">
    <div class="container flex justify-between items-center px-4 mx-auto text-xs md:text-sm">
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
        <div class="flex items-center px-2">
            @php
                $countries = App\Models\Country::where('status', 'active')->orderBy('name')->limit(7)->get();
            @endphp
            <select name="country" id="country" class="text-black text-[10px] md:text-xs py-1 px-1.5 md:px-2 rounded bg-white">
                <option selected disabled>Select a country</option>
                @foreach ($countries as $country)
                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                @endforeach
            </select>
        </div>
        </div>
    </div>
</div>

    <!-- Main Navigation -->
    <div class="container px-4 py-3 mx-auto md:py-4 lg:py-5">
        <div class="flex gap-3 justify-between items-center md:gap-4 lg:gap-6">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="/" class="flex items-center">
                    <img src="https://afrisellers.com/public/uploads/all/rcIW6v7SfbxlCbrTIBU6CXQNggsQbKVO1a8vXheE.png"
                        alt="AfriSellers" class="h-8 sm:h-10 md:h-12 lg:h-14">
                </a>
            </div>

            <!-- Search Bar - Hidden on mobile, shown on md+ -->
            <div class="hidden flex-1 max-w-2xl md:flex">
            <div class="relative w-full">
                <form action="{{ route('global.search') }}" method="GET" class="relative w-full">
                    <input type="text" name="query" id="navSearchInput" placeholder="{{ __('messages.search_placeholder') }}"
                        class="w-full px-3 py-2 pr-24 md:px-4 md:py-2.5 lg:py-3 lg:pr-32 rounded-lg border-2 border-gray-300 focus:border-[#ff0808] focus:outline-none font-medium text-sm"
                        autocomplete="off">
                    <button type="submit"
                        class="absolute right-0 top-0 h-full px-4 md:px-5 lg:px-6 bg-[#ff0808] text-white rounded-r-lg hover:bg-red-700 transition-colors font-bold text-sm">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                <div id="navSearchResults" class="absolute w-full bg-white rounded-lg shadow-xl mt-1 hidden z-50 max-h-96 overflow-y-auto"></div>
            </div>
            </div>

            <!-- Right Menu -->
            <div class="flex gap-2 items-center md:gap-3 lg:gap-6">
                <!-- Mobile Menu Toggle -->
                <button id="mobile-menu-toggle" class="md:hidden text-gray-700 hover:text-[#ff0808]">
                    <i class="text-lg fas fa-bars"></i>
                </button>

                <!-- User Authentication Section -->
                <div class="flex gap-2 items-center md:gap-3">
                    @auth
                        <!-- Authenticated User -->
                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-[#ff0808] flex items-center justify-center">
                            <span class="text-xs md:text-sm font-semibold text-white">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </span>
                        </div>
                        <div class="hidden gap-2 items-center text-gray-700 lg:flex">
                            @php
                                $user = auth()->user();
                                $isAdmin = false;
                                $vendor = null;

                                if ($user) {
                                    $isAdmin = $user
                                        ->roles()
                                        ->where('roles.id', 1)
                                        ->where('roles.name', 'Admin')
                                        ->where('roles.slug', 'admin')
                                        ->exists();

                                    if (!$isAdmin) {
                                        $vendor = App\Models\Vendor\Vendor::where('user_id', auth()->id())->first();
                                    }
                                }
                            @endphp

                            @if ($isAdmin)
                                <a href="{{ route('admin.dashboard.home') }}"
                                    class="hover:text-[#ff0808] font-semibold transition-colors text-sm">
                                    {{ __('messages.dashboard') }}
                                </a>
                            @elseif($vendor)
                                <a href="{{ route('vendor.dashboard.home') }}"
                                    class="hover:text-[#ff0808] font-semibold transition-colors text-sm">
                                    {{ __('messages.dashboard') }}
                                </a>
                            @else
                                <a href="{{ route('buyer.dashboard.home') }}"
                                    class="hover:text-[#ff0808] font-semibold transition-colors text-sm">
                                    {{ __('messages.dashboard') }}
                                </a>
                            @endif

                            <span class="text-gray-400">|</span>

                            <form action="{{ route('auth.logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="hover:text-[#ff0808] font-semibold transition-colors text-sm">
                                    {{ __('messages.logout') }}
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- Guest User -->
                        <div class="flex justify-center items-center w-8 h-8 md:w-10 md:h-10 rounded-full border-2 border-gray-300">
                            <i class="text-xs md:text-sm text-gray-400 fas fa-user"></i>
                        </div>
                        <div class="hidden gap-2 items-center text-gray-700 lg:flex">
                            <a href="{{ route('auth.signin') }}"
                                class="hover:text-[#ff0808] font-semibold transition-colors text-sm">
                                {{ __('messages.login') }}
                            </a>
                            <span class="text-gray-400">|</span>
                            <a href="{{ route('auth.register') }}"
                                class="hover:text-[#ff0808] font-semibold transition-colors text-sm">
                                {{ __('messages.registration') }}
                            </a>
                        </div>
                    @endauth
                </div>

                <!-- Livestream Link -->
                <a href="{{ route('livestream') }}"
                    class="text-gray-700 hover:text-[#ff0808] flex items-center gap-1.5 md:gap-2 relative transition-colors">
                    <div class="relative">
                        <i class="text-base md:text-lg lg:text-xl fas fa-video"></i>
                        <span
                            class="absolute -top-1.5 -right-1.5 md:-top-2 md:-right-2 bg-[#ff0808] text-white text-[10px] md:text-xs rounded-full w-4 h-4 md:w-5 md:h-5 flex items-center justify-center font-bold shadow-md">7</span>
                    </div>
                    <span
                        class="hidden lg:flex items-center gap-1 bg-[#ff0808] text-white text-xs px-2 py-0.5 rounded-full font-bold">
                        <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
                        {{ __('messages.live') }}
                    </span>
                </a>

                <!-- Cart Icon -->
            <a href="{{ route('cart.index') }}"
                class="relative flex gap-2 items-center text-gray-700 transition-colors hover:text-[#ff0808]">
                <div class="relative">
                    <i class="text-base fas fa-shopping-cart md:text-lg lg:text-xl"></i>
                    <span id="cartCount"
                        class="absolute -top-1.5 -right-1.5 md:-top-2 md:-right-2 bg-[#ff0808] text-white text-[10px] md:text-xs rounded-full w-4 h-4 md:w-5 md:h-5 flex items-center justify-center font-bold shadow-md">
                        0
                    </span>
                </div>
                <span class="hidden text-sm font-semibold lg:inline">Cart</span>
            </a>
            </div>
        </div>

        <!-- Mobile Search Bar -->
        <div class="mt-3 md:hidden">
            <div class="relative">
                <input type="text" placeholder="{{ __('messages.search_mobile_placeholder') }}"
                    class="w-full px-3 py-2 pr-14 rounded-lg border-2 border-gray-300 focus:border-[#ff0808] focus:outline-none font-medium text-sm">
                <button
                    class="absolute right-0 top-0 h-full px-3 bg-[#ff0808] text-white rounded-r-lg hover:bg-red-700 transition-colors text-sm">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>


    <!-- Category Navigation -->
    @if (Route::currentRouteName() === 'home')
        <div class="relative bg-gray-100 border-t border-gray-200">
            <div class="container px-4 mx-auto">
                <div class="flex overflow-x-auto gap-3 md:gap-4 lg:gap-6 items-center py-2.5 md:py-3 scrollbar-hide">
                    <!-- All Categories -->
                    <div class="relative">
                        <button id="categories-btn"
                            class="flex items-center gap-1.5 md:gap-2 text-gray-700 hover:text-[#ff0808] font-bold whitespace-nowrap transition-colors bg-white px-3 py-1.5 md:px-4 md:py-2 rounded-lg shadow-sm text-xs md:text-sm">
                            <i class="text-xs md:text-sm fas fa-th"></i>
                            <span>{{ __('messages.all_categories') }}</span>
                            <i class="text-[10px] md:text-xs fas fa-chevron-down"></i>
                        </button>
                    </div>

                    <!-- Featured Suppliers -->
                    <div class="relative">
                        <a href="#"
                            class="nav-dropdown-trigger text-gray-700 hover:text-[#ff0808] font-bold whitespace-nowrap transition-colors flex items-center gap-1.5 md:gap-2 text-xs md:text-sm"
                            data-dropdown="suppliers-menu">
                            {{ __('messages.featured_suppliers') }}
                            <i class="text-[10px] md:text-xs fas fa-chevron-down"></i>
                        </a>
                    </div>

                    <!-- New Arrivals -->
                    <div class="relative">
                        <a href="#"
                            class="nav-dropdown-trigger text-gray-700 hover:text-[#ff0808] font-bold whitespace-nowrap transition-colors flex items-center gap-1.5 md:gap-2 text-xs md:text-sm"
                            data-dropdown="arrivals-menu">
                            {{ __('messages.new_arrivals') }}
                            <i class="text-[10px] md:text-xs fas fa-chevron-down"></i>
                        </a>
                    </div>

                    <!-- Top RFQs -->
                    @if($canSeeTopRfqs ?? false)
                    <div class="relative">
                        <a href="#"
                            class="nav-dropdown-trigger text-gray-700 hover:text-[#ff0808] font-bold whitespace-nowrap transition-colors flex items-center gap-1.5 md:gap-2 text-xs md:text-sm"
                            data-dropdown="rfq-menu">
                            {{ __('messages.top_rfqs') }}
                            <i class="text-[10px] md:text-xs fas fa-chevron-down"></i>
                        </a>
                    </div>
                    @endif

                    <!-- Loadboard -->
                    <div class="relative">
                        <a href="#"
                            class="nav-dropdown-trigger text-gray-700 hover:text-[#ff0808] font-bold whitespace-nowrap transition-colors flex items-center gap-1.5 md:gap-2 text-xs md:text-sm"
                            data-dropdown="loadboard-menu">
                            {{ __('messages.loadboard') }}
                            <i class="text-[10px] md:text-xs fas fa-chevron-down"></i>
                        </a>
                    </div>

                    <!-- Trade Shows -->
                    <div class="relative">
                        <a href="#"
                            class="nav-dropdown-trigger text-gray-700 hover:text-[#ff0808] font-bold whitespace-nowrap transition-colors flex items-center gap-1.5 md:gap-2 text-xs md:text-sm"
                            data-dropdown="tradeshows-menu">
                            {{ __('messages.trade_shows') }}
                            <i class="text-[10px] md:text-xs fas fa-chevron-down"></i>
                        </a>
                    </div>

                    <!-- Send RFQs Button -->
                    <div class="relative">
                        <a href="{{ route('rfqs.create') }}"
                            class="flex items-center gap-1.5 md:gap-2 text-white hover:text-white font-bold whitespace-nowrap transition-colors bg-[#ff0808] hover:bg-red-700 px-3 py-1.5 md:px-4 md:py-2 rounded-lg shadow-sm text-xs md:text-sm">
                            <i class="text-xs md:text-sm fas fa-file-invoice"></i>
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
                                class="flex gap-1.5 md:gap-2 items-center mb-3 md:mb-4 lg:mb-6 text-sm md:text-base lg:text-lg font-black text-gray-900">
                                <i class="fas fa-layer-group text-[#ff0808] text-xs md:text-sm"></i>
                                {{ __('messages.browse_by_category') }}
                            </h3>
                            <div class="pb-2 relative">
                                <input type="text"
                                       id="category-search-input"
                                       placeholder="Search categories..."
                                       class="w-full px-3 py-2 md:px-4 md:py-2.5 pl-9 md:pl-10 pr-3 md:pr-4 rounded-lg border-2 border-gray-300 focus:border-[#ff0808] focus:outline-none font-medium text-xs md:text-sm">
                                <i class="absolute left-2.5 md:left-3 top-1/2 -translate-y-1/2 mt-1 text-gray-400 fas fa-search text-xs md:text-sm"></i>
                            </div>
                            <div id="categories-list" class="space-y-1.5 md:space-y-2 lg:space-y-3 max-h-[400px] md:max-h-[500px] overflow-y-auto">
                                @foreach ($categories as $index => $category)
                                    <button
                                        class="category-sidebar-btn w-full text-left px-2.5 md:px-3 lg:px-4 py-2 md:py-2.5 lg:py-3 rounded-lg {{ $index === 0 ? 'bg-[#ff0808] text-white font-bold' : 'bg-white text-gray-700 font-semibold' }} transition-all hover:bg-red-50 hover:text-[#ff0808] text-xs md:text-sm"
                                        data-category="category-{{ $category->id }}"
                                        data-category-name="{{ strtolower($category->name) }}">
                                        <i class="mr-1.5 md:mr-2 fas fa-box text-xs md:text-sm"></i><span class="category-name">{{ $category->name }}</span>
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
                                            class="flex gap-1.5 md:gap-2 lg:gap-3 items-center text-base md:text-xl lg:text-2xl font-black text-gray-900">
                                            <span class="text-xl md:text-2xl lg:text-4xl">📦</span>
                                            <span class="hidden sm:inline">{{ $category->name }}</span>
                                            <span
                                                class="sm:hidden">{{ \Illuminate\Support\Str::limit($category->name, 15) }}</span>
                                        </h2>
                                        <a href="{{ route('products.search', ['type' => 'category', 'slug' => \Illuminate\Support\Str::slug($category->name)]) }}"
                                            class="text-[#ff0808] hover:text-red-700 font-bold flex items-center gap-1 md:gap-2 text-xs md:text-sm">
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
                                                        <div class="mb-1.5 md:mb-2 lg:mb-4 text-xl md:text-2xl lg:text-4xl">📦</div>
                                                    @endif
                                                    <h4
                                                        class="font-bold text-gray-900 group-hover:text-[#ff0808] text-xs md:text-sm line-clamp-2">
                                                        {{ $product->name }}
                                                    </h4>
                                                    <p class="mt-0.5 md:mt-1 text-[10px] md:text-xs lg:text-sm text-gray-600">
                                                        {{ number_format($product->base_price, 2) }}
                                                        {{ $product->currency }}
                                                    </p>
                                                </div>
                                            </a>
                                        @empty
                                            <div class="col-span-4 py-6 md:py-8 text-center">
                                                <p class="text-gray-500 text-sm">No products available in this category yet.
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
                        <h2 class="text-sm md:text-base lg:text-xl font-bold text-gray-900">Featured Suppliers</h2>
                        <a href="{{ route('featured-suppliers') }}"
                            class="text-[#ff0808] hover:text-red-700 font-semibold text-xs md:text-sm flex items-center gap-1 md:gap-2">
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
                                        class="flex justify-center items-center mx-auto mb-1.5 md:mb-2 lg:mb-3 w-10 h-10 md:w-12 md:h-12 lg:w-14 lg:h-14 text-xs md:text-sm lg:text-lg font-bold text-gray-700 bg-gray-100 rounded-full">
                                        {{ $initial }}</div>
                                    <h4
                                        class="font-semibold text-gray-900 text-[10px] md:text-xs lg:text-sm group-hover:text-[#ff0808] transition-colors line-clamp-2">
                                        {{ $supplier->business_name }}</h4>
                                    @if ($rating > 0)
                                        <p class="mt-0.5 md:mt-1 text-[10px] md:text-xs text-gray-600">⭐ {{ number_format($rating, 1) }}
                                            ({{ number_format($reviewsCount) }})
                                        </p>
                                    @else
                                        <p class="mt-0.5 md:mt-1 text-[10px] md:text-xs text-gray-500">No ratings yet</p>
                                    @endif
                                    <span
                                        class="inline-block px-1.5 md:px-2 py-0.5 md:py-1 mt-1 md:mt-2 text-[10px] md:text-xs text-green-700 bg-green-50 rounded border border-green-200">Verified</span>
                                </div>
                            </a>
                        @empty
                            <div class="col-span-full py-6 md:py-8 text-center">
                                <p class="text-xs md:text-sm text-gray-500">No featured suppliers available yet.</p>
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
                class="arrivals-tab px-3 py-1.5 md:px-4 md:py-2 font-bold text-xs md:text-sm border-b-4 border-green-500 text-green-500 transition-all">
                <i class="fas fa-box mr-1 md:mr-2"></i>By Products
            </button>
            <button id="companies-arrivals-tab"
                class="arrivals-tab px-3 py-1.5 md:px-4 md:py-2 font-bold text-xs md:text-sm border-b-4 border-transparent text-gray-500 hover:text-green-500 transition-all">
                <i class="fas fa-building mr-1 md:mr-2"></i>By Company
            </button>
        </div>

        <!-- By Products Content -->
        <div id="products-arrivals-content" class="arrivals-content">
            <div class="flex justify-between items-center mb-2 md:mb-3">
                <h2 class="text-sm md:text-base font-bold text-gray-900">New Products (Last 2 Days)</h2>
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
                                    <span class="text-2xl md:text-lg">📦</span>
                                </div>
                            @endif
                            <span class="inline-block px-2 py-0.5 text-[9px] md:text-[10px] font-bold text-green-700 bg-green-50 rounded border border-green-200 mb-1">
                                NEW
                            </span>
                            <h4 class="font-semibold text-gray-900 group-hover:text-green-600 text-xs md:text-sm transition-colors line-clamp-2">
                                {{ $product->name }}
                            </h4>
                            <p class="mt-0.5 text-[10px] md:text-xs text-gray-600">{{ $categoryName }}</p>
                            <p class="mt-0.5 text-[10px] md:text-xs text-green-600 font-medium">
                                {{ number_format($product->base_price, 2) }} {{ $product->currency }}
                            </p>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-8 text-center">
                        <div class="text-6xl mb-3">📦</div>
                        <p class="text-sm text-gray-500">No new products in the last 2 days.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- By Company Content -->
        <div id="companies-arrivals-content" class="arrivals-content hidden">
            <div class="flex justify-between items-center mb-2 md:mb-3">
                <h2 class="text-sm md:text-base font-bold text-gray-900">New Companies (Last 2 Days)</h2>
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
                                <div class="flex justify-center items-center mx-auto mb-1.5 md:mb-2 w-16 h-16 md:w-20 md:h-20 text-sm md:text-lg font-bold text-gray-700 bg-gray-100 rounded-full">
                                    {{ $initial }}
                                </div>
                            @endif
                            <span class="inline-block px-2 py-0.5 text-[9px] md:text-[10px] font-bold text-green-700 bg-green-50 rounded border border-green-200 mb-1">
                                {{ $productCount }} NEW {{ $productCount == 1 ? 'ITEM' : 'ITEMS' }}
                            </span>
                            <h4 class="font-semibold text-gray-900 group-hover:text-green-600 text-xs md:text-sm transition-colors line-clamp-2">
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
                        <p class="text-sm text-gray-500">No new companies in the last 2 days.</p>
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
            <h2 class="text-xl font-bold text-gray-900 md:text-2xl">Top RFQs</h2>
            <a href="{{ route('rfqs.create') }}"
                class="text-[#ff0808] hover:text-red-700 font-semibold text-sm md:text-base flex items-center gap-2">
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
                            <h4 class="mb-2 text-sm font-bold text-gray-900 md:text-base line-clamp-2">{{ $title }}</h4>
                            <p class="mb-3 text-xs text-gray-600 md:text-sm line-clamp-2">{{ $messagePreview }}</p>
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
                                class="inline-block px-3 py-1 text-xs font-bold text-white {{ $colorClass['bg'] }} rounded-lg">
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
                            class="loadboard-tab px-4 py-2 md:px-6 md:py-3 font-bold text-sm md:text-base border-b-4 border-purple-500 text-purple-500 transition-all">
                            <i class="fas fa-box mr-2"></i>Available Loads
                        </button>
                        <button id="cars-tab"
                            class="loadboard-tab px-4 py-2 md:px-6 md:py-3 font-bold text-sm md:text-base border-b-4 border-transparent text-gray-500 hover:text-purple-500 transition-all">
                            <i class="fas fa-car mr-2"></i>Available Cars
                        </button>
                    </div>

<!-- Available Loads Content -->
<div id="loads-content" class="loadboard-content">
    <div class="flex justify-between items-center mb-3 md:mb-4">
        <h2 class="text-base md:text-lg font-bold text-gray-900">Available Loads</h2>
        <a href="{{ route('loadboard.loads.index') }}"
            class="text-[#ff0808] hover:text-red-700 font-semibold text-xs md:text-sm flex items-center gap-1">
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
                        <h4 class="text-xs md:text-sm font-bold text-gray-900 truncate">{{ $originCity }} → {{ $destinationCity }}</h4>
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
                <p class="text-gray-500 text-sm">No available loads at the moment.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Available Cars Content -->
<div id="cars-content" class="loadboard-content hidden">
    <div class="flex justify-between items-center mb-3 md:mb-4">
        <h2 class="text-base md:text-lg font-bold text-gray-900">Available Vehicles for Hire</h2>
        <a href="{{ route('loadboard.cars.index') }}"
            class="text-[#ff0808] hover:text-red-700 font-semibold text-xs md:text-sm flex items-center gap-1">
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
                        <h4 class="text-xs md:text-sm font-bold text-gray-900 truncate">{{ $fullName }}</h4>
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
                <p class="text-gray-500 text-sm">No available vehicles at the moment.</p>
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
                class="tradeshows-tab px-3 py-1.5 md:px-4 md:py-2 font-bold text-xs md:text-sm border-b-4 border-[#ff0808] text-[#ff0808] transition-all">
                <i class="fas fa-calendar-alt mr-1 md:mr-2"></i>Trade Shows
            </button>
            <button id="showroom-tab"
                class="tradeshows-tab px-3 py-1.5 md:px-4 md:py-2 font-bold text-xs md:text-sm border-b-4 border-transparent text-gray-500 hover:text-[#ff0808] transition-all">
                <i class="fas fa-store mr-1 md:mr-2"></i>Show Room
            </button>
        </div>

<!-- Trade Shows Content -->
<div id="tradeshows-content" class="tradeshows-content">
    <div class="flex justify-between items-center mb-2 md:mb-3">
        <h2 class="text-sm md:text-base font-bold text-gray-900">Upcoming Trade Shows</h2>
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
                <div class="flex justify-center items-center h-20 md:h-24 text-white bg-gradient-to-br from-{{ $color }}-400 to-{{ $color }}-600">
                    <div class="text-center">
                        <div class="text-lg md:text-2xl font-black">{{ strtoupper($monthShort) }}</div>
                        <div class="text-2xl md:text-4xl font-black">{{ $day }}</div>
                    </div>
                </div>
                <div class="p-2 md:p-3">
                    <h4 class="mb-1 text-xs md:text-sm font-bold text-gray-900 line-clamp-2">{{ $tradeshow->name }}</h4>
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
                <p class="text-gray-500 text-sm">No upcoming tradeshows at the moment.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Show Room Content -->
<div id="showroom-content" class="tradeshows-content hidden">
    <div class="flex justify-between items-center mb-2 md:mb-3">
        <h2 class="text-sm md:text-base font-bold text-gray-900">Featured Showrooms</h2>
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
                <h4 class="text-xs md:text-sm font-bold text-gray-900 truncate">{{ $showroom->name }}</h4>
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
                <p class="text-gray-500 text-sm">No featured showrooms available.</p>
            </div>
        @endforelse
    </div>
</div>
    </div>
</div>

    @endif
        </div>
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



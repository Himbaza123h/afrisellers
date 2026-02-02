<aside id="sidebar"
    class="overflow-y-auto fixed top-0 left-0 z-40 flex-shrink-0 w-72 h-full bg-white border-r border-gray-200 shadow-xl transition-transform duration-300 -translate-x-full lg:static lg:translate-x-0 lg:shadow-none">
    <!-- Sidebar Header -->
    {{-- <div class="flex justify-between items-center p-5 border-b border-gray-200">
        <button id="close-sidebar" class="text-gray-400 hover:text-gray-600 lg:hidden">
            <i class="text-xl fas fa-times"></i>
        </button>
    </div> --}}

    <div class="p-4">
        <!-- Dashboard -->
        <div class="mb-6">
            @php
                $user = auth()->user();
                $dashboardRoute = route('buyer.dashboard.home'); // Default

                // Check roles in priority order
                if ($user->roles()->where('roles.slug', 'admin')->exists()) {
                    $dashboardRoute = route('admin.dashboard.home');
                } elseif ($user->roles()->where('roles.slug', 'regional_admin')->exists()) {
                    $dashboardRoute = route('regional.dashboard.home');
                } elseif ($user->roles()->where('roles.slug', 'country_admin')->exists()) {
                    $dashboardRoute = route('country.dashboard.home');
                } elseif ($user->roles()->where('roles.slug', 'agent')->exists()) {
                    $dashboardRoute = route('agent.dashboard.home');
                } elseif ($user->isVendor()) {
                    $dashboardRoute = route('vendor.dashboard.home');
                }
            @endphp

            <a href="{{ $dashboardRoute }}"
                class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('*.dashboard.home') ? 'text-white bg-[#ff0808] to-[#cc0606]' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg font-semibold transition-all group">
                <i class="w-5 text-center transition-transform fas fa-th-large {{ request()->routeIs('*.dashboard.home') ? '' : 'text-gray-400' }} group-hover:scale-110"></i>
                <span class="text-sm">Dashboard</span>
                @if(request()->routeIs('*.dashboard.home'))
                    <span class="ml-auto w-1.5 h-1.5 bg-white rounded-full"></span>
                @endif
            </a>
        </div>

        <!-- User Management (Admin Only) -->
        @if (auth()->user()->hasRole('admin'))
            <div class="mb-6">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">User Management</p>
                <nav class="space-y-1">
                    <a href="{{ route('admin.regional-admins.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.regional-admins.*') ? 'text-[#ff0808] bg-red-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.regional-admins.*') ? 'text-[#ff0808]' : 'text-gray-400' }} transition-transform fas fa-user-shield group-hover:scale-110"></i>
                        <span class="text-sm">Regional Admins</span>
                    </a>

                    <a href="{{ route('admin.country.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.countries.*') || request()->routeIs('admin.country.*') ? 'text-blue-600 bg-blue-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.countries.*') || request()->routeIs('admin.country.*') ? 'text-blue-600' : 'text-gray-400' }} transition-transform fas fa-globe group-hover:scale-110"></i>
                        <span class="text-sm">Countries</span>
                    </a>

                    @php
                        $pendingBusinessProfilescount = App\Models\BusinessProfile::where('is_admin_verified', false)->count();
                    @endphp

                    <a href="{{ route('admin.business-profile.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.business-profile.*') ? 'text-purple-600 bg-purple-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.business-profile.*') ? 'text-purple-600' : 'text-gray-400' }} transition-transform fas fa-store group-hover:scale-110"></i>
                        <span class="text-sm">Vendors</span>
                        @if($pendingBusinessProfilescount > 0)
                            <span class="ml-auto px-2 py-0.5 bg-orange-500 text-white text-[10px] font-bold rounded-full">{{ $pendingBusinessProfilescount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.buyer.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.buyer.*') ? 'text-green-600 bg-green-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.buyer.*') ? 'text-green-600' : 'text-gray-400' }} transition-transform fas fa-users group-hover:scale-110"></i>
                        <span class="text-sm">Buyers</span>
                    </a>

                    <a href="{{ route('admin.agents.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.agents.*') ? 'text-orange-600 bg-orange-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.agents.*') ? 'text-orange-600' : 'text-gray-400' }} transition-transform fas fa-handshake group-hover:scale-110"></i>
                        <span class="text-sm">Agents</span>
                    </a>

                    <a href="{{ route('admin.transporters.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.transporters.*') ? 'text-indigo-600 bg-indigo-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.transporters.*') ? 'text-indigo-600' : 'text-gray-400' }} transition-transform fas fa-truck group-hover:scale-110"></i>
                        <span class="text-sm">Transporters</span>
                    </a>
                </nav>
            </div>
        @endif


        @if (auth()->user()->hasRole('agent'))


            @endif

        <!-- Regional Admin Sections -->
@if (auth()->user()->hasRole('regional_admin'))
    <!-- Vendors Management -->
    <div class="mb-6">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">Vendors</p>
        <nav class="space-y-1">
            <a href="{{ route('regional.vendors.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('regional.vendors.*') ? 'text-purple-600 bg-purple-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('regional.vendors.*') ? 'text-purple-600' : 'text-gray-400' }} transition-transform fas fa-store group-hover:scale-110"></i>
                <span class="text-sm">Vendors</span>
            </a>

            <a href="{{ route('regional.products.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('regional.products.*') ? 'text-indigo-600 bg-indigo-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('regional.products.*') ? 'text-indigo-600' : 'text-gray-400' }} transition-transform fas fa-boxes group-hover:scale-110"></i>
                <span class="text-sm">Products</span>
            </a>

            <a href="{{ route('regional.showrooms.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('regional.showrooms.*') ? 'text-purple-600 bg-purple-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('regional.showrooms.*') ? 'text-purple-600' : 'text-gray-400' }} transition-transform fas fa-building group-hover:scale-110"></i>
                <span class="text-sm">Showrooms</span>
            </a>

            <a href="{{ route('regional.orders.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('regional.orders.*') ? 'text-blue-600 bg-blue-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('regional.orders.*') ? 'text-blue-600' : 'text-gray-400' }} transition-transform fas fa-shopping-bag group-hover:scale-110"></i>
                <span class="text-sm">Orders</span>
            </a>

            <a href="{{ route('regional.loads.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('regional.loads.*') ? 'text-orange-600 bg-orange-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('regional.loads.*') ? 'text-orange-600' : 'text-gray-400' }} transition-transform fas fa-truck-loading group-hover:scale-110"></i>
                <span class="text-sm">Loads</span>
            </a>

            <a href="{{ route('regional.transporters.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('regional.transporters.*') ? 'text-indigo-600 bg-indigo-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('regional.transporters.*') ? 'text-indigo-600' : 'text-gray-400' }} transition-transform fas fa-truck group-hover:scale-110"></i>
                <span class="text-sm">Transporters</span>
            </a>
        </nav>
    </div>

    <!-- Country Admins -->
    <div class="mb-6">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">Administration</p>
        <nav class="space-y-1">
            <a href="{{ route('regional.country-admins.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('regional.country-admins.*') ? 'text-blue-600 bg-blue-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('regional.country-admins.*') ? 'text-blue-600' : 'text-gray-400' }} transition-transform fas fa-user-tie group-hover:scale-110"></i>
                <span class="text-sm">Country Admins</span>
            </a>
        </nav>
    </div>

    <!-- Reports -->
    <div class="mb-6">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">Analytics</p>
        <nav class="space-y-1">
            <a href="{{ route('regional.reports.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('regional.reports.*') ? 'text-rose-600 bg-rose-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('regional.reports.*') ? 'text-rose-600' : 'text-gray-400' }} transition-transform fas fa-chart-line group-hover:scale-110"></i>
                <span class="text-sm">Reports</span>
            </a>
        </nav>
    </div>
@endif

<!-- Country Admin Sections -->
@if (auth()->user()->hasRole('country_admin'))
    <!-- Vendors Management -->
    <div class="mb-6">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">Vendors</p>
        <nav class="space-y-1">
            <a href="{{ route('country.vendors.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('country.vendors.*') ? 'text-purple-600 bg-purple-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('country.vendors.*') ? 'text-purple-600' : 'text-gray-400' }} transition-transform fas fa-store group-hover:scale-110"></i>
                <span class="text-sm">Vendors</span>
            </a>

            <a href="{{ route('country.products.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('country.products.*') ? 'text-indigo-600 bg-indigo-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('country.products.*') ? 'text-indigo-600' : 'text-gray-400' }} transition-transform fas fa-boxes group-hover:scale-110"></i>
                <span class="text-sm">Products</span>
            </a>

            <a href="{{ route('country.showrooms.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('country.showrooms.*') ? 'text-purple-600 bg-purple-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('country.showrooms.*') ? 'text-purple-600' : 'text-gray-400' }} transition-transform fas fa-building group-hover:scale-110"></i>
                <span class="text-sm">Showrooms</span>
            </a>

            <a href="{{ route('country.orders.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('country.orders.*') ? 'text-blue-600 bg-blue-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('country.orders.*') ? 'text-blue-600' : 'text-gray-400' }} transition-transform fas fa-shopping-bag group-hover:scale-110"></i>
                <span class="text-sm">Orders</span>
            </a>

            <a href="{{ route('country.loads.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('country.loads.*') ? 'text-orange-600 bg-orange-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('country.loads.*') ? 'text-orange-600' : 'text-gray-400' }} transition-transform fas fa-truck-loading group-hover:scale-110"></i>
                <span class="text-sm">Loads</span>
            </a>

            <a href="{{ route('country.transporters.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('country.transporters.*') ? 'text-indigo-600 bg-indigo-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('country.transporters.*') ? 'text-indigo-600' : 'text-gray-400' }} transition-transform fas fa-truck group-hover:scale-110"></i>
                <span class="text-sm">Transporters</span>
            </a>
        </nav>
    </div>

    <!-- Reports -->
    <div class="mb-6">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">Analytics</p>
        <nav class="space-y-1">
            <a href="{{ route('country.reports.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('country.reports.*') ? 'text-rose-600 bg-rose-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('country.reports.*') ? 'text-rose-600' : 'text-gray-400' }} transition-transform fas fa-chart-line group-hover:scale-110"></i>
                <span class="text-sm">Reports</span>
            </a>
        </nav>
    </div>
@endif

<!-- Agent Sections -->
@if (auth()->user()->hasRole('agent'))
    <!-- Referrals -->
    <div class="mb-6">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">Referrals</p>
        <nav class="space-y-1">
            <a href="{{ route('agent.referrals.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('agent.referrals.*') ? 'text-blue-600 bg-blue-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('agent.referrals.*') ? 'text-blue-600' : 'text-gray-400' }} transition-transform fas fa-users group-hover:scale-110"></i>
                <span class="text-sm">My Referrals</span>
            </a>
        </nav>
    </div>

    <!-- Commissions -->
    <div class="mb-6">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">Earnings</p>
        <nav class="space-y-1">
            <a href="{{ route('agent.commissions.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('agent.commissions.*') ? 'text-green-600 bg-green-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('agent.commissions.*') ? 'text-green-600' : 'text-gray-400' }} transition-transform fas fa-dollar-sign group-hover:scale-110"></i>
                <span class="text-sm">Commissions</span>
            </a>
        </nav>
    </div>
@endif

        <!-- Marketplace -->
        @if (auth()->user()->hasRole('admin'))
            <div class="mb-6">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">Marketplace</p>
                <nav class="space-y-1">
                    <a href="{{ route('admin.product.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.product.*') ? 'text-indigo-600 bg-indigo-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.product.*') ? 'text-indigo-600' : 'text-gray-400' }} transition-transform fas fa-boxes group-hover:scale-110"></i>
                        <span class="text-sm">Products</span>
                    </a>

                    <a href="{{ route('admin.product-category.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.product-category.*') ? 'text-teal-600 bg-teal-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.product-category.*') ? 'text-teal-600' : 'text-gray-400' }} transition-transform fas fa-list-alt group-hover:scale-110"></i>
                        <span class="text-sm">Categories</span>
                    </a>

                    <a href="{{ route('admin.orders.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.orders.*') ? 'text-blue-600 bg-blue-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.orders.*') ? 'text-blue-600' : 'text-gray-400' }} transition-transform fas fa-shopping-bag group-hover:scale-110"></i>
                        <span class="text-sm">Orders</span>
                    </a>

                    <a href="{{ route('admin.rfq.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.rfq.*') ? 'text-pink-600 bg-pink-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.rfq.*') ? 'text-pink-600' : 'text-gray-400' }} transition-transform fas fa-file-invoice group-hover:scale-110"></i>
                        <span class="text-sm">RFQs</span>
                    </a>
                </nav>
            </div>

            <!-- Services -->
            <div class="mb-6">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">Services</p>
                <nav class="space-y-1">
                    <a href="{{ route('admin.showrooms.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.showrooms.*') ? 'text-purple-600 bg-purple-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.showrooms.*') ? 'text-purple-600' : 'text-gray-400' }} transition-transform fas fa-building group-hover:scale-110"></i>
                        <span class="text-sm">Showrooms</span>
                    </a>

                    <a href="{{ route('admin.tradeshows.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.tradeshows.*') ? 'text-cyan-600 bg-cyan-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.tradeshows.*') ? 'text-cyan-600' : 'text-gray-400' }} transition-transform fas fa-calendar-alt group-hover:scale-110"></i>
                        <span class="text-sm">Tradeshows</span>
                    </a>

                    <a href="{{ route('admin.loads.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.loads.*') ? 'text-orange-600 bg-orange-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.loads.*') ? 'text-orange-600' : 'text-gray-400' }} transition-transform fas fa-truck-loading group-hover:scale-110"></i>
                        <span class="text-sm">Loads</span>
                    </a>
                </nav>
            </div>
        @endif

        @if (auth()->user()->isVendor())
            <div class="mb-6">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">My Business</p>
                <nav class="space-y-1">
                    <a href="{{ route('vendor.product.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('vendor.product.*') ? 'text-indigo-600 bg-indigo-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('vendor.product.*') ? 'text-indigo-600' : 'text-gray-400' }} transition-transform fas fa-boxes group-hover:scale-110"></i>
                        <span class="text-sm">My Products</span>
                    </a>

                    <a href="{{ route('vendor.promo-code.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('vendor.promo-code.*') ? 'text-indigo-600 bg-indigo-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('vendor.promo-code.*') ? 'text-indigo-600' : 'text-gray-400' }} transition-transform fas fa-ticket-alt group-hover:scale-110"></i>
                        <span class="text-sm">Promo Codes</span>
                    </a>

                    <a href="{{ route('vendor.orders.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('vendor.orders.*') ? 'text-blue-600 bg-blue-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('vendor.orders.*') ? 'text-blue-600' : 'text-gray-400' }} transition-transform fas fa-shopping-bag group-hover:scale-110"></i>
                        <span class="text-sm">My Orders</span>
                    </a>

                    @php
                        $vendor = App\Models\Vendor\Vendor::where('user_id', auth()->id())->first();
                        $canViewRFQs = $vendor && $vendor->plan_id && $vendor->plan;
                    @endphp
                    @if($canViewRFQs)
                        <a href="{{ route('vendor.rfq.index') }}"
                            class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('vendor.rfq.*') ? 'text-pink-600 bg-pink-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                            <i class="w-5 text-center {{ request()->routeIs('vendor.rfq.*') ? 'text-pink-600' : 'text-gray-400' }} transition-transform fas fa-file-invoice group-hover:scale-110"></i>
                            <span class="text-sm">RFQs</span>
                        </a>
                    @endif
                </nav>
            </div>
        @endif

<!-- Finance -->
@if (auth()->user()->hasRole('admin') || auth()->user()->isVendor())
    <div class="mb-6">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">Finance</p>
        <nav class="space-y-1">
            <a href="{{ auth()->user()->hasRole('admin') ? route('admin.transactions.index') : route('vendor.transactions.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('*.transactions.*') ? 'text-emerald-600 bg-emerald-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('*.transactions.*') ? 'text-emerald-600' : 'text-gray-400' }} transition-transform fas fa-wallet group-hover:scale-110"></i>
                <span class="text-sm">
                    @if (auth()->user()->isVendor() && !auth()->user()->hasRole('admin'))
                        My Transactions
                    @else
                        Transactions
                    @endif
                </span>
            </a>

            @if (auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.escrow.index') }}"
                    class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.escrow.*') ? 'text-cyan-600 bg-cyan-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                    <i class="w-5 text-center {{ request()->routeIs('admin.escrow.*') ? 'text-cyan-600' : 'text-gray-400' }} transition-transform fas fa-shield-alt group-hover:scale-110"></i>
                    <span class="text-sm">Escrow</span>
                </a>

                <a href="{{ route('admin.commissions.index') }}"
                    class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.commissions.*') ? 'text-violet-600 bg-violet-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                    <i class="w-5 text-center {{ request()->routeIs('admin.commissions.*') ? 'text-violet-600' : 'text-gray-400' }} transition-transform fas fa-percentage group-hover:scale-110"></i>
                    <span class="text-sm">Commissions</span>
                </a>

            @if (auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.memberships.plans.index') }}"
                    class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.memberships.*') ? 'text-amber-600 bg-amber-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                    <i class="w-5 text-center {{ request()->routeIs('admin.memberships.*') ? 'text-amber-600' : 'text-gray-400' }} transition-transform fas fa-crown group-hover:scale-110"></i>
                    <span class="text-sm">Memberships</span>
                </a>
                <a href="{{ route('admin.addons.index') }}"
                    class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.addons.*') ? 'text-fuchsia-600 bg-fuchsia-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                    <i class="w-5 text-center {{ request()->routeIs('admin.addons.*') ? 'text-fuchsia-600' : 'text-gray-400' }} transition-transform fas fa-puzzle-piece group-hover:scale-110"></i>
                    <span class="text-sm">Addons</span>
                </a>
            @endif
            @else
                <a href="{{ route('vendor.earnings.index') }}"
                    class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('vendor.earnings.*') ? 'text-violet-600 bg-violet-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                    <i class="w-5 text-center {{ request()->routeIs('vendor.earnings.*') ? 'text-violet-600' : 'text-gray-400' }} transition-transform fas fa-dollar-sign group-hover:scale-110"></i>
                    <span class="text-sm">My Earnings</span>
                </a>

                @if (auth()->user()->isVendor())
                    <a href="{{ route('vendor.subscriptions.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('vendor.subscriptions.*') ? 'text-purple-600 bg-purple-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('vendor.subscriptions.*') ? 'text-purple-600' : 'text-gray-400' }} transition-transform fas fa-crown group-hover:scale-110"></i>
                        <span class="text-sm">My Subscription</span>
                    </a>
                    <a href="{{ route('vendor.addons.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('vendor.addons.*') ? 'text-pink-600 bg-pink-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('vendor.addons.*') ? 'text-pink-600' : 'text-gray-400' }} transition-transform fas fa-puzzle-piece group-hover:scale-110"></i>
                        <span class="text-sm">My Addons</span>
                    </a>
                @endif
            @endif
        </nav>
    </div>
@endif

<!-- Analytics -->
        @if (auth()->user()->hasRole('admin') || auth()->user()->isVendor())
            <div class="mb-6">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">Analytics</p>
                <nav class="space-y-1">


                    <a href="{{ auth()->user()->hasRole('admin') ? route('admin.reports.index') : route('vendor.reports.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('*.reports.*') ? 'text-rose-600 bg-rose-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('*.reports.*') ? 'text-rose-600' : 'text-gray-400' }} transition-transform fas fa-chart-line group-hover:scale-110"></i>
                        <span class="text-sm">
                            @if (auth()->user()->isVendor() && !auth()->user()->hasRole('admin'))
                                Sales Reports
                            @else
                                Reports
                            @endif
                        </span>
                    </a>

                    <a href="{{ auth()->user()->hasRole('admin') ? route('admin.analytics.index') : route('vendor.performance.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ (auth()->user()->hasRole('admin') && request()->routeIs('*.analytics.*')) || (!auth()->user()->hasRole('admin') && request()->routeIs('*.performance.*')) ? 'text-sky-600 bg-sky-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ (auth()->user()->hasRole('admin') && request()->routeIs('*.analytics.*')) || (!auth()->user()->hasRole('admin') && request()->routeIs('*.performance.*')) ? 'text-sky-600' : 'text-gray-400' }} transition-transform fas fa-chart-bar group-hover:scale-110"></i>
                        <span class="text-sm">
                            @if (auth()->user()->isVendor() && !auth()->user()->hasRole('admin'))
                                Performance
                            @else
                                Analytics
                            @endif
                        </span>
                    </a>
                </nav>
            </div>
        @endif

        <!-- System (Admin Only) -->
        @if (auth()->user()->hasRole('admin'))
            <div class="pt-4 border-t border-gray-200 mb-6">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">System</p>
                <nav class="space-y-1">
                    <a href="{{ route('admin.settings.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.settings.*') ? 'text-slate-600 bg-slate-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.settings.*') ? 'text-slate-600' : 'text-gray-400' }} transition-transform fas fa-cog group-hover:scale-110"></i>
                        <span class="text-sm">Settings</span>
                    </a>

                    <a href="{{ route('admin.security.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.security.*') ? 'text-lime-600 bg-lime-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.security.*') ? 'text-lime-600' : 'text-gray-400' }} transition-transform fas fa-lock group-hover:scale-110"></i>
                        <span class="text-sm">Security</span>
                    </a>

                    <a href="{{ route('admin.audit-logs.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.audit-logs.*') ? 'text-fuchsia-600 bg-fuchsia-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.audit-logs.*') ? 'text-fuchsia-600' : 'text-gray-400' }} transition-transform fas fa-history group-hover:scale-110"></i>
                        <span class="text-sm">Audit Logs</span>
                    </a>
                </nav>
            </div>
        @endif

        <!-- Account Settings -->
        <div class="pt-4 border-t border-gray-200">
            @if (!auth()->user()->hasRole('admin'))
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">Account</p>
                <nav class="space-y-1">
                    <a href="{{ route('vendor.profile.show') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('*.profile.*') ? 'text-slate-600 bg-slate-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('*.profile.*') ? 'text-slate-600' : 'text-gray-400' }} transition-transform fas fa-user-circle group-hover:scale-110"></i>
                        <span class="text-sm">Profile Settings</span>
                    </a>
                </nav>
            @endif

            @if (auth()->user()->isVendor())
                <nav class="space-y-1 mb-4">
                    <a href="{{ route('vendor.store.settings') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('vendor.store.*') ? 'text-slate-600 bg-slate-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('vendor.store.*') ? 'text-slate-600' : 'text-gray-400' }} transition-transform fas fa-store group-hover:scale-110"></i>
                        <span class="text-sm">Store Settings</span>
                    </a>
                    <a href="{{ route('vendor.showrooms.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('vendor.showrooms.*') ? 'text-slate-600 bg-slate-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('vendor.showrooms.*') ? 'text-slate-600' : 'text-gray-400' }} transition-transform fas fa-building group-hover:scale-110"></i>
                        <span class="text-sm">My Showrooms</span>
                    </a>
                </nav>
            @endif

            <form action="{{ route('auth.logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="flex gap-3 items-center px-4 py-2.5 w-full text-gray-700 rounded-lg transition-all hover:bg-red-50 group">
                    <i class="w-5 text-center text-gray-400 transition-transform fas fa-sign-out-alt group-hover:scale-110 group-hover:text-red-600"></i>
                    <span class="text-sm font-medium">Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>

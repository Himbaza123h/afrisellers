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
                } elseif ($user->is_partner) {                          // ← ADD THIS
                    $dashboardRoute = route('partner.dashboard');
                } elseif ($user->isVendor()) {
                    $dashboardRoute = route('vendor.dashboard.home');
                }
            @endphp

            @php
                $isDashboardActive = request()->routeIs('*.dashboard.home') || request()->routeIs('partner.dashboard');
            @endphp
            <a href="{{ $dashboardRoute }}"
                class="flex items-center gap-3 px-4 py-3 {{ $isDashboardActive ? 'text-white bg-[#ff0808] to-[#cc0606]' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg font-semibold transition-all group">
                <i class="w-5 text-center transition-transform fas fa-th-large {{ $isDashboardActive ? '' : 'text-gray-400' }} group-hover:scale-110"></i>
                <span class="text-sm">Dashboard</span>
                @if($isDashboardActive)
                    <span class="ml-auto w-1.5 h-1.5 bg-white rounded-full"></span>
                @endif
            </a>
        </div>

            <!-- User Management (Admin Only) -->
            @if (auth()->user()->hasRole('admin'))
            @php
                $adminPerms = auth()->user()->manageablePermission;
                $isSuperAdmin = auth()->id() === 1;
                $canManageUsers      = $isSuperAdmin || ($adminPerms?->can_manage_users ?? false);
                $canManageSettings   = $isSuperAdmin || ($adminPerms?->can_manage_settings ?? false);
                $canViewAuditLogs    = $isSuperAdmin || ($adminPerms?->can_view_audit_logs ?? false);
                $canManageMemberships= $isSuperAdmin || ($adminPerms?->can_manage_memberships ?? false);
            @endphp
            <div class="mb-6">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">User Management</p>
                <nav class="space-y-1">
@if($canManageUsers)
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

                    <a href="{{ route('admin.partners.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.partners.*') ? 'text-teal-600 bg-teal-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.partners.*') ? 'text-teal-600' : 'text-gray-400' }} transition-transform fas fa-handshake group-hover:scale-110"></i>
                        <span class="text-sm">Partners</span>
                    </a>

                    <a href="{{ route('admin.partner-requests.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.partner-requests.*') ? 'text-amber-600 bg-amber-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.partner-requests.*') ? 'text-amber-600' : 'text-gray-400' }} transition-transform fas fa-user-plus group-hover:scale-110"></i>
                        <span class="text-sm">Partner Requests</span>
                        @php $pendingReqs = \App\Models\PartnerRequest::pending()->count(); @endphp
                        @if($pendingReqs > 0)
                            <span class="ml-auto px-2 py-0.5 bg-amber-500 text-white text-[10px] font-bold rounded-full">{{ $pendingReqs }}</span>
                        @endif
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

                    {{-- Agent requests --}}
                    <a href="{{ route('admin.agent-requests.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.agent-requests.*') ? 'text-red-600 bg-red-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.agent-requests.*') ? 'text-red-600' : 'text-gray-400' }} transition-transform fas fa-user-plus group-hover:scale-110"></i>
                        <span class="text-sm">Agent Requests</span>
                        @php $pendingAgentReqs = \App\Models\AgentRequest::where('status', 'pending')->count(); @endphp
                        @if($pendingAgentReqs > 0)
                            <span class="ml-auto px-2 py-0.5 bg-red-500 text-white text-[10px] font-bold rounded-full">{{ $pendingAgentReqs }}</span>
                        @endif
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

                    @if($canManageUsers)
                    <a href="{{ route('admin.departments.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.departments.*') ? 'text-violet-600 bg-violet-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.departments.*') ? 'text-violet-600' : 'text-gray-400' }} transition-transform fas fa-building group-hover:scale-110"></i>
                        <span class="text-sm">Departments</span>
                        @php $deptCount = \App\Models\Department::where('is_active', true)->count(); @endphp
                        @if($deptCount > 0)
                            <span class="ml-auto px-2 py-0.5 bg-violet-500 text-white text-[10px] font-bold rounded-full">{{ $deptCount }}</span>
                        @endif
                    </a>
                    @endif

                    <a href="{{ route('admin.manageusers.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.manageusers.*') ? 'text-rose-600 bg-rose-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.manageusers.*') ? 'text-rose-600' : 'text-gray-400' }} transition-transform fas fa-user-cog group-hover:scale-110"></i>
                        <span class="text-sm">Admin Users</span>
                    </a>
                    @endif

                    <a href="{{ route('admin.ui-sections.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.ui-sections.*') ? 'text-pink-600 bg-pink-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.ui-sections.*') ? 'text-pink-600' : 'text-gray-400' }} transition-transform fas fa-layer-group group-hover:scale-110"></i>
                        <span class="text-sm">UI Sections</span>
                    </a>

                </nav>
            </div>
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

            <a href="{{ route('regional.agents.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('regional.agents.*') ? 'text-orange-600 bg-orange-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('regional.agents.*') ? 'text-orange-600' : 'text-gray-400' }} transition-transform fas fa-handshake group-hover:scale-110"></i>
                <span class="text-sm">Agents</span>
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

            <a href="{{ route('country.agents.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('country.agents.*') ? 'text-orange-600 bg-orange-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('country.agents.*') ? 'text-orange-600' : 'text-gray-400' }} transition-transform fas fa-handshake group-hover:scale-110"></i>
                <span class="text-sm">Agents</span>
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

{{-- ─── Partner Sections ─────────────────────────────────────── --}}
@if (auth()->user()->is_partner)

    {{-- Profile Completion --}}
    <div class="mb-6">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">My Profile</p>
        <nav class="space-y-1">
            <a href="{{ route('partner.profile.show') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('partner.profile.*') ? 'text-teal-600 bg-teal-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('partner.profile.*') ? 'text-teal-600' : 'text-gray-400' }} transition-transform fas fa-id-card group-hover:scale-110"></i>
                <span class="text-sm">Profile Overview</span>
            </a>
            <a href="{{ route('partner.company.show') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('partner.company.*') ? 'text-teal-600 bg-teal-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('partner.company.*') ? 'text-teal-600' : 'text-gray-400' }} transition-transform fas fa-building group-hover:scale-110"></i>
                <span class="text-sm">Company Info</span>
            </a>
            <a href="{{ route('partner.branding.show') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('partner.branding.*') ? 'text-pink-600 bg-pink-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('partner.branding.*') ? 'text-pink-600' : 'text-gray-400' }} transition-transform fas fa-palette group-hover:scale-110"></i>
                <span class="text-sm">Branding & Content</span>
            </a>
            <a href="{{ route('partner.contact.show') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('partner.contact.*') ? 'text-blue-600 bg-blue-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('partner.contact.*') ? 'text-blue-600' : 'text-gray-400' }} transition-transform fas fa-address-book group-hover:scale-110"></i>
                <span class="text-sm">Contact Details</span>
            </a>
            <a href="{{ route('partner.social.show') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('partner.social.*') ? 'text-sky-600 bg-sky-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('partner.social.*') ? 'text-sky-600' : 'text-gray-400' }} transition-transform fas fa-share-alt group-hover:scale-110"></i>
                <span class="text-sm">Social Media</span>
            </a>
            <a href="{{ route('partner.business.show') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('partner.business.*') ? 'text-amber-600 bg-amber-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('partner.business.*') ? 'text-amber-600' : 'text-gray-400' }} transition-transform fas fa-briefcase group-hover:scale-110"></i>
                <span class="text-sm">Business Type</span>
            </a>
            <a href="{{ route('partner.operations.show') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('partner.operations.*') ? 'text-indigo-600 bg-indigo-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('partner.operations.*') ? 'text-indigo-600' : 'text-gray-400' }} transition-transform fas fa-globe-africa group-hover:scale-110"></i>
                <span class="text-sm">Operations</span>
            </a>
        </nav>
    </div>

    {{-- Communication --}}
    <div class="mb-6">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">Communication</p>
        <nav class="space-y-1">
            <a href="{{ route('partner.messages.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('partner.messages.*') ? 'text-blue-600 bg-blue-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('partner.messages.*') ? 'text-blue-600' : 'text-gray-400' }} transition-transform fas fa-comments group-hover:scale-110"></i>
                <span class="text-sm">Messages</span>
                @if(auth()->user()->unreadMessagesCount() > 0)
                    <span class="ml-auto px-2 py-0.5 bg-red-500 text-white text-[10px] font-bold rounded-full">
                        {{ auth()->user()->unreadMessagesCount() }}
                    </span>
                @endif
            </a>
            <a href="{{ route('partner.support.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('partner.support.*') ? 'text-violet-600 bg-violet-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('partner.support.*') ? 'text-violet-600' : 'text-gray-400' }} transition-transform fas fa-headset group-hover:scale-110"></i>
                <span class="text-sm">Support</span>
            </a>
        </nav>
    </div>

    {{-- Documents --}}
    <div class="mb-6">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">Documents</p>
        <nav class="space-y-1">
            <a href="{{ route('partner.documents.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('partner.documents.*') ? 'text-slate-600 bg-slate-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('partner.documents.*') ? 'text-slate-600' : 'text-gray-400' }} transition-transform fas fa-folder-open group-hover:scale-110"></i>
                <span class="text-sm">My Documents</span>
            </a>
        </nav>
    </div>

    {{-- Account --}}
    <div class="mb-6">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">Account</p>
        <nav class="space-y-1">
            <a href="{{ route('partner.settings.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('partner.settings.*') ? 'text-slate-600 bg-slate-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('partner.settings.*') ? 'text-slate-600' : 'text-gray-400' }} transition-transform fas fa-cog group-hover:scale-110"></i>
                <span class="text-sm">Settings</span>
            </a>
            <a href="{{ route('partner.notifications.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('partner.notifications.*') ? 'text-slate-600 bg-slate-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('partner.notifications.*') ? 'text-slate-600' : 'text-gray-400' }} transition-transform fas fa-bell group-hover:scale-110"></i>
                <span class="text-sm">Notifications</span>
            </a>
        </nav>
    </div>

@endif
{{-- ─── End Partner Sections ────────────────────────────────────── --}}

<!-- Agent Sections -->
@if (auth()->user()->hasRole('agent'))

    <!-- My Vendors -->
    <div class="mb-6">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">My Vendors</p>
        <nav class="space-y-1">
            <a href="{{ route('agent.vendors.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('agent.vendors.*') ? 'text-purple-600 bg-purple-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('agent.vendors.*') ? 'text-purple-600' : 'text-gray-400' }} transition-transform fas fa-store group-hover:scale-110"></i>
                <span class="text-sm">Vendors</span>
            </a>
            <a href="{{ route('agent.referrals.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('agent.referrals.*') ? 'text-blue-600 bg-blue-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('agent.referrals.*') ? 'text-blue-600' : 'text-gray-400' }} transition-transform fas fa-user-plus group-hover:scale-110"></i>
                <span class="text-sm">Referrals</span>
            </a>
        </nav>
    </div>

    <!-- Earnings & Finance -->
    <div class="mb-6">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">Finance</p>
        <nav class="space-y-1">
            <a href="{{ route('agent.commissions.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('agent.commissions.*') ? 'text-green-600 bg-green-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('agent.commissions.*') ? 'text-green-600' : 'text-gray-400' }} transition-transform fas fa-percentage group-hover:scale-110"></i>
                <span class="text-sm">Commissions</span>
            </a>
            <a href="{{ route('agent.earnings.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('agent.earnings.*') ? 'text-emerald-600 bg-emerald-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('agent.earnings.*') ? 'text-emerald-600' : 'text-gray-400' }} transition-transform fas fa-dollar-sign group-hover:scale-110"></i>
                <span class="text-sm">Earnings</span>
            </a>
            <a href="{{ route('agent.transactions.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('agent.transactions.*') ? 'text-teal-600 bg-teal-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('agent.transactions.*') ? 'text-teal-600' : 'text-gray-400' }} transition-transform fas fa-wallet group-hover:scale-110"></i>
                <span class="text-sm">Transactions</span>
            </a>
            <a href="{{ route('agent.payouts.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('agent.payouts.*') ? 'text-cyan-600 bg-cyan-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('agent.payouts.*') ? 'text-cyan-600' : 'text-gray-400' }} transition-transform fas fa-money-bill-wave group-hover:scale-110"></i>
                <span class="text-sm">Payouts</span>
            </a>
        </nav>
    </div>

    <!-- Subscription & Packages -->
    <div class="mb-6">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">Subscription</p>
        <nav class="space-y-1">
            <a href="{{ route('agent.subscriptions.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('agent.subscriptions.*') ? 'text-amber-600 bg-amber-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('agent.subscriptions.*') ? 'text-amber-600' : 'text-gray-400' }} transition-transform fas fa-crown group-hover:scale-110"></i>
                <span class="text-sm">My Subscription</span>
            </a>
            <a href="{{ route('agent.packages.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('agent.packages.*') ? 'text-orange-600 bg-orange-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('agent.packages.*') ? 'text-orange-600' : 'text-gray-400' }} transition-transform fas fa-box-open group-hover:scale-110"></i>
                <span class="text-sm">Packages</span>
            </a>
        </nav>
    </div>

    <!-- Analytics -->
    <div class="mb-6">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">Analytics</p>
        <nav class="space-y-1">
            <a href="{{ route('agent.analytics.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('agent.analytics.*') ? 'text-sky-600 bg-sky-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('agent.analytics.*') ? 'text-sky-600' : 'text-gray-400' }} transition-transform fas fa-chart-bar group-hover:scale-110"></i>
                <span class="text-sm">Analytics</span>
            </a>
            <a href="{{ route('agent.reports.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('agent.reports.*') ? 'text-rose-600 bg-rose-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('agent.reports.*') ? 'text-rose-600' : 'text-gray-400' }} transition-transform fas fa-chart-line group-hover:scale-110"></i>
                <span class="text-sm">Reports</span>
            </a>
            <a href="{{ route('agent.performance.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('agent.performance.*') ? 'text-indigo-600 bg-indigo-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('agent.performance.*') ? 'text-indigo-600' : 'text-gray-400' }} transition-transform fas fa-tachometer-alt group-hover:scale-110"></i>
                <span class="text-sm">Performance</span>
            </a>
        </nav>
    </div>

    <!-- Communication -->
    <div class="mb-6">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">Communication</p>
        <nav class="space-y-1">
            <a href="{{ route('agent.messages.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('agent.messages.*') ? 'text-blue-600 bg-blue-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('agent.messages.*') ? 'text-blue-600' : 'text-gray-400' }} transition-transform fas fa-comments group-hover:scale-110"></i>
                <span class="text-sm">Messages</span>
                @if(auth()->user()->unreadMessagesCount() > 0)
                    <span class="ml-auto px-2 py-0.5 bg-red-500 text-white text-[10px] font-bold rounded-full">
                        {{ auth()->user()->unreadMessagesCount() }}
                    </span>
                @endif
            </a>
            <a href="{{ route('agent.support.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('agent.support.*') ? 'text-violet-600 bg-violet-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('agent.support.*') ? 'text-violet-600' : 'text-gray-400' }} transition-transform fas fa-headset group-hover:scale-110"></i>
                <span class="text-sm">Support</span>
            </a>
        </nav>
    </div>

    <!-- Documents -->
    <div class="mb-6">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">Documents</p>
        <nav class="space-y-1">
            <a href="{{ route('agent.documents.index') }}"
                class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('agent.documents.*') ? 'text-slate-600 bg-slate-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                <i class="w-5 text-center {{ request()->routeIs('agent.documents.*') ? 'text-slate-600' : 'text-gray-400' }} transition-transform fas fa-folder-open group-hover:scale-110"></i>
                <span class="text-sm">My Documents</span>
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

                    {{-- @if($canViewPromos ?? false) --}}
                    <a href="{{ route('vendor.promo-code.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('vendor.promo-code.*') ? 'text-indigo-600 bg-indigo-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('vendor.promo-code.*') ? 'text-indigo-600' : 'text-gray-400' }} transition-transform fas fa-ticket-alt group-hover:scale-110"></i>
                        <span class="text-sm">Promo Codes</span>
                    </a>
                    {{-- @endif --}}

                    <a href="{{ route('vendor.orders.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('vendor.orders.*') ? 'text-blue-600 bg-blue-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('vendor.orders.*') ? 'text-blue-600' : 'text-gray-400' }} transition-transform fas fa-shopping-bag group-hover:scale-110"></i>
                        <span class="text-sm">My Orders</span>
                    </a>

                 @php
    $vendor = App\Models\Vendor\Vendor::where('user_id', auth()->id())->first();

$canViewRFQs = false;

if ($vendor) {
    // Check active subscription plan
    $activeSub = \App\Models\Subscription::where('seller_id', auth()->id())
        ->where('status', 'active')
        ->with('plan.features')
        ->first();

    \Illuminate\Support\Facades\Log::info('=== VENDOR PLAN FEATURES DEBUG ===', [
        'user_id'      => auth()->id(),
        'has_sub'      => $activeSub ? true : false,
        'plan_name'    => $activeSub?->plan?->name,
        'plan_id'      => $activeSub?->plan?->id,
        'all_features' => $activeSub?->plan?->features
            ?->map(fn($f) => [
                'key'   => $f->feature_key,
                'value' => $f->feature_value,
            ])
            ->toArray() ?? [],
    ]);

        if ($activeSub && $activeSub->plan) {
            $rfqFeature = $activeSub->plan->features
                ->where('feature_key', 'has_rfq_access')
                ->first();
            $canViewRFQs = $rfqFeature && in_array(strtolower($rfqFeature->feature_value), ['true', '1', 'yes']);
        }

        // Also allow if active trial exists
                // Also allow if active trial exists
                if (!$canViewRFQs) {
                    $trial = \App\Models\VendorTrial::where('user_id', auth()->id())
                        ->where('is_active', true)
                        ->where('ends_at', '>=', now())
                        ->first();

                    if ($trial) {
                        // Trial gives access to basic features including RFQ
                        $canViewRFQs = true;
                    }
                }
}

    // Build all feature flags
    $vendorFeatures = [];
    if (isset($activeSub) && $activeSub && $activeSub->plan) {
        foreach ($activeSub->plan->features as $f) {
            $vendorFeatures[$f->feature_key] = in_array(strtolower($f->feature_value), ['true','1','yes']);
        }
    }

    // Trial unlocks everything
    if (isset($trial) && $trial) {
        $vendorFeatures['has_buyer_messaging']    = true;
        $vendorFeatures['can_has_ads']            = true;
        $vendorFeatures['has_performance_reports']= true;
        $vendorFeatures['has_monthly_reports']    = true;
        $vendorFeatures['has_analytics']          = true;
        $vendorFeatures['has_basic_analytics']    = true;
        $vendorFeatures['has_brand_storytelling'] = true;
        $vendorFeatures['has_company_page']       = true;
    }

    $canViewMessages  = $vendorFeatures['has_buyer_messaging']     ?? false;
    $canViewAds       = $vendorFeatures['can_has_ads']             ?? false;
    $canViewReports   = ($vendorFeatures['has_performance_reports'] ?? false) || ($vendorFeatures['has_monthly_reports'] ?? false);
    $canViewAnalytics = ($vendorFeatures['has_analytics'] ?? false) || ($vendorFeatures['has_basic_analytics'] ?? false);
    $canViewArticles  = $vendorFeatures['has_brand_storytelling']  ?? false;
    $canViewShowrooms = $vendorFeatures['has_company_page']        ?? false;
    // $canViewPromos    = $vendorFeatures['can_has_promos']             ?? false;
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
                @if($canManageMemberships)
                <a href="{{ route('admin.memberships.plans.index') }}"
                    class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.memberships.*') ? 'text-amber-600 bg-amber-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                    <i class="w-5 text-center {{ request()->routeIs('admin.memberships.*') ? 'text-amber-600' : 'text-gray-400' }} transition-transform fas fa-crown group-hover:scale-110"></i>
                    <span class="text-sm">Memberships</span>
                </a>
                <a href="{{ route('admin.service-deliveries.index') }}"
                    class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.service-deliveries.*') ? 'text-green-600 bg-green-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                    <i class="w-5 text-center {{ request()->routeIs('admin.service-deliveries.*') ? 'text-green-600' : 'text-gray-400' }} transition-transform fas fa-tasks group-hover:scale-110"></i>
                    <span class="text-sm">Service Deliveries</span>
                    @php $_pendingDeliveries = \App\Models\ServiceDelivery::where('status','pending')->count(); @endphp
                    @if($_pendingDeliveries > 0)
                        <span class="ml-auto px-2 py-0.5 bg-orange-500 text-white text-[10px] font-bold rounded-full">{{ $_pendingDeliveries }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.addons.index') }}"
                    class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.addons.*') ? 'text-fuchsia-600 bg-fuchsia-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                    <i class="w-5 text-center {{ request()->routeIs('admin.addons.*') ? 'text-fuchsia-600' : 'text-gray-400' }} transition-transform fas fa-puzzle-piece group-hover:scale-110"></i>
                    <span class="text-sm">Addons</span>
                </a>
                @endif
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
@if (auth()->user()->hasRole('admin') || auth()->user()->isVendor())
            @php
                $showReportsSection = auth()->user()->hasRole('admin')
                    || ($canViewReports ?? false)
                    || ($canViewAnalytics ?? false);
            @endphp
            @if($showReportsSection)
            <div class="mb-6">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">Analytics</p>
                <nav class="space-y-1">

                    @if(auth()->user()->hasRole('admin') || ($canViewReports ?? false))
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
                    @endif

                    @if(auth()->user()->hasRole('admin') || ($canViewAnalytics ?? false))
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
                    @endif

                </nav>
            </div>
            @endif
        @endif
    @if (auth()->user()->isVendor())

    <div class="mb-6">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">Content</p>
        <nav class="space-y-1">
            <a href="{{ route('vendor.articles.index') }}"
                    class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('*.articles.*') ? 'text-indigo-600 bg-indigo-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                    <i class="w-5 text-center {{ request()->routeIs('*.articles.*') ? 'text-indigo-600' : 'text-gray-400' }} transition-transform fas fa-newspaper group-hover:scale-110"></i>
                    <span class="text-sm">Articles</span>
                </a>
            </nav>
        </div>
       @endif

        @if($canViewAds ?? false)
        <div class="mb-6">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">Marketing</p>
            <nav class="space-y-1">
                <a href="{{ route('vendor.advertisements.index') }}"
                    class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('vendor.advertisements.*') ? 'text-blue-600 bg-blue-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                    <i class="w-5 text-center {{ request()->routeIs('vendor.advertisements.*') ? 'text-blue-600' : 'text-gray-400' }} transition-transform fas fa-ad group-hover:scale-110"></i>
                    <span class="text-sm">Advertisements</span>
                    @php $activeAdsCount = \App\Models\Advertisement::where('user_id', auth()->id())->where('status','running')->count(); @endphp
                    @if($activeAdsCount > 0)
                        <span class="ml-auto text-xs font-bold bg-blue-600 text-white px-2 py-0.5 rounded-full">{{ $activeAdsCount }}</span>
                    @endif
                </a>
                <a href="{{ route('vendor.ads.index') }}"
                    class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('vendor.ads.*') ? 'text-blue-600 bg-blue-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                    <i class="w-5 text-center {{ request()->routeIs('vendor.ads.*') ? 'text-blue-600' : 'text-gray-400' }} transition-transform fas fa-bullhorn group-hover:scale-110"></i>
                    <span class="text-sm">Ads & Promotions</span>
                    @php
                        $activeAdsCount = auth()->check()
                            ? \App\Models\Ad::where('user_id', auth()->id())->where('status', 'active')->count()
                            : 0;
                    @endphp
                    @if($activeAdsCount > 0)
                        <span class="ml-auto text-xs font-bold bg-blue-600 text-white px-2 py-0.5 rounded-full">
                            {{ $activeAdsCount }}
                        </span>
                    @endif
                </a>
            </nav>
        </div>
        @endif


        <!-- Add this in the appropriate section -->
        <div class="mb-6">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">Communication</p>
            <nav class="space-y-1">
            @if(auth()->user()->hasRole('admin') || ($canViewMessages ?? false))
                <a href="{{ route('messages.index') }}"
                    class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('messages.*') ? 'text-blue-600 bg-blue-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                    <i class="w-5 text-center {{ request()->routeIs('messages.*') ? 'text-blue-600' : 'text-gray-400' }} transition-transform fas fa-comments group-hover:scale-110"></i>
                    <span class="text-sm">Messages</span>
                    @if(auth()->user()->unreadMessagesCount() > 0)
                        <span class="ml-auto px-2 py-0.5 bg-red-500 text-white text-[10px] font-bold rounded-full">
                            {{ auth()->user()->unreadMessagesCount() }}
                        </span>
                    @endif
                </a>
                @endif

                <!-- Add this below messages link -->
                <a href="{{ route('message.join-page') }}" class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('message.join-page*') ? 'text-blue-600 bg-blue-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                    <i class="fas fa-plus-circle"></i> Join Group
                </a>

                @if (auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.messages.broadcast') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.messages.broadcast') ? 'text-purple-600 bg-purple-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.messages.broadcast') ? 'text-purple-600' : 'text-gray-400' }} transition-transform fas fa-bullhorn group-hover:scale-110"></i>
                        <span class="text-sm">Broadcast</span>
                    </a>
                    <a href="{{ route('admin.support.index') }}"
                    class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.support.*') ? 'text-rose-600 bg-rose-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.support.*') ? 'text-rose-600' : 'text-gray-400' }} transition-transform fas fa-headset group-hover:scale-110"></i>
                        <span class="text-sm">Support Tickets</span>
                        @php $_openTickets = \App\Models\SupportTicket::where('status', 'open')->count(); @endphp
                        @if($_openTickets > 0)
                            <span class="ml-auto px-2 py-0.5 bg-rose-500 text-white text-[10px] font-bold rounded-full">{{ $_openTickets }}</span>
                        @endif
                    </a>
                @endif
            </nav>
        </div>

        <!-- System (Admin Only) -->
        @if (auth()->user()->hasRole('admin'))
            <div class="pt-4 border-t border-gray-200 mb-6">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">System</p>
                <nav class="space-y-1">
                    {{-- Configuration route --}}
                    <a href="{{ route('admin.configurations.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.configurations.*') ? 'text-slate-600 bg-slate-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.configurations.*') ? 'text-slate-600' : 'text-gray-400' }} transition-transform fas fa-tools group-hover:scale-110"></i>
                        <span class="text-sm">Configuration</span>
                    </a>

                    <a href="{{ route('admin.fallback-ads.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.fallback-ads.*') ? 'text-orange-600 bg-orange-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.fallback-ads.*') ? 'text-orange-600' : 'text-gray-400' }} transition-transform fas fa-images group-hover:scale-110"></i>
                        <span class="text-sm">Fallback Ads</span>
                    </a>

                    <a href="{{ route('admin.square-ads.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.square-ads.*') ? 'text-yellow-600 bg-yellow-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.square-ads.*') ? 'text-yellow-600' : 'text-gray-400' }} transition-transform fas fa-th-large group-hover:scale-110"></i>
                        <span class="text-sm">Square Ads</span>
                    </a>

                                        <a href="{{ route('admin.ad-library.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.ad-library.*') ? 'text-violet-600 bg-violet-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.ad-library.*') ? 'text-violet-600' : 'text-gray-400' }} transition-transform fas fa-photo-video group-hover:scale-110"></i>
                        <span class="text-sm">Ad Library</span>
                        @php $_adMediaCount = \App\Models\AdMedia::count(); @endphp
                        @if($_adMediaCount > 0)
                            <span class="ml-auto px-2 py-0.5 bg-violet-100 text-violet-700 text-[10px] font-bold rounded-full">{{ $_adMediaCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.ad-placements.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.ad-placements.*') ? 'text-blue-600 bg-blue-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.ad-placements.*') ? 'text-blue-600' : 'text-gray-400' }} transition-transform fas fa-map-marker-alt group-hover:scale-110"></i>
                        <span class="text-sm">Ad Placements</span>
                        @php $_livePlacements = \App\Models\AdPlacement::where('is_active', true)->count(); @endphp
                        @if($_livePlacements > 0)
                            <span class="ml-auto px-2 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-bold rounded-full">{{ $_livePlacements }}</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.documents.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.documents.*') ? 'text-blue-600 bg-blue-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.documents.*') ? 'text-blue-600' : 'text-gray-400' }} transition-transform fas fa-folder-open group-hover:scale-110"></i>
                        <span class="text-sm">Agent Documents</span>
                        @php $_attentionDocs = \App\Models\AgentDocument::where('requires_attention', true)->count(); @endphp
                        @if($_attentionDocs > 0)
                            <span class="ml-auto px-2 py-0.5 bg-red-500 text-white text-[10px] font-bold rounded-full">{{ $_attentionDocs }}</span>
                        @endif
                    </a>


                    @if($canManageSettings)
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
                    @endif

                    @if($canViewAuditLogs)
                    <a href="{{ route('admin.audit-logs.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('admin.audit-logs.*') ? 'text-fuchsia-600 bg-fuchsia-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('admin.audit-logs.*') ? 'text-fuchsia-600' : 'text-gray-400' }} transition-transform fas fa-history group-hover:scale-110"></i>
                        <span class="text-sm">Audit Logs</span>
                    </a>
                    @endif
                </nav>
            </div>
        @endif

        <!-- Account Settings -->
        <div class="pt-4 border-t border-gray-200">
        @if (!auth()->user()->hasRole('admin'))
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">Account</p>
            <nav class="space-y-1">
                @php
                    $profileRoute = auth()->user()->hasRole('agent')
                        ? route('agent.profile.show')
                        : route('vendor.profile.show');
                @endphp
                <a href="{{ $profileRoute }}"
                    class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('*.profile.*') ? 'text-slate-600 bg-slate-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                    <i class="w-5 text-center {{ request()->routeIs('*.profile.*') ? 'text-slate-600' : 'text-gray-400' }} transition-transform fas fa-user-circle group-hover:scale-110"></i>
                    <span class="text-sm">Profile Settings</span>
                </a>
                @if (auth()->user()->hasRole('agent'))
                    <a href="{{ route('agent.settings.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('agent.settings.*') ? 'text-slate-600 bg-slate-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('agent.settings.*') ? 'text-slate-600' : 'text-gray-400' }} transition-transform fas fa-cog group-hover:scale-110"></i>
                        <span class="text-sm">Settings</span>
                    </a>
                    <a href="{{ route('agent.notifications.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('agent.notifications.*') ? 'text-slate-600 bg-slate-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('agent.notifications.*') ? 'text-slate-600' : 'text-gray-400' }} transition-transform fas fa-bell group-hover:scale-110"></i>
                        <span class="text-sm">Notifications</span>
                    </a>
                @endif
            </nav>
        @endif

            @if (auth()->user()->isVendor())
                <nav class="space-y-1 mb-4">
                    <a href="{{ route('vendor.store.settings') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('vendor.store.*') ? 'text-slate-600 bg-slate-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('vendor.store.*') ? 'text-slate-600' : 'text-gray-400' }} transition-transform fas fa-store group-hover:scale-110"></i>
                        <span class="text-sm">Store Settings</span>
                    </a>
                @if($canViewShowrooms ?? false)
                    <a href="{{ route('vendor.showrooms.index') }}"
                        class="flex gap-3 items-center px-4 py-2.5 {{ request()->routeIs('vendor.showrooms.*') ? 'text-slate-600 bg-slate-50 font-semibold' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg transition-all group">
                        <i class="w-5 text-center {{ request()->routeIs('vendor.showrooms.*') ? 'text-slate-600' : 'text-gray-400' }} transition-transform fas fa-building group-hover:scale-110"></i>
                        <span class="text-sm">My Showrooms</span>
                    </a>
                    @endif
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

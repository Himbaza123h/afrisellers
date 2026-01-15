<!-- Top Navigation Bar -->
<nav class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
    <div class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4">
        <div class="flex items-center justify-between">
            <!-- Left: Logo & Mobile Menu -->
            <div class="flex items-center gap-2 sm:gap-3 lg:gap-4">
                <button id="mobile-menu-btn" class="lg:hidden p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <i class="fas fa-bars text-lg sm:text-xl"></i>
                </button>
                <div class="w-72 lg:w-64 flex-shrink-0">
                    <a href={{ route('home')}}>
                    <img src="https://afrisellers.com/public/uploads/all/rcIW6v7SfbxlCbrTIBU6CXQNggsQbKVO1a8vXheE.png"
                        alt="AfriSellers" class="h-8 sm:h-9 lg:h-10">
                    </a>
                </div>

                <!-- Role Badge -->
                <div class="hidden lg:block border-l border-gray-200 pl-3 lg:pl-4">
                    @if (auth()->user()->hasRole('admin'))
                        <p class="text-[10px] sm:text-xs text-gray-500 leading-tight">Superadmin</p>
                        <p class="text-xs sm:text-sm font-bold text-gray-900 leading-tight">Global Control</p>
                    @elseif(auth()->user()->isVendor())
                        <p class="text-[10px] sm:text-xs text-gray-500 leading-tight">Vendor</p>
                        <p class="text-xs sm:text-sm font-bold text-gray-900 leading-tight">Seller Dashboard</p>
                    @endif
                </div>
            </div>

            <!-- Right: Search, Icons & Profile -->
            <div class="flex items-center gap-1 sm:gap-2 lg:gap-3">
                <!-- Search Bar - Hidden on small screens -->
                <div class="relative hidden xl:block">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm mt-2.5"></i>
                    <input type="text" placeholder="Search..."
                        class="pl-9 pr-4 py-2 w-48 xl:w-64 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent transition-all">
                </div>

                <!-- Quick Actions -->
                <!-- Notifications Dropdown Component -->
                <x-notifications-dropdown />

                <!-- System Alerts Dropdown Component (Admin Only) -->
                @if (auth()->user()->hasRole('admin'))
                    <x-alerts-dropdown />
                @endif

                <!-- Profile -->
                <div class="relative">
                    <button id="profile-dropdown-btn-1"
                        class="flex items-center gap-2 sm:gap-3 pl-2 sm:pl-3 lg:pl-4 border-l border-gray-200 hover:bg-gray-50 rounded-lg transition-colors p-1">
                        @php
                            $userName = auth()->user()->name;
                            $userRole = auth()->user()->hasRole('admin')
                                ? 'Superadmin'
                                : (auth()->user()->isVendor()
                                    ? 'Vendor'
                                    : 'User');
                            $roleColor = auth()->user()->hasRole('admin')
                                ? 'ff0808'
                                : (auth()->user()->isVendor()
                                    ? '9333ea'
                                    : '3b82f6');
                        @endphp

                        <img src="https://ui-avatars.com/api/?name={{ urlencode($userName) }}&background={{ $roleColor }}&color=fff&bold=true"
                            alt="{{ $userName }}"
                            class="w-8 h-8 sm:w-9 sm:h-9 lg:w-10 lg:h-10 rounded-full ring-2 ring-gray-100">

                        <div class="hidden lg:block text-left">
                            <p class="text-xs sm:text-sm font-bold text-gray-900 leading-tight">{{ $userName }}</p>
                            <p
                                class="text-[10px] sm:text-xs font-semibold leading-tight {{ auth()->user()->hasRole('admin') ? 'text-[#ff0808]' : (auth()->user()->isVendor() ? 'text-purple-600' : 'text-blue-600') }}">
                                {{ $userRole }}
                            </p>
                        </div>
                        <i class="fas fa-chevron-down text-gray-600 text-xs hidden lg:block"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="profile-dropdown-1"
                        class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50 pointer-events-auto">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-sm font-bold text-gray-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                        </div>

                        <a href="{{ route('profile.show') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-user w-5 text-center text-gray-600"></i>
                            <span class="text-sm font-medium">My Profile</span>
                        </a>

                        @if (auth()->user()->hasRole('admin'))
                            <a href="#"
                                class="flex items-center gap-3 px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition-colors">
                                <i class="fas fa-cog w-5 text-center text-gray-600"></i>
                                <span class="text-sm font-medium">Settings</span>
                            </a>
                        @endif

                        @if (auth()->user()->isVendor())
                            <a href="{{ route('vendor.store.settings') }}"
                                class="flex items-center gap-3 px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition-colors">
                                <i class="fas fa-store w-5 text-center text-purple-600"></i>
                                <span class="text-sm font-medium">My Store</span>
                            </a>
                        @endif

                        <div class="border-t border-gray-100 mt-2"></div>

                        <form action="{{ route('auth.logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-3 px-4 py-2.5 text-red-600 hover:bg-red-50 transition-all group">
                                <i
                                    class="fas fa-sign-out-alt w-5 text-center group-hover:scale-110 transition-transform"></i>
                                <span class="text-sm font-medium">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    // Profile dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    // Handle all profile dropdowns
    [1, 2].forEach(num => {
        const profileBtn = document.getElementById(`profile-dropdown-btn-${num}`);
        const profileDropdown = document.getElementById(`profile-dropdown-${num}`);

        if (profileBtn && profileDropdown) {
            profileBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                profileDropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', function(e) {
                if (!profileBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
                    profileDropdown.classList.add('hidden');
                }
            });
        }
    });
});
</script>

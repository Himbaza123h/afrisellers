<!-- Secondary Navigation Bar (Horizontal Menu) -->
<div class="sticky top-16 z-40 bg-white border-b border-gray-200 shadow-sm">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-3">
            <!-- Left: Navigation Links -->
            <nav class="flex overflow-x-auto gap-1 items-center scrollbar-hide">
                <!-- Dashboard Link -->
                <a href="{{ route('buyer.dashboard.home') }}"
                    class="flex items-center gap-2 px-3 sm:px-4 py-2 {{ request()->routeIs('buyer.dashboard.home') ? 'bg-[#ff0808] text-white' : 'text-gray-700 hover:bg-gray-100' }} rounded-md font-semibold text-xs sm:text-sm whitespace-nowrap transition-colors">
                    <i class="text-sm fas fa-th-large"></i>
                    <span class="hidden sm:inline">{{ __('messages.dashboard') }}</span>
                </a>

                <!-- My Orders Link -->
                <a href="{{ route('buyer.orders') }}"
                    class="flex gap-2 items-center px-3 py-2 {{ request()->routeIs('buyer.orders*') ? 'bg-[#ff0808] text-white' : 'text-gray-700 hover:bg-gray-100' }} text-xs font-semibold whitespace-nowrap rounded-md transition-colors sm:px-4 sm:text-sm">
                    <i class="text-sm fas fa-box"></i>
                    <span class="hidden sm:inline">{{ __('messages.my_orders') }}</span>
                </a>

                <!-- Wishlist Link -->
                <a href="#"
                    class="flex gap-2 items-center px-3 py-2 text-xs font-semibold text-gray-700 whitespace-nowrap rounded-md transition-colors sm:px-4 hover:bg-gray-100 sm:text-sm">
                    <i class="text-sm fas fa-heart"></i>
                    <span class="hidden sm:inline">{{ __('messages.wishlist') }}</span>
                </a>



                <!-- My RFQs Link -->
                <a href="{{ route('buyer.rfqs.index') }}"
                    class="flex gap-2 items-center px-3 py-2 {{ request()->routeIs('buyer.rfqs*') ? 'bg-[#ff0808] text-white' : 'text-gray-700 hover:bg-gray-100' }} text-xs font-semibold whitespace-nowrap rounded-md transition-colors sm:px-4 sm:text-sm">
                    <i class="text-sm fas fa-file-invoice"></i>
                    <span class="hidden sm:inline">{{ __('messages.my_rfqs') }}</span>
                </a>

                <!-- Account Settings Link -->
                <a href="{{ route('buyer.account.settings') }}"
                    class="flex gap-2 items-center px-3 py-2 {{ request()->routeIs('buyer.account.settings') ? 'bg-[#ff0808] text-white' : 'text-gray-700 hover:bg-gray-100' }} text-xs font-semibold whitespace-nowrap rounded-md transition-colors sm:px-4 sm:text-sm">
                    <i class="text-sm fas fa-user-cog"></i>
                    <span class="hidden sm:inline">{{ __('messages.account_settings') }}</span>
                </a>

                @php
                    $user = auth()->user();
                    $businessProfile = App\Models\BusinessProfile::where('user_id', $user->id)->first();
                    $hasVendor = App\Models\Vendor\Vendor::where('user_id', $user->id)->exists();
                    $hasAgent  = App\Models\Agent::where('user_id', $user->id)->exists();
                    $agentRequest = App\Models\AgentRequest::where('user_id', $user->id)->latest()->first();
                @endphp

                @if ($hasVendor)
                    <a href="{{ route('vendor.dashboard.home') }}"
                        class="flex gap-2 items-center px-3 py-2 {{ request()->routeIs('vendor.dashboard*') ? 'bg-[#ff0808] text-white' : 'text-gray-700 hover:bg-gray-100' }} text-xs font-semibold whitespace-nowrap rounded-md transition-colors sm:px-4 sm:text-sm">
                        <i class="text-sm fas fa-store"></i>
                        <span class="hidden sm:inline">Vendor Dashboard</span>
                    </a>
                @elseif ($businessProfile && $businessProfile->verification_status === 'pending')
                    <a href="{{ route('buyer.submitted-business') }}"
                        class="flex gap-2 items-center px-3 py-2 {{ request()->routeIs('buyer.submitted-business') ? 'bg-[#ff0808] text-white' : 'text-gray-700 hover:bg-gray-100' }} text-xs font-semibold whitespace-nowrap rounded-md transition-colors sm:px-4 sm:text-sm">
                        <i class="text-sm fas fa-clock"></i>
                        <span class="hidden sm:inline">Submitted Business</span>
                    </a>
                @else
                    <a href="{{ route('buyer.become-vendor') }}"
                        class="flex gap-2 items-center px-3 py-2 {{ request()->routeIs('buyer.become-vendor') ? 'bg-[#ff0808] text-white' : 'text-gray-700 hover:bg-gray-100' }} text-xs font-semibold whitespace-nowrap rounded-md transition-colors sm:px-4 sm:text-sm">
                        <i class="text-sm fas fa-store"></i>
                        <span class="hidden sm:inline">Become Vendor</span>
                    </a>
                @endif

                {{-- Agent section --}}
                @if ($hasAgent)
                    <a href="{{ route('agent.dashboard') ?? '#' }}"
                        class="flex gap-2 items-center px-3 py-2 text-gray-700 hover:bg-gray-100 text-xs font-semibold whitespace-nowrap rounded-md transition-colors sm:px-4 sm:text-sm">
                        <i class="text-sm fas fa-user-tie"></i>
                        <span class="hidden sm:inline">Agent Dashboard</span>
                    </a>
                @elseif ($agentRequest && $agentRequest->status === 'pending')
                    <a href="{{ route('buyer.become-agent') }}"
                        class="flex gap-2 items-center px-3 py-2 {{ request()->routeIs('buyer.become-agent') ? 'bg-[#ff0808] text-white' : 'text-gray-700 hover:bg-gray-100' }} text-xs font-semibold whitespace-nowrap rounded-md transition-colors sm:px-4 sm:text-sm">
                        <i class="text-sm fas fa-clock"></i>
                        <span class="hidden sm:inline">Agent Request</span>
                    </a>
                @else
                    <a href="{{ route('buyer.become-agent') }}"
                        class="flex gap-2 items-center px-3 py-2 {{ request()->routeIs('buyer.become-agent') ? 'bg-[#ff0808] text-white' : 'text-gray-700 hover:bg-gray-100' }} text-xs font-semibold whitespace-nowrap rounded-md transition-colors sm:px-4 sm:text-sm">
                        <i class="text-sm fas fa-user-tie"></i>
                        <span class="hidden sm:inline">Become Agent</span>
                    </a>
                @endif

            </nav>
        </div>
    </div>
</div>

<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>

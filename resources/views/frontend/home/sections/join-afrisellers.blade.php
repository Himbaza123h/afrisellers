<section class="relative py-8 overflow-hidden">
    <div class="container px-4 mx-auto">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.pexels.com/photos/30918003/pexels-photo-30918003.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2"
                 alt="Join Afrisellers"
                 class="w-full h-full object-cover"
                 style="object-position: center 70%;">
            <div class="absolute inset-0 bg-gradient-to-r from-gray-900/90 via-gray-900/80 to-gray-900/70"></div>
        </div>

        <!-- Content -->
        <div class="container relative z-10 px-4 mx-auto">
            <div class="max-w-3xl mx-auto text-center">
                <!-- Main Heading -->
                <h2 class="mb-3 text-2xl font-bold text-white md:text-3xl">
                    {{ __('messages.join_afrisellers_today') }}
                </h2>

                <!-- Subtitle -->
                <p class="mb-6 text-base text-gray-200">
                    {{ __('messages.connect_verified_african_suppliers') }}
                </p>

                <!-- Icons Row -->
                <div class="flex justify-center items-center gap-3 mb-6">
                    <!-- Email Icon -->
                    <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white/10 rounded-lg backdrop-blur-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-xs font-medium text-white">{{ __('messages.email_icon') }}</span>
                    </div>

                    <!-- Person Icon -->
                    <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white/10 rounded-lg backdrop-blur-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="text-xs font-medium text-white">{{ __('messages.person_icon') }}</span>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 justify-center items-center">
                    <!-- Join as Buyer Button -->
                    <a href=""
                       class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-gray-900 bg-[#ff9933] rounded-lg hover:bg-[#ff8800] transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl min-w-[180px] justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        {{ __('messages.join_as_buyer') }}
                    </a>

                    <!-- Join as Supplier Button -->
                    <a href=""
                       class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl min-w-[180px] justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        {{ __('messages.join_as_supplier') }}
                    </a>
                </div>

                <!-- Additional Info -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 text-white">
                    <!-- Stat 1 -->
                    <div class="p-3 bg-white/10 rounded-lg backdrop-blur-sm">
                        <div class="text-xl font-bold text-[#ff9933] mb-1">10,000+</div>
                        <div class="text-xs">{{ __('messages.verified_suppliers') }}</div>
                    </div>

                    <!-- Stat 2 -->
                    <div class="p-3 bg-white/10 rounded-lg backdrop-blur-sm">
                        <div class="text-xl font-bold text-green-400 mb-1">50+</div>
                        <div class="text-xs">{{ __('messages.african_countries') }}</div>
                    </div>

                    <!-- Stat 3 -->
                    <div class="p-3 bg-white/10 rounded-lg backdrop-blur-sm">
                        <div class="text-xl font-bold text-blue-400 mb-1">24/7</div>
                        <div class="text-xs">{{ __('messages.support_available') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Decorative Elements -->
        <div class="absolute top-10 left-10 w-16 h-16 bg-[#ff9933]/20 rounded-full blur-xl animate-pulse"></div>
        <div class="absolute bottom-10 right-10 w-20 h-20 bg-green-500/20 rounded-full blur-xl animate-pulse delay-1000"></div>
    </div>
</section>

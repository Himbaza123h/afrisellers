<footer class="bg-gray-900 text-gray-400">
    <div class="container mx-auto px-4 py-8 md:py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 md:gap-6 lg:gap-8 mb-6 md:mb-8">
            <!-- About -->
            <div class="lg:col-span-2">
                <h3 class="text-white font-bold text-base md:text-lg mb-2 md:mb-3">
                    <img src="{{ asset('mainlogo.png') }}" alt="" class="h-8 md:h-10 inline-block mb-1 md:mb-2">
                </h3>
                <p class="text-[10px] md:text-xs leading-relaxed mb-3 md:mb-4">{{ __('messages.footer_about') }}</p>
                <div class="flex gap-2 md:gap-3">
                    <a href="https://www.facebook.com/share/18JZuFCVuG/?mibextid=wwXIfr " class="w-7 h-7 md:w-8 md:h-8 bg-gray-800 hover:bg-blue-600 rounded-lg flex items-center justify-center transition-colors">
                        <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <a href="https://x.com/Afrisellers" class="w-7 h-7 md:w-8 md:h-8 bg-gray-800 hover:bg-blue-400 rounded-lg flex items-center justify-center transition-colors">
<svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.737-8.835L1.254 2.25H8.08l4.259 5.63L18.244 2.25zm-1.161 17.52h1.833L7.084 4.126H5.117L17.083 19.77z"/>
                        </svg>
                    </a>
                    <a href="#" class="w-7 h-7 md:w-8 md:h-8 bg-gray-800 hover:bg-blue-700 rounded-lg flex items-center justify-center transition-colors">
                        <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </a>
                    <a href="https://www.instagram.com/afriseller?igsh=MXhnZ3ZiMXR1dWd0dQ%3D%3D&utm_source=qr" class="w-7 h-7 md:w-8 md:h-8 bg-gray-800 hover:bg-pink-600 rounded-lg flex items-center justify-center transition-colors">
                        <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- For Buyers -->
            <div>
                <h3 class="text-white font-semibold text-[10px] md:text-xs mb-2 md:mb-3 uppercase tracking-wider">{{ __('messages.footer_for_buyers') }}</h3>
                <ul class="space-y-1.5 md:space-y-2 text-[9px] md:text-[10px]">
                    <li><a href="{{ route('rfqs.create') }}" class="hover:text-white transition-colors">{{ __('messages.footer_post_rfq') }}</a></li>
                    <li><a href="{{ route('featured-suppliers') }}" class="hover:text-white transition-colors">{{ __('messages.footer_browse_suppliers') }}</a></li>
                    <li><a href="" class="hover:text-white transition-colors">{{ __('messages.footer_product_categories') }}</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">{{ __('messages.footer_buyer_protection') }}</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">{{ __('messages.footer_trade_assurance') }}</a></li>
                </ul>
            </div>

            <!-- Shipping Brand -->
            <div>
                <h3 class="text-white font-semibold text-[10px] md:text-xs mb-2 md:mb-3 uppercase tracking-wider">{{ __('Shipping Brands') }}</h3>
                <ul class="space-y-1.5 md:space-y-2 text-[9px] md:text-[10px]">
                    <li><a href="#" class="hover:text-white transition-colors">{{ __('DHL Express') }}</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">{{ __('FedEx Shipping') }}</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">{{ __('UPS Logistics') }}</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">{{ __('Aramex Africa') }}</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">{{ __('Local Carriers') }}</a></li>
                </ul>
            </div>

            <!-- For Suppliers -->
            <div>
                <h3 class="text-white font-semibold text-[10px] md:text-xs mb-2 md:mb-3 uppercase tracking-wider">{{ __('messages.footer_for_suppliers') }}</h3>
                <ul class="space-y-1.5 md:space-y-2 text-[9px] md:text-[10px]">
                    <li><a href="#" class="hover:text-white transition-colors">{{ __('messages.footer_start_selling') }}</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">{{ __('messages.footer_membership_plans') }}</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">{{ __('messages.footer_seller_resources') }}</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">{{ __('messages.footer_success_stories') }}</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">{{ __('messages.footer_advertising') }}</a></li>
                </ul>
            </div>

            <!-- Support -->
            <div>
                <h3 class="text-white font-semibold text-[10px] md:text-xs mb-2 md:mb-3 uppercase tracking-wider">{{ __('messages.footer_support') }}</h3>
                <ul class="space-y-1.5 md:space-y-2 text-[9px] md:text-[10px]">
                    <li><a href="#" class="hover:text-white transition-colors">{{ __('messages.footer_help_center') }}</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">{{ __('messages.footer_contact_us') }}</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">{{ __('messages.footer_terms_conditions') }}</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">{{ __('messages.footer_privacy_policy') }}</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">{{ __('messages.footer_safety_security') }}</a></li>
                </ul>
            </div>
        </div>

        <!-- Payment & Trust Badges -->
        <div class="border-t border-gray-800 pt-4 md:pt-6 pb-4 md:pb-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-3 md:gap-4">
                <div class="text-[9px] md:text-[10px]">
                    <p class="text-gray-500 mb-1.5 md:mb-2">{{ __('messages.footer_secure_payment') }}</p>
                    <div class="flex gap-2">
                        <div class="bg-white px-2 py-1 md:px-2.5 md:py-1.5 rounded">
                            <svg class="h-4 md:h-5" viewBox="0 0 38 24" fill="none">
                                <rect width="48" height="24" rx="3" fill="#fff"/>
                                <circle cx="15" cy="12" r="7" fill="#EB001B"/>
                                <circle cx="23" cy="12" r="7" fill="#F79E1B"/>
                                <path d="M19 6.5c1.4 1.2 2.3 3 2.3 5s-.9 3.8-2.3 5c-1.4-1.2-2.3-3-2.3-5s.9-3.8 2.3-5z" fill="#FF5F00"/>
                            </svg>
                        </div>
                        <div class="bg-white px-2 py-1 md:px-2.5 md:py-1.5 rounded">
                            <svg class="h-4 md:h-5" viewBox="0 0 48 24" fill="none">
                                <rect width="48" height="24" rx="3" fill="#fff"/>
                                <path d="M15.3 8.5l-2.8 7h-2l-1.4-5.4c-.1-.3-.2-.4-.4-.5-.4-.2-1-.4-1.5-.5l0-.1h2.6c.3 0 .6.2.7.6l.6 3.4 1.6-4h2l0 0zm8 4.7c0-1.9-2.6-2-2.6-2.8 0-.2.2-.5.7-.6.3 0 1-.1 1.7.3l.3-1.4c-.4-.2-1-.3-1.6-.3-1.7 0-2.9.9-2.9 2.2 0 1 .9 1.5 1.5 1.8.7.3.9.5.9.8 0 .4-.5.6-1 .6-.8 0-1.3-.2-1.6-.4l-.3 1.4c.4.2 1.1.3 1.8.3 1.8.1 3-.8 3.1-2.1v.2zm4.7 2.3h1.8l-1.6-7h-1.7c-.3 0-.6.2-.7.5l-2.5 6.5h1.8l.4-1h2.2l.3 1zm-1.9-2.4l.9-2.5.5 2.5h-1.4zm-6.5-4.6l-1.4 7h-1.7l1.4-7h1.7z" fill="#1434CB"/>
                            </svg>
                        </div>
                        <div class="bg-white px-2 py-1 md:px-2.5 md:py-1.5 rounded flex items-center">
                            <img src="https://www.openbanking.org.uk/wp-content/uploads/PayPal_Logo_Horizontal_Full_Color_RGB-002-1.png" alt="" class="h-4 md:h-5 w-auto">
                        </div>
                        <div class="bg-white px-2 py-1 md:px-2.5 md:py-1.5 rounded flex items-center">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/0/0b/M-PESA.png" alt="" class="h-4 md:h-5 w-auto">
                        </div>
                    </div>
                </div>
                <div class="text-[9px] md:text-[10px] text-center md:text-right">
                    <p class="text-gray-500 mb-1.5 md:mb-2">{{ __('messages.footer_trusted_by') }}</p>
                    <div class="flex items-center gap-2 md:gap-3">
                        <div class="bg-gray-800 px-2 md:px-3 py-1 md:py-1.5 rounded text-[8px] md:text-[9px] text-gray-400">
                            <span class="font-semibold">50,000+</span> {{ __('messages.footer_suppliers_count') }}
                        </div>
                        <div class="bg-gray-800 px-2 md:px-3 py-1 md:py-1.5 rounded text-[8px] md:text-[9px] text-gray-400">
                            <span class="font-semibold">100,000+</span> {{ __('messages.footer_buyers_count') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-gray-800 pt-4 md:pt-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-2 md:gap-3 text-[9px] md:text-[10px]">
                <p class="text-gray-500">© {{ date('Y') }} {{ \App\Models\SystemSetting::get('site_name', config('app.name')) }}. {{ __('messages.footer_rights_reserved') }}</p>
                <div class="flex flex-wrap justify-center gap-3 md:gap-4 text-gray-500">
                    <a href="#" class="hover:text-white transition-colors">{{ __('messages.footer_sitemap') }}</a>
                    <a href="#" class="hover:text-white transition-colors">{{ __('messages.footer_cookies_policy') }}</a>
                    <a href="#" class="hover:text-white transition-colors">{{ __('messages.footer_accessibility') }}</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-8 gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Latest Request for Quotations</h2>
                <p class="text-gray-600">Submit your quote and win new business opportunities</p>
            </div>
            <a href="#" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors text-center">
                Post Your RFQ
            </a>
        </div>
        <div class="space-y-4">
            @for($i = 1; $i <= 5; $i++)
            <div class="bg-gray-50 rounded-lg p-4 md:p-6 hover:shadow-md transition-shadow border border-gray-200">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex flex-wrap items-center gap-3 mb-3">
                            <h3 class="font-bold text-lg text-gray-900">Looking for Solar Panels - 500 Units</h3>
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">Active</span>
                        </div>
                        <p class="text-gray-600 mb-4">Need high-quality solar panels for commercial installation. Preferred brands: Tier 1 manufacturers. Delivery to Nairobi, Kenya.</p>
                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Kenya
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                $50,000 - $75,000
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                2 hours ago
                            </span>
                            <span class="text-blue-600 font-semibold">12 Quotes Received</span>
                        </div>
                    </div>
                    <button class="bg-blue-600 text-white px-6 py-2.5 rounded-lg hover:bg-blue-700 transition-colors whitespace-nowrap font-semibold">
                        Submit Quote
                    </button>
                </div>
            </div>
            @endfor
        </div>
        <div class="text-center mt-8">
            <a href="#" class="text-blue-600 hover:text-blue-700 font-semibold inline-flex items-center gap-2">
                View All RFQs
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>
</section>

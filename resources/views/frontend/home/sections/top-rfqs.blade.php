<section class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div style="max-w-8xl; margin: 0 auto;">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-12 gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Request for Quotations</h2>
                    <p class="text-gray-600">Connect with buyers and grow your business</p>
                </div>
                <a href="#" class="border-2 border-blue-600 text-blue-600 px-6 py-2.5 rounded-lg font-medium hover:bg-blue-50 transition-colors text-center inline-flex items-center justify-center gap-2 whitespace-nowrap">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Post Your RFQ
                </a>
            </div>

            <div class="space-y-4">
                @php
                $rfqs = [
                    [
                        'id' => 1,
                        'title' => 'Solar Panel Installation - 500 Units Required',
                        'description' => 'Seeking tier-1 manufacturers for high-efficiency solar panels. Commercial-grade installation for industrial facility in Nairobi. Must include warranty and installation support documentation.',
                        'location' => 'Kenya',
                        'budget' => '$50,000 - $75,000',
                        'time' => '2 hours ago',
                        'quotes' => '12',
                        'category' => 'Energy & Power',
                        'urgent' => true,
                        'requirements' => [
                            'Tier 1 manufacturer certification',
                            'Minimum 25-year warranty',
                            'Installation documentation and support',
                            'Delivery within 30 days'
                        ]
                    ],
                    [
                        'id' => 2,
                        'title' => 'Premium Organic Coffee Beans - 5 Tons Bulk Order',
                        'description' => 'Looking for certified organic coffee suppliers. Required: Grade A Arabica beans with full traceability and Fair Trade certification for European export market.',
                        'location' => 'Ethiopia',
                        'budget' => '$30,000 - $45,000',
                        'time' => '5 hours ago',
                        'quotes' => '8',
                        'category' => 'Agriculture',
                        'urgent' => false,
                        'requirements' => [
                            'Organic certification required',
                            'Fair Trade certified',
                            'Full traceability documentation',
                            'Quality assurance samples'
                        ]
                    ],
                    [
                        'id' => 3,
                        'title' => 'Construction Materials: Cement & Steel Rods',
                        'description' => 'Major infrastructure project requires 200 tons cement and 50 tons reinforcement steel. Delivery to Lagos construction site with phased delivery schedule.',
                        'location' => 'Nigeria',
                        'budget' => '$80,000 - $100,000',
                        'time' => '1 day ago',
                        'quotes' => '15',
                        'category' => 'Construction',
                        'urgent' => false,
                        'requirements' => [
                            'ISO certification for all materials',
                            'Phased delivery capability',
                            'Quality testing certificates',
                            'Previous project references'
                        ]
                    ],
                    [
                        'id' => 4,
                        'title' => 'Electronics: Smartphones & Tablets - 1000 Units',
                        'description' => 'Retail chain seeking mid-range devices for East African distribution. Warranty and after-sales support required for all units.',
                        'location' => 'Tanzania',
                        'budget' => '$120,000 - $150,000',
                        'time' => '1 day ago',
                        'quotes' => '6',
                        'category' => 'Electronics',
                        'urgent' => true,
                        'requirements' => [
                            '12-month warranty on all devices',
                            'Local after-sales support',
                            'Bulk packaging and shipping',
                            'Flexible payment terms available'
                        ]
                    ],
                    [
                        'id' => 5,
                        'title' => 'Medical Equipment for New Healthcare Facility',
                        'description' => 'Hospital expansion needs 50 adjustable beds and 30 wheelchairs. ISO-certified suppliers only with installation and staff training support.',
                        'location' => 'Ghana',
                        'budget' => '$25,000 - $35,000',
                        'time' => '2 days ago',
                        'quotes' => '10',
                        'category' => 'Healthcare',
                        'urgent' => false,
                        'requirements' => [
                            'ISO 13485 certification',
                            'Installation and setup included',
                            'Staff training program',
                            'Maintenance support contract'
                        ]
                    ]
                ];
                @endphp

                @foreach($rfqs as $rfq)
                <div class="bg-white rounded-lg border border-gray-200 hover:border-blue-300 transition-all duration-300 rfq-accordion">
                    <div class="p-5 cursor-pointer rfq-header" data-rfq-id="{{ $rfq['id'] }}">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-start gap-3 mb-3">
                                    <h3 class="font-semibold text-lg text-gray-900 leading-tight flex-1">{{ $rfq['title'] }}</h3>
                                    <div class="flex gap-2 shrink-0">
                                        @if($rfq['urgent'])
                                        <span class="bg-red-100 text-red-700 px-2.5 py-1 rounded-full text-xs font-medium">Urgent</span>
                                        @endif
                                        <span class="bg-green-100 text-green-700 px-2.5 py-1 rounded-full text-xs font-medium">Open</span>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm">
                                    <span class="text-gray-600 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                        </svg>
                                        <span class="font-medium">{{ $rfq['category'] }}</span>
                                    </span>
                                    <span class="text-gray-600 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        {{ $rfq['location'] }}
                                    </span>
                                    <span class="text-gray-600 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="font-semibold text-gray-900">{{ $rfq['budget'] }}</span>
                                    </span>
                                    <span class="text-gray-500 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $rfq['time'] }}
                                    </span>
                                    <span class="text-blue-600 font-semibold flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        {{ $rfq['quotes'] }} Quotes
                                    </span>
                                </div>
                            </div>

                            <button class="rfq-toggle text-gray-400 hover:text-blue-600 transition-transform duration-300 shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="rfq-content hidden border-t border-gray-200">
                        <div class="p-5 bg-gray-50">
                            <div class="mb-5">
                                <h4 class="font-semibold text-gray-900 mb-2">Description</h4>
                                <p class="text-gray-700 text-sm leading-relaxed">{{ $rfq['description'] }}</p>
                            </div>

                            <div class="mb-5">
                                <h4 class="font-semibold text-gray-900 mb-3">Requirements</h4>
                                <ul class="space-y-2">
                                    @foreach($rfq['requirements'] as $requirement)
                                    <li class="flex items-start gap-2 text-sm text-gray-700">
                                        <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>{{ $requirement }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <a href="#" class="border-2 border-blue-600 bg-blue-600 text-white px-6 py-2.5 rounded-lg hover:bg-blue-700 hover:border-blue-700 transition-colors font-medium text-sm inline-flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Submit Quote
                                </a>
                                <a href="#" class="border-2 border-gray-300 text-gray-700 px-6 py-2.5 rounded-lg hover:bg-gray-50 transition-colors font-medium text-sm inline-flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="text-center mt-12">
                <a href="#" class="text-blue-600 hover:text-blue-700 font-medium inline-flex items-center gap-2">
                    View All Quotation Requests
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rfqHeaders = document.querySelectorAll('.rfq-header');

            rfqHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    const accordion = this.closest('.rfq-accordion');
                    const content = accordion.querySelector('.rfq-content');
                    const toggle = accordion.querySelector('.rfq-toggle');

                    // Toggle current accordion
                    content.classList.toggle('hidden');
                    toggle.classList.toggle('rotate-180');

                    // Optional: Close other accordions (remove if you want multiple open)
                    // document.querySelectorAll('.rfq-accordion').forEach(otherAccordion => {
                    //     if (otherAccordion !== accordion) {
                    //         otherAccordion.querySelector('.rfq-content').classList.add('hidden');
                    //         otherAccordion.querySelector('.rfq-toggle').classList.remove('rotate-180');
                    //     }
                    // });
                });
            });
        });
    </script>
</section>

@extends('layouts.home')

@section('page-content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Available Addons</h1>
            <p class="mt-1 text-xs text-gray-500">
                Promote your products and services with premium placements
                @if($businessProfile->country)
                    <span class="inline-flex items-center gap-1 ml-2 px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-medium">
                        <i class="fas fa-globe"></i>
                        {{ $businessProfile->country->name }}
                    </span>
                @endif
            </p>
        </div>
        <a href="{{ route('vendor.addons.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
            <i class="fas fa-arrow-left"></i>
            <span>Back to My Addons</span>
        </a>
    </div>

    <!-- Info Banner -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="bg-[#ff0808] p-5 text-white">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-lightbulb text-xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-lg mb-1">How Addons Work</h3>
                    <p class="text-sm text-white text-opacity-90 mb-3">
                        Addons place your items in premium positions across our platform, increasing visibility and engagement.
                        Choose from various high-traffic locations to maximize your reach.
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-white bg-opacity-20 rounded-full text-xs font-medium">
                            <i class="fas fa-eye"></i> Increased Visibility
                        </span>
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-white bg-opacity-20 rounded-full text-xs font-medium">
                            <i class="fas fa-chart-line"></i> Higher Engagement
                        </span>
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-white bg-opacity-20 rounded-full text-xs font-medium">
                            <i class="fas fa-dollar-sign"></i> Boost Sales
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $homepageSlotHelp = [
            ['y' => 'recommendedsuppliers', 'section' => __('messages.most_recommended_suppliers'), 'promote' => 'Supplier profile'],
            ['y' => 'weeklyoffers', 'section' => __('messages.weekly_special_offers'), 'promote' => 'Product'],
            ['y' => 'hotdeals', 'section' => __('messages.hot_deals'), 'promote' => 'Product'],
            ['y' => 'featuredsuppliers', 'section' => __('messages.recommended_suppliers'), 'promote' => 'Supplier profile'],
            ['y' => 'trendingproducts', 'section' => __('messages.trending_products'), 'promote' => 'Product'],
            ['y' => 'BrowseBY', 'section' => __('messages.browse_by_region'), 'promote' => '—'],
        ];
    @endphp
    <div class="p-4 bg-slate-50 rounded-lg border border-slate-200 text-sm text-slate-800">
        <p class="font-semibold text-slate-900 mb-2 flex items-center gap-2">
            <i class="fas fa-map-signs text-[#ff0808]"></i>
            Homepage add-ons — which position goes where
        </p>
        <p class="text-xs text-slate-600 mb-3">
            On each add-on, <span class="font-medium">Homepage</span> is the area; the line below (e.g. Recommendedsuppliers) is the <span class="font-medium">position</span>.
            For <span class="font-medium">{{ __('messages.most_recommended_suppliers') }}</span>, buy an add-on whose position is
            <code class="px-1 py-0.5 bg-white border border-slate-200 rounded text-[11px] font-mono">recommendedsuppliers</code>
            and complete checkout as <span class="font-medium">Supplier profile</span>.
        </p>
        <div class="overflow-x-auto rounded border border-slate-200 bg-white">
            <table class="w-full text-xs">
                <thead class="bg-slate-100 text-slate-600 uppercase tracking-wide">
                    <tr>
                        <th class="text-left px-3 py-2 font-semibold">{{ __('Position (location)') }}</th>
                        <th class="text-left px-3 py-2 font-semibold">{{ __('Homepage area') }}</th>
                        <th class="text-left px-3 py-2 font-semibold">{{ __('Usually promote') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($homepageSlotHelp as $row)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-3 py-2 font-mono text-[11px] text-slate-800">{{ $row['y'] }}</td>
                            <td class="px-3 py-2 font-medium text-slate-900">{{ $row['section'] }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $row['promote'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-3 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <p class="text-sm font-medium text-red-900 flex-1">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <!-- Selected Addons Summary (Floating Cart) -->
    <div id="selectedAddonsSummary" class="hidden sticky top-4 z-40">
        <div class="bg-white rounded-lg border-2 border-[#ff0808] shadow-lg overflow-hidden">
            <div class="bg-[#ff0808] px-5 py-3 text-white flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-shopping-cart"></i>
                    <h3 class="font-bold text-sm">Selected Addons (<span id="selectedCount">0</span>)</h3>
                </div>
                <button onclick="clearSelectedAddons()" class="text-xs text-white text-opacity-80 hover:text-white">
                    <i class="fas fa-trash mr-1"></i> Clear All
                </button>
            </div>
            <div class="p-4">
                <div id="selectedAddonsList" class="space-y-2 mb-3 max-h-40 overflow-y-auto"></div>
                <div class="pt-3 border-t border-gray-200 flex items-center justify-between mb-3">
                    <span class="text-sm font-semibold text-gray-700">Total:</span>
                    <span id="totalPrice" class="text-lg font-bold text-gray-900">$0.00</span>
                </div>
                <button onclick="proceedToCheckout()" class="w-full px-4 py-2.5 bg-[#ff0808] text-white rounded-lg font-bold text-sm hover:bg-[#dd0606] hover:shadow-lg transition-all">
                    <i class="fas fa-bolt mr-1"></i>
                    Purchase Selected Addons
                </button>
            </div>
        </div>
    </div>

    @php
        // --- Build dynamic tabs from actual locationX values in the collection ---
        // $availableAddons is a paginator, so use ->getCollection() to work with items
        $addonItems = $availableAddons instanceof \Illuminate\Pagination\AbstractPaginator
            ? $availableAddons->getCollection()
            : $availableAddons;

        // Get all unique locationX values present in the data
        $uniqueLocations = $addonItems->pluck('locationX')->unique()->filter()->sort()->values()->toArray();

        // Icon map — add more as needed; falls back to fa-tag
        $locationIconMap = [
            'homepage'    => 'fa-home',
            'products'    => 'fa-boxes',
            'suppliers'   => 'fa-store',
            'marketplace' => 'fa-shopping-bag',
            'category'    => 'fa-list',
            'search'      => 'fa-search',
            'blog'        => 'fa-newspaper',
            'sidebar'     => 'fa-columns',
            'banner'      => 'fa-image',
        ];

        // Color map — cycles if more locations than colors
        $colorPalette = [
            'bg-pink-500',   'bg-blue-500',  'bg-purple-500',
            'bg-green-500',  'bg-orange-500','bg-teal-500',
            'bg-rose-500',   'bg-indigo-500','bg-yellow-500',
            'bg-cyan-500',
        ];
        $textPalette = [
            'text-pink-800',   'text-blue-800',  'text-purple-800',
            'text-green-800',  'text-orange-800','text-teal-800',
            'text-rose-800',   'text-indigo-800','text-yellow-800',
            'text-cyan-800',
        ];
        $bgPalette = [
            'bg-pink-100',   'bg-blue-100',  'bg-purple-100',
            'bg-green-100',  'bg-orange-100','bg-teal-100',
            'bg-rose-100',   'bg-indigo-100','bg-yellow-100',
            'bg-cyan-100',
        ];

        // Build a map: locationX => [bgClass, iconClass, index]
        $locationStyleMap = [];
        foreach ($uniqueLocations as $i => $loc) {
            $key = strtolower($loc);
            $locationStyleMap[$loc] = [
                'bg'   => $colorPalette[$i % count($colorPalette)],
                'icon' => $locationIconMap[$key] ?? 'fa-tag',
                'idx'  => $i,
            ];
        }
    @endphp

    <!-- Tab Navigation -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex overflow-x-auto border-b border-gray-200">

            <!-- "All" tab -->
            <button onclick="switchLocationTab('all')"
                    id="tab-all"
                    class="location-tab flex-shrink-0 px-4 py-3 text-sm font-semibold text-gray-600 hover:text-[#ff0808] hover:bg-gray-50 transition-all duration-200 border-b-2 border-transparent">
                <div class="flex items-center justify-center gap-2 whitespace-nowrap">
                    <i class="fas fa-th-large"></i>
                    <span>All Addons</span>
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-gray-100 text-gray-600 text-xs font-bold">
                        {{ $addonItems->count() }}
                    </span>
                </div>
            </button>

            <!-- Dynamic tabs from real locationX values -->
            @foreach($uniqueLocations as $loc)
                @php
                    $style  = $locationStyleMap[$loc];
                    $count  = $addonItems->where('locationX', $loc)->count();
                    $tabId  = 'tab-' . Str::slug($loc);
                @endphp
                <button onclick="switchLocationTab('{{ Str::slug($loc) }}')"
                        id="{{ $tabId }}"
                        class="location-tab flex-shrink-0 px-4 py-3 text-sm font-semibold text-gray-600 hover:text-[#ff0808] hover:bg-gray-50 transition-all duration-200 border-b-2 border-transparent">
                    <div class="flex items-center justify-center gap-2 whitespace-nowrap">
                        <i class="fas {{ $style['icon'] }}"></i>
                        <span>{{ $loc }}</span>
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-gray-100 text-gray-600 text-xs font-bold">
                            {{ $count }}
                        </span>
                    </div>
                </button>
            @endforeach
        </div>
    </div>

    <!-- Search -->
<div class="bg-white rounded-lg border border-gray-200 shadow-sm px-4 py-3">
    <div class="relative">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm mt-2"></i>
        <input type="text"
               id="addonSearch"
               placeholder="Search by location, position, country..."
               class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#ff0808] focus:border-transparent outline-none"
               oninput="filterAddons(this.value)">
        <button onclick="document.getElementById('addonSearch').value=''; filterAddons('')"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden"
                id="clearSearch">
            <i class="fas fa-times text-sm"></i>
        </button>
    </div>
    <p id="searchResultCount" class="text-xs text-gray-500 mt-2 hidden"></p>
</div>

<!-- Tab Content -->
    <div class="relative min-h-400">

        {{-- ALL tab --}}
        <div id="content-all" class="location-content hidden">
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-5 bg-gray-50 border-b flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900">All Available Addons</h3>
                    <span class="text-xs text-gray-500">Select multiple addons to purchase</span>
                </div>

                @forelse($addonItems as $addon)
                    @php
                        $isOwned    = in_array($addon->id, $userAddonIds);
                        $style      = $locationStyleMap[$addon->locationX] ?? ['bg' => 'bg-gray-500', 'icon' => 'fa-tag', 'idx' => 0];
                        $badgeBg    = $bgPalette[$style['idx'] % count($bgPalette)];
                        $badgeText  = $textPalette[$style['idx'] % count($textPalette)];
                    @endphp
                    @include('vendor.addons._addon_row', compact('addon','isOwned','style','badgeBg','badgeText'))
                @empty
                    @include('vendor.addons._addon_empty', ['label' => 'any'])
                @endforelse
            </div>
        </div>

        {{-- Per-location tabs --}}
        @foreach($uniqueLocations as $loc)
            @php
                $style      = $locationStyleMap[$loc];
                $badgeBg    = $bgPalette[$style['idx'] % count($bgPalette)];
                $badgeText  = $textPalette[$style['idx'] % count($textPalette)];
                $locAddons  = $addonItems->where('locationX', $loc)->values();
            @endphp
            <div id="content-{{ Str::slug($loc) }}" class="location-content hidden">
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-5 bg-gray-50 border-b flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900">{{ $loc }} Addons</h3>
                        <span class="text-xs text-gray-500">Select multiple addons to purchase</span>
                    </div>

                    @forelse($locAddons as $addon)
                        @php $isOwned = in_array($addon->id, $userAddonIds); @endphp
                        @include('vendor.addons._addon_row', compact('addon','isOwned','style','badgeBg','badgeText'))
                    @empty
                        @include('vendor.addons._addon_empty', ['label' => $loc])
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>


    <!-- FAQ Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 bg-gray-50 border-b">
            <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-question-circle text-purple-600"></i>
                Frequently Asked Questions
            </h2>
        </div>
        <div class="p-5 space-y-3">
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <h3 class="font-semibold text-gray-900 mb-2 text-sm">How do addons work?</h3>
                <p class="text-xs text-gray-600">Addons place your products, services, or profile in premium positions across our platform. This increases visibility and engagement, leading to more clicks and conversions.</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <h3 class="font-semibold text-gray-900 mb-2 text-sm">Can I purchase multiple addons?</h3>
                <p class="text-xs text-gray-600">Yes! You can select multiple addons at once using the checkboxes and purchase them together. Each addon works independently to promote specific items in different locations.</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <h3 class="font-semibold text-gray-900 mb-2 text-sm">What happens when an addon expires?</h3>
                <p class="text-xs text-gray-600">When an addon expires, your item returns to its normal position. You can renew the addon at any time to continue the promotion. We'll send you reminders before expiration.</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <h3 class="font-semibold text-gray-900 mb-2 text-sm">Do you offer discounts for longer durations?</h3>
                <p class="text-xs text-gray-600">Yes! Save 5% on 2-month plans, 10% on 3-month plans, and 15% on 6-month plans. The longer you commit, the more you save.</p>
            </div>
        </div>
    </div>
</div>

{{-- ─── Addon row partial (inline) ─────────────────────────────────────────── --}}
@push('partials')
@endpush

<!-- Addon Details Modal -->
<div id="addonDetailsModal" class="hidden fixed inset-0 bg-black bg-opacity-60 z-50 flex items-start justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-lg max-w-4xl w-full mt-16 mb-8 animate-modal shadow-2xl">
        <div id="modal-header" class="relative bg-[#ff0808] px-6 py-5 rounded-t-lg text-white">
            <button onclick="closeAddonDetailsModal()" class="absolute top-4 right-4 text-white hover:text-gray-200 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
            <div class="flex items-center gap-4 pr-8">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i id="modal-icon" class="fas fa-home text-3xl"></i>
                </div>
                <div class="flex-1">
                    <h2 id="modal-addon-name" class="text-2xl font-bold mb-1"></h2>
                    <p class="text-white text-opacity-90 text-sm" id="modal-addon-position"></p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold" id="modal-addon-price"></div>
                    <div class="text-white text-opacity-90 text-xs mt-0.5">per 30 days</div>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="mb-6">
                <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fas fa-map-marker-alt text-[#ff0808] text-xs"></i>
                    <span>Location Details</span>
                </h3>
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-purple-50 rounded-lg p-3 border border-purple-100">
                        <p class="text-xs text-gray-600 font-medium mb-1">Placement Area</p>
                        <p id="modal-location" class="text-sm font-bold text-gray-900"></p>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-3 border border-blue-100">
                        <p class="text-xs text-gray-600 font-medium mb-1">Geographic Scope</p>
                        <p id="modal-country" class="text-sm font-bold text-gray-900"></p>
                    </div>
                </div>
            </div>
            <div class="mb-6">
                <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fas fa-star text-yellow-500 text-xs"></i>
                    <span>Features & Benefits</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach(['Prime Location Visibility|Your listing appears in high-traffic premium positions','Increased Click-Through Rate|Premium placements drive significantly more engagement','Flexible Duration Options|Choose from 30, 60, 90, or 180-day plans with discounts','Performance Analytics|Track impressions, clicks, and conversion metrics'] as $feature)
                        @php [$title, $desc] = explode('|', $feature); @endphp
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                            <div class="flex items-start gap-2">
                                <div class="w-7 h-7 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-check text-green-600 text-xs"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 text-xs mb-0.5">{{ $title }}</h4>
                                    <p class="text-xs text-gray-600">{{ $desc }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="mb-6">
                <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fas fa-dollar-sign text-green-600 text-xs"></i>
                    <span>Pricing Information</span>
                </h3>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="grid grid-cols-4 gap-3 text-center">
                        <div>
                            <div class="text-lg font-bold text-gray-900" id="modal-price-30">$0</div>
                            <div class="text-xs text-gray-600">30 days</div>
                        </div>
                        <div>
                            <div class="text-lg font-bold text-gray-900"><span id="modal-price-60">$0</span><span class="text-xs text-green-600 ml-1">-5%</span></div>
                            <div class="text-xs text-gray-600">60 days</div>
                        </div>
                        <div>
                            <div class="text-lg font-bold text-gray-900"><span id="modal-price-90">$0</span><span class="text-xs text-green-600 ml-1">-10%</span></div>
                            <div class="text-xs text-gray-600">90 days</div>
                        </div>
                        <div>
                            <div class="text-lg font-bold text-gray-900"><span id="modal-price-180">$0</span><span class="text-xs text-green-600 ml-1">-15%</span></div>
                            <div class="text-xs text-gray-600">180 days</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 pt-4 border-t border-gray-200">
                <button onclick="closeAddonDetailsModal()" class="flex-1 px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-semibold text-xs transition-all">
                    <i class="fas fa-arrow-left mr-1 text-xs"></i> Back to Addons
                </button>
                <a id="modal-purchase-link" href="#"
                   class="flex-1 px-5 py-2.5 bg-[#ff0808] hover:bg-[#dd0606] text-white rounded-lg font-bold text-xs transition-all shadow-lg hover:shadow-xl text-center">
                    <i class="fas fa-shopping-cart mr-1 text-xs"></i> Purchase This Addon
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ─── Inline blade components for the addon rows ─────────────────────────── --}}
{{--
    Instead of separate partial files, we render each row right here via a macro-style
    @php block so the file stays self-contained.
--}}

<style>
    .location-tab { position: relative; }
    .location-tab.active { color: #ff0808; border-bottom-color: #ff0808; background-color: #fff5f5; }
    .location-content { animation: slideUpFade 0.3s ease-out; }
    @keyframes slideUpFade { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
    .location-content.hidden { display: none; }
    .animate-modal { animation: modalSlideDown 0.3s ease-out; }
    @keyframes modalSlideDown { 0% { opacity:0; transform:translateY(-30px); } 100% { opacity:1; transform:translateY(0); } }
    #addonDetailsModal { backdrop-filter: blur(2px); }
    #selectedAddonsSummary { animation: slideDown 0.3s ease-out; }
    @keyframes slideDown { from { opacity:0; transform:translateY(-20px); } to { opacity:1; transform:translateY(0); } }
    .flex.overflow-x-auto::-webkit-scrollbar { height: 4px; }
    ::-webkit-scrollbar { width:8px; height:8px; }
    ::-webkit-scrollbar-track { background:#f1f1f1; border-radius:4px; }
    ::-webkit-scrollbar-thumb { background:#cbd5e0; border-radius:4px; }
    ::-webkit-scrollbar-thumb:hover { background:#a0aec0; }
</style>

<script>
    let selectedAddons = [];

    // ── Tab switching ────────────────────────────────────────────────────────
    function switchLocationTab(location) {
        document.querySelectorAll('.location-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.location-tab').forEach(el => el.classList.remove('active'));

        const content = document.getElementById('content-' + location);
        if (content) content.classList.remove('hidden');

        const tab = document.getElementById('tab-' + location);
        if (tab) {
            tab.classList.add('active');
            tab.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
        }

        localStorage.setItem('activeLocationTab', location);
    }

    // ── Cart logic ───────────────────────────────────────────────────────────
    function updateSelectedAddons() {
        selectedAddons = [];
        document.querySelectorAll('.addon-checkbox:checked').forEach(cb => {
            // Avoid duplicates when same addon appears in multiple tabs
            if (!selectedAddons.find(a => a.id === cb.dataset.addonId)) {
                selectedAddons.push({
                    id:       cb.dataset.addonId,
                    name:     cb.dataset.addonName,
                    price:    parseFloat(cb.dataset.addonPrice),
                    location: cb.dataset.addonLocation,
                    position: cb.dataset.addonPosition,
                });
            }
        });
        updateSummaryDisplay();
    }

    function updateSummaryDisplay() {
        const summary  = document.getElementById('selectedAddonsSummary');
        const countEl  = document.getElementById('selectedCount');
        const listEl   = document.getElementById('selectedAddonsList');
        const totalEl  = document.getElementById('totalPrice');

        if (selectedAddons.length === 0) { summary.classList.add('hidden'); return; }
        summary.classList.remove('hidden');
        countEl.textContent = selectedAddons.length;
        listEl.innerHTML = selectedAddons.map(a => `
            <div class="flex items-center justify-between p-2 bg-gray-50 rounded border border-gray-200">
                <div class="flex-1 min-w-0">
                    <div class="text-xs font-semibold text-gray-900 truncate">${a.location}</div>
                    <div class="text-xs text-gray-600">${a.position}</div>
                </div>
                <div class="text-sm font-bold text-gray-900 ml-2">$${a.price.toFixed(0)}</div>
            </div>`).join('');
        totalEl.textContent = '$' + selectedAddons.reduce((s, a) => s + a.price, 0).toFixed(2);
    }

    function clearSelectedAddons() {
        document.querySelectorAll('.addon-checkbox').forEach(cb => cb.checked = false);
        updateSelectedAddons();
    }

    function proceedToCheckout() {
        if (!selectedAddons.length) { alert('Please select at least one addon to purchase.'); return; }
        const params = new URLSearchParams();
        selectedAddons.forEach(a => params.append('addon_ids[]', a.id));
        window.location.href = '{{ route("vendor.addons.create") }}?' + params.toString();
    }

    // ── Modal ────────────────────────────────────────────────────────────────
    // Build a JS icon map mirroring the PHP map
    const locationIconMap = {
        homepage:    'fa-home',
        products:    'fa-boxes',
        suppliers:   'fa-store',
        marketplace: 'fa-shopping-bag',
        category:    'fa-list',
        search:      'fa-search',
        blog:        'fa-newspaper',
        sidebar:     'fa-columns',
        banner:      'fa-image',
    };

    function showAddonDetails(addon) {
        const key  = (addon.locationX || '').toLowerCase();
        const icon = locationIconMap[key] || 'fa-tag';

        document.getElementById('modal-header').className =
            'relative bg-[#ff0808] px-6 py-5 rounded-t-lg text-white';
        document.getElementById('modal-icon').className          = `fas ${icon} text-3xl`;
        document.getElementById('modal-addon-name').textContent  = addon.locationX;
        document.getElementById('modal-addon-position').textContent =
            (addon.locationY || '').replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        document.getElementById('modal-addon-price').textContent = '$' + parseFloat(addon.price).toFixed(0);
        document.getElementById('modal-location').textContent    = addon.locationX;
        document.getElementById('modal-country').textContent     = addon.country ? addon.country.name : 'Available Globally';

        const base = parseFloat(addon.price);
        document.getElementById('modal-price-30').textContent  = '$' + base.toFixed(0);
        document.getElementById('modal-price-60').textContent  = '$' + (base * 2 * 0.95).toFixed(0);
        document.getElementById('modal-price-90').textContent  = '$' + (base * 3 * 0.90).toFixed(0);
        document.getElementById('modal-price-180').textContent = '$' + (base * 6 * 0.85).toFixed(0);
        document.getElementById('modal-purchase-link').href    = `/vendor/addons/create?addon_id=${addon.id}`;

        document.getElementById('addonDetailsModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeAddonDetailsModal() {
        document.getElementById('addonDetailsModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    document.getElementById('addonDetailsModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeAddonDetailsModal();
    });

    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAddonDetailsModal(); });

    // ── Init ─────────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        // Build the list of available tab slugs from the server-rendered tabs
        const allTabIds = Array.from(document.querySelectorAll('.location-tab'))
            .map(t => t.id.replace('tab-', ''));

        const saved  = localStorage.getItem('activeLocationTab') || 'all';
        // Fall back to 'all' if the saved tab no longer exists
        const target = allTabIds.includes(saved) ? saved : 'all';
        switchLocationTab(target);

        updateSelectedAddons();
    });

    function filterAddons(query) {
    const clearBtn = document.getElementById('clearSearch');
    const countEl  = document.getElementById('searchResultCount');
    clearBtn.classList.toggle('hidden', query.trim() === '');

    // Search within whichever tab content is currently visible
    const activeContent = document.querySelector('.location-content:not(.hidden)');
    if (!activeContent) return;

    const rows = activeContent.querySelectorAll('[data-searchable]');
    let visible = 0;

    rows.forEach(row => {
        const text = row.dataset.searchable.toLowerCase();
        const match = text.includes(query.toLowerCase().trim());
        row.style.display = match ? '' : 'none';
        if (match) visible++;
    });

    if (query.trim()) {
        countEl.textContent = `${visible} result${visible !== 1 ? 's' : ''} found`;
        countEl.classList.remove('hidden');
    } else {
        countEl.classList.add('hidden');
    }
}

// Reset search when switching tabs
const originalSwitch = switchLocationTab;
window.switchLocationTab = function(location) {
    originalSwitch(location);
    const input = document.getElementById('addonSearch');
    if (input) { input.value = ''; filterAddons(''); }
};
</script>

@endsection

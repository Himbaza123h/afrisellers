{{-- Browse by Region Section - Compact Size with Interactive Map --}}

@php
    // UISection settings for browse_by_regions
    $browseSection    = $uiSection ?? \App\Models\UISection::where('section_key', 'browse_by_regions')->first();
    $browseAnimation  = $browseSection?->getAnimationMode() ?? 'slide'; // slide | fade | flip | none
    $browseItemCount  = $browseSection?->number_items ?? 8;

    $supplierColors = [
        'bg-red-100', 'bg-orange-100', 'bg-green-100', 'bg-yellow-100',
        'bg-gray-100', 'bg-emerald-100', 'bg-blue-100', 'bg-purple-100',
    ];

    // Paid-addon suppliers shown in the ticker (left column)
    $browseBySuppliers = App\Models\BusinessProfile::where('verification_status', 'verified')
        ->where('is_admin_verified', true)
        ->whereHas('addonUsers', function ($query) {
            $query->where('type', 'supplier')
                ->whereNotNull('paid_at')
                ->where(function ($q) {
                    $q->whereNull('ended_at')->orWhere('ended_at', '>', now());
                })
                ->whereHas('addon', function ($addonQuery) {
                    $addonQuery->where('locationX', 'Homepage')->where('locationY', 'BrowseBY');
                });
        })
        ->with(['country', 'user'])
        ->limit($browseItemCount)
        ->get();

    // ALL verified suppliers — used for ticker data & region filtering
    $allVerifiedSuppliers = App\Models\BusinessProfile::where('verification_status', 'verified')
        ->where('is_admin_verified', true)
        ->with(['country'])
        ->get();

    $allSuppliersData  = [];
    $suppliersByRegion = [];

    foreach ($allVerifiedSuppliers as $index => $supplier) {
        $regionId = $supplier->country?->region_id;

        $entry = [
            'id'              => $supplier->id,
            'business_name'   => $supplier->business_name,
            'logo'            => $supplier->logo,
            'is_verified_pro' => $supplier->is_verified_pro,
            'url'             => route('business-profile.show', $supplier->id),
            'color'           => $supplierColors[$index % count($supplierColors)],
            'region_id'       => $regionId,
        ];

        $allSuppliersData[] = $entry;

        if ($regionId) {
            $suppliersByRegion[$regionId][] = $entry;
        }
    }

    // Ticker source: prefer paid-addon list, fallback to all verified suppliers
    $tickerSuppliers = $browseBySuppliers->isNotEmpty()
        ? $browseBySuppliers->map(fn($s, $i) => [
            'id'              => $s->id,
            'business_name'   => $s->business_name,
            'logo'            => $s->logo,
            'is_verified_pro' => $s->is_verified_pro,
            'url'             => route('business-profile.show', $s->id),
            'color'           => $supplierColors[$i % count($supplierColors)],
            'region_id'       => $s->country?->region_id,
        ])->values()->all()
        : $allSuppliersData;

    // Determine wrapper height (at least 4 rows visible)
    $tickerRows     = max(min(count($tickerSuppliers), 6), 4);
    $tickerHeightPx = $tickerRows * 54;

    // Regions (needed for pills — load once here)
    $activeRegions = $regions ?? \App\Models\Region::active()->withCount('countries')->orderBy('name', 'asc')->get();
@endphp

<section class="py-6 md:py-8 bg-gray-50">
    <div class="container px-4 mx-auto">

        <!-- Section Title -->
        <div class="flex items-center mb-3 gap-3">
            <h2 class="text-lg md:text-xl lg:text-2xl font-bold text-gray-900 whitespace-nowrap">
                {{ __('messages.browse_by_region') ?? 'Browse by Region' }}
            </h2>
            <div class="flex-1 h-px bg-gray-300"></div>
        </div>

        <!-- ══ Region Filter Pills (below title, above everything) ══ -->
        <div class="flex flex-wrap gap-1.5 mb-4 md:mb-5">
            <button class="region-pill active text-[10px] px-3 py-1 rounded-full font-semibold bg-blue-600 text-white shadow border border-blue-600"
                    data-region-id="" data-region-name="{{ __('messages.all_countries') ?? 'All Countries' }}">
                {{ __('messages.all') ?? 'All' }}
            </button>
            @foreach ($activeRegions as $region)
                @php $countriesCount = $region->countries()->where('status', 'active')->count(); @endphp
                @if ($countriesCount > 0)
                    <button class="region-pill text-[10px] px-3 py-1 rounded-full font-semibold bg-white border border-gray-300 text-gray-700 shadow-sm"
                            data-region-id="{{ $region->id }}"
                            data-region-slug="{{ Str::slug($region->name) }}"
                            data-region-name="{{ $region->name }}">
                        {{ $region->name }}
                    </button>
                @endif
            @endforeach
        </div>

        <!-- Main Grid: Left 5col | Right 7col -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 md:gap-6">

            <!-- ══ LEFT: Ticker + Map ══ -->
            <div class="relative order-2 lg:order-1 lg:col-span-5">

                <!-- Animated Supplier Ticker -->
                <div class="suppliers-ticker-wrapper relative overflow-hidden rounded-lg bg-gray-50"
                     id="supplierTickerWrapper"
                     style="height: {{ $tickerHeightPx }}px; max-height: 330px;"
                     data-animation="{{ $browseAnimation }}"
                     data-count="{{ count($tickerSuppliers) }}">

                    <div class="suppliers-ticker" id="suppliersTicker">
                        {{-- Render twice for seamless loop --}}
                        @foreach ([1, 2] as $pass)
                            <div class="suppliers-set" data-set="{{ $pass }}">
                                @foreach ($tickerSuppliers as $index => $supplier)
                                    <div class="supplier-item pb-1">
                                        <a href="{{ $supplier['url'] }}"
                                           class="flex items-center gap-2 px-2.5 py-2 text-[10px] md:text-xs text-gray-700 hover:bg-white rounded transition-colors">
                                            <span class="w-6 h-6 {{ $supplier['color'] }} rounded flex items-center justify-center flex-shrink-0 overflow-hidden">
                                                @if ($supplier['logo'])
                                                    <img src="{{ $supplier['logo'] }}" alt="{{ $supplier['business_name'] }}" class="w-full h-full object-cover">
                                                @else
                                                    <span class="text-xs">🏢</span>
                                                @endif
                                            </span>
                                            <span class="font-medium truncate flex-1">{{ Str::limit($supplier['business_name'], 28) }}</span>
                                            @if ($supplier['is_verified_pro'])
                                                <span class="text-[8px] text-blue-600 font-bold flex-shrink-0">✓ PRO</span>
                                            @endif
                                        </a>
                                        <div class="border-b border-gray-200 w-1/2 ml-9"></div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>

                    {{-- Fade masks --}}
                    <div class="absolute top-0 left-0 right-0 h-6 bg-gradient-to-b from-gray-50 to-transparent z-10 pointer-events-none"></div>
                    <div class="absolute bottom-0 left-0 right-0 h-6 bg-gradient-to-t from-gray-50 to-transparent z-10 pointer-events-none"></div>
                </div>

                <!-- Map Overlay (desktop only) -->
                <div class="hidden lg:block absolute top-0 left-1/2 bottom-0 w-full max-w-xs pointer-events-none"
                     style="z-index: 5;">
                    <div id="mapCenter" class="absolute inset-0 cursor-pointer pointer-events-auto" title="Click to show all regions">
                        <img src="{{ asset('africaimage.png') }}" alt="Africa Map"
                             class="w-full h-full object-contain opacity-25"
                             style="max-width: 90%; max-height: 90%; margin: auto; display: block;">
                    </div>

                    @php
                        $regionPositions = [
                            'West Africa'     => ['top' => '25%',  'left'  => '12%',  'color' => 'teal'],
                            'East Africa'     => ['top' => '30%',  'right' => '18%',  'color' => 'orange'],
                            'Central Africa'  => ['top' => '45%',  'left'  => '40%',  'color' => 'blue'],
                            'Southern Africa' => ['bottom' => '15%','left'  => '30%',  'color' => 'green'],
                            'North Africa'    => ['top' => '8%',   'left'  => '28%',  'color' => 'yellow'],
                            'Region Diaspora' => ['bottom' => '4%', 'right' => '8%',  'color' => 'purple'],
                        ];
                    @endphp

                    <div class="pointer-events-auto">
                        @foreach ($activeRegions as $region)
                            @php
                                $pos      = $regionPositions[$region->name] ?? ['top' => '50%', 'left' => '50%', 'color' => 'gray'];
                                $color    = $pos['color'];
                                $posStyle = '';
                                if (isset($pos['top']))    $posStyle .= "top:{$pos['top']};";
                                if (isset($pos['bottom'])) $posStyle .= "bottom:{$pos['bottom']};";
                                if (isset($pos['left']))   $posStyle .= "left:{$pos['left']};";
                                if (isset($pos['right']))  $posStyle .= "right:{$pos['right']};";
                                $regionSlug     = Str::slug($region->name);
                                $countriesCount = $region->countries()->where('status', 'active')->count();
                            @endphp
                            @if ($countriesCount > 0)
                                <div class="absolute cursor-pointer region-label" style="{{ $posStyle }} z-index:10;"
                                     data-region-id="{{ $region->id }}"
                                     data-region-slug="{{ $regionSlug }}"
                                     data-region-name="{{ $region->name }}">
                                    <div class="bg-{{ $color }}-500 text-white text-[9px] px-2 py-0.5 rounded-full font-bold shadow-lg hover:bg-{{ $color }}-600 transition-all whitespace-nowrap select-none">
                                        {{ $region->name }}
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

            </div>
            {{-- end LEFT --}}

            <!-- ══ RIGHT: Country Cards ══ -->
            <div class="relative z-10 order-1 lg:order-2 lg:col-span-7">
                <div class="mb-3 md:mb-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                    <h3 class="text-sm md:text-base lg:text-lg font-bold text-gray-900">
                        <span id="selected-region-name">{{ __('messages.all_countries') ?? 'All Countries' }}</span>
                    </h3>
                    <a href="{{ route('regions.index') }}"
                       class="text-blue-600 hover:text-blue-700 font-semibold text-[10px] md:text-xs flex items-center gap-1">
                        {{ __('messages.view_all_regions') ?? 'View All Regions' }}
                        <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                <!-- Country Cards Grid -->
                @php
                    $allCountriesData    = [];
                    $regionCountriesData = (object)[];

                    $regionsWithCountries = \App\Models\Region::active()
                        ->with(['countries' => fn($q) => $q->where('status', 'active')->orderBy('name', 'asc')])
                        ->orderBy('name', 'asc')
                        ->get();

                    foreach ($regionsWithCountries as $region) {
                        $regionSlug      = Str::slug($region->name);
                        $regionCountries = [];

                        foreach ($region->countries as $country) {
                            $countryData = [
                                'id'              => $country->id,
                                'name'            => $country->name,
                                'image'           => $country->image,
                                'flag_url'        => $country->flag_url,
                                'suppliers_count' => $country->suppliers_count ?? $country->getVendorsCount(),
                                'url'             => route('featured-suppliers', ['country' => $country->id]),
                                'region_id'       => $region->id,
                                'region_name'     => $region->name,
                            ];
                            $regionCountries[]  = $countryData;
                            $allCountriesData[] = $countryData;
                        }

                        $regionCountriesData->{$regionSlug} = $regionCountries;
                        $regionCountriesData->{$region->id} = $regionCountries;
                    }

                    $displayCountries = collect($allCountriesData)->take(8);
                @endphp

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 md:gap-3" id="countriesGrid">
                    @forelse ($displayCountries as $country)
                        <a href="{{ $country['url'] }}"
                           class="country-card block bg-white hover:shadow-lg rounded-lg transition-all group border border-gray-200 hover:border-blue-400"
                           data-country-id="{{ $country['id'] }}"
                           data-region-id="{{ $country['region_id'] ?? '' }}">
                            <div class="relative h-16 md:h-20 overflow-hidden rounded-t-lg">
                                @if ($country['image'])
                                    <img src="{{ $country['image'] }}" alt="{{ $country['name'] }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                                @else
                                    <div class="flex justify-center items-center w-full h-full bg-blue-50">
                                        <span class="text-3xl">🌍</span>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-black/20"></div>
                            </div>
                            <div class="p-2 md:p-2.5">
                                <div class="flex items-center gap-1 mb-1">
                                    @if ($country['flag_url'])
                                        <img src="{{ $country['flag_url'] }}" alt="{{ $country['name'] }}"
                                             class="w-4 h-3 md:w-5 md:h-3.5 object-cover rounded-sm shadow-sm flex-shrink-0">
                                    @endif
                                    <h4 class="text-[10px] md:text-xs font-bold text-gray-900 group-hover:text-blue-600 truncate">
                                        {{ $country['name'] }}
                                    </h4>
                                </div>
                                <div class="mb-0.5">
                                    <span class="text-base md:text-lg font-bold text-gray-900">{{ $country['suppliers_count'] }}</span>
                                </div>
                                <p class="text-[9px] md:text-[10px] text-gray-500">
                                    {{ __('messages.products_sell_requests') ?? 'Products / Sell Requests' }}
                                </p>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full py-8 text-center">
                            <div class="text-gray-400 text-4xl mb-2">🌍</div>
                            <p class="text-gray-500 text-sm">{{ __('messages.no_countries_available') ?? 'No countries available' }}</p>
                        </div>
                    @endforelse
                </div>

                <div id="loadingState" class="hidden text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-500 border-t-transparent"></div>
                    <p class="mt-2 text-gray-500 text-sm">Loading...</p>
                </div>
                <div id="emptyState" class="hidden text-center py-8">
                    <div class="text-gray-400 text-4xl mb-2">🌍</div>
                    <p class="text-gray-500 text-sm">No countries found for this region.</p>
                </div>
            </div>
            {{-- end RIGHT --}}

        </div>
    </div>
</section>

@push('scripts')
<script>
(function () {
    'use strict';

    // ══════════════════════════════════════════════════════════════
    // DATA (injected from PHP)
    // ══════════════════════════════════════════════════════════════
    const allCountries      = {!! json_encode($allCountriesData ?? []) !!};
    const regionCountries   = {!! json_encode($regionCountriesData ?? new stdClass()) !!};
    const allSuppliers      = {!! json_encode($allSuppliersData ?? []) !!};
    const suppliersByRegion = {!! json_encode($suppliersByRegion ?? new stdClass()) !!};
    const tickerSuppliers   = {!! json_encode($tickerSuppliers ?? []) !!};

    // ══════════════════════════════════════════════════════════════
    // TICKER ANIMATION
    // ══════════════════════════════════════════════════════════════
    const wrapper  = document.getElementById('supplierTickerWrapper');
    const ticker   = document.getElementById('suppliersTicker');
    const animMode = wrapper ? wrapper.getAttribute('data-animation') || 'slide' : 'slide';

    let tickerPaused = false;
    let tickerReset  = () => {};

    if (wrapper && ticker) {
        wrapper.addEventListener('mouseenter', () => tickerPaused = true);
        wrapper.addEventListener('mouseleave', () => tickerPaused = false);

        if (animMode === 'slide')      initSlide();
        else if (animMode === 'fade')  initFade();
        else if (animMode === 'flip')  initFlip();
        // 'none' → static
    }

    function initSlide() {
        const PX_PER_SEC = 40;
        ticker.style.display       = 'flex';
        ticker.style.flexDirection = 'column';
        ticker.style.willChange    = 'transform';

        let posY = 0, setH = 0, lastTs = null;

        tickerReset = function () {
            posY = 0; setH = 0; lastTs = null;
            ticker.style.transform = 'translateY(0)';
        };

        function measureSetHeight() {
            const first = ticker.querySelector('.suppliers-set');
            return first ? first.offsetHeight : 300;
        }

        function tick(ts) {
            if (!lastTs) lastTs = ts;
            const delta = ts - lastTs;
            lastTs = ts;
            if (!tickerPaused) {
                if (!setH) setH = measureSetHeight();
                posY += (PX_PER_SEC * delta) / 1000;
                if (posY >= setH) posY -= setH;
                ticker.style.transform = `translateY(-${posY}px)`;
            }
            requestAnimationFrame(tick);
        }
        requestAnimationFrame(tick);
    }

    function initFade() {
        const sets = ticker.querySelectorAll('.suppliers-set');
        if (sets[1]) sets[1].remove();

        const items = Array.from(ticker.querySelectorAll('.supplier-item'));
        if (!items.length) return;

        const vis = Math.min(5, items.length);
        let start = 0;

        items.forEach((item, i) => {
            item.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            item.style.opacity    = i < vis ? '1' : '0';
            item.style.transform  = i < vis ? 'translateY(0)' : 'translateY(10px)';
        });

        function fadeNext() {
            for (let i = start; i < start + vis && i < items.length; i++) {
                items[i].style.opacity   = '0';
                items[i].style.transform = 'translateY(-10px)';
            }
            setTimeout(() => {
                start = (start + vis) % items.length;
                for (let i = start; i < start + vis && i < items.length; i++) {
                    items[i].style.transition = 'none';
                    items[i].style.opacity    = '0';
                    items[i].style.transform  = 'translateY(20px)';
                }
                requestAnimationFrame(() => requestAnimationFrame(() => {
                    for (let i = start; i < start + vis && i < items.length; i++) {
                        items[i].style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                        items[i].style.opacity    = '1';
                        items[i].style.transform  = 'translateY(0)';
                    }
                }));
            }, 500);
        }

        tickerReset = () => { start = 0; };
        setInterval(() => { if (!tickerPaused) fadeNext(); }, 3000);
    }

    function initFlip() {
        const sets = ticker.querySelectorAll('.suppliers-set');
        if (sets[1]) sets[1].remove();

        const items = Array.from(ticker.querySelectorAll('.supplier-item'));
        if (!items.length) return;

        const vis = Math.min(5, items.length);
        let start = 0;

        items.forEach((item, i) => {
            item.style.transition      = 'transform 0.4s ease, opacity 0.4s ease';
            item.style.transformOrigin = 'top center';
            item.style.opacity         = i < vis ? '1' : '0';
            item.style.transform       = i < vis ? 'rotateX(0deg)' : 'rotateX(-90deg)';
        });

        function flipNext() {
            for (let i = start; i < start + vis && i < items.length; i++) {
                items[i].style.transform = 'rotateX(90deg)';
                items[i].style.opacity   = '0';
            }
            setTimeout(() => {
                start = (start + vis) % items.length;
                for (let i = start; i < start + vis && i < items.length; i++) {
                    items[i].style.transition = 'none';
                    items[i].style.transform  = 'rotateX(-90deg)';
                    items[i].style.opacity    = '0';
                }
                requestAnimationFrame(() => requestAnimationFrame(() => {
                    for (let i = start; i < start + vis && i < items.length; i++) {
                        items[i].style.transition = 'transform 0.4s ease, opacity 0.4s ease';
                        items[i].style.transform  = 'rotateX(0deg)';
                        items[i].style.opacity    = '1';
                    }
                }));
            }, 400);
        }

        tickerReset = () => { start = 0; };
        setInterval(() => { if (!tickerPaused) flipNext(); }, 3500);
    }

    // ══════════════════════════════════════════════════════════════
    // TICKER DOM UPDATE
    // ══════════════════════════════════════════════════════════════
    function renderSupplierHTML(suppliers) {
        return suppliers.map(s => `
            <div class="supplier-item pb-1">
                <a href="${s.url}" class="flex items-center gap-2 px-2.5 py-2 text-[10px] md:text-xs text-gray-700 hover:bg-white rounded transition-colors">
                    <span class="w-6 h-6 ${s.color} rounded flex items-center justify-center flex-shrink-0 overflow-hidden">
                        ${s.logo
                            ? `<img src="${s.logo}" alt="" class="w-full h-full object-cover">`
                            : `<span class="text-xs">🏢</span>`}
                    </span>
                    <span class="font-medium truncate flex-1">${s.business_name.substring(0, 28)}</span>
                    ${s.is_verified_pro ? `<span class="text-[8px] text-blue-600 font-bold flex-shrink-0">✓ PRO</span>` : ''}
                </a>
                <div class="border-b border-gray-200 w-1/2 ml-9"></div>
            </div>`
        ).join('');
    }

    function updateTicker(suppliers) {
        if (!ticker || !wrapper) return;
        const list = (suppliers && suppliers.length) ? suppliers : tickerSuppliers;
        const html = renderSupplierHTML(list);
        ticker.querySelectorAll('.suppliers-set').forEach(set => { set.innerHTML = html; });
        const rows = Math.max(Math.min(list.length, 6), 4);
        wrapper.style.height = (rows * 54) + 'px';
        ticker.style.transform = 'translateY(0)';
        tickerReset();
    }

    // ══════════════════════════════════════════════════════════════
    // COUNTRY GRID
    // ══════════════════════════════════════════════════════════════
    const countriesGrid     = document.getElementById('countriesGrid');
    const loadingState      = document.getElementById('loadingState');
    const emptyState        = document.getElementById('emptyState');
    const regionNameDisplay = document.getElementById('selected-region-name');

    function createCountryCard(c) {
        const img = c.image
            ? `<img src="${c.image}" alt="${c.name}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">`
            : `<div class="flex justify-center items-center w-full h-full bg-blue-50"><span class="text-3xl">🌍</span></div>`;
        const flag = c.flag_url
            ? `<img src="${c.flag_url}" alt="${c.name}" class="w-4 h-3 md:w-5 md:h-3.5 object-cover rounded-sm shadow-sm flex-shrink-0">`
            : '';
        return `
            <a href="${c.url}"
               class="country-card block bg-white hover:shadow-lg rounded-lg transition-all group border border-gray-200 hover:border-blue-400"
               data-country-id="${c.id}" data-region-id="${c.region_id || ''}">
                <div class="relative h-16 md:h-20 overflow-hidden rounded-t-lg">
                    ${img}
                    <div class="absolute inset-0 bg-black/20"></div>
                </div>
                <div class="p-2 md:p-2.5">
                    <div class="flex items-center gap-1 mb-1">
                        ${flag}
                        <h4 class="text-[10px] md:text-xs font-bold text-gray-900 group-hover:text-blue-600 truncate">${c.name}</h4>
                    </div>
                    <div class="mb-0.5"><span class="text-base md:text-lg font-bold text-gray-900">${c.suppliers_count || 0}</span></div>
                    <p class="text-[9px] md:text-[10px] text-gray-500">{{ __('messages.products_sell_requests') ?? 'Products / Sell Requests' }}</p>
                </div>
            </a>`;
    }

    function showCountries(countries, regionName) {
        if (!countriesGrid) return;
        countriesGrid.classList.add('hidden');
        if (emptyState)   emptyState.classList.add('hidden');
        if (loadingState) loadingState.classList.remove('hidden');

        setTimeout(() => {
            if (loadingState) loadingState.classList.add('hidden');
            if (countries && countries.length > 0) {
                countriesGrid.innerHTML = countries.slice(0, 8).map(createCountryCard).join('');
                countriesGrid.classList.remove('hidden');
                if (regionNameDisplay) regionNameDisplay.textContent = regionName || '{{ __("messages.all_countries") ?? "All Countries" }}';
            } else {
                if (emptyState) emptyState.classList.remove('hidden');
            }
        }, 250);
    }

    // ══════════════════════════════════════════════════════════════
    // REGION SELECTION (shared logic)
    // ══════════════════════════════════════════════════════════════
    function selectRegion(regionId, regionSlug, regionName) {
        if (!regionId) {
            showCountries(allCountries, '{{ __("messages.all_countries") ?? "All Countries" }}');
            updateTicker(tickerSuppliers);
        } else {
            const countries = regionCountries[regionId] || regionCountries[regionSlug] || [];
            showCountries(countries, regionName);
            updateTicker(suppliersByRegion[regionId] || []);
        }
    }

    // ── Region pills (title area — all screen sizes) ──
    document.querySelectorAll('.region-pill').forEach(pill => {
        pill.addEventListener('click', function () {
            document.querySelectorAll('.region-pill').forEach(p => {
                p.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
                p.classList.add('bg-white', 'border-gray-300', 'text-gray-700');
            });
            this.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
            this.classList.remove('bg-white', 'border-gray-300', 'text-gray-700');

            // Sync map highlight if desktop
            resetMapHighlights();
            const matchingLabel = document.querySelector(`.region-label[data-region-id="${this.dataset.regionId}"]`);
            if (matchingLabel) matchingLabel.querySelector('div').classList.add('ring-2', 'ring-white', 'scale-110');

            selectRegion(
                this.dataset.regionId || null,
                this.dataset.regionSlug || null,
                this.dataset.regionName
            );
        });
    });

    // ── Desktop: Map region labels ──
    function resetMapHighlights() {
        document.querySelectorAll('.region-label div').forEach(d =>
            d.classList.remove('ring-2', 'ring-white', 'scale-110'));
    }

    document.querySelectorAll('.region-label').forEach(label => {
        label.addEventListener('click', function (e) {
            e.stopPropagation();
            resetMapHighlights();
            this.querySelector('div').classList.add('ring-2', 'ring-white', 'scale-110');

            // Sync pill highlight
            document.querySelectorAll('.region-pill').forEach(p => {
                p.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
                p.classList.add('bg-white', 'border-gray-300', 'text-gray-700');
            });
            const matchingPill = document.querySelector(`.region-pill[data-region-id="${this.dataset.regionId}"]`);
            if (matchingPill) {
                matchingPill.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
                matchingPill.classList.remove('bg-white', 'border-gray-300', 'text-gray-700');
            }

            selectRegion(this.dataset.regionId, this.dataset.regionSlug, this.dataset.regionName);
        });
        label.addEventListener('mouseenter', function () { this.querySelector('div').classList.add('scale-110'); });
        label.addEventListener('mouseleave', function () {
            if (!this.querySelector('div').classList.contains('ring-2'))
                this.querySelector('div').classList.remove('scale-110');
        });
    });

    const mapCenter = document.getElementById('mapCenter');
    if (mapCenter) {
        mapCenter.addEventListener('click', function (e) {
            if (e.target === this || e.target.tagName === 'IMG') {
                resetMapHighlights();
                // Reset pills to "All"
                document.querySelectorAll('.region-pill').forEach(p => {
                    p.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
                    p.classList.add('bg-white', 'border-gray-300', 'text-gray-700');
                });
                const allPill = document.querySelector('.region-pill[data-region-id=""]');
                if (allPill) {
                    allPill.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
                    allPill.classList.remove('bg-white', 'border-gray-300', 'text-gray-700');
                }
                selectRegion(null, null, null);
            }
        });
    }

})();
</script>
@endpush

<style>
/* ── Wrapper ─────────────────────────────────────────── */
.suppliers-ticker-wrapper {
    overflow: hidden;
    position: relative;
    transition: height 0.3s ease;
}

/* ── Ticker items ────────────────────────────────────── */
.suppliers-ticker { will-change: transform; }
.supplier-item a  { display: flex; align-items: center; }

/* ── Region labels ───────────────────────────────────── */
.region-label div {
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    cursor: pointer;
}
.region-label:hover div { transform: scale(1.12); }

/* ── Map center ──────────────────────────────────────── */
#mapCenter { transition: opacity 0.3s ease; }
#mapCenter:hover { opacity: 0.9; }

/* ── Country cards ───────────────────────────────────── */
.country-card { animation: fadeInCard 0.3s ease-in-out both; }
@keyframes fadeInCard {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── Country grid responsive cols ───────────────────── */
@media (max-width: 639px)                         { #countriesGrid { grid-template-columns: repeat(2, 1fr); } }
@media (min-width: 640px) and (max-width: 767px)  { #countriesGrid { grid-template-columns: repeat(3, 1fr); } }
@media (min-width: 768px) and (max-width: 1023px) { #countriesGrid { grid-template-columns: repeat(4, 1fr); } }
@media (min-width: 1024px)                        { #countriesGrid { grid-template-columns: repeat(4, 1fr); } }

/* ── Region pills ────────────────────────────────────── */
.region-pill {
    transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
    cursor: pointer;
    border: 1px solid;
}
</style>
{{-- End Browse by Region Section --}}

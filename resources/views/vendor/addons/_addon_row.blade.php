{{--
    Variables expected:
        $addon      – Addon model instance
        $isOwned    – bool
        $style      – ['bg' => 'bg-xxx-500', 'icon' => 'fa-xxx', 'idx' => int]
        $badgeBg    – 'bg-xxx-100'
        $badgeText  – 'text-xxx-800'
--}}
<div class="border-b border-gray-200 last:border-b-0 hover:bg-gray-50 transition-colors {{ $isOwned ? 'bg-green-50 bg-opacity-30' : '' }}"
     data-searchable="{{ strtolower($addon->locationX . ' ' . $addon->locationY . ' ' . ($addon->country?->name ?? 'global')) }}">
    <div class="p-5">
        <div class="flex items-start gap-4">

            <!-- Checkbox / owned check -->
            @if(!$isOwned)
                <div class="flex items-center pt-1">
                    <input type="checkbox"
                           id="addon-check-{{ $addon->id }}"
                           class="addon-checkbox w-5 h-5 text-[#ff0808] border-gray-300 rounded focus:ring-[#ff0808] cursor-pointer"
                           data-addon-id="{{ $addon->id }}"
                           data-addon-name="{{ $addon->locationX }} - {{ ucfirst(str_replace('_', ' ', $addon->locationY)) }}"
                           data-addon-price="{{ $addon->price }}"
                           data-addon-location="{{ $addon->locationX }}"
                           data-addon-position="{{ ucfirst(str_replace('_', ' ', $addon->locationY)) }}"
                           onchange="updateSelectedAddons()">
                </div>
            @else
                <div class="w-5 h-5 pt-1 flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            @endif

            <!-- Icon -->
            <div class="flex-shrink-0">
                <div class="w-12 h-12 {{ $style['bg'] }} rounded-lg flex items-center justify-center shadow-md">
                    <i class="fas {{ $style['icon'] }} text-white text-xl"></i>
                </div>
            </div>

            <!-- Details -->
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-4 mb-2">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="text-base font-bold text-gray-900">{{ $addon->locationX }}</h3>
                            @if($isOwned)
                                <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                    <i class="fas fa-check-circle mr-1"></i>Active
                                </span>
                            @else
                                <span class="px-2 py-0.5 {{ $badgeBg }} {{ $badgeText }} rounded-full text-xs font-medium">
                                    Available
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mb-2">{{ ucfirst(str_replace('_', ' ', $addon->locationY)) }} Position</p>
                        <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <i class="fas fa-map-marker-alt"></i>
                                {{ $addon->country ? $addon->country->name : 'Available Globally' }}
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="fas fa-calendar"></i>
                                30 days duration
                            </span>
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="text-right flex-shrink-0">
                        <div class="text-2xl font-bold text-gray-900">${{ number_format($addon->price, 0) }}</div>
                        <div class="text-xs text-gray-500">per 30 days</div>
                    </div>
                </div>

                <!-- Feature badges -->
                <div class="flex flex-wrap gap-2 mb-3">
                    @foreach(['Prime location visibility', 'Increased click-through rate', 'Flexible duration options'] as $feat)
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 rounded text-xs text-gray-700">
                            <i class="fas fa-check text-green-600" style="font-size:10px;"></i>
                            {{ $feat }}
                        </span>
                    @endforeach
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2">
                    <button onclick="showAddonDetails({{ json_encode($addon) }})"
                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-xs transition-all">
                        <i class="fas fa-info-circle"></i>
                        <span>View Details</span>
                    </button>

                    @if($isOwned)
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 text-gray-500 rounded-lg font-medium text-xs cursor-not-allowed">
                            <i class="fas fa-check-circle"></i> Already Active
                        </span>
                    @else
                        <a href="{{ route('vendor.addons.create', ['addon_id' => $addon->id]) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 {{ $style['bg'] }} text-white rounded-lg hover:shadow-lg font-medium text-xs transition-all">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Purchase Now</span>
                        </a>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

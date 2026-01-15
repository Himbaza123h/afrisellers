@extends('layouts.home')

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('vendor.product.show', $product) }}" class="p-2 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Set Product Pricing</h1>
                <p class="mt-1 text-sm text-gray-500">Configure pricing tiers for {{ $product->name }}</p>
            </div>
        </div>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <div class="flex-1">
                <p class="text-sm font-medium text-red-900 mb-2">Please fix the following errors:</p>
                <ul class="text-sm text-red-700 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <!-- Success Message -->
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <!-- Product Info Card -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200 shadow-sm p-6">
        <div class="flex items-start gap-4">
            @if($product->images && $product->images->where('is_primary', true)->first())
                <img src="{{ $product->images->where('is_primary', true)->first()->image_url }}"
                     alt="{{ $product->name }}"
                     class="w-20 h-20 object-cover rounded-lg border-2 border-white shadow-md">
            @else
                <div class="w-20 h-20 bg-gradient-to-br from-gray-200 to-gray-300 rounded-lg flex items-center justify-center border-2 border-white shadow-md">
                    <i class="fas fa-image text-2xl text-gray-500"></i>
                </div>
            @endif
            <div class="flex-1">
                <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $product->name }}</h3>
                <p class="text-sm text-gray-600 mb-2">{{ $product->short_description ?? 'No description available' }}</p>
                <div class="flex flex-wrap gap-2">
                    @if($product->productCategory)
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-white border border-blue-200 text-blue-700">
                            <i class="fas fa-tag mr-1"></i> {{ $product->productCategory->name }}
                        </span>
                    @endif
                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-white border border-blue-200 text-blue-700 capitalize">
                        <i class="fas fa-circle mr-1 text-[6px]"></i> {{ $product->status }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Pricing Form -->
    <form action="{{ route('vendor.product.price.update', $product) }}" method="POST" id="pricingForm">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-dollar-sign text-green-600"></i>
                        Price Tiers
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Set different prices based on quantity ranges. Leave max quantity empty for unlimited.</p>
                </div>
                <button type="button" onclick="addPriceTier()"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#ff0808] text-white text-sm rounded font-medium shadow-sm hover:shadow-md hover:bg-[#e60707] transition-all duration-200 active:scale-95">
                    <i class="fas fa-plus text-xs"></i>
                    <span>Add Price Tier</span>
                </button>
            </div>

            <!-- Currency Selection -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <label for="currency" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                    Currency <span class="text-red-600">*</span>
                </label>
                <select name="currency" id="currency" required
                    class="w-full md:w-64 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-gray-900">
                    <option value="USD" {{ old('currency', $prices->first()->currency ?? 'USD') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                    <option value="EUR" {{ old('currency', $prices->first()->currency ?? '') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                    <option value="GBP" {{ old('currency', $prices->first()->currency ?? '') == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                    <option value="RWF" {{ old('currency', $prices->first()->currency ?? '') == 'RWF' ? 'selected' : '' }}>RWF - Rwandan Franc</option>
                    <option value="KES" {{ old('currency', $prices->first()->currency ?? '') == 'KES' ? 'selected' : '' }}>KES - Kenyan Shilling</option>
                    <option value="UGX" {{ old('currency', $prices->first()->currency ?? '') == 'UGX' ? 'selected' : '' }}>UGX - Ugandan Shilling</option>
                    <option value="TZS" {{ old('currency', $prices->first()->currency ?? '') == 'TZS' ? 'selected' : '' }}>TZS - Tanzanian Shilling</option>
                </select>
                <p class="mt-2 text-xs text-gray-500">This currency will apply to all price</p>
            </div>

            <!-- Price Tiers Container -->
            <div id="priceTiersContainer" class="space-y-4">
                @if($prices && $prices->count() > 0)
                    @foreach($prices->sortBy('min_qty') as $index => $price)
                        <div class="p-5 bg-gradient-to-br from-green-50 to-white rounded-xl border border-green-100 price-tier-item">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
                                <div>
                                    <label class="block mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Min Quantity <span class="text-red-600">*</span>
                                    </label>
                                    <input type="number" name="prices[{{ $index }}][min_qty]" required min="1"
                                        class="px-3 py-2 w-full text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500"
                                        value="{{ old("prices.$index.min_qty", $price->min_qty) }}"
                                        placeholder="e.g., 1">
                                </div>
                                <div>
                                    <label class="block mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Max Quantity
                                    </label>
                                    <input type="number" name="prices[{{ $index }}][max_qty]" min="1"
                                        class="px-3 py-2 w-full text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500"
                                        value="{{ old("prices.$index.max_qty", $price->max_qty) }}"
                                        placeholder="Leave empty for unlimited">
                                </div>
                                <div>
                                    <label class="block mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Price <span class="text-red-600">*</span>
                                    </label>
                                    <input type="number" step="0.01" name="prices[{{ $index }}][price]" required min="0"
                                        class="px-3 py-2 w-full text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500"
                                        value="{{ old("prices.$index.price", $price->price) }}"
                                        placeholder="0.00">
                                </div>
                                <div>
                                    <label class="block mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Discount Amount
                                    </label>
                                    <input type="number" step="0.01" name="prices[{{ $index }}][discount]" min="0"
                                        class="px-3 py-2 w-full text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500"
                                        value="{{ old("prices.$index.discount", $price->discount ?? 0) }}"
                                        placeholder="0.00">
                                </div>
                                <div class="flex items-end">
                                    <button type="button" onclick="this.closest('.price-tier-item').remove(); updateTierNumbers();"
                                        class="px-3 py-2 w-full text-sm font-medium text-red-600 bg-white rounded-lg border border-red-300 transition-colors hover:bg-red-50">
                                        <i class="mr-1 fas fa-trash"></i>Remove
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="prices[{{ $index }}][id]" value="{{ $price->id }}">
                        </div>
                    @endforeach
                @else
                    <!-- Default price tier if none exist -->
                    <div class="p-5 bg-gradient-to-br from-green-50 to-white rounded-xl border border-green-100 price-tier-item">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
                            <div>
                                <label class="block mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Min Quantity <span class="text-red-600">*</span>
                                </label>
                                <input type="number" name="prices[0][min_qty]" required min="1"
                                    class="px-3 py-2 w-full text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500"
                                    value="1" placeholder="e.g., 1">
                            </div>
                            <div>
                                <label class="block mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Max Quantity
                                </label>
                                <input type="number" name="prices[0][max_qty]" min="1"
                                    class="px-3 py-2 w-full text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500"
                                    placeholder="Leave empty for unlimited">
                            </div>
                            <div>
                                <label class="block mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Price <span class="text-red-600">*</span>
                                </label>
                                <input type="number" step="0.01" name="prices[0][price]" required min="0"
                                    class="px-3 py-2 w-full text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500"
                                    placeholder="0.00">
                            </div>
                            <div>
                                <label class="block mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Discount Amount
                                </label>
                                <input type="number" step="0.01" name="prices[0][discount]" min="0"
                                class="px-3 py-2 w-full text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500"
                                placeholder="0.00">
                            </div>
                            <div class="flex items-end">
                                <button type="button" onclick="this.closest('.price-tier-item').remove(); updateTierNumbers();"
                                    class="px-3 py-2 w-full text-sm font-medium text-red-600 bg-white rounded-lg border border-red-300 transition-colors hover:bg-red-50">
                                    <i class="mr-1 fas fa-trash"></i>Remove
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Helper Text -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                    <div class="text-sm text-blue-900">
                        <p class="font-medium mb-1">Pricing Tips:</p>
                        <ul class="list-disc list-inside space-y-1 text-blue-800">
                            <li>Create bulk discounts by adding multiple price tiers</li>
                            <li>Ensure quantity ranges don't overlap</li>
                            <li>Leave max quantity empty for the highest tier (unlimited)</li>
                            <li>Lower quantities should have higher unit prices</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex gap-4 justify-end items-center pt-6">
            <a href="{{ route('vendor.product.show', $product) }}" class="inline-flex items-center gap-2 px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition-colors">
                <i class="fas fa-times"></i>
                <span>Cancel</span>
            </a>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-[#ff0808] text-white rounded-lg hover:bg-[#e60707] transition-colors font-semibold shadow-md">
                <i class="fas fa-save"></i>
                <span>Save Pricing</span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let priceCount = {{ $prices ? $prices->count() : 1 }};

    function addPriceTier() {
        const container = document.getElementById('priceTiersContainer');

        const tierDiv = document.createElement('div');
        tierDiv.className = 'p-5 bg-gradient-to-br from-green-50 to-white rounded-xl border border-green-100 price-tier-item';
        tierDiv.innerHTML = `
            <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
                <div>
                    <label class="block mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Min Quantity <span class="text-red-600">*</span>
                    </label>
                    <input type="number" name="prices[${priceCount}][min_qty]" required min="1"
                        class="px-3 py-2 w-full text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500"
                        placeholder="e.g., 1">
                </div>
                <div>
                    <label class="block mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Max Quantity
                    </label>
                    <input type="number" name="prices[${priceCount}][max_qty]" min="1"
                        class="px-3 py-2 w-full text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500"
                        placeholder="Leave empty for unlimited">
                </div>
                <div>
                    <label class="block mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Price <span class="text-red-600">*</span>
                    </label>
                    <input type="number" step="0.01" name="prices[${priceCount}][price]" required min="0"
                        class="px-3 py-2 w-full text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500"
                        placeholder="0.00">
                </div>
                <div>
                    <label class="block mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Discount Amount
                    </label>
                    <input type="number" step="0.01" name="prices[${priceCount}][discount]" min="0"
                        class="px-3 py-2 w-full text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500"
                        placeholder="0.00">
                </div>
                <div class="flex items-end">
                    <button type="button" onclick="this.closest('.price-tier-item').remove(); updateTierNumbers();"
                        class="px-3 py-2 w-full text-sm font-medium text-red-600 bg-white rounded-lg border border-red-300 transition-colors hover:bg-red-50">
                        <i class="mr-1 fas fa-trash"></i>Remove
                    </button>
                </div>
            </div>
        `;
        container.appendChild(tierDiv);
        priceCount++;
        updateTierNumbers();
    }

    function updateTierNumbers() {
        const tiers = document.querySelectorAll('.price-tier-item');
        tiers.forEach((tier, index) => {
            // Update all input names to maintain sequential indexing
            const inputs = tier.querySelectorAll('input[name^="prices["]');
            inputs.forEach(input => {
                const name = input.getAttribute('name');
                const fieldName = name.match(/\[([^\]]+)\]$/)[1];
                input.setAttribute('name', `prices[${index}][${fieldName}]`);
            });
        });
        priceCount = tiers.length;
    }

    // Form validation
    document.getElementById('pricingForm').addEventListener('submit', function(e) {
        const tiers = document.querySelectorAll('.price-tier-item');

        if (tiers.length === 0) {
            e.preventDefault();
            alert('Please add at least one price tier.');
            return false;
        }

        // Check for overlapping ranges
        const ranges = [];
        let hasError = false;

        tiers.forEach((tier, index) => {
            const minQty = parseInt(tier.querySelector('input[name*="[min_qty]"]').value) || 0;
            const maxQtyInput = tier.querySelector('input[name*="[max_qty]"]');
            const maxQty = maxQtyInput.value ? parseInt(maxQtyInput.value) : Infinity;
            const price = parseFloat(tier.querySelector('input[name*="[price]"]').value) || 0;

            if (minQty <= 0) {
                hasError = true;
                alert(`Tier ${index + 1}: Minimum quantity must be greater than 0.`);
                return;
            }

            if (maxQty !== Infinity && maxQty < minQty) {
                hasError = true;
                alert(`Tier ${index + 1}: Maximum quantity must be greater than or equal to minimum quantity.`);
                return;
            }

            if (price <= 0) {
                hasError = true;
                alert(`Tier ${index + 1}: Price must be greater than 0.`);
                return;
            }

            ranges.push({ min: minQty, max: maxQty, index: index + 1 });
        });

        if (hasError) {
            e.preventDefault();
            return false;
        }

        // Sort ranges by min quantity
        ranges.sort((a, b) => a.min - b.min);

        // Check for overlaps
        for (let i = 0; i < ranges.length - 1; i++) {
            const current = ranges[i];
            const next = ranges[i + 1];

            if (current.max >= next.min && current.max !== Infinity) {
                e.preventDefault();
                alert(`Tier ${current.index} and Tier ${next.index} have overlapping quantity ranges. Please adjust.`);
                return false;
            }
        }
    });
</script>
@endpush
@endsection

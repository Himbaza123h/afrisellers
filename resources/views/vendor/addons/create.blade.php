@extends('layouts.home')

@section('page-content')
<div class="mx-auto space-y-6 max-w-7xl">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex gap-4 items-center">
            <a href="{{ route('vendor.addons.available') }}" class="p-2 text-gray-600 hover:text-[#ff0808] rounded-lg hover:bg-gray-50 transition-colors">
                <i class="text-lg fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Purchase Addon</h1>
                <p class="mt-1 text-xs text-gray-500">Promote your item with premium placement</p>
            </div>
        </div>
    </div>

    <!-- Messages -->
    @if(session('error'))
        <div class="flex gap-3 items-start p-3 bg-red-50 rounded-lg border border-red-200">
            <i class="mt-0.5 text-red-600 fas fa-exclamation-circle"></i>
            <p class="flex-1 text-sm font-medium text-red-900">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    @if(session('success'))
        <div class="flex gap-3 items-start p-3 bg-green-50 rounded-lg border border-green-200">
            <i class="mt-0.5 text-green-600 fas fa-check-circle"></i>
            <p class="flex-1 text-sm font-medium text-green-900">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <!-- Addon Details Card -->
    <div class="overflow-hidden bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="bg-[#ff0808] p-5 text-white">
            <div class="flex gap-4 justify-between items-start">
                <div class="flex flex-1 gap-4 items-center">
                    <div class="flex flex-shrink-0 justify-center items-center w-16 h-16 bg-white bg-opacity-20 rounded-lg">
                        @php
                            $locationIcons = [
                                'Homepage' => 'fa-home',
                                'Products' => 'fa-boxes',
                                'Suppliers' => 'fa-store',
                                'Marketplace' => 'fa-shopping-bag',
                                'Category' => 'fa-list',
                                'Search' => 'fa-search',
                            ];
                            $icon = $locationIcons[$addon->locationX] ?? 'fa-map-marker-alt';
                        @endphp
                        <i class="fas {{ $icon }} text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h2 class="mb-1 text-xl font-bold">{{ $addon->locationX }}</h2>
                        <p class="mb-2 text-sm text-white text-opacity-90">{{ ucfirst(str_replace('_', ' ', $addon->locationY)) }} Position</p>
                        <div class="flex flex-wrap gap-3 items-center text-xs">
                            <span class="flex gap-1 items-center px-2 py-1 bg-white bg-opacity-20 rounded">
                                <i class="fas fa-map-marker-alt"></i>
                                {{ $addon->country ? $addon->country->name : 'Global' }}
                            </span>
                            <span class="flex gap-1 items-center px-2 py-1 bg-white bg-opacity-20 rounded">
                                <i class="fas fa-calendar"></i>
                                30 days base
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex-shrink-0 text-right">
                    <div class="text-3xl font-bold">${{ number_format($addon->price, 2) }}</div>
                    <div class="text-sm text-white text-opacity-90">per 30 days</div>
                </div>
            </div>
        </div>

        <div class="p-5">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                <div class="flex gap-3 items-center p-3 bg-blue-50 rounded-lg border border-blue-100">
                    <div class="flex flex-shrink-0 justify-center items-center w-10 h-10 bg-blue-100 rounded-lg">
                        <i class="text-blue-600 fas fa-eye"></i>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-blue-600">Visibility</div>
                        <div class="text-sm font-semibold text-gray-900">Premium Placement</div>
                    </div>
                </div>
                <div class="flex gap-3 items-center p-3 bg-purple-50 rounded-lg border border-purple-100">
                    <div class="flex flex-shrink-0 justify-center items-center w-10 h-10 bg-purple-100 rounded-lg">
                        <i class="text-purple-600 fas fa-chart-line"></i>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-purple-600">Engagement</div>
                        <div class="text-sm font-semibold text-gray-900">Higher Clicks</div>
                    </div>
                </div>
                <div class="flex gap-3 items-center p-3 bg-green-50 rounded-lg border border-green-100">
                    <div class="flex flex-shrink-0 justify-center items-center w-10 h-10 bg-green-100 rounded-lg">
                        <i class="text-green-600 fas fa-dollar-sign"></i>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-green-600">Revenue</div>
                        <div class="text-sm font-semibold text-gray-900">Boost Sales</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Form -->
    <form action="{{ route('vendor.addons.purchase', $addon) }}" method="POST" class="space-y-6">
        @csrf

        <!-- Type Selection -->
        <div class="overflow-hidden bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="p-5 bg-gray-50 border-b">
                <h3 class="text-sm font-semibold text-gray-900">What do you want to promote?</h3>
                <p class="mt-1 text-xs text-gray-500">Select the type of content you want to feature</p>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 gap-3 md:grid-cols-3">
                    @php
                        $allTypes = [
                            'product' => ['Product', 'fa-box', 'blue'],
                            'supplier' => ['Supplier Profile', 'fa-store', 'purple'],
                            'showroom' => ['Showroom', 'fa-building', 'indigo'],
                            'tradeshow' => ['Tradeshow', 'fa-calendar', 'cyan'],
                            'loadboad' => ['Load Board', 'fa-truck', 'orange'],
                            'car' => ['Car', 'fa-car', 'green']
                        ];

                        // Filter types based on location
                        if ($addon->locationX === 'Homepage' && in_array($addon->locationY, ['herosection', 'featuredsuppliers'])) {
                            $allowedTypes = ['supplier' => $allTypes['supplier']];
                        } elseif ($addon->locationX === 'Homepage' && $addon->locationY === 'trendingproducts') {
                            $allowedTypes = ['product' => $allTypes['product']];
                        } else {
                            $allowedTypes = $allTypes;
                        }
                    @endphp

                    @foreach($allowedTypes as $typeValue => $typeData)
                        <label class="relative cursor-pointer">
                            <input type="radio" name="type" value="{{ $typeValue }}" class="sr-only peer" required onchange="updateItemSelect(this.value)">
                            <div class="p-4 border-2 border-gray-200 rounded-lg transition-all peer-checked:border-[#ff0808] peer-checked:bg-red-50 hover:border-gray-300">
                                <div class="flex flex-col gap-2 items-center text-center">
                                    <div class="w-12 h-12 bg-{{ $typeData[2] }}-100 rounded-lg flex items-center justify-center transition-all peer-checked:bg-{{ $typeData[2] }}-200">
                                        <i class="fas {{ $typeData[1] }} text-{{ $typeData[2] }}-600 text-xl"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $typeData[0] }}</span>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('type')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Item Selection -->
        <div id="itemSelectContainer" class="hidden overflow-hidden bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="p-5 bg-gray-50 border-b">
                <h3 class="text-sm font-semibold text-gray-900">Select Item to Promote</h3>
                <p class="mt-1 text-xs text-gray-500">Choose which specific item you want to feature</p>
            </div>
            <div class="p-5">
                <!-- Product Select -->
                <select id="productSelect" name="related_id" class="item-select hidden w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808] text-sm" required>
                    <option value="">Choose a product...</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>

                <!-- Showroom Select -->
                <select id="showroomSelect" name="related_id" class="item-select hidden w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808] text-sm" required>
                    <option value="">Choose a showroom...</option>
                    @foreach($showrooms as $showroom)
                        <option value="{{ $showroom->id }}">{{ $showroom->name }}</option>
                    @endforeach
                </select>

                <!-- Supplier Auto-Selected Message -->
                <div id="supplierMessage" class="hidden p-4 bg-purple-50 rounded-lg border border-purple-200">
                    <div class="flex gap-3 items-start">
                        <div class="flex flex-shrink-0 justify-center items-center w-8 h-8 bg-purple-100 rounded-lg">
                            <i class="text-purple-600 fas fa-info-circle"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 text-sm font-semibold text-purple-900">Supplier Profile Selected</h4>
                            <p class="text-xs text-purple-700">Your supplier profile will be automatically promoted with this addon.</p>
                        </div>
                    </div>
                </div>

                <!-- Other types message -->
                <div id="otherTypeMessage" class="hidden p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <div class="flex gap-3 items-start">
                        <div class="flex flex-shrink-0 justify-center items-center w-8 h-8 bg-yellow-100 rounded-lg">
                            <i class="text-yellow-600 fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 text-sm font-semibold text-yellow-900">Coming Soon</h4>
                            <p class="text-xs text-yellow-700">This content type is not yet available. Please select Product, Supplier, or Showroom.</p>
                        </div>
                    </div>
                </div>

                @error('related_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Duration Selection -->
        <div class="overflow-hidden bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="p-5 bg-gray-50 border-b">
                <h3 class="text-sm font-semibold text-gray-900">Choose Duration</h3>
                <p class="mt-1 text-xs text-gray-500">Save more with longer subscription periods</p>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                    @foreach([30 => ['1 Month', '0%', 'blue'], 60 => ['2 Months', '5%', 'green'], 90 => ['3 Months', '10%', 'purple'], 180 => ['6 Months', '15%', 'orange']] as $days => $data)
                        <label class="relative cursor-pointer">
                            <input type="radio" name="duration_days" value="{{ $days }}" class="sr-only peer" required {{ $days === 30 ? 'checked' : '' }} onchange="calculatePrice()">
                            <div class="p-4 border-2 border-gray-200 rounded-lg transition-all peer-checked:border-[#ff0808] peer-checked:bg-red-50 hover:border-gray-300 h-full">
                                <div class="text-center">
                                    <div class="text-base font-bold text-gray-900">{{ $data[0] }}</div>
                                    <div class="mt-1 mb-2 text-xs text-gray-600">{{ $days }} days</div>
                                    @if($data[1] !== '0%')
                                        <div class="inline-flex items-center gap-1 px-2 py-1 bg-{{ $data[2] }}-100 text-{{ $data[2] }}-800 text-xs font-medium rounded-full">
                                            <i class="fas fa-tag" style="font-size: 10px;"></i>
                                            Save {{ $data[1] }}
                                        </div>
                                    @else
                                        <div class="h-6"></div>
                                    @endif
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('duration_days')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Price Summary -->
        <div class="overflow-hidden bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="p-5 bg-gray-50 border-b">
                <h3 class="text-sm font-semibold text-gray-900">Price Summary</h3>
                <p class="mt-1 text-xs text-gray-500">Review your order details</p>
            </div>
            <div class="p-5">
                <div class="space-y-3">
                    <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                        <div class="flex gap-2 items-center">
                            <i class="text-xs text-gray-400 fas fa-tag"></i>
                            <span class="text-sm font-medium text-gray-600">Base Price (30 days)</span>
                        </div>
                        <span class="text-sm font-bold text-gray-900">${{ number_format($addon->price, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                        <div class="flex gap-2 items-center">
                            <i class="text-xs text-gray-400 fas fa-calendar"></i>
                            <span class="text-sm font-medium text-gray-600">Selected Duration</span>
                        </div>
                        <span class="text-sm font-bold text-gray-900" id="durationDisplay">30 days</span>
                    </div>
                    <div id="discountRow" class="flex hidden justify-between items-center pb-3 border-b border-gray-200">
                        <div class="flex gap-2 items-center">
                            <i class="text-xs text-green-600 fas fa-percent"></i>
                            <span class="text-sm font-medium text-green-600">Discount Applied</span>
                        </div>
                        <span class="text-sm font-bold text-green-600" id="discountDisplay">-$0.00</span>
                    </div>
                    <div class="flex justify-between items-center pt-3">
                        <span class="text-base font-bold text-gray-900">Total Amount</span>
                        <span class="text-2xl font-bold text-[#ff0808]" id="totalPrice">${{ number_format($addon->price, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="p-5 bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('vendor.addons.available') }}" class="flex-1 px-5 py-2.5 text-sm font-medium text-center text-gray-700 rounded-lg border border-gray-300 transition-all hover:bg-gray-50">
                    <i class="mr-1 text-xs fas fa-times"></i>
                    Cancel
                </a>
                <button type="submit" class="flex-1 px-5 py-2.5 bg-[#ff0808] text-white rounded-lg hover:bg-[#dd0606] font-bold text-sm shadow-lg hover:shadow-xl transition-all">
                    <i class="mr-1 text-xs fas fa-shopping-cart"></i>
                    Complete Purchase
                </button>
            </div>
        </div>
    </form>
</div>

<style>
    /* Smooth transitions */
    * {
        transition-property: color, background-color, border-color, transform, box-shadow;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }

    /* Form focus states */
    select:focus {
        outline: none;
    }

    /* Radio button animations */
    input[type="radio"]:checked + div {
        animation: scaleIn 0.2s ease-out;
    }

    @keyframes scaleIn {
        0% {
            transform: scale(0.95);
        }
        100% {
            transform: scale(1);
        }
    }

    /* Fade in animation for dynamic content */
    .fade-in {
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<script>
const basePrice = {{ $addon->price }};

function updateItemSelect(type) {
    console.log('updateItemSelect called with type:', type);

    // Hide all item selects and disable them
    document.querySelectorAll('.item-select').forEach(el => {
        el.classList.add('hidden');
        el.style.display = 'none';
        el.removeAttribute('required');
        el.disabled = true;
        console.log('Hidden and disabled element:', el.id);
    });

    // Hide all messages
    document.getElementById('otherTypeMessage').classList.add('hidden');
    document.getElementById('supplierMessage').classList.add('hidden');

    // Show container with animation
    const container = document.getElementById('itemSelectContainer');
    container.style.display = 'block';
    container.classList.remove('hidden');
    container.classList.add('fade-in');
    console.log('Item select container shown');

    // Show appropriate select based on type
    if (type === 'product') {
        console.log('Product type selected');
        const select = document.getElementById('productSelect');
        select.classList.remove('hidden');
        select.style.display = 'block';
        select.setAttribute('required', 'required');
        select.disabled = false;
        console.log('Product select shown and required');
    } else if (type === 'supplier') {
        console.log('Supplier type selected - showing message');
        document.getElementById('supplierMessage').classList.remove('hidden');
        document.getElementById('supplierMessage').classList.add('fade-in');
    } else if (type === 'showroom') {
        console.log('Showroom type selected');
        const select = document.getElementById('showroomSelect');
        select.classList.remove('hidden');
        select.style.display = 'block';
        select.setAttribute('required', 'required');
        select.disabled = false;
        console.log('Showroom select shown and required');
    } else {
        console.log('Other type selected:', type);
        document.getElementById('otherTypeMessage').classList.remove('hidden');
        document.getElementById('otherTypeMessage').classList.add('fade-in');
    }
}

function calculatePrice() {
    const durationRadio = document.querySelector('input[name="duration_days"]:checked');
    if (!durationRadio) return;

    const days = parseInt(durationRadio.value);
    const months = days / 30;

    // Calculate discount
    let discount = 0;
    if (days === 60) discount = 0.05;
    else if (days === 90) discount = 0.10;
    else if (days === 180) discount = 0.15;

    const totalBeforeDiscount = basePrice * months;
    const discountAmount = totalBeforeDiscount * discount;
    const total = totalBeforeDiscount - discountAmount;

    // Update duration display
    document.getElementById('durationDisplay').textContent = days + ' days';

    // Update discount row visibility and amount
    const discountRow = document.getElementById('discountRow');
    const discountDisplay = document.getElementById('discountDisplay');

    if (discount > 0) {
        discountRow.classList.remove('hidden');
        discountDisplay.textContent = '-$' + discountAmount.toFixed(2);
    } else {
        discountRow.classList.add('hidden');
    }

    // Update total price
    document.getElementById('totalPrice').textContent = '$' + total.toFixed(2);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    calculatePrice();

    // If there's an old type selected (from form errors), show the appropriate select
    const selectedType = document.querySelector('input[name="type"]:checked');
    if (selectedType) {
        updateItemSelect(selectedType.value);
    }
});
</script>
@endsection

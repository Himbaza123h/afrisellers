@extends('layouts.home')

@section('page-content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('vendor.addons.available') }}" class="p-2 text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-100">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">Purchase Addon</h1>
            <p class="mt-1 text-sm text-gray-500">Promote your item with this addon placement</p>
        </div>
    </div>

    <!-- Messages -->
    @if(session('error'))
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <p class="text-sm font-medium text-red-900 flex-1">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <!-- Addon Details Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="bg-pink-500 to-purple-600 p-6 text-white">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h2 class="text-2xl font-bold mb-2">{{ $addon->locationX }}</h2>
                    <p class="text-pink-100 mb-3">{{ ucfirst(str_replace('_', ' ', $addon->locationY)) }}</p>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-map-marker-alt"></i>
                            <span class="text-sm">
                                {{ $addon->country ? $addon->country->name : 'Global' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold">${{ number_format($addon->price, 2) }}</div>
                    <div class="text-sm text-pink-100">per 30 days</div>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="flex items-center gap-3 p-4 bg-blue-50 rounded-lg">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-eye text-blue-600"></i>
                    </div>
                    <div>
                        <div class="text-xs text-blue-600 font-medium">Visibility</div>
                        <div class="text-sm font-semibold text-gray-900">Premium Placement</div>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-4 bg-purple-50 rounded-lg">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-purple-600"></i>
                    </div>
                    <div>
                        <div class="text-xs text-purple-600 font-medium">Engagement</div>
                        <div class="text-sm font-semibold text-gray-900">Higher Clicks</div>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-4 bg-green-50 rounded-lg">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-green-600"></i>
                    </div>
                    <div>
                        <div class="text-xs text-green-600 font-medium">Revenue</div>
                        <div class="text-sm font-semibold text-gray-900">Boost Sales</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Form -->
    <form action="{{ route('vendor.addons.purchase', $addon) }}" method="POST" class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-6">
        @csrf

        <!-- Type Selection -->
        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-3">
                What do you want to promote?
                <span class="text-red-500">*</span>
            </label>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                @php
                    $allTypes = ['product' => ['Product', 'fa-box', 'blue'], 'supplier' => ['Supplier Profile', 'fa-store', 'purple'], 'showroom' => ['Showroom', 'fa-building', 'indigo'], 'tradeshow' => ['Tradeshow', 'fa-calendar', 'cyan'], 'loadboad' => ['Load Board', 'fa-truck', 'orange'], 'car' => ['Car', 'fa-car', 'green']];

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
                    <label class="relative">
                        <input type="radio" name="type" value="{{ $typeValue }}" class="peer sr-only" required onchange="updateItemSelect(this.value)">
                        <div class="p-4 border-2 border-gray-200 rounded-lg cursor-pointer transition-all peer-checked:border-{{ $typeData[2] }}-500 peer-checked:bg-{{ $typeData[2] }}-50 hover:border-{{ $typeData[2] }}-300">
                            <div class="flex flex-col items-center text-center gap-2">
                                <div class="w-12 h-12 bg-{{ $typeData[2] }}-100 rounded-full flex items-center justify-center peer-checked:bg-{{ $typeData[2] }}-200">
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

        <!-- Item Selection -->
        <div id="itemSelectContainer" style="display: none;">
            <label class="block text-sm font-semibold text-gray-900 mb-2">
                Select Item to Promote
                <span class="text-red-500">*</span>
            </label>

            <!-- Product Select -->
            <select id="productSelect" name="related_id" class="item-select hidden w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500" required>
                <option value="">Choose a product...</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>

            <!-- Showroom Select -->
            <select id="showroomSelect" name="related_id" class="item-select hidden w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500" required>
                <option value="">Choose a showroom...</option>
                @foreach($showrooms as $showroom)
                    <option value="{{ $showroom->id }}">{{ $showroom->name }}</option>
                @endforeach
            </select>
            <!-- Supplier Select -->
            <!-- Supplier Auto-Selected Message -->
            <div id="supplierMessage" class="hidden p-4 bg-purple-50 border border-purple-200 rounded-lg">
                <p class="text-sm text-purple-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    Your supplier profile will be automatically promoted.
                </p>
            </div>

            <!-- Other types message -->
            <div id="otherTypeMessage" class="hidden p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm text-yellow-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    This type is not yet available. Please select Product or Showroom.
                </p>
            </div>

            @error('related_id')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Duration Selection -->
        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">
                Duration
                <span class="text-red-500">*</span>
            </label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach([30 => ['1 Month', '0%'], 60 => ['2 Months', 'Save 5%'], 90 => ['3 Months', 'Save 10%'], 180 => ['6 Months', 'Save 15%']] as $days => $data)
                    <label class="relative">
                        <input type="radio" name="duration_days" value="{{ $days }}" class="peer sr-only" required {{ $days === 30 ? 'checked' : '' }} onchange="calculatePrice()">
                        <div class="p-4 border-2 border-gray-200 rounded-lg cursor-pointer transition-all peer-checked:border-pink-500 peer-checked:bg-pink-50 hover:border-pink-300">
                            <div class="text-center">
                                <div class="text-lg font-bold text-gray-900">{{ $data[0] }}</div>
                                <div class="text-xs text-gray-600 mt-1">{{ $days }} days</div>
                                @if($data[1] !== '0%')
                                    <div class="mt-2 inline-block px-2 py-0.5 bg-green-100 text-green-800 text-xs font-medium rounded">{{ $data[1] }}</div>
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

        <!-- Price Summary -->
        <div class="p-6 bg-gray-50 to-gray-100 rounded-xl border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-medium text-gray-600">Base Price (30 days)</span>
                <span class="text-sm font-bold text-gray-900">${{ number_format($addon->price, 2) }}</span>
            </div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-medium text-gray-600">Duration</span>
                <span class="text-sm font-bold text-gray-900" id="durationDisplay">30 days</span>
            </div>
            <div class="flex items-center justify-between pt-4 border-t-2 border-gray-300">
                <span class="text-lg font-bold text-gray-900">Total Amount</span>
                <span class="text-2xl font-bold text-pink-600" id="totalPrice">${{ number_format($addon->price, 2) }}</span>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex gap-3">
            <a href="{{ route('vendor.addons.available') }}" class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-center">
                Cancel
            </a>
            <button type="submit" class="flex-1 px-6 py-3 bg-pink-600 to-purple-600 text-white rounded-lg hover:from-pink-700 hover:to-purple-700 font-medium shadow-lg hover:shadow-xl transition-all">
                <i class="fas fa-shopping-cart mr-2"></i>
                Purchase Addon
            </button>
        </div>
    </form>
</div>

<script>
const basePrice = {{ $addon->price }};

function updateItemSelect(type) {
    console.log('updateItemSelect called with type:', type);

    // Hide all item selects and disable them
    document.querySelectorAll('.item-select').forEach(el => {
        el.classList.add('hidden');
        el.style.display = 'none'; // Force hide with inline style
        el.removeAttribute('required');
        el.disabled = true; // Disable so they don't submit
        console.log('Hidden and disabled element:', el.id);
    });
    document.getElementById('otherTypeMessage').classList.add('hidden');
    document.getElementById('supplierMessage').classList.add('hidden');

    // Show container
    document.getElementById('itemSelectContainer').style.display = 'block';
    console.log('Item select container shown');

    // Show appropriate select based on type
    if (type === 'product') {
        console.log('Product type selected');
        const select = document.getElementById('productSelect');
        select.classList.remove('hidden');
        select.style.display = 'block'; // Force show with inline style
        select.setAttribute('required', 'required');
        select.disabled = false; // Enable it
        console.log('Product select shown and required');
    } else if (type === 'supplier') {
        console.log('Supplier type selected - showing message, no select needed');
        // Show supplier message instead of select
        document.getElementById('supplierMessage').classList.remove('hidden');
    } else if (type === 'showroom') {
        console.log('Showroom type selected');
        const select = document.getElementById('showroomSelect');
        select.classList.remove('hidden');
        select.style.display = 'block'; // Force show with inline style
        select.setAttribute('required', 'required');
        select.disabled = false; // Enable it
        console.log('Showroom select shown and required');
    } else {
        console.log('Other type selected:', type);
        // For other types not yet implemented
        document.getElementById('otherTypeMessage').classList.remove('hidden');
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

    // Update display
    document.getElementById('durationDisplay').textContent = days + ' days';
    document.getElementById('totalPrice').textContent = '$' + total.toFixed(2);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    calculatePrice();
});
</script>
@endsection

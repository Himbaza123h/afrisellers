@extends('layouts.home')

@section('title', 'Create New Order')

@section('page-content')
<div class="container-fluid max-w-5xl px-3 py-4 mx-auto sm:px-4 sm:py-5">
    <!-- Page Header -->
    <div class="mb-4 sm:mb-5">
        <div class="flex items-center gap-2 mb-2">
            <a href="{{ route('admin.orders.index') }}"
               class="p-1.5 transition-colors rounded hover:bg-gray-100">
                <i class="text-gray-600 fas fa-arrow-left"></i>
            </a>
            <h1 class="text-xl font-black text-gray-900 sm:text-2xl lg:text-lg">
                Create New Order
            </h1>
        </div>
        <p class="ml-8 text-xs text-gray-600 sm:text-sm">Add a new order to the system</p>
    </div>

    @if($errors->any())
        <div class="p-3 mb-4 border-l-4 border-red-500 rounded-lg bg-red-50">
            <div class="flex items-start gap-2">
                <i class="text-base text-red-500 fas fa-exclamation-circle"></i>
                <div>
                    <p class="mb-2 text-sm font-semibold text-red-800">Please fix the following errors:</p>
                    <ul class="space-y-1 text-xs text-red-700 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.orders.store') }}" method="POST" class="space-y-4" id="orderForm">
        @csrf

        <!-- Customer & Vendor Selection -->
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="flex items-center gap-2 pb-3 mb-3 border-b border-gray-200">
                <div class="flex items-center justify-center w-8 h-8 rounded-md bg-[#ff0808]">
                    <i class="text-sm text-white fas fa-users"></i>
                </div>
                <h2 class="text-sm font-bold text-gray-900">Customer & Vendor</h2>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <!-- Customer -->
                <div>
                    <label for="buyer_id" class="block mb-1 text-xs font-semibold text-gray-700">
                        Customer <span class="text-red-500">*</span>
                    </label>
                    <select id="buyer_id"
                            name="buyer_id"
                            required
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                        <option value="">-- Select Customer --</option>
                        @foreach($buyers as $buyer)
                            <option value="{{ $buyer->id }}" {{ old('buyer_id') == $buyer->id ? 'selected' : '' }}>
                                {{ $buyer->name }} ({{ $buyer->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Vendor -->
                <div>
                    <label for="vendor_id" class="block mb-1 text-xs font-semibold text-gray-700">
                        Vendor <span class="text-red-500">*</span>
                    </label>
                    <select id="vendor_id"
                            name="vendor_id"
                            required
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                        <option value="">-- Select Vendor --</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-200">
                <div class="flex items-center gap-2">
                    <div class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-md">
                        <i class="text-sm text-blue-600 fas fa-box"></i>
                    </div>
                    <h2 class="text-sm font-bold text-gray-900">Order Items</h2>
                </div>
                <button type="button"
                        onclick="addOrderItem()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                    <i class="fas fa-plus"></i> Add Item
                </button>
            </div>

            <div id="orderItems" class="space-y-3">
                <!-- Order items will be added here dynamically -->
            </div>

            <p class="mt-2 text-xs text-gray-500">
                <i class="fas fa-info-circle"></i> Click "Add Item" to add products to this order
            </p>
        </div>

        <!-- Order Details -->
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="flex items-center gap-2 pb-3 mb-3 border-b border-gray-200">
                <div class="flex items-center justify-center w-8 h-8 bg-green-100 rounded-md">
                    <i class="text-sm text-green-600 fas fa-dollar-sign"></i>
                </div>
                <h2 class="text-sm font-bold text-gray-900">Order Details</h2>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <!-- Currency -->
                <div>
                    <label for="currency" class="block mb-1 text-xs font-semibold text-gray-700">
                        Currency <span class="text-red-500">*</span>
                    </label>
                    <select id="currency"
                            name="currency"
                            required
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                        <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                        <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                        <option value="RWF" {{ old('currency', 'RWF') == 'RWF' ? 'selected' : '' }}>RWF (FRw)</option>
                    </select>
                </div>

                <!-- Tax -->
                <div>
                    <label for="tax" class="block mb-1 text-xs font-semibold text-gray-700">
                        Tax Amount
                    </label>
                    <input type="number"
                           id="tax"
                           name="tax"
                           value="{{ old('tax', 0) }}"
                           step="0.01"
                           min="0"
                           class="w-full px-3 py-2 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                           placeholder="0.00">
                </div>

                <!-- Shipping Fee -->
                <div>
                    <label for="shipping_fee" class="block mb-1 text-xs font-semibold text-gray-700">
                        Shipping Fee
                    </label>
                    <input type="number"
                           id="shipping_fee"
                           name="shipping_fee"
                           value="{{ old('shipping_fee', 0) }}"
                           step="0.01"
                           min="0"
                           class="w-full px-3 py-2 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                           placeholder="0.00">
                </div>
            </div>

            <!-- Notes -->
            <div class="mt-3">
                <label for="notes" class="block mb-1 text-xs font-semibold text-gray-700">
                    Order Notes
                </label>
                <textarea id="notes"
                          name="notes"
                          rows="3"
                          class="w-full px-3 py-2 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                          placeholder="Add any special instructions or notes...">{{ old('notes') }}</textarea>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-between gap-3 pt-3">
            <a href="{{ route('admin.orders.index') }}"
               class="px-4 py-2 text-xs font-semibold text-gray-700 transition-colors border border-gray-300 rounded-md hover:bg-gray-50">
                <i class="mr-1 fas fa-times"></i>Cancel
            </a>

            <button type="submit"
                    class="px-4 py-2 text-xs font-semibold text-white transition-all rounded-md bg-[#ff0808] hover:bg-red-700 shadow-sm">
                <i class="mr-1 fas fa-save"></i>Create Order
            </button>
        </div>
    </form>
</div>

<script>
let itemIndex = 0;
const products = @json($products);

function addOrderItem() {
    const container = document.getElementById('orderItems');
    const itemHtml = `
        <div class="p-3 border border-gray-200 rounded-lg order-item" data-index="${itemIndex}">
            <div class="flex items-start justify-between mb-3">
                <h3 class="text-xs font-semibold text-gray-900">Item #${itemIndex + 1}</h3>
                <button type="button"
                        onclick="removeOrderItem(${itemIndex})"
                        class="p-1 text-red-600 transition-colors rounded hover:bg-red-50">
                    <i class="text-xs fas fa-times"></i>
                </button>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-4">
                <div class="sm:col-span-2">
                    <label class="block mb-1 text-xs font-medium text-gray-700">Product</label>
                    <select name="items[${itemIndex}][product_id]"
                            required
                            onchange="updatePrice(${itemIndex})"
                            class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-[#ff0808]">
                        <option value="">-- Select Product --</option>
                        ${products.map(p => `<option value="${p.id}" data-price="${p.price}">${p.name} - $${p.price}</option>`).join('')}
                    </select>
                </div>

                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700">Quantity</label>
                    <input type="number"
                           name="items[${itemIndex}][quantity]"
                           value="1"
                           min="1"
                           required
                           class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-[#ff0808]">
                </div>

                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700">Unit Price</label>
                    <input type="number"
                           name="items[${itemIndex}][unit_price]"
                           value="0"
                           step="0.01"
                           min="0"
                           required
                           class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-[#ff0808]"
                           id="price_${itemIndex}">
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', itemHtml);
    itemIndex++;
}

function removeOrderItem(index) {
    const item = document.querySelector(`.order-item[data-index="${index}"]`);
    if (item) {
        item.remove();
    }
}

function updatePrice(index) {
    const select = document.querySelector(`select[name="items[${index}][product_id]"]`);
    const priceInput = document.getElementById(`price_${index}`);
    const selectedOption = select.options[select.selectedIndex];
    const price = selectedOption.getAttribute('data-price');

    if (price) {
        priceInput.value = price;
    }
}

// Add first item on page load
document.addEventListener('DOMContentLoaded', function() {
    addOrderItem();
});
</script>
@endsection

@extends('layouts.home')

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create New Order</h1>
            <p class="mt-1 text-sm text-gray-500">Place an order on behalf of a customer</p>
        </div>
        <a href="{{ route('vendor.orders.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all font-medium">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Orders</span>
        </a>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="p-4 bg-red-50 rounded-lg border border-red-200">
            <p class="text-sm font-medium text-red-900 mb-2">Please fix the following errors:</p>
            <ul class="space-y-1 text-sm text-red-700 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('vendor.orders.store') }}" method="POST" id="orderForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content - Left Side -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Customer Selection -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Customer Information</h2>

                    <div class="space-y-4">
                        <div>
                            <label for="buyer_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Select Customer <span class="text-red-500">*</span>
                            </label>
                            <select name="buyer_id" id="buyer_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Select a customer --</option>
                                @foreach($buyers as $buyer)
                                    <option value="{{ $buyer->id }}" {{ old('buyer_id') == $buyer->id ? 'selected' : '' }}>
                                        {{ $buyer->name }} ({{ $buyer->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="shippingAddressSection" style="display: none;">
                            <label for="shipping_address_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Shipping Address
                            </label>
                            <select name="shipping_address_id" id="shipping_address_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Select shipping address --</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Products Section -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Order Items</h2>
                        <button type="button" onclick="addProductRow()" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-medium text-sm">
                            <i class="fas fa-plus"></i>
                            Add Product
                        </button>
                    </div>

                    <div id="productsContainer" class="space-y-4">
                        <!-- Product rows will be added here dynamically -->
                    </div>

                    <div class="mt-4 p-4 bg-gray-50 rounded-lg text-center text-sm text-gray-500" id="emptyState">
                        <i class="fas fa-box-open text-lg mb-2"></i>
                        <p>Click "Add Product" to start adding items to this order</p>
                    </div>
                </div>

                <!-- Order Notes -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h2>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Order Notes
                        </label>
                        <textarea name="notes" id="notes" rows="4" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Add any special instructions or notes...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Order Summary - Right Side -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 sticky top-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>

                    <!-- Calculation -->
                    <div class="space-y-3 mb-6 pb-6 border-b">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium text-gray-900" id="summarySubtotal">$0.00</span>
                        </div>

                        <div>
                            <label for="tax" class="block text-sm text-gray-600 mb-1">Tax</label>
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500">$</span>
                                <input type="number" name="tax" id="tax" value="{{ old('tax', 0) }}" step="0.01" min="0" class="flex-1 px-3 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500" onchange="calculateTotal()">
                            </div>
                        </div>

                        <div>
                            <label for="shipping_fee" class="block text-sm text-gray-600 mb-1">Shipping Fee</label>
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500">$</span>
                                <input type="number" name="shipping_fee" id="shipping_fee" value="{{ old('shipping_fee', 0) }}" step="0.01" min="0" class="flex-1 px-3 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500" onchange="calculateTotal()">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center mb-6 pb-6 border-b">
                        <span class="text-base font-semibold text-gray-900">Total</span>
                        <span class="text-2xl font-bold text-gray-900" id="summaryTotal">$0.00</span>
                    </div>

                    <!-- Status Selection -->
                    <div class="space-y-4 mb-6">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Order Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" id="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="processing" {{ old('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                            </select>
                        </div>

                        <div>
                            <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-2">
                                Payment Status <span class="text-red-500">*</span>
                            </label>
                            <select name="payment_status" id="payment_status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                <option value="pending" {{ old('payment_status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>
                        <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">
                            Payment Method
                        </label>
                        <select name="payment_method" id="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Select method --</option>
                            <option value="cash">Cash</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            {{-- <option value="paypal">PayPal</option>
                            <option value="stripe">Stripe</option> --}}
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label for="payment_reference" class="block text-sm font-medium text-gray-700 mb-2">
                            Payment Reference
                        </label>
                        <input type="text" name="payment_reference" id="payment_reference" placeholder="e.g., Check #123" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-[#ff0808] text-white rounded-lg hover:bg-[#e00707] transition-all font-semibold">
                            <i class="fas fa-check"></i>
                            Create Order
                        </button>
                        <a href="{{ route('vendor.orders.index') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all font-medium">
                            <i class="fas fa-times"></i>
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Product Row Template -->
<template id="productRowTemplate">
    <div class="product-row p-4 bg-gray-50 rounded-lg border border-gray-200">
        <div class="flex items-start gap-3">
            <div class="flex-1 grid grid-cols-1 md:grid-cols-12 gap-3">
                <!-- Product Selection -->
                <div class="md:col-span-5">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Product</label>
                    <select name="products[INDEX][product_id]" class="product-select w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500" required onchange="loadProductDetails(this)">
                        <option value="">Select product...</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-stock="{{ $product->stock_quantity }}">
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Quantity -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Quantity</label>
                    <input type="number" name="products[INDEX][quantity]" class="quantity-input w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500" value="1" min="1" required onchange="onQuantityChange(this)">
                </div>

                <!-- Price -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Price ($)</label>
                    <input type="number" name="products[INDEX][price]" class="price-input w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500" value="0" step="0.01" min="0" required onchange="calculateRowTotal(this)">
                </div>

                <!-- Subtotal -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Subtotal</label>
                    <div class="flex items-center h-[38px] px-3 bg-gray-100 rounded text-sm font-medium text-gray-900 row-subtotal">
                        $0.00
                    </div>
                </div>
            </div>

            <!-- Remove Button -->
            <button type="button" onclick="removeProductRow(this)" class="mt-6 p-2 text-red-600 hover:bg-red-50 rounded transition-colors" title="Remove">
                <i class="fas fa-trash"></i>
            </button>
        </div>

        <!-- Stock Warning -->
        <div class="stock-warning hidden mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-xs text-yellow-800">
            <i class="fas fa-exclamation-triangle mr-1"></i>
            <span class="stock-message"></span>
        </div>
    </div>
</template>

<script>
let productRowIndex = 0;

// Load buyer addresses when buyer is selected
document.getElementById('buyer_id').addEventListener('change', function() {
    const buyerId = this.value;
    const addressSection = document.getElementById('shippingAddressSection');
    const addressSelect = document.getElementById('shipping_address_id');

    if (buyerId) {
        // Fetch addresses
        fetch(`/vendor/orders/buyer/${buyerId}/addresses`)
            .then(response => response.json())
            .then(addresses => {
                addressSelect.innerHTML = '<option value="">-- Select shipping address --</option>';
                addresses.forEach(address => {
                    const option = document.createElement('option');
                    option.value = address.id;
                    option.textContent = `${address.address_line1}, ${address.city}, ${address.state} ${address.zip_code}`;
                    addressSelect.appendChild(option);
                });
                addressSection.style.display = addresses.length > 0 ? 'block' : 'none';
            });
    } else {
        addressSection.style.display = 'none';
    }
});

// Add product row
function addProductRow() {
    const template = document.getElementById('productRowTemplate');
    const clone = template.content.cloneNode(true);
    const container = document.getElementById('productsContainer');

    // Replace INDEX with actual index
    const html = clone.querySelector('.product-row').outerHTML.replace(/INDEX/g, productRowIndex);
    container.insertAdjacentHTML('beforeend', html);

    productRowIndex++;

    // Hide empty state
    document.getElementById('emptyState').style.display = 'none';
}

// Remove product row
function removeProductRow(button) {
    const row = button.closest('.product-row');
    row.remove();

    // Show empty state if no products
    const container = document.getElementById('productsContainer');
    if (container.children.length === 0) {
        document.getElementById('emptyState').style.display = 'block';
    }

    calculateTotal();
}

// Load product details when product is selected
function loadProductDetails(select) {
    const row = select.closest('.product-row');
    const option = select.options[select.selectedIndex];
    const price = option.dataset.price || 0;
    const stock = option.dataset.stock || 0;

    const priceInput = row.querySelector('.price-input');
    priceInput.value = price;

    // Show stock warning if applicable
    const stockWarning = row.querySelector('.stock-warning');
    const stockMessage = row.querySelector('.stock-message');
    const quantityInput = row.querySelector('.quantity-input');

    if (stock > 0) {
        stockMessage.textContent = `Available stock: ${stock} units`;
        stockWarning.classList.remove('hidden');
        quantityInput.max = stock;
    } else {
        stockWarning.classList.add('hidden');
    }

    calculateRowTotal(select);
}

// Calculate row subtotal
function calculateRowTotal(element) {
    const row = element.closest('.product-row');
    const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
    const price = parseFloat(row.querySelector('.price-input').value) || 0;
    const subtotal = quantity * price;

    row.querySelector('.row-subtotal').textContent = `$${subtotal.toFixed(2)}`;

    calculateTotal();
}

// Calculate total
function calculateTotal() {
    let subtotal = 0;

    document.querySelectorAll('.product-row').forEach(row => {
        const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        subtotal += quantity * price;
    });

    const tax = parseFloat(document.getElementById('tax').value) || 0;
    const shippingFee = parseFloat(document.getElementById('shipping_fee').value) || 0;
    const total = subtotal + tax + shippingFee;

    document.getElementById('summarySubtotal').textContent = `$${subtotal.toFixed(2)}`;
    document.getElementById('summaryTotal').textContent = `$${total.toFixed(2)}`;
}

// Form validation
document.getElementById('orderForm').addEventListener('submit', function(e) {
    const productsContainer = document.getElementById('productsContainer');

    if (productsContainer.children.length === 0) {
        e.preventDefault();
        alert('Please add at least one product to the order.');
        return false;
    }
});

// Add initial product row on page load
document.addEventListener('DOMContentLoaded', function() {
    addProductRow();
});
</script>

<style>
select, input[type="number"], input[type="text"], textarea {
    transition: all 0.2s;
}

select:focus, input:focus, textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.product-row {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
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
@endsection

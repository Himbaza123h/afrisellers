@extends('layouts.app')

@section('title', 'Shopping Cart')

@php
    $currencySymbols = [
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'RWF' => 'RF',
        'KES' => 'KSh',
        'UGX' => 'USh',
        'TZS' => 'TSh',
    ];
@endphp

@section('content')
<div class="py-4 sm:py-6 md:py-8 min-h-screen bg-gray-50">
    <div class="container px-3 sm:px-4 md:px-6 mx-auto">
        <h1 class="mb-4 sm:mb-6 text-lg sm:text-xl md:text-2xl font-bold text-gray-900">Shopping Cart</h1>

        @if($cartItems->count() > 0)
            <div class="grid grid-cols-1 gap-4 sm:gap-6 lg:grid-cols-3">
                <!-- Cart Items -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-md shadow-sm">
                        @foreach($cartItems as $item)
                            @php
                                $image = $item->product->images->where('is_primary', true)->first() ?? $item->product->images->first();
                                $symbol = $currencySymbols[$item->currency] ?? $item->currency;
                            @endphp
                            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 p-3 sm:p-4 border-b last:border-b-0 cart-item"
                                 data-product-name="{{ $item->product->name }}"
                                 data-quantity="{{ $item->quantity }}"
                                 data-price="{{ $item->price }}"
                                 data-total="{{ $item->price * $item->quantity }}"
                                 data-currency="{{ $symbol }}"
                                 data-variations="{{ json_encode($item->selected_variations ?? []) }}">
                                <img src="{{ $image ? $image->image_url : asset('images/placeholder-product.png') }}"
                                     alt="{{ $item->product->name }}"
                                     class="object-cover w-full sm:w-20 md:w-24 h-32 sm:h-20 md:h-24 rounded-md flex-shrink-0">

                                <div class="flex-1 min-w-0">
                                    <h3 class="mb-1.5 sm:mb-2 text-sm sm:text-base font-semibold text-gray-900 line-clamp-2">
                                        {{ $item->product->name }}
                                    </h3>

                                    @if($item->selected_variations)
                                        <div class="mb-2 text-xs sm:text-sm text-gray-600">
                                            @foreach($item->selected_variations as $type => $value)
                                                <span class="inline-block px-1.5 sm:px-2 py-0.5 sm:py-1 mr-1 sm:mr-2 text-[10px] sm:text-xs font-medium text-gray-700 bg-gray-100 rounded">
                                                    {{ ucfirst(str_replace('_', ' ', $type)) }}: <strong>{{ ucfirst($value) }}</strong>
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Price for mobile (shows above buttons) -->
                                    <div class="sm:hidden mb-2">
                                        <p class="text-base font-bold text-gray-900">
                                            {{ $symbol }}{{ number_format($item->price * $item->quantity, 2) }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $symbol }}{{ number_format($item->price, 2) }} each
                                        </p>
                                    </div>

                                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 items-start sm:items-center">
                                        <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center gap-1.5 sm:gap-2 w-full sm:w-auto">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="quantity" value="{{ $item->quantity }}"
                                                   min="1"
                                                   class="w-16 sm:w-20 px-2 py-1 text-xs sm:text-sm text-center border border-gray-300 rounded">
                                            <button type="submit" class="px-2 sm:px-3 py-1 text-[10px] sm:text-xs md:text-sm text-white bg-blue-600 rounded hover:bg-blue-700 whitespace-nowrap">
                                                Update
                                            </button>
                                        </form>

                                        <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-[10px] sm:text-xs md:text-sm text-red-600 hover:text-red-700">
                                                <i class="fas fa-trash"></i> Remove
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Price for desktop (shows on right side) -->
                                <div class="hidden sm:block text-right flex-shrink-0">
                                    <p class="text-base sm:text-lg font-bold text-gray-900">
                                        {{ $symbol }}{{ number_format($item->price * $item->quantity, 2) }}
                                    </p>
                                    <p class="text-xs sm:text-sm text-gray-500">
                                        {{ $symbol }}{{ number_format($item->price, 2) }} each
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="lg:sticky lg:top-4 p-4 sm:p-5 md:p-6 bg-white rounded-md shadow-sm">
                        <h2 class="mb-3 sm:mb-4 text-base sm:text-lg md:text-xl font-bold text-gray-900">Order Summary</h2>

                        <div class="space-y-2 sm:space-y-3 mb-4 sm:mb-6">
                            <div class="flex justify-between text-sm sm:text-base">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-semibold" id="subtotal">${{ number_format($totalAmount, 2) }}</span>
                            </div>

                            <!-- Shipping Dropdown -->
                            <div class="pt-2 sm:pt-3 border-t">
                                <label class="block mb-1.5 sm:mb-2 text-xs sm:text-sm font-medium text-gray-700">
                                    <i class="mr-1 fas fa-truck"></i> Shipping Option
                                </label>
                                <select id="shippingSelect" class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">No Shipping Required</option>
                                    @foreach($availableCars as $car)
                                        <option value="{{ $car->id }}"
                                                data-price="{{ $car->price }}"
                                                data-name="{{ $car->full_name }}"
                                                data-route="{{ $car->route }}"
                                                {{ $selectedCarId == $car->id ? 'selected' : '' }}>
                                            {{ $car->full_name }} - {{ $car->route }}
                                            (${{ number_format($car->price, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-[10px] sm:text-xs text-gray-500">Select a vehicle for shipping or leave unselected for no shipping</p>
                            </div>

                            <div class="flex justify-between text-sm sm:text-base">
                                <span class="text-gray-600">Shipping Cost</span>
                                <span class="font-semibold text-green-600" id="shippingCost">
                                    ${{ number_format($shippingCost, 2) }}
                                </span>
                            </div>

                            <div class="pt-2 sm:pt-3 border-t">
                                <div class="flex justify-between text-base sm:text-lg">
                                    <span class="font-bold">Total</span>
                                    <span class="font-bold text-blue-600" id="totalAmount">
                                        ${{ number_format($totalAmount + $shippingCost, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <button id="checkoutBtn" class="w-full py-2.5 sm:py-3 text-sm sm:text-base font-bold text-white bg-green-600 rounded-md hover:bg-green-700 transition-colors">
                            <i class="mr-1.5 sm:mr-2 fab fa-whatsapp"></i>
                            Proceed to Checkout
                        </button>

                        <a href="{{ route('home') }}" class="block mt-3 sm:mt-4 text-xs sm:text-sm text-center text-blue-600 hover:underline">
                            <i class="mr-1 fas fa-arrow-left"></i> Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="py-12 sm:py-16 text-center bg-white rounded-md shadow-sm">
                <i class="mb-3 sm:mb-4 text-4xl sm:text-5xl md:text-6xl text-gray-300 fas fa-shopping-cart"></i>
                <h2 class="mb-2 text-lg sm:text-xl md:text-2xl font-bold text-gray-900">Your cart is empty</h2>
                <p class="mb-4 sm:mb-6 text-sm sm:text-base text-gray-600">Add some products to get started!</p>
                <a href="{{ route('home') }}" class="inline-block px-4 sm:px-6 py-2 sm:py-3 text-sm sm:text-base font-bold text-white bg-blue-600 rounded-md hover:bg-blue-700">
                    <i class="mr-1.5 sm:mr-2 fas fa-shopping-bag"></i> Start Shopping
                </a>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const shippingSelect = document.getElementById('shippingSelect');
    const checkoutBtn = document.getElementById('checkoutBtn');

    // Only run if cart has items
    if (!shippingSelect) return;

    const subtotalEl = document.getElementById('subtotal');
    const shippingCostEl = document.getElementById('shippingCost');
    const totalEl = document.getElementById('totalAmount');

    const subtotal = {{ $totalAmount }};
    let currentShippingCost = {{ $shippingCost }};
    let selectedShippingInfo = null;

    shippingSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const shippingPrice = parseFloat(selectedOption.dataset.price) || 0;
        const carId = this.value || null;

        // Store shipping info for WhatsApp message
        if (carId) {
            selectedShippingInfo = {
                name: selectedOption.dataset.name,
                route: selectedOption.dataset.route,
                price: shippingPrice
            };
        } else {
            selectedShippingInfo = null;
        }

        console.log('Shipping selected:', {
            carId: carId,
            price: shippingPrice
        });

        // Show loading state
        shippingCostEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        totalEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        // Update shipping cost via AJAX
        fetch('{{ route("cart.shipping") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ car_id: carId })
        })
        .then(response => response.json())
        .then(data => {
            const shipping = parseFloat(data.shipping_cost) || 0;
            const total = subtotal + shipping;
            currentShippingCost = shipping;

            console.log('Updated costs:', {
                subtotal: subtotal,
                shipping: shipping,
                total: total
            });

            // Update UI with animation
            shippingCostEl.style.opacity = '0';
            totalEl.style.opacity = '0';

            setTimeout(() => {
                shippingCostEl.textContent = '$' + shipping.toFixed(2);
                totalEl.textContent = '$' + total.toFixed(2);
                shippingCostEl.style.transition = 'opacity 0.3s';
                totalEl.style.transition = 'opacity 0.3s';
                shippingCostEl.style.opacity = '1';
                totalEl.style.opacity = '1';
            }, 100);

            // Show success toast
            showToastMessage(shipping > 0 ? `Shipping added: $${shipping.toFixed(2)}` : 'Shipping removed', 'success');
        })
        .catch(error => {
            console.error('Error updating shipping:', error);
            shippingCostEl.textContent = '$0.00';
            totalEl.textContent = '$' + subtotal.toFixed(2);
            showToastMessage('Failed to update shipping. Please try again.', 'error');
        });
    });

    // WhatsApp Checkout with Loading State
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function() {
            // Store original button content
            const originalContent = checkoutBtn.innerHTML;

            // Disable button and show loading state
            checkoutBtn.disabled = true;
            checkoutBtn.classList.add('opacity-75', 'cursor-not-allowed');
            checkoutBtn.innerHTML = `
                <i class="fas fa-spinner fa-spin mr-1.5 sm:mr-2"></i>
                <span>Preparing Order...</span>
            `;

            // Simulate processing time for better UX
            setTimeout(() => {
                // Collect cart items
                const cartItems = document.querySelectorAll('.cart-item');
                let orderDetails = '🛒 *NEW ORDER REQUEST*\n\n';
                orderDetails += '━━━━━━━━━━━━━━━━━━━━\n\n';

                let itemCount = 0;
                cartItems.forEach((item, index) => {
                    itemCount++;
                    const name = item.dataset.productName;
                    const quantity = item.dataset.quantity;
                    const price = parseFloat(item.dataset.price);
                    const total = parseFloat(item.dataset.total);
                    const currency = item.dataset.currency;
                    const variations = JSON.parse(item.dataset.variations || '{}');

                    orderDetails += `*${itemCount}. ${name}*\n`;

                    // Add variations if any
                    if (Object.keys(variations).length > 0) {
                        Object.entries(variations).forEach(([type, value]) => {
                            orderDetails += `   • ${type.replace('_', ' ')}: ${value}\n`;
                        });
                    }

                    orderDetails += `   Quantity: ${quantity} pcs\n`;
                    orderDetails += `   Price: ${currency}${price.toFixed(2)} each\n`;
                    orderDetails += `   Subtotal: ${currency}${total.toFixed(2)}\n\n`;
                });

                orderDetails += '━━━━━━━━━━━━━━━━━━━━\n\n';
                orderDetails += `📦 *Items Total:* $${subtotal.toFixed(2)}\n`;

                // Add shipping info
                if (selectedShippingInfo) {
                    orderDetails += `\n🚚 *Shipping Details:*\n`;
                    orderDetails += `   Vehicle: ${selectedShippingInfo.name}\n`;
                    orderDetails += `   Route: ${selectedShippingInfo.route}\n`;
                    orderDetails += `   Cost: $${selectedShippingInfo.price.toFixed(2)}\n`;
                } else {
                    orderDetails += `\n🚚 *Shipping:* No shipping required\n`;
                }

                orderDetails += `\n━━━━━━━━━━━━━━━━━━━━\n`;
                orderDetails += `\n💰 *TOTAL AMOUNT: $${(subtotal + currentShippingCost).toFixed(2)}*\n\n`;
                orderDetails += `━━━━━━━━━━━━━━━━━━━━\n\n`;
                orderDetails += `📅 Date: ${new Date().toLocaleDateString()}\n`;
                orderDetails += `🕐 Time: ${new Date().toLocaleTimeString()}\n\n`;
                orderDetails += `✅ Please confirm this order and provide payment details.\n\n`;
                orderDetails += `Thank you for shopping with us! 🙏`;

                // WhatsApp phone number (Burundi format)
                const whatsappNumber = '25776044316'; // +257 76 04 43 16

                // Encode message for URL
                const encodedMessage = encodeURIComponent(orderDetails);

                // Create WhatsApp URL
                const whatsappURL = `https://wa.me/${whatsappNumber}?text=${encodedMessage}`;

                // Update button to show success
                checkoutBtn.innerHTML = `
                    <i class="fas fa-check-circle mr-1.5 sm:mr-2"></i>
                    <span>Opening WhatsApp...</span>
                `;

                // Open WhatsApp
                window.open(whatsappURL, '_blank');

                // Show success message
                showToastMessage('Opening WhatsApp for checkout...', 'success');

                // Reset button after delay
                setTimeout(() => {
                    checkoutBtn.disabled = false;
                    checkoutBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                    checkoutBtn.innerHTML = originalContent;
                }, 2000);

            }, 800); // 800ms delay for smooth UX
        });
    }

    // Toast notification function
    function showToastMessage(message, type = 'success') {
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-600' : 'bg-red-600';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

        toast.className = `fixed bottom-4 right-4 ${bgColor} text-white px-4 py-3 rounded-lg shadow-lg text-sm font-medium z-50 animate-slide-up flex items-center gap-2`;
        toast.innerHTML = `<i class="fas ${icon}"></i> ${message}`;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
});
</script>

<style>
/* Button loading states */
#checkoutBtn {
    position: relative;
    transition: all 0.3s ease;
}

#checkoutBtn:disabled {
    cursor: not-allowed;
    pointer-events: none;
}

#checkoutBtn .fa-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

@keyframes slide-up {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.animate-slide-up {
    animation: slide-up 0.3s ease-out;
}

/* Smooth transitions */
#shippingCost, #totalAmount {
    transition: opacity 0.3s ease;
}

/* Mobile optimizations */
@media (max-width: 640px) {
    .cart-item {
        border-radius: 0;
    }
}

/* Ensure proper spacing on mobile */
@media (max-width: 640px) {
    .container {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }
}

/* Line clamp utility */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection

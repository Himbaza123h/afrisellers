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
<div class="py-8 min-h-screen bg-gray-50">
    <div class="container px-4 mx-auto">
        <h1 class="mb-6 text-lg font-bold text-gray-900">Shopping Cart</h1>

        @if($cartItems->count() > 0)
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Cart Items -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-md shadow-sm">
                        @foreach($cartItems as $item)
                            @php
                                $image = $item->product->images->where('is_primary', true)->first() ?? $item->product->images->first();
                                $symbol = $currencySymbols[$item->currency] ?? $item->currency;
                            @endphp
                            <div class="flex gap-4 p-4 border-b last:border-b-0">
                                <img src="{{ $image ? $image->image_url : asset('images/placeholder-product.png') }}"
                                     alt="{{ $item->product->name }}"
                                     class="object-cover w-24 h-24 rounded-md">

                                <div class="flex-1">
                                    <h3 class="mb-2 font-semibold text-gray-900">{{ $item->product->name }}</h3>

                                    @if($item->selected_variations)
                                        <div class="mb-2 text-sm text-gray-600">
                                            @foreach($item->selected_variations as $type => $value)
                                                <span class="inline-block px-2 py-1 mr-2 text-xs font-medium text-gray-700 bg-gray-100 rounded">
                                                    {{ ucfirst(str_replace('_', ' ', $type)) }}: <strong>{{ ucfirst($value) }}</strong>
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="flex gap-4 items-center">
                                        <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="quantity" value="{{ $item->quantity }}"
                                                   min="1"
                                                   class="w-20 px-2 py-1 text-center border border-gray-300 rounded">
                                            <button type="submit" class="px-3 py-1 text-sm text-white bg-blue-600 rounded hover:bg-blue-700">
                                                Update
                                            </button>
                                        </form>

                                        <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm text-red-600 hover:text-red-700">
                                                <i class="fas fa-trash"></i> Remove
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <p class="text-lg font-bold text-gray-900">
                                        {{ $symbol }}{{ number_format($item->price * $item->quantity, 2) }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ $symbol }}{{ number_format($item->price, 2) }} each
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="sticky top-4 p-6 bg-white rounded-md shadow-sm">
                        <h2 class="mb-4 text-xl font-bold text-gray-900">Order Summary</h2>

                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-semibold" id="subtotal">${{ number_format($totalAmount, 2) }}</span>
                            </div>

                            <!-- Shipping Dropdown -->
                            <div class="pt-3 border-t">
                                <label class="block mb-2 text-sm font-medium text-gray-700">
                                    <i class="mr-1 fas fa-truck"></i> Shipping Option
                                </label>
                                <select id="shippingSelect" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">No Shipping Required</option>
                                    @foreach($availableCars as $car)
                                        <option value="{{ $car->id }}"
                                                data-price="{{ $car->price }}"
                                                {{ $selectedCarId == $car->id ? 'selected' : '' }}>
                                            {{ $car->full_name }} - {{ $car->route }}
                                            (${{ number_format($car->price, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Select a vehicle for shipping or leave unselected for no shipping</p>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping Cost</span>
                                <span class="font-semibold text-green-600" id="shippingCost">
                                    ${{ number_format($shippingCost, 2) }}
                                </span>
                            </div>

                            <div class="pt-3 border-t">
                                <div class="flex justify-between">
                                    <span class="text-lg font-bold">Total</span>
                                    <span class="text-lg font-bold text-blue-600" id="totalAmount">
                                        ${{ number_format($totalAmount + $shippingCost, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <button class="w-full py-3 font-bold text-white bg-green-600 rounded-md hover:bg-green-700 transition-colors">
                            <i class="mr-2 fas fa-lock"></i>
                            Proceed to Checkout
                        </button>

                        <a href="{{ route('home') }}" class="block mt-4 text-center text-blue-600 hover:underline">
                            <i class="mr-1 fas fa-arrow-left"></i> Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="py-16 text-center bg-white rounded-md shadow-sm">
                <i class="mb-4 text-6xl text-gray-300 fas fa-shopping-cart"></i>
                <h2 class="mb-2 text-2xl font-bold text-gray-900">Your cart is empty</h2>
                <p class="mb-6 text-gray-600">Add some products to get started!</p>
                <a href="{{ route('home') }}" class="inline-block px-6 py-3 font-bold text-white bg-blue-600 rounded-md hover:bg-blue-700">
                    <i class="mr-2 fas fa-shopping-bag"></i> Start Shopping
                </a>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const shippingSelect = document.getElementById('shippingSelect');

    // Only run if cart has items
    if (!shippingSelect) return;

    const subtotalEl = document.getElementById('subtotal');
    const shippingCostEl = document.getElementById('shippingCost');
    const totalEl = document.getElementById('totalAmount');

    const subtotal = {{ $totalAmount }};

    shippingSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const shippingPrice = parseFloat(selectedOption.dataset.price) || 0;
        const carId = this.value || null;

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

            // Show success toast if available
            if (typeof showToast === 'function') {
                if (shipping > 0) {
                    showToast(`Shipping added: $${shipping.toFixed(2)}`, 'success');
                } else {
                    showToast('Shipping removed', 'success');
                }
            }
        })
        .catch(error => {
            console.error('Error updating shipping:', error);
            shippingCostEl.textContent = '$0.00';
            totalEl.textContent = '$' + subtotal.toFixed(2);

            if (typeof showToast === 'function') {
                showToast('Failed to update shipping. Please try again.', 'error');
            }
        });
    });
});
</script>
@endsection

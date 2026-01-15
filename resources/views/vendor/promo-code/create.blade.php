@extends('layouts.home')

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('vendor.promo-code.index') }}" class="p-2 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Promo Code</h1>
            <p class="mt-1 text-sm text-gray-500">Set up a new promotional discount code</p>
        </div>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="p-4 bg-red-50 rounded-lg border border-red-200">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
                <div class="flex-1">
                    <p class="text-sm font-medium text-red-900 mb-2">Please fix the following errors:</p>
                    <ul class="text-sm text-red-700 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Form -->
    <form action="{{ route('vendor.promo-code.store') }}" method="POST" id="promoCodeForm">
        @csrf

        <!-- Basic Information -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                <i class="fas fa-info-circle text-red-600"></i>
                Basic Information
            </h2>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Promo Code <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code') }}" required maxlength="50"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-gray-900 uppercase"
                        placeholder="e.g., SUMMER2024" oninput="this.value = this.value.toUpperCase()">
                    <p class="mt-1 text-xs text-gray-500">Must be unique. Will be converted to uppercase.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Status <span class="text-red-600">*</span>
                    </label>
                    <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-gray-900">
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Description
                    </label>
                    <textarea name="description" rows="3" maxlength="500"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-gray-900"
                        placeholder="Describe what this promo code is for...">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Discount Settings -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                <i class="fas fa-percentage text-red-600"></i>
                Discount Settings
            </h2>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Discount Type <span class="text-red-600">*</span>
                    </label>
                    <select name="discount_type" id="discountType" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-gray-900">
                        <option value="percentage" {{ old('discount_type') === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Discount Value <span class="text-red-600">*</span>
                    </label>
                    <input type="number" name="discount_value" value="{{ old('discount_value') }}" required min="0" step="0.01"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-gray-900"
                        placeholder="e.g., 20">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Currency <span class="text-red-600">*</span>
                    </label>
                    <select name="currency" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-gray-900">
                        <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                        <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                        <option value="GBP" {{ old('currency') === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                        <option value="RWF" {{ old('currency') === 'RWF' ? 'selected' : '' }}>RWF - Rwandan Franc</option>
                        <option value="KES" {{ old('currency') === 'KES' ? 'selected' : '' }}>KES - Kenyan Shilling</option>
                    </select>
                </div>

                <div id="maxDiscountField">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Maximum Discount Amount
                    </label>
                    <input type="number" name="max_discount_amount" value="{{ old('max_discount_amount') }}" min="0" step="0.01"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-gray-900"
                        placeholder="Leave empty for unlimited">
                    <p class="mt-1 text-xs text-gray-500">Only for percentage discounts</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Minimum Purchase Amount
                    </label>
                    <input type="number" name="min_purchase_amount" value="{{ old('min_purchase_amount') }}" min="0" step="0.01"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-gray-900"
                        placeholder="0.00">
                </div>
            </div>
        </div>

        <!-- Usage Limits -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                <i class="fas fa-users text-red-600"></i>
                Usage Limits
            </h2>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Total Usage Limit
                    </label>
                    <input type="number" name="usage_limit" value="{{ old('usage_limit') }}" min="1"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-gray-900"
                        placeholder="Leave empty for unlimited">
                    <p class="mt-1 text-xs text-gray-500">Maximum times this code can be used in total</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Per User Limit
                    </label>
                    <input type="number" name="user_usage_limit" value="{{ old('user_usage_limit') }}" min="1"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-gray-900"
                        placeholder="Leave empty for unlimited">
                    <p class="mt-1 text-xs text-gray-500">Maximum times one user can use this code</p>
                </div>
            </div>
        </div>

        <!-- Validity Period -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                <i class="fas fa-calendar text-red-600"></i>
                Validity Period
            </h2>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Start Date <span class="text-red-600">*</span>
                    </label>
                    <input type="datetime-local" name="start_date" value="{{ old('start_date') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-gray-900">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        End Date <span class="text-red-600">*</span>
                    </label>
                    <input type="datetime-local" name="end_date" value="{{ old('end_date') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-gray-900">
                </div>
            </div>
        </div>

        <!-- Applicable Products -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                <i class="fas fa-box text-red-600"></i>
                Applicable Products
            </h2>

            <div class="mb-6">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                    Apply To <span class="text-red-600">*</span>
                </label>
                <div class="space-y-3">
                    <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="radio" name="applicable_to" value="all" {{ old('applicable_to', 'all') === 'all' ? 'checked' : '' }}
                            class="w-4 h-4 text-red-600 focus:ring-red-500" onchange="toggleProductSelection()">
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-900">All Products</span>
                            <p class="text-xs text-gray-500">This promo code will apply to all your products</p>
                        </div>
                    </label>
                    <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="radio" name="applicable_to" value="specific_products" {{ old('applicable_to') === 'specific_products' ? 'checked' : '' }}
                            class="w-4 h-4 text-red-600 focus:ring-red-500" onchange="toggleProductSelection()">
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-900">Specific Products</span>
                            <p class="text-xs text-gray-500">Choose which products this code applies to</p>
                        </div>
                    </label>
                </div>
            </div>

            <div id="productSelection" style="display: none;">
                @if($products->count() > 0)
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($products as $product)
                            <label class="flex items-start p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                <input type="checkbox" name="products[]" value="{{ $product->id }}"
                                    {{ (old('products') && in_array($product->id, old('products'))) ? 'checked' : '' }}
                                    class="mt-1 w-4 h-4 text-red-600 focus:ring-red-500">
                                <div class="ml-3 flex-1">
                                    <span class="text-sm font-medium text-gray-900 block">{{ $product->name }}</span>
                                    @if($product->productCategory)
                                        <span class="text-xs text-gray-500">{{ $product->productCategory->name }}</span>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 bg-gray-50 rounded-lg border border-gray-200">
                        <i class="fas fa-box-open text-2xl text-gray-400 mb-2"></i>
                        <p class="text-sm text-gray-500">No active products available</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex gap-4 justify-end items-center">
            <a href="{{ route('vendor.promo-code.index') }}" class="inline-flex items-center gap-2 px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition-colors">
                <i class="fas fa-times"></i>
                <span>Cancel</span>
            </a>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-[#ff0808] text-white rounded-lg hover:bg-[#e60707] transition-colors font-semibold shadow-md">
                <i class="fas fa-save"></i>
                <span>Create Promo Code</span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function toggleProductSelection() {
        const applicableTo = document.querySelector('input[name="applicable_to"]:checked').value;
        const productSelection = document.getElementById('productSelection');

        if (applicableTo === 'specific_products') {
            productSelection.style.display = 'block';
        } else {
            productSelection.style.display = 'none';
        }
    }

    function toggleMaxDiscount() {
        const discountType = document.getElementById('discountType').value;
        const maxDiscountField = document.getElementById('maxDiscountField');

        if (discountType === 'percentage') {
            maxDiscountField.style.display = 'block';
        } else {
            maxDiscountField.style.display = 'none';
            maxDiscountField.querySelector('input').value = '';
        }
    }

    document.getElementById('discountType').addEventListener('change', toggleMaxDiscount);

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleProductSelection();
        toggleMaxDiscount();
    });
</script>
@endpush
@endsection

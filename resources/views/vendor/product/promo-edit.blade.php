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
                <h1 class="text-2xl font-bold text-gray-900">Manage Promo Codes</h1>
                <p class="mt-1 text-sm text-gray-500">Configure applicable promo codes for {{ $product->name }}</p>
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
    <div class="bg-gradient-to-r from-red-50 to-orange-50 rounded-xl border border-red-200 shadow-sm p-6">
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
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-white border border-red-200 text-red-700">
                            <i class="fas fa-tag mr-1"></i> {{ $product->productCategory->name }}
                        </span>
                    @endif
                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-white border border-red-200 text-red-700 capitalize">
                        <i class="fas fa-circle mr-1 text-[6px]"></i> {{ $product->status }}
                    </span>
                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-white border border-red-200 text-red-700">
                        <i class="fas fa-ticket-alt mr-1"></i> {{ count($assignedPromoCodeIds) }} Promo Codes
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Promo Codes Form -->
    <form action="{{ route('vendor.product.promo.update', $product) }}" method="POST" id="promoCodesForm">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="mb-6">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2 mb-2">
                    <i class="fas fa-ticket-alt text-red-600"></i>
                    Available Promo Codes
                </h2>
                <p class="text-sm text-gray-500">Select which promo codes customers can use with this product. Leave all unchecked if all promo codes should apply.</p>
            </div>

            @if($availablePromoCodes && $availablePromoCodes->count() > 0)
                <!-- Select/Deselect All -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Quick Actions:</span>
                        <div class="flex gap-3">
                            <button type="button" onclick="selectAll()" class="text-sm font-medium text-red-600 hover:text-red-700 transition-colors">
                                <i class="fas fa-check-square mr-1"></i> Select All
                            </button>
                            <button type="button" onclick="deselectAll()" class="text-sm font-medium text-gray-600 hover:text-gray-700 transition-colors">
                                <i class="fas fa-square mr-1"></i> Deselect All
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Promo Codes Grid -->
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($availablePromoCodes as $promo)
                        <label class="relative flex cursor-pointer group">
                            <input type="checkbox"
                                   name="promo_codes[]"
                                   value="{{ $promo->id }}"
                                   {{ in_array($promo->id, $assignedPromoCodeIds) ? 'checked' : '' }}
                                   class="peer sr-only promo-checkbox">

                            <div class="w-full p-5 bg-white rounded-xl border-2 border-gray-200 transition-all peer-checked:border-red-500 peer-checked:bg-red-50 hover:border-gray-300 hover:shadow-md">
                                <div class="flex items-start justify-between mb-3">
                                    <span class="px-3 py-1.5 bg-red-600 text-white text-sm font-bold rounded-md shadow-sm">
                                        {{ $promo->code }}
                                    </span>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $promo->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($promo->status) }}
                                        </span>
                                        <i class="fas fa-check-circle text-red-500 text-lg opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                    </div>
                                </div>

                                <p class="text-sm text-gray-700 mb-4 line-clamp-2 min-h-[2.5rem]">{{ $promo->description }}</p>

                                <div class="space-y-2 text-xs text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-percentage text-red-600 w-4"></i>
                                        <span class="font-medium">
                                            @if($promo->discount_type === 'percentage')
                                                {{ $promo->discount_value }}% off
                                                @if($promo->max_discount_amount)
                                                    <span class="text-gray-500">(max {{ $promo->currency }} {{ number_format($promo->max_discount_amount, 2) }})</span>
                                                @endif
                                            @else
                                                {{ $promo->currency }} {{ number_format($promo->discount_value, 2) }} off
                                            @endif
                                        </span>
                                    </div>

                                    @if($promo->min_purchase_amount)
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-shopping-cart text-red-600 w-4"></i>
                                        <span>Min purchase: {{ $promo->currency }} {{ number_format($promo->min_purchase_amount, 2) }}</span>
                                    </div>
                                    @endif

                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-calendar text-red-600 w-4"></i>
                                        <span>Valid until {{ $promo->end_date->format('M d, Y') }}</span>
                                    </div>

                                    @if($promo->usage_limit)
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-users text-red-600 w-4"></i>
                                        <span>{{ $promo->usage_count }}/{{ $promo->usage_limit }} used</span>
                                    </div>
                                    @endif

                                    @if($promo->applicable_to !== 'all')
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-filter text-red-600 w-4"></i>
                                        <span class="capitalize">{{ str_replace('_', ' ', $promo->applicable_to) }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>

                <!-- Info Box -->
                <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                        <div class="text-sm text-blue-900">
                            <p class="font-medium mb-1">How it works:</p>
                            <ul class="list-disc list-inside space-y-1 text-blue-800">
                                <li>If no promo codes are selected, all active promo codes will apply to this product</li>
                                <li>If you select specific codes, only those codes will work with this product</li>
                                <li>Customers can only use one promo code per order</li>
                                <li>Only active promo codes within their validity period are shown here</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-16 bg-gray-50 rounded-xl border-2 border-dashed border-gray-300">
                    <i class="fas fa-ticket-alt text-5xl text-gray-400 mb-4"></i>
                    <p class="text-lg text-gray-600 font-medium mb-2">No Active Promo Codes Available</p>
                    <p class="text-sm text-gray-500 mb-4">Create promo codes first to assign them to products</p>
                    <a href="{{ route('vendor.promo-code.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                        <i class="fas fa-plus"></i>
                        <span>Create Promo Code</span>
                    </a>
                </div>
            @endif
        </div>

        <!-- Form Actions -->
        <div class="flex gap-4 justify-end items-center pt-6">
            <a href="{{ route('vendor.product.show', $product) }}" class="inline-flex items-center gap-2 px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition-colors">
                <i class="fas fa-times"></i>
                <span>Cancel</span>
            </a>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-[#ff0808] text-white rounded-lg hover:bg-[#e60707] transition-colors font-semibold shadow-md">
                <i class="fas fa-save"></i>
                <span>Save Promo Codes</span>
            </button>
        </div>
    </form>
</div>

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush

@push('scripts')
<script>
    function selectAll() {
        document.querySelectorAll('.promo-checkbox').forEach(checkbox => {
            checkbox.checked = true;
        });
    }

    function deselectAll() {
        document.querySelectorAll('.promo-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
    }

    // Form submission handler
    document.getElementById('promoCodesForm').addEventListener('submit', function(e) {
        const selectedCount = document.querySelectorAll('.promo-checkbox:checked').length;
        const totalCount = document.querySelectorAll('.promo-checkbox').length;

        if (selectedCount === 0 && totalCount > 0) {
            const confirmed = confirm('No promo codes selected. This means ALL promo codes will apply to this product. Continue?');
            if (!confirmed) {
                e.preventDefault();
                return false;
            }
        }
    });
</script>
@endpush
@endsection

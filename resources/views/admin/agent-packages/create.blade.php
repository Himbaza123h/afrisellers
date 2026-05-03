@extends('layouts.home')

@section('page-content')
<div class="max-w-6xl mx-auto space-y-4">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.agent-packages.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Create Agent Package</h1>
            <p class="text-xs text-gray-500 mt-0.5">Define a new subscription plan for agents</p>
        </div>
    </div>

    <form action="{{ route('admin.agent-packages.store') }}" method="POST" class="space-y-4">
        @csrf

        {{-- Basic Info --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
            <h2 class="text-sm font-bold text-gray-800 border-b border-gray-100 pb-3">Basic Information</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Package Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-400 focus:border-transparent @error('name') border-red-400 @enderror"
                        placeholder="e.g. Premium Agent">
                    @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Billing Cycle <span class="text-red-500">*</span></label>
                    <select name="billing_cycle"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-400 @error('billing_cycle') border-red-400 @enderror">
                        <option value="monthly"   {{ old('billing_cycle') === 'monthly'   ? 'selected' : '' }}>Monthly</option>
                        <option value="quarterly" {{ old('billing_cycle') === 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                        <option value="yearly"    {{ old('billing_cycle') === 'yearly'    ? 'selected' : '' }}>Yearly</option>
                    </select>
                    @error('billing_cycle')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Description</label>
                <textarea name="description" rows="2"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-400 @error('description') border-red-400 @enderror"
                    placeholder="Brief description shown to agents...">{{ old('description') }}</textarea>
                @error('description')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Price ($) <span class="text-red-500">*</span></label>
                    <input type="number" name="price" value="{{ old('price', '0.00') }}" step="0.01" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-400 @error('price') border-red-400 @enderror">
                    @error('price')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Duration (Days) <span class="text-red-500">*</span></label>
                    <input type="number" name="duration_days" value="{{ old('duration_days', 30) }}" min="1"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-400 @error('duration_days') border-red-400 @enderror">
                    @error('duration_days')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-400">
                </div>
            </div>
        </div>

        {{-- Limits --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
            <h2 class="text-sm font-bold text-gray-800 border-b border-gray-100 pb-3">Usage Limits</h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Max Referrals <span class="text-red-500">*</span></label>
                    <input type="number" name="max_referrals" value="{{ old('max_referrals', 5) }}" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-400 @error('max_referrals') border-red-400 @enderror">
                    @error('max_referrals')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Max Vendors <span class="text-red-500">*</span></label>
                    <input type="number" name="max_vendors" value="{{ old('max_vendors', 5) }}" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-400 @error('max_vendors') border-red-400 @enderror">
                    @error('max_vendors')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror {{-- NEW FIELD --}}
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Max Payouts / Month <span class="text-red-500">*</span></label>
                    <input type="number" name="max_payouts_per_month" value="{{ old('max_payouts_per_month', 2) }}" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-400 @error('max_payouts_per_month') border-red-400 @enderror">
                    @error('max_payouts_per_month')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Features --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
            <h2 class="text-sm font-bold text-gray-800 border-b border-gray-100 pb-3">Features & Perks</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Commission boost --}}
                <div class="flex items-start gap-3 p-3 rounded-lg border border-gray-200">
                    <input type="hidden" name="commission_boost" value="0">
                    <input type="checkbox" id="commission_boost" name="commission_boost" value="1"
                        {{ old('commission_boost') ? 'checked' : '' }}
                        class="mt-0.5 w-4 h-4 text-red-600 rounded"
                        onchange="document.getElementById('commission_rate_wrap').classList.toggle('hidden', !this.checked)">
                    <div>
                        <label for="commission_boost" class="text-sm font-semibold text-gray-700 cursor-pointer">Commission Boost</label>
                        <p class="text-xs text-gray-400">Enable a custom commission rate for this plan</p>
                    </div>
                </div>

                {{-- Priority support --}}
                <div class="flex items-start gap-3 p-3 rounded-lg border border-gray-200">
                    <input type="hidden" name="priority_support" value="0">
                    <input type="checkbox" id="priority_support" name="priority_support" value="1"
                        {{ old('priority_support') ? 'checked' : '' }}
                        class="mt-0.5 w-4 h-4 text-red-600 rounded">
                    <div>
                        <label for="priority_support" class="text-sm font-semibold text-gray-700 cursor-pointer">Priority Support</label>
                        <p class="text-xs text-gray-400">Agent gets faster support response</p>
                    </div>
                </div>

                {{-- Allow RFQs --}}
                <div class="flex items-start gap-3 p-3 rounded-lg border border-gray-200">
                    <input type="hidden" name="allow_rfqs" value="0">
                    <input type="checkbox" id="allow_rfqs" name="allow_rfqs" value="1"
                        {{ old('allow_rfqs') ? 'checked' : '' }}
                        class="mt-0.5 w-4 h-4 text-red-600 rounded">
                    <div>
                        <label for="allow_rfqs" class="text-sm font-semibold text-gray-700 cursor-pointer">Allow RFQs</label>
                        <p class="text-xs text-gray-400">Agent can access RFQ requests</p>
                    </div>
                </div>

                {{-- Advanced analytics --}}
                <div class="flex items-start gap-3 p-3 rounded-lg border border-gray-200">
                    <input type="hidden" name="advanced_analytics" value="0">
                    <input type="checkbox" id="advanced_analytics" name="advanced_analytics" value="1"
                        {{ old('advanced_analytics') ? 'checked' : '' }}
                        class="mt-0.5 w-4 h-4 text-red-600 rounded">
                    <div>
                        <label for="advanced_analytics" class="text-sm font-semibold text-gray-700 cursor-pointer">Advanced Analytics</label>
                        <p class="text-xs text-gray-400">Unlock detailed performance data</p>
                    </div>
                </div>

                {{-- Featured profile --}}
                <div class="flex items-start gap-3 p-3 rounded-lg border border-gray-200">
                    <input type="hidden" name="featured_profile" value="0">
                    <input type="checkbox" id="featured_profile" name="featured_profile" value="1"
                        {{ old('featured_profile') ? 'checked' : '' }}
                        class="mt-0.5 w-4 h-4 text-red-600 rounded">
                    <div>
                        <label for="featured_profile" class="text-sm font-semibold text-gray-700 cursor-pointer">Featured Profile</label>
                        <p class="text-xs text-gray-400">Agent appears in featured listings</p>
                    </div>
                </div>
            </div>

            {{-- Commission rate (shown when boost checked) --}}
            <div id="commission_rate_wrap" class="{{ old('commission_boost') ? '' : 'hidden' }}">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Commission Rate (%)</label>
                <input type="number" name="commission_rate" value="{{ old('commission_rate', 5) }}" step="0.01" min="0" max="100"
                    class="w-full sm:w-48 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-400">
            </div>
        </div>

        {{-- Visibility --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-sm font-bold text-gray-800 border-b border-gray-100 pb-3 mb-4">Visibility</h2>
            <div class="flex flex-wrap gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1"
                        {{ old('is_active', true) ? 'checked' : '' }}
                        class="w-4 h-4 text-red-600 rounded">
                    <span class="text-sm font-medium text-gray-700">Active (visible to agents)</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="is_featured" value="0">
                    <input type="checkbox" name="is_featured" value="1"
                        {{ old('is_featured') ? 'checked' : '' }}
                        class="w-4 h-4 text-red-600 rounded">
                    <span class="text-sm font-medium text-gray-700">Featured (highlighted on plans page)</span>
                </label>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.agent-packages.index') }}"
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit"
                class="px-5 py-2 bg-[#ff0808] text-white rounded-lg text-sm font-bold hover:bg-red-700 shadow">
                <i class="fas fa-save mr-1"></i> Create Package
            </button>
        </div>
    </form>
</div>

<script>
    // Keep commission rate visible on page load if checkbox is checked
    document.getElementById('commission_boost').addEventListener('change', function () {
        document.getElementById('commission_rate_wrap').classList.toggle('hidden', !this.checked);
    });
</script>
@endsection

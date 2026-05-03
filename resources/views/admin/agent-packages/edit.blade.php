@extends('layouts.home')

@section('page-content')
<div class="max-w-6xl mx-auto space-y-4">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.agent-packages.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Edit Package — {{ $agentPackage->name }}</h1>
            <p class="text-xs text-gray-500 mt-0.5">Update plan settings and feature flags</p>
        </div>
    </div>

    @if(session('error'))
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <p class="text-sm text-red-900 flex-1 font-medium">{{ session('error') }}</p>
        </div>
    @endif

    <form action="{{ route('admin.agent-packages.update', $agentPackage) }}" method="POST" class="space-y-4">
        @csrf @method('PUT')

        {{-- Basic Info --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
            <h2 class="text-sm font-bold text-gray-800 border-b border-gray-100 pb-3">Basic Information</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Package Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $agentPackage->name) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-400 @error('name') border-red-400 @enderror">
                    @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Billing Cycle <span class="text-red-500">*</span></label>
                    <select name="billing_cycle"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-400">
                        @foreach(['monthly','quarterly','yearly'] as $cycle)
                            <option value="{{ $cycle }}" {{ old('billing_cycle', $agentPackage->billing_cycle) === $cycle ? 'selected' : '' }}>
                                {{ ucfirst($cycle) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Description</label>
                <textarea name="description" rows="2"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-400">{{ old('description', $agentPackage->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Price ($) <span class="text-red-500">*</span></label>
                    <input type="number" name="price" value="{{ old('price', $agentPackage->price) }}" step="0.01" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Duration (Days) <span class="text-red-500">*</span></label>
                    <input type="number" name="duration_days" value="{{ old('duration_days', $agentPackage->duration_days) }}" min="1"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $agentPackage->sort_order) }}" min="0"
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
                    <input type="number" name="max_referrals" value="{{ old('max_referrals', $agentPackage->max_referrals) }}" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-400">
                </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Max Vendors <span class="text-red-500">*</span></label>
                        <input type="number" name="max_vendors" value="{{ old('max_vendors', $agentPackage->max_vendors) }}" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-400">
                    </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Max Payouts / Month <span class="text-red-500">*</span></label>
                    <input type="number" name="max_payouts_per_month" value="{{ old('max_payouts_per_month', $agentPackage->max_payouts_per_month) }}" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-400">
                </div>
            </div>
        </div>

        {{-- Features --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
            <h2 class="text-sm font-bold text-gray-800 border-b border-gray-100 pb-3">Features & Perks</h2>

            @php
                $boolFields = [
                    'commission_boost'   => ['label' => 'Commission Boost',    'desc' => 'Enable a custom commission rate'],
                    'priority_support'   => ['label' => 'Priority Support',    'desc' => 'Faster support response'],
                    'allow_rfqs'         => ['label' => 'Allow RFQs',          'desc' => 'Agent can access RFQ requests'],
                    'advanced_analytics' => ['label' => 'Advanced Analytics',  'desc' => 'Unlock detailed performance data'],
                    'featured_profile'   => ['label' => 'Featured Profile',    'desc' => 'Agent appears in featured listings'],
                ];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($boolFields as $field => $meta)
                    <div class="flex items-start gap-3 p-3 rounded-lg border border-gray-200">
                        <input type="hidden" name="{{ $field }}" value="0">
                        <input type="checkbox" id="{{ $field }}" name="{{ $field }}" value="1"
                            {{ old($field, $agentPackage->$field) ? 'checked' : '' }}
                            class="mt-0.5 w-4 h-4 text-red-600 rounded"
                            @if($field === 'commission_boost') onchange="document.getElementById('commission_rate_wrap').classList.toggle('hidden', !this.checked)" @endif>
                        <div>
                            <label for="{{ $field }}" class="text-sm font-semibold text-gray-700 cursor-pointer">{{ $meta['label'] }}</label>
                            <p class="text-xs text-gray-400">{{ $meta['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div id="commission_rate_wrap" class="{{ old('commission_boost', $agentPackage->commission_boost) ? '' : 'hidden' }}">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Commission Rate (%)</label>
                <input type="number" name="commission_rate" value="{{ old('commission_rate', $agentPackage->commission_rate) }}" step="0.01" min="0" max="100"
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
                        {{ old('is_active', $agentPackage->is_active) ? 'checked' : '' }}
                        class="w-4 h-4 text-red-600 rounded">
                    <span class="text-sm font-medium text-gray-700">Active</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="is_featured" value="0">
                    <input type="checkbox" name="is_featured" value="1"
                        {{ old('is_featured', $agentPackage->is_featured) ? 'checked' : '' }}
                        class="w-4 h-4 text-red-600 rounded">
                    <span class="text-sm font-medium text-gray-700">Featured</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.agent-packages.index') }}"
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">Cancel</a>
            <button type="submit"
                class="px-5 py-2 bg-[#ff0808] text-white rounded-lg text-sm font-bold hover:bg-red-700 shadow">
                <i class="fas fa-save mr-1"></i> Save Changes
            </button>
        </div>
    </form>
</div>
@endsection

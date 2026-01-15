@extends('layouts.home')

@section('page-content')
<div class="space-y-6 max-w-4xl">
    <!-- Page Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.memberships.plans.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <i class="fas fa-arrow-left text-gray-600"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Membership Plan</h1>
            <p class="mt-1 text-sm text-gray-500">Define a new subscription plan for sellers</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <form action="{{ route('admin.memberships.plans.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Basic Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Plan Name <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">e.g., Free Trial, Basic, Pro</p>
                    </div>

                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug <span class="text-red-500">*</span></label>
                        <input type="text" id="slug" name="slug" value="{{ old('slug') }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('slug') border-red-500 @enderror">
                        @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">e.g., free_trial, basic, pro</p>
                    </div>
                </div>
            </div>

            <!-- Pricing & Duration -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pricing & Duration</h3>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Price (USD) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                            <input type="number" id="price" name="price" value="{{ old('price', 0) }}" step="0.01" min="0" required class="w-full pl-8 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('price') border-red-500 @enderror">
                        </div>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Set to 0 for free plans</p>
                    </div>

                    <div>
                        <label for="duration_days" class="block text-sm font-medium text-gray-700 mb-2">Duration (Days) <span class="text-red-500">*</span></label>
                        <input type="number" id="duration_days" name="duration_days" value="{{ old('duration_days', 30) }}" min="1" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('duration_days') border-red-500 @enderror">
                        @error('duration_days')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Subscription validity period</p>
                    </div>
                </div>
            </div>

            <!-- Display Settings -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Display Settings</h3>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="display_order" class="block text-sm font-medium text-gray-700 mb-2">Display Order</label>
                        <input type="number" id="display_order" name="display_order" value="{{ old('display_order', 0) }}" min="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('display_order') border-red-500 @enderror">
                        @error('display_order')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Lower numbers appear first</p>
                    </div>

                    <div class="flex items-center h-full pt-8">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" id="is_active" name="is_active" {{ old('is_active', true) ? 'checked' : '' }} class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <div>
                                <span class="text-sm font-medium text-gray-900">Active Plan</span>
                                <p class="text-xs text-gray-500">Make this plan available for purchase</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t">
                <a href="{{ route('admin.memberships.plans.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#ff0808] hover:bg-[#e60707] text-white rounded-lg transition-all font-medium shadow-sm">
                    <i class="fas fa-check"></i>
                    <span>Create Plan</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Info Card -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="flex gap-3">
            <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
            <div class="flex-1">
                <h4 class="text-sm font-semibold text-blue-900 mb-1">After Creating the Plan</h4>
                <p class="text-sm text-blue-700">Once the plan is created, you'll be able to add features and configure limits for this plan.</p>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-generate slug from name
document.getElementById('name').addEventListener('input', function(e) {
    const slug = e.target.value
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '_')
        .replace(/^_+|_+$/g, '');
    document.getElementById('slug').value = slug;
});
</script>
@endsection

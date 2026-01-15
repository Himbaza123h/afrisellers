@extends('layouts.home')

@section('page-content')
<div class="space-y-6 max-w-4xl">
    <!-- Page Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.memberships.plans.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <i class="fas fa-arrow-left text-gray-600"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Membership Plan</h1>
            <p class="mt-1 text-sm text-gray-500">Update subscription plan details</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <form action="{{ route('admin.memberships.plans.update', $membershipPlan) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Plan Name <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $membershipPlan->name) }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug <span class="text-red-500">*</span></label>
                        <input type="text" id="slug" name="slug" value="{{ old('slug', $membershipPlan->slug) }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('slug') border-red-500 @enderror">
                        @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
                            <input type="number" id="price" name="price" value="{{ old('price', $membershipPlan->price) }}" step="0.01" min="0" required class="w-full pl-8 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('price') border-red-500 @enderror">
                        </div>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="duration_days" class="block text-sm font-medium text-gray-700 mb-2">Duration (Days) <span class="text-red-500">*</span></label>
                        <input type="number" id="duration_days" name="duration_days" value="{{ old('duration_days', $membershipPlan->duration_days) }}" min="1" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('duration_days') border-red-500 @enderror">
                        @error('duration_days')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Display Settings -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Display Settings</h3>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="display_order" class="block text-sm font-medium text-gray-700 mb-2">Display Order</label>
                        <input type="number" id="display_order" name="display_order" value="{{ old('display_order', $membershipPlan->display_order) }}" min="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('display_order') border-red-500 @enderror">
                        @error('display_order')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center h-full pt-8">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" id="is_active" name="is_active" {{ old('is_active', $membershipPlan->is_active) ? 'checked' : '' }} class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <div>
                                <span class="text-sm font-medium text-gray-900">Active Plan</span>
                                <p class="text-xs text-gray-500">Make this plan available for purchase</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="p-4 bg-purple-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-users text-purple-600 text-2xl"></i>
                            <div>
                                <p class="text-sm text-purple-600 font-medium">Active Subscriptions</p>
                                <p class="text-2xl font-bold text-purple-900">{{ $membershipPlan->subscriptions()->where('status', 'active')->count() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-list text-blue-600 text-2xl"></i>
                            <div>
                                <p class="text-sm text-blue-600 font-medium">Total Features</p>
                                <p class="text-2xl font-bold text-blue-900">{{ $membershipPlan->features()->count() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 bg-green-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-calendar text-green-600 text-2xl"></i>
                            <div>
                                <p class="text-sm text-green-600 font-medium">Created</p>
                                <p class="text-sm font-bold text-green-900">{{ $membershipPlan->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t">
                <a href="{{ route('admin.memberships.plans.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </a>
                <a href="{{ route('admin.memberships.features.index', $membershipPlan) }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-all font-medium">
                    <i class="fas fa-list"></i>
                    <span>Manage Features</span>
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#ff0808] hover:bg-[#e60707] text-white rounded-lg transition-all font-medium shadow-sm">
                    <i class="fas fa-save"></i>
                    <span>Update Plan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

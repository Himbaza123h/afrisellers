@extends('layouts.home')

@section('page-content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-black text-gray-900 uppercase lg:text-lg">Store Settings</h1>
            <p class="mt-1 text-sm text-gray-600">Manage your business information and view statistics</p>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
    <div class="p-4 mb-6 bg-green-50 rounded-md border border-green-300">
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
@endif

@if($errors->any())
    <div class="p-4 mb-6 bg-red-50 rounded-md border border-red-300">
        <p class="mb-2 text-sm font-medium text-red-900">Please fix the following errors:</p>
        <ul class="space-y-1 text-sm text-red-700">
            @foreach($errors->all() as $error)
                <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- Statistics Cards -->
    <div class="lg:col-span-3">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 mb-6">
            <!-- Total Products -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600 uppercase tracking-wide">Total Products</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_products'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-md flex items-center justify-center">
                        <i class="fas fa-box text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Active Products -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600 uppercase tracking-wide">Active</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['active_products'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-md flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Pending Products -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600 uppercase tracking-wide">Pending</p>
                        <p class="text-2xl font-bold text-orange-600 mt-1">{{ $stats['pending_products'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-md flex items-center justify-center">
                        <i class="fas fa-clock text-orange-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Views -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600 uppercase tracking-wide">Total Views</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($stats['total_views']) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-md flex items-center justify-center">
                        <i class="fas fa-eye text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Inquiries -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600 uppercase tracking-wide">Inquiries</p>
                        <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $stats['total_inquiries'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-100 rounded-md flex items-center justify-center">
                        <i class="fas fa-envelope text-indigo-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Account Age -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 uppercase tracking-wide">Account Age</p>
                    @php
                        $accountYears = round($stats['account_age_days'] / 365.25, 1);
                    @endphp
                    @if($accountYears < 1)
                        <p class="text-xl font-bold text-gray-900 mt-1">Less than</p>
                        <p class="text-xs text-gray-500">a year</p>
                    @else
                        <p class="text-xl font-bold text-gray-900 mt-1">{{ $accountYears }}</p>
                        <p class="text-xs text-gray-500">{{ $accountYears == 1.0 ? 'year' : 'years' }}</p>
                    @endif
                </div>
                    <div class="w-12 h-12 bg-gray-100 rounded-md flex items-center justify-center">
                        <i class="fas fa-calendar text-gray-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Reviews -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600 uppercase tracking-wide">Total Reviews</p>
                        <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['total_reviews'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-md flex items-center justify-center">
                        <i class="fas fa-star text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>

    <!-- Average Rating -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600 uppercase tracking-wide">Avg Rating</p>
                        <p class="text-2xl font-bold text-yellow-600 mt-1">{{ number_format($stats['average_rating'], 1) }}</p>
                        <p class="text-xs text-gray-500">out of 5</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-md flex items-center justify-center">
                        <i class="fas fa-star-half-alt text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
          <!-- Total Impressions -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 uppercase tracking-wide">Total Impressions</p>
                    <p class="text-2xl font-bold text-cyan-600 mt-1">{{ number_format($stats['total_impressions']) }}</p>
                </div>
                <div class="w-12 h-12 bg-cyan-100 rounded-md flex items-center justify-center">
                    <i class="fas fa-chart-line text-cyan-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Clicks -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 uppercase tracking-wide">Total Clicks</p>
                    <p class="text-2xl font-bold text-teal-600 mt-1">{{ number_format($stats['total_clicks']) }}</p>
                </div>
                <div class="w-12 h-12 bg-teal-100 rounded-md flex items-center justify-center">
                    <i class="fas fa-mouse-pointer text-teal-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Click-Through Rate -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 uppercase tracking-wide">CTR</p>
                    @php
                        $ctr = $stats['total_impressions'] > 0 ? ($stats['total_clicks'] / $stats['total_impressions']) * 100 : 0;
                    @endphp
                    <p class="text-2xl font-bold text-pink-600 mt-1">{{ number_format($ctr, 2) }}%</p>
                </div>
                <div class="w-12 h-12 bg-pink-100 rounded-md flex items-center justify-center">
                    <i class="fas fa-percentage text-pink-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Avg Clicks per Product -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 uppercase tracking-wide">Avg Clicks/Product</p>
                    @php
                        $avgClicks = $stats['total_products'] > 0 ? $stats['total_clicks'] / $stats['total_products'] : 0;
                    @endphp
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($avgClicks, 1) }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-md flex items-center justify-center">
                    <i class="fas fa-chart-bar text-emerald-600 text-xl"></i>
                </div>
            </div>
        </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Business Information Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Business Information</h2>
            </div>
            <div class="p-6">
                <form action="{{ route('vendor.store.update-settings') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Business Logo -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Business Logo</label>
                        <div class="flex items-center gap-4">
                            @if($vendor->businessProfile->logo)
                                <img src="{{ Storage::url($vendor->businessProfile->logo) }}"
                                     alt="Business Logo"
                                     class="w-20 h-20 rounded-md object-cover border border-gray-200">
                            @else
                                <div class="w-20 h-20 rounded-md bg-gray-100 flex items-center justify-center border border-gray-200">
                                    <i class="fas fa-store text-gray-400 text-2xl"></i>
                                </div>
                            @endif
                            <div>
                                <input type="file"
                                       name="business_logo"
                                       id="business_logo"
                                       accept="image/*"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                                <p class="text-xs text-gray-500 mt-1">JPG, PNG. Max 2MB</p>
                            </div>
                        </div>
                        @error('business_logo')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="business_name" class="block text-sm font-medium text-gray-700 mb-2">Business Name *</label>
                            <input type="text"
                                   name="business_name"
                                   id="business_name"
                                   value="{{ old('business_name', $vendor->businessProfile->business_name) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                                   required>
                            @error('business_name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="business_email" class="block text-sm font-medium text-gray-700 mb-2">Business Email *</label>
                            <input type="email"
                                   name="business_email"
                                   id="business_email"
                                   value="{{ old('business_email', $vendor->businessProfile->business_email) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                                   required>
                            @error('business_email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 mt-6">
                        <div>
                            <label for="phone_code" class="block text-sm font-medium text-gray-700 mb-2">Phone Code</label>
                            <input type="text"
                                   name="phone_code"
                                   id="phone_code"
                                   value="{{ old('phone_code', $vendor->businessProfile->phone_code ?? '+250') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                                   placeholder="+250">
                            @error('phone_code')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                            <input type="text"
                                   name="phone"
                                   id="phone"
                                   value="{{ old('phone', $vendor->businessProfile->phone) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                                   required>
                            @error('phone')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Business Address *</label>
                        <textarea name="address"
                                  id="address"
                                  rows="2"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                                  required>{{ old('address', $vendor->businessProfile->address) }}</textarea>
                        @error('address')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3 mt-6">
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                            <input type="text"
                                   name="city"
                                   id="city"
                                   value="{{ old('city', $vendor->businessProfile->city) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                            @error('city')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="country_id" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                            <select name="country_id" id="country_id"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                                <option value="">Select Country</option>
                                @php
                                    $countries = \App\Models\Country::where('status', 'active')->orderBy('name')->get();
                                @endphp
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ old('country_id', $vendor->businessProfile->country_id) == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('country_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                            <input type="text"
                                   name="postal_code"
                                   id="postal_code"
                                   value="{{ old('postal_code', $vendor->businessProfile->postal_code) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                            @error('postal_code')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="website" class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                        <input type="url"
                               name="website"
                               id="website"
                               value="{{ old('website', $vendor->businessProfile->website) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                               placeholder="https://example.com">
                        @error('website')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                <div class="mt-6">
                        <label for="youtube_link" class="block text-sm font-medium text-gray-700 mb-2">Youtube Video</label>
                        <input type="url"
                               name="youtube_link"
                               id="youtube_link"
                               value="{{ old('youtube_link', $vendor->businessProfile->youtube_link) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent"
                               placeholder="https://www.youtube.com/">
                        @error('youtube_link')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Business Description</label>
                        <textarea name="description"
                                  id="description"
                                  rows="4"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                                  placeholder="Tell customers about your business...">{{ old('description', $vendor->businessProfile->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-6">
                        <button type="submit"
                                class="px-6 py-2.5 bg-[#ff0808] text-white font-semibold rounded-md hover:bg-purple-700 transition-colors">
                            Update Store Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Account Status Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Account Status</h2>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Verification Status</label>
                    <p class="mt-1 text-sm">
                        @if($vendor->verification_status === 'verified')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="mr-1 fas fa-check-circle"></i> Verified
                            </span>
                        @elseif($vendor->verification_status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                <i class="mr-1 fas fa-clock"></i> Pending
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="mr-1 fas fa-times-circle"></i> Rejected
                            </span>
                        @endif
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Account Status</label>
                    <p class="mt-1 text-sm">
                        @if($vendor->account_status === 'active')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="mr-1 fas fa-check"></i> Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="mr-1 fas fa-ban"></i> Suspended
                            </span>
                        @endif
                    </p>
                </div>

                @if($vendor->plan)
                <div>
                    <label class="block text-sm font-medium text-gray-700">Current Plan</label>
                    <p class="mt-1 text-sm font-semibold text-purple-600">{{ $vendor->plan->name }}</p>
                </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700">Member Since</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $vendor->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Quick Actions</h2>
            </div>
            <div class="p-4 space-y-2">
                <a href="{{ route('vendor.product.create') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-purple-50 rounded-md transition-colors">
                    <i class="fas fa-plus-circle w-5 text-center text-purple-600"></i>
                    <span class="text-sm font-medium">Add New Product</span>
                </a>
                <a href="{{ route('vendor.product.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-purple-50 rounded-md transition-colors">
                    <i class="fas fa-box w-5 text-center text-purple-600"></i>
                    <span class="text-sm font-medium">My Products</span>
                </a>
                <a href="{{ route('profile.show') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-purple-50 rounded-md transition-colors">
                    <i class="fas fa-user w-5 text-center text-purple-600"></i>
                    <span class="text-sm font-medium">Personal Profile</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

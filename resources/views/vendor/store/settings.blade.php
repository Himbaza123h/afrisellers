@extends('layouts.home')

@section('page-content')
<div class="space-y-4">
    <!-- Page Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Store Settings</h1>
            <p class="mt-1 text-xs text-gray-500">Manage your business information and view statistics</p>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-md border border-green-300">
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-red-50 rounded-md border border-red-300">
            <p class="mb-2 text-sm font-medium text-red-900">Please fix the following errors:</p>
            <ul class="space-y-1 text-sm text-red-700">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Tab Navigation -->
    <div class="flex gap-2 border-b border-gray-200">
        <button onclick="switchTab('all')" id="tab-all" class="tab-button px-4 py-2 text-sm font-semibold text-blue-600 border-b-2 border-blue-600 transition-colors">
            All
        </button>
        <button onclick="switchTab('statistics')" id="tab-statistics" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            Statistics
        </button>
        <button onclick="switchTab('settings')" id="tab-settings" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            Settings
        </button>
    </div>

    <!-- Statistics Section -->
    <div id="statistics-section" class="stats-container">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Products -->
            <div class="stat-card p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg border border-purple-200">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-box text-white text-lg"></i>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800">
                        Total
                    </span>
                </div>
                <p class="text-xs font-medium text-purple-900 mt-3 mb-1">Total Products</p>
                <p class="text-lg font-bold text-purple-900">{{ $stats['total_products'] }}</p>
                <p class="text-xs text-purple-700 mt-1">All products</p>
            </div>

            <!-- Active Products -->
            <div class="stat-card p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-lg border border-green-200">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-white text-lg"></i>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                        Active
                    </span>
                </div>
                <p class="text-xs font-medium text-green-900 mt-3 mb-1">Active Products</p>
                <p class="text-lg font-bold text-green-900">{{ $stats['active_products'] }}</p>
                <p class="text-xs text-green-700 mt-1">Currently visible</p>
            </div>

            <!-- Pending Products -->
            <div class="stat-card p-4 bg-gradient-to-br from-amber-50 to-amber-100 rounded-lg border border-amber-200">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-white text-lg"></i>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                        Pending
                    </span>
                </div>
                <p class="text-xs font-medium text-amber-900 mt-3 mb-1">Pending Products</p>
                <p class="text-lg font-bold text-amber-900">{{ $stats['pending_products'] }}</p>
                <p class="text-xs text-amber-700 mt-1">Awaiting approval</p>
            </div>

            <!-- Total Views -->
            <div class="stat-card p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg border border-blue-200">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-eye text-white text-lg"></i>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                        Views
                    </span>
                </div>
                <p class="text-xs font-medium text-blue-900 mt-3 mb-1">Total Views</p>
                <p class="text-lg font-bold text-blue-900">{{ number_format($stats['total_views']) }}</p>
                <p class="text-xs text-blue-700 mt-1">Product views</p>
            </div>
        </div>

        <!-- Additional Statistics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-envelope text-indigo-600 text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600">Total Inquiries</p>
                        <p class="text-lg font-bold text-gray-900">{{ $stats['total_inquiries'] }}</p>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-star text-yellow-600 text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600">Total Reviews</p>
                        <p class="text-lg font-bold text-gray-900">{{ $stats['total_reviews'] }}</p>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-star-half-alt text-yellow-600 text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600">Average Rating</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['average_rating'], 1) }}/5</p>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-cyan-600 text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600">Total Impressions</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_impressions']) }}</p>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-mouse-pointer text-teal-600 text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600">Total Clicks</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_clicks']) }}</p>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-percentage text-pink-600 text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600">Click-Through Rate</p>
                        @php
                            $ctr = $stats['total_impressions'] > 0 ? ($stats['total_clicks'] / $stats['total_impressions']) * 100 : 0;
                        @endphp
                        <p class="text-lg font-bold text-gray-900">{{ number_format($ctr, 2) }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Information Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <!-- Account Status -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Account Status</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-shield-alt text-blue-600 text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-900">Verification Status</p>
                                @if($vendor->verification_status === 'verified')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                        <i class="mr-1 fas fa-check-circle"></i> Verified
                                    </span>
                                @elseif($vendor->verification_status === 'pending')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-800">
                                        <i class="mr-1 fas fa-clock"></i> Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                        <i class="mr-1 fas fa-times-circle"></i> Rejected
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check text-green-600 text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-900">Account Status</p>
                                @if($vendor->account_status === 'active')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                        <i class="mr-1 fas fa-check"></i> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                        <i class="mr-1 fas fa-ban"></i> Suspended
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Account Information</h3>
                <div class="space-y-3">
                    @if($vendor->plan)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-crown text-purple-600 text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-900">Current Plan</p>
                                <p class="text-sm font-semibold text-purple-600">{{ $vendor->plan->name }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-calendar text-gray-600 text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-900">Member Since</p>
                                @php
                                    $accountYears = round($stats['account_age_days'] / 365.25, 1);
                                @endphp
                                @if($accountYears < 1)
                                    <p class="text-sm font-semibold text-gray-900">Less than a year</p>
                                @else
                                    <p class="text-sm font-semibold text-gray-900">{{ $accountYears }} {{ $accountYears == 1.0 ? 'year' : 'years' }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Section -->
    <div id="settings-section" class="settings-container">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Business Information Card -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-base font-semibold text-gray-900">Business Information</h2>
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

            <!-- Quick Actions Sidebar -->
            <div class="space-y-4">
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-base font-semibold text-gray-900">Quick Actions</h2>
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
                </div
                </div>
                </div>
@endsection

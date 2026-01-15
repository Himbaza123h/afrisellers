@extends('layouts.home')

@section('page-content')
<!-- Header Section -->
<div class="mb-4 sm:mb-6 lg:mb-8">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-black text-gray-900 uppercase sm:text-2xl lg:text-lg">My Profile</h1>
            <p class="mt-1 text-xs text-gray-600 sm:text-sm">View and manage your profile information</p>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
    <div class="p-4 mb-4 bg-green-50 rounded-lg border border-green-300">
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
@endif

@if($errors->any())
    <div class="p-4 mb-4 bg-red-50 rounded-lg border border-red-300">
        <p class="mb-2 text-sm font-medium text-red-900">Please fix the following errors:</p>
        <ul class="space-y-1 text-sm text-red-700">
            @foreach($errors->all() as $error)
                <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Personal Information Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Personal Information</h2>
            </div>
            <div class="p-6">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   value="{{ old('name', $user->name) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                                   required>
                            @error('name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email"
                                   name="email"
                                   id="email"
                                   value="{{ old('email', $user->email) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                                   required>
                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    @if($user->buyer)
                        <!-- Buyer-specific fields -->
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 mt-6">
                            <div>
                                <label for="phone_code" class="block text-sm font-medium text-gray-700 mb-2">Phone Code</label>
                                <input type="text"
                                       name="phone_code"
                                       id="phone_code"
                                       value="{{ old('phone_code', $user->buyer->phone_code ?? '') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                                       placeholder="+250">
                                @error('phone_code')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                                <input type="text"
                                       name="phone"
                                       id="phone"
                                       value="{{ old('phone', $user->buyer->phone ?? '') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                                @error('phone')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 mt-6">
                            <div>
                                <label for="country_id" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                                <select name="country_id" id="country_id"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                                    <option value="">Select Country</option>
                                    @php
                                        $countries = \App\Models\Country::where('status', 'active')->orderBy('name')->get();
                                    @endphp
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}" {{ old('country_id', $user->buyer->country_id ?? '') == $country->id ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('country_id')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                <input type="text"
                                       name="city"
                                       id="city"
                                       value="{{ old('city', $user->buyer->city ?? '') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                                @error('city')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 mt-6">
                            <div>
                                <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                               <input type="date"
                                name="date_of_birth"
                                id="date_of_birth"
                                value="{{ old('date_of_birth', $user->buyer->date_of_birth ? \Carbon\Carbon::parse($user->buyer->date_of_birth)->format('Y-m-d') : '') }}"
                                max="{{ \Carbon\Carbon::now()->subYears(18)->format('Y-m-d') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                                @error('date_of_birth')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="sex" class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                                <select name="sex" id="sex"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                                    <option value="">Select Gender</option>
                                    <option value="Male" {{ old('sex', $user->buyer->sex ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('sex', $user->buyer->sex ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                                @error('sex')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @endif

                    <div class="mt-6">
                        <button type="submit"
                                class="px-6 py-2.5 bg-[#ff0808] text-white font-semibold rounded-lg hover:bg-red-700 transition-colors">
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Change Password</h2>
            </div>
            <div class="p-6">
                <form action="{{ route('profile.password.update') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                            <input type="password"
                                   name="current_password"
                                   id="current_password"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                                   required>
                            @error('current_password')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                            <input type="password"
                                   name="password"
                                   id="password"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                                   required
                                   minlength="8">
                            @error('password')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Password must be at least 8 characters long.</p>
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                            <input type="password"
                                   name="password_confirmation"
                                   id="password_confirmation"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                                   required
                                   minlength="8">
                        </div>
                    </div>
                    <div class="mt-6">
                        <button type="submit"
                                class="px-6 py-2.5 bg-[#ff0808] text-white font-semibold rounded-lg hover:bg-red-700 transition-colors">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Account Information Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Account Information</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Account Type</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($user->hasRole('admin'))
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Admin
                                </span>
                            @elseif($user->isVendor())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    Vendor
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Buyer
                                </span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email Verified</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($user->email_verified_at)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="mr-1 text-xs fas fa-check-circle"></i>Verified
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="mr-1 text-xs fas fa-clock"></i>Not Verified
                                </span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Member Since</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if($user->isVendor() && $user->vendor)
            <!-- Vendor Information Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900">Vendor Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        @if($user->vendor->businessProfile)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Business Name</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->vendor->businessProfile->business_name }}</p>
                            </div>
                        @endif
                        @if($user->vendor->plan)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Current Plan</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->vendor->plan->name }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if($user->buyer)
            <!-- Buyer Information Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900">Buyer Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Phone</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->buyer->phone_code }} {{ $user->buyer->phone }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Location</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->buyer->city }}, {{ $user->buyer->country->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Account Status</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @if($user->buyer->account_status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @elseif($user->buyer->account_status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Suspended
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Profile Picture Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="p-6 text-center">
                @php
                    $userName = $user->name;
                    $roleColor = $user->hasRole('admin') ? 'ff0808' : ($user->isVendor() ? '9333ea' : '3b82f6');
                @endphp
                <img src="https://ui-avatars.com/api/?name={{ urlencode($userName) }}&background={{ $roleColor }}&color=fff&bold=true&size=128"
                     alt="{{ $userName }}"
                     class="w-24 h-24 mx-auto rounded-full ring-4 ring-gray-100 mb-4">
                <h3 class="text-lg font-bold text-gray-900">{{ $user->name }}</h3>
                <p class="text-sm text-gray-600 mt-1">{{ $user->email }}</p>
                @if($user->hasRole('admin'))
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-2">
                        Admin
                    </span>
                @elseif($user->isVendor())
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 mt-2">
                        Vendor
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mt-2">
                        Buyer
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection


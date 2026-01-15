@extends('layouts.app')

@section('title', __('messages.account_settings'))

@section('content')
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        @include('buyer.partial.buyer-nav')

        <!-- Content -->
        <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="p-4 mb-4 bg-green-50 rounded-lg border border-green-300">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 mb-4 bg-red-50 rounded-lg border border-red-300">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Profile Settings -->
                <div class="lg:col-span-2">
                    <div class="p-6 bg-white rounded-lg border border-gray-200 shadow-sm">
                        <h2 class="mb-4 text-lg font-bold text-gray-900">Profile Information</h2>

                        <form action="{{ route('buyer.account.profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="space-y-4">
                                <div>
                                    <label class="block mb-1 text-sm font-medium text-gray-700">Name</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808]">
                                    @error('name')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block mb-1 text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808]">
                                    @error('email')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <label class="block mb-1 text-sm font-medium text-gray-700">Phone Code</label>
                                        <input type="text" name="phone_code"
                                            value="{{ old('phone_code', $buyer->phone_code ?? '') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808]">
                                    </div>

                                    <div>
                                        <label class="block mb-1 text-sm font-medium text-gray-700">Phone</label>
                                        <input type="text" name="phone" value="{{ old('phone', $buyer->phone ?? '') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808]">
                                    </div>
                                </div>

                                <div>
                                    <label class="block mb-1 text-sm font-medium text-gray-700">Country</label>
                                    <select name="country_id"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808]">
                                        <option value="">Select Country</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}"
                                                {{ old('country_id', $buyer->country_id ?? '') == $country->id ? 'selected' : '' }}>
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block mb-1 text-sm font-medium text-gray-700">City</label>
                                    <input type="text" name="city" value="{{ old('city', $buyer->city ?? '') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808]">
                                </div>

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <label class="block mb-1 text-sm font-medium text-gray-700">Date of Birth</label>
                                        <input type="date" name="date_of_birth"
                                            value="{{ old('date_of_birth', $buyer->date_of_birth ?? '') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808]">
                                    </div>

                                    <div>
                                        <label class="block mb-1 text-sm font-medium text-gray-700">Gender</label>
                                        <select name="sex"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808]">
                                            <option value="">Select Gender</option>
                                            <option value="Male"
                                                {{ old('sex', $buyer->sex ?? '') == 'Male' ? 'selected' : '' }}>Male
                                            </option>
                                            <option value="Female"
                                                {{ old('sex', $buyer->sex ?? '') == 'Female' ? 'selected' : '' }}>Female
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <button type="submit"
                                    class="w-full px-4 py-2.5 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 transition-colors font-semibold">
                                    Update Profile
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Password Change -->
                <div>
                    <div class="p-6 bg-white rounded-lg border border-gray-200 shadow-sm">
                        <h2 class="mb-4 text-lg font-bold text-gray-900">Change Password</h2>

                        <form action="{{ route('buyer.account.password.update') }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="block mb-1 text-sm font-medium text-gray-700">Current Password</label>
                                    <input type="password" name="current_password" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808]">
                                    @error('current_password')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block mb-1 text-sm font-medium text-gray-700">New Password</label>
                                    <input type="password" name="password" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808]">
                                    @error('password')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block mb-1 text-sm font-medium text-gray-700">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808]">
                                </div>

                                <button type="submit"
                                    class="px-4 py-2.5 w-full font-semibold text-white bg-gray-800 rounded-lg transition-colors hover:bg-gray-900">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

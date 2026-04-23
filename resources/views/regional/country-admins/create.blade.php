@extends('layouts.home')

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('regional.country-admins.index') }}"
               class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <i class="fas fa-arrow-left text-gray-600"></i>
            </a>
            <h1 class="text-2xl sm:text-lg font-bold text-gray-900">Add Country Administrator</h1>
        </div>
        <p class="text-sm text-gray-600 ml-12">Create a new country administrator account</p>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-circle text-red-500 text-xl mt-0.5"></i>
                <div>
                    <p class="font-semibold text-red-800 mb-2">Please fix the following errors:</p>
                    <ul class="list-disc list-inside text-red-700 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Form Card -->
    <form action="{{ route('regional.country-admins.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Personal Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <div class="w-10 h-10 bg-[#ff0808] rounded-lg flex items-center justify-center">
                    <i class="fas fa-user text-white"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Personal Information</h2>
                    <p class="text-sm text-gray-600">Basic details about the administrator</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Full Name -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 mt-2"></i>
                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               required
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent @error('name') border-red-500 @enderror"
                               placeholder="Enter full name">
                    </div>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 mt-2"></i>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent @error('email') border-red-500 @enderror"
                               placeholder="admin@example.com">
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone (Optional) -->
                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                        Phone Number <span class="text-gray-400 text-xs">(Optional)</span>
                    </label>
                    <div class="relative">
                        <i class="fas fa-phone absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 mt-2"></i>
                        <input type="text"
                               id="phone"
                               name="phone"
                               value="{{ old('phone') }}"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent @error('phone') border-red-500 @enderror"
                               placeholder="+250 XXX XXX XXX">
                    </div>
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Country Assignment -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <div class="w-10 h-10 bg-[#ff0808] rounded-lg flex items-center justify-center">
                    <i class="fas fa-flag text-white"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Country Assignment</h2>
                    <p class="text-sm text-gray-600">Assign the administrator to a country</p>
                </div>
            </div>

            <!-- Country Selection -->
            <div>
                <label for="country_id" class="block text-sm font-semibold text-gray-700 mb-2">
                    Select Country <span class="text-red-500">*</span>
                </label>
                <select id="country_id"
                        name="country_id"
                        required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent @error('country_id') border-red-500 @enderror">
                    <option value="">-- Select a Country --</option>
                    @foreach($availableCountries as $country)
                        <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                            {{ $country->name }} ({{ $country->code }})
                        </option>
                    @endforeach
                </select>
                @error('country_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                @if($availableCountries->isEmpty())
                    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5"></i>
                            <div>
                                <p class="text-sm font-semibold text-yellow-800">No Available Countries</p>
                                <p class="text-xs text-yellow-700 mt-1">All active countries in {{ $region->name }} already have assigned administrators. Please deactivate an existing administrator or add a new country first.</p>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="mt-2 text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Only countries in {{ $region->name }} without an active administrator are shown
                    </p>
                @endif
            </div>
        </div>

        <!-- Security -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-lock text-white"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Security</h2>
                    <p class="text-sm text-gray-600">Set the administrator's login credentials</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="fas fa-key absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 mt-2"></i>
                        <input type="password"
                               id="password"
                               name="password"
                               required
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent @error('password') border-red-500 @enderror"
                               placeholder="Min. 8 characters">
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500">
                        <i class="fas fa-shield-alt mr-1"></i>
                        Password must be at least 8 characters long
                    </p>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                        Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="fas fa-check-circle absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 mt-2"></i>
                        <input type="password"
                               id="password_confirmation"
                               name="password_confirmation"
                               required
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                               placeholder="Re-enter password">
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-between gap-4 pt-6">
            <a href="{{ route('regional.country-admins.index') }}"
               class="px-6 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>

            <button type="submit"
                    class="px-6 py-2.5 bg-[#ff0808] text-white font-semibold rounded-lg hover:bg-red-700 transition-all shadow-sm"
                    {{ $availableCountries->isEmpty() ? 'disabled' : '' }}>
                <i class="fas fa-save mr-2"></i>Create Administrator
            </button>
        </div>
    </form>
</div>
@endsection

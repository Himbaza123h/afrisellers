@extends('layouts.home')

@section('page-content')
<div class="space-y-6 max-w-6xl mx-auto">
    <!-- Page Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.country.show', $country) }}" class="p-2 text-gray-600 rounded-lg hover:bg-gray-100">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                {{ isset($countryAdmin) ? 'Edit' : 'Assign' }} Country Admin
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ isset($countryAdmin) ? 'Update' : 'Create' }} Country Administrator credentials for {{ $country->name ?? 'this country' }}
            </p>
        </div>
    </div>

    <!-- Info Alert -->
    <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
        <i class="fas fa-info-circle text-green-600 mt-0.5"></i>
        <div class="flex-1">
            <p class="text-sm font-medium text-green-900">
                {{ isset($countryAdmin) ? 'Updating' : 'Creating' }} Country Administrator Account
            </p>
            <p class="text-xs text-green-700 mt-1">This user will have country-level access and manage all activities within this country.</p>
        </div>
    </div>

    <!-- Errors -->
    @if($errors->any())
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <div class="flex-1">
                @foreach($errors->all() as $error)
                    <p class="text-sm font-medium text-red-900">{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Form -->
    <form action="{{ route('admin.countries.assign-country-admin.store', $country) }}" method="POST" class="bg-white rounded-xl border border-gray-200 shadow-sm">
        @csrf

        <div class="p-6 space-y-6">
            <!-- User Type Display -->
            <div class="p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-xl border-2 border-green-200">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-green-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-flag text-3xl text-white"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-green-900">Country Administrator</h3>
                        <p class="text-sm text-green-700">Country-specific management access and permissions</p>
                    </div>
                    <div class="px-4 py-2 bg-white rounded-lg shadow-sm">
                        <span class="text-xs font-medium text-green-600 uppercase">Country Level</span>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200"></div>

            <!-- Personal Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-user text-green-600"></i>
                    Personal Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name"
                            value="{{ old('name', $countryAdmin->name ?? '') }}"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Alain Honore">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number
                        </label>
                        <input type="text" name="phone"
                            value="{{ old('phone', $countryAdmin->phone ?? '') }}"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="+250 xxx xxx xxx">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200"></div>

            <!-- Account Credentials -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-key text-green-600"></i>
                    Account Credentials
                </h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email"
                            value="{{ old('email', $countryAdmin->email ?? '') }}"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="admin@example.com"
                            {{ isset($countryAdmin) ? 'readonly' : '' }}>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if(isset($countryAdmin))
                            <p class="mt-1 text-xs text-gray-500">Email cannot be changed</p>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Password
                                @if(!isset($countryAdmin))
                                    <span class="text-red-500">*</span>
                                @else
                                    <span class="text-gray-500">(leave blank to keep current)</span>
                                @endif
                            </label>
                            <div class="relative">
                                <input type="password" name="password" id="password"
                                    {{ !isset($countryAdmin) ? 'required' : '' }}
                                    class="w-full px-4 py-2.5 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                    placeholder="••••••••">
                                <button type="button" onclick="togglePassword('password')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 mt-3">
                                    <i class="fas fa-eye" id="password-icon"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Minimum 8 characters</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Confirm Password
                                @if(!isset($countryAdmin))
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>
                            <div class="relative">
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    {{ !isset($countryAdmin) ? 'required' : '' }}
                                    class="w-full px-4 py-2.5 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                    placeholder="••••••••">
                                <button type="button" onclick="togglePassword('password_confirmation')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 mt-3">
                                    <i class="fas fa-eye" id="password_confirmation-icon"></i>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200"></div>

            <!-- Assignment Details -->
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <h4 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fas fa-map-marker-alt text-green-600"></i>
                    Assignment Details
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-3 bg-white rounded-lg border border-gray-200">
                        <span class="text-xs text-gray-600 block mb-1">Country</span>
                        <div class="flex items-center gap-2">
                            @if($country->flag_url)
                                <img src="{{ $country->flag_url }}" alt="{{ $country->name }}" class="w-5 h-4 object-cover rounded">
                            @endif
                            <span class="text-sm font-semibold text-gray-900">{{ $country->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="p-3 bg-white rounded-lg border border-gray-200">
                        <span class="text-xs text-gray-600 block mb-1">Country Code</span>
                        <span class="text-sm font-semibold text-gray-900">+{{ $country->code ?? 'N/A' }}</span>
                    </div>
                    <div class="p-3 bg-white rounded-lg border border-gray-200">
                        <span class="text-xs text-gray-600 block mb-1">Role Type</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-flag mr-1"></i> Country Admin
                        </span>
                    </div>
                </div>
                @if($country->region)
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="fas fa-globe text-xs"></i>
                        <span>Region: <span class="font-medium text-gray-900">{{ $country->region->name }}</span></span>
                    </div>
                </div>
                @endif
            </div>

            @if(isset($countryAdmin))
            <!-- Additional Info for Edit Mode -->
            <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex items-center gap-3">
                    <i class="fas fa-info-circle text-blue-600"></i>
                    <div class="flex-1 text-sm">
                        <p class="font-medium text-blue-900">Editing Existing Admin</p>
                        <p class="text-blue-700 text-xs mt-1">
                            Created: {{ $countryAdmin->created_at->format('M d, Y h:i A') }}
                            @if($countryAdmin->updated_at->ne($countryAdmin->created_at))
                                | Last Updated: {{ $countryAdmin->updated_at->format('M d, Y h:i A') }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Form Actions -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl flex items-center justify-end gap-3">
            <a href="{{ route('admin.country.show', $country) }}"
                class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium shadow-sm">
                <i class="fas fa-{{ isset($countryAdmin) ? 'save' : 'user-plus' }}"></i>
                {{ isset($countryAdmin) ? 'Update' : 'Create' }} Country Admin
            </button>
        </div>
    </form>
</div>

<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId + '-icon');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endsection

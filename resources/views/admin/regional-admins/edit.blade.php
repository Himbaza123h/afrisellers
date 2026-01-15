@extends('layouts.home')

@section('page-content')
<div class="container-fluid px-4 py-6 max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('admin.regional-admins.show', $regionalAdmin) }}"
               class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <i class="fas fa-arrow-left text-gray-600"></i>
            </a>
            <h1 class="text-2xl sm:text-lg font-bold text-gray-900">Edit Regional Administrator</h1>
        </div>
        <p class="text-sm text-gray-600 ml-12">Update administrator information and settings</p>
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
    <form action="{{ route('admin.regional-admins.update', $regionalAdmin) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Personal Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <div class="w-10 h-10 bg-[#ff0808] rounded-lg flex items-center justify-center">
                    <i class="fas fa-user text-white"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Personal Information</h2>
                    <p class="text-sm text-gray-600">Update basic details about the administrator</p>
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
                               value="{{ old('name', $regionalAdmin->user->name) }}"
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
                               value="{{ old('email', $regionalAdmin->user->email) }}"
                               required
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent @error('email') border-red-500 @enderror"
                               placeholder="admin@example.com">
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Region Assignment & Status -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <div class="w-10 h-10 bg-[#ff0808] rounded-lg flex items-center justify-center">
                    <i class="fas fa-map-marked-alt text-white"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Region & Status</h2>
                    <p class="text-sm text-gray-600">Update region assignment and account status</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Region Selection -->
                <div>
                    <label for="region_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        Select Region <span class="text-red-500">*</span>
                    </label>
                    <select id="region_id"
                            name="region_id"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent @error('region_id') border-red-500 @enderror">
                        <option value="">-- Select a Region --</option>
                        @foreach($availableRegions as $region)
                            <option value="{{ $region->id }}"
                                    {{ old('region_id', $regionalAdmin->region_id) == $region->id ? 'selected' : '' }}>
                                {{ $region->name }} ({{ $region->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('region_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Current: {{ $regionalAdmin->region->name }}
                    </p>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                        Account Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status"
                            name="status"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent @error('status') border-red-500 @enderror">
                        <option value="active" {{ old('status', $regionalAdmin->status) == 'active' ? 'selected' : '' }}>
                            Active
                        </option>
                        <option value="inactive" {{ old('status', $regionalAdmin->status) == 'inactive' ? 'selected' : '' }}>
                            Inactive
                        </option>
                        <option value="suspended" {{ old('status', $regionalAdmin->status) == 'suspended' ? 'selected' : '' }}>
                            Suspended
                        </option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Change Password (Optional) -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-lock text-white"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Change Password</h2>
                    <p class="text-sm text-gray-600">Leave blank to keep current password</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        New Password <span class="text-gray-400 text-xs">(Optional)</span>
                    </label>
                    <div class="relative">
                        <i class="fas fa-key absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 mt-2"></i>
                        <input type="password"
                               id="password"
                               name="password"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent @error('password') border-red-500 @enderror"
                               placeholder="Enter new password">
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500">
                        <i class="fas fa-shield-alt mr-1"></i>
                        Minimum 8 characters required
                    </p>
                </div>

                <!-- Confirm New Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                        Confirm New Password
                    </label>
                    <div class="relative">
                        <i class="fas fa-check-circle absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 mt-2"></i>
                        <input type="password"
                               id="password_confirmation"
                               name="password_confirmation"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                               placeholder="Confirm new password">
                    </div>
                </div>
            </div>

            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                    <p class="text-sm text-blue-800">
                        If you want to change the password, fill in both password fields. Otherwise, leave them empty to keep the current password.
                    </p>
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-info-circle text-white"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Account Information</h2>
                    <p class="text-sm text-gray-600">Read-only account details</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Assigned Date -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Assigned Date</label>
                    <div class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg">
                        <p class="text-sm text-gray-900">{{ $regionalAdmin->assigned_at->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $regionalAdmin->assigned_at->diffForHumans() }}</p>
                    </div>
                </div>

                <!-- Account Created -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Account Created</label>
                    <div class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg">
                        <p class="text-sm text-gray-900">{{ $regionalAdmin->created_at->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $regionalAdmin->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-between gap-4 pt-6">
            <a href="{{ route('admin.regional-admins.show', $regionalAdmin) }}"
               class="px-6 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>

            <button type="submit"
                    class="px-6 py-2.5 bg-[#ff0808] text-white font-semibold rounded-lg hover:bg-red-700 transition-all shadow-sm">
                <i class="fas fa-save mr-2"></i>Update Administrator
            </button>
        </div>
    </form>
</div>
@endsection

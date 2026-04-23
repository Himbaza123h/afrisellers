@extends('layouts.home')

@section('page-content')
<div class="w-[55%] space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Change Password</h1>
        <p class="mt-1 text-sm text-gray-500">Update your password to keep your account secure</p>
    </div>

    <!-- Change Password Form -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <form method="POST" action="{{ route('admin.security.update-password') }}" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                <input type="password" name="current_password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Enter current password">
                @error('current_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                <input type="password" name="new_password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Enter new password">
                @error('new_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Password must be at least 8 characters long</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                <input type="password" name="new_password_confirmation" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Confirm new password">
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    <i class="fas fa-key mr-2"></i>Update Password
                </button>
                <a href="{{ route('admin.security.index') }}" class="px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Password Requirements -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h4 class="text-sm font-semibold text-blue-900 mb-2">Password Requirements:</h4>
        <ul class="space-y-1 text-xs text-blue-700">
            <li><i class="fas fa-check mr-2"></i>At least 8 characters long</li>
            <li><i class="fas fa-check mr-2"></i>Include uppercase and lowercase letters</li>
            <li><i class="fas fa-check mr-2"></i>Include at least one number</li>
            <li><i class="fas fa-check mr-2"></i>Include at least one special character</li>
        </ul>
    </div>
</div>
@endsection

@extends('layouts.home')

@section('page-content')
<div class="container-fluid px-4 py-5 max-w-6xl mx-auto">

    {{-- Header --}}
    <div class="mb-5">
        <div class="flex items-center gap-3 mb-1">
            <a href="{{ route('admin.manageusers.index') }}"
               class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <i class="fas fa-arrow-left text-gray-600 text-sm mt-2"></i>
            </a>
            <h1 class="text-xl font-bold text-gray-900">Edit Admin User</h1>
        </div>
        <p class="text-xs text-gray-500 ml-11">Update account details and permissions for <strong>{{ $user->name }}</strong></p>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-5 rounded-lg">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
                <div>
                    <p class="text-sm font-semibold text-red-800 mb-1">Please fix the following errors:</p>
                    <ul class="list-disc list-inside text-xs text-red-700 space-y-0.5">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.manageusers.update', $user) }}" method="POST" class="space-y-5">
        @csrf @method('PUT')

        {{-- Personal Information --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-200">
                <div class="w-9 h-9 bg-[#ff0808] rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user text-white text-sm mt-2"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Personal Information</h2>
                    <p class="text-xs text-gray-500">Update basic details</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-xs font-semibold text-gray-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs mt-2"></i>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent @error('name') border-red-500 @enderror">
                    </div>
                    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-700 mb-1.5">Email Address <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs mt-2"></i>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent @error('email') border-red-500 @enderror">
                    </div>
                    @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="phone" class="block text-xs font-semibold text-gray-700 mb-1.5">Phone <span class="text-gray-400">(Optional)</span></label>
                    <div class="relative">
                        <i class="fas fa-phone absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs mt-2"></i>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                               class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                    </div>
                </div>

                <div>
                    <label for="department_id" class="block text-xs font-semibold text-gray-700 mb-1.5">Department <span class="text-gray-400">(Optional)</span></label>
                    <div class="relative">
                        <i class="fas fa-building absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs mt-2"></i>
                        <select id="department_id" name="department_id"
                                class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                            <option value="">— No Department —</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" @if(old('department_id', $user->department_id) == $department->id) selected @endif>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>



            </div>
        </div>

        {{-- Password (optional on edit) --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-200">
                <div class="w-9 h-9 bg-green-600 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-lock text-white text-sm mt-2"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Change Password</h2>
                    <p class="text-xs text-gray-500">Leave blank to keep the current password</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-xs font-semibold text-gray-700 mb-1.5">New Password</label>
                    <div class="relative">
                        <i class="fas fa-key absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs mt-2"></i>
                        <input type="password" id="password" name="password"
                               class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent @error('password') border-red-500 @enderror"
                               placeholder="Min. 8 characters">
                    </div>
                    @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-gray-700 mb-1.5">Confirm New Password</label>
                    <div class="relative">
                        <i class="fas fa-check-circle absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs mt-2"></i>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                               placeholder="Re-enter new password">
                    </div>
                </div>
            </div>
        </div>

        {{-- Permissions Panel --}}
        @include('admin.users.partials.permissions_panel', ['permission' => $permission, 'groups' => $groups])

        {{-- Actions --}}
        <div class="flex items-center justify-between gap-4 pt-2">
            <a href="{{ route('admin.manageusers.index') }}"
               class="px-5 py-2 border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <button type="submit"
                    class="px-5 py-2 bg-[#ff0808] text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-all shadow-sm">
                <i class="fas fa-save mr-2"></i>Save Changes
            </button>
        </div>
    </form>
</div>
@endsection

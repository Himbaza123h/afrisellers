@extends('layouts.home')

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Country Administrators</h1>
                <p class="text-sm text-gray-600 mt-1">Manage country administrators for {{ $region->name }}</p>
            </div>
            <a href="{{ route('regional.country-admins.create') }}"
               class="px-4 py-2.5 bg-[#ff0808] text-white font-semibold rounded-lg hover:bg-red-700 transition-all shadow-sm">
                <i class="fas fa-plus mr-2"></i>Add Country Admin
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-lg">
            <div class="flex items-start gap-3">
                <i class="fas fa-check-circle text-green-500 text-xl mt-0.5"></i>
                <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
                <button onclick="this.parentElement.parentElement.remove()" class="text-green-600 hover:text-green-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    <!-- Error Messages -->
    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-circle text-red-500 text-xl mt-0.5"></i>
                <div class="flex-1">
                    <p class="font-semibold text-red-800 mb-2">Please fix the following errors:</p>
                    <ul class="list-disc list-inside text-red-700 space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Admins</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
                <div class="w-14 h-14 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Active</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['active'] }}</p>
                </div>
                <div class="w-14 h-14 bg-green-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-check text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Inactive</p>
                    <p class="text-3xl font-bold text-gray-600">{{ $stats['inactive'] }}</p>
                </div>
                <div class="w-14 h-14 bg-gray-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-slash text-2xl text-gray-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('regional.country-admins.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search by name or email..."
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
            </div>

            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                <select name="country_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                    <option value="">All Countries</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="w-40">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>

            @if(request()->hasAny(['search', 'country_id', 'status']))
                <a href="{{ route('regional.country-admins.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-all">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Admin Details</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Country</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Contact</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Created Date</th>
                        <th class="px-6 py-4 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($countryAdmins as $admin)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-purple-100 to-purple-200 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-semibold text-purple-700">{{ substr($admin->name ?? 'U', 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $admin->name ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">ID: #{{ $admin->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    @if($admin->country->flag_url)
                                        <img src="{{ $admin->country->flag_url }}" alt="{{ $admin->country->name }}" class="w-6 h-4 rounded">
                                    @endif
                                    <span class="text-sm font-medium text-gray-900">{{ $admin->country->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm text-gray-900">{{ $admin->email ?? 'N/A' }}</span>
                                    @if($admin->phone)
                                        <span class="text-xs text-gray-500">{{ $admin->phone }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($admin->deleted_at === null)
                                    <span class="px-3 py-1.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>Active
                                    </span>
                                @else
                                    <span class="px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-times-circle mr-1"></i>Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-900">{{ $admin->created_at ? $admin->created_at->format('M d, Y') : 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('regional.country-admins.edit', $admin->id) }}"
                                       class="p-2 text-blue-600 rounded-lg hover:bg-blue-50"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('regional.country-admins.destroy', $admin->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this country administrator?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 rounded-lg hover:bg-red-50" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-users text-4xl text-gray-300"></i>
                                    </div>
                                    <p class="text-lg font-semibold text-gray-900 mb-1">No country administrators found</p>
                                    <p class="text-sm text-gray-500 mb-6">Get started by adding a new country administrator</p>
                                    <a href="{{ route('regional.country-admins.create') }}"
                                       class="px-4 py-2 bg-[#ff0808] text-white font-semibold rounded-lg hover:bg-red-700 transition-all">
                                        <i class="fas fa-plus mr-2"></i>Add Country Admin
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($countryAdmins->hasPages())
            <div class="px-6 py-4 border-t bg-gray-50">
                {{ $countryAdmins->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

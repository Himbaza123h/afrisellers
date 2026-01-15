@extends('layouts.home')

@section('page-content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Addon Management</h1>
            <p class="mt-1 text-sm text-gray-500">Manage promotional addon placements</p>
        </div>
        <a href="{{ route('admin.addons.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-pink-600 text-white rounded-lg hover:bg-pink-700 font-medium shadow-sm">
            <i class="fas fa-plus"></i>
            Create Addon
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Addons</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-layer-group text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Global Addons</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['global'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-globe text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Country Specific</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['country_specific'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-map-marker-alt text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Subscriptions</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['active_subscriptions'] }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <p class="text-sm font-medium text-red-900 flex-1">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search addons..." class="flex-1 min-w-[200px] px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">

            <select name="country_id" class="px-4 py-2.5 border border-gray-300 rounded-lg">
                <option value="">All Countries</option>
                <option value="global" {{ request('country_id') == 'global' ? 'selected' : '' }}>Global Only</option>
                @foreach($countries as $country)
                    <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                        {{ $country->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2.5 bg-pink-600 text-white rounded-lg hover:bg-pink-700 font-medium">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>

            @if(request()->hasAny(['search', 'country_id', 'locationX']))
                <a href="{{ route('admin.addons.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Location</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Country</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Price</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Subscriptions</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Created</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($addons as $addon)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-gray-900">{{ $addon->locationX }}</span>
                                    <span class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $addon->locationY)) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($addon->country)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $addon->country->name }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <i class="fas fa-globe mr-1"></i> Global
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-gray-900">${{ number_format($addon->price, 2) }}</span>
                                <span class="text-xs text-gray-500">/30 days</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium text-gray-900">{{ $addon->addonUsers->count() }}</span>
                                    <span class="text-xs text-gray-500">
                                        ({{ $addon->activeAddonUsers->count() }} active)
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $addon->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.addons.show', $addon) }}" class="p-2 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.addons.edit', $addon) }}" class="p-2 text-gray-600 rounded-lg hover:bg-yellow-50 hover:text-yellow-600" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.addons.destroy', $addon) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure? This will delete the addon.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-600 rounded-lg hover:bg-red-50 hover:text-red-600" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-layer-group text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-gray-500">No addons found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($addons->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $addons->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

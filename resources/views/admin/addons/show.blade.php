@extends('layouts.home')

@section('page-content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.addons.index') }}" class="p-2 text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-100">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Addon Details</h1>
                <p class="mt-1 text-sm text-gray-500">View addon information and subscriptions</p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.addons.edit', $addon) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 font-medium">
                <i class="fas fa-edit"></i>
                Edit
            </a>
        </div>
    </div>

    <!-- Addon Details Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="bg-pink-500 to-purple-600 p-6 text-white">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h2 class="text-2xl font-bold mb-2">{{ $addon->locationX }}</h2>
                    <p class="text-pink-100 mb-3">{{ ucfirst(str_replace('_', ' ', $addon->locationY)) }}</p>
                    <div class="flex items-center gap-2">
                        @if($addon->country)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/20">
                                {{ $addon->country->name }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/20">
                                <i class="fas fa-globe mr-1"></i> Global
                            </span>
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold">${{ number_format($addon->price, 2) }}</div>
                    <div class="text-sm text-pink-100">per 30 days</div>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 bg-blue-50 rounded-lg">
                    <div class="text-xs text-blue-600 font-medium mb-1">Total Subscriptions</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['total_subscriptions'] }}</div>
                </div>
                <div class="p-4 bg-green-50 rounded-lg">
                    <div class="text-xs text-green-600 font-medium mb-1">Active Subscriptions</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['active_subscriptions'] }}</div>
                </div>
                <div class="p-4 bg-purple-50 rounded-lg">
                    <div class="text-xs text-purple-600 font-medium mb-1">Total Revenue</div>
                    <div class="text-2xl font-bold text-gray-900">${{ number_format($stats['total_revenue'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscriptions Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-bold text-gray-900">Subscriptions</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Vendor</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Item</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Duration</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Expires</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($addon->addonUsers as $addonUser)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-900">{{ $addonUser->user->name }}</span>
                                    <span class="text-xs text-gray-500">{{ $addonUser->user->email }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded capitalize">
                                    {{ $addonUser->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-900">
                                    {{ $addonUser->related_entity->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-900">{{ $addonUser->paid_days }} days</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($addonUser->isActive())
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">
                                        Active
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">
                                        Expired
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($addonUser->ended_at)
                                    <span class="text-sm text-gray-900">{{ $addonUser->ended_at->format('M d, Y') }}</span>
                                @else
                                    <span class="text-xs text-gray-400">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                No subscriptions yet
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

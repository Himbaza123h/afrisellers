@extends('layouts.home')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Subscriptions</h1>
            <p class="mt-1 text-sm text-gray-500">Manage all user subscriptions</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Active</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Trial</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['trial'] }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-gift text-xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Expired</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $stats['expired'] }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-xl text-orange-600"></i>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Cancelled</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['cancelled'] }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-red-50 to-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-xl text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <form method="GET" class="space-y-4">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by seller name or email..." class="w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 mt-2"></i>
            </div>

            <div class="flex flex-wrap gap-3 items-center">
                <label class="text-sm font-medium text-gray-700">Filters:</label>

                <select name="status" class="px-4 py-2.5 border border-gray-300 rounded-lg">
                    <option value="">All Status</option>
                    <option value="trial" {{ request('status') === 'trial' ? 'selected' : '' }}>Trial</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>

                <select name="plan_id" class="px-4 py-2.5 border border-gray-300 rounded-lg">
                    <option value="">All Plans</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}" {{ request('plan_id') == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
                    @endforeach
                </select>

                <select name="is_trial" class="px-4 py-2.5 border border-gray-300 rounded-lg">
                    <option value="">Trial Status</option>
                    <option value="yes" {{ request('is_trial') === 'yes' ? 'selected' : '' }}>Is Trial</option>
                    <option value="no" {{ request('is_trial') === 'no' ? 'selected' : '' }}>Not Trial</option>
                </select>

                <input type="text" id="dateRange" name="date_range" value="{{ request('date_range') }}" placeholder="Date range" readonly class="px-4 py-2.5 border border-gray-300 rounded-lg w-56 cursor-pointer">

                <button type="submit" class="px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                    <i class="fas fa-filter"></i> Apply
                </button>

                @if(request()->hasAny(['search', 'status', 'plan_id', 'is_trial', 'date_range']))
                    <a href="{{ route('admin.memberships.subscriptions.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
                        <i class="fas fa-times"></i> Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-hidden bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Seller</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Plan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Period</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Days Left</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Type</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($subscriptions as $subscription)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-semibold text-gray-900">{{ $subscription->seller->user->name }}</span>
                                    <span class="text-xs text-gray-500">{{ $subscription->seller->user->email }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <i class="fas fa-crown mr-1"></i> {{ $subscription->plan->name }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1 text-xs">
                                    <span class="text-gray-600"><i class="fas fa-play text-green-600 mr-1"></i>{{ $subscription->starts_at->format('M d, Y') }}</span>
                                    <span class="text-gray-600"><i class="fas fa-stop text-red-600 mr-1"></i>{{ $subscription->ends_at->format('M d, Y') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $daysLeft = round($subscription->daysRemaining());
                                @endphp
                                @if($daysLeft > 0)
                                    <span class="text-sm font-semibold text-green-600">{{ $daysLeft }} days</span>
                                @else
                                    <span class="text-sm font-semibold text-red-600">Expired</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($subscription->is_trial)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Trial</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Paid</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                    {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $subscription->status === 'trial' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $subscription->status === 'expired' ? 'bg-orange-100 text-orange-800' : '' }}
                                    {{ $subscription->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.memberships.subscriptions.show', $subscription) }}" class="p-2 text-blue-600 rounded-lg hover:bg-blue-50" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($subscription->status === 'active')
                                        <form action="{{ route('admin.memberships.subscriptions.cancel', $subscription) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-red-600 rounded-lg hover:bg-red-50" title="Cancel" onclick="return confirm('Cancel this subscription?')">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-users text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-base font-semibold text-gray-900 mb-1">No subscriptions found</p>
                                    <p class="text-sm text-gray-500">No subscriptions match your filters</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($subscriptions, 'hasPages') && $subscriptions->hasPages())
            <div class="px-6 py-4 border-t bg-gray-50">
                {{ $subscriptions->links() }}
            </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
flatpickr("#dateRange", {
    mode: "range",
    dateFormat: "Y-m-d",
    locale: { rangeSeparator: " to " }
});
</script>
@endsection

@extends('layouts.home')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .custom-scrollbar::-webkit-scrollbar { height: 8px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #888; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #555; }
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .vendor-logo { transition: transform 0.3s; }
    .vendor-logo:hover { transform: scale(1.05); }
</style>
@endpush

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Country Vendors Management</h1>
            <p class="mt-1 text-sm text-gray-500">Monitor and manage vendors in {{ $country->name }}</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            <button class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-download"></i>
                <span>Export</span>
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Vendors</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-store mr-1 text-[10px]"></i> All vendors
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                    <i class="fas fa-store text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Active Vendors</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['active']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $stats['active_percentage'] }}%
                        </span>
                        <span class="text-xs text-gray-500">of total</span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-green-50 to-green-100 rounded-xl">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Suspended</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['suspended']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i class="fas fa-ban mr-1 text-[10px]"></i> Inactive
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-red-50 to-red-100 rounded-xl">
                    <i class="fas fa-ban text-2xl text-red-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Email Verified</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['email_verified']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            <i class="fas fa-envelope-circle-check mr-1 text-[10px]"></i> Verified
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl">
                    <i class="fas fa-envelope-circle-check text-2xl text-indigo-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Email Unverified</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['email_unverified']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-clock mr-1 text-[10px]"></i> Pending
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl">
                    <i class="fas fa-clock text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Business Verified</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['business_verified']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            {{ $stats['verified_percentage'] }}%
                        </span>
                        <span class="text-xs text-gray-500">of total</span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl">
                    <i class="fas fa-shield-check text-2xl text-purple-600"></i>
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

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <form method="GET" action="{{ route('country.vendors.index') }}" class="space-y-4">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search vendors by name, email, business name..." class="w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 mt-2"></i>
            </div>

            <div class="flex flex-wrap gap-3 items-center">
                <label class="text-sm font-medium text-gray-700">Filters:</label>

                <div class="relative">
                    <input type="text" id="dateRangePicker" name="date_range" value="{{ request('date_range') }}" readonly placeholder="Date range" class="pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg w-56 cursor-pointer bg-white">
                    <i class="fas fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none mt-2"></i>
                </div>

                <select name="account_status" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">Account Status</option>
                    <option value="active" {{ request('account_status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ request('account_status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="inactive" {{ request('account_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>

                <select name="email_verified" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">Email Status</option>
                    <option value="verified" {{ request('email_verified') == 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="unverified" {{ request('email_verified') == 'unverified' ? 'selected' : '' }}>Unverified</option>
                </select>

                <select name="business_verified" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">Business Verification</option>
                    <option value="verified" {{ request('business_verified') == 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="unverified" {{ request('business_verified') == 'unverified' ? 'selected' : '' }}>Unverified</option>
                </select>

                <select name="sort_by" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Sort by Date</option>
                    <option value="business_name" {{ request('sort_by') == 'business_name' ? 'selected' : '' }}>Business Name</option>
                    <option value="account_status" {{ request('sort_by') == 'account_status' ? 'selected' : '' }}>Account Status</option>
                </select>

                <select name="sort_order" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                </select>

                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    <i class="fas fa-filter"></i> Apply
                </button>

                @if(request()->hasAny(['search', 'date_range', 'account_status', 'email_verified', 'business_verified', 'sort_by']))
                    <a href="{{ route('country.vendors.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
                        <i class="fas fa-times"></i> Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-hidden bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Business</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Owner</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Contact</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Registration</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Email Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Business Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Account</th>
                        <th class="px-6 py-4 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($vendors as $vendor)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($vendor->businessProfile && $vendor->businessProfile->logo)
                                        <img src="{{ asset('storage/' . $vendor->businessProfile->logo) }}"
                                             alt="{{ $vendor->businessProfile->business_name }}"
                                             class="vendor-logo w-12 h-12 object-cover rounded-lg border border-gray-200">
                                    @else
                                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center border border-gray-300">
                                            <i class="fas fa-store text-xl text-gray-400"></i>
                                        </div>
                                    @endif
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-900">
                                            {{ $vendor->businessProfile->business_name ?? 'N/A' }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            ID: #{{ $vendor->id }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-900">{{ $vendor->user->name ?? 'N/A' }}</span>
                                    <span class="text-xs text-gray-500">{{ $vendor->user->email ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col text-sm">
                                    @if($vendor->businessProfile && $vendor->businessProfile->phone)
                                        <div class="flex items-center gap-1 text-gray-900">
                                            <i class="fas fa-phone text-xs text-gray-400"></i>
                                            <span>{{ $vendor->businessProfile->full_phone }}</span>
                                        </div>
                                    @endif
                                    @if($vendor->businessProfile && $vendor->businessProfile->business_email)
                                        <div class="flex items-center gap-1 text-gray-600 text-xs">
                                            <i class="fas fa-envelope text-xs text-gray-400"></i>
                                            <span>{{ $vendor->businessProfile->business_email }}</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-900">{{ $vendor->created_at->format('M d, Y') }}</span>
                                    <span class="text-xs text-gray-500">{{ $vendor->created_at->format('h:i A') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($vendor->email_verified)
                                    <span class="px-2.5 py-1 rounded-md text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i> Verified
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-md text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i> Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($vendor->businessProfile && $vendor->businessProfile->is_admin_verified)
                                    <span class="px-2.5 py-1 rounded-md text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-shield-check mr-1"></i> Verified
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-md text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-shield-exclamation mr-1"></i> Unverified
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'active' => ['Active', 'bg-green-100 text-green-800'],
                                        'suspended' => ['Suspended', 'bg-red-100 text-red-800'],
                                        'inactive' => ['Inactive', 'bg-gray-100 text-gray-800'],
                                    ];
                                    $status = $statusColors[$vendor->account_status] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
                                @endphp
                                <span class="px-3 py-1.5 rounded-full text-xs font-medium {{ $status[1] }}">{{ $status[0] }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('country.vendors.show', $vendor->id) }}" class="p-2 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($vendor->businessProfile && !$vendor->businessProfile->is_admin_verified)
                                        <form action="{{ route('country.vendors.verify', $vendor->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-gray-600 rounded-lg hover:bg-green-50 hover:text-green-600" title="Verify Business">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($vendor->account_status === 'active')
                                        <form action="{{ route('country.vendors.suspend', $vendor->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-gray-600 rounded-lg hover:bg-red-50 hover:text-red-600" title="Suspend" onclick="return confirm('Are you sure you want to suspend this vendor?')">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('country.vendors.activate', $vendor->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-gray-600 rounded-lg hover:bg-green-50 hover:text-green-600" title="Activate">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-store text-4xl text-gray-300"></i>
                                    </div>
                                    <p class="text-lg font-semibold text-gray-900 mb-1">No vendors found</p>
                                    <p class="text-sm text-gray-500 mb-6">Vendors from your country will appear here</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($vendors, 'hasPages') && $vendors->hasPages())
            <div class="px-6 py-4 border-t bg-gray-50">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-700">Showing {{ $vendors->firstItem() }}-{{ $vendors->lastItem() }} of {{ $vendors->total() }}</span>
                    <div>{{ $vendors->links() }}</div>
                </div>
            </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
flatpickr("#dateRangePicker", {
    mode: "range",
    dateFormat: "Y-m-d",
    showMonths: 2,
    locale: { rangeSeparator: " to " },
    onClose: function(dates, str, inst) {
        if (dates.length === 2) inst.element.closest('form').submit();
    }
});
</script>
@endsection

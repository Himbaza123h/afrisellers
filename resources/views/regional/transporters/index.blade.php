@extends('layouts.home')

@push('styles')
<style>
    .custom-scrollbar::-webkit-scrollbar { height: 8px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #888; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #555; }
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
</style>
@endpush

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Regional Transporters Management</h1>
            <p class="mt-1 text-sm text-gray-500">Monitor and manage transporters across your region</p>
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
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Transporters</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-globe mr-1 text-[10px]"></i> Regional
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                    <i class="fas fa-truck text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Verified</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['verified']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $stats['verified_percentage'] }}%
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
                    <p class="text-sm font-medium text-gray-600 mb-1">Active</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['active']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            {{ $stats['active_percentage'] }}%
                        </span>
                        <span class="text-xs text-gray-500">of total</span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl">
                    <i class="fas fa-user-check text-2xl text-indigo-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Fleet</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_fleet']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <i class="fas fa-car mr-1 text-[10px]"></i> Vehicles
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl">
                    <i class="fas fa-shipping-fast text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Unverified</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['unverified']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-clock mr-1 text-[10px]"></i> Review
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl">
                    <i class="fas fa-hourglass-half text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Suspended</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['suspended']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                            <i class="fas fa-ban mr-1 text-[10px]"></i> Action
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl">
                    <i class="fas fa-pause-circle text-2xl text-orange-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Avg Rating</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['average_rating'] ?? 0, 1) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800">
                            <i class="fas fa-star mr-1 text-[10px]"></i> Rating
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-teal-50 to-teal-100 rounded-xl">
                    <i class="fas fa-star text-2xl text-teal-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Inactive</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['inactive']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            <i class="fas fa-power-off mr-1 text-[10px]"></i> Offline
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl">
                    <i class="fas fa-user-slash text-2xl text-gray-600"></i>
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
        <form method="GET" action="{{ route('regional.transporters.index') }}" class="space-y-4">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, business name..." class="w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 mt-2"></i>
            </div>

            <div class="flex flex-wrap gap-3 items-center">
                <label class="text-sm font-medium text-gray-700">Filters:</label>

                <select name="country_id" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Countries</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>

                <select name="is_verified" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Verification Status</option>
                    <option value="verified" {{ request('is_verified') == 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="unverified" {{ request('is_verified') == 'unverified' ? 'selected' : '' }}>Unverified</option>
                </select>

                <select name="status" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>

                <select name="sort_by" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Sort by Date</option>
                    <option value="company_name" {{ request('sort_by') == 'company_name' ? 'selected' : '' }}>Company Name</option>
                    <option value="average_rating" {{ request('sort_by') == 'average_rating' ? 'selected' : '' }}>Rating</option>
                    <option value="total_deliveries" {{ request('sort_by') == 'total_deliveries' ? 'selected' : '' }}>Total Deliveries</option>
                    <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
                </select>

                <select name="sort_order" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                </select>

                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    <i class="fas fa-filter"></i> Apply
                </button>

                @if(request()->hasAny(['search', 'country_id', 'is_verified', 'status', 'sort_by']))
                    <a href="{{ route('regional.transporters.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
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
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Company</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Owner</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Country</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Contact</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Fleet Size</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Rating</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Verified</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($transporters as $transporter)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <p class="text-sm font-semibold text-gray-900">{{ $transporter->company_name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">Reg: {{ $transporter->registration_number ?? 'N/A' }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full">
                                        <span class="text-sm font-bold text-blue-700">{{ substr($transporter->user->name ?? 'U', 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $transporter->user->name ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">{{ $transporter->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-flag text-gray-400 text-xs"></i>
                                    <span class="text-sm text-gray-900">{{ $transporter->country->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-900">{{ $transporter->email ?? 'N/A' }}</span>
                                    @if($transporter->phone)
                                        <span class="text-xs text-gray-500">{{ $transporter->phone }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-truck text-gray-400 text-xs"></i>
                                    <span class="text-sm font-medium text-gray-900">{{ $transporter->fleet_size ?? 0 }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-star text-yellow-400 text-xs"></i>
                                    <span class="text-sm font-medium text-gray-900">{{ number_format($transporter->average_rating ?? 0, 1) }}</span>
                                    <span class="text-xs text-gray-500">({{ $transporter->total_deliveries ?? 0 }})</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($transporter->is_verified)
                                    <span class="px-3 py-1.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Verified</span>
                                @else
                                    <span class="px-3 py-1.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Unverified</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'active' => ['Active', 'bg-green-100 text-green-800'],
                                        'inactive' => ['Inactive', 'bg-gray-100 text-gray-800'],
                                        'suspended' => ['Suspended', 'bg-red-100 text-red-800'],
                                    ];
                                    $status = $statusColors[$transporter->status] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
                                @endphp
                                <span class="px-3 py-1.5 rounded-full text-xs font-medium {{ $status[1] }}">{{ $status[0] }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('regional.transporters.show', $transporter->id) }}" class="p-2 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-truck text-4xl text-gray-300"></i>
                                    </div>
                                    <p class="text-lg font-semibold text-gray-900 mb-1">No transporters found</p>
                                    <p class="text-sm text-gray-500">Transporters from your region will appear here</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($transporters, 'hasPages') && $transporters->hasPages())
            <div class="px-6 py-4 border-t bg-gray-50">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-700">Showing {{ $transporters->firstItem() }}-{{ $transporters->lastItem() }} of {{ $transporters->total() }}</span>
                    <div>{{ $transporters->links() }}</div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

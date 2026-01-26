@extends('layouts.home')

@push('styles')
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .tab-content { animation: fadeIn 0.3s ease-in-out; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .custom-scrollbar::-webkit-scrollbar { height: 8px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #888; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #555; }
</style>
@endpush

@section('page-content')
<div class="space-y-4">
    <!-- Page Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Country Administrators</h1>
            <p class="mt-1 text-xs text-gray-500">Manage country admins for {{ $region->name }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="window.open('{{ route('regional.country-admins.print') }}' + window.location.search, '_blank')" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            <a href="{{ route('regional.country-admins.create') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-plus"></i>
                <span>Add Admin</span>
            </a>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex gap-2 border-b border-gray-200 no-print">
        <button onclick="switchTab('overview')" id="tab-overview" class="tab-button px-4 py-2 text-sm font-semibold text-blue-600 border-b-2 border-blue-600 transition-colors">
            <i class="fas fa-chart-line mr-2"></i> Overview
        </button>
        <button onclick="switchTab('admins')" id="tab-admins" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-users mr-2"></i> Country Admins
        </button>
        <button onclick="switchTab('countries')" id="tab-countries" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-globe mr-2"></i> Countries
        </button>
        <button onclick="switchTab('activity')" id="tab-activity" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-history mr-2"></i> Activity
        </button>
    </div>

    <!-- Overview Tab Content (Default) -->
    <div id="tab-overview-content" class="tab-content">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Admins</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-globe mr-1 text-[8px]"></i> Regional
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                        <i class="fas fa-users text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Active</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['active']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $stats['active_percentage'] ?? 0 }}%
                            </span>
                            <span class="text-xs text-gray-500">of total</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                        <i class="fas fa-user-check text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Inactive</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['inactive']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <i class="fas fa-power-off mr-1 text-[8px]"></i> Offline
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg">
                        <i class="fas fa-user-slash text-gray-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages -->
        @if(session('success'))
            <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-start gap-2 mt-4">
                <i class="fas fa-check-circle text-green-600 mt-0.5 text-sm"></i>
                <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
                <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times text-sm"></i></button>
            </div>
        @endif

        @if($errors->any())
            <div class="p-3 bg-red-50 rounded-lg border border-red-200 mt-4">
                <div class="flex items-start gap-2">
                    <i class="fas fa-exclamation-circle text-red-600 mt-0.5 text-sm"></i>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-red-800 mb-1">Please fix the following errors:</p>
                        <ul class="list-disc list-inside text-red-700 space-y-1 text-xs">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mt-4">
            <form method="GET" action="{{ route('regional.country-admins.index') }}" class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                    <!-- Search -->
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    <!-- Country -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Country</label>
                        <select name="country_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Countries</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        <i class="fas fa-filter text-sm"></i> Apply
                    </button>
                    <a href="{{ route('regional.country-admins.index') }}" class="inline-flex items-center gap-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                        <i class="fas fa-undo text-sm"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Country Admins Tab Content -->
    <div id="tab-admins-content" class="tab-content hidden">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-900">Country Administrators List</h2>
                    <span class="px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">
                        {{ $countryAdmins->total() }} {{ Str::plural('admin', $countryAdmins->total()) }}
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Admin Details</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Country</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Contact</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Created</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($countryAdmins as $admin)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-gradient-to-br from-purple-100 to-purple-200 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-semibold text-purple-700">{{ substr($admin->name ?? 'U', 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $admin->name ?? 'N/A' }}</p>
                                            <p class="text-xs text-gray-500">ID: #{{ $admin->id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        @if(isset($admin->country->flag_url) && $admin->country->flag_url)
                                            <img src="{{ $admin->country->flag_url }}" alt="{{ $admin->country->name }}" class="w-5 h-3 rounded">
                                        @endif
                                        <span class="text-sm font-medium text-gray-900">{{ $admin->country->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm text-gray-900">{{ $admin->email ?? 'N/A' }}</p>
                                    @if($admin->phone)
                                        <p class="text-xs text-gray-500">{{ $admin->phone }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($admin->deleted_at === null)
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>Active
                                        </span>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-times-circle mr-1"></i>Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm text-gray-900">{{ $admin->created_at ? $admin->created_at->format('M d, Y') : 'N/A' }}</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('regional.country-admins.edit', $admin->id) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium px-2 py-1 rounded hover:bg-blue-50" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('regional.country-admins.destroy', $admin->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this country administrator?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium px-2 py-1 rounded hover:bg-red-50" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-2">
                                            <i class="fas fa-users text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium">No country administrators found</p>
                                        <p class="text-xs text-gray-400 mt-1">Try adjusting your filters or add a new admin</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($countryAdmins->hasPages())
                <div class="px-4 py-3 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-700">Showing {{ $countryAdmins->firstItem() }}-{{ $countryAdmins->lastItem() }} of {{ $countryAdmins->total() }}</span>
                        <div class="text-sm">{{ $countryAdmins->links() }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Countries Tab Content -->
    <div id="tab-countries-content" class="tab-content hidden">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Countries in {{ $region->name }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($countries as $country)
                    @php
                        $adminCount = $countryAdmins->where('country_id', $country->id)->count();
                        $hasAdmin = $adminCount > 0;
                    @endphp
                    <div class="p-4 {{ $hasAdmin ? 'bg-green-50 border-green-200' : 'bg-yellow-50 border-yellow-200' }} rounded-lg border">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                @if(isset($country->flag_url) && $country->flag_url)
                                    <img src="{{ $country->flag_url }}" alt="{{ $country->name }}" class="w-8 h-5 rounded">
                                @endif
                                <h4 class="font-semibold text-gray-900">{{ $country->name }}</h4>
                            </div>
                            @if($hasAdmin)
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>Assigned
                                </span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-exclamation-circle mr-1"></i>No Admin
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mb-2">Code: {{ $country->code ?? 'N/A' }}</p>
                        @if($hasAdmin)
                            @php
                                $admin = $countryAdmins->where('country_id', $country->id)->first();
                            @endphp
                            <div class="mt-3 pt-3 border-t border-green-200">
                                <p class="text-xs font-medium text-gray-700 mb-1">Administrator:</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $admin->name }}</p>
                                <p class="text-xs text-gray-600">{{ $admin->email }}</p>
                            </div>
                        @else
                            <div class="mt-3">
                                <a href="{{ route('regional.country-admins.create') }}" class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:text-blue-700">
                                    <i class="fas fa-plus"></i> Assign Admin
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Activity Tab Content -->
    <div id="tab-activity-content" class="tab-content hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Recent Admins -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Recent Administrators</h3>
                <div class="space-y-3">
                    @php
                        $recentAdmins = $countryAdmins->take(5);
                    @endphp

                    @forelse($recentAdmins as $admin)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <span class="text-sm font-semibold text-purple-700">{{ substr($admin->name ?? 'U', 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $admin->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ $admin->country->name ?? 'N/A' }} • {{ $admin->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($admin->deleted_at === null)
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Inactive</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                            <p class="text-sm text-gray-500">No administrators yet</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Status Distribution -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Status Distribution</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Active Administrators</span>
                            <span class="text-sm font-bold text-green-700">{{ number_format($stats['active']) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-green-600 h-3 rounded-full transition-all duration-300" style="width: {{ $stats['active_percentage'] ?? 0 }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['active_percentage'] ?? 0 }}% of total</p>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Inactive Administrators</span>
                            <span class="text-sm font-bold text-gray-700">{{ number_format($stats['inactive']) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-gray-600 h-3 rounded-full transition-all duration-300" style="width: {{ $stats['total'] > 0 ? round(($stats['inactive'] / $stats['total']) * 100) : 0 }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['total'] > 0 ? round(($stats['inactive'] / $stats['total']) * 100) : 0 }}% of total</p>
                    </div>
                </div>

                <!-- Summary Stats -->
                <div class="mt-6 p-4 bg-gradient-to-br from-blue-50 to-white rounded-lg border border-blue-100">
                    <h4 class="text-sm font-semibold text-blue-700 mb-3">Quick Summary</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-600">Total Countries</p>
                            <p class="text-lg font-bold text-gray-900">{{ $countries->count() }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600">Total Admins</p>
                            <p class="text-lg font-bold text-gray-900">{{ $stats['total'] }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600">Coverage</p>
                            <p class="text-lg font-bold text-gray-900">
                                @php
                                    $assignedCountries = $countryAdmins->pluck('country_id')->unique()->count();
                                    $coverage = $countries->count() > 0 ? round(($assignedCountries / $countries->count()) * 100) : 0;
                                @endphp
                                {{ $coverage }}%
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600">Active Rate</p>
                            <p class="text-lg font-bold text-gray-900">{{ $stats['active_percentage'] ?? 0 }}%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Tab Switching Function
    function switchTab(tabName) {
        // Remove active state from all tabs
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
            btn.classList.add('text-gray-600');
        });

        // Add active state to selected tab
        const activeTab = document.getElementById(`tab-${tabName}`);
        activeTab.classList.remove('text-gray-600');
        activeTab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');

        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Show selected tab content
        document.getElementById(`tab-${tabName}-content`).classList.remove('hidden');
    }

    // Initialize with Overview tab active
    document.addEventListener('DOMContentLoaded', function() {
        switchTab('overview');
    });
</script>
@endpush
@endsection

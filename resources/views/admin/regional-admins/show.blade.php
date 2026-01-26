@extends('layouts.home')

@section('page-content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="mb-4">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('admin.regional-admins.index') }}"
               class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <i class="fas fa-arrow-left text-gray-600"></i>
            </a>
            <h1 class="text-xl font-bold text-gray-900">Regional Administrator Details</h1>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-3 mb-4 rounded-lg">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-green-500"></i>
                <p class="text-green-800 font-medium text-sm">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Left Column: Admin Info & Actions -->
        <div class="lg:col-span-1 space-y-4">
            <!-- Admin Profile Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="text-center">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($regionalAdmin->user->name) }}&background=ff0808&color=fff&bold=true&size=96"
                         alt="{{ $regionalAdmin->user->name }}"
                         class="w-20 h-20 rounded-full mx-auto mb-3 ring-4 ring-gray-100">

                    <h2 class="text-lg font-bold text-gray-900 mb-1">{{ $regionalAdmin->user->name }}</h2>
                    <p class="text-xs text-gray-600 mb-3">{{ $regionalAdmin->user->email }}</p>

                    <!-- Status Badge -->
                    @if($regionalAdmin->status == 'active')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                            <i class="fas fa-check-circle"></i>
                            Active
                        </span>
                    @elseif($regionalAdmin->status == 'inactive')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">
                            <i class="fas fa-minus-circle"></i>
                            Inactive
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-100 text-red-800 text-xs font-semibold rounded-full">
                            <i class="fas fa-ban"></i>
                            Suspended
                        </span>
                    @endif
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200 space-y-3">
                    <!-- Region -->
                    <div class="flex items-start gap-2">
                        <i class="fas fa-map-marker-alt text-[#ff0808] mt-0.5 text-sm"></i>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Assigned Region</p>
                            <p class="font-semibold text-gray-900 text-sm">{{ $regionalAdmin->region->name }}</p>
                            <p class="text-xs text-gray-600">{{ $regionalAdmin->region->code }}</p>
                        </div>
                    </div>

                    <!-- Assigned Date -->
                    <div class="flex items-start gap-2">
                        <i class="fas fa-calendar-alt text-blue-600 mt-0.5 text-sm"></i>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Assigned Date</p>
                            <p class="font-semibold text-gray-900 text-sm">{{ $regionalAdmin->assigned_at->format('M d, Y') }}</p>
                            <p class="text-xs text-gray-600">{{ $regionalAdmin->assigned_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    <!-- Created Date -->
                    <div class="flex items-start gap-2">
                        <i class="fas fa-clock text-purple-600 mt-0.5 text-sm"></i>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Account Created</p>
                            <p class="font-semibold text-gray-900 text-sm">{{ $regionalAdmin->created_at->format('M d, Y') }}</p>
                            <p class="text-xs text-gray-600">{{ $regionalAdmin->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-4 pt-4 border-t border-gray-200 space-y-2">
                    <!-- Switch to Dashboard Button -->
                    <button type="button"
                            onclick="dashboardSwitch.open({{ $regionalAdmin->id }}, '{{ $regionalAdmin->user->name }}', 'Regional Admin', '{{ route('admin.regional-admins.switch-to-regional', $regionalAdmin) }}')"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Switch to Dashboard</span>
                    </button>

                    <a href="{{ route('admin.regional-admins.edit', $regionalAdmin) }}"
                       class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors text-sm">
                        <i class="fas fa-edit"></i>
                        <span>Edit Administrator</span>
                    </a>

                    @if($regionalAdmin->status == 'active')
                        <form action="{{ route('admin.regional-admins.deactivate', $regionalAdmin) }}"
                              method="POST">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Are you sure you want to deactivate this administrator?')"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 transition-colors text-sm">
                                <i class="fas fa-pause"></i>
                                <span>Deactivate</span>
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.regional-admins.activate', $regionalAdmin) }}"
                              method="POST">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Are you sure you want to activate this administrator?')"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors text-sm">
                                <i class="fas fa-check"></i>
                                <span>Activate</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Stats & Details -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Statistics -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Total Vendors -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-store text-blue-600"></i>
                        </div>
                    </div>
                    <p class="text-xl font-bold text-gray-900">{{ number_format($stats['total_vendors']) }}</p>
                    <p class="text-xs text-gray-600 mt-1">Total Vendors</p>
                </div>

                <!-- Monthly Revenue -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-green-600"></i>
                        </div>
                    </div>
                    <p class="text-xl font-bold text-gray-900">${{ number_format($stats['monthly_revenue'], 2) }}</p>
                    <p class="text-xs text-gray-600 mt-1">Monthly Revenue</p>
                </div>

                <!-- Monthly Orders -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-purple-600"></i>
                        </div>
                    </div>
                    <p class="text-xl font-bold text-gray-900">{{ number_format($stats['monthly_orders']) }}</p>
                    <p class="text-xs text-gray-600 mt-1">Monthly Orders</p>
                </div>

                <!-- Countries -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-flag text-orange-600"></i>
                        </div>
                    </div>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['countries_count'] }}</p>
                    <p class="text-xs text-gray-600 mt-1">Countries</p>
                </div>
            </div>

            <!-- Region Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-200">
                    <div class="w-9 h-9 bg-[#ff0808] rounded-lg flex items-center justify-center">
                        <i class="fas fa-map-marked-alt text-white text-sm"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Region Information</h2>
                        <p class="text-xs text-gray-600">Details about the assigned region</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <!-- Region Name -->
                    <div class="flex items-start gap-4">
                        <div class="w-20 text-xs font-semibold text-gray-600">Name:</div>
                        <div class="flex-1 text-sm text-gray-900">{{ $regionalAdmin->region->name }}</div>
                    </div>

                    <!-- Region Code -->
                    <div class="flex items-start gap-4">
                        <div class="w-20 text-xs font-semibold text-gray-600">Code:</div>
                        <div class="flex-1">
                            <span class="inline-flex items-center px-2.5 py-1 bg-gray-100 text-gray-800 text-xs font-mono rounded">
                                {{ $regionalAdmin->region->code }}
                            </span>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($regionalAdmin->region->description)
                        <div class="flex items-start gap-4">
                            <div class="w-20 text-xs font-semibold text-gray-600">Description:</div>
                            <div class="flex-1 text-sm text-gray-900">{{ $regionalAdmin->region->description }}</div>
                        </div>
                    @endif

                    <!-- Countries List -->
                    <div class="flex items-start gap-4">
                        <div class="w-20 text-xs font-semibold text-gray-600">Countries:</div>
                        <div class="flex-1">
                            @if($regionalAdmin->region->countries->count() > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($regionalAdmin->region->countries as $country)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 text-blue-700 text-xs rounded-lg border border-blue-200">
                                            <i class="fas fa-map-marker-alt text-xs"></i>
                                            {{ $country->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-gray-500 italic">No countries assigned to this region</p>
                            @endif
                        </div>
                    </div>

                    <!-- Region Status -->
                    <div class="flex items-start gap-4">
                        <div class="w-20 text-xs font-semibold text-gray-600">Status:</div>
                        <div class="flex-1">
                            @if($regionalAdmin->region->status == 'active')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                    <i class="fas fa-check-circle"></i>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">
                                    <i class="fas fa-minus-circle"></i>
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-200">
                    <div class="w-9 h-9 bg-[#ff0808] rounded-lg flex items-center justify-center">
                        <i class="fas fa-history text-white text-sm"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Recent Activities</h2>
                        <p class="text-xs text-gray-600">Latest actions and updates</p>
                    </div>
                </div>

                @if(isset($recentActivities) && $recentActivities->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentActivities as $activity)
                            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-circle text-blue-600 text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">{{ $activity->description ?? 'Activity' }}</p>
                                    <p class="text-xs text-gray-600">{{ $activity->created_at->diffForHumans() ?? 'Recently' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-clipboard-list text-gray-300 text-3xl mb-3"></i>
                        <p class="text-sm text-gray-500">No recent activities to display</p>
                        <p class="text-xs text-gray-400 mt-1">Activities will appear here as they occur</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

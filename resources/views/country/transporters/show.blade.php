@extends('layouts.home')

@push('styles')
<style>
    @media print {
        .no-print { display: none !important; }
    }
</style>
@endpush

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between no-print">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('country.transporters.index') }}" class="text-gray-600 hover:text-gray-900 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Transporter Details</h1>
            </div>
            <p class="text-sm text-gray-500">Complete information about this transporter</p>
        </div>
        <div class="flex flex-wrap gap-3">
            @if($transporter->verification_status == 'pending')
                <form action="{{ route('country.transporters.verify', $transporter->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium shadow-sm">
                        <i class="fas fa-check-circle"></i>
                        <span>Verify Transporter</span>
                    </button>
                </form>
            @endif
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
        </div>
    </div>

    <!-- Status Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Transporter Name</p>
                <p class="text-lg font-bold text-gray-900">{{ $transporter->user->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Joined Date</p>
                <p class="text-sm font-semibold text-gray-900">{{ $transporter->created_at->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Country</p>
                <p class="text-sm font-semibold text-gray-900">{{ $transporter->user->country->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Verification</p>
                @php
                    $verificationColors = [
                        'pending' => ['Pending', 'bg-yellow-100 text-yellow-800'],
                        'verified' => ['Verified', 'bg-green-100 text-green-800'],
                        'rejected' => ['Rejected', 'bg-red-100 text-red-800'],
                    ];
                    $verification = $verificationColors[$transporter->verification_status] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
                @endphp
                <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-medium {{ $verification[1] }}">
                    {{ $verification[0] }}
                </span>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Account Status</p>
                @php
                    $statusColors = [
                        'active' => ['Active', 'bg-green-100 text-green-800'],
                        'inactive' => ['Inactive', 'bg-gray-100 text-gray-800'],
                        'suspended' => ['Suspended', 'bg-red-100 text-red-800'],
                    ];
                    $status = $statusColors[$transporter->account_status] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
                @endphp
                <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-medium {{ $status[1] }}">
                    {{ $status[0] }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Business Profile -->
            @if($transporter->businessProfile)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Business Profile</h3>
                    <div class="space-y-4">
                        @if($transporter->businessProfile->logo)
                            <div class="flex items-center gap-4">
                                <img src="{{ asset('storage/' . $transporter->businessProfile->logo) }}" alt="Logo" class="w-20 h-20 rounded-lg object-cover border">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $transporter->businessProfile->business_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $transporter->businessProfile->business_type ?? 'N/A' }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs font-medium text-gray-600 mb-1">Business Name</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $transporter->businessProfile->business_name }}</p>
                            </div>

                            @if($transporter->businessProfile->business_registration_number)
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs font-medium text-gray-600 mb-1">Registration Number</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $transporter->businessProfile->business_registration_number }}</p>
                                </div>
                            @endif

                            @if($transporter->businessProfile->phone)
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs font-medium text-gray-600 mb-1">Phone</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $transporter->businessProfile->phone }}</p>
                                </div>
                            @endif

                            @if($transporter->businessProfile->business_email)
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs font-medium text-gray-600 mb-1">Email</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $transporter->businessProfile->business_email }}</p>
                                </div>
                            @endif

                            @if($transporter->businessProfile->city)
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs font-medium text-gray-600 mb-1">City</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $transporter->businessProfile->city }}</p>
                                </div>
                            @endif

                            @if($transporter->businessProfile->year_established)
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs font-medium text-gray-600 mb-1">Year Established</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $transporter->businessProfile->year_established }}</p>
                                </div>
                            @endif
                        </div>

                        @if($transporter->businessProfile->description)
                            <div class="p-3 bg-blue-50 rounded-lg">
                                <p class="text-xs font-medium text-blue-700 mb-1">Description</p>
                                <p class="text-sm text-gray-900">{{ $transporter->businessProfile->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Vehicles -->
            @if($transporter->vehicles->count() > 0)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Vehicles ({{ $transporter->vehicles->count() }})</h3>
                    <div class="space-y-3">
                        @foreach($transporter->vehicles as $vehicle)
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-truck text-blue-600"></i>
                                            <p class="text-sm font-semibold text-gray-900">{{ $vehicle->make }} {{ $vehicle->model }}</p>
                                        </div>
                                        <div class="mt-2 grid grid-cols-2 gap-2 text-xs">
                                            <span class="text-gray-600">Type: <span class="font-medium text-gray-900">{{ $vehicle->vehicle_type }}</span></span>
                                            <span class="text-gray-600">Capacity: <span class="font-medium text-gray-900">{{ $vehicle->capacity }} {{ $vehicle->capacity_unit }}</span></span>
                                            <span class="text-gray-600">Year: <span class="font-medium text-gray-900">{{ $vehicle->year }}</span></span>
                                            <span class="text-gray-600">Plate: <span class="font-medium text-gray-900">{{ $vehicle->license_plate }}</span></span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @php
                                            $vehicleStatusColors = [
                                                'active' => ['Active', 'bg-green-100 text-green-800'],
                                                'maintenance' => ['Maintenance', 'bg-yellow-100 text-yellow-800'],
                                                'inactive' => ['Inactive', 'bg-gray-100 text-gray-800'],
                                            ];
                                            $vehicleStatus = $vehicleStatusColors[$vehicle->status] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
                                        @endphp
                                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium {{ $vehicleStatus[1] }}">
                                            {{ $vehicleStatus[0] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Recent Loads -->
            @if($transporter->loads->count() > 0)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Loads ({{ $transporter->loads->take(5)->count() }})</h3>
                    <div class="space-y-3">
                        @foreach($transporter->loads->take(5) as $load)
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-900">{{ $load->load_number }}</p>
                                        <div class="mt-1 flex items-center gap-4 text-xs text-gray-600">
                                            <span><i class="fas fa-map-marker-alt text-green-600 mr-1"></i>{{ $load->origin_city }}</span>
                                            <span><i class="fas fa-arrow-right text-gray-400 mr-1"></i></span>
                                            <span><i class="fas fa-map-marker-alt text-red-600 mr-1"></i>{{ $load->destination_city }}</span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">{{ $load->created_at->format('M d, Y') }}</p>
                                    </div>
                                    <div class="text-right">
                                        @php
                                            $loadStatusColors = [
                                                'posted' => ['Posted', 'bg-blue-100 text-blue-800'],
                                                'bidding' => ['Bidding', 'bg-yellow-100 text-yellow-800'],
                                                'assigned' => ['Assigned', 'bg-purple-100 text-purple-800'],
                                                'in_transit' => ['In Transit', 'bg-indigo-100 text-indigo-800'],
                                                'delivered' => ['Delivered', 'bg-green-100 text-green-800'],
                                                'cancelled' => ['Cancelled', 'bg-red-100 text-red-800'],
                                            ];
                                            $loadStatus = $loadStatusColors[$load->status] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
                                        @endphp
                                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium {{ $loadStatus[1] }}">
                                            {{ $loadStatus[0] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Statistics -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h3>
                <div class="space-y-4">
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <p class="text-xs font-medium text-blue-600 mb-1">Total Loads</p>
                        <p class="text-2xl font-bold text-blue-900">{{ number_format($stats['total_loads']) }}</p>
                    </div>
                    <div class="p-3 bg-green-50 rounded-lg">
                        <p class="text-xs font-medium text-green-600 mb-1">Completed Loads</p>
                        <p class="text-2xl font-bold text-green-900">{{ number_format($stats['completed_loads']) }}</p>
                    </div>
                    <div class="p-3 bg-indigo-50 rounded-lg">
                        <p class="text-xs font-medium text-indigo-600 mb-1">Active Loads</p>
                        <p class="text-2xl font-bold text-indigo-900">{{ number_format($stats['active_loads']) }}</p>
                    </div>
                    <div class="p-3 bg-purple-50 rounded-lg">
                        <p class="text-xs font-medium text-purple-600 mb-1">Total Vehicles</p>
                        <p class="text-2xl font-bold text-purple-900">{{ number_format($stats['total_vehicles']) }}</p>
                    </div>
                    <div class="p-3 bg-teal-50 rounded-lg">
                        <p class="text-xs font-medium text-teal-600 mb-1">Active Vehicles</p>
                        <p class="text-2xl font-bold text-teal-900">{{ number_format($stats['active_vehicles']) }}</p>
                    </div>
                    <div class="p-3 bg-yellow-50 rounded-lg">
                        <p class="text-xs font-medium text-yellow-600 mb-1">Total Bids</p>
                        <p class="text-2xl font-bold text-yellow-900">{{ number_format($stats['total_bids']) }}</p>
                    </div>
                    @if(isset($stats['total_revenue']))
                        <div class="p-3 bg-emerald-50 rounded-lg">
                            <p class="text-xs font-medium text-emerald-600 mb-1">Total Revenue</p>
                            <p class="text-lg font-bold text-emerald-900">{{ number_format($stats['total_revenue'], 2) }}</p>
                        </div>
                    @endif
                    @if($stats['total_reviews'] > 0)
                        <div class="p-3 bg-orange-50 rounded-lg">
                            <p class="text-xs font-medium text-orange-600 mb-1">Average Rating</p>
                            <div class="flex items-center gap-2">
                                <p class="text-2xl font-bold text-orange-900">{{ number_format($stats['average_rating'], 1) }}</p>
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star text-{{ $i <= $stats['average_rating'] ? 'yellow' : 'gray' }}-400 text-sm"></i>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-xs text-gray-600 mt-1">{{ $stats['total_reviews'] }} reviews</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full">
                            <span class="text-lg font-bold text-blue-700">{{ substr($transporter->user->name ?? 'U', 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $transporter->user->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">Transporter</p>
                        </div>
                    </div>

                    <div class="pt-3 border-t space-y-2">
                        <div class="flex items-center gap-2 text-sm">
                            <i class="fas fa-envelope text-gray-400 w-5"></i>
                            <span class="text-gray-900">{{ $transporter->user->email ?? 'N/A' }}</span>
                        </div>
                        @if($transporter->businessProfile && $transporter->businessProfile->phone)
                            <div class="flex items-center gap-2 text-sm">
                                <i class="fas fa-phone text-gray-400 w-5"></i>
                                <span class="text-gray-900">{{ $transporter->businessProfile->phone }}</span>
                            </div>
                        @endif
                        @if($transporter->businessProfile && $transporter->businessProfile->address)
                            <div class="flex items-center gap-2 text-sm">
                                <i class="fas fa-map-marker-alt text-gray-400 w-5"></i>
                                <span class="text-gray-900">{{ $transporter->businessProfile->address }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Timeline</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-user-plus text-blue-600 mt-1"></i>
                        <div>
                            <p class="font-medium text-gray-900">Registered</p>
                            <p class="text-xs text-gray-500">{{ $transporter->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                    @if($transporter->verified_at)
                        <div class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-green-600 mt-1"></i>
                            <div>
                                <p class="font-medium text-gray-900">Verified</p>
                                <p class="text-xs text-gray-500">{{ $transporter->verified_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    @endif
                    @if($transporter->last_active_at)
                        <div class="flex items-start gap-3">
                            <i class="fas fa-clock text-indigo-600 mt-1"></i>
                            <div>
                                <p class="font-medium text-gray-900">Last Active</p>
                                <p class="text-xs text-gray-500">{{ $transporter->last_active_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

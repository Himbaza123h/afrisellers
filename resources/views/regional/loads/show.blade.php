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
                <a href="{{ route('regional.loads.index') }}" class="text-gray-600 hover:text-gray-900 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Load Details</h1>
            </div>
            <div class="flex items-center gap-2">
                <p class="text-sm text-gray-500">{{ $load->load_number }}</p>
                @if($load->tracking_number)
                    <span class="px-2 py-0.5 rounded-md text-xs font-medium bg-blue-100 text-blue-800">
                        Tracking: {{ $load->tracking_number }}
                    </span>
                @endif
            </div>
        </div>
        <div class="flex flex-wrap gap-3">
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
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Load Number</p>
                <p class="text-lg font-bold text-gray-900">{{ $load->load_number }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Created Date</p>
                <p class="text-sm font-semibold text-gray-900">{{ $load->created_at->format('M d, Y') }}</p>
                <p class="text-xs text-gray-500">{{ $load->created_at->format('h:i A') }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Pickup Date</p>
                <p class="text-sm font-semibold text-gray-900">{{ $load->pickup_date ? $load->pickup_date->format('M d, Y') : 'N/A' }}</p>
                @if($load->pickup_time_start)
                    <p class="text-xs text-gray-500">{{ $load->pickup_time_start->format('h:i A') }}</p>
                @endif
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Delivery Date</p>
                <p class="text-sm font-semibold text-gray-900">{{ $load->delivery_date ? $load->delivery_date->format('M d, Y') : 'N/A' }}</p>
                @if($load->delivery_time_start)
                    <p class="text-xs text-gray-500">{{ $load->delivery_time_start->format('h:i A') }}</p>
                @endif
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Status</p>
                @php
                    $statusColors = [
                        'posted' => ['Posted', 'bg-blue-100 text-blue-800'],
                        'bidding' => ['Bidding', 'bg-yellow-100 text-yellow-800'],
                        'assigned' => ['Assigned', 'bg-purple-100 text-purple-800'],
                        'in_transit' => ['In Transit', 'bg-indigo-100 text-indigo-800'],
                        'delivered' => ['Delivered', 'bg-green-100 text-green-800'],
                        'cancelled' => ['Cancelled', 'bg-red-100 text-red-800'],
                    ];
                    $status = $statusColors[$load->status] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
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
            <!-- Route Information -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Route Information</h3>
                <div class="space-y-4">
                    <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                        <div class="flex items-start gap-3">
                            <div class="flex items-center justify-center w-10 h-10 bg-green-600 rounded-full text-white">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs font-medium text-green-700 uppercase mb-1">Origin</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $load->origin_address }}</p>
                                <p class="text-sm text-gray-700">{{ $load->origin_city }}, {{ $load->origin_state }}</p>
                                <p class="text-sm font-medium text-gray-900">{{ $load->originCountry->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <div class="flex items-center gap-2 text-gray-400">
                            <i class="fas fa-arrow-down text-2xl"></i>
                        </div>
                    </div>

                    <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                        <div class="flex items-start gap-3">
                            <div class="flex items-center justify-center w-10 h-10 bg-red-600 rounded-full text-white">
                                <i class="fas fa-arrow-down"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs font-medium text-red-700 uppercase mb-1">Destination</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $load->destination_address }}</p>
                                <p class="text-sm text-gray-700">{{ $load->destination_city }}, {{ $load->destination_state }}</p>
                                <p class="text-sm font-medium text-gray-900">{{ $load->destinationCountry->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cargo Details -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Cargo Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs font-medium text-gray-600 mb-1">Cargo Type</p>
                        <p class="text-sm font-semibold text-gray-900">{{ ucfirst($load->cargo_type ?? 'N/A') }}</p>
                    </div>

                    @if($load->weight)
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs font-medium text-gray-600 mb-1">Weight</p>
                            <p class="text-sm font-semibold text-gray-900">{{ number_format($load->weight, 2) }} {{ $load->weight_unit }}</p>
                        </div>
                    @endif

                    @if($load->volume)
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs font-medium text-gray-600 mb-1">Volume</p>
                            <p class="text-sm font-semibold text-gray-900">{{ number_format($load->volume, 2) }} {{ $load->volume_unit }}</p>
                        </div>
                    @endif

                    @if($load->quantity)
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs font-medium text-gray-600 mb-1">Quantity</p>
                            <p class="text-sm font-semibold text-gray-900">{{ number_format($load->quantity) }} {{ $load->packaging_type }}</p>
                        </div>
                    @endif

                    @if($load->packaging_type)
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs font-medium text-gray-600 mb-1">Packaging</p>
                            <p class="text-sm font-semibold text-gray-900">{{ ucfirst($load->packaging_type) }}</p>
                        </div>
                    @endif
                </div>

                @if($load->cargo_description)
                    <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                        <p class="text-xs font-medium text-blue-700 mb-1">Description</p>
                        <p class="text-sm text-gray-900">{{ $load->cargo_description }}</p>
                    </div>
                @endif

                @if($load->special_requirements && count($load->special_requirements) > 0)
                    <div class="mt-4">
                        <p class="text-xs font-medium text-gray-600 mb-2">Special Requirements</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($load->special_requirements as $requirement)
                                <span class="px-3 py-1 bg-orange-50 text-orange-700 rounded-full text-xs font-medium">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $requirement }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Assigned Transporter -->
            @if($load->assignedTransporter)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Assigned Transporter</h3>
                    <div class="flex items-start gap-4 p-4 bg-purple-50 rounded-lg border border-purple-200">
                        <div class="flex items-center justify-center w-12 h-12 bg-purple-600 rounded-full text-white">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900">{{ $load->assignedTransporter->user->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-600">{{ $load->assignedTransporter->user->email ?? 'N/A' }}</p>
                            @if($load->assignedTransporter->businessProfile)
                                <p class="text-xs text-gray-600 mt-1">{{ $load->assignedTransporter->businessProfile->business_name }}</p>
                            @endif
                            @if($load->assigned_at)
                                <p class="text-xs text-purple-700 mt-1">Assigned on {{ $load->assigned_at->format('M d, Y h:i A') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Bids -->
            @if($load->bids->count() > 0)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Bids ({{ $load->bids->count() }})</h3>
                    <div class="space-y-3">
                        @foreach($load->bids->sortBy('bid_amount') as $bid)
                            <div class="p-4 {{ $bid->id == $load->winning_bid_id ? 'bg-green-50 border-2 border-green-500' : 'bg-gray-50 border border-gray-200' }} rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-semibold text-gray-900">{{ $bid->transporter->user->name ?? 'N/A' }}</p>
                                            @if($bid->id == $load->winning_bid_id)
                                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-600 text-white">
                                                    <i class="fas fa-trophy mr-1"></i>Winner
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-600">{{ $bid->created_at->format('M d, Y h:i A') }}</p>
                                        @if($bid->notes)
                                            <p class="text-xs text-gray-700 mt-1">{{ Str::limit($bid->notes, 100) }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-gray-900">{{ $bid->currency }} {{ number_format($bid->bid_amount, 2) }}</p>
                                        @php
                                            $bidStatusColors = [
                                                'pending' => ['Pending', 'bg-yellow-100 text-yellow-800'],
                                                'accepted' => ['Accepted', 'bg-green-100 text-green-800'],
                                                'rejected' => ['Rejected', 'bg-red-100 text-red-800'],
                                                'withdrawn' => ['Withdrawn', 'bg-gray-100 text-gray-800'],
                                            ];
                                            $bidStatus = $bidStatusColors[$bid->status] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
                                        @endphp
                                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium {{ $bidStatus[1] }} mt-1">
                                            {{ $bidStatus[0] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Notes -->
            @if($load->notes)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Notes</h3>
                    <p class="text-sm text-gray-700">{{ $load->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Statistics -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Bid Statistics</h3>
                <div class="space-y-4">
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <p class="text-xs font-medium text-blue-600 mb-1">Total Bids</p>
                        <p class="text-2xl font-bold text-blue-900">{{ number_format($stats['total_bids']) }}</p>
                    </div>
                    <div class="p-3 bg-yellow-50 rounded-lg">
                        <p class="text-xs font-medium text-yellow-600 mb-1">Pending Bids</p>
                        <p class="text-2xl font-bold text-yellow-900">{{ number_format($stats['pending_bids']) }}</p>
                    </div>
                    <div class="p-3 bg-green-50 rounded-lg">
                        <p class="text-xs font-medium text-green-600 mb-1">Accepted Bids</p>
                        <p class="text-2xl font-bold text-green-900">{{ number_format($stats['accepted_bids']) }}</p>
                    </div>
                    <div class="p-3 bg-red-50 rounded-lg">
                        <p class="text-xs font-medium text-red-600 mb-1">Rejected Bids</p>
                        <p class="text-2xl font-bold text-red-900">{{ number_format($stats['rejected_bids']) }}</p>
                    </div>
                    @if(isset($stats['average_bid']))
                        <div class="p-3 bg-purple-50 rounded-lg">
                            <p class="text-xs font-medium text-purple-600 mb-1">Average Bid</p>
                            <p class="text-lg font-bold text-purple-900">{{ $load->currency }} {{ number_format($stats['average_bid'], 2) }}</p>
                        </div>
                        <div class="p-3 bg-indigo-50 rounded-lg">
                            <p class="text-xs font-medium text-indigo-600 mb-1">Lowest Bid</p>
                            <p class="text-lg font-bold text-indigo-900">{{ $load->currency }} {{ number_format($stats['lowest_bid'], 2) }}</p>
                        </div>
                        <div class="p-3 bg-teal-50 rounded-lg">
                            <p class="text-xs font-medium text-teal-600 mb-1">Highest Bid</p>
                            <p class="text-lg font-bold text-teal-900">{{ $load->currency }} {{ number_format($stats['highest_bid'], 2) }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Shipper Information -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Shipper Information</h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full">
                            <span class="text-lg font-bold text-blue-700">{{ substr($load->user->name ?? 'U', 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $load->user->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">Load Owner</p>
                        </div>
                    </div>

                    <div class="pt-3 border-t space-y-2">
                        <div class="flex items-center gap-2 text-sm">
                            <i class="fas fa-envelope text-gray-400 w-5"></i>
                            <span class="text-gray-900">{{ $load->user->email ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pricing Information -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pricing Information</h3>
                <div class="space-y-3">
                    @if($load->budget)
                        <div class="p-3 bg-green-50 rounded-lg">
                            <p class="text-xs font-medium text-green-600 mb-1">Budget</p>
                            <p class="text-xl font-bold text-green-900">{{ $load->currency }} {{ number_format($load->budget, 2) }}</p>
                        </div>
                    @endif
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <p class="text-xs font-medium text-blue-600 mb-1">Pricing Type</p>
                        <p class="text-sm font-semibold text-blue-900">{{ ucfirst($load->pricing_type ?? 'N/A') }}</p>
                    </div>
                    <div class="p-3 bg-purple-50 rounded-lg">
                        <p class="text-xs font-medium text-purple-600 mb-1">Currency</p>
                        <p class="text-sm font-semibold text-purple-900">{{ $load->currency ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Timeline</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-plus-circle text-blue-600 mt-1"></i>
                        <div>
                            <p class="font-medium text-gray-900">Posted</p>
                            <p class="text-xs text-gray-500">{{ $load->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                    @if($load->assigned_at)
                        <div class="flex items-start gap-3">
                            <i class="fas fa-user-check text-purple-600 mt-1"></i>
                            <div>
                                <p class="font-medium text-gray-900">Assigned</p>
                                <p class="text-xs text-gray-500">{{ $load->assigned_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    @endif
                    @if($load->picked_up_at)
                        <div class="flex items-start gap-3">
                            <i class="fas fa-truck text-indigo-600 mt-1"></i>
                            <div>
                                <p class="font-medium text-gray-900">Picked Up</p>
                                <p class="text-xs text-gray-500">{{ $load->picked_up_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    @endif
                    @if($load->delivered_at)
                        <div class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-green-600 mt-1"></i>
                            <div>
                                <p class="font-medium text-gray-900">Delivered</p>
                                <p class="text-xs text-gray-500">{{ $load->delivered_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    @endif
                    @if($load->cancelled_at)
                        <div class="flex items-start gap-3">
                            <i class="fas fa-times-circle text-red-600 mt-1"></i>
                            <div>
                                <p class="font-medium text-gray-900">Cancelled</p>
                                <p class="text-xs text-gray-500">{{ $load->cancelled_at->format('M d, Y h:i A') }}</p>
                                @if($load->cancellation_reason)
                                    <p class="text-xs text-red-600 mt-1">{{ $load->cancellation_reason }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

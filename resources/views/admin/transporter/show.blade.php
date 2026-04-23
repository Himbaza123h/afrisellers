@extends('layouts.home')

@section('page-content')
<div class="space-y-4">

    {{-- Page Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                <a href="{{ route('admin.transporters.index') }}" class="hover:text-gray-700">Transporters</a>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <span class="text-gray-700 font-medium">{{ $transporter->company_name ?? 'N/A' }}</span>
            </div>
            <h1 class="text-xl font-bold text-gray-900">Transporter Details</h1>
            <p class="mt-1 text-xs text-gray-500">Full profile and management actions</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.transporters.index') }}"
               class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm shadow-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-3 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <p class="text-sm font-medium text-red-900 flex-1">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- LEFT COLUMN --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Profile Card --}}
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5">
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center mb-3">
                        <span class="text-xl font-bold text-blue-700">
                            {{ strtoupper(substr($transporter->company_name ?? 'NA', 0, 2)) }}
                        </span>
                    </div>
                    <h2 class="text-base font-bold text-gray-900">{{ $transporter->company_name ?? 'N/A' }}</h2>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $transporter->user->email ?? 'N/A' }}</p>

                    {{-- Status Badge --}}
                    @php
                        $statusColors = [
                            'active'    => 'bg-green-100 text-green-800',
                            'inactive'  => 'bg-gray-100 text-gray-800',
                            'suspended' => 'bg-red-100 text-red-800',
                        ];
                        $statusColor = $statusColors[$transporter->status] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="mt-2 px-3 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                        {{ ucfirst($transporter->status ?? 'Unknown') }}
                    </span>

                    {{-- Verified Badge --}}
                    <span class="mt-1 px-3 py-1 rounded-full text-xs font-semibold {{ $transporter->is_verified ? 'bg-emerald-100 text-emerald-800' : 'bg-yellow-100 text-yellow-800' }}">
                        <i class="fas {{ $transporter->is_verified ? 'fa-shield-alt' : 'fa-clock' }} mr-1"></i>
                        {{ $transporter->is_verified ? 'Verified' : 'Unverified' }}
                    </span>
                </div>

                {{-- Rating --}}
                <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-3 gap-3 text-center">
                    <div>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($transporter->average_rating ?? 0, 1) }}</p>
                        <p class="text-xs text-gray-500">Rating</p>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($transporter->total_deliveries ?? 0) }}</p>
                        <p class="text-xs text-gray-500">Deliveries</p>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-gray-900">{{ $transporter->fleet_size ?? 0 }}</p>
                        <p class="text-xs text-gray-500">Fleet</p>
                    </div>
                </div>
            </div>

            {{-- Actions Card --}}
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Actions</h3>
                <div class="space-y-2">

                    {{-- Verify / Unverify --}}
                    {{-- @if(!$transporter->is_verified)
                        <form action="{{ route('admin.transporters.verify', $transporter) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Verify this transporter?')"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 bg-emerald-50 text-emerald-700 rounded-lg hover:bg-emerald-100 font-medium text-sm transition">
                                <i class="fas fa-shield-alt w-4"></i> Verify Transporter
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.transporters.unverify', $transporter) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Revoke verification for this transporter?')"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 bg-yellow-50 text-yellow-700 rounded-lg hover:bg-yellow-100 font-medium text-sm transition">
                                <i class="fas fa-shield-virus w-4"></i> Revoke Verification
                            </button>
                        </form>
                    @endif --}}

                    {{-- Activate / Suspend --}}
                    @if($transporter->status === 'suspended' || $transporter->status === 'inactive')
                        <form action="{{ route('admin.transporters.activate', $transporter) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Activate this transporter?')"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 font-medium text-sm transition">
                                <i class="fas fa-check-circle w-4"></i> Activate
                            </button>
                        </form>
                    @endif

                    @if($transporter->status === 'active')
                        <form action="{{ route('admin.transporters.suspend', $transporter) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Suspend this transporter?')"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 bg-orange-50 text-orange-700 rounded-lg hover:bg-orange-100 font-medium text-sm transition">
                                <i class="fas fa-ban w-4"></i> Suspend
                            </button>
                        </form>
                    @endif

                    <div class="border-t border-gray-100 pt-2">
                        <form action="{{ route('admin.transporters.destroy', $transporter) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    onclick="return confirm('Permanently delete this transporter? This cannot be undone.')"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 font-medium text-sm transition">
                                <i class="fas fa-trash w-4"></i> Delete Transporter
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Company Information --}}
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-building text-blue-600"></i> Company Information
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Company Name</p>
                        <p class="text-sm font-medium text-gray-900">{{ $transporter->company_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Registration Number</p>
                        <p class="text-sm font-medium text-gray-900">{{ $transporter->registration_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">License Number</p>
                        <p class="text-sm font-medium text-gray-900">{{ $transporter->license_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Country</p>
                        <p class="text-sm font-medium text-gray-900">{{ $transporter->country->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Phone</p>
                        <p class="text-sm font-medium text-gray-900">{{ $transporter->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Email</p>
                        <p class="text-sm font-medium text-gray-900">{{ $transporter->email ?? $transporter->user->email ?? 'N/A' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs text-gray-500 mb-0.5">Address</p>
                        <p class="text-sm font-medium text-gray-900">{{ $transporter->address ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            {{-- Fleet & Performance --}}
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-truck text-blue-600"></i> Fleet & Performance
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-blue-50 rounded-lg p-3 text-center">
                        <p class="text-xl font-bold text-blue-700">{{ $transporter->fleet_size ?? 0 }}</p>
                        <p class="text-xs text-blue-600 mt-0.5">Fleet Size</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-3 text-center">
                        <p class="text-xl font-bold text-green-700">{{ number_format($transporter->total_deliveries ?? 0) }}</p>
                        <p class="text-xs text-green-600 mt-0.5">Total Deliveries</p>
                    </div>
                    <div class="bg-emerald-50 rounded-lg p-3 text-center">
                        <p class="text-xl font-bold text-emerald-700">{{ number_format($transporter->successful_deliveries ?? 0) }}</p>
                        <p class="text-xs text-emerald-600 mt-0.5">Successful</p>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-3 text-center">
                        @php
                            $successRate = ($transporter->total_deliveries ?? 0) > 0
                                ? round(($transporter->successful_deliveries / $transporter->total_deliveries) * 100, 1)
                                : 0;
                        @endphp
                        <p class="text-xl font-bold text-yellow-700">{{ $successRate }}%</p>
                        <p class="text-xs text-yellow-600 mt-0.5">Success Rate</p>
                    </div>
                </div>

                {{-- Rating Bar --}}
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-xs text-gray-500">Average Rating</p>
                        <p class="text-sm font-bold text-gray-900">
                            {{ number_format($transporter->average_rating ?? 0, 1) }} / 5.0
                        </p>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-yellow-400 h-2 rounded-full"
                             style="width: {{ min(($transporter->average_rating ?? 0) / 5 * 100, 100) }}%">
                        </div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-400 mt-1">
                        <span>0</span><span>5</span>
                    </div>
                </div>
            </div>

            {{-- Account Information --}}
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-user text-blue-600"></i> Account Information
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Account Owner</p>
                        <p class="text-sm font-medium text-gray-900">{{ $transporter->user->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Owner Email</p>
                        <p class="text-sm font-medium text-gray-900">{{ $transporter->user->email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Registered</p>
                        <p class="text-sm font-medium text-gray-900">
                            {{ $transporter->created_at?->format('M d, Y') ?? 'N/A' }}
                            <span class="text-xs text-gray-400 ml-1">{{ $transporter->created_at?->format('h:i A') }}</span>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Last Updated</p>
                        <p class="text-sm font-medium text-gray-900">
                            {{ $transporter->updated_at?->format('M d, Y') ?? 'N/A' }}
                            <span class="text-xs text-gray-400 ml-1">{{ $transporter->updated_at?->format('h:i A') }}</span>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Account Status</p>
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                            {{ ucfirst($transporter->status ?? 'Unknown') }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Verification</p>
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $transporter->is_verified ? 'bg-emerald-100 text-emerald-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $transporter->is_verified ? 'Verified' : 'Not Verified' }}
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

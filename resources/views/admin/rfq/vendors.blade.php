@extends('layouts.home')

@push('styles')
<style>
    .vendor-card { transition: transform 0.2s, box-shadow 0.2s; }
    .vendor-card:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
</style>
@endpush

@section('page-content')
<div class="space-y-6">
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

    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.rfq.index') }}" class="flex items-center justify-center w-10 h-10 text-gray-600 transition-colors bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-gray-900 shadow-sm">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Vendor Responses</h1>
                <p class="mt-1 text-sm text-gray-500">
                    #RFQ-{{ str_pad($rfq->id, 6, '0', STR_PAD_LEFT) }} - {{ $rfq->product ? $rfq->product->name : 'General Inquiry' }}
                </p>
            </div>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
        </div>
    </div>

    <!-- RFQ Details Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-600"></i>
                RFQ Information
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="flex items-start gap-3">
                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg">
                        <i class="fas fa-info text-yellow-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 mb-1">Status</p>
                        @php
                            $statusColors = [
                                'pending' => ['Pending', 'bg-yellow-100 text-yellow-800'],
                                'accepted' => ['Accepted', 'bg-green-100 text-green-800'],
                                'rejected' => ['Rejected', 'bg-red-100 text-red-800'],
                                'closed' => ['Closed', 'bg-gray-100 text-gray-800'],
                            ];
                            $status = $statusColors[$rfq->status] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
                        @endphp
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium {{ $status[1] }}">{{ $status[0] }}</span>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                        <i class="fas fa-user text-blue-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 mb-1">Buyer</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $rfq->name ?? 'N/A' }}</p>
                        @if($rfq->email)
                            <p class="text-xs text-gray-500">{{ $rfq->email }}</p>
                        @endif
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                        <i class="fas fa-map-marker-alt text-purple-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 mb-1">Location</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $rfq->country ? $rfq->country->name : 'N/A' }}</p>
                        @if($rfq->city)
                            <p class="text-xs text-gray-500">{{ $rfq->city }}</p>
                        @endif
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                        <i class="fas fa-users text-green-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 mb-1">Responses</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $vendors->count() }} {{ Str::plural('Vendor', $vendors->count()) }}</p>
                        <p class="text-xs text-gray-500">Total responses</p>
                    </div>
                </div>
            </div>

            @if($rfq->message)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-xs font-medium text-gray-500 mb-2 flex items-center gap-2">
                        <i class="fas fa-comment-alt text-gray-400"></i>
                        Buyer Message
                    </p>
                    <p class="text-sm text-gray-700 bg-gray-50 rounded-lg p-4 border border-gray-100">{{ $rfq->message }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Vendors</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $vendors->count() }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-store mr-1 text-[10px]"></i> Responded
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
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Messages</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $vendors->sum('messages_count') }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <i class="fas fa-comments mr-1 text-[10px]"></i> All vendors
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl">
                    <i class="fas fa-comments text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Average Messages</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $vendors->count() > 0 ? number_format($vendors->sum('messages_count') / $vendors->count(), 1) : 0 }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-chart-line mr-1 text-[10px]"></i> Per vendor
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-green-50 to-green-100 rounded-xl">
                    <i class="fas fa-chart-bar text-2xl text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendors Section -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <i class="fas fa-users text-gray-600"></i>
                Vendor Responses ({{ $vendors->count() }})
            </h2>
        </div>

        <!-- Vendors Grid -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            @forelse($vendors as $vendor)
                <div class="vendor-card bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-6">
                        <!-- Vendor Header -->
                        <div class="flex items-center gap-4 mb-4">
                            <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-md">
                                <span class="text-xl font-bold text-white">{{ strtoupper(substr($vendor->name ?? 'V', 0, 1)) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-bold text-gray-900 truncate">{{ $vendor->name }}</h3>
                                <p class="text-sm text-gray-500 truncate">{{ $vendor->email }}</p>
                            </div>
                        </div>

                        <!-- Vendor Stats -->
                        <div class="space-y-3 mb-4">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100">
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center justify-center w-8 h-8 bg-white rounded-lg border border-gray-200">
                                        <i class="fas fa-comments text-purple-600 text-sm"></i>
                                    </div>
                                    <span class="text-sm text-gray-600">Messages</span>
                                </div>
                                <span class="text-sm font-bold text-gray-900">{{ $vendor->messages_count }}</span>
                            </div>

                            @if($vendor->phone)
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <i class="fas fa-phone w-4"></i>
                                    <span>{{ $vendor->phone }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Action Button -->
                        <a href="{{ route('admin.rfq.messages', ['rfq' => $rfq, 'vendor' => $vendor->id]) }}"
                            class="block w-full py-2.5 text-center text-sm font-semibold text-white bg-gradient-to-r from-red-600 to-red-700 rounded-lg hover:from-red-700 hover:to-red-800 transition-all shadow-md hover:shadow-lg">
                            <i class="fas fa-eye mr-2"></i>View Conversation
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-users text-4xl text-gray-300"></i>
                            </div>
                            <p class="text-lg font-semibold text-gray-900 mb-1">No vendor responses yet</p>
                            <p class="text-sm text-gray-500 mb-6">Vendors will appear here once they respond to this RFQ</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

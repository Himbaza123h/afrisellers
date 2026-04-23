@extends('layouts.home')

@section('page-content')
    <div class="space-y-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Vendor</h1>
                <p class="mt-1 text-xs text-gray-500">{{ $vendor->businessProfile?->business_name ?? 'N/A' }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.business-profile.show', $vendor->businessProfile) }}"
                   class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                    <i class="fas fa-building"></i> Business profile
                </a>
                <a href="{{ route('admin.vendors.edit', $vendor) }}"
                   class="inline-flex items-center gap-2 px-3 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-[#e60707] text-sm font-medium">
                    <i class="fas fa-edit"></i> Edit vendor
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3 bg-green-50 rounded-lg border border-green-200 text-sm text-green-900">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <p class="text-xs font-medium text-gray-500 uppercase">Vendor ID</p>
                <p class="text-lg font-bold text-gray-900">#{{ $vendor->id }}</p>
            </div>
            <div class="p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <p class="text-xs font-medium text-gray-500 uppercase">Account status</p>
                <p class="text-sm font-semibold text-gray-900 capitalize">{{ $vendor->account_status }}</p>
            </div>
            <div class="p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <p class="text-xs font-medium text-gray-500 uppercase">Products</p>
                <p class="text-sm font-semibold text-gray-900">{{ $stats['total_products'] }} total · {{ $stats['active_products'] }} active · {{ $stats['pending_products'] }} pending</p>
            </div>
        </div>

        <div class="p-4 bg-white rounded-lg border border-gray-200 shadow-sm space-y-2">
            <h2 class="text-sm font-semibold text-gray-800">Owner</h2>
            <p class="text-sm text-gray-700">{{ $vendor->user?->name ?? '—' }}</p>
            <p class="text-xs text-gray-500">{{ $vendor->user?->email ?? '' }}</p>
        </div>

        @if($vendor->businessProfile && ! $vendor->businessProfile->is_admin_verified)
            <form action="{{ route('admin.vendors.verify', $vendor) }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                    <i class="fas fa-check"></i> Verify business profile
                </button>
            </form>
        @endif
    </div>
@endsection

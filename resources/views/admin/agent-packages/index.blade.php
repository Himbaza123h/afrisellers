@extends('layouts.home')

@section('page-content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Agent Packages</h1>
            <p class="mt-1 text-xs text-gray-500">Create and manage subscription plans for agents</p>
        </div>
        <a href="{{ route('admin.agent-packages.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 text-sm font-medium shadow-sm">
            <i class="fas fa-plus"></i> New Package
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm text-green-900 flex-1 font-medium">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <p class="text-sm text-red-900 flex-1 font-medium">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-box-open text-amber-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Total Packages</p>
                <p class="text-xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Active</p>
                <p class="text-xl font-bold text-gray-900">{{ $stats['active'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-star text-yellow-500"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Featured</p>
                <p class="text-xl font-bold text-gray-900">{{ $stats['featured'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-dollar-sign text-blue-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Total Revenue</p>
                <p class="text-xl font-bold text-gray-900">${{ number_format($stats['revenue'], 2) }}</p>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Package</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Price</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Features</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Subscribers</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($packages as $package)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-crown text-amber-500 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $package->name }}</p>
                                        <p class="text-xs text-gray-400">{{ ucfirst($package->billing_cycle) }} · {{ $package->duration_days }}d</p>
                                    </div>
                                    @if($package->is_featured)
                                        <span class="ml-1 px-1.5 py-0.5 bg-yellow-100 text-yellow-700 text-[10px] font-bold rounded">FEATURED</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-bold text-gray-900">${{ number_format($package->price, 2) }}</span>
                                <p class="text-xs text-gray-400">/ {{ $package->billing_cycle }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-[11px] font-medium">
                                        <i class="fas fa-users text-[9px]"></i> {{ $package->max_referrals }}
                                    </span>
                                    @if($package->priority_support)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-purple-50 text-purple-700 rounded text-[11px] font-medium">
                                            <i class="fas fa-headset text-[9px]"></i> Support
                                        </span>
                                    @endif
                                    @if($package->commission_boost)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-50 text-green-700 rounded text-[11px] font-medium">
                                            <i class="fas fa-percentage text-[9px]"></i> {{ $package->commission_rate }}%
                                        </span>
                                    @endif
                                    @if($package->allow_rfqs)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-orange-50 text-orange-700 rounded text-[11px] font-medium">
                                            <i class="fas fa-file-invoice text-[9px]"></i> RFQ
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-bold text-gray-900">{{ $package->active_subscriptions_count }}</span>
                                <p class="text-xs text-gray-400">{{ $package->subscriptions_count }} total</p>
                            </td>
                            <td class="px-4 py-3">
                                <form action="{{ route('admin.agent-packages.toggle-status', $package) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold
                                        {{ $package->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                        <i class="fas fa-circle text-[6px]"></i>
                                        {{ $package->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.agent-packages.show', $package) }}"
                                       class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.agent-packages.edit', $package) }}"
                                       class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.agent-packages.destroy', $package) }}" method="POST"
                                          onsubmit="return confirm('Delete this package? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-400 text-sm">
                                No packages yet. <a href="{{ route('admin.agent-packages.create') }}" class="text-red-600 font-semibold hover:underline">Create one →</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($packages->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $packages->links() }}</div>
        @endif
    </div>
</div>
@endsection

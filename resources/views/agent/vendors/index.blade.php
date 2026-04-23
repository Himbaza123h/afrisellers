@extends('layouts.home')

@section('page-content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">My Vendors</h1>
            <p class="mt-1 text-xs text-gray-500">Vendors you have onboarded — {{ $stats['total'] }} / {{ $stats['limit'] }} slots used</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <a href="{{ route('agent.vendors.print') }}" target="_blank"
               class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium shadow-sm">
                <i class="fas fa-print"></i> Print
            </a>
            <form action="{{ route('agent.vendors.export') }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium shadow-sm">
                    <i class="fas fa-download"></i> Export CSV
                </button>
            </form>
            @if($stats['total'] < $stats['limit'])
                <a href="{{ route('agent.vendors.create') }}"
                   class="inline-flex items-center gap-2 px-3 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 text-sm font-medium shadow-sm">
                    <i class="fas fa-plus"></i> Add Vendor
                </a>
            @else
                <a href="{{ route('agent.subscriptions.plans') }}"
                   class="inline-flex items-center gap-2 px-3 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 text-sm font-medium shadow-sm">
                    <i class="fas fa-crown"></i> Upgrade to Add More
                </a>
            @endif
        </div>
    </div>

    {{-- Subscription slot bar --}}
    @php $pct = $stats['limit'] > 0 ? min(100, round($stats['total'] / $stats['limit'] * 100)) : 0; @endphp
    <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-semibold text-gray-600">Vendor Slots Used</span>
            <span class="text-xs font-bold {{ $pct >= 100 ? 'text-red-600' : 'text-gray-700' }}">
                {{ $stats['total'] }} / {{ $stats['limit'] }}
            </span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="h-2 rounded-full transition-all {{ $pct >= 100 ? 'bg-red-500' : ($pct >= 75 ? 'bg-amber-500' : 'bg-green-500') }}"
                 style="width: {{ $pct }}%"></div>
        </div>
        @if($pct >= 100)
            <p class="mt-1 text-xs text-red-600 font-medium">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                Limit reached. <a href="{{ route('agent.subscriptions.plans') }}" class="underline">Upgrade your plan</a> to add more vendors.
            </p>
        @endif
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach([
            ['label'=>'Total',     'value'=>$stats['total'],     'color'=>'blue',   'icon'=>'fa-store'],
            ['label'=>'Active',    'value'=>$stats['active'],    'color'=>'green',  'icon'=>'fa-check-circle'],
            ['label'=>'Pending',   'value'=>$stats['pending'],   'color'=>'yellow', 'icon'=>'fa-clock'],
            ['label'=>'Suspended', 'value'=>$stats['suspended'], 'color'=>'red',    'icon'=>'fa-ban'],
        ] as $card)
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 flex items-center gap-3">
            <div class="flex items-center justify-center w-10 h-10 bg-{{ $card['color'] }}-100 rounded-lg flex-shrink-0">
                <i class="fas {{ $card['icon'] }} text-{{ $card['color'] }}-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">{{ $card['label'] }}</p>
                <p class="text-xl font-bold text-gray-900">{{ $card['value'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm text-green-900 flex-1 font-medium">{!! session('success') !!}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <p class="text-sm text-red-900 flex-1 font-medium">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Filter --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 no-print">
        <form method="GET" action="{{ route('agent.vendors.index') }}" class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by name or email…"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="active"    {{ request('status') == 'active'    ? 'selected' : '' }}>Active</option>
                    <option value="pending"   {{ request('status') == 'pending'   ? 'selected' : '' }}>Pending</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
            <a href="{{ route('agent.vendors.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">
                <i class="fas fa-undo mr-1"></i> Reset
            </a>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900">Vendor List</h2>
            <span class="px-2 py-1 text-xs font-semibold text-purple-700 bg-purple-100 rounded-full">
                {{ $vendors->total() }} {{ Str::plural('vendor', $vendors->total()) }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Business</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Contact</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Location</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Joined</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($vendors as $vendor)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-store text-purple-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm">
                                            {{ $vendor->businessProfile?->business_name ?? 'N/A' }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ $vendor->businessProfile?->business_type ?? '—' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm text-gray-800">{{ $vendor->user?->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $vendor->user?->email }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm text-gray-700">{{ $vendor->businessProfile?->country?->name ?? '—' }}</p>
                                <p class="text-xs text-gray-400">{{ $vendor->businessProfile?->city ?? '' }}</p>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusMap = [
                                        'active'    => 'bg-green-100 text-green-700',
                                        'pending'   => 'bg-yellow-100 text-yellow-700',
                                        'suspended' => 'bg-red-100 text-red-700',
                                        'rejected'  => 'bg-gray-100 text-gray-600',
                                    ];
                                    $cls = $statusMap[$vendor->account_status] ?? 'bg-gray-100 text-gray-600';
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold {{ $cls }}">
                                    <i class="fas fa-circle text-[6px]"></i>
                                    {{ ucfirst($vendor->account_status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ $vendor->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('agent.vendors.show', $vendor->id) }}"
                                       class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg" title="View">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="{{ route('agent.vendors.edit', $vendor->id) }}"
                                       class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg" title="Edit">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    @if($vendor->account_status === 'active')
                                        <form action="{{ route('agent.vendors.suspend', $vendor->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg" title="Suspend"
                                                onclick="return confirm('Suspend this vendor?')">
                                                <i class="fas fa-ban text-sm"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('agent.vendors.activate', $vendor->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg" title="Activate">
                                                <i class="fas fa-check text-sm"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('agent.vendors.destroy', $vendor->id) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Remove this vendor from your account? The vendor account will remain active.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg" title="Remove">
                                            <i class="fas fa-unlink text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-store text-3xl text-gray-300"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium">No vendors yet</p>
                                    <p class="text-xs text-gray-400 mt-1 mb-4">Add your first vendor to get started</p>
                                    <a href="{{ route('agent.vendors.create') }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white rounded-lg text-sm font-medium hover:bg-red-700">
                                        <i class="fas fa-plus"></i> Add Vendor
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vendors->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 flex items-center justify-between">
                <span class="text-xs text-gray-600">
                    Showing {{ $vendors->firstItem() }}–{{ $vendors->lastItem() }} of {{ $vendors->total() }}
                </span>
                <div class="text-sm">{{ $vendors->links() }}</div>
            </div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.home')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .tab-content { animation: fadeIn 0.3s ease-in-out; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .custom-scrollbar::-webkit-scrollbar { height: 8px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #888; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #555; }
</style>
@endpush

@section('page-content')
<div class="space-y-4">

    {{-- Page Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Country Agents Management</h1>
            <p class="mt-1 text-xs text-gray-500">Monitor and manage agents in {{ $country->name }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button
                onclick="window.open('{{ route('country.agents.print') }}' + window.location.search, '_blank')"
                class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="flex gap-2 border-b border-gray-200 no-print">
        <button onclick="switchTab('overview')" id="tab-overview"
            class="tab-button px-4 py-2 text-sm font-semibold text-blue-600 border-b-2 border-blue-600 transition-colors">
            <i class="fas fa-chart-line mr-2"></i> Overview
        </button>
        <button onclick="switchTab('agents')" id="tab-agents"
            class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-user-tie mr-2"></i> Agents
        </button>
        <button onclick="switchTab('analytics')" id="tab-analytics"
            class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-chart-bar mr-2"></i> Analytics
        </button>
    </div>

    {{-- ===================== OVERVIEW TAB ===================== --}}
    <div id="tab-overview-content" class="tab-content">

        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

            {{-- Total --}}
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Agents</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                        <div class="mt-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-user-tie mr-1 text-[8px]"></i> All agents
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-blue-50 rounded-lg">
                        <i class="fas fa-user-tie text-blue-600"></i>
                    </div>
                </div>
            </div>

            {{-- Active --}}
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Active Agents</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['active']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $stats['active_percentage'] }}%
                            </span>
                            <span class="text-xs text-gray-500">of total</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-green-50 rounded-lg">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
            </div>

            {{-- Suspended --}}
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Suspended</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['suspended']) }}</p>
                        <div class="mt-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-ban mr-1 text-[8px]"></i> Inactive
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-red-50 rounded-lg">
                        <i class="fas fa-ban text-red-600"></i>
                    </div>
                </div>
            </div>

            {{-- Inactive --}}
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Inactive</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['inactive']) }}</p>
                        <div class="mt-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                <i class="fas fa-circle-xmark mr-1 text-[8px]"></i> No status
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gray-50 rounded-lg">
                        <i class="fas fa-circle-xmark text-gray-500"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Session messages --}}
        @if(session('success'))
            <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-start gap-2 mt-4">
                <i class="fas fa-check-circle text-green-600 mt-0.5 text-sm"></i>
                <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
                <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        @endif

        {{-- Filters --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mt-4">
            <form method="GET" action="{{ route('country.agents.index') }}" class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">

                    {{-- Search --}}
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search by name or email..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    {{-- Date Range --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Date Range</label>
                        <input type="text" id="dateRangePicker" placeholder="Select dates" readonly
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg cursor-pointer text-sm">
                        <input type="hidden" name="date_from" id="dateFrom" value="{{ request('date_from') }}">
                        <input type="hidden" name="date_to"   id="dateTo"   value="{{ request('date_to') }}">
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <select name="status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Status</option>
                            <option value="active"    {{ request('status') == 'active'    ? 'selected' : '' }}>Active</option>
                            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="inactive"  {{ request('status') == 'inactive'  ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    {{-- Sort By --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Sort By</label>
                        <select name="sort_by"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date</option>
                            <option value="name"       {{ request('sort_by') == 'name'       ? 'selected' : '' }}>Name</option>
                            <option value="email"      {{ request('sort_by') == 'email'      ? 'selected' : '' }}>Email</option>
                            <option value="status"     {{ request('sort_by') == 'status'     ? 'selected' : '' }}>Status</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                        class="inline-flex items-center gap-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        <i class="fas fa-filter text-sm"></i> Apply
                    </button>
                    <a href="{{ route('country.agents.index') }}"
                        class="inline-flex items-center gap-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                        <i class="fas fa-undo text-sm"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ===================== AGENTS TAB ===================== --}}
    <div id="tab-agents-content" class="tab-content hidden">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-900">Agents List</h2>
                <span class="px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">
                    {{ $agents->total() }} {{ Str::plural('agent', $agents->total()) }}
                </span>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Agent</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Email</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Country</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Registered</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($agents as $agent)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center font-bold text-indigo-700 text-sm shrink-0">
                                            {{ strtoupper(substr($agent->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $agent->name }}</p>
                                            <p class="text-xs text-gray-500">ID: #{{ $agent->id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $agent->email }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $agent->country->name ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <p class="text-sm text-gray-900">{{ $agent->created_at->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ $agent->created_at->format('h:i A') }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $map = [
                                            'active'    => ['Active',    'bg-green-100 text-green-800'],
                                            'suspended' => ['Suspended', 'bg-red-100 text-red-800'],
                                        ];
                                        [$label, $cls] = $map[$agent->status] ?? ['Inactive', 'bg-gray-100 text-gray-700'];
                                    @endphp
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $cls }}">{{ $label }}</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('country.agents.show', $agent->id) }}"
                                            class="text-blue-600 hover:text-blue-700 text-sm font-medium px-2 py-1 rounded hover:bg-blue-50" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($agent->status === 'active')
                                            <form action="{{ route('country.agents.suspend', $agent->id) }}" method="POST" class="inline"
                                                onsubmit="return confirm('Suspend this agent?')">
                                                @csrf
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-700 text-sm px-2 py-1 rounded hover:bg-red-50" title="Suspend">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('country.agents.activate', $agent->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="text-green-600 hover:text-green-700 text-sm px-2 py-1 rounded hover:bg-green-50" title="Activate">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-2">
                                            <i class="fas fa-user-tie text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium">No agents found</p>
                                        <p class="text-xs text-gray-400 mt-1">Try adjusting your filters</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($agents->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 flex items-center justify-between">
                    <span class="text-xs text-gray-700">
                        Showing {{ $agents->firstItem() }}–{{ $agents->lastItem() }} of {{ $agents->total() }}
                    </span>
                    <div class="text-sm">{{ $agents->links() }}</div>
                </div>
            @endif
        </div>
    </div>

    {{-- ===================== ANALYTICS TAB ===================== --}}
    <div id="tab-analytics-content" class="tab-content hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

            {{-- Status Distribution --}}
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Account Status Distribution</h3>
                <div class="space-y-4">

                    @php
                        $rows = [
                            ['Active Agents',    $stats['active'],    'bg-green-600',  'text-green-700'],
                            ['Suspended Agents', $stats['suspended'], 'bg-red-600',    'text-red-700'],
                            ['Inactive Agents',  $stats['inactive'],  'bg-gray-500',   'text-gray-700'],
                        ];
                    @endphp

                    @foreach($rows as [$title, $count, $barCls, $textCls])
                        @php $pct = $stats['total'] > 0 ? round(($count / $stats['total']) * 100) : 0; @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700">{{ $title }}</span>
                                <span class="text-sm font-bold {{ $textCls }}">{{ number_format($count) }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="{{ $barCls }} h-3 rounded-full transition-all duration-300"
                                    style="width: {{ $pct }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $pct }}% of total</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Recent Registrations --}}
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Recent Agent Registrations</h3>
                @php
                    $recent = \App\Models\User::whereHas('roles', fn($q) => $q->where('slug','agent'))
                        ->where('country_id', $country->id)
                        ->orderByDesc('created_at')
                        ->take(6)->get();
                @endphp
                <div class="space-y-3">
                    @forelse($recent as $a)
                        @php [$lbl,$cls] = $map[$a->status] ?? ['Inactive','bg-gray-100 text-gray-700']; @endphp
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center font-bold text-indigo-700 text-sm shrink-0">
                                    {{ strtoupper(substr($a->name,0,1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $a->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $a->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $cls }}">{{ $lbl }}</span>
                                <a href="{{ route('country.agents.show', $a->id) }}"
                                    class="text-blue-600 hover:text-blue-700 text-sm px-2 py-1 rounded hover:bg-blue-50">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                            <p class="text-sm text-gray-500">No agents registered yet</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
flatpickr("#dateRangePicker", {
    mode: "range",
    dateFormat: "Y-m-d",
    showMonths: 2,
    onChange: function(selectedDates) {
        if (selectedDates.length === 2) {
            document.getElementById('dateFrom').value = flatpickr.formatDate(selectedDates[0], 'Y-m-d');
            document.getElementById('dateTo').value   = flatpickr.formatDate(selectedDates[1], 'Y-m-d');
        }
    },
    defaultDate: [
        document.getElementById('dateFrom').value,
        document.getElementById('dateTo').value
    ].filter(d => d)
});

function switchTab(name) {
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
        btn.classList.add('text-gray-600');
    });
    const active = document.getElementById(`tab-${name}`);
    active.classList.remove('text-gray-600');
    active.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');

    document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
    document.getElementById(`tab-${name}-content`).classList.remove('hidden');
}

document.addEventListener('DOMContentLoaded', () => switchTab('overview'));
</script>
@endpush
@endsection

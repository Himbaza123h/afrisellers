@extends('layouts.home')

@push('styles')
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    @media print { .no-print { display: none !important; } }
</style>
@endpush

@section('page-content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Countries Management</h1>
            <p class="mt-1 text-xs text-gray-500">Manage countries and their configurations</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <button onclick="printReport()"
                class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-print"></i><span>Print</span>
            </button>
            <a href="{{ route('admin.country.create') }}"
               class="inline-flex items-center gap-2 px-3 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-plus"></i><span>Add Country</span>
            </a>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-2 border-b border-gray-200 no-print">
        <button onclick="switchTab('all')" id="tab-all"
            class="tab-button px-4 py-2 text-sm font-semibold text-[#ff0808] border-b-2 border-[#ff0808] transition-colors">All</button>
        <button onclick="switchTab('stats')" id="tab-stats"
            class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">Stats</button>
        <button onclick="switchTab('table')" id="tab-table"
            class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">Table</button>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3 no-print">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if($errors->any())
        <div class="p-3 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3 no-print">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <div class="flex-1">
                @foreach($errors->all() as $error)
                    <p class="text-sm font-medium text-red-900">{{ $error }}</p>
                @endforeach
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Stats Section --}}
    <div id="stats-section">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total</p>
                <p class="text-2xl font-black text-gray-900">{{ $stats['total'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Countries</p>
            </div>
            <div class="stat-card bg-white rounded-xl border border-green-200 shadow-sm p-4">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Active</p>
                <p class="text-2xl font-black text-green-600">{{ $stats['active'] }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $stats['active_percentage'] }}% of total</p>
            </div>
            <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Inactive</p>
                <p class="text-2xl font-black text-gray-500">{{ $stats['inactive'] }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $stats['inactive_percentage'] }}% of total</p>
            </div>
            <div class="stat-card bg-white rounded-xl border border-blue-200 shadow-sm p-4">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Regions</p>
                <p class="text-2xl font-black text-blue-600">{{ $stats['total_regions'] }}</p>
                <p class="text-xs text-gray-500 mt-1">~{{ $stats['avg_countries_per_region'] }} / region</p>
            </div>
            <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Vendors</p>
                <p class="text-2xl font-black text-gray-900">{{ number_format($stats['total_vendors']) }}</p>
                <p class="text-xs text-gray-500 mt-1">Across all countries</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div id="table-section">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden no-print">
            <form method="GET" action="{{ route('admin.country.index') }}"
                  class="flex flex-wrap gap-3 p-4 border-b border-gray-100 bg-gray-50">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search name or code…"
                    class="flex-1 min-w-[180px] px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#ff0808]/20 focus:border-[#ff0808]">

                <select name="region" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#ff0808]/20 focus:border-[#ff0808]">
                    <option value="">All Regions</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}" {{ request('region') == $region->id ? 'selected' : '' }}>
                            {{ $region->name }}
                        </option>
                    @endforeach
                </select>

                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#ff0808]/20 focus:border-[#ff0808]">
                    <option value="">All Status</option>
                    <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>

                <button type="submit"
                    class="px-4 py-2 bg-[#ff0808] text-white rounded-lg text-sm font-semibold hover:bg-red-700 transition-colors">
                    <i class="fas fa-search mr-1"></i> Filter
                </button>
                @if(request()->hasAny(['search','status','region']))
                    <a href="{{ route('admin.country.index') }}"
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-300 transition-colors">
                        Clear
                    </a>
                @endif
            </form>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-wider">Country</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-wider hidden sm:table-cell">Code</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-wider hidden md:table-cell">Region</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-wider hidden lg:table-cell">Vendors</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-right text-[10px] font-black text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($countries as $country)
                        <tr class="hover:bg-gray-50 transition-colors">
                            {{-- Flag + Name --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if($country->flag_url)
                                        <img src="{{ $country->flag_url }}" alt="{{ $country->name }}"
                                             class="w-8 h-6 object-cover rounded border border-gray-200 flex-shrink-0"
                                             onerror="this.style.display='none'">
                                    @else
                                        <div class="w-8 h-6 bg-gray-200 rounded border border-gray-200 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-flag text-gray-400 text-[10px]"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('admin.country.show', $country) }}"
                                           class="font-semibold text-gray-900 hover:text-[#ff0808] transition-colors">
                                            {{ $country->name }}
                                        </a>
                                        {{-- Show region on mobile --}}
                                        @if($country->region)
                                            <p class="text-[10px] text-gray-400 md:hidden">{{ $country->region->name }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Code --}}
                            <td class="px-4 py-3 hidden sm:table-cell">
                                <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">
                                    {{ $country->code ?? '—' }}
                                </span>
                            </td>

                            {{-- Region --}}
                            <td class="px-4 py-3 hidden md:table-cell">
                                @if($country->region)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-700 text-xs font-semibold rounded-lg border border-blue-100">
                                        <i class="fas fa-globe-africa text-[9px]"></i>
                                        {{ $country->region->name }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>

                            {{-- Vendors --}}
                            <td class="px-4 py-3 hidden lg:table-cell">
                                <span class="text-sm font-semibold text-gray-700">
                                    {{ number_format($country->vendors_count ?? 0) }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold
                                             {{ $country->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $country->status === 'active' ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                    {{ ucfirst($country->status) }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.country.show', $country) }}"
                                       class="p-1.5 text-gray-500 hover:text-[#ff0808] hover:bg-red-50 rounded-lg transition-colors" title="View">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.country.edit', $country) }}"
                                       class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.country.toggle-status', $country) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="p-1.5 rounded-lg transition-colors text-xs
                                                   {{ $country->status === 'active' ? 'text-gray-500 hover:text-orange-600 hover:bg-orange-50' : 'text-gray-500 hover:text-green-600 hover:bg-green-50' }}"
                                            title="{{ $country->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-{{ $country->status === 'active' ? 'pause' : 'play' }} text-xs"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.country.destroy', $country) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Delete {{ $country->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <i class="fas fa-globe text-3xl text-gray-200 mb-3 block"></i>
                                <p class="text-sm text-gray-400">No countries found.</p>
                                <a href="{{ route('admin.country.create') }}"
                                   class="mt-2 inline-block text-xs text-[#ff0808] font-semibold hover:underline">Add a country →</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($countries->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $countries->links() }}
            </div>
            @endif
        </div>
    </div>

</div>

<script>
function switchTab(tab) {
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('text-[#ff0808]', 'border-b-2', 'border-[#ff0808]');
        btn.classList.add('text-gray-600');
    });
    const activeTab = document.getElementById('tab-' + tab);
    activeTab.classList.remove('text-gray-600');
    activeTab.classList.add('text-[#ff0808]', 'border-b-2', 'border-[#ff0808]');

    const stats = document.getElementById('stats-section');
    const table = document.getElementById('table-section');
    if (tab === 'all')   { stats.style.display = 'block'; table.style.display = 'block'; }
    if (tab === 'stats') { stats.style.display = 'block'; table.style.display = 'none';  }
    if (tab === 'table') { stats.style.display = 'none';  table.style.display = 'block'; }
}

function printReport() {
    window.open('{{ route("admin.countries.print") }}', '_blank');
}
</script>
@endsection

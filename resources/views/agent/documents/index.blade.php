@extends('layouts.home')

@section('page-content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">My Documents</h1>
            <p class="mt-1 text-xs text-gray-500">Store, organise and share your business documents</p>
        </div>
        <a href="{{ route('agent.documents.upload') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 text-sm font-semibold shadow-md">
            <i class="fas fa-upload"></i> Upload Document
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm text-green-900 font-medium flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <p class="text-sm text-red-900 font-medium flex-1">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Expiry Warning --}}
    @if($stats['expiring_soon'] > 0)
        <div class="p-4 bg-amber-50 rounded-lg border border-amber-200 flex items-start gap-3">
            <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5 flex-shrink-0"></i>
            <p class="text-sm text-amber-800 font-medium">
                <strong>{{ $stats['expiring_soon'] }}</strong>
                {{ Str::plural('document', $stats['expiring_soon']) }} will expire within 30 days.
                <a href="{{ route('agent.documents.index', ['expiring'=>1]) }}" class="underline">Review now</a>
            </p>
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        @php
        $formattedTotal = $stats['total_size'] >= 1073741824
            ? number_format($stats['total_size'] / 1073741824, 2) . ' GB'
            : ($stats['total_size'] >= 1048576
                ? number_format($stats['total_size'] / 1048576, 2) . ' MB'
                : number_format($stats['total_size'] / 1024, 2) . ' KB');
        @endphp
        @foreach([
            ['label'=>'Total Files',    'value'=>$stats['total'],         'color'=>'blue',   'icon'=>'fa-folder-open', 'sub'=>$formattedTotal . ' used'],
            ['label'=>'Shared',         'value'=>$stats['shared'],        'color'=>'purple', 'icon'=>'fa-share-alt',   'sub'=>'visible to vendors'],
            ['label'=>'Expiring Soon',  'value'=>$stats['expiring_soon'], 'color'=>'amber',  'icon'=>'fa-clock',       'sub'=>'within 30 days'],
            ['label'=>'Expired',        'value'=>$stats['expired'],       'color'=>'red',    'icon'=>'fa-exclamation-circle', 'sub'=>'need attention'],
        ] as $card)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-{{ $card['color'] }}-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas {{ $card['icon'] }} text-{{ $card['color'] }}-600 text-sm"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">{{ $card['label'] }}</p>
                <p class="text-xl font-bold text-gray-900">{{ $card['value'] }}</p>
                <p class="text-[10px] text-gray-400">{{ $card['sub'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">

        {{-- Sidebar: Categories --}}
        <div class="lg:col-span-1 space-y-3">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Categories</h3>
                <nav class="space-y-1">
                    <a href="{{ route('agent.documents.index', array_merge(request()->except('category','page'), [])) }}"
                       class="flex items-center justify-between px-3 py-2 rounded-lg text-sm transition-colors
                           {{ !request('category') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-th-large w-4 text-center"></i> All Files
                        </span>
                        <span class="text-xs font-bold">{{ $stats['total'] }}</span>
                    </a>
                    @php
                        $categoryMeta = [
                            'contract'  => ['fa-file-signature', 'text-blue-500',   'Contracts'],
                            'invoice'   => ['fa-file-invoice',   'text-green-500',  'Invoices'],
                            'identity'  => ['fa-id-card',        'text-purple-500', 'Identity'],
                            'agreement' => ['fa-handshake',      'text-amber-500',  'Agreements'],
                            'report'    => ['fa-chart-bar',      'text-rose-500',   'Reports'],
                            'license'   => ['fa-certificate',    'text-cyan-500',   'Licenses'],
                            'other'     => ['fa-file',           'text-gray-400',   'Other'],
                        ];
                    @endphp
                    @foreach($categoryMeta as $cat => [$icon, $iconCls, $label])
                        @if(($categoryBreakdown[$cat] ?? 0) > 0 || request('category') === $cat)
                        <a href="{{ route('agent.documents.index', array_merge(request()->except('category','page'), ['category'=>$cat])) }}"
                           class="flex items-center justify-between px-3 py-2 rounded-lg text-sm transition-colors
                               {{ request('category') === $cat ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                            <span class="flex items-center gap-2">
                                <i class="fas {{ $icon }} {{ $iconCls }} w-4 text-center text-sm"></i>
                                {{ $label }}
                            </span>
                            <span class="text-xs font-bold text-gray-500">
                                {{ $categoryBreakdown[$cat] ?? 0 }}
                            </span>
                        </a>
                        @endif
                    @endforeach
                </nav>
            </div>

            {{-- Storage summary --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Storage</h3>
                @php
                    $limitBytes  = 500 * 1024 * 1024; // 500 MB display limit
                    $usedPct     = min(100, round(($stats['total_size'] / $limitBytes) * 100));
                @endphp
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs text-gray-600">Used</span>
                    <span class="text-xs font-bold {{ $usedPct >= 90 ? 'text-red-600' : 'text-gray-700' }}">
                        {{ $formattedTotal }}
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all
                        {{ $usedPct >= 90 ? 'bg-red-500' : ($usedPct >= 70 ? 'bg-amber-500' : 'bg-blue-500') }}"
                         style="width: {{ $usedPct }}%"></div>
                </div>
                <p class="text-[10px] text-gray-400 mt-1">{{ $stats['total'] }} {{ Str::plural('file', $stats['total']) }}</p>
            </div>
        </div>

        {{-- Main: Document Grid --}}
        <div class="lg:col-span-3 space-y-3">

            {{-- Filters + Sort --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-3">
                <form method="GET" action="{{ route('agent.documents.index') }}"
                      class="flex flex-wrap gap-2 items-center">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <div class="flex-1 min-w-[180px] relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs mt-2"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search documents…"
                            class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <select name="sort"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="latest" {{ request('sort','latest')=='latest' ?'selected':'' }}>Newest First</option>
                        <option value="oldest" {{ request('sort')=='oldest' ?'selected':'' }}>Oldest First</option>
                        <option value="name"   {{ request('sort')=='name'   ?'selected':'' }}>Name A–Z</option>
                        <option value="size"   {{ request('sort')=='size'   ?'selected':'' }}>Largest First</option>
                    </select>
                    <label class="flex items-center gap-1.5 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="shared" value="1" {{ request('shared') ?'checked':'' }}
                            class="rounded border-gray-300 text-blue-600">
                        Shared only
                    </label>
                    <button type="submit"
                        class="px-3 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    <a href="{{ route('agent.documents.index') }}"
                       class="px-3 py-2 border border-gray-300 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50">
                        <i class="fas fa-undo mr-1"></i> Reset
                    </a>

                    {{-- View toggle --}}
                    <div class="flex gap-1 ml-auto">
                        <button type="button" onclick="setView('grid')" id="btn-grid"
                            class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
                            <i class="fas fa-th-large text-sm"></i>
                        </button>
                        <button type="button" onclick="setView('list')" id="btn-list"
                            class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
                            <i class="fas fa-list text-sm"></i>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Results count --}}
            <div class="flex items-center justify-between px-1">
                <p class="text-xs text-gray-500">
                    Showing <strong>{{ $documents->total() }}</strong> {{ Str::plural('document', $documents->total()) }}
                </p>
            </div>

            {{-- GRID VIEW --}}
            <div id="view-grid">
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                    @forelse($documents as $doc)
                        @php
                            $expired      = $doc->isExpired();
                            $expiringSoon = $doc->isExpiringSoon();
                        @endphp
                        <div class="bg-white rounded-xl border {{ $expired ? 'border-red-200' : ($expiringSoon ? 'border-amber-200' : 'border-gray-200') }} shadow-sm hover:shadow-md transition-shadow group flex flex-col">

                            {{-- File type header --}}
                            <div class="p-4 flex items-start justify-between">
                                <div class="w-12 h-12 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fas {{ $doc->icon }} text-xl"></i>
                                </div>
                                <div class="flex items-center gap-1 ml-2">
                                    @if($doc->is_shared)
                                        <span class="px-1.5 py-0.5 bg-purple-100 text-purple-600 text-[9px] font-bold rounded uppercase">
                                            Shared
                                        </span>
                                    @endif
                                    @if($expired)
                                        <span class="px-1.5 py-0.5 bg-red-100 text-red-600 text-[9px] font-bold rounded uppercase">
                                            Expired
                                        </span>
                                    @elseif($expiringSoon)
                                        <span class="px-1.5 py-0.5 bg-amber-100 text-amber-600 text-[9px] font-bold rounded uppercase">
                                            Expiring
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Info --}}
                            <div class="px-4 pb-3 flex-1">
                                <h3 class="text-sm font-bold text-gray-900 truncate group-hover:text-blue-600 transition-colors">
                                    {{ $doc->title }}
                                </h3>
                                <p class="text-[10px] text-gray-400 mt-0.5 truncate">{{ $doc->file_name }}</p>
                                @if($doc->description)
                                    <p class="text-xs text-gray-500 mt-1 line-clamp-2 leading-relaxed">
                                        {{ $doc->description }}
                                    </p>
                                @endif

                                {{-- Tags --}}
                                @if($doc->tags)
                                    <div class="flex flex-wrap gap-1 mt-2">
                                        @foreach(array_slice($doc->tags, 0, 3) as $tag)
                                            <span class="px-1.5 py-0.5 bg-gray-100 text-gray-500 text-[9px] rounded font-medium">
                                                #{{ $tag }}
                                            </span>
                                        @endforeach
                                        @if(count($doc->tags) > 3)
                                            <span class="text-[9px] text-gray-400">+{{ count($doc->tags) - 3 }} more</span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            {{-- Footer --}}
                            <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
                                <div>
                                    <p class="text-[10px] text-gray-400">
                                        {{ $doc->formatted_size }} &middot; {{ $doc->created_at->format('M d, Y') }}
                                    </p>
                                    @if($doc->expires_at)
                                        <p class="text-[10px] {{ $expired ? 'text-red-500 font-semibold' : ($expiringSoon ? 'text-amber-600' : 'text-gray-400') }}">
                                            <i class="fas fa-clock mr-0.5"></i>
                                            {{ $expired ? 'Expired ' : 'Expires ' }}{{ $doc->expires_at->format('M d, Y') }}
                                        </p>
                                    @endif
                                </div>
                                <div class="flex gap-1">
                                    <a href="{{ route('agent.documents.show', $doc->id) }}"
                                       class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg" title="View">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('agent.documents.download', $doc->id) }}"
                                       class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg" title="Download">
                                        <i class="fas fa-download text-xs"></i>
                                    </a>
                                    <form action="{{ route('agent.documents.destroy', $doc->id) }}" method="POST"
                                          onsubmit="return confirm('Delete this document? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg" title="Delete">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 flex flex-col items-center py-16">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                <i class="fas fa-folder-open text-3xl text-gray-300"></i>
                            </div>
                            <p class="text-gray-500 font-medium">No documents found</p>
                            <p class="text-xs text-gray-400 mt-1 mb-5">
                                {{ request()->hasAny(['search','category','shared'])
                                    ? 'Try adjusting your filters.'
                                    : 'Upload your first document to get started.' }}
                            </p>
                            <a href="{{ route('agent.documents.upload') }}"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white rounded-lg text-sm font-medium hover:bg-red-700">
                                <i class="fas fa-upload"></i> Upload Document
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- LIST VIEW --}}
            <div id="view-list" class="hidden">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Document</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Category</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Size</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Expires</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Uploaded</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($documents as $doc)
                                @php
                                    $expired      = $doc->isExpired();
                                    $expiringSoon = $doc->isExpiringSoon();
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors {{ $expired ? 'bg-red-50/30' : '' }}">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <i class="fas {{ $doc->icon }}"></i>
                                            </div>
                                            <div>
                                                <a href="{{ route('agent.documents.show', $doc->id) }}"
                                                   class="text-sm font-semibold text-gray-900 hover:text-blue-600">
                                                    {{ Str::limit($doc->title, 40) }}
                                                </a>
                                                <p class="text-[10px] text-gray-400">{{ $doc->file_name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="capitalize text-xs text-gray-600">{{ $doc->category }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-500">{{ $doc->formatted_size }}</td>
                                    <td class="px-4 py-3">
                                        @if($doc->expires_at)
                                            <span class="text-xs {{ $expired ? 'text-red-600 font-semibold' : ($expiringSoon ? 'text-amber-600' : 'text-gray-500') }}">
                                                {{ $doc->expires_at->format('M d, Y') }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-500">
                                        {{ $doc->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <a href="{{ route('agent.documents.show', $doc->id) }}"
                                               class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg" title="View">
                                                <i class="fas fa-eye text-xs"></i>
                                            </a>
                                            <a href="{{ route('agent.documents.download', $doc->id) }}"
                                               class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg" title="Download">
                                                <i class="fas fa-download text-xs"></i>
                                            </a>
                                            <form action="{{ route('agent.documents.destroy', $doc->id) }}" method="POST"
                                                  onsubmit="return confirm('Delete this document?')">
                                                @csrf @method('DELETE')
                                                <button class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg" title="Delete">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center text-gray-400 text-sm">
                                        No documents found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            @if($documents->hasPages())
                <div class="flex items-center justify-between px-1">
                    <span class="text-xs text-gray-500">
                        Showing {{ $documents->firstItem() }}–{{ $documents->lastItem() }} of {{ $documents->total() }}
                    </span>
                    <div class="text-sm">{{ $documents->links() }}</div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const PREF_KEY = 'doc_view_pref';

function setView(view) {
    document.getElementById('view-grid').classList.toggle('hidden', view !== 'grid');
    document.getElementById('view-list').classList.toggle('hidden', view !== 'list');
    document.getElementById('btn-grid').classList.toggle('bg-blue-50', view === 'grid');
    document.getElementById('btn-grid').classList.toggle('text-blue-600', view === 'grid');
    document.getElementById('btn-list').classList.toggle('bg-blue-50', view === 'list');
    document.getElementById('btn-list').classList.toggle('text-blue-600', view === 'list');
    localStorage.setItem(PREF_KEY, view);
}

document.addEventListener('DOMContentLoaded', () => {
    setView(localStorage.getItem(PREF_KEY) || 'grid');
});
</script>
@endpush

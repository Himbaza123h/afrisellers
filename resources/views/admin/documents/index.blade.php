@extends('layouts.home')

@section('page-content')
<div class="space-y-5">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Agent Documents</h1>
            <p class="mt-1 text-xs text-gray-500">All documents uploaded by agents</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm text-green-900 font-medium flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach([
            ['label'=>'Total',       'value'=>$stats['total'],     'color'=>'gray',  'icon'=>'fa-folder'],
            ['label'=>'Need Attn',   'value'=>$stats['attention'], 'color'=>'red',   'icon'=>'fa-exclamation-triangle', 'filter'=>['attention'=>'1']],
            ['label'=>'Shared',      'value'=>$stats['shared'],    'color'=>'purple','icon'=>'fa-share-alt',            'filter'=>['shared'=>'1']],
            ['label'=>'Expired',     'value'=>$stats['expired'],   'color'=>'amber', 'icon'=>'fa-clock',                'filter'=>['expired'=>'1']],
        ] as $card)
        <a href="{{ route('admin.documents.index', $card['filter'] ?? []) }}"
           class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3 hover:shadow-md transition-all">
            <div class="w-9 h-9 bg-{{ $card['color'] }}-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas {{ $card['icon'] }} text-{{ $card['color'] }}-600 text-xs"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-500">{{ $card['label'] }}</p>
                <p class="text-xl font-bold text-gray-900">{{ $card['value'] }}</p>
            </div>
        </a>
        @endforeach
    </div>

    {{-- User filter banner --}}
    @if($filterUser)
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-blue-200 flex items-center justify-center font-bold text-blue-700 flex-shrink-0">
            {{ strtoupper(substr($filterUser->name, 0, 1)) }}
        </div>
        <div class="flex-1">
            <p class="text-sm font-semibold text-blue-900">Documents for: {{ $filterUser->name }}</p>
            <p class="text-xs text-blue-600">{{ $filterUser->email }}</p>
        </div>
        <a href="{{ route('admin.documents.index') }}"
           class="px-3 py-1.5 bg-white border border-blue-200 text-blue-700 rounded-lg text-xs font-semibold hover:bg-blue-50">
            <i class="fas fa-times mr-1"></i> Clear
        </a>
    </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <form method="GET" action="{{ route('admin.documents.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Search</label>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs mt-2"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Title, filename, agent name…"
                           class="w-full pl-8 pr-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#ff0808]/20 focus:border-[#ff0808]">
                </div>
            </div>
            <div class="min-w-[130px]">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Category</label>
                <select name="category" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#ff0808]/20 focus:border-[#ff0808]">
                    <option value="">All</option>
                    @foreach(['contract','invoice','identity','agreement','report','license','other'] as $cat)
                        <option value="{{ $cat }}" {{ request('category')===$cat?'selected':'' }}>{{ ucfirst($cat) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[130px]">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Attention</label>
                <select name="attention" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#ff0808]/20 focus:border-[#ff0808]">
                    <option value="">All Docs</option>
                    <option value="1" {{ request('attention')==='1'?'selected':'' }}>Needs Attention</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-[#ff0808] text-white rounded-lg text-sm font-semibold hover:bg-red-700">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                @if(request()->hasAny(['search','category','attention','shared','user_id']))
                    <a href="{{ route('admin.documents.index') }}"
                       class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-200">
                        <i class="fas fa-times mr-1"></i> Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Documents Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-800">
                Documents
                <span class="ml-2 px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">{{ $documents->total() }}</span>
            </h2>
        </div>

        @if($documents->count())
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Document</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Agent</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Category</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Size</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Uploaded</th>
                        <th class="px-5 py-3 text-right text-[10px] font-bold text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($documents as $doc)
                    <tr class="hover:bg-gray-50 transition-colors {{ $doc->requires_attention ? 'bg-red-50/40' : '' }}">
                        <td class="px-5 py-4">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 {{ $doc->requires_attention ? 'bg-red-100' : 'bg-blue-50' }} rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas {{ $doc->requires_attention ? 'fa-exclamation-triangle text-red-500' : 'fa-file text-blue-500' }} text-xs"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-semibold text-gray-900 line-clamp-1 max-w-[200px]">{{ $doc->title }}</p>
                                        @if($doc->requires_attention)
                                            <span class="flex-shrink-0 px-1.5 py-0.5 bg-red-100 text-red-700 text-[9px] font-bold rounded uppercase tracking-wider">
                                                Needs Attention
                                            </span>
                                        @endif
                                        @if($doc->is_shared)
                                            <span class="flex-shrink-0 px-1.5 py-0.5 bg-purple-100 text-purple-600 text-[9px] font-bold rounded uppercase tracking-wider">
                                                Shared
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $doc->file_name }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <a href="{{ route('admin.documents.index', ['user_id' => $doc->user_id]) }}"
                               class="flex items-center gap-2 group">
                                <div class="w-7 h-7 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-[10px] font-bold text-gray-500">{{ strtoupper(substr($doc->user?->name ?? '?', 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-800 group-hover:text-[#ff0808] transition-colors">{{ $doc->user?->name ?? 'N/A' }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $doc->user?->email }}</p>
                                </div>
                            </a>
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-xs text-gray-600">{{ ucfirst($doc->category) }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-xs text-gray-600">
                                {{ $doc->file_size >= 1048576
                                    ? number_format($doc->file_size / 1048576, 1) . ' MB'
                                    : number_format($doc->file_size / 1024, 0) . ' KB' }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-xs text-gray-600">{{ $doc->created_at->format('M d, Y') }}</p>
                            <p class="text-[10px] text-gray-400">{{ $doc->created_at->diffForHumans() }}</p>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.documents.download', $doc) }}"
                                   class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-semibold hover:bg-blue-100 transition-colors">
                                    <i class="fas fa-download"></i>
                                </a>
                                @if($doc->requires_attention)
                                <form action="{{ route('admin.documents.clear-attention', $doc) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="px-3 py-1.5 bg-red-50 text-red-700 rounded-lg text-xs font-semibold hover:bg-red-100 transition-colors"
                                            title="Clear attention flag">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('admin.documents.destroy', $doc) }}" method="POST"
                                      onsubmit="return confirm('Delete this document permanently?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded-lg text-xs font-semibold hover:bg-red-50 hover:text-red-600 transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($documents->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">{{ $documents->links() }}</div>
        @endif
        @else
        <div class="flex flex-col items-center py-16">
            <i class="fas fa-folder-open text-3xl text-gray-200 mb-3"></i>
            <p class="text-sm text-gray-500">No documents found</p>
        </div>
        @endif
    </div>

</div>
@endsection

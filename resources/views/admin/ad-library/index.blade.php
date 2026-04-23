@extends('layouts.home')

@section('page-content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Ad Library</h1>
            <p class="mt-1 text-xs text-gray-500">Upload and manage all your ad media — images, GIFs, videos, documents</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.ad-placements.index') }}"
               class="px-3 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 text-sm font-medium shadow-sm">
                <i class="fas fa-map-marker-alt mr-1"></i> Placements
            </a>
            <button onclick="document.getElementById('upload-modal').classList.remove('hidden')"
                    class="px-4 py-2 bg-[#ff0808] text-white rounded-lg text-sm font-semibold hover:bg-red-700 shadow-sm">
                <i class="fas fa-cloud-upload-alt mr-1"></i> Upload Files
            </button>
        </div>
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

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach([
            ['label' => 'Total Files',   'value' => $stats['total'],                                                          'color' => 'gray',   'icon' => 'fa-photo-video'],
            ['label' => 'Images & GIFs', 'value' => $stats['images'],                                                         'color' => 'blue',   'icon' => 'fa-image'],
            ['label' => 'Videos',        'value' => $stats['videos'],                                                         'color' => 'purple', 'icon' => 'fa-film'],
            ['label' => 'Storage Used',  'value' => ($stats['size'] < 1_048_576 ? round($stats['size']/1024,1).'KB' : round($stats['size']/1_048_576,1).'MB'), 'color' => 'amber', 'icon' => 'fa-hdd'],
        ] as $card)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
            <div class="w-9 h-9 bg-{{ $card['color'] }}-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas {{ $card['icon'] }} text-{{ $card['color'] }}-600 text-xs"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-500">{{ $card['label'] }}</p>
                <p class="text-xl font-bold text-gray-900">{{ $card['value'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Search</label>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs mt-2"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="File name…"
                           class="w-full pl-8 pr-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#ff0808]/20 focus:border-[#ff0808]">
                </div>
            </div>
            <div class="min-w-[140px]">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Type</label>
                <select name="type" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#ff0808]/20 focus:border-[#ff0808]">
                    <option value="">All Types</option>
                    <option value="image"    {{ request('type')==='image'    ?'selected':'' }}>Images</option>
                    <option value="gif"      {{ request('type')==='gif'      ?'selected':'' }}>GIFs</option>
                    <option value="video"    {{ request('type')==='video'    ?'selected':'' }}>Videos</option>
                    <option value="document" {{ request('type')==='document' ?'selected':'' }}>Documents</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-[#ff0808] text-white rounded-lg text-sm font-semibold hover:bg-red-700">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                @if(request()->hasAny(['search','type']))
                    <a href="{{ route('admin.ad-library.index') }}"
                       class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-200">
                        <i class="fas fa-times mr-1"></i> Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Media Grid --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-gray-800">
                Library
                <span class="ml-2 px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">{{ $media->total() }}</span>
            </h2>
        </div>

        @if($media->count())
        <div class="p-5 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($media as $item)
            @php
                $typeColors = [
                    'image'    => ['bg-blue-100',   'text-blue-600',   'fa-image'],
                    'gif'      => ['bg-green-100',  'text-green-600',  'fa-image'],
                    'video'    => ['bg-purple-100', 'text-purple-600', 'fa-film'],
                    'document' => ['bg-amber-100',  'text-amber-600',  'fa-file-pdf'],
                ];
                [$tbg, $tcol, $ticon] = $typeColors[$item->type] ?? ['bg-gray-100','text-gray-500','fa-file'];
            @endphp
            <div class="group relative bg-gray-50 rounded-xl border border-gray-200 overflow-hidden hover:border-[#ff0808]/40 hover:shadow-md transition-all">
                {{-- Thumbnail --}}
                <div class="aspect-square bg-gray-100 flex items-center justify-center overflow-hidden">
                    @if($item->is_image)
                        <img src="{{ $item->url }}" alt="{{ $item->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @elseif($item->is_video)
                        <div class="relative w-full h-full flex items-center justify-center bg-gray-800">
                            <i class="fas fa-play-circle text-white text-3xl"></i>
                        </div>
                    @else
                        <div class="flex flex-col items-center gap-1">
                            <i class="fas {{ $ticon }} {{ $tcol }} text-3xl"></i>
                            <span class="text-[10px] text-gray-500 uppercase font-bold">{{ strtoupper(pathinfo($item->original_name, PATHINFO_EXTENSION)) }}</span>
                        </div>
                    @endif
                </div>

                {{-- Type badge --}}
                <span class="absolute top-2 left-2 px-1.5 py-0.5 {{ $tbg }} {{ $tcol }} text-[9px] font-bold rounded uppercase tracking-wider">
                    {{ $item->type }}
                </span>

                {{-- Overlay actions --}}
                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                    <a href="{{ $item->url }}" target="_blank"
                       class="p-2 bg-white rounded-lg text-gray-700 hover:text-[#ff0808] transition-colors text-xs"
                       title="Preview">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.ad-placements.create', ['media_id' => $item->id]) }}"
                       class="p-2 bg-white rounded-lg text-gray-700 hover:text-blue-600 transition-colors text-xs"
                       title="Place this ad">
                        <i class="fas fa-map-marker-alt"></i>
                    </a>
                    <form action="{{ route('admin.ad-library.destroy', $item) }}" method="POST"
                          onsubmit="return confirm('Delete this file from library?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="p-2 bg-white rounded-lg text-gray-700 hover:text-red-600 transition-colors text-xs"
                                title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>

                {{-- Info --}}
                <div class="p-2 border-t border-gray-100">
                    <p class="text-[11px] font-semibold text-gray-700 truncate" title="{{ $item->name }}">{{ $item->name }}</p>
                    <div class="flex items-center justify-between mt-0.5">
                        <span class="text-[10px] text-gray-400">{{ $item->formatted_size }}</span>
                        <span class="text-[10px] text-gray-400">{{ $item->placements()->count() }} placed</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($media->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $media->links() }}</div>
        @endif
        @else
        <div class="flex flex-col items-center py-20">
            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
                <i class="fas fa-photo-video text-2xl text-gray-300"></i>
            </div>
            <p class="text-sm font-semibold text-gray-500 mb-1">No files uploaded yet</p>
            <p class="text-xs text-gray-400 mb-4">Upload images, GIFs, videos, or documents</p>
            <button onclick="document.getElementById('upload-modal').classList.remove('hidden')"
                    class="px-4 py-2 bg-[#ff0808] text-white rounded-lg text-sm font-semibold hover:bg-red-700">
                <i class="fas fa-cloud-upload-alt mr-1"></i> Upload Your First File
            </button>
        </div>
        @endif
    </div>

</div>

{{-- ══════════════════════════════════════════════════════
     UPLOAD MODAL
══════════════════════════════════════════════════════ --}}
<div id="upload-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
        <div class="flex items-center justify-between p-5 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900"><i class="fas fa-cloud-upload-alt text-[#ff0808] mr-2"></i>Upload Files</h3>
            <button onclick="document.getElementById('upload-modal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('admin.ad-library.store') }}" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">
            @csrf

            {{-- Drop zone --}}
            <div id="drop-zone"
                 class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-[#ff0808] transition-colors cursor-pointer"
                 onclick="document.getElementById('file-input').click()">
                <div class="flex flex-col items-center gap-2">
                    <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-cloud-upload-alt text-[#ff0808] text-xl"></i>
                    </div>
                    <p class="text-sm font-semibold text-gray-700">Drop files here or click to browse</p>
                    <p class="text-xs text-gray-400">JPG, PNG, WebP, GIF, MP4, WebM, PDF — max {{ \App\Models\AdMedia::maxUploadMb() }}MB each — up to 20 files</p>
                </div>
                <input type="file" id="file-input" name="files[]" multiple accept=".jpg,.jpeg,.png,.webp,.gif,.mp4,.webm,.pdf"
                       class="hidden" onchange="showFileList(this)">
            </div>

            {{-- File list preview --}}
            <div id="file-list" class="hidden space-y-2 max-h-48 overflow-y-auto"></div>

            <div class="flex justify-end gap-3">
                <button type="button"
                        onclick="document.getElementById('upload-modal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-200">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-[#ff0808] text-white rounded-lg text-sm font-semibold hover:bg-red-700">
                    <i class="fas fa-upload mr-1"></i> Upload
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Drag & drop
const dz = document.getElementById('drop-zone');
dz.addEventListener('dragover',  e => { e.preventDefault(); dz.classList.add('border-[#ff0808]','bg-red-50'); });
dz.addEventListener('dragleave', e => { dz.classList.remove('border-[#ff0808]','bg-red-50'); });
dz.addEventListener('drop', e => {
    e.preventDefault();
    dz.classList.remove('border-[#ff0808]','bg-red-50');
    document.getElementById('file-input').files = e.dataTransfer.files;
    showFileList(document.getElementById('file-input'));
});

function showFileList(input) {
    const list = document.getElementById('file-list');
    list.innerHTML = '';
    if (!input.files.length) { list.classList.add('hidden'); return; }
    list.classList.remove('hidden');
    Array.from(input.files).forEach(f => {
        const size = f.size < 1048576 ? (f.size/1024).toFixed(1)+' KB' : (f.size/1048576).toFixed(1)+' MB';
        const div = document.createElement('div');
        div.className = 'flex items-center gap-3 p-2 bg-gray-50 rounded-lg';
        div.innerHTML = `<i class="fas fa-file text-gray-400 text-xs w-4"></i>
            <p class="text-xs text-gray-700 flex-1 truncate">${f.name}</p>
            <span class="text-[10px] text-gray-400 flex-shrink-0">${size}</span>`;
        list.appendChild(div);
    });
}
</script>
@endsection

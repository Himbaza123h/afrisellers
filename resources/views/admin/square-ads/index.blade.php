@extends('layouts.home')
@section('page-content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Square Ads</h1>
            <p class="mt-1 text-xs text-gray-500">Square ad units pulled from the Ad Library</p>
        </div>
        <a href="{{ route('admin.square-ads.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-[#dd0606] font-medium text-sm">
            <i class="fas fa-plus"></i> New Square Ad
        </a>
    </div>

    @if(session('success'))
        <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()"><i class="fas fa-times text-green-600"></i></button>
        </div>
    @endif

    {{-- Filter --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="type" value="{{ request('type') }}"
                   placeholder="Filter by type..."
                   class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808]">
            <button type="submit" class="px-4 py-1.5 bg-[#ff0808] text-white rounded-lg text-sm font-medium">Filter</button>
            <a href="{{ route('admin.square-ads.index') }}" class="px-4 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium">Reset</a>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Preview</th>
                        <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">File</th>
                        <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Type</th>
                        <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Headline</th>
                        <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Badge</th>
                        <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Order</th>
                        <th class="px-4 py-3 text-xs font-semibold text-center text-gray-700 uppercase">Status</th>
                        <th class="px-4 py-3 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($ads as $ad)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            @if($ad->media)
                                @if($ad->media->type === 'video')
                                    <div class="w-14 h-14 bg-gray-100 rounded border border-gray-200 flex items-center justify-center">
                                        <i class="fas fa-play text-gray-400 text-lg"></i>
                                    </div>
                                @else
                                    <img src="{{ Storage::url($ad->media->file_path) }}"
                                         class="w-14 h-14 object-cover rounded border border-gray-200">
                                @endif
                            @else
                                <div class="w-14 h-14 bg-gray-100 rounded border border-gray-200 flex items-center justify-center">
                                    <i class="fas fa-image text-gray-300"></i>
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-xs text-gray-500 truncate max-w-[140px]">{{ $ad->media?->name ?? '—' }}</p>
                        </td>
                        <td class="px-4 py-3">
                            @if($ad->type)
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ $ad->type }}</span>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-xs font-semibold text-gray-900 truncate max-w-[180px]">{{ $ad->headline }}</p>
                            @if($ad->sub_text)
                                <p class="text-[10px] text-gray-400 truncate max-w-[180px]">{{ $ad->sub_text }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($ad->badge)
                                <span class="px-2 py-0.5 text-[10px] font-black tracking-widest uppercase rounded-sm text-white"
                                      style="background:{{ $ad->accent ?? '#ff0808' }};">{{ $ad->badge }}</span>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs text-gray-600">{{ $ad->sort_order }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <form action="{{ route('admin.square-ads.toggle-status', $ad) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        class="px-2.5 py-1 rounded-full text-xs font-medium transition-colors
                                            {{ $ad->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                    {{ $ad->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.square-ads.edit', $ad) }}"
                                   class="text-blue-600 hover:text-blue-800" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.square-ads.destroy', $ad) }}" method="POST"
                                      class="inline" onsubmit="return confirm('Delete this square ad?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center">
                            <i class="fas fa-th-large text-4xl text-gray-300 mb-3 block"></i>
                            <p class="text-sm text-gray-500 mb-3">No square ads yet</p>
                            <a href="{{ route('admin.square-ads.create') }}"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white rounded-lg text-sm font-medium">
                                <i class="fas fa-plus"></i> Add First Square Ad
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($ads->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">{{ $ads->links() }}</div>
        @endif
    </div>

</div>
@endsection

@extends('layouts.home')
@section('page-content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Fallback Ads</h1>
            <p class="mt-1 text-xs text-gray-500">Shown when no real running ads exist for a position</p>
        </div>
        <a href="{{ route('admin.fallback-ads.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-[#dd0606] font-medium text-sm">
            <i class="fas fa-plus"></i> New Fallback Ad
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()"><i class="fas fa-times text-green-600"></i></button>
        </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <select name="position" class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808]">
                <option value="">All Positions</option>
                @foreach($positions as $key => $label)
                    <option value="{{ $key }}" {{ request('position')===$key?'selected':'' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="type" class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808]">
                <option value="">All Types</option>
                @foreach($types as $key => $label)
                    <option value="{{ $key }}" {{ request('type')===$key?'selected':'' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-1.5 bg-[#ff0808] text-white rounded-lg text-sm font-medium">Filter</button>
            <a href="{{ route('admin.fallback-ads.index') }}" class="px-4 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium">Reset</a>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Preview</th>
                        <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Position</th>
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
                            @if($ad->type === 'text')
                                <div class="w-14 h-9 rounded border flex items-center justify-center text-white text-[10px] font-bold"
                                     style="background:{{ $ad->bg ?? '#ff0808' }};">T</div>
                            @elseif($ad->media)
                                <img src="{{ $ad->media }}" class="w-14 h-9 object-cover rounded border border-gray-200">
                            @else
                                <div class="w-14 h-9 bg-gray-100 rounded border border-gray-200 flex items-center justify-center">
                                    <i class="fas fa-image text-gray-300 text-xs"></i>
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-medium text-gray-700">{{ $positions[$ad->position] ?? $ad->position }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ ucfirst($ad->type) }}</span>
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
                                      style="background:{{ $ad->accent ?? '#ff0808' }};">
                                    {{ $ad->badge }}
                                </span>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs text-gray-600">{{ $ad->sort_order }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <form action="{{ route('admin.fallback-ads.toggle-status', $ad) }}" method="POST" class="inline">
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
                                <a href="{{ route('admin.fallback-ads.edit', $ad) }}"
                                   class="text-blue-600 hover:text-blue-800" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.fallback-ads.destroy', $ad) }}" method="POST"
                                      class="inline" onsubmit="return confirm('Delete this fallback ad?')">
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
                            <i class="fas fa-images text-4xl text-gray-300 mb-3 block"></i>
                            <p class="text-sm text-gray-500 mb-3">No fallback ads yet</p>
                            <a href="{{ route('admin.fallback-ads.create') }}"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white rounded-lg text-sm font-medium">
                                <i class="fas fa-plus"></i> Add First Fallback Ad
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

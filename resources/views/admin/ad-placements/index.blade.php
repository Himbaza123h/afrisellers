@extends('layouts.home')

@section('page-content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Ad Placements</h1>
            <p class="mt-1 text-xs text-gray-500">Assign library media to positions across the site</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.ad-library.index') }}"
               class="px-3 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 text-sm font-medium shadow-sm">
                <i class="fas fa-photo-video mr-1"></i> Library
            </a>
            <a href="{{ route('admin.ad-placements.create') }}"
               class="px-4 py-2 bg-[#ff0808] text-white rounded-lg text-sm font-semibold hover:bg-red-700 shadow-sm">
                <i class="fas fa-plus mr-1"></i> New Placement
            </a>
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

    {{-- Position Cards Grid --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        @foreach($positions as $key => $label)
        @php
            $positionPlacements = $placements->get($key, collect());
            $activePlacement    = $positionPlacements->firstWhere('is_active', true);
            $hasActive          = (bool) $activePlacement;
        @endphp
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden
                    {{ $hasActive ? 'border-green-200' : '' }}">

            {{-- Position Header --}}
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 bg-gray-50">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full {{ $hasActive ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                    <p class="text-sm font-bold text-gray-800">{{ $label }}</p>
                    <span class="px-1.5 py-0.5 bg-gray-200 text-gray-500 text-[9px] font-mono rounded">{{ $key }}</span>
                </div>
                <a href="{{ route('admin.ad-placements.create', ['position' => $key]) }}"
                   class="px-3 py-1.5 bg-[#ff0808] text-white rounded-lg text-[11px] font-semibold hover:bg-red-700 transition-colors">
                    <i class="fas fa-plus mr-1"></i> Assign
                </a>
            </div>

            {{-- Placements for this position --}}
            @if($positionPlacements->isEmpty())
                <div class="flex items-center gap-3 px-5 py-6 text-center">
                    <div class="flex-1 flex flex-col items-center">
                        <i class="fas fa-image text-2xl text-gray-200 mb-2"></i>
                        <p class="text-xs text-gray-400">No ad assigned to this position</p>
                        <a href="{{ route('admin.ad-placements.create', ['position' => $key]) }}"
                           class="mt-2 text-xs text-[#ff0808] font-semibold hover:underline">
                            Assign from library →
                        </a>
                    </div>
                </div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach($positionPlacements as $placement)
                    @php
                        $isLive = $placement->is_live;
                    @endphp
                    <div class="flex items-center gap-4 px-5 py-3.5 {{ !$placement->is_active ? 'opacity-60' : '' }}">

                        {{-- Thumbnail --}}
                        <div class="w-14 h-14 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0 border border-gray-200">
                            @if($placement->media?->is_image)
                                <img src="{{ $placement->media->url }}" alt="{{ $placement->media->name }}"
                                     class="w-full h-full object-cover">
                            @elseif($placement->media?->is_video)
                                <div class="w-full h-full flex items-center justify-center bg-gray-800">
                                    <i class="fas fa-play-circle text-white text-lg"></i>
                                </div>
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-file-pdf text-amber-500 text-lg"></i>
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-800 truncate">
                                {{ $placement->media?->name ?? 'Media deleted' }}
                            </p>
                            <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                {{-- Live badge --}}
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[9px] font-bold
                                             {{ $isLive ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    <i class="fas fa-circle text-[4px]"></i>
                                    {{ $isLive ? 'LIVE' : ($placement->is_active ? 'SCHEDULED' : 'INACTIVE') }}
                                </span>
                                {{-- Type --}}
                                <span class="text-[10px] text-gray-400 uppercase font-semibold">{{ $placement->media?->type }}</span>
                                {{-- Dates --}}
                                @if($placement->starts_at || $placement->ends_at)
                                    <span class="text-[10px] text-gray-400">
                                        {{ $placement->starts_at?->format('d M') ?? '∞' }} – {{ $placement->ends_at?->format('d M') ?? '∞' }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            {{-- Toggle --}}
                            <form action="{{ route('admin.ad-placements.toggle', $placement) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="p-1.5 rounded-lg text-xs transition-colors
                                               {{ $placement->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}"
                                        title="{{ $placement->is_active ? 'Deactivate' : 'Activate' }}">
                                    <i class="fas {{ $placement->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                </button>
                            </form>
                            {{-- Edit --}}
                            <a href="{{ route('admin.ad-placements.edit', $placement) }}"
                               class="p-1.5 bg-gray-100 text-gray-600 rounded-lg text-xs hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                <i class="fas fa-edit"></i>
                            </a>
                            {{-- Delete --}}
                            <form action="{{ route('admin.ad-placements.destroy', $placement) }}" method="POST"
                                  onsubmit="return confirm('Remove this placement?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="p-1.5 bg-gray-100 text-gray-600 rounded-lg text-xs hover:bg-red-50 hover:text-red-600 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>

                    </div>
                    @endforeach
                </div>
            @endif

        </div>
        @endforeach
    </div>

</div>
@endsection

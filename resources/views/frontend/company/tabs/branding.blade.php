{{-- ═══ TAB: Branding & Content ══════════════════════════════════════ --}}

@php
$items = [
    [
        'field' => 'logo',
        'icon'  => 'fas fa-image',
        'color' => 'text-[#ff0808]',
        'bg'    => 'bg-red-50',
        'label' => 'Company Logo',
        'type'  => 'logo',
    ],
    [
        'field' => 'cover_image',
        'icon'  => 'fas fa-panorama',
        'color' => 'text-indigo-600',
        'bg'    => 'bg-indigo-50',
        'label' => 'Cover Image / Banner',
        'type'  => 'cover',
    ],
    [
        'field' => 'short_description',
        'icon'  => 'fas fa-align-left',
        'color' => 'text-blue-600',
        'bg'    => 'bg-blue-50',
        'label' => 'Short Description',
        'type'  => 'text',
    ],
    [
        'field' => 'full_description',
        'icon'  => 'fas fa-file-alt',
        'color' => 'text-purple-600',
        'bg'    => 'bg-purple-50',
        'label' => 'Full Description',
        'type'  => 'text',
    ],
    [
        'field' => 'promo_video_url',
        'icon'  => 'fas fa-play-circle',
        'color' => 'text-rose-600',
        'bg'    => 'bg-rose-50',
        'label' => 'Promotional Video',
        'type'  => 'url',
    ],
];
@endphp

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-5">

    {{-- Header --}}
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2">
        <div class="w-7 h-7 bg-purple-50 rounded-lg flex items-center justify-center">
            <i class="fas fa-paint-brush text-purple-600 text-xs"></i>
        </div>
        <h2 class="text-sm font-black text-gray-800">Branding & Content</h2>
    </div>

    {{-- Fields --}}
    <div class="divide-y divide-gray-50">
        @foreach($items as $item)
        @php $val = $profile->{$item['field']} ?? null; @endphp

        <div class="flex items-center gap-4 px-5 py-4 {{ $val ? '' : 'opacity-60' }}">

            {{-- Icon --}}
            <div class="w-8 h-8 {{ $item['bg'] }} rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="{{ $item['icon'] }} {{ $item['color'] }} text-xs"></i>
            </div>

            {{-- Label + Value --}}
            <div class="flex-1 min-w-0">
                <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">
                    {{ $item['label'] }}
                </p>

                @if($val)
                    @if($item['type'] === 'logo')
                        <img src="{{ Storage::url($val) }}" alt="Logo"
                             class="w-10 h-10 rounded-md object-cover border border-gray-100">

                    @elseif($item['type'] === 'cover')
                        <img src="{{ Storage::url($val) }}" alt="Cover"
                             class="w-full h-16 rounded-md object-cover border border-gray-100">

                    @elseif($item['type'] === 'url')
                        <a href="{{ $val }}" target="_blank" rel="noopener noreferrer"
                           class="text-xs font-semibold text-[#ff0808] hover:underline truncate block">
                            {{ Str::limit($val, 50) }}
                        </a>

                    @else
                        <p class="text-xs font-semibold text-gray-800 leading-relaxed line-clamp-2">
                            {{ Str::limit($val, 100) }}
                        </p>
                    @endif
                @else
                    <p class="text-xs text-gray-400 italic">Not added</p>
                @endif
            </div>

            {{-- Status badge --}}
            @if($val)
                <span class="flex-shrink-0 px-2 py-0.5 bg-green-50 text-green-700 text-[10px] font-bold rounded-md flex items-center gap-1">
                    <i class="fas fa-check-circle text-[8px]"></i> Filled
                </span>
            @else
                <span class="flex-shrink-0 px-2 py-0.5 bg-gray-50 text-gray-400 text-[10px] font-semibold rounded-md">
                    Empty
                </span>
            @endif

        </div>
        @endforeach
    </div>

</div>

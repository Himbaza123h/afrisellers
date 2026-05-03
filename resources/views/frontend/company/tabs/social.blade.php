{{-- ═══ TAB: Social Media ══════════════════════════════════════ --}}

@php
$items = [
    [
        'field' => 'facebook_url',
        'icon'  => 'fab fa-facebook',
        'color' => 'text-blue-600',
        'bg'    => 'bg-blue-50',
        'label' => 'Facebook',
    ],
    [
        'field' => 'instagram_url',
        'icon'  => 'fab fa-instagram',
        'color' => 'text-pink-600',
        'bg'    => 'bg-pink-50',
        'label' => 'Instagram',
    ],
    [
        'field' => 'twitter_url',
        'icon'  => 'fab fa-twitter',
        'color' => 'text-sky-500',
        'bg'    => 'bg-sky-50',
        'label' => 'Twitter / X',
    ],
    [
        'field' => 'linkedin_url',
        'icon'  => 'fab fa-linkedin',
        'color' => 'text-blue-700',
        'bg'    => 'bg-blue-50',
        'label' => 'LinkedIn',
    ],
    [
        'field' => 'youtube_url',
        'icon'  => 'fab fa-youtube',
        'color' => 'text-red-600',
        'bg'    => 'bg-red-50',
        'label' => 'YouTube',
    ],
    [
        'field' => 'tiktok_url',
        'icon'  => 'fab fa-tiktok',
        'color' => 'text-gray-900',
        'bg'    => 'bg-gray-100',
        'label' => 'TikTok',
    ],
];
@endphp

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-5">

    {{-- Header --}}
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2">
        <div class="w-7 h-7 bg-sky-50 rounded-lg flex items-center justify-center">
            <i class="fas fa-share-alt text-sky-500 text-xs"></i>
        </div>
        <h2 class="text-sm font-black text-gray-800">Social Media</h2>
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
                    <a href="{{ $val }}" target="_blank" rel="noopener noreferrer"
                       class="text-xs font-semibold text-[#ff0808] hover:underline truncate block">
                        {{ Str::limit($val, 45) }}
                    </a>
                @else
                    <p class="text-xs text-gray-400 italic">Not added</p>
                @endif
            </div>

            {{-- Status badge / external link --}}
            @if($val)
                <a href="{{ $val }}" target="_blank" rel="noopener noreferrer"
                   class="flex-shrink-0 px-2 py-0.5 bg-green-50 text-green-700 text-[10px] font-bold rounded-md flex items-center gap-1 hover:bg-green-100 transition-colors">
                    <i class="fas fa-check-circle text-[8px]"></i> Connected
                </a>
            @else
                <span class="flex-shrink-0 px-2 py-0.5 bg-gray-50 text-gray-400 text-[10px] font-semibold rounded-md">
                    Empty
                </span>
            @endif

        </div>
        @endforeach
    </div>

</div>

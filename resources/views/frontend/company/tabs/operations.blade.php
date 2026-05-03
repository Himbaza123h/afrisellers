{{-- ═══ TAB: Operations & Presence ══════════════════════════════════════ --}}

@php
$details = [
    [
        'field' => 'presence_countries',
        'icon'  => 'fas fa-globe-africa',
        'color' => 'text-teal-600',
        'bg'    => 'bg-teal-50',
        'label' => 'Countries Present',
        'suffix' => ' countries',
    ],
    [
        'field' => 'branches_count',
        'icon'  => 'fas fa-code-branch',
        'color' => 'text-blue-500',
        'bg'    => 'bg-blue-50',
        'label' => 'Branches / Offices',
        'suffix' => ' offices',
    ],
    [
        'field' => 'target_market',
        'icon'  => 'fas fa-users',
        'color' => 'text-indigo-600',
        'bg'    => 'bg-indigo-50',
        'label' => 'Target Market',
        'suffix' => '',
    ],
];

$countries = is_array($profile->countries_of_operation ?? null)
    ? $profile->countries_of_operation
    : [];
@endphp

{{-- ── Operations Detail Card ───────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-4">

    {{-- Header --}}
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2">
        <div class="w-7 h-7 bg-teal-50 rounded-lg flex items-center justify-center">
            <i class="fas fa-cogs text-teal-600 text-xs"></i>
        </div>
        <h2 class="text-sm font-black text-gray-800">Operations Overview</h2>
    </div>

    {{-- Fields --}}
    <div class="divide-y divide-gray-50">
        @foreach($details as $item)
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
                    <p class="text-xs font-semibold text-gray-800">
                        {{ $val }}{{ $item['suffix'] }}
                    </p>
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

{{-- ── Countries of Operation Card ──────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-5">

    {{-- Header --}}
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between gap-2">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 bg-amber-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-map-marker-alt text-amber-500 text-xs"></i>
            </div>
            <h2 class="text-sm font-black text-gray-800">Countries of Operation</h2>
        </div>
        @if(count($countries))
            <span class="px-2 py-0.5 bg-teal-50 text-teal-700 text-[10px] font-black rounded-md border border-teal-100">
                {{ count($countries) }} {{ Str::plural('country', count($countries)) }}
            </span>
        @endif
    </div>

    {{-- Countries list --}}
    @if(count($countries))
        <div class="p-4 flex flex-wrap gap-2">
            @foreach($countries as $country)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-teal-50 border border-teal-100 rounded-lg">
                    <i class="fas fa-map-marker-alt text-teal-500 text-[10px]"></i>
                    <span class="text-xs font-semibold text-teal-700">{{ $country }}</span>
                </span>
            @endforeach
        </div>
    @else
        <div class="py-10 text-center">
            <i class="fas fa-globe text-gray-200 text-2xl mb-2"></i>
            <p class="text-xs text-gray-400 italic">No countries listed</p>
        </div>
    @endif

</div>

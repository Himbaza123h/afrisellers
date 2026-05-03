{{-- ═══ TAB: Business Type & Category ══════════════════════════════════════ --}}

@php
$classifications = [
    [
        'field' => 'industry',
        'icon'  => 'fas fa-industry',
        'color' => 'text-amber-500',
        'bg'    => 'bg-amber-50',
        'label' => 'Industry',
    ],
    [
        'field' => 'business_type',
        'icon'  => 'fas fa-briefcase',
        'color' => 'text-indigo-600',
        'bg'    => 'bg-indigo-50',
        'label' => 'Business Type',
    ],
];

$services = is_array($profile->services ?? null) ? $profile->services : [];
@endphp

{{-- ── Classification Card ──────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-4">

    {{-- Header --}}
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2">
        <div class="w-7 h-7 bg-orange-50 rounded-lg flex items-center justify-center">
            <i class="fas fa-briefcase text-orange-500 text-xs"></i>
        </div>
        <h2 class="text-sm font-black text-gray-800">Business Classification</h2>
    </div>

    {{-- Fields --}}
    <div class="divide-y divide-gray-50">
        @foreach($classifications as $item)
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
                    <p class="text-xs font-semibold text-gray-800">{{ $val }}</p>
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

{{-- ── Services Card ─────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-5">

    {{-- Header --}}
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between gap-2">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 bg-red-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-concierge-bell text-[#ff0808] text-xs"></i>
            </div>
            <h2 class="text-sm font-black text-gray-800">Services Offered</h2>
        </div>
        @if(count($services))
            <span class="px-2 py-0.5 bg-red-50 text-[#ff0808] text-[10px] font-black rounded-md border border-red-100">
                {{ count($services) }} {{ Str::plural('service', count($services)) }}
            </span>
        @endif
    </div>

    {{-- Services list --}}
    @if(count($services))
        <div class="p-4 flex flex-wrap gap-2">
            @foreach($services as $service)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 border border-red-100 rounded-lg">
                    <i class="fas fa-check-circle text-[#ff0808] text-[10px]"></i>
                    <span class="text-xs font-semibold text-[#ff0808]">{{ $service }}</span>
                </span>
            @endforeach
        </div>
    @else
        <div class="py-10 text-center">
            <i class="fas fa-concierge-bell text-gray-200 text-2xl mb-2"></i>
            <p class="text-xs text-gray-400 italic">No services added</p>
        </div>
    @endif

</div>

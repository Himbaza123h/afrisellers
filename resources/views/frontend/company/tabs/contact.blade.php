@php
$items = [
    [
        'field' => 'contact_name',
        'icon'  => 'fas fa-user',
        'color' => 'text-blue-600',
        'bg'    => 'bg-blue-50',
        'label' => 'Contact Person',
        'type'  => 'text',
    ],
    [
        'field' => 'contact_position',
        'icon'  => 'fas fa-briefcase',
        'color' => 'text-purple-600',
        'bg'    => 'bg-purple-50',
        'label' => 'Position / Role',
        'type'  => 'text',
    ],
    [
        'field' => 'email',
        'icon'  => 'fas fa-envelope',
        'color' => 'text-[#ff0808]',
        'bg'    => 'bg-red-50',
        'label' => 'Email Address',
        'type'  => 'email',
    ],
    [
        'field' => 'phone',
        'icon'  => 'fas fa-phone',
        'color' => 'text-green-600',
        'bg'    => 'bg-green-50',
        'label' => 'Phone Number',
        'type'  => 'phone',
    ],
    [
        'field' => 'whatsapp',
        'icon'  => 'fab fa-whatsapp',
        'color' => 'text-emerald-600',
        'bg'    => 'bg-emerald-50',
        'label' => 'WhatsApp',
        'type'  => 'phone',
    ],
];
@endphp

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-5">

    {{-- Header --}}
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2">
        <div class="w-7 h-7 bg-green-50 rounded-lg flex items-center justify-center">
            <i class="fas fa-address-book text-green-600 text-xs"></i>
        </div>
        <h2 class="text-sm font-black text-gray-800">Contact Details</h2>
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
                    @if($item['type'] === 'email')
                        <a href="mailto:{{ $val }}"
                           class="text-xs font-semibold text-[#ff0808] hover:underline truncate block">
                            {{ $val }}
                        </a>

                    @elseif($item['type'] === 'phone' && $item['field'] === 'whatsapp')
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $val) }}"
                           target="_blank" rel="noopener noreferrer"
                           class="text-xs font-semibold text-emerald-600 hover:underline truncate block">
                            {{ $val }}
                        </a>

                    @elseif($item['type'] === 'phone')
                        <a href="tel:{{ $val }}"
                           class="text-xs font-semibold text-gray-800 hover:underline truncate block">
                            {{ $val }}
                        </a>

                    @else
                        <p class="text-xs font-semibold text-gray-800 truncate">
                            {{ $val }}
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

@extends('layouts.home')

@section('page-content')
<div class="space-y-4 max-w-2xl">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Configure Section</h1>
            <p class="mt-1 text-xs text-gray-500">{{ $uiSection->name }}</p>
        </div>
        <a href="{{ route('admin.ui-sections.index') }}"
           class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium shadow-sm">
            <i class="fas fa-arrow-left"></i><span>Back</span>
        </a>
    </div>

    {{-- Form --}}
    <form action="{{ route('admin.ui-sections.update', $uiSection) }}" method="POST" id="section-form">
        @csrf @method('PUT')

        {{-- Basic Settings --}}
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden mb-4">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700">Basic Settings</h3>
            </div>
            <div class="p-4 space-y-4">

                {{-- Section Name (read only) --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Section</label>
                    <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 font-medium">
                        {{ $uiSection->name }}
                    </div>
                </div>

                {{-- Visibility --}}
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Visible on Homepage</p>
                        <p class="text-xs text-gray-400">Toggle to show or hide this section</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                               {{ $uiSection->is_active ? 'checked' : '' }}>
                        <div class="w-10 h-5 bg-gray-200 peer-checked:bg-[#ff0808] rounded-full transition-colors
                                    after:content-[''] after:absolute after:top-0.5 after:left-0.5
                                    after:bg-white after:rounded-full after:h-4 after:w-4
                                    after:transition-all peer-checked:after:translate-x-5"></div>
                    </label>
                </div>

                {{-- Number of Items --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Number of Items
                        <span class="text-gray-400 font-normal">(max 8)</span>
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="range" name="number_items" id="number_items"
                               min="1" max="8"
                               value="{{ $uiSection->number_items }}"
                               class="flex-1 accent-[#ff0808]"
                               oninput="document.getElementById('items_display').textContent = this.value">
                        <span class="w-8 text-center text-sm font-bold text-[#ff0808]"
                              id="items_display">{{ $uiSection->number_items }}</span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-400 mt-1 px-1">
                        @for($i = 1; $i <= 8; $i++)
                            <span>{{ $i }}</span>
                        @endfor
                    </div>
                    @error('number_items')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Animation Settings --}}
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden mb-4">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700">Animation Style</h3>
                <p class="text-xs text-gray-400 mt-0.5">Only one animation type can be active at a time</p>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">

                    @php $current = $uiSection->getAnimationMode(); @endphp

                    @foreach([
                        'none'  => ['icon' => 'fa-ban',        'label' => 'None',  'desc' => 'Static display'],
                        'slide' => ['icon' => 'fa-arrows-alt-v','label' => 'Slide', 'desc' => 'Slides up/down'],
                        'fade'  => ['icon' => 'fa-adjust',      'label' => 'Fade',  'desc' => 'Fades in/out'],
                        'flip'  => ['icon' => 'fa-sync-alt',    'label' => 'Flip',  'desc' => 'Flips over'],
                    ] as $value => $opt)
                    <label class="animation-option relative flex flex-col items-center gap-2 p-3 rounded-lg border-2 cursor-pointer transition-all
                                  {{ $current === $value ? 'border-[#ff0808] bg-red-50' : 'border-gray-200 hover:border-gray-300' }}">
                        <input type="radio" name="animation" value="{{ $value }}"
                               class="sr-only"
                               {{ $current === $value ? 'checked' : '' }}
                               onchange="updateAnimationUI()">
                        <i class="fas {{ $opt['icon'] }} text-xl {{ $current === $value ? 'text-[#ff0808]' : 'text-gray-400' }}"></i>
                        <span class="text-xs font-semibold {{ $current === $value ? 'text-[#ff0808]' : 'text-gray-700' }}">{{ $opt['label'] }}</span>
                        <span class="text-[10px] text-gray-400 text-center">{{ $opt['desc'] }}</span>
                        @if($current === $value)
                        <span class="absolute top-1.5 right-1.5 w-3 h-3 bg-[#ff0808] rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-white" style="font-size:7px"></i>
                        </span>
                        @endif
                    </label>
                    @endforeach
                </div>
                @error('animation')
                    <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Manual Items (only for sections that allow it) --}}
        @if($uiSection->allow_manual)
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden mb-4">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-700">Manual Items</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Items pinned to this section (up to <span id="max_display">{{ $uiSection->number_items }}</span>)</p>
                </div>
                <span id="items_count_badge"
                      class="text-xs px-2 py-0.5 rounded-full font-medium
                             {{ count($uiSection->manual_items ?? []) >= $uiSection->number_items ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                    {{ count($uiSection->manual_items ?? []) }}/{{ $uiSection->number_items }}
                </span>
            </div>
            <div class="p-4 space-y-3">

                {{-- Current manual items --}}
                <div id="manual-items-list" class="space-y-2">
                    @forelse($uiSection->manual_items ?? [] as $index => $item)
                    <div class="flex items-center justify-between px-3 py-2 bg-gray-50 rounded-lg border border-gray-200"
                         id="manual-item-{{ $index }}">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700 capitalize">
                                {{ $item['type'] ?? 'item' }}
                            </span>
                            <span class="text-xs text-gray-700 font-medium">ID #{{ $item['id'] }}</span>
                        </div>
                        <button type="button"
                                onclick="removeManualItem({{ $item['id'] }}, '{{ $item['type'] }}')"
                                class="p-1 text-red-500 hover:bg-red-50 rounded transition-colors">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400 text-center py-4" id="no-items-msg">No manual items added yet.</p>
                    @endforelse
                </div>

                {{-- Add new item --}}
                <div class="flex gap-2 pt-2 border-t border-gray-100">
                    <select id="add_item_type"
                            class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                        @switch($uiSection->section_key)
                            @case('weekly_special_offers')
                            @case('hot_deals')
                                <option value="product">Product</option>
                                <option value="offer">Offer</option>
                                @break
                            @case('most_popular_suppliers')
                                <option value="supplier">Supplier</option>
                                @break
                            @case('trending_products')
                                <option value="product">Product</option>
                                @break
                            @default
                                <option value="product">Product</option>
                        @endswitch
                    </select>
                    <input type="number" id="add_item_id" min="1"
                           placeholder="Item ID"
                           class="flex-1 text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                    <button type="button" onclick="addManualItem()"
                            class="px-4 py-2 bg-[#ff0808] text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors flex items-center gap-1">
                        <i class="fas fa-plus text-xs"></i> Add
                    </button>
                </div>
                <div id="manual-error" class="hidden text-xs text-red-600 mt-1"></div>
            </div>
        </div>
        @endif

        {{-- Save --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.ui-sections.index') }}"
               class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700">
                Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2 bg-[#ff0808] text-white text-sm font-medium rounded-lg hover:bg-red-700 shadow-sm">
                <i class="fas fa-save mr-1"></i> Save Changes
            </button>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
// ── Animation card visual toggle ──────────────────────────────
function updateAnimationUI() {
    document.querySelectorAll('.animation-option').forEach(label => {
        const radio   = label.querySelector('input[type=radio]');
        const icon    = label.querySelector('i.fas');
        const title   = label.querySelector('span.text-xs.font-semibold');
        const dot     = label.querySelector('span.absolute');

        if (radio.checked) {
            label.classList.add('border-[#ff0808]', 'bg-red-50');
            label.classList.remove('border-gray-200');
            icon.classList.add('text-[#ff0808]');
            icon.classList.remove('text-gray-400');
            title.classList.add('text-[#ff0808]');
            title.classList.remove('text-gray-700');
            if (!dot) {
                const newDot = document.createElement('span');
                newDot.className = 'absolute top-1.5 right-1.5 w-3 h-3 bg-[#ff0808] rounded-full flex items-center justify-center';
                newDot.innerHTML = '<i class="fas fa-check text-white" style="font-size:7px"></i>';
                label.appendChild(newDot);
            }
        } else {
            label.classList.remove('border-[#ff0808]', 'bg-red-50');
            label.classList.add('border-gray-200');
            icon.classList.remove('text-[#ff0808]');
            icon.classList.add('text-gray-400');
            title.classList.remove('text-[#ff0808]');
            title.classList.add('text-gray-700');
            dot?.remove();
        }
    });
}

// Sync slider max with max_display label
document.getElementById('number_items')?.addEventListener('input', function() {
    const maxEl = document.getElementById('max_display');
    if (maxEl) maxEl.textContent = this.value;
});

// ── Manual Items via AJAX ─────────────────────────────────────
const sectionId = {{ $uiSection->id }};
const addUrl    = '{{ route('admin.ui-sections.manual-items.add', $uiSection) }}';
const removeUrl = '{{ route('admin.ui-sections.manual-items.remove', $uiSection) }}';
const csrfToken = '{{ csrf_token() }}';

function showError(msg) {
    const el = document.getElementById('manual-error');
    el.textContent = msg;
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 4000);
}

function updateCountBadge(items) {
    const max    = parseInt(document.getElementById('number_items').value);
    const badge  = document.getElementById('items_count_badge');
    if (!badge) return;
    badge.textContent = `${items.length}/${max}`;
    badge.className   = `text-xs px-2 py-0.5 rounded-full font-medium ${items.length >= max ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'}`;
}

function renderItems(items) {
    const list = document.getElementById('manual-items-list');
    list.innerHTML = '';

    if (!items.length) {
        list.innerHTML = '<p class="text-xs text-gray-400 text-center py-4" id="no-items-msg">No manual items added yet.</p>';
        return;
    }

    items.forEach((item, index) => {
        list.innerHTML += `
        <div class="flex items-center justify-between px-3 py-2 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700 capitalize">${item.type}</span>
                <span class="text-xs text-gray-700 font-medium">ID #${item.id}</span>
            </div>
            <button type="button" onclick="removeManualItem(${item.id}, '${item.type}')"
                    class="p-1 text-red-500 hover:bg-red-50 rounded transition-colors">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>`;
    });
    updateCountBadge(items);
}

async function addManualItem() {
    const itemId   = document.getElementById('add_item_id').value.trim();
    const itemType = document.getElementById('add_item_type').value;

    if (!itemId) { showError('Please enter an item ID.'); return; }

    try {
        const res  = await fetch(addUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ item_id: parseInt(itemId), item_type: itemType })
        });
        const data = await res.json();
        if (!res.ok) { showError(data.error ?? 'Failed to add item.'); return; }
        renderItems(data.items);
        document.getElementById('add_item_id').value = '';
    } catch (e) {
        showError('Something went wrong.');
    }
}

async function removeManualItem(itemId, itemType) {
    try {
        const res  = await fetch(removeUrl, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ item_id: itemId, item_type: itemType })
        });
        const data = await res.json();
        if (!res.ok) { showError(data.error ?? 'Failed to remove item.'); return; }
        renderItems(data.items);
    } catch (e) {
        showError('Something went wrong.');
    }
}
</script>
@endpush

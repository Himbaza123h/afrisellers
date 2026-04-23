@extends('layouts.home')

@push('styles')
<style>
    .section-card { cursor: grab; transition: box-shadow 0.2s, opacity 0.2s; }
    .section-card:active { cursor: grabbing; }
    .section-card.dragging { opacity: 0.4; box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
    .drag-over { border-color: #ff0808 !important; background: #fff5f5; }
</style>
@endpush

@section('page-content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">User Interface Sections</h1>
            <p class="mt-1 text-xs text-gray-500">Control homepage sections — visibility, animations, item counts and manual content</p>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Legend --}}
    <div class="bg-white rounded-lg border border-gray-200 p-3 flex flex-wrap gap-4 text-xs text-gray-500">
        <span class="flex items-center gap-1"><i class="fas fa-grip-vertical text-gray-300"></i> Drag to reorder</span>
        <span class="flex items-center gap-1"><i class="fas fa-eye text-green-500"></i> Visible on homepage</span>
        <span class="flex items-center gap-1"><i class="fas fa-eye-slash text-gray-400"></i> Hidden</span>
        <span class="flex items-center gap-1"><i class="fas fa-hand-point-up text-blue-500"></i> Supports manual items</span>
    </div>

    {{-- Sections List --}}
    <div id="sortable-sections" class="space-y-3">
        @foreach($sections as $section)
        <div class="section-card bg-white rounded-lg border border-gray-200 p-4"
             data-id="{{ $section->id }}">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">

                {{-- Drag Handle + Name --}}
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <div class="text-gray-300 hover:text-gray-500 cursor-grab flex-shrink-0">
                        <i class="fas fa-grip-vertical"></i>
                    </div>
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0
                        {{ $section->is_active ? 'bg-green-100' : 'bg-gray-100' }}">
                        @switch($section->section_key)
                            @case('hero_section')          <i class="fas fa-image {{ $section->is_active ? 'text-green-600' : 'text-gray-400' }} text-sm"></i> @break
                            @case('browse_by_regions')     <i class="fas fa-globe {{ $section->is_active ? 'text-green-600' : 'text-gray-400' }} text-sm"></i> @break
                            @case('weekly_special_offers') <i class="fas fa-tags {{ $section->is_active ? 'text-green-600' : 'text-gray-400' }} text-sm"></i> @break
                            @case('hot_deals')             <i class="fas fa-fire {{ $section->is_active ? 'text-green-600' : 'text-gray-400' }} text-sm"></i> @break
                            @case('most_popular_suppliers')<i class="fas fa-store {{ $section->is_active ? 'text-green-600' : 'text-gray-400' }} text-sm"></i> @break
                            @case('trending_products')     <i class="fas fa-chart-line {{ $section->is_active ? 'text-green-600' : 'text-gray-400' }} text-sm"></i> @break
                            @default                       <i class="fas fa-th-large {{ $section->is_active ? 'text-green-600' : 'text-gray-400' }} text-sm"></i>
                        @endswitch
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $section->name }}</p>
                        <div class="flex flex-wrap items-center gap-2 mt-0.5">
                            {{-- Animation badge --}}
                            @if($section->getAnimationMode() !== 'none')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-purple-100 text-purple-700">
                                    <i class="fas fa-play text-[9px]"></i>
                                    {{ ucfirst($section->getAnimationMode()) }}
                                </span>
                            @endif
                            {{-- Items count --}}
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-700">
                                <i class="fas fa-th-large text-[9px]"></i>
                                {{ $section->number_items }} items
                            </span>
                            {{-- Manual badge --}}
                            @if($section->allow_manual)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-700">
                                    <i class="fas fa-hand-point-up text-[9px]"></i>
                                    Manual ({{ count($section->manual_items ?? []) }})
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 flex-shrink-0">
                    {{-- Toggle active --}}
                    <form action="{{ route('admin.ui-sections.toggle-active', $section) }}" method="POST">
                        @csrf
                        <button type="submit"
                                title="{{ $section->is_active ? 'Hide section' : 'Show section' }}"
                                class="p-2 rounded-lg text-sm transition-colors
                                    {{ $section->is_active
                                        ? 'bg-green-50 text-green-600 hover:bg-green-100'
                                        : 'bg-gray-100 text-gray-400 hover:bg-gray-200' }}">
                            <i class="fas {{ $section->is_active ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                        </button>
                    </form>

                    {{-- Edit --}}
                    <a href="{{ route('admin.ui-sections.edit', $section) }}"
                       class="p-2 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-sm transition-colors"
                       title="Configure">
                        <i class="fas fa-cog"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection

@push('scripts')
<script>
// ── Simple drag-and-drop reorder ──────────────────────────────
const container = document.getElementById('sortable-sections');
let dragged = null;

container.querySelectorAll('.section-card').forEach(card => {
    card.setAttribute('draggable', true);

    card.addEventListener('dragstart', () => {
        dragged = card;
        setTimeout(() => card.classList.add('dragging'), 0);
    });
    card.addEventListener('dragend', () => {
        card.classList.remove('dragging');
        saveOrder();
    });
    card.addEventListener('dragover', e => {
        e.preventDefault();
        const target = e.currentTarget;
        if (target !== dragged) {
            const rect = target.getBoundingClientRect();
            const mid  = rect.top + rect.height / 2;
            if (e.clientY < mid) {
                container.insertBefore(dragged, target);
            } else {
                container.insertBefore(dragged, target.nextSibling);
            }
        }
    });
});

function saveOrder() {
    const order = [...container.querySelectorAll('.section-card')].map(c => c.dataset.id);
    fetch('{{ route('admin.ui-sections.reorder') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ order })
    });
}
</script>
@endpush

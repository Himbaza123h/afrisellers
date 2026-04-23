@extends('layouts.home')

@push('styles')
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    @media print {
        .no-print { display: none !important; }
    }

    /* Section assignment modal */
    #sectionModal {
        transition: opacity 0.2s ease;
    }
    .section-checkbox-label {
        transition: background 0.15s, border-color 0.15s;
    }
    .section-checkbox-label:has(input:checked) {
        background: #fff5f5;
        border-color: #ff0808;
    }
    .section-checkbox-label.is-full {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>
@endpush

@section('page-content')
<div class="space-y-4">
    <!-- Page Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Products Management</h1>
            <p class="mt-1 text-xs text-gray-500">
                @if(auth()->user()->hasRole('admin'))
                    View and verify all products
                @else
                    Manage your products and inventory
                @endif
            </p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <button onclick="printReport()" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            @if(auth()->user()->isVendor() && !auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.product.create') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 transition-all font-medium shadow-sm text-sm">
                    <i class="fas fa-plus"></i>
                    <span>Add Product</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex gap-2 border-b border-gray-200 no-print">
        <button onclick="switchTab('all')" id="tab-all" class="tab-button px-4 py-2 text-sm font-semibold text-[#ff0808] border-b-2 border-[#ff0808] transition-colors">
            All
        </button>
        <button onclick="switchTab('stats')" id="tab-stats" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            Stats
        </button>
        <button onclick="switchTab('table')" id="tab-table" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            Table
        </button>
    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3 no-print">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-3 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3 no-print">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <p class="text-sm font-medium text-red-900 flex-1">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-3 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3 no-print">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <div class="flex-1">
                @foreach($errors->all() as $error)
                    <p class="text-sm font-medium text-red-900">{{ $error }}</p>
                @endforeach
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <!-- Stats Section (shared across tabs) -->
    <div id="stats-section" class="stats-container">
        @include('admin.product.partials.stats')
    </div>

    <!-- Table Section (shared across tabs) -->
    <div id="table-section" class="table-container">
        @include('admin.product.partials.table')
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- SECTION ASSIGNMENT MODAL                                      --}}
{{-- Trigger: openSectionModal(productId, productName)             --}}
{{-- In your table partial, add this button per row:               --}}
{{-- <button onclick="openSectionModal({{ $product->id }}, '{{ addslashes($product->name) }}')"> --}}
{{--     <i class="fas fa-layer-group"></i> Sections               --}}
{{-- </button>                                                      --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="sectionModal"
     class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-black/50 hidden opacity-0"
     onclick="if(event.target===this) closeSectionModal()">

    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col overflow-hidden">

        <!-- Header -->
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 bg-gray-50 rounded-t-xl">
            <div>
                <h3 class="text-base font-bold text-gray-900">Assign to Homepage Sections</h3>
                <p id="modalProductName" class="text-xs text-gray-500 mt-0.5 truncate max-w-xs"></p>
            </div>
            <button onclick="closeSectionModal()" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-200 transition-colors text-gray-500">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto px-5 py-4">

            <!-- Loading -->
            <div id="modalLoading" class="flex flex-col items-center justify-center py-10 gap-3">
                <div class="w-8 h-8 border-4 border-[#ff0808] border-t-transparent rounded-full animate-spin"></div>
                <span class="text-xs text-gray-400">Loading sections...</span>
            </div>

            <!-- Sections list -->
            <div id="modalSections" class="hidden space-y-2"></div>

            <!-- Empty -->
            <div id="modalEmpty" class="hidden text-center py-8 text-xs text-gray-400">
                No sections available.
            </div>

        </div>

        <!-- Footer -->
        <div class="px-5 py-4 border-t border-gray-200 bg-gray-50 rounded-b-xl flex items-center justify-between gap-3">
            <p class="text-[10px] text-gray-400">
                <i class="fas fa-info-circle mr-1"></i>
                Checked sections will show this product manually.
            </p>
            <div class="flex gap-2">
                <button onclick="closeSectionModal()" class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-100 text-gray-700 font-medium transition-colors">
                    Cancel
                </button>
                <button onclick="saveSectionAssignments()" id="modalSaveBtn"
                        class="px-5 py-2 bg-[#ff0808] text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-save"></i>
                    Save
                </button>
            </div>
        </div>

    </div>
</div>

<!-- Toast notification -->
<div id="sectionToast"
     class="fixed bottom-4 right-4 z-[100000] hidden">
    <div class="flex items-center gap-3 px-4 py-3 rounded-lg shadow-xl text-sm font-medium text-white bg-green-600" id="sectionToastInner">
        <i class="fas fa-check-circle"></i>
        <span id="sectionToastMsg">Saved!</span>
    </div>
</div>

<script>
// ══════════════════════════════════════════════════════════════
// SECTION ASSIGNMENT MODAL
// ══════════════════════════════════════════════════════════════
(function () {

    const SECTIONS_URL = (productId) =>
        `/admin/section-assignments/product/${productId}/sections`;
    const SYNC_URL = (productId) =>
        `/admin/section-assignments/product/${productId}/sync`;

    let _currentProductId   = null;
    let _originalSectionIds = [];

    // ── Open ─────────────────────────────────────────────────────
    window.openSectionModal = function (productId, productName) {
        _currentProductId   = productId;
        _originalSectionIds = [];

        // Show modal
        const modal = document.getElementById('sectionModal');
        modal.classList.remove('hidden');
        requestAnimationFrame(() => modal.classList.remove('opacity-0'));

        document.getElementById('modalProductName').textContent = productName ?? '';
        document.getElementById('modalLoading').classList.remove('hidden');
        document.getElementById('modalSections').classList.add('hidden');
        document.getElementById('modalEmpty').classList.add('hidden');
        document.getElementById('modalSaveBtn').disabled = true;

        // Fetch sections
        fetch(SECTIONS_URL(productId), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('modalLoading').classList.add('hidden');

            const container = document.getElementById('modalSections');
            const sections  = data.sections ?? [];

            if (!sections.length) {
                document.getElementById('modalEmpty').classList.remove('hidden');
                return;
            }

            container.innerHTML = '';
            sections.forEach(sec => {
                if (sec.assigned) _originalSectionIds.push(sec.id);

                const label = document.createElement('label');
                label.className = `section-checkbox-label flex items-center gap-3 p-3 rounded-lg border-2 cursor-pointer select-none
                    ${sec.assigned ? 'border-[#ff0808] bg-[#fff5f5]' : 'border-gray-200 hover:border-gray-300'}
                    ${sec.full && !sec.assigned ? 'is-full pointer-events-none' : ''}`;

                const statusBadge = sec.assigned
                    ? `<span class="ml-auto text-[9px] font-bold text-[#ff0808] bg-red-50 px-1.5 py-0.5 rounded border border-[#ff0808]">ON</span>`
                    : sec.full
                        ? `<span class="ml-auto text-[9px] font-bold text-orange-600 bg-orange-50 px-1.5 py-0.5 rounded border border-orange-200">FULL</span>`
                        : `<span class="ml-auto text-[9px] font-bold text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded">${sec.count}/${sec.max}</span>`;

                const activeIcon = sec.is_active
                    ? `<span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>`
                    : `<span class="w-1.5 h-1.5 rounded-full bg-gray-300 inline-block"></span>`;

                label.innerHTML = `
                    <input type="checkbox"
                           class="w-4 h-4 accent-[#ff0808] section-check"
                           data-section-id="${sec.id}"
                           ${sec.assigned ? 'checked' : ''}
                           ${sec.full && !sec.assigned ? 'disabled' : ''}>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1.5">
                            ${activeIcon}
                            <span class="text-xs font-semibold text-gray-800 truncate">${sec.name}</span>
                        </div>
                        <span class="text-[9px] text-gray-400 font-mono">${sec.section_key}</span>
                    </div>
                    ${statusBadge}
                `;

                container.appendChild(label);
            });

            container.classList.remove('hidden');
            document.getElementById('modalSaveBtn').disabled = false;
        })
        .catch(() => {
            document.getElementById('modalLoading').classList.add('hidden');
            document.getElementById('modalEmpty').classList.remove('hidden');
            document.getElementById('modalEmpty').textContent = 'Failed to load sections.';
        });
    };

    // ── Close ────────────────────────────────────────────────────
    window.closeSectionModal = function () {
        const modal = document.getElementById('sectionModal');
        modal.classList.add('opacity-0');
        setTimeout(() => modal.classList.add('hidden'), 200);
        _currentProductId = null;
    };

    // ── Save ─────────────────────────────────────────────────────
    window.saveSectionAssignments = function () {
        if (!_currentProductId) return;

        const checks     = document.querySelectorAll('.section-check:checked');
        const sectionIds = Array.from(checks).map(c => parseInt(c.getAttribute('data-section-id')));
        const saveBtn    = document.getElementById('modalSaveBtn');

        saveBtn.disabled   = true;
        saveBtn.innerHTML  = '<i class="fas fa-spinner fa-spin"></i> Saving...';

        fetch(SYNC_URL(_currentProductId), {
            method:  'POST',
            headers: {
                'Content-Type':     'application/json',
                'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ section_ids: sectionIds }),
        })
        .then(r => r.json())
        .then(data => {
            closeSectionModal();
            showToast(data.message ?? 'Saved!', 'green');
        })
        .catch(() => {
            showToast('Failed to save assignments.', 'red');
        })
        .finally(() => {
            saveBtn.disabled  = false;
            saveBtn.innerHTML = '<i class="fas fa-save"></i> Save';
        });
    };

    // ── Toast ────────────────────────────────────────────────────
    function showToast(msg, color) {
        const toast    = document.getElementById('sectionToast');
        const inner    = document.getElementById('sectionToastInner');
        const msgEl    = document.getElementById('sectionToastMsg');
        const colorMap = { green: 'bg-green-600', red: 'bg-red-600', orange: 'bg-orange-500' };

        inner.className = `flex items-center gap-3 px-4 py-3 rounded-lg shadow-xl text-sm font-medium text-white ${colorMap[color] ?? 'bg-gray-800'}`;
        msgEl.textContent = msg;

        toast.classList.remove('hidden');
        setTimeout(() => toast.classList.add('hidden'), 3500);
    }

    // ── Keyboard close ───────────────────────────────────────────
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeSectionModal();
    });

})();

// ── Tab switcher ──────────────────────────────────────────────
function switchTab(tab) {
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('text-[#ff0808]', 'border-b-2', 'border-[#ff0808]');
        btn.classList.add('text-gray-600');
    });

    const activeTab = document.getElementById(`tab-${tab}`);
    activeTab.classList.remove('text-gray-600');
    activeTab.classList.add('text-[#ff0808]', 'border-b-2', 'border-[#ff0808]');

    const statsSection = document.getElementById('stats-section');
    const tableSection = document.getElementById('table-section');

    switch(tab) {
        case 'all':
            statsSection.style.display = 'block';
            tableSection.style.display = 'block';
            break;
        case 'stats':
            statsSection.style.display = 'block';
            tableSection.style.display = 'none';
            break;
        case 'table':
            statsSection.style.display = 'none';
            tableSection.style.display = 'block';
            break;
    }
}

function printReport() {
    window.open('{{ route("admin.products.print") }}', '_blank');
}

// Auto-hide alerts
setTimeout(function() {
    document.querySelectorAll('.bg-green-50, .bg-red-50').forEach(function(alert) {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity    = '0';
        setTimeout(function() { alert.remove(); }, 500);
    });
}, 5000);
</script>
@endsection

@extends('layouts.home')

@push('styles')
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    @media print {
        .no-print { display: none !important; }
    }
</style>
@endpush

@section('page-content')
<div class="space-y-4">
    <!-- Page Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Commission Management</h1>
            <p class="mt-1 text-xs text-gray-500">Monitor and manage all commission earnings</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <button onclick="window.open('{{ route('admin.commissions.print') }}', '_blank')" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            <form action="{{ route('admin.commissions.export') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
                    <i class="fas fa-download"></i>
                    <span>Export CSV</span>
                </button>
            </form>
            <a href="{{ route('admin.commissions.settings') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
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

    <!-- Stats Section -->
    <div id="stats-section" class="stats-container">
        @include('admin.commissions.partials.stats')
    </div>

    <!-- Table Section (includes filters) -->
    <div id="table-section" class="table-container">
        @include('admin.commissions.partials.table')
    </div>
</div>

<script>
function switchTab(tab) {
    // Remove active state from all tabs
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('text-[#ff0808]', 'border-b-2', 'border-[#ff0808]');
        btn.classList.add('text-gray-600');
    });

    // Add active state to selected tab
    const activeTab = document.getElementById(`tab-${tab}`);
    activeTab.classList.remove('text-gray-600');
    activeTab.classList.add('text-[#ff0808]', 'border-b-2', 'border-[#ff0808]');

    // Show/hide sections based on tab
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

// Toggle dropdown
function toggleDropdown(event, id) {
    event.stopPropagation();
    const dropdown = document.getElementById(id);
    const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');

    allDropdowns.forEach(d => {
        if (d.id !== id) {
            d.classList.add('hidden');
        }
    });

    dropdown.classList.toggle('hidden');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
    allDropdowns.forEach(dropdown => {
        dropdown.classList.add('hidden');
    });
});

// Bulk Actions
function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.commission-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
    updateBulkActions();
}

function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.commission-checkbox:checked');
    const count = checkboxes.length;
    const bulkBar = document.getElementById('bulkActionsBar');
    const selectedCount = document.getElementById('selectedCount');

    if (count > 0) {
        bulkBar.classList.remove('hidden');
        selectedCount.textContent = count;
    } else {
        bulkBar.classList.add('hidden');
    }

    // Update select all checkbox
    const allCheckboxes = document.querySelectorAll('.commission-checkbox');
    const selectAllCheckbox = document.getElementById('selectAll');
    selectAllCheckbox.checked = count === allCheckboxes.length && count > 0;
}

function clearSelection() {
    const checkboxes = document.querySelectorAll('.commission-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = false;
    });
    document.getElementById('selectAll').checked = false;
    updateBulkActions();
}

function bulkApprove() {
    const checkboxes = document.querySelectorAll('.commission-checkbox:checked');
    const ids = Array.from(checkboxes).map(cb => cb.value);

    if (ids.length === 0) {
        alert('Please select commissions to approve');
        return;
    }

    if (!confirm(`Are you sure you want to approve ${ids.length} commission(s)?`)) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.commissions.bulk-approve") }}';

    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);

    ids.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'commission_ids[]';
        input.value = id;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
}

function openBulkPayModal() {
    const checkboxes = document.querySelectorAll('.commission-checkbox:checked');
    const ids = Array.from(checkboxes).map(cb => cb.value);

    if (ids.length === 0) {
        alert('Please select commissions to pay');
        return;
    }

    document.getElementById('bulkPayCommissionIds').value = JSON.stringify(ids);
    document.getElementById('bulkPayCount').textContent = ids.length;
    document.getElementById('bulkPayModal').classList.remove('hidden');
}

function closeBulkPayModal() {
    document.getElementById('bulkPayModal').classList.add('hidden');
}

// Auto-hide success/error messages after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50');
    alerts.forEach(function(alert) {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(function() {
            alert.remove();
        }, 500);
    });
}, 5000);
</script>
@endsection

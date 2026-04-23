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
            <h1 class="text-xl font-bold text-gray-900">Product Categories Management</h1>
            <p class="mt-1 text-xs text-gray-500">Manage product categories and their settings</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <button onclick="printReport()" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            <a href="{{ route('admin.product-category.create') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-plus"></i>
                <span>Add Category</span>
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

    <!-- Stats Section (shared across tabs) -->
    <div id="stats-section" class="stats-container">
        @include('admin.product-category.partials.stats')
    </div>

    <!-- Table Section (shared across tabs) -->
    <div id="table-section" class="table-container">
        @include('admin.product-category.partials.table')
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

function printReport() {
    window.open('{{ route("admin.product-categories.print") }}', '_blank');
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

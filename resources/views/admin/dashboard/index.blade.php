@extends('layouts.home')

@section('page-content')
<!-- Welcome Section with Tabs -->
<div class="mb-4 sm:mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <div>
            <p class="text-xs text-gray-500 mb-1">Welcome back,</p>
            <h1 class="text-xl font-bold text-gray-900 uppercase">{{ auth()->user()->name }}</h1>
        </div>
        <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm w-fit">
            <span class="text-xs font-semibold text-gray-600">System Status</span>
            <span class="flex items-center gap-1.5 text-green-600 font-bold text-xs">
                <span class="w-1.5 h-1.5 bg-green-600 rounded-full animate-pulse"></span>
                Operational
            </span>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex gap-2 border-b border-gray-200">
        <button onclick="switchTab('all')" id="tab-all" class="tab-button px-4 py-2 text-sm font-semibold text-[#ff0808] border-b-2 border-[#ff0808] transition-colors">
            All
        </button>
        <button onclick="switchTab('stats')" id="tab-stats" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            Stats
        </button>
        <button onclick="switchTab('graph')" id="tab-graph" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            Graph
        </button>
        <button onclick="switchTab('table')" id="tab-table" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            Table
        </button>
    </div>
</div>

<!-- Tab Content -->
<div id="content-all">
    @include('admin.dashboard.partials.all')
</div>

<div id="content-stats" class="hidden">
    @include('admin.dashboard.partials.stats')
</div>

<div id="content-graph" class="hidden">
    @include('admin.dashboard.partials.graph')
</div>

<div id="content-table" class="hidden">
    @include('admin.dashboard.partials.table')
</div>
@endsection

@push('scripts')
<script>
function switchTab(tab) {
    // Hide all content
    document.querySelectorAll('[id^="content-"]').forEach(el => el.classList.add('hidden'));

    // Remove active state from all tabs
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('text-[#ff0808]', 'border-b-2', 'border-[#ff0808]');
        btn.classList.add('text-gray-600');
    });

    // Show selected content
    document.getElementById(`content-${tab}`).classList.remove('hidden');

    // Add active state to selected tab
    const activeTab = document.getElementById(`tab-${tab}`);
    activeTab.classList.remove('text-gray-600');
    activeTab.classList.add('text-[#ff0808]', 'border-b-2', 'border-[#ff0808]');
}
</script>
@endpush

@push('scripts')
<script>
function switchTab(tab) {
    // Hide all content
    document.querySelectorAll('[id^="content-"]').forEach(el => el.classList.add('hidden'));

    // Remove active state from all tabs
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('text-[#ff0808]', 'border-b-2', 'border-[#ff0808]');
        btn.classList.add('text-gray-600');
    });

    // Show selected content
    document.getElementById(`content-${tab}`).classList.remove('hidden');

    // Add active state to selected tab
    const activeTab = document.getElementById(`tab-${tab}`);
    activeTab.classList.remove('text-gray-600');
    activeTab.classList.add('text-[#ff0808]', 'border-b-2', 'border-[#ff0808]');

    // Initialize graph chart when Graph tab is clicked
    if (tab === 'graph' && !window.graphChartInitialized) {
        initGraphChart();
        window.graphChartInitialized = true;
    }
}

function initGraphChart() {
    const ctxGraph = document.getElementById('regionalChartGraph');
    if (!ctxGraph) return;

    const regionalData = @json($regionalData);
    const labels = Object.keys(regionalData);
    const revenues = Object.values(regionalData).map(item => item.revenue);
    const vendors = Object.values(regionalData).map(item => item.vendors);

    new Chart(ctxGraph, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue ($k)',
                data: revenues,
                backgroundColor: '#ff0808',
                borderRadius: 4,
            }, {
                label: 'Vendors',
                data: vendors,
                backgroundColor: '#3b82f6',
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        padding: 10,
                        font: { size: 10 },
                        boxWidth: 30,
                        boxHeight: 10
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' },
                    ticks: { font: { size: 9 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 9 } }
                }
            }
        }
    });
}
</script>
@endpush

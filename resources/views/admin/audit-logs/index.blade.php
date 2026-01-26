@extends('layouts.home')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .badge-action {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
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
            <h1 class="text-xl font-bold text-gray-900">Audit Logs</h1>
            <p class="mt-1 text-xs text-gray-500">Track all system activities and user actions</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <button onclick="window.open('{{ route('admin.audit-logs.print') }}' + window.location.search, '_blank')" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            <button onclick="document.getElementById('exportForm').submit()" class="inline-flex items-center gap-2 px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-download"></i>
                <span>Export CSV</span>
            </button>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex gap-2 border-b border-gray-200 no-print">
        <button onclick="switchTab('all')" id="tab-all" class="tab-button px-4 py-2 text-sm font-semibold text-blue-600 border-b-2 border-blue-600 transition-colors">
            All
        </button>
        <button onclick="switchTab('stats')" id="tab-stats" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            Stats
        </button>
        <button onclick="switchTab('logs')" id="tab-logs" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            Logs
        </button>
    </div>

    <!-- Stats Section -->
    <div id="stats-section" class="stats-container hidden">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Logs</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_logs']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-list mr-1 text-[8px]"></i> All time
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                        <i class="fas fa-list text-xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Today's Logs</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['today_logs']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-calendar-day mr-1 text-[8px]"></i> Today
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                        <i class="fas fa-calendar-day text-xl text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">This Week</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['this_week_logs']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                <i class="fas fa-calendar-week mr-1 text-[8px]"></i> Weekly
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                        <i class="fas fa-calendar-week text-xl text-purple-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Action Types</p>
                        <p class="text-lg font-bold text-gray-900">{{ count($stats['actions_by_type']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                <i class="fas fa-chart-bar mr-1 text-[8px]"></i> Unique
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg">
                        <i class="fas fa-chart-bar text-xl text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Distribution -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mt-4">
            <h3 class="text-base font-semibold text-gray-900 mb-3">Action Distribution</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($stats['actions_by_type'] as $action => $count)
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs font-medium text-gray-600 mb-1">{{ ucfirst($action) }}</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($count) }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $stats['total_logs'] > 0 ? number_format(($count / $stats['total_logs']) * 100, 1) : 0 }}%
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Logs Section -->
    <div id="logs-section" class="logs-container">
        <!-- Filters -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 no-print">
            <form method="GET" class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                    <!-- Search -->
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search logs..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    <!-- Action Type -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Action</label>
                        <select name="action" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Actions</option>
                            @foreach($actionTypes as $actionType)
                                <option value="{{ $actionType }}" {{ request('action') == $actionType ? 'selected' : '' }}>
                                    {{ ucfirst($actionType) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Model Type -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Model</label>
                        <select name="model" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Models</option>
                            @foreach($modelTypes as $modelType)
                                <option value="{{ $modelType }}" {{ request('model') == $modelType ? 'selected' : '' }}>
                                    {{ $modelType }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Date Range</label>
                        <input type="text" id="dateRange" placeholder="Select dates" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg cursor-pointer text-sm">
                        <input type="hidden" name="date_from" id="dateFrom" value="{{ request('date_from') }}">
                        <input type="hidden" name="date_to" id="dateTo" value="{{ request('date_to') }}">
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        <i class="fas fa-filter text-sm"></i> Apply
                    </button>
                    <a href="{{ route('admin.audit-logs.index') }}" class="inline-flex items-center gap-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                        <i class="fas fa-undo text-sm"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Audit Logs Table -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden mt-4">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-900">Activity Logs</h2>
                    <span class="px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">
                        {{ $logs->total() }} {{ Str::plural('record', $logs->total()) }}
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">User</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Action</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Model</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Description</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Date</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="flex items-center justify-center w-6 h-6 bg-gray-200 rounded-full text-gray-600 font-semibold text-xs">
                                            {{ $log->user ? strtoupper(substr($log->user->name, 0, 1)) : 'S' }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $log->user ? $log->user->name : 'System' }}</p>
                                            @if($log->user?->email)
                                                <p class="text-xs text-gray-500 truncate max-w-[150px]">{{ $log->user->email }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $actionColor = match($log->action) {
                                            'created' => 'green',
                                            'updated' => 'blue',
                                            'deleted' => 'red',
                                            default => 'yellow'
                                        };
                                        $actionIcon = match($log->action) {
                                            'created' => 'fa-plus',
                                            'updated' => 'fa-edit',
                                            'deleted' => 'fa-trash',
                                            default => 'fa-eye'
                                        };
                                    @endphp
                                    <span class="badge-action bg-{{ $actionColor }}-100 text-{{ $actionColor }}-700">
                                        <i class="fas {{ $actionIcon }} text-xs"></i>
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($log->model_type)
                                        <span class="px-2 py-1 text-xs font-medium text-gray-700 bg-gray-100 rounded">
                                            {{ class_basename($log->model_type) }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">N/A</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm text-gray-900 truncate max-w-[250px]">{{ Str::limit($log->description, 40) }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm text-gray-900">{{ $log->created_at->format('M d') }}</p>
                                    <p class="text-xs text-gray-500">{{ $log->created_at->format('h:i A') }}</p>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button onclick="viewLog({{ $log->id }})" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                        <i class="fas fa-eye text-sm"></i> View
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-2">
                                            <i class="fas fa-clipboard-list text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium">No audit logs found</p>
                                        <p class="text-xs text-gray-400 mt-1">Try adjusting your filters</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($logs->hasPages())
                <div class="px-4 py-3 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-700">Showing {{ $logs->firstItem() }}-{{ $logs->lastItem() }} of {{ $logs->total() }}</span>
                        <div class="text-sm">{{ $logs->links() }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- View Log Modal -->
<div id="logModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 no-print">
    <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full max-h-[80vh] overflow-hidden">
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-900">Audit Log Details</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="modalContent" class="p-4 overflow-y-auto max-h-[calc(80vh-70px)]">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<!-- Hidden Export Form -->
<form id="exportForm" action="{{ route('admin.audit-logs.export') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="search" value="{{ request('search') }}">
    <input type="hidden" name="action" value="{{ request('action') }}">
    <input type="hidden" name="model" value="{{ request('model') }}">
    <input type="hidden" name="date_from" value="{{ request('date_from') }}">
    <input type="hidden" name="date_to" value="{{ request('date_to') }}">
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
// Date Range Picker
flatpickr("#dateRange", {
    mode: "range",
    dateFormat: "Y-m-d",
    showMonths: 2,
    onChange: function(selectedDates) {
        if (selectedDates.length === 2) {
            document.getElementById('dateFrom').value = flatpickr.formatDate(selectedDates[0], 'Y-m-d');
            document.getElementById('dateTo').value = flatpickr.formatDate(selectedDates[1], 'Y-m-d');
        }
    },
    defaultDate: [document.getElementById('dateFrom').value, document.getElementById('dateTo').value].filter(d => d)
});

// Tab Switching
function switchTab(tab) {
    // Remove active state from all tabs
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
        btn.classList.add('text-gray-600');
    });

    // Add active state to selected tab
    const activeTab = document.getElementById(`tab-${tab}`);
    activeTab.classList.remove('text-gray-600');
    activeTab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');

    // Show/hide sections
    const statsSection = document.getElementById('stats-section');
    const logsSection = document.getElementById('logs-section');

    switch(tab) {
        case 'all':
            statsSection.style.display = 'none';
            logsSection.style.display = 'block';
            break;
        case 'stats':
            statsSection.style.display = 'block';
            logsSection.style.display = 'none';
            break;
        case 'logs':
            statsSection.style.display = 'none';
            logsSection.style.display = 'block';
            break;
    }
}

// Initialize with All tab active
document.addEventListener('DOMContentLoaded', function() {
    switchTab('all');
});

// View Log Details
async function viewLog(logId) {
    const modal = document.getElementById('logModal');
    const content = document.getElementById('modalContent');

    content.innerHTML = '<div class="flex justify-center py-8"><i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i></div>';
    modal.classList.remove('hidden');

    try {
        const response = await fetch(`/admin/audit-logs/${logId}`);
        const html = await response.text();

        // Parse the response and extract the log details
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const logDetails = doc.querySelector('.log-details');

        if (logDetails) {
            content.innerHTML = logDetails.innerHTML;
        }
    } catch (error) {
        content.innerHTML = '<div class="text-center py-4 text-red-600 text-sm">Error loading log details</div>';
    }
}

function closeModal() {
    document.getElementById('logModal').classList.add('hidden');
}

// Close modal on outside click
document.getElementById('logModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
</script>
@endpush

@endsection

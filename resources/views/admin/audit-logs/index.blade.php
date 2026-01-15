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
</style>
@endpush

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Audit Logs</h1>
            <p class="mt-1 text-sm text-gray-500">Track all system activities and user actions</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="document.getElementById('exportForm').submit()" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                <i class="fas fa-download"></i>Export CSV
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-list text-blue-600 text-xl"></i>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">Total Logs</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_logs']) }}</p>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-day text-green-600 text-xl"></i>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">Today's Logs</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['today_logs']) }}</p>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-week text-purple-600 text-xl"></i>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">This Week</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['this_week_logs']) }}</p>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-bar text-orange-600 text-xl"></i>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">Action Types</p>
            <p class="text-2xl font-bold text-gray-900">{{ count($stats['actions_by_type']) }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <form method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by description, user, IP..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Action Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Action</label>
                    <select name="action" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Model</label>
                    <select name="model" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                    <input type="text" id="dateRange" placeholder="Select dates" readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg cursor-pointer">
                    <input type="hidden" name="date_from" id="dateFrom" value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to" id="dateTo" value="{{ request('date_to') }}">
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    <i class="fas fa-filter"></i>Apply Filters
                </button>
                <a href="{{ route('admin.audit-logs.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                    <i class="fas fa-undo"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Audit Logs Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Activity Logs</h2>
                <span class="px-3 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">
                    {{ $logs->total() }} {{ Str::plural('record', $logs->total()) }}
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Model</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">IP Address</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-8 h-8 bg-gray-200 rounded-full text-gray-600 font-semibold text-sm">
                                        {{ $log->user ? strtoupper(substr($log->user->name, 0, 1)) : 'S' }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $log->user ? $log->user->name : 'System' }}</p>
                                        <p class="text-xs text-gray-500">{{ $log->user?->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="badge-action bg-{{ $log->action_color }}-100 text-{{ $log->action_color }}-700">
                                    <i class="fas {{ $log->action_icon }}"></i>
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($log->model_type)
                                    <span class="px-2 py-1 text-xs font-medium text-gray-700 bg-gray-100 rounded">
                                        {{ class_basename($log->model_type) }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-900">{{ Str::limit($log->description, 50) }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-600 font-mono">{{ $log->ip_address ?? 'N/A' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-900">{{ $log->created_at->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $log->created_at->format('h:i A') }}</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button onclick="viewLog({{ $log->id }})" class="text-blue-600 hover:text-blue-700 font-medium text-sm">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-clipboard-list text-gray-300 text-5xl mb-3"></i>
                                    <p class="text-gray-500 font-medium">No audit logs found</p>
                                    <p class="text-gray-400 text-sm mt-1">Try adjusting your filters</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>

<!-- View Log Modal -->
<div id="logModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Audit Log Details</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="modalContent" class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
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
        } else {
            // Fallback: fetch data and build UI
            const data = @json($logs);
            const log = data.data.find(l => l.id === logId);

            content.innerHTML = `
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-600">User</p>
                            <p class="text-base text-gray-900">${log.user?.name || 'System'}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Action</p>
                            <p class="text-base text-gray-900">${log.action}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Model</p>
                            <p class="text-base text-gray-900">${log.model_type ? log.model_type.split('\\\\').pop() : 'N/A'}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">IP Address</p>
                            <p class="text-base text-gray-900 font-mono">${log.ip_address || 'N/A'}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-2">Description</p>
                        <p class="text-base text-gray-900 bg-gray-50 p-3 rounded-lg">${log.description}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-2">User Agent</p>
                        <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg font-mono">${log.user_agent || 'N/A'}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Created At</p>
                            <p class="text-base text-gray-900">${new Date(log.created_at).toLocaleString()}</p>
                        </div>
                    </div>
                </div>
            `;
        }
    } catch (error) {
        content.innerHTML = '<div class="text-center py-8 text-red-600">Error loading log details</div>';
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
</script>
@endpush

@endsection

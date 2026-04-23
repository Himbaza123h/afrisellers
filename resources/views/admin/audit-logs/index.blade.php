@extends('layouts.home')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .badge-action {
        display: inline-flex; align-items: center; gap: 0.25rem;
        padding: 0.25rem 0.75rem; border-radius: 9999px;
        font-size: 0.75rem; font-weight: 600;
    }
    .tab-button { border-bottom: 2px solid transparent; }
    .tab-button.active { color: #2563eb; border-bottom-color: #2563eb; }
    .vendor-panel { transition: all 0.2s ease; }
    @media print { .no-print { display: none !important; } }
</style>
@endpush

@section('page-content')
<div class="space-y-4">

    {{-- ── Page Header ── --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Audit Logs</h1>
            <p class="mt-1 text-xs text-gray-500">Track all system activities, user actions and company engagement</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <a href="{{ route('admin.audit-logs.print') }}{{ request()->getQueryString() ? '?'.request()->getQueryString() : '' }}"
               target="_blank"
               class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium shadow-sm text-sm">
                <i class="fas fa-print"></i> Print Logs
            </a>
            <a href="{{ route('admin.audit-logs.print-vendor') }}"
               target="_blank"
               class="inline-flex items-center gap-2 px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium shadow-sm text-sm">
                <i class="fas fa-building"></i> Print All Vendors
            </a>
            <button onclick="document.getElementById('exportForm').submit()"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium shadow-sm text-sm">
                <i class="fas fa-download"></i> Export CSV
            </button>
        </div>
    </div>

    {{-- ── Tab Navigation ── --}}
    <div class="flex gap-1 border-b border-gray-200 no-print overflow-x-auto scrollbar-hide">
        @foreach([
            ['key'=>'all',     'label'=>'All',        'icon'=>'fa-th-large'],
            ['key'=>'stats',   'label'=>'Stats',      'icon'=>'fa-chart-bar'],
            ['key'=>'logs',    'label'=>'Logs',       'icon'=>'fa-list'],
            ['key'=>'vendors', 'label'=>'By Vendor',  'icon'=>'fa-building'],
            ['key'=>'visitors','label'=>'Visitors',   'icon'=>'fa-eye'],
        ] as $tab)
        <button onclick="switchTab('{{ $tab['key'] }}')"
                id="tab-{{ $tab['key'] }}"
                class="tab-button flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-900 whitespace-nowrap transition-colors">
            <i class="fas {{ $tab['icon'] }} text-xs"></i>
            {{ $tab['label'] }}
        </button>
        @endforeach
    </div>

    {{-- ════════════════════════════════════════
         STATS SECTION
    ════════════════════════════════════════ --}}
    <div id="stats-section" class="hidden space-y-4">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                ['label'=>'Total Logs',    'value'=>$stats['total_logs'],     'color'=>'blue',   'icon'=>'fa-list',         'sub'=>'All time'],
                ['label'=>"Today's Logs",  'value'=>$stats['today_logs'],     'color'=>'green',  'icon'=>'fa-calendar-day', 'sub'=>'Today'],
                ['label'=>'This Week',     'value'=>$stats['this_week_logs'], 'color'=>'purple', 'icon'=>'fa-calendar-week','sub'=>'Weekly'],
                ['label'=>'Active Users',  'value'=>$stats['active_users'],   'color'=>'orange', 'icon'=>'fa-users',        'sub'=>'Today'],
            ] as $s)
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">{{ $s['label'] }}</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($s['value']) }}</p>
                        <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $s['color'] }}-100 text-{{ $s['color'] }}-800">
                            {{ $s['sub'] }}
                        </span>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-{{ $s['color'] }}-50 rounded-lg">
                        <i class="fas {{ $s['icon'] }} text-xl text-{{ $s['color'] }}-600"></i>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Action Distribution --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
            <h3 class="text-base font-semibold text-gray-900 mb-3">Action Distribution</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach($stats['actions_by_type'] as $action => $count)
                @php
                    $pct = $stats['total_logs'] > 0 ? round(($count / $stats['total_logs']) * 100, 1) : 0;
                    $aColor = match($action) {
                        'created'  => 'green',
                        'updated'  => 'blue',
                        'deleted'  => 'red',
                        'visited'  => 'indigo',
                        'viewed'   => 'purple',
                        'liked'    => 'pink',
                        'shared'   => 'cyan',
                        'chat'     => 'teal',
                        'comment'  => 'amber',
                        'clicked'  => 'orange',
                        'login'    => 'emerald',
                        'logout'   => 'gray',
                        default    => 'gray'
                    };
                @endphp
                <div class="p-3 bg-{{ $aColor }}-50 border border-{{ $aColor }}-100 rounded-lg">
                    <p class="text-xs font-semibold text-{{ $aColor }}-700 mb-1 capitalize">{{ $action }}</p>
                    <p class="text-xl font-bold text-gray-900">{{ number_format($count) }}</p>
                    <div class="mt-1.5 h-1 bg-{{ $aColor }}-100 rounded-full overflow-hidden">
                        <div class="h-1 bg-{{ $aColor }}-400 rounded-full" style="width:{{ $pct }}%"></div>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1">{{ $pct }}%</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Geo + Browser --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach([
                ['title'=>'Top Countries','data'=>$stats['top_countries'],'color'=>'blue'],
                ['title'=>'Top Cities',   'data'=>$stats['top_cities'],   'color'=>'purple'],
                ['title'=>'Browsers',     'data'=>$stats['top_browsers'], 'color'=>'green'],
            ] as $card)
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">{{ $card['title'] }}</h3>
                @forelse($card['data'] as $label => $count)
                <div class="flex justify-between items-center py-1.5 border-b border-gray-50 last:border-0">
                    <span class="text-sm text-gray-700">{{ $label ?: 'Unknown' }}</span>
                    <span class="text-xs font-semibold text-{{ $card['color'] }}-600 bg-{{ $card['color'] }}-50 px-2 py-0.5 rounded-full">
                        {{ number_format($count) }}
                    </span>
                </div>
                @empty
                <p class="text-xs text-gray-400">No data yet</p>
                @endforelse
            </div>
            @endforeach
        </div>
    </div>

    {{-- ════════════════════════════════════════
         LOGS SECTION
    ════════════════════════════════════════ --}}
    <div id="logs-section" class="hidden space-y-4">
        {{-- Filters --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 no-print">
            <form method="GET" class="space-y-3">
                <input type="hidden" name="tab" value="logs">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search logs..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Action</label>
                        <select name="action" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">All Actions</option>
                            @foreach($actionTypes as $at)
                            <option value="{{ $at }}" {{ request('action')==$at?'selected':'' }}>{{ ucfirst($at) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Model</label>
                        <select name="model" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">All Models</option>
                            @foreach($modelTypes as $mt)
                            <option value="{{ $mt }}" {{ request('model')==$mt?'selected':'' }}>{{ $mt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Date Range</label>
                        <input type="text" id="dateRange" placeholder="Select dates" readonly
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg cursor-pointer text-sm">
                        <input type="hidden" name="date_from" id="dateFrom" value="{{ request('date_from') }}">
                        <input type="hidden" name="date_to"   id="dateTo"   value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        <i class="fas fa-filter text-sm"></i> Apply
                    </button>
                    <a href="{{ route('admin.audit-logs.index') }}?tab=logs"
                       class="inline-flex items-center gap-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                        <i class="fas fa-undo text-sm"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-900">Activity Logs</h2>
                <span class="px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">
                    {{ $logs->total() }} {{ Str::plural('record', $logs->total()) }}
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">User</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Action</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Model</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Description</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Location</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Date</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($logs as $log)
                        @php
                            $aColor = match($log->action) {
                                'created'=>'green','updated'=>'blue','deleted'=>'red',
                                'visited'=>'indigo','viewed'=>'purple','liked'=>'pink',
                                'shared'=>'cyan','chat'=>'teal','comment'=>'amber',
                                'clicked'=>'orange','login'=>'emerald','logout'=>'gray',
                                default=>'yellow'
                            };
                            $aIcon = match($log->action) {
                                'created'=>'fa-plus','updated'=>'fa-edit','deleted'=>'fa-trash',
                                'visited'=>'fa-eye','viewed'=>'fa-eye','liked'=>'fa-heart',
                                'shared'=>'fa-share-alt','chat'=>'fa-comment-dots','comment'=>'fa-comment',
                                'clicked'=>'fa-mouse-pointer','login'=>'fa-sign-in-alt','logout'=>'fa-sign-out-alt',
                                default=>'fa-circle'
                            };
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center justify-center w-7 h-7 bg-indigo-100 rounded-full text-indigo-700 font-bold text-xs flex-shrink-0">
                                        {{ $log->user ? strtoupper(substr($log->user->name,0,1)) : 'S' }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 leading-tight">{{ $log->user?->name ?? 'System' }}</p>
                                        @if($log->user?->email)
                                        <p class="text-xs text-gray-400 truncate max-w-[130px]">{{ $log->user->email }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge-action bg-{{ $aColor }}-100 text-{{ $aColor }}-700">
                                    <i class="fas {{ $aIcon }} text-xs"></i>
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($log->model_type)
                                <span class="px-2 py-1 text-xs font-medium text-gray-700 bg-gray-100 rounded">
                                    {{ class_basename($log->model_type) }}
                                </span>
                                @else
                                <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm text-gray-900 truncate max-w-[220px]">{{ Str::limit($log->description, 40) }}</p>
                            </td>
                            <td class="px-4 py-3">
                                @if($log->country)
                                <p class="text-xs text-gray-700">{{ $log->city ? $log->city.', ' : '' }}{{ $log->country }}</p>
                                @endif
                                <p class="text-xs text-gray-400 font-mono">{{ $log->ip_address ?? '-' }}</p>
                                @if($log->browser)
                                <p class="text-[10px] text-gray-300">{{ $log->browser }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm text-gray-900">{{ $log->created_at->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-400">{{ $log->created_at->format('h:i A') }}</p>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button onclick="viewLog({{ $log->id }})"
                                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center">
                                <i class="fas fa-clipboard-list text-4xl text-gray-200 mb-2 block"></i>
                                <p class="text-gray-400 text-sm">No audit logs found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-500">
                    Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }}
                </span>
                <div class="text-sm">{{ $logs->appends(request()->query())->links() }}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- ════════════════════════════════════════
         BY-VENDOR SECTION
    ════════════════════════════════════════ --}}
    <div id="vendors-section" class="hidden space-y-4">

        {{-- Vendor search --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 no-print">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <input type="hidden" name="tab" value="vendors">
                <div class="flex-1 min-w-48">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Search vendor</label>
                    <input type="text" name="vendor_search" value="{{ request('vendor_search') }}"
                           placeholder="Name or email..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-400">
                </div>
                <button type="submit"
                        class="px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 font-medium">
                    <i class="fas fa-search mr-1"></i> Search
                </button>
                <a href="{{ route('admin.audit-logs.index') }}?tab=vendors"
                   class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 font-medium">
                    Reset
                </a>
                <a href="{{ route('admin.audit-logs.print-vendor') }}" target="_blank"
                   class="ml-auto px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 font-medium flex items-center gap-2">
                    <i class="fas fa-print"></i> Print All Vendors
                </a>
            </form>
        </div>

        {{-- Vendor cards --}}
        <div class="space-y-3">
        @forelse($vendorActivities as $row)
        @php
            $user = $usersMap[$row->user_id] ?? null;
            if (!$user) continue;
            $initial = strtoupper(substr($user->name ?? 'U', 0, 1));
            $engagement = $row->liked_count + $row->shared_count + $row->chat_count + $row->comment_count + $row->clicked_count;
        @endphp

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

            {{-- Vendor header --}}
            <div class="flex items-center gap-4 px-5 py-4 cursor-pointer hover:bg-gray-50 transition-colors"
                 onclick="toggleVendorCard({{ $row->user_id }})">

                {{-- Avatar --}}
                <div class="w-11 h-11 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0 text-base font-bold text-purple-700">
                    {{ $initial }}
                </div>

                {{-- Name + email --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-900 leading-tight">{{ $user->name }}</p>
                    <p class="text-xs text-gray-400">{{ $user->email }}</p>
                </div>

                {{-- Action pill summary --}}
                <div class="hidden sm:flex flex-wrap items-center gap-2">
                    @if($row->created_count > 0)
                    <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded-full">
                        <i class="fas fa-plus mr-0.5 text-[8px]"></i>{{ $row->created_count }} created
                    </span>
                    @endif
                    @if($row->viewed_count + $row->visited_count > 0)
                    <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-[10px] font-bold rounded-full">
                        <i class="fas fa-eye mr-0.5 text-[8px]"></i>{{ $row->viewed_count + $row->visited_count }} views
                    </span>
                    @endif
                    @if($engagement > 0)
                    <span class="px-2 py-0.5 bg-pink-100 text-pink-700 text-[10px] font-bold rounded-full">
                        <i class="fas fa-heart mr-0.5 text-[8px]"></i>{{ $engagement }} engagements
                    </span>
                    @endif
                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-[10px] font-bold rounded-full">
                        {{ number_format($row->total_actions) }} total
                    </span>
                    @if($row->last_activity)
                    <span class="text-[10px] text-gray-400">
                        Last: {{ \Carbon\Carbon::parse($row->last_activity)->diffForHumans() }}
                    </span>
                    @endif
                </div>

                <div class="flex items-center gap-2 ml-2 flex-shrink-0">
                    <a href="{{ route('admin.audit-logs.print-vendor', $row->user_id) }}"
                       target="_blank"
                       onclick="event.stopPropagation()"
                       class="hidden sm:inline-flex items-center gap-1 px-2.5 py-1 bg-purple-50 text-purple-600 border border-purple-200 rounded-lg text-[10px] font-semibold hover:bg-purple-100 transition-colors">
                        <i class="fas fa-print text-[9px]"></i> Print
                    </a>
                    <i id="vchev-{{ $row->user_id }}"
                       class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-200"></i>
                </div>
            </div>

            {{-- Expandable body --}}
            <div id="vcard-{{ $row->user_id }}" class="hidden border-t border-gray-100">

                {{-- Metrics grid --}}
                <div class="grid grid-cols-3 sm:grid-cols-5 lg:grid-cols-10 border-b border-gray-100">
                    @foreach([
                        ['label'=>'Created',  'val'=>$row->created_count,  'color'=>'green',  'icon'=>'fa-plus'],
                        ['label'=>'Updated',  'val'=>$row->updated_count,  'color'=>'blue',   'icon'=>'fa-edit'],
                        ['label'=>'Deleted',  'val'=>$row->deleted_count,  'color'=>'red',    'icon'=>'fa-trash'],
                        ['label'=>'Views',    'val'=>$row->viewed_count,   'color'=>'purple', 'icon'=>'fa-eye'],
                        ['label'=>'Visits',   'val'=>$row->visited_count,  'color'=>'indigo', 'icon'=>'fa-globe'],
                        ['label'=>'Likes',    'val'=>$row->liked_count,    'color'=>'pink',   'icon'=>'fa-heart'],
                        ['label'=>'Shares',   'val'=>$row->shared_count,   'color'=>'cyan',   'icon'=>'fa-share-alt'],
                        ['label'=>'Chats',    'val'=>$row->chat_count,     'color'=>'teal',   'icon'=>'fa-comment-dots'],
                        ['label'=>'Comments', 'val'=>$row->comment_count,  'color'=>'amber',  'icon'=>'fa-comment'],
                        ['label'=>'Clicks',   'val'=>$row->clicked_count,  'color'=>'orange', 'icon'=>'fa-mouse-pointer'],
                    ] as $m)
                    <div class="p-3 text-center border-r border-gray-100 last:border-0">
                        <div class="text-lg font-bold text-gray-900">{{ number_format($m['val']) }}</div>
                        <div class="text-[10px] text-{{ $m['color'] }}-600 font-semibold flex items-center justify-center gap-1 mt-0.5">
                            <i class="fas {{ $m['icon'] }} text-[8px]"></i>{{ $m['label'] }}
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Recent activity log for this user --}}
                @php
                    $userLogs = \App\Models\AuditLog::where('user_id', $user->id)
                        ->orderByDesc('created_at')
                        ->limit(8)
                        ->get();
                @endphp
                <div class="p-4">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Recent Activity</p>
                        <a href="{{ route('admin.audit-logs.index') }}?user_id={{ $user->id }}&tab=logs"
                           class="text-xs text-blue-600 hover:underline font-medium">
                            View all →
                        </a>
                    </div>

                    @if($userLogs->isEmpty())
                    <p class="text-xs text-gray-400 py-2">No activity recorded.</p>
                    @else
                    <div class="space-y-1.5">
                        @foreach($userLogs as $ul)
                        @php
                            $ulColor = match($ul->action) {
                                'created'=>'green','updated'=>'blue','deleted'=>'red',
                                'visited'=>'indigo','viewed'=>'purple','liked'=>'pink',
                                'shared'=>'cyan','chat'=>'teal','comment'=>'amber',
                                'clicked'=>'orange','login'=>'emerald','logout'=>'gray',
                                default=>'gray'
                            };
                            $ulIcon = match($ul->action) {
                                'created'=>'fa-plus','updated'=>'fa-edit','deleted'=>'fa-trash',
                                'visited'=>'fa-globe','viewed'=>'fa-eye','liked'=>'fa-heart',
                                'shared'=>'fa-share-alt','chat'=>'fa-comment-dots','comment'=>'fa-comment',
                                'clicked'=>'fa-mouse-pointer','login'=>'fa-sign-in-alt','logout'=>'fa-sign-out-alt',
                                default=>'fa-circle'
                            };
                        @endphp
                        <div class="flex items-start gap-2.5 py-1.5 border-b border-gray-50 last:border-0">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-{{ $ulColor }}-100 flex-shrink-0 mt-0.5">
                                <i class="fas {{ $ulIcon }} text-{{ $ulColor }}-600 text-[8px]"></i>
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-gray-800 font-medium truncate">
                                    <span class="text-{{ $ulColor }}-700 capitalize">{{ $ul->action }}</span>
                                    @if($ul->model_type)
                                    <span class="text-gray-400 font-normal"> · {{ class_basename($ul->model_type) }}</span>
                                    @endif
                                    <span class="text-gray-500 font-normal"> — {{ Str::limit($ul->description, 55) }}</span>
                                </p>
                            </div>
                            <span class="text-[10px] text-gray-400 flex-shrink-0 whitespace-nowrap">
                                {{ $ul->created_at->diffForHumans(null, true) }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl border border-gray-200 px-6 py-16 text-center">
            <i class="fas fa-building text-4xl text-gray-200 mb-3 block"></i>
            <p class="text-sm font-semibold text-gray-400">No vendor activity found</p>
        </div>
        @endforelse
        </div>

        {{-- Pagination --}}
        @if($vendorActivities->hasPages())
        <div class="flex justify-end">
            {{ $vendorActivities->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    {{-- ════════════════════════════════════════
         VISITORS SECTION
    ════════════════════════════════════════ --}}
    <div id="visitors-section" class="hidden space-y-4">

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 no-print">
            <form method="GET" class="space-y-3">
                <input type="hidden" name="tab" value="visitors">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="vsearch" value="{{ request('vsearch') }}"
                               placeholder="IP, country, URL..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Country</label>
                        <select name="vcountry" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">All Countries</option>
                            @foreach($visitorCountries as $c)
                            <option value="{{ $c }}" {{ request('vcountry')==$c?'selected':'' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        <i class="fas fa-filter"></i> Apply
                    </button>
                    <a href="{{ route('admin.audit-logs.index') }}?tab=visitors"
                       class="inline-flex items-center gap-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                        <i class="fas fa-undo"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-900">Page Visitors</h2>
                <span class="px-2 py-1 text-xs font-semibold text-indigo-700 bg-indigo-100 rounded-full">
                    {{ $visitors->total() }} {{ Str::plural('visit', $visitors->total()) }}
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">User</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">IP</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Location</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Browser / OS</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Page</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Referer</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($visitors as $visit)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs">
                                        {{ $visit->user ? strtoupper(substr($visit->user->name,0,1)) : '?' }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $visit->user?->name ?? 'Guest' }}</p>
                                        @if($visit->user?->email)
                                        <p class="text-xs text-gray-400 truncate max-w-[110px]">{{ $visit->user->email }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $visit->ip_address ?? '-' }}</td>
                            <td class="px-4 py-3 text-xs text-gray-700">
                                {{ $visit->city ? $visit->city.', ' : '' }}{{ $visit->country ?? 'Unknown' }}
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-xs text-gray-700">{{ $visit->browser ?? '-' }}</p>
                                <p class="text-[10px] text-gray-400">{{ $visit->platform ?? '' }}</p>
                            </td>
                            <td class="px-4 py-3 max-w-[180px]">
                                <p class="text-xs text-gray-700 truncate" title="{{ $visit->url }}">
                                    {{ $visit->url ? parse_url($visit->url, PHP_URL_PATH) : '-' }}
                                </p>
                            </td>
                            <td class="px-4 py-3 max-w-[140px]">
                                <p class="text-xs text-gray-400 truncate">
                                    {{ $visit->referer ? parse_url($visit->referer, PHP_URL_HOST) : '-' }}
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm text-gray-900">{{ $visit->created_at->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-400">{{ $visit->created_at->format('h:i A') }}</p>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center">
                                <i class="fas fa-users text-4xl text-gray-200 mb-2 block"></i>
                                <p class="text-gray-400 text-sm">No visitor logs found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($visitors->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-500">
                    Showing {{ $visitors->firstItem() }}–{{ $visitors->lastItem() }} of {{ $visitors->total() }}
                </span>
                <div class="text-sm">{{ $visitors->appends(request()->query())->links() }}</div>
            </div>
            @endif
        </div>
    </div>

</div>

{{-- ── Log Detail Modal ── --}}
<div id="logModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 no-print">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[80vh] overflow-hidden">
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-900">Audit Log Details</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="modalContent" class="p-4 overflow-y-auto max-h-[calc(80vh-70px)]"></div>
    </div>
</div>

{{-- ── Hidden Export Form ── --}}
<form id="exportForm" action="{{ route('admin.audit-logs.export') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="search"    value="{{ request('search') }}">
    <input type="hidden" name="action"    value="{{ request('action') }}">
    <input type="hidden" name="model"     value="{{ request('model') }}">
    <input type="hidden" name="date_from" value="{{ request('date_from') }}">
    <input type="hidden" name="date_to"   value="{{ request('date_to') }}">
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
// ── Date picker ──
flatpickr("#dateRange", {
    mode: "range", dateFormat: "Y-m-d", showMonths: 2,
    onChange: function(dates) {
        if (dates.length === 2) {
            document.getElementById('dateFrom').value = flatpickr.formatDate(dates[0], 'Y-m-d');
            document.getElementById('dateTo').value   = flatpickr.formatDate(dates[1], 'Y-m-d');
        }
    },
    defaultDate: [
        document.getElementById('dateFrom')?.value,
        document.getElementById('dateTo')?.value
    ].filter(Boolean)
});

// ── Tabs ──
const SECTIONS = ['stats','logs','vendors','visitors'];

function switchTab(tab) {
    document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
    const activeBtn = document.getElementById('tab-' + tab);
    if (activeBtn) activeBtn.classList.add('active');

    SECTIONS.forEach(s => document.getElementById(s + '-section').classList.add('hidden'));

    if (tab === 'all') {
        SECTIONS.forEach(s => document.getElementById(s + '-section').classList.remove('hidden'));
    } else {
        document.getElementById(tab + '-section')?.classList.remove('hidden');
    }

    const url = new URL(window.location);
    url.searchParams.set('tab', tab);
    history.replaceState(null, '', url);
}

document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);
    switchTab(params.get('tab') || 'all');
});

// ── Vendor card toggle ──
function toggleVendorCard(userId) {
    const panel = document.getElementById('vcard-' + userId);
    const chev  = document.getElementById('vchev-' + userId);
    const open  = !panel.classList.contains('hidden');
    panel.classList.toggle('hidden', open);
    chev.style.transform = open ? 'rotate(0deg)' : 'rotate(180deg)';
}

// ── Log detail modal ──
async function viewLog(logId) {
    const modal   = document.getElementById('logModal');
    const content = document.getElementById('modalContent');
    content.innerHTML = '<div class="flex justify-center py-8"><i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i></div>';
    modal.classList.remove('hidden');
    try {
        const res  = await fetch(`/admin/audit-logs/${logId}`);
        const html = await res.text();
        const doc  = new DOMParser().parseFromString(html, 'text/html');
        const det  = doc.querySelector('.log-details');
        content.innerHTML = det
            ? det.innerHTML
            : '<div class="text-center py-4 text-gray-400 text-sm">No details found.</div>';
    } catch {
        content.innerHTML = '<div class="text-center py-4 text-red-500 text-sm">Error loading details.</div>';
    }
}

function closeModal() { document.getElementById('logModal').classList.add('hidden'); }
document.getElementById('logModal').addEventListener('click', e => { if (e.target === e.currentTarget) closeModal(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
</script>
@endpush
@endsection

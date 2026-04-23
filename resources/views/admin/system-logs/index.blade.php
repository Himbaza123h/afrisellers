@extends('layouts.home')

@section('page-content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">System Logs</h1>
            <p class="mt-1 text-xs text-gray-500">Everything that happens in the system is recorded here</p>
        </div>
        {{-- Purge --}}
        <form action="{{ route('admin.system-logs.purge') }}" method="POST"
              onsubmit="return confirm('Purge old info/warning logs? Critical and error logs will be kept.')"
              class="flex items-center gap-2">
            @csrf
            <select name="days"
                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                <option value="30">Older than 30 days</option>
                <option value="60">Older than 60 days</option>
                <option value="90" selected>Older than 90 days</option>
                <option value="180">Older than 180 days</option>
            </select>
            <button type="submit"
                class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-semibold hover:bg-red-700 shadow-sm">
                <i class="fas fa-trash"></i> Purge
            </button>
        </form>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm text-green-900 font-medium flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach([
            ['label'=>'Total Logs',       'value'=>$stats['total'],    'color'=>'blue',   'icon'=>'fa-list'],
            ['label'=>'Errors (24h)',      'value'=>$stats['critical'], 'color'=>'red',    'icon'=>'fa-times-circle'],
            ['label'=>'Warnings (24h)',    'value'=>$stats['warning'],  'color'=>'amber',  'icon'=>'fa-exclamation-triangle'],
            ['label'=>"Today's Activity",  'value'=>$stats['today'],    'color'=>'green',  'icon'=>'fa-calendar-day'],
        ] as $card)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-{{ $card['color'] }}-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas {{ $card['icon'] }} text-{{ $card['color'] }}-600 text-sm"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">{{ $card['label'] }}</p>
                <p class="text-xl font-bold text-gray-900">{{ $card['value'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <form method="GET" action="{{ route('admin.system-logs.index') }}"
              class="flex flex-wrap gap-3">
            {{-- Search --}}
            <div class="flex-1 min-w-[200px] relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs mt-2"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search description…"
                    class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Level --}}
            <select name="level"
                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">All Levels</option>
                @foreach(['info','warning','error','critical'] as $lvl)
                    <option value="{{ $lvl }}" {{ request('level')===$lvl ? 'selected' : '' }}>
                        {{ ucfirst($lvl) }}
                    </option>
                @endforeach
            </select>

            {{-- Module --}}
            <select name="module"
                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">All Modules</option>
                @foreach($modules as $mod)
                    <option value="{{ $mod }}" {{ request('module')===$mod ? 'selected' : '' }}>
                        {{ ucfirst($mod) }}
                    </option>
                @endforeach
            </select>

            {{-- Action --}}
            <select name="action"
                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">All Actions</option>
                @foreach($actions as $act)
                    <option value="{{ $act }}" {{ request('action')===$act ? 'selected' : '' }}>
                        {{ ucfirst($act) }}
                    </option>
                @endforeach
            </select>

            {{-- Date From --}}
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">

            {{-- Date To --}}
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">

            <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
            <a href="{{ route('admin.system-logs.index') }}"
               class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50">
                <i class="fas fa-undo mr-1"></i> Reset
            </a>
        </form>
    </div>

    {{-- Results count --}}
    <div class="flex items-center justify-between px-1">
        <p class="text-xs text-gray-500">
            Showing <strong>{{ $logs->total() }}</strong> {{ Str::plural('entry', $logs->total()) }}
        </p>
    </div>

    {{-- Log Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-8">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Level</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Description</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Module</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Action</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">User</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Time</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($logs as $log)
                        @php
                            $rowBg = match($log->level) {
                                'critical' => 'bg-red-50/40',
                                'error'    => 'bg-orange-50/40',
                                'warning'  => 'bg-amber-50/30',
                                default    => '',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors {{ $rowBg }}">
                            <td class="px-4 py-3 text-xs text-gray-400 font-mono">{{ $log->id }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $log->level_badge }}">
                                    <i class="fas {{ $log->icon }} text-[8px]"></i>
                                    {{ strtoupper($log->level) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 max-w-xs">
                                <p class="text-sm text-gray-800 truncate">{{ $log->description }}</p>
                                @if($log->entity_type)
                                    <p class="text-[10px] text-gray-400 mt-0.5 font-mono">
                                        {{ $log->entity_type }}
                                        @if($log->entity_id) #{{ $log->entity_id }} @endif
                                    </p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($log->module)
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-[10px] font-semibold rounded capitalize">
                                        {{ $log->module }}
                                    </span>
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($log->action)
                                    <span class="text-xs text-gray-500 capitalize">{{ $log->action }}</span>
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($log->user)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-blue-500 to-blue-700 flex items-center justify-center flex-shrink-0">
                                            <span class="text-white text-[9px] font-bold">
                                                {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <span class="text-xs text-gray-600 truncate max-w-[100px]">{{ $log->user->name }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">System</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-400 whitespace-nowrap">
                                {{ $log->time_ago }}
                                <br>
                                <span class="text-[9px]">{{ $log->created_at->format('M d, H:i') }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.system-logs.show', $log->id) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-list text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium">No logs found</p>
                                    <p class="text-xs text-gray-400 mt-1">Try adjusting your filters</p>
                                </div>
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
                <div class="text-sm">{{ $logs->links() }}</div>
            </div>
        @endif
    </div>
</div>
@endsection
